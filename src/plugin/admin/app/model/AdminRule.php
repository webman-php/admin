<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id 主键(主键)
 * @property string $title 标题
 * @property string $key key，全局唯一
 * @property integer $pid 上级id
 * @property string $icon 图标
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $href url
 * @property integer $type 类型
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
