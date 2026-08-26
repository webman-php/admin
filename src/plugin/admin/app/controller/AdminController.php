<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\common\Auth;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\Admin;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\Role;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use Throwable;

/**
 * 管理员列表 
 */
class AdminController extends Crud
{
    /**
     * 不需要鉴权的方法
     * @var array
     */
    protected $noNeedAuth = ['select'];

    /**
     * @var Admin
     */
    protected $model = null;

    /**
     * 开启auth数据限制
     * @var string
     */
    protected $dataLimit = 'auth';

    /**
     * 以id为数据限制字段
     * @var string
     */
    protected $dataLimitField = 'id';

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
     * @throws Throwable
     */
    public function index(): Response
    {
        return raw_view('admin/index');
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $query = $this->doSelect($where, $field, $order);
        if ($format === 'select') {
            return $this->formatSelect($query->get());
        }
        $paginator = $query->paginate($limit);
        $items = $paginator->items();
        $admin_ids = array_column($items, 'id');
        $roles = AdminRole::whereIn('admin_id', $admin_ids)->get();
        $roles_map = [];
        foreach ($roles as $role) {
            $roles_map[$role['admin_id']][] = $role['role_id'];
        }
        $login_admin_id = admin_id();
        foreach ($items as $index => $item) {
            $admin_id = $item['id'];
            $items[$index]['password'] = '';
            $items[$index]['roles'] = isset($roles_map[$admin_id]) ? implode(',', $roles_map[$admin_id]) : '';
            $items[$index]['show_toolbar'] = $admin_id != $login_admin_id;
        }
        return json(['code' => 0, 'msg' => 'ok', 'count' => $paginator->total(), 'data' => $items]);
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $role_ids = $this->normalizeRoleIds($request->post('roles', ''));
            if ($role_ids === null) {
                return $this->json(1, '角色参数不合法');
            }
            if (!$role_ids) {
                return $this->json(1, '至少选择一个角色组');
            }
            $is_super_admin = Auth::isSuperAdmin();
            if (!$is_super_admin && array_diff($role_ids, Auth::getScopeRoleIds())) {
                return $this->json(1, '角色超出权限范围');
            }
            if (Role::whereIn('id', $role_ids)->count() !== count($role_ids)) {
                return $this->json(1, '角色不存在');
            }
            $data = $this->insertInput($request);
            unset($data['id']);
            $admin_id = Util::db()->transaction(function () use ($data, $role_ids) {
                $admin_id = $this->doInsert($data);
                foreach ($role_ids as $id) {
                    $admin_role = new AdminRole;
                    $admin_role->admin_id = $admin_id;
                    $admin_role->role_id = $id;
                    $admin_role->save();
                }
                return $admin_id;
            });
            return $this->json(0, 'ok', ['id' => $admin_id]);
        }
        return raw_view('admin/insert');
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException|Throwable
     */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {

            $admin_id = (int)$request->post('id');
            if ($admin_id <= 0) {
                return $this->json(1, '缺少参数');
            }
            // 自己的资料及密码应通过账户设置修改，避免绕过原密码校验。
            if (!Auth::canManageAdmin($admin_id, false)) {
                return $this->json(1, '无权限更改该记录');
            }
            [$id, $data] = $this->updateInput($request);

            // 不能禁用自己
            if (isset($data['status']) && $data['status'] == 1 && $admin_id === (int)admin_id()) {
                return $this->json(1, '不能禁用自己');
            }

            // 需要更新角色
            $role_ids = null;
            $role_input = $request->post('roles');
            if ($role_input !== null) {
                $role_ids = $this->normalizeRoleIds($role_input);
                if ($role_ids === null) {
                    return $this->json(1, '角色参数不合法');
                }
                if (!$role_ids) {
                    return $this->json(1, '至少选择一个角色组');
                }
                $is_super_admin = Auth::isSuperAdmin();
                $scope_role_ids = Auth::getScopeRoleIds();
                if (!$is_super_admin && array_diff($role_ids, $scope_role_ids)) {
                    return $this->json(1, '角色超出权限范围');
                }
                if (Role::whereIn('id', $role_ids)->count() !== count($role_ids)) {
                    return $this->json(1, '角色不存在');
                }
            }

            Util::db()->transaction(function () use ($admin_id, $id, $data, $role_ids) {
                if ($role_ids !== null) {
                    $exist_role_ids = array_map('intval', AdminRole::where('admin_id', $admin_id)->pluck('role_id')->toArray());
                    // 删除账户角色
                    $delete_ids = array_diff($exist_role_ids, $role_ids);
                    AdminRole::whereIn('role_id', $delete_ids)->where('admin_id', $admin_id)->delete();
                    // 添加账户角色
                    $add_ids = array_diff($role_ids, $exist_role_ids);
                    foreach ($add_ids as $role_id) {
                        $admin_role = new AdminRole;
                        $admin_role->admin_id = $admin_id;
                        $admin_role->role_id = $role_id;
                        $admin_role->save();
                    }
                }
                $this->doUpdate($id, $data);
            });
            return $this->json(0);
        }

        return raw_view('admin/update');
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
        $ids = array_map('intval', (array)$ids);
        if (in_array((int)admin_id(), $ids, true)) {
            return $this->json(1, '不能删除自己');
        }
        foreach ($ids as $id) {
            if (!Auth::canManageAdmin($id, false)) {
                return $this->json(1, '无数据权限');
            }
        }
        $this->model->whereIn($primary_key, $ids)->each(function (Admin $admin) {
            $admin->delete();
        });
        AdminRole::whereIn('admin_id', $ids)->each(function (AdminRole $admin_role) {
            $admin_role->delete();
        });
        return $this->json(0);
    }

    /**
     * 规范化并校验角色参数格式
     * @param mixed $role_ids
     * @return array|null
     */
    private function normalizeRoleIds($role_ids): ?array
    {
        if (!is_string($role_ids) && !is_int($role_ids)) {
            return null;
        }
        if ($role_ids === '') {
            return [];
        }
        $normalized = [];
        foreach (explode(',', (string)$role_ids) as $role_id) {
            $role_id = trim($role_id);
            if (!preg_match('/^[1-9][0-9]*$/D', $role_id)) {
                return null;
            }
            $normalized[] = (int)$role_id;
        }
        return array_values(array_unique($normalized));
    }


}
