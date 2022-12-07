<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\model\Role;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 角色管理
 */
class RoleController extends Crud
{
    /**
     * @var Role
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new Role;
    }

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('role/index');
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
        return view('role/insert');
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
            return view('role/update');
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
