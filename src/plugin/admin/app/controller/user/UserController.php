<?php

namespace plugin\admin\app\controller\user;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\User;
use support\Request;

/**
 * User Management
 */
class UserController extends Base
{
    /**
     * @var User
     */
    protected $model = null;

    /**
     * Add, delete, modify and check
     */
    use Crud;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = new User;
    }

}
