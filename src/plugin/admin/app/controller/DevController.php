<?php

namespace plugin\admin\app\controller;

use support\Request;
use support\Response;

/**
 * 开发辅助相关
 */
class DevController
{
    /**
     * 表单构建
     * @return Response
     */
    public function formBuild()
    {
        return view('dev/form-build');
    }

}
