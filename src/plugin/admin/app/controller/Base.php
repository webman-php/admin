<?php

namespace plugin\admin\app\controller;

use support\Model;
use support\Response;

/**
 * 基础控制器
 */
class Base
{

    /**
     * @var Model
     */
    protected $model = null;

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
     * @return Response
     */
    protected function json(int $code, string $msg = 'ok', array $data = []): Response
    {
        return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }

}
