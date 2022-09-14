<?php
namespace plugin\admin\api;

use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use support\exception\BusinessException;
use function admin;

/**
 * 对外提供的菜单接口
 */
class Menu
{

    /**
     * 根据名字获得菜单
     *
     * @param $name
     * @return array
     */
    public static function get($name)
    {
        $menu = AdminRule::where('name', $name)->first();
        return $menu ? $menu->toArray() : null;
    }

    /**
     * 根据id获得菜单
     *
     * @param $id
     * @return array
     */
    public static function find($id)
    {
        return AdminRule::find($id)->toArray();
    }

    /**
     * 添加菜单
     *
     * @param array $menu
     * @return int
     */
    public static function add(array $menu)
    {
        $item = new AdminRule;
        foreach ($menu as $key => $value) {
            $item->$key = $value;
        }
        if (!empty($item->frame_src)) {
            $item->component = '';
        }
        $item->save();
        return $item->id;
    }

    /**
     * 导入菜单
     *
     * @param array $menu_tree
     * @return void
     */
    public static function import(array $menu_tree)
    {
        if (is_numeric(key($menu_tree)) && !isset($menu_tree['name'])) {
            foreach ($menu_tree as $item) {
                static::import($item);
            }
            return;
        }
        $children = $menu_tree['children'] ?? [];
        unset($menu_tree['children']);
        if ($old_menu = Menu::get($menu_tree['name'])) {
            $pid = $old_menu['id'];
            AdminRule::where('name', $menu_tree['name'])->update($menu_tree);
        } else {
            $pid = static::add($menu_tree);
        }
        foreach ($children as $menu) {
            $menu['pid'] = $pid;
            static::import($menu);
        }
    }

    /**
     * 删除菜单
     *
     * @param $name
     * @return void
     */
    public static function delete($name)
    {
        $item = AdminRule::where('name', $name)->first();
        if (!$item) {
            return;
        }
        // 子规则一起删除
        $delete_ids = $children_ids = [$item['id']];
        while($children_ids) {
            $children_ids = AdminRule::whereIn('pid', $children_ids)->pluck('id')->toArray();
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
        AdminRule::whereIn('id', $delete_ids)->delete();
    }


    /**
     * 获取菜单中某个(些)字段的值
     *
     * @param $menus
     * @param $column
     * @return array|mixed
     */
    public static function column($menu, $column = null, $index = null)
    {
        $values = [];
        if (is_numeric(key($menu)) && !isset($menu['name'])) {
            foreach ($menu as $item) {
                $values = array_merge($values, static::column($item, $column, $index));
            }
            return $values;
        }

        $children = $menu['children'] ?? [];
        unset($menu['children']);
        if ($column === null) {
            if ($index) {
                $values[$menu[$index]] = $menu;
            } else {
                $values[] = $menu;
            }
        } else {
            if (is_array($column)) {
                $item = [];
                foreach ($column as $f) {
                    $item[$f] = $menu[$f] ?? null;
                }
                if ($index) {
                    $values[$menu[$index]] = $item;
                } else {
                    $values[] = $item;
                }
            } else {
                $value = $menu[$column] ?? null;
                if ($index) {
                    $values[$menu[$index]] = $value;
                } else {
                    $values[] = $value;
                }
            }
        }
        foreach ($children as $child) {
            $values = array_merge($values, static::column($child, $column, $index));
        }
        return $values;
    }

}