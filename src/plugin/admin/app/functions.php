<?php
/**
 * Here is your custom functions.
 */

/**
 * 当前登录管理员id
 *
 * @return mixed|null
 */
function admin_id()
{
    return session('admin.id');
}

/**
 * 当前管理员
 *
 * @param null|array|string $fields
 * @return array|mixed|null
 */
function admin($fields = null)
{
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
 *
 * @return mixed|null
 */
function user_id()
{
    return session('user.id');
}

/**
 * 当前登录用户
 *
 * @param null|array|string $fields
 * @return array|mixed|null
 */
function user($fields = null)
{
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