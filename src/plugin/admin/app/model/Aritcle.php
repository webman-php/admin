<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id 主键(主键)
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $title 标题
 * @property string $content 内容
 * @property integer $uid 用户id
 */
class Aritcle extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aritcle';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
    
    
}
