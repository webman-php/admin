<?php
/**
 * Here is your custom functions.
 */

use support\Db;

function admin_id()
{
    return session('admin_id');
}

function admin($column = null)
{
    if (!$admin_id = admin_id()) {
        return null;
    }
    if ($column) {
        if (!is_array($column)) {
            return Db::connection('plugin.admin.mysql')->table('wa_admins')->where('id', $admin_id)->value($column);
        }
        return (array)Db::connection('plugin.admin.mysql')->table('wa_admins')->where('id', $admin_id)->select($column)->first();
    }
    return (array)Db::connection('plugin.admin.mysql')->table('wa_admins')->where('id', $admin_id)->first();
}

function user_id()
{
    return session('admin_id');
}

function user($column = null)
{
    if (!$user_id = user_id()) {
        return null;
    }
    if ($column) {
        if (!is_array($column)) {
            return Db::connection('plugin.admin.mysql')->table('wa_users')->where('id', $user_id)->value($column);
        }
        return (array)Db::connection('plugin.admin.mysql')->table('wa_users')->where('id', $user_id)->select($column)->first();
    }
    return (array)Db::connection('plugin.admin.mysql')->table('wa_users')->where('id', $user_id)->first();
}