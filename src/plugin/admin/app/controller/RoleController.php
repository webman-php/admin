<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\common\Tree;
use plugin\admin\app\model\Role;
use plugin\admin\app\model\Rule;
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
            $data = $this->insertInput($request);
            if (isset($data['pid']) && $data['pid'] == 0) {
                return $this->json(1, '请选择父级权限组');
            }
            $id = $this->doInsert($data);
            return $this->json(0, 'ok', ['id' => $id]);
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
        // id为1的权限权限固定为*
        if (isset($data['rules']) && $id == 1) {
            $data['rules'] = '*';
        }
        // id为1的上级pid固定为0
        if (isset($data['pid']) && $id == 1) {
            $data['pid'] = 0;
        }
        if (isset($data['pid'])) {
            if ($data['pid'] == $id) {
                return $this->json(1, '父级不能是自己');
            }
            if ($data['pid'] == 0) {
                return $this->json(1, '请选择父级权限组');
            }
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


    /**
     * 获取角色权限
     * @param Request $request
     * @return Response
     */
    public function rules(Request $request): Response
    {
        $role_id = $request->get('id');
        if (empty($role_id)) {
            return $this->json(0, 'ok', []);
        }
        $rule_id_string = Role::where('id', $role_id)->value('rules');
        if ($rule_id_string === '') {
            return $this->json(0, 'ok', []);
        }
        $rules = Rule::get();
        $include = [];
        if ($rule_id_string !== '*') {
            $include = explode(',', $rule_id_string);
        }
        return $this->formatTree($rules, $include);
    }

}
