layui.define(['jquery', 'element', 'dropdown'], function (exports) {
	"use strict";

	var MOD_NAME = 'tabPage',
		$ = layui.jquery,
		dropdown = layui.dropdown,
		element = layui.element;

	var tabPage = function (opt) {
		this.option = opt;
	};

	var tabData = new Array();
	var tabDataCurrent = 0;
	var contextTabDOM;

	tabPage.prototype.render = function (opt) {

		var option = {
			elem: opt.elem,
			data: opt.data,
			index: opt.index,
			tool: opt.tool || true,
			roll: opt.roll || true,
			success: opt.success ? opt.success : function (id) { },
			session: opt.session ? opt.session : false,
			preload: opt.preload ? opt.preload : false,
			height: opt.height || "100%",
			width: opt.width || "100%",
			closeEvent: opt.closeEvent,
			tabMax: opt.tabMax,
		}

		if (option.session) {
			if (sessionStorage.getItem(option.elem + "-pear-tab-page-data") != null) {
				tabData = JSON.parse(sessionStorage.getItem(option.elem + "-pear-tab-page-data"));
				option.data = JSON.parse(sessionStorage.getItem(option.elem + "-pear-tab-page-data"));
				tabDataCurrent = sessionStorage.getItem(option.elem + "-pear-tab-page-data-current");
				tabData.forEach(function (item, index) {
					if (item.id == tabDataCurrent) {
						option.index = index;
					}
				})
			} else {
				tabData = opt.data;
			}
		}

		var lastIndex;
		var tab = createTab(option);
		$("#" + option.elem).html(tab);
		$(".layui-tab[lay-filter='" + option.elem + "'] .layui-tab-prev").click(function () {
			rollPage("left", option);
		})
		$(".layui-tab[lay-filter='" + option.elem + "'] .layui-tab-next").click(function () {
			rollPage("right", option);
		})
		element.init();

		$("#" + option.elem).width(opt.width);
		$("#" + option.elem).height(opt.height);
		$("#" + option.elem).css({
			position: "relative"
		});

		closeEvent(option);

		option.success(sessionStorage.getItem(option.elem + "-pear-tab-page-data-current"));

		dropdown.render({
			elem: `#${option.elem} .layui-tab-control > .layui-icon-down`,
			trigger: 'hover',
			data: [{
				title: '关 闭 当 前',
				id: 1
			}, {
				title: '关 闭 其 他',
				id: 2
			}, {
				title: '关 闭 全 部',
				id: 3
			}],
			click: function (obj) {

				const id = obj.id;

				if (id === 1) {
					var currentTab = $(".layui-tab[lay-filter='" + option.elem +
						"'] .layui-tab-title .layui-this");
					if (currentTab.find("span").is(".able-close")) {
						var currentId = currentTab.attr("lay-id");
						tabDelete(option.elem, currentId, option.closeEvent, option);
					} else {
						layer.msg("当前页面不允许关闭", {
							icon: 3,
							time: 1000
						})
					}
				} else if (id === 2) {
					var currentId = $(".layui-tab[lay-filter='" + option.elem +
						"'] .layui-tab-title .layui-this").attr("lay-id");
					var tabtitle = $(".layui-tab[lay-filter='" + option.elem + "'] .layui-tab-title li");
					$.each(tabtitle, function (i) {
						if ($(this).attr("lay-id") != currentId) {
							if ($(this).find("span").is(".able-close")) {
								tabDelete(option.elem, $(this).attr("lay-id"), option.closeEvent,
									option);
							}
						}
					})
				} else {
					var currentId = $(".layui-tab[lay-filter='" + option.elem +
						"'] .layui-tab-title .layui-this").attr("lay-id");
					var tabtitle = $(".layui-tab[lay-filter='" + option.elem + "'] .layui-tab-title li");
					$.each(tabtitle, function (i) {
						if ($(this).find("span").is(".able-close")) {
							tabDelete(option.elem, $(this).attr("lay-id"), option.closeEvent, option);
						}
					})
				}

			}
		})

		$("body .layui-tab[lay-filter='" + option.elem + "'] .layui-tab-title").on("contextmenu", "li",
			function (e) {
				var top = e.clientY;
				var left = e.clientX;
				var menuWidth = 100;
				var menu = `<ul>
								<li class='item' id='${option.elem}closeThis'>关闭当前</li>
								<li class='item' id='${option.elem}closeOther'>关闭其他</li>
								<li class='item' id='${option.elem}closeAll'>关闭所有</li>
							</ul>`;

				contextTabDOM = $(this);
				var isOutsideBounds = (left + menuWidth) > $(window).width();
				if (isOutsideBounds) {
					left = $(window).width() - menuWidth;
				}

				layer.open({
					type: 1,
					title: false,
					shade: false,
					skin: 'pear-tab-page-menu',
					closeBtn: false,
					area: [menuWidth + 'px', '108px'],
					fixed: true,
					anim: false,
					isOutAnim: false,
					offset: [top, left],
					content: menu,
					success: function (layero, index) {
						layer.close(lastIndex);
						lastIndex = index;
						menuEvent(option, index);
						var timer;
						$(layero).on('mouseout', function () {
							timer = setTimeout(function () {
								layer.close(index);
							}, 30)
						});

						$(layero).on('mouseover', function () {
							clearTimeout(timer);
						});

						$(layero).on('contextmenu', function () {
							return false;
						})
					}
				});
				return false;
			})

		mousewheelAndTouchmoveHandler(option)
		return new tabPage(option);
	}

	tabPage.prototype.click = function (callback) {
		var option = this.option;
		var elem = this.option.elem;
		element.on('tab(' + this.option.elem + ')', function (data) {
			var id = $("#" + elem + " .layui-tab-title .layui-this").attr("lay-id");
			sessionStorage.setItem(option.elem + "-pear-tab-page-data-current", id);
			callback(id);
		});
	}

	tabPage.prototype.positionTab = function () {
		var $tabTitle = $('.layui-tab[lay-filter=' + this.option.elem + ']  .layui-tab-title');
		var autoLeft = 0;
		$tabTitle.children("li").each(function () {
			if ($(this).hasClass('layui-this')) {
				return false;
			} else {
				autoLeft += $(this).outerWidth();
			}
		});
		$tabTitle.animate({
			scrollLeft: autoLeft - $tabTitle.width() / 3
		}, 200);
	}

	tabPage.prototype.clear = function () {
		sessionStorage.removeItem(this.option.elem + "-pear-tab-page-data");
		sessionStorage.removeItem(this.option.elem + "-pear-tab-page-data-current");
	}

	tabPage.prototype.changeTabTitleById = function (id, title) {
		var currentTab = $(".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-title [lay-id='" + id + "'] .title");
		currentTab.html(title);
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * 删除指定选项卡
	 * 
	 * @param id 编号
	 */
	tabPage.prototype.removeTab = function (id) {
		var elem = this.option.elem;
		if (id != undefined) {
			var currentTab = $(".layui-tab[lay-filter='" + elem + "'] .layui-tab-title [lay-id='" + id + "']");
			if (currentTab.find("span").is(".able-close")) {
				tabDelete(elem, id, () => { });
			}
		} else {
			var tabtitle = $(".layui-tab[lay-filter='" + elem + "'] .layui-tab-title li");
			$.each(tabtitle, function () {
				if ($(this).find("span").is(".able-close")) {
					tabDelete(elem, $(this).attr("lay-id"), () => { });
				}
			})
		}
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * 删除其他选项卡
	 */
	tabPage.prototype.removeOtherTab = function () {
		var elem = this.option.elem;
		var currentId = $(".layui-tab[lay-filter='" + elem + "'] .layui-tab-title .layui-this").attr("lay-id");
		var tabtitle = $(".layui-tab[lay-filter='" + elem + "'] .layui-tab-title li");
		$.each(tabtitle, function () {
			if ($(this).attr("lay-id") != currentId) {
				if ($(this).find("span").is(".able-close")) {
					tabDelete(elem, $(this).attr("lay-id"), () => { });
				}
			}
		})
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * 删除选中选项卡
	 */
	tabPage.prototype.removeCurrentTab = function () {
		var currentTab = $(".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-title .layui-this");
		if (currentTab.find("span").is(".able-close")) {
			var currentId = currentTab.attr("lay-id");
			tabDelete(this.option.elem, currentId, () => { });
		}
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * 切换选项卡
	 * 
	 * @param opt 内容
	 */
	tabPage.prototype.changePage = function (opt) {

		var title = `<span class="pear-tab-page-active"></span>
					 <span class="${opt.close ? 'able-close' : 'disable-close'} title">${opt.title}</span>
					 <i class="layui-icon layui-unselect layui-tab-close">ဆ</i>`;

		if ($(".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-title li[lay-id]").length <=
			0) {

			var that = this;

			if (opt.type === "_iframe") {

				element.tabAdd(this.option.elem, {
					id: opt.id,
					title: title,
					content: `<iframe id="${opt.id}" type="${opt.type}" data-frameid="${opt.id}" scrolling="auto" frameborder="0" src="${opt.url}" style="width:100%;height:100%;" allowfullscreen="true"></iframe>`
				});

			} else {

				$.ajax({
					url: opt.url,
					type: 'get',
					dataType: 'html',
					async: false,
					success: function (data) {
						element.tabAdd(that.option.elem, {
							id: opt.id,
							title: title,
							content: `<div id="${opt.id}" type="${opt.type}" data-frameid="${opt.id}" src="${opt.url}">${data}</div>`,
						});
					},
					error: function (xhr, textstatus, thrown) {
						return layer.msg('Status:' + xhr.status + '，' + xhr.statusText + '，请稍后再试！');
					}
				});
			}

			tabData.push(opt);
			sessionStorage.setItem(that.option.elem + "-pear-tab-page-data", JSON.stringify(tabData));
			sessionStorage.setItem(that.option.elem + "-pear-tab-page-data-current", opt.id);

		} else {

			var isData = false;
			$.each($(".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-title li[lay-id]"),
				function () {
					if ($(this).attr("lay-id") == opt.id) {
						isData = true;
					}
				})

			if (isData == false) {

				if (this.option.tabMax != false) {
					if ($(".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-title li[lay-id]")
						.length >= this.option.tabMax) {
						layer.msg("最多打开" + this.option.tabMax + "个标签页", {
							icon: 2,
							time: 1000,
							shift: 6
						});
						return false;
					}
				}

				var that = this;
				if (opt.type === "_iframe") {
					element.tabAdd(this.option.elem, {
						id: opt.id,
						title: title,
						content: `<iframe id="${opt.id}" type="${opt.type}" data-frameid="${opt.id}" scrolling="auto" frameborder="0" src="${opt.url}" style="width:100%;height:100%;" allowfullscreen="true"></iframe>`
					});
				} else {
					$.ajax({
						url: opt.url,
						type: 'get',
						dataType: 'html',
						async: false,
						success: function (data) {
							element.tabAdd(that.option.elem, {
								id: opt.id,
								title: title,
								content: `<div id="${opt.id}" type="${opt.type}" data-frameid="${opt.id}" src="${opt.url}">${data}</div>`,
							});
						},
						error: function (xhr, textstatus, thrown) {
							return layer.msg('Status:' + xhr.status + '，' + xhr.statusText + '，请稍后再试！');
						}
					});
				}
				tabData.push(opt);
				sessionStorage.setItem(that.option.elem + "-pear-tab-page-data", JSON.stringify(tabData));
				sessionStorage.setItem(that.option.elem + "-pear-tab-page-data-current", opt.id);
			}
		}
		element.tabChange(this.option.elem, opt.id);
		sessionStorage.setItem(this.option.elem + "-pear-tab-page-data-current", opt.id);
	}

	/**
	 * 刷新当前选型卡
	 * 
	 * @param time 动画时长
	 */
	tabPage.prototype.refresh = function (time) {

		var $iframe = $(".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-content .layui-show > div[data-frameid], " +
			".layui-tab[lay-filter='" + this.option.elem + "'] .layui-tab-content .layui-show > iframe[data-frameid]");
		var $iframeLoad;

		if (time != false && time != 0) {
			$iframeLoad = $("#" + this.option.elem).find(".pear-tab-page-loading");
			$iframeLoad.css({
				display: "block"
			});
		}

		if ($iframe.attr("type") === "_iframe") {
			$iframe.attr("src", $iframe.attr("src"));
			$iframe.on("load", function () {
				$iframeLoad.fadeOut(1000, function () {
					$iframeLoad.css({
						display: "none"
					});
				});
			})
		} else {
			$.ajax({
				url: $iframe.attr("src"),
				type: 'get',
				dataType: 'html',
				success: function (data) {
					$iframe.html(data);
					if ($iframeLoad != undefined) {
						$iframeLoad.fadeOut(1000, function () {
							$iframeLoad.css({
								display: "none"
							});
						});
					}
				},
				error: function (xhr) {
					return layer.msg('Status:' + xhr.status + '，' + xhr.statusText + '，请稍后再试！');
				}
			});
		}
	}

	function tabDelete(elem, id, callback) {
		var tabTitle = $(".layui-tab[lay-filter='" + elem + "']").find(".layui-tab-title");
		var removeTab = tabTitle.find("li[lay-id='" + id + "']");
		var nextNode = removeTab.next("li");
		if (!removeTab.hasClass("layui-this")) {
			removeTab.remove();
			var tabContent = $(".layui-tab[lay-filter='" + elem + "']").find("*[id='" + id + "']")
				.parent();
			tabContent.remove();

			tabData = JSON.parse(sessionStorage.getItem(elem + "-pear-tab-page-data"));
			tabDataCurrent = sessionStorage.getItem(elem + "-pear-tab-page-data-current");
			tabData = tabData.filter(function (item) {
				return item.id != id;
			})
			sessionStorage.setItem(elem + "-pear-tab-page-data", JSON.stringify(tabData));
			return false;
		}

		var currId;
		if (nextNode.length) {
			nextNode.addClass("layui-this");
			currId = nextNode.attr("lay-id");
			$("#" + elem + " [id='" + currId + "']").parent().addClass("layui-show");
		} else {
			var prevNode = removeTab.prev("li");
			prevNode.addClass("layui-this");
			currId = prevNode.attr("lay-id");
			$("#" + elem + " [id='" + currId + "']").parent().addClass("layui-show");
		}
		callback(currId);
		tabData = JSON.parse(sessionStorage.getItem(elem + "-pear-tab-page-data"));
		tabDataCurrent = sessionStorage.getItem(elem + "-pear-tab-page-data-current");
		tabData = tabData.filter(function (item) {
			return item.id != id;
		})
		sessionStorage.setItem(elem + "-pear-tab-page-data", JSON.stringify(tabData));
		sessionStorage.setItem(elem + "-pear-tab-page-data-current", currId);
		removeTab.remove();
		var tabContent = $(".layui-tab[lay-filter='" + elem + "']").find("*[id='" + id + "']").parent();
		tabContent.remove();
	}

	/**
	 * @since Pear Admin 4.0
	 */
	function createTab(option) {

		var type = "";
		if (option.roll == true) {
			type = "layui-tab-roll";
		}
		if (option.tool != false) {
			type = "layui-tab-tool";
		}
		if (option.roll == true && option.tool != false) {
			type = "layui-tab-rollTool";
		}
		var tab = '<div class="pear-tab-page ' + type + ' layui-tab" lay-filter="' + option.elem +
			'" lay-allowClose="true">';

		var headers = '<ul class="layui-tab-title">';
		var content = '<div class="layui-tab-content">';
		var loading = '<div class="pear-tab-page-loading"><div class="ball-loader"><span></span><span></span><span></span><span></span></div></div>'
		var control = `<div class="layui-tab-control">
							<li class="layui-tab-prev layui-icon layui-icon-left"></li>
							<li class="layui-tab-next layui-icon layui-icon-right"></li>
							<li class="layui-tab-tool layui-icon layui-icon-down"></li>
						</div>`;

		// 处 理 选 项 卡 头 部
		var index = 0;

		$.each(option.data, function (i, item) {

			var titleItem = `<li lay-id="${item.id}" class="${option.index == index ? 'layui-this' : ''}">
								<span class="pear-tab-page-active"></span>
								<span class="${item.close ? 'able-close' : 'disable-close'} title">
									${item.title}
								</span>
                                <i class="layui-icon layui-unselect layui-tab-close">ဆ</i></li>
							</li>`;

			headers += titleItem;

			if (item.type === "_iframe") {

				content += `<div class="${option.index == index ? 'layui-show' : ''} layui-tab-item"><iframe id="${item.id}" type="${item.type}" data-frameid="${item.id}" scrolling="auto" frameborder="0" src="${item.url}" style="width:100%;height:100%;" allowfullscreen="true"></iframe></div>`

			} else {

				$.ajax({
					url: item.url,
					type: 'get',
					dataType: 'html',
					async: false,
					success: function (data) {
						content += `<div class="${option.index == index ? 'layui-show' : ''} layui-tab-item"><div id="${item.id}" type="${item.type}" data-frameid="${item.id}"  src="${item.url}">${data}</div></div>`;
					},
					error: function (xhr) {
						return layer.msg('Status:' + xhr.status + '，' + xhr.statusText + '，请稍后再试！');
					}
				});
			}

			index++;
		});

		headers += '</ul>';
		content += '</div>';

		tab += headers;
		tab += control;
		tab += content;
		tab += loading;
		tab += '</div>';
		tab += ''
		return tab;
	}

	function rollPage(d, option) {
		var $tabTitle = $('#' + option.elem + '  .layui-tab-title');
		var left = $tabTitle.scrollLeft();
		if ('left' === d) {
			$tabTitle.animate({
				scrollLeft: left - 450
			}, 200);
		} else {
			$tabTitle.animate({
				scrollLeft: left + 450
			}, 200);
		}
	}

	function closeEvent(option) {
		$(".layui-tab[lay-filter='" + option.elem + "']")
			.on("click", ".layui-tab-close", function () {
				var layid = $(this).parent().attr("lay-id");
				tabDelete(option.elem, layid, option.closeEvent, option);
			})
			.on("mousedown", ".layui-tab-title li", function (e) {
				if (e.buttons === 4 && $(this).find("span").is(".able-close")) {
					tabDelete(option.elem, $(this).attr("lay-id"), option.closeEvent, option);
				}
			});
	}

	function menuEvent(option, index) {

		$("#" + option.elem + "closeThis").click(function () {
			var currentTab = contextTabDOM;

			if (currentTab.find("span").is(".able-close")) {
				var currentId = currentTab.attr("lay-id");
				tabDelete(option.elem, currentId, option.closeEvent, option);
			} else {
				layer.msg("当前页面不允许关闭", {
					icon: 3,
					time: 800
				})
			}
			layer.close(index);
		})

		$("#" + option.elem + "closeOther").click(function () {
			var currentId = contextTabDOM.attr("lay-id");
			var tabtitle = $(".layui-tab[lay-filter='" + option.elem + "'] .layui-tab-title li");
			$.each(tabtitle, function (i) {
				if ($(this).attr("lay-id") != currentId) {
					if ($(this).find("span").is(".able-close")) {
						tabDelete(option.elem, $(this).attr("lay-id"), option.closeEvent,
							option);
					}
				}
			})
			layer.close(index);
		})

		$("#" + option.elem + "closeAll").click(function () {
			var tabtitle = $(".layui-tab[lay-filter='" + option.elem + "'] .layui-tab-title li");
			$.each(tabtitle, function (i) {
				if ($(this).find("span").is(".able-close")) {
					tabDelete(option.elem, $(this).attr("lay-id"), option.closeEvent, option);
				}
			})
			layer.close(index);
		})
	}

	function mousewheelAndTouchmoveHandler(option) {
		var $bodyTab = $("body .layui-tab[lay-filter='" + option.elem + "'] .layui-tab-title")
		var $tabTitle = $('#' + option.elem + '  .layui-tab-title');
		var mouseScrollStep = 100
		// 鼠标滚轮
		$bodyTab.on("mousewheel DOMMouseScroll", function (e) {
			e.originalEvent.preventDefault()
			var delta = (e.originalEvent.wheelDelta && (e.originalEvent.wheelDelta > 0 ? "top" :
				"down")) || // chrome & ie
				(e.originalEvent.detail && (e.originalEvent.detail > 0 ? "down" : "top")); // firefox
			var scrollLeft = $tabTitle.scrollLeft();

			if (delta === "top") {
				scrollLeft -= mouseScrollStep
			} else if (delta === "down") {
				scrollLeft += mouseScrollStep
			}
			$tabTitle.scrollLeft(scrollLeft)
		});

		// 触摸移动
		var touchX = 0;
		$bodyTab.on("touchstart", function (e) {
			var touch = e.originalEvent.targetTouches[0];
			touchX = touch.pageX
		})

		$bodyTab.on("touchmove", function (e) {
			var event = e.originalEvent;
			if (event.targetTouches.length > 1) return;
			event.preventDefault();
			var touch = event.targetTouches[0];
			var distanceX = touchX - touch.pageX
			var scrollLeft = $tabTitle.scrollLeft();
			touchX = touch.pageX
			$tabTitle.scrollLeft(scrollLeft += distanceX)
		});
	}

	exports(MOD_NAME, new tabPage());
})
