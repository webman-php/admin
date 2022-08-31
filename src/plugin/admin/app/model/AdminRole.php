<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id 主键(主键)
 * @property string $name 角色名
 * @property string $rules 规则
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class AdminRole extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_admin_roles';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
}
