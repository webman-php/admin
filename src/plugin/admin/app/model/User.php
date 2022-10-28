<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id primary key (primary key)
 * @property string $username username
 * @property string $nickname Nick name
 * @property string $password password
 * @property string $sex gender
 * @property string $avatar avatar
 * @property string $email Mail
 * @property string $mobile cell phone
 * @property integer $level grade
 * @property string $birthday Birthday
 * @property integer $money balance
 * @property integer $score integral
 * @property string $last_time Last Login Time
 * @property string $last_ip Last Loginip
 * @property string $join_time Registration time
 * @property string $join_ip registerip
 * @property string $token token
 * @property string $created_at creation time
 * @property string $updated_at Update time
 * @property string $roles Update time
 */
class User extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wa_users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';
    
    
}
