<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\Aritcle;
use support\Request;

/**
 * 文章管理 
 */
class AritcleController extends Base
{
    /**
     * 开启增删改查 
     */
    use Crud;
    
    /**
     * @var Aritcle
     */
    protected $model = null;

    /**
     * 构造函数
     * 
     * @return void
     */
    public function __construct()
    {
        $this->model = new Aritcle;
    }

}
