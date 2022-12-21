<?php
namespace plugin\admin\app\common;


use plugin\admin\app\model\Admin;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\Role;

class Auth
{
    /**
     * 获取管理员及子管理员id数组
     * @param array $admin_ids
     * @return array
     */
    public static function getDescendantRoleIds(array $admin_ids = []): array
    {
        if (!$admin_ids) {
            $admin = admin();
            if (!$admin) {
                return [];
            }
            $role_ids = $admin['roles'];
            $rules = Role::whereIn('id', $role_ids)->pluck('rules')->toArray();
            if ($rules && in_array('*', $rules)) {
                return Admin::pluck('id')->toArray();
            }
        } else {
            $role_ids = AdminRole::whereIn('admin_id', $admin_ids)->pluck('role_id');
        }

        $roles = Role::get();
        $tree = new Tree($roles);
        $descendants = $tree->getDescendant($role_ids, true);
        return array_column($descendants, 'id');
    }

    /**
     * 获取管理员及子管理员id数组
     * @param array $admin_ids
     * @return array
     */
    public static function getDescendantAdminIds(array $admin_ids = []): array
    {
        return AdminRole::whereIn('role_id', static::getDescendantRoleIds())->pluck('admin_id')->toArray();
    }

    /**
     * 是否是超级管理员
     * @param int $admin_id
     * @return bool
     */
    public static function isSupperAdmin(int $admin_id = 0): bool
    {
        if (!$admin_id) {
            $roles = admin('roles');
            if (!$roles) {
                return false;
            }
        } else {
            $roles = AdminRole::where('admin_id', $admin_id)->pluck('role_id');
        }
        $rules = Role::whereIn('id', $roles)->pluck('rules');
        return $rules && in_array('*', $rules->toArray());
    }

}