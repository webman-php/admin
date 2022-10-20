<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\Util;
use support\Db;
use support\Request;

/**
 * Basic Controller
 */
class Base
{

    /**
     * Method and authentication without login
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * Requires login without authentication method
     * @var array
     */
    protected $noNeedAuth = [];

    /**
     * return formatted json data
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
