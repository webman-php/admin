layui.define(['jquery'], function (exports) {
	"use strict";

	/**
	 * @since Pear Admin 4.0
	 * 
	 * Button component
	 * */
	var MOD_NAME = 'button',
		$ = layui.jquery;

	var button = function (opt) {
		this.option = opt;
	};

	/**
	 * @since Pear Admin 4.0
	 * 
	 * Button start loading
	 * */
	button.prototype.load = function (opt) {

		var options = {
			elem: opt.elem,
			time: opt.time ? opt.time : false,
			done: opt.done ? opt.done : function () { }
		}

		var text = $(options.elem).html();

		$(options.elem).html("<i class='layui-anim layui-anim-rotate layui-icon layui-anim-loop layui-icon-loading'/>");
		$(options.elem).attr("disabled", "disabled");

		var $button = $(options.elem);

		if (options.time != "" || options.time != false) {
			setTimeout(function () {
				$button.attr("disabled", false);
				$button.html(text);
				options.done();
			}, options.time);
		}
		options.text = text;
		return new button(options);
	}

	/**
	 * @since Pear Admin 4.0
	 * 
	 * Button stop loaded
	 * */
	button.prototype.stop = function (success) {
		$(this.option.elem).attr("disabled", false);
		$(this.option.elem).html(this.option.text);
		success && success();
	}

	exports(MOD_NAME, new button());
});
