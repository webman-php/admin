layui.define(['jquery', 'tools'], function (exports) {
	"use strict";

	/**
	 * @since Pear Admin 4.0
	 * 
	 * Button component
	 * */
	var MOD_NAME = 'menuSearch',
		tools = layui.tools,
		$ = layui.jquery;

	var menuSearch = function (opt) {
		this.option = opt;
	};

	/**
	 * @since Pear Admin 4.0
	 * 
	 * Button start loading
	 * */
	menuSearch.prototype.render = function (opt) {

		var options = {
			select: opt.select,
			elem: opt.elem,
			dataProvider: opt.dataProvider,
		}

		$('body').on("click", options.elem, function () {

			var _html = [
				`<div class="menu-search-content">
				  <div class="layui-form menu-search-input-wrapper">
				    <div class=" layui-input-wrap layui-input-wrap-prefix">
				      <div class="layui-input-prefix">
				        <i class="layui-icon layui-icon-search"></i>
				      </div>
				      <input type="text" name="menuSearch" value="" placeholder="搜索菜单" autocomplete="off" class="layui-input" lay-affix="clear">
				    </div>
				  </div>
				  <div class="menu-search-no-data">暂 无 信 息</div>
				  <ul class="menu-search-list">
				  </ul>
				  <div class="menu-search-tips">
				    <div>
				      <span class="mr-1">选择</span><kbd class="mr-1 w-5"> ↑ </kbd><kbd class="mr-5 w-5"> ↓ </kbd>
				      <span class="mr-1">确定</span><kbd class="mr-5"> Enter </kbd><span class="mr-1">关闭</span><kbd class="mr-1"> Esc </kbd>
				    </div>
				  </div>
				</div>`
			].join('');

			layer.open({
				type: 1,
				offset: "10%",
				area: ['600px'],
				title: false,
				closeBtn: 0,
				shadeClose: true,
				anim: 0,
				move: false,
				content: _html,
				success: function (layero, layeridx) {

					var $layer = layero;
					var $content = $(layero).children('.layui-layer-content');
					var $input = $(".menu-search-input-wrapper input");
					var $noData = $(".menu-search-no-data");
					var $list = $(".menu-search-list");
					var menuData = options.dataProvider();

					$layer.css("border-radius", "6px");
					$input.off("focus").focus();

					// 搜索输入事件
					$input.off("input").on("input", tools.debounce(function () {
						var keywords = $input.val().trim();
						var filteredMenus = filterHandle(menuData, keywords);

						if (filteredMenus.length) {
							var tiledMenus = tiledHandle(filteredMenus);
							var listHtml = createList(tiledMenus);
							$noData.css("display", "none");
							$list.html("").append(listHtml).children(":first").addClass("this")
						} else {
							$list.html("");
							$noData.css("display", "flex");
						}
						var currentHeight = $(".menu-search-content").outerHeight()
						$layer.css("height", currentHeight);
						$content.css("height", currentHeight);
					}, 500)
					)

					// 列表点击事件
					$list.off("click").on("click", "li", function () {
						var id = $(this).attr("smenu-id");
						var title = $(this).attr("smenu-title");
						var url = $(this).attr("smenu-url");
						var type = $(this).attr("smenu-type");
						var openType = $(this).attr("smenu-open-type");

						options.select({ id, title, url, type, openType });

						layer.close(layeridx);
					})

					$list.off('mouseenter').on("mouseenter", "li", function () {
						$(".menu-search-list li.this").removeClass("this");
						$(this).addClass("this");
					}).off("mouseleave").on("mouseleave", "li", function () {
						$(this).removeClass("this");
					})

					// 监听键盘事件
					$('.menu-search-content').off("keydown").keydown(function (e) {
						if (e.keyCode === 13 || e.keyCode === 32) {
							e.preventDefault();
							var that = $(".menu-search-list li.this");
							var id = that.attr("smenu-id");
							var title = that.attr("smenu-title");
							var url = that.attr("smenu-url");
							var type = that.attr("smenu-type");
							var openType = that.attr("smenu-open-type");

							options.select({ id, title, url, type, openType });

							layer.close(layeridx);
						} else if (e.keyCode === 38) {
							e.preventDefault();
							var prevEl = $(".menu-search-list li.this").prev();
							$(".menu-search-list li.this").removeClass("this");
							if (prevEl.length !== 0) {
								prevEl.addClass("this");
							} else {
								$list.children().last().addClass("this");
							}
						} else if (e.keyCode === 40) {
							e.preventDefault();
							var nextEl = $(".menu-search-list li.this").next();
							$(".menu-search-list li.this").removeClass("this");
							if (nextEl.length !== 0) {
								nextEl.addClass("this");
							} else {
								$list.children().first().addClass("this");
							}
						} else if (e.keyCode === 27) {
							e.preventDefault();
							layer.close(layeridx);
						}
					})
				}
			})
		});

		return new menuSearch(options);
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * 创建结果列表
	 */
	var createList = function (data) {
		var listHtml = '';
		$.each(data, function (index, item) {
			listHtml += `<li smenu-open-type="${item.info.openType}" smenu-id="${item.info.id}" smenu-icon="'${item.info.icon}" smenu-url="${item.info.href}" smenu-title="${item.info.title}" smenu-type="${item.info.type}">
			   <span><i style="margin-right:10px" class="${item.info.icon}"></i>${item.path}</span>
			   <i class="layui-icon layui-icon-right"></i>
			 </li>`
		})
		return listHtml;
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * Tree 转 path 列表
	 */
	var tiledHandle = function (data) {
		var tiledMenus = [];
		var treeTiled = function (data, content) {
			var path = "";
			var separator = " / ";
			if (!content) content = "";
			$.each(data, function (index, item) {
				if (item.children && item.children.length) {
					path += content + item.title + separator;
					var childPath = treeTiled(item.children, path);
					path += childPath;
					if (!childPath) path = ""; // 重置路径
				} else {
					path += content + item.title
					tiledMenus.push({ path: path, info: item });
					path = ""; //重置路径
				}
			})
			return path;
		};
		treeTiled(data);

		return tiledMenus;
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * 查询匹配算法
	 */
	var filterHandle = function (filterData, val) {
		if (!val) return [];
		var filteredMenus = [];
		filterData = $.extend(true, {}, filterData);
		$.each(filterData, function (index, item) {
			if (item.children && item.children.length) {
				var children = filterHandle(item.children, val)
				var obj = $.extend({}, item, { children: children });
				if (children && children.length) {
					filteredMenus.push(obj);
				} else if (item.title.indexOf(val) >= 0) {
					item.children = []; // 父级匹配但子级不匹配,就去除子级
					filteredMenus.push($.extend({}, item));
				}
			} else if (item.title.indexOf(val) >= 0) {
				filteredMenus.push(item);
			}
		})
		return filteredMenus;
	}

	exports(MOD_NAME, new menuSearch());
});
