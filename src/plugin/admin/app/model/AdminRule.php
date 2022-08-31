<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id 主键(主键)
 * @property string $title 标题
 * @property string $name 名字
 * @property integer $pid 上级id
 * @property string $component 组件
 * @property string $path 路径
 * @property string $icon 图标
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $frame_src url
 * @property integer $hide_menu 隐藏菜单
 * @property integer $is_menu 是否菜单
 */
class AdminRule extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_admin_rules';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
}
