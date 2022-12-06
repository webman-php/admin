/**
 * 获取控制器详细权限，并决定展示哪些按钮或dom元素
 */
layui.$(function () {
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