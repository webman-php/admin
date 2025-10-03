window.rootPath = (function (src) {
	src = document.currentScript
		? document.currentScript.src
		: document.scripts[document.scripts.length - 1].src;
	return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
	base: rootPath + "module/",
	version: "4.0.3"
}).extend({
	admin: "admin",
	page: "page",
	tabPage: "tabPage",
	menu: "menu",
	fullscreen: "fullscreen",
	messageCenter: "messageCenter",
	menuSearch: "menuSearch",
	button: "button",
	tools: "tools",
	popup: "extends/popup",
	count: "extends/count",
	toast: "extends/toast",
	nprogress: "extends/nprogress",
	echarts: "extends/echarts",
	echartsTheme: "extends/echartsTheme",
	yaml: "extends/yaml"
}).use([], function () { });