<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\model\AdminRole;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 管理员角色设置
 */
class AdminRoleController extends Crud
{
    /**
     * @var AdminRole
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new AdminRole;
    }

    /**
     * 浏览
     * @return Response
     */
    public function index()
    {
        return view('admin-role/index');
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            return parent::insert($request);
        }
        return view('admin-role/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response
    {
        if ($request->method() === 'GET') {
            return view('admin-role/update');
        }
        [$id, $data] = $this->updateInput($request);
        // id为1的管理员权限固定为*
        if (isset($data['rules']) && $id == 1) {
            $data['rules'] = '*';
        }
        $this->doUpdate($id, $data);
        return $this->json(0);
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $ids = $this->deleteInput($request);
        if (in_array(1, $ids)) {
            return $this->json(1, '无法删除超级管理员角色');
        }
        $this->doDelete($ids);
        return $this->json(0);
    }

}
