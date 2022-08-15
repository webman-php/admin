<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\Util;
use support\Db;
use support\Request;

class IndexController
{

    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = ['index'];


    public function index()
    {
        return response()->file(base_path() . '/plugin/admin/public/index.html');
    }

}
