<?php

namespace plugin\admin\app\controller\user;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\User;
use support\Request;

class UserController extends Base
{
    /**
     * @var User
     */
    protected $model = null;

    use Crud;

    public function __construct()
    {
        $this->model = new User;
    }

}
