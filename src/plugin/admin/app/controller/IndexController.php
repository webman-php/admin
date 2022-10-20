<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\Util;
use support\Db;
use support\Request;

class IndexController
{

    /**
     * Methods without login
     * @var array
     */
    protected $noNeedLogin = ['index'];


    /**
     * Background Home
     *
     * @return \support\Response
     */
    public function index(Request $request)
    {
        if (!$request->queryString()) {
            // Check if installedadmin
            $database_config_file = base_path() . '/plugin/admin/config/database.php';
            clearstatcache();
            if (!is_file($database_config_file)) {
                return redirect('/app/admin?install#install');
            }
        }
        return response()->file(base_path() . '/plugin/admin/public/index.html');
    }

}
