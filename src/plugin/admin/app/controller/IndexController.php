<?php

namespace plugin\admin\app\controller;

use support\Request;
use support\Response;

class IndexController
{

    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = ['index'];

    /**
     * 后台主页
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        clearstatcache();
        if (!is_file(base_path('plugin/admin/config/database.php'))) {
            return view('index/install');
        }
        $admin = admin();
        if (!$admin) {
            return view('account/login');
        }
        return view('index/index');
    }

}
