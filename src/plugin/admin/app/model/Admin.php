<?php

namespace plugin\admin\app\model;


/**
 * @property integer $id ID(primary key)
 * @property string $username username
 * @property string $nickname Nick name
 * @property string $password password
 * @property string $avatar avatar
 * @property string $email Mail
 * @property string $mobile cell phone
 * @property string $created_at creation time
 * @property string $updated_at Update time
 * @property string $roles Role
 */
class Admin extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_admins';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
}
