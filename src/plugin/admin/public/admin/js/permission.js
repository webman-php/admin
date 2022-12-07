/**
 * 获取控制器详细权限，并决定展示哪些按钮或dom元素
 */
layui.$(function () {
    let $ = layui.$;
    $.ajax({
        url: "/app/admin/admin-rule/permission-codes",
        dataType: "json",
        success: function (res) {
            let style = '';
            let codes = res.data || [];
            // codes里有*，说明是超级管理员，拥有所有权限
            if (codes.indexOf('*') !== -1) {
                $("head").append("<style>*[permission]{display: initial}</style>");
                return;
            }
            // 细分权限
            layui.each(codes, function (k, code) {
                codes[k] = '*[permission^="'+code+'"]';
            });
            $("head").append("<style>"+codes.join(",")+"{display: initial}</style>");
        }
    });
});