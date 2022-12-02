<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\Admin;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 管理员列表 
 */
class AdminController extends Crud
{
    
    /**
     * @var Admin
     */
    protected $model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        $this->model = new Admin;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view("admin/index");
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::insert($request);
        }
        return view("admin/insert");
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::update($request);
        }
        return view("admin/update");
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $primary_key = $this->model->getKeyName();
        $ids = $request->post($primary_key);
        if (!$ids) {
            return $this->json(0);
        }
        $ids = (array)$ids;
        if (in_array(admin_id(), $ids)) {
            return $this->json(1, '不能删除自己');
        }
        $this->model->whereIn($primary_key, $ids)->delete();
        return $this->json(0);
    }

}
