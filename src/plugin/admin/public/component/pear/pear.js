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
}).use(['admin'], function (){
    setTimeout(function () {
        const $ = layui.$;

        function changeDarkTheme() {
            let dark = localStorage.getItem('dark')
            if (dark === 'true') {
                layui.$('body').addClass('pear-admin-dark')
            } else {
                layui.$('body').removeClass('pear-admin-dark')
            }
        }
        layui.admin.changeTheme()
        changeDarkTheme()
        window.addEventListener('storage',ev =>  {
            if (ev.key === 'theme-color-color') {
                layui.admin.changeTheme()
            }
            if (ev.key === 'dark') {
                changeDarkTheme()
            }
        })

        function applyButtonStyles() {
            // 为 Pear 按钮添加对应的 Layui 样式类
            const buttonMap = [
                ['.pear-btn', 'layui-btn'],
                ['.pear-btn-primary', 'layui-btn-primary'],
                ['.pear-btn-danger', 'layui-btn-danger'],
                ['.pear-btn-warm', 'layui-btn-warm'],
                ['.pear-btn-success', 'layui-btn-success'],
            ];

            buttonMap.forEach(([selector, className]) => {
                $(selector).each(function() {
                    if (!$(this).hasClass(className)) {
                        $(this).addClass(className);
                    }
                });
            });
        }

        // 初始应用样式
        applyButtonStyles();

        // 使用 MutationObserver 监听新添加的元素
        const observer = new MutationObserver(function(mutationsList) {
            for(let mutation of mutationsList) {
                if (mutation.type === 'childList') {
                    applyButtonStyles();
                    break;
                }
            }
        });

        // 开始观察 body 及其子元素的变化
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    })
});