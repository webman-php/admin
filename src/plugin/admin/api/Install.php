<?php

namespace plugin\admin\api;

class Install
{
    /**
     * Install
     *
     * @param $version
     * @return void
     */
    public static function install($version)
    {
        // Import menu
        Menu::import(static::getMenus());
    }

    /**
     * Uninstall
     *
     * @param $version
     * @return void
     */
    public static function uninstall($version)
    {
        // Delete menu
        foreach (static::getMenus() as $menu) {
            Menu::delete($menu['name']);
        }
    }

    /**
     * renew
     *
     * @param $from_version
     * @param $to_version
     * @param $context
     * @return void
     */
    public static function update($from_version, $to_version, $context = null)
    {
        // Delete unused menus
        if (isset($context['previous_menus'])) {
            static::removeUnnecessaryMenus($context['previous_menus']);
        }
        // Import new menu
        Menu::import(static::getMenus());
    }

    /**
     * Data collection before update etc
     *
     * @param $from_version
     * @param $to_version
     * @return array|array[]
     */
    public static function beforeUpdate($from_version, $to_version)
    {
        // Get the old menu before updating, passed the context to update
        return ['previous_menus' => static::getMenus()];
    }

    /**
     * Get menu
     *
     * @return array|mixed
     */
    public static function getMenus()
    {
        clearstatcache();
        if (is_file($menu_file = __DIR__ . '/../config/menu.php')) {
            $menus = include $menu_file;
            return $menus ?: [];
        }
        return [];
    }

    /**
     * Remove unwanted menus
     *
     * @param $previous_menus
     * @return void
     */
    public static function removeUnnecessaryMenus($previous_menus)
    {
        $menus_to_remove = array_diff(Menu::column($previous_menus, 'name'), Menu::column(static::getMenus(), 'name'));
        foreach ($menus_to_remove as $name) {
            Menu::delete($name);
        }
    }

}