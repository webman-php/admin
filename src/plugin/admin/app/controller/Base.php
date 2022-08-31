<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\Util;
use support\Db;
use support\Request;

/**
 * 基础控制器
 */
class Base
{

    /**
     * 无需登录的方法及鉴权
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 需要登录无需鉴权的方法
     * @var array
     */
    protected $noNeedAuth = [];

    /**
     * 返回格式化json数据
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return \support\Response
     */
    protected function json(int $code, string $msg = 'ok', array $data = [])
    {
        return json(['code' => $code, 'result' => $data, 'message' => $msg, 'type' => $code ? 'error' : 'success']);
    }

}
