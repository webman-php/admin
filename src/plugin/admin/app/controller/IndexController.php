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


    /**
     * 后台主页
     *
     * @return \support\Response
     */
    public function index(Request $request)
    {
        if (!$request->queryString()) {
            // 检查是否安装了admin
            $database_config_file = base_path() . '/plugin/admin/config/database.php';
            clearstatcache();
            if (!is_file($database_config_file)) {
                return redirect('/app/admin?install#install');
            }
        }
        return response()->file(base_path() . '/plugin/admin/public/index.html');
    }

}
