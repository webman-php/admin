<?php

namespace plugin\admin\app\model;

/**
 * @property integer $id 主键(主键)
 * @property string $username 用户名
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property string $sex 性别
 * @property string $avatar 头像
 * @property string $email 邮箱
 * @property string $mobile 手机
 * @property integer $level 等级
 * @property string $birthday 生日
 * @property integer $money 余额
 * @property integer $score 积分
 * @property string $last_time 上次登录时间
 * @property string $last_ip 上次登录ip
 * @property string $join_time 注册时间
 * @property string $join_ip 注册ip
 * @property string $token token
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * @property string $roles 更新时间
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
