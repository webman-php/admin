<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\common\Auth;
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
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {
        $id = $request->get('id');
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $role_ids = Auth::getScopeRoleIds(true);
        if (!$id) {
            $where['id'] = ['in', $role_ids];
        } elseif (!in_array($id, $role_ids)) {
            throw new BusinessException('无权限');
        }
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
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
            $pid = $data['pid'] ?? null;
            if ($pid) {
                return $this->json(1, '请选择父级角色组');
            }
            if (!Auth::isSupperAdmin() && !in_array($pid, Auth::getScopeRoleIds(true))) {
                return $this->json(1, '父级角色组超出权限范围');
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
        $is_supper_admin = Auth::isSupperAdmin();
        $descendant_role_ids = Auth::getScopeRoleIds();
        if (!$is_supper_admin && !in_array($id, $descendant_role_ids)) {
            return $this->json(1, '无数据权限');
        }
        // id为1的权限权限固定为*
        if (isset($data['rules']) && $id == 1) {
            $data['rules'] = '*';
        }
        // id为1的上级pid固定为0
        if (isset($data['pid']) && $id == 1) {
            $data['pid'] = 0;
        }

        if (key_exists('pid', $data)) {
            $pid = $data['pid'];
            if (!$pid) {
                return $this->json(1, '请选择父级角色组');
            }
            if ($pid == $id) {
                return $this->json(1, '父级不能是自己');
            }
            if (!$is_supper_admin && !in_array($pid, Auth::getScopeRoleIds(true))) {
                return $this->json(1, '父级超出权限范围');
            }
        }

        $this->doUpdate($id, $data);
        return $this->json(0);
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response
    {
        $ids = $this->deleteInput($request);
        if (in_array(1, $ids)) {
            return $this->json(1, '无法删除超级管理员角色');
        }
        if (!Auth::isSupperAdmin() && array_diff($ids, Auth::getScopeRoleIds())) {
            return $this->json(1, '无删除权限');
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
        if (!Auth::isSupperAdmin() && !in_array($role_id, Auth::getScopeRoleIds(true))) {
            return $this->json(1, '角色组超出权限范围');
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
        $items = [];
        foreach ($rules as $item) {
            $items[] = [
                'name' => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'id' => $item->id,
                'pid' => $item->pid,
            ];
        }
        $tree = new Tree($items);
        return $this->json(0, 'ok', $tree->getTree($include));
    }


}
