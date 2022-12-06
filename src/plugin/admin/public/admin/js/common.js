/**
 * 浏览页面顶部搜索框展开收回控制
 */
function toggleSearchFormShow()
{
    let $ = layui.$;
    let items = $('.top-search-from .layui-form-item');
    if (items.length <= 2) {
        if (items.length <= 1) $('.top-search-from').remove();
        return;
    }
    let btns = $('.top-search-from .toggle-btn a');
    let toggle = toggleSearchFormShow;
    if (typeof toggle.hide === 'undefined') {
        btns.on('click', function () {
            toggle();
        });
    }
    let countPerRow = parseInt($('.top-search-from').width()/$('.layui-form-item').width());
    if (items.length <= countPerRow) {
        return;
    }
    btns.removeClass('layui-hide');
    toggle.hide = !toggle.hide;
    if (toggle.hide) {
        for (let i = countPerRow - 1; i < items.length - 1; i++) {
            $(items[i]).hide();
        }
        return $('.top-search-from .toggle-btn a:last').addClass('layui-hide');
    }
    items.show();
    $('.top-search-from .toggle-btn a:first').addClass('layui-hide');
}

/**
 * 获取控制器详细权限，并决定展示哪些按钮或dom元素
 */
layui.$(function () {
    if (typeof CONTROLLER === "undefined") return;
    let $ = layui.$;
    $.ajax({
        url: "/app/admin/admin-rule/permission",
        dataType: "json",
        data: {controller: CONTROLLER},
        success: function (res) {
            let style = '';
            layui.each(res.data || [], function (k, action) {
                if (action === '*') {
                    style = '*[permission]{display: initial}';
                    return;
                }
                style += '*[permission="'+action+'"]{display: initial}';
            });
            $("head").append("<style>"+style+"</style>");
        }
    });
});

layui.$(function () {
    toggleSearchFormShow();
});

