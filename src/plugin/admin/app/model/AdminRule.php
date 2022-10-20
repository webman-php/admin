<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id primary key (primary key)
 * @property string $title title
 * @property string $name name
 * @property integer $pid superiorid
 * @property string $component component
 * @property string $path path
 * @property string $icon icon
 * @property string $created_at creation time
 * @property string $updated_at Update time
 * @property string $frame_src url
 * @property integer $hide_menu Hide menu
 * @property integer $is_menu Whether menu
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
