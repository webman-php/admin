<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id primary key (primary key)
 * @property string $name character name
 * @property string $rules rule
 * @property string $created_at creation time
 * @property string $updated_at Update time
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
