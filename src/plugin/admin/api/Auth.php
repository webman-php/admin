<?php
namespace plugin\admin\api;

use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use support\exception\BusinessException;
use function admin;

/**
 * Authentication interface provided externally
 */
class Auth
{
    /**
     * judgment authority
     * Throw exception if no permission
     *
     * @param string $controller
     * @param string $action
     * @return void
     * @throws \ReflectionException
     */
    public static function access(string $controller, string $action)
    {
        $code = 0;
        $msg = '';
        if (!static::canAccess($controller, $action, $code, $msg)) {
            throw new BusinessException($msg, $code);
        }
    }

    /**
     * Determine whether there is permission
     *
     * @param string $controller
     * @param string $action
     * @param int $code
     * @param string $msg
     * @return bool
     * @throws \ReflectionException
     */
    public static function canAccess(string $controller, string $action, int &$code = 0, string &$msg = '')
    {
        // Get controller authentication information
        $class = new \ReflectionClass($controller);
        $properties = $class->getDefaultProperties();
        $noNeedLogin = $properties['noNeedLogin'] ?? [];
        $noNeedAuth = $properties['noNeedAuth'] ?? [];

        // No login required
        if (in_array($action, $noNeedLogin)) {
            return true;
        }

        // Get login information
        $admin = admin();
        if (!$admin) {
            $msg = 'please sign in';
            // 401is not logged in fixed return code
            $code = 401;
            return false;
        }

        // No authentication required
        if (in_array($action, $noNeedAuth)) {
            return true;
        }

        // Current administrator has no role
        $roles = $admin['roles'];
        if (!$roles) {
            $msg = 'No permission';
            $code = 2;
            return false;
        }

        // Role has no rules
        $rules = AdminRole::whereIn('id', $roles)->pluck('rules');
        $rule_ids = [];
        foreach ($rules as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rule_ids = array_merge($rule_ids, explode(',', $rule_string));
        }
        if (!$rule_ids) {
            $msg = 'No permission';
            $code = 2;
            return false;
        }

        // Super Admin
        if (in_array('*', $rule_ids)){
            return true;
        }

        // No rules for current controller
        $rule = AdminRule::where(function ($query) use ($controller, $action) {
            $query->where('name', "$controller@$action")->orWhere('name', $controller);
        })->whereIn('id', $rule_ids)->first();

        if (!$rule) {
            $msg = 'No permission';
            $code = 2;
            return false;
        }

        return true;
    }

}