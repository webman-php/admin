<?php
/**
 * Here is your custom functions.
 */

use app\model\User;
use plugin\admin\app\model\Admin;
use support\exception\BusinessException;

/**
 * 当前管理员id
 * @return integer|null
 */
function admin_id()
{
    return session('admin.id');
}

/**
 * 当前管理员
 * @param null|array|string $fields
 * @return array|mixed|null
 * @throws BusinessException
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
 * 当前登录用户id
 * @return integer|null
 */
function user_id()
{
    return session('user.id');
}

/**
 * 当前登录用户
 * @param null|array|string $fields
 * @return array|mixed|null
 * @throws BusinessException
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
 * @throws BusinessException
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
    $admin = Admin::find($admin_id)->toArray();
    if (!$admin) {
        throw new BusinessException('当前账户不存在或已被禁用');
    }
    unset($admin['password']);
    $admin['roles'] = $admin['roles'] ? explode(',', $admin['roles']) : [];
    $admin['session_last_update_time'] = $time_now;
    request()->session()->set('admin', $admin);
}


/**
 * 刷新当前用户session
 * @param bool $force
 * @return void
 * @throws BusinessException
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
    $user = User::find($user_id)->toArray();
    if (!$user) {
        throw new BusinessException('当前账户不存在或已被禁用');
    }
    unset($user['password']);
    $user['session_last_update_time'] = $time_now;
    request()->session()->set('user', $user);
}