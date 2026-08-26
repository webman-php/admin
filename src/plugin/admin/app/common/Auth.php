<?php
namespace plugin\admin\app\common;


use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\Role;

class Auth
{
    /**
     * 获取权限范围内的所有角色id
     * @param bool $with_self
     * @return array
     * @throws \Exception
     */
    public static function getScopeRoleIds(bool $with_self = false): array
    {
        if (!$admin = admin()) {
            return [];
        }
        $role_ids = $admin['roles'];
        $rules = Role::whereIn('id', $role_ids)->pluck('rules')->toArray();
        if ($rules && in_array('*', $rules, true)) {
            return array_map('intval', Role::pluck('id')->toArray());
        }

        $roles = Role::get();
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, $with_self);
        return array_map('intval', array_column($descendants, 'id'));
    }

    /**
     * 获取权限范围内的所有管理员id
     * @param bool $with_self
     * @return array
     * @throws \Exception
     */
    public static function getScopeAdminIds(bool $with_self = false): array
    {
        $role_ids = static::getScopeRoleIds();
        $admin_ids = [];
        if ($role_ids) {
            $admin_ids = array_values(array_unique(array_map(
                'intval',
                AdminRole::whereIn('role_id', $role_ids)->pluck('admin_id')->toArray()
            )));
            if ($admin_ids) {
                // 一个管理员可能同时拥有多个角色。只要其中一个角色超出当前范围，
                // 就不能因为另一个下级角色而将该管理员纳入可管理范围。
                $out_of_scope_admin_ids = AdminRole::whereIn('admin_id', $admin_ids)
                    ->whereNotIn('role_id', $role_ids)
                    ->pluck('admin_id')
                    ->toArray();
                $admin_ids = array_values(array_diff($admin_ids, array_map('intval', $out_of_scope_admin_ids)));
            }
        }
        if ($with_self) {
            $admin_ids[] = (int)admin_id();
        }
        return array_values(array_unique($admin_ids));
    }

    /**
     * 当前管理员是否有权管理目标管理员
     * @param int $target_admin_id
     * @param bool $allow_self 是否允许操作自己
     * @return bool
     * @throws \Exception
     */
    public static function canManageAdmin(int $target_admin_id, bool $allow_self = true): bool
    {
        if ($target_admin_id <= 0) {
            return false;
        }
        if ($target_admin_id === (int)admin_id()) {
            return $allow_self;
        }
        if (static::isSuperAdmin()) {
            return true;
        }
        if (static::isSuperAdmin($target_admin_id)) {
            return false;
        }
        $exist_role_ids = array_map('intval', AdminRole::where('admin_id', $target_admin_id)->pluck('role_id')->toArray());
        $scope_role_ids = static::getScopeRoleIds();
        return $exist_role_ids && !array_diff($exist_role_ids, $scope_role_ids);
    }

    /**
     * 兼容旧版本
     * @param int $admin_id
     * @return bool
     * @throws \Exception
     * @deprecated
     */
    public static function isSupperAdmin(int $admin_id = 0): bool
    {
        return static::isSuperAdmin($admin_id);
    }

    /**
     * 是否是超级管理员
     * @param int $admin_id
     * @return bool
     * @throws \Exception
     */
    public static function isSuperAdmin(int $admin_id = 0): bool
    {
        if (!$admin_id) {
            if (!$roles = admin('roles')) {
                return false;
            }
        } else {
            $roles = AdminRole::where('admin_id', $admin_id)->pluck('role_id');
        }
        $rules = Role::whereIn('id', $roles)->pluck('rules')->toArray();
        return in_array('*', $rules, true);
    }

}
