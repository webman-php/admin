<?php

namespace plugin\admin\app\model;

use plugin\admin\app\model\Base;

/**
 * @property integer $id 主键(主键)
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $bb bb
 * @property integer $tree1 
 * @property string $tree2 
 * @property string $icon 
 * @property string $file 
 * @property integer $select1 
 * @property string $select2 
 * @property string $img 
 * @property integer $state
 */
class Dict extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'dict';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
    
    
}
