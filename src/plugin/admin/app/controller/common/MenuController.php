<?php

namespace plugin\admin\app\controller\common;

use plugin\admin\app\controller\Base;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use plugin\admin\app\Util;
use function admin;

class MenuController extends Base
{
    /**
     * 不需要权限的方法
     *
     * @var string[]
     */
    public $noNeedAuth = ['get', 'tree'];

    /**
     * @var AdminRule
     */
    protected $model = null;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new AdminRule;
    }

    /**
     * 获取菜单
     *
     * @return \support\Response
     */
    function get()
    {
        [$rules, $items] = $this->getRulesAndItems();
        $items_map = [];
        foreach ($items as $item) {
            $items_map[$item['id']] = $item;
        }
        $formatted_items = [];
        foreach ($items_map as $index => $item) {
            foreach (['title', 'icon', 'hide_menu', 'frame_src'] as $name) {
                $value = $item[$name];
                unset($items_map[$index][$name]);
                if (!$value) {
                    continue;
                }
                $items_map[$index]['meta'][Util::smCamel($name)] = $value;
            }
            if ($item['pid'] && isset($items_map[$item['pid']])) {
                $items_map[$item['pid']]['children'][] = &$items_map[$index];
            }
        }
        foreach ($items_map as $item) {
            if (!$item['pid']) {
                $formatted_items[] = $item;
            }
        }

        // 超级管理员权限为 *
        if (!in_array('*', $rules)) {
            $this->removeUncontain($formatted_items, 'id', $rules);
        }
        $this->removeUncontain($formatted_items, 'is_menu', [1]);
        $formatted_items = array_values($formatted_items);
        foreach ($formatted_items as &$item) {
            $this->arrayValues($item);
        }
        return $this->json(0, 'ok', $formatted_items);
    }

    /**
     * 获取菜单树
     *
     * @return \support\Response
     */
    function tree()
    {
        [$rules, $items] = $this->getRulesAndItems();

        $items_map = [];
        foreach ($items as $item) {
            if ($item['hide_menu']) {
                continue;
            }
            $items_map[$item['id']] = [
                'title' => $item['title'],
                'value' => (string)$item['id'],
                'key' => (string)$item['id'],
                'id' => $item['id'],
                'is_menu' => $item['is_menu'],
                'pid' => $item['pid'],
            ];
        }
        $formatted_items = [];
        foreach ($items_map as $index => $item) {
            if ($item['pid'] && isset($items_map[$item['pid']])) {
                $items_map[$item['pid']]['children'][] = &$items_map[$index];
            }
        }
        foreach ($items_map as $item) {
            if (!$item['pid']) {
                $formatted_items[] = $item;
            }
        }

        // 超级管理员权限为 *
        if (!in_array('*', $rules)) {
            $this->removeUncontain($formatted_items, 'id', $rules);
        }
        $this->removeUncontain($formatted_items, 'is_menu', [1]);
        $this->recursiveRemove($formatted_items, ['id', 'pid', 'is_menu']);
        $formatted_items = array_values($formatted_items);
        foreach ($formatted_items as &$item) {
            $this->arrayValues($item);
        }

        return  $this->json(0, 'ok', $formatted_items);
    }

    /**
     * 移除不包含某些数据的数组
     *
     * @param $array
     * @param $key
     * @param $values
     * @return void
     */
    protected function removeUncontain(&$array, $key, $values)
    {
        foreach ($array as $k => &$item) {
            if (!is_array($item)) {
                continue;
            }
            if (!$this->arrayContain($item, $key, $values)) {
                unset($array[$k]);
            } else {
                if (!isset($item['children'])) {
                    continue;
                }
                $this->removeUncontain($item['children'], $key, $values);
            }
        }
    }

    /**
     * 判断数组是否包含某些数据
     *
     * @param $array
     * @param $key
     * @param $values
     * @return bool
     */
    protected function arrayContain(&$array, $key, $values)
    {
        if (!is_array($array)) {
            return false;
        }
        if (isset($array[$key]) && in_array($array[$key], $values)) {
            return true;
        }
        if (!isset($array['children'])) {
            return false;
        }
        foreach ($array['children'] as $item) {
            if ($this->arrayContain($item, $key, $values)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 递归删除某些key
     *
     * @param $array
     * @param $keys
     * @return void
     */
    protected function recursiveRemove(&$array, $keys)
    {
        if (!is_array($array)) {
            return;
        }
        foreach ($keys as $key) {
            unset($array[$key]);
        }
        foreach ($array as &$item) {
            $this->recursiveRemove($item, $keys);
        }
    }

    /**
     * 获取权限规则
     * @return array
     */
    protected function getRulesAndItems()
    {
        $roles = admin('roles');
        $rules_strings = $roles ? AdminRole::whereIn('id', $roles)->pluck('rules') : [];
        $rules = [];
        foreach ($rules_strings as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rules = array_merge($rules, explode(',', $rule_string));
        }

        $items = AdminRule::get()->toArray();
        return [$rules, $items];
    }

    /**
     * 递归重建数组下标
     *
     * @return void
     */
    protected function arrayValues(&$array)
    {
        if (!is_array($array) || !isset($array['children'])) {
            return;
        }
        $array['children'] = array_values($array['children']);

        foreach ($array['children'] as &$child) {
            $this->arrayValues($child);
        }
    }

}
