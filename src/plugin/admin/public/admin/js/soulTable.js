// 使用 extend 将 刚刚下载的插件 加载进来
layui.extend({
    soulTable:'soulTable/soulTable.slim',// 模块
})

/**
 * 拖拽表格 进行排序
 * @param obj  当前拖拽对象
 * @param tableId  表的主键ID
 * @param updateUrl  更新数据的接口
 * @param weightField  排序字段
 */
function rowDragDoneFunc(obj,tableId,updateUrl,weightField){
    weightField = weightField || 'weight';
    console.log(obj.row,'--obj.row')
    // 获取最新位置 前后数据的id
    var beforId = afterId = 0;
    if(obj.newIndex > 0){
        beforId = obj.cache[obj.newIndex-1][tableId]
    }
    if(obj.newIndex < obj.cache.length-1){
        afterId = obj.cache[obj.newIndex+1][tableId]
    }
    var data = {
        dragDone:1,// 增加数据标识  方便后台接口进行判断
        id:obj.row[tableId],
        field:weightField,
        beforId,afterId
    }
    // 提交数据进行排序更新
    layui.$.post(updateUrl,data,function(res){
        if(res.code){
            layui.layer.msg(res.msg,{icon:5});
        }else{
            obj.row[weightField] =res.data;
            refreshTable();
        }
    })
}
