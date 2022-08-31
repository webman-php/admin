<?php

namespace plugin\admin\app\controller\user;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\User;
use support\Request;

/**
 * 用户管理
 */
class UserController extends Base
{
    /**
     * @var User
     */
    protected $model = null;

    /**
     * 增删改查
     */
    use Crud;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new User;
    }

}
