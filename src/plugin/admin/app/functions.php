<?php
/**
 * Here is your custom functions.
 */

use app\model\User;
use plugin\admin\app\model\Admin;
use plugin\admin\app\model\Role;
use plugin\admin\app\model\Rule;

/**
 * 当前管理员id
 * @return integer|null
 */
function admin_id(): ?int
{
    return session('admin.id');
}

/**
 * 当前管理员
 * @param null|array|string $fields
 * @return array|mixed|null
 */
function admin($fields = null)
{
    refresh_admin_session();
    if (!$admin = session('admin')) {
        return null;
    }
    if ($fields === null) {
        return $admin;
    }
    if (is_array($fields)) {
        $results = [];
        foreach ($fields as $field) {
            $results[$field] = $admin[$field] ?? null;
        }
        return $results;
    }
    return $admin[$fields] ?? null;
}

/**
 * 当前是否是超级管理员
 * @return bool
 */
function is_supper_admin(): bool
{
    $roles = admin('roles');
    if (!$roles) {
        return false;
    }
    $rules = Role::whereIn('id', $roles)->pluck('rules');
    return $rules && in_array('*', $rules->toArray());
}

/**
 * 获取当前管理员权限
 * @return array
 */
function admin_rules(): array
{
    $roles = admin('roles');
    if (!$roles) {
        return [];
    }
    $rule_ids = Role::whereIn('id', $roles)->pluck('rules');
    if (!$rule_ids) {
        return [];
    }
    $rule_id_strings = $rule_ids->toArray();
    $rule_ids = [];
    foreach ($rule_id_strings as $id_string) {
        if (!$id_string) {
            continue;
        }
        $rule_ids = array_merge($rule_ids, explode(',', $id_string));
    }
    if (in_array('*', $rule_ids)) {
        $rules = Rule::pluck('key', 'id');
    } else {
        $rules = Rule::whereIn('id', $rule_ids)->pluck('key', 'id');
    }
    return $rules ? $rules->toArray() : [];
}

/**
 * 当前登录用户id
 * @return integer|null
 */
function user_id(): ?int
{
    return session('user.id');
}

/**
 * 当前登录用户
 * @param null|array|string $fields
 * @return array|mixed|null
 */
function user($fields = null)
{
    refresh_user_session();
    if (!$user = session('user')) {
        return null;
    }
    if ($fields === null) {
        return $user;
    }
    if (is_array($fields)) {
        $results = [];
        foreach ($fields as $field) {
            $results[$field] = $user[$field] ?? null;
        }
        return $results;
    }
    return $user[$fields] ?? null;
}

/**
 * 刷新当前管理员session
 * @param bool $force
 * @return void
 */
function refresh_admin_session(bool $force = false)
{
    if (!$admin_id = admin_id()) {
        return null;
    }
    $time_now = time();
    // session在2秒内不刷新
    $session_ttl = 2;
    $session_last_update_time = session('admin.session_last_update_time', 0);
    if (!$force && $time_now - $session_last_update_time < $session_ttl) {
        return null;
    }
    $session = request()->session();
    $admin = Admin::find($admin_id);
    if (!$admin) {
        $session->forget('admin');
        return null;
    }
    $admin = $admin->toArray();
    unset($admin['password']);
    $admin['roles'] = $admin['roles'] ? explode(',', $admin['roles']) : [];
    $admin['session_last_update_time'] = $time_now;
    $session->set('admin', $admin);
}


/**
 * 刷新当前用户session
 * @param bool $force
 * @return void
 */
function refresh_user_session(bool $force = false)
{
    if (!$user_id = user_id()) {
        return null;
    }
    $time_now = time();
    // session在2秒内不刷新
    $session_ttl = 2;
    $session_last_update_time = session('user.session_last_update_time', 0);
    if (!$force && $time_now - $session_last_update_time < $session_ttl) {
        return null;
    }
    $session = request()->session();
    $user = User::find($user_id);
    if (!$user) {
        $session->forget('user');
        return null;
    }
    $user = $user->toArray();
    unset($user['password']);
    $user['session_last_update_time'] = $time_now;
    $session->set('user', $user);
}