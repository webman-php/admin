<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use support\Db;
use support\Request;

class AdminRoleController extends Base
{
    /**
     * @var AdminRole
     */
    protected $model = null;

    use Crud;

    public function __construct()
    {
        $this->model = new AdminRole;
    }

    /**
     * @param Request $request
     * @return \support\Response
     */
    public function tree(Request $request)
    {
        $items = $this->model->where('status', 'normal')->get();
        $items_map = [];
        foreach ($items as $item) {
            if ($item->hide_menu) {
                continue;
            }
            $items_map[$item->id] = [
                'title' => $item->name,
                'value' => $item->id,
                'key' => $item->id,
                'pid' => $item->pid,
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

        return $this->json(0, 'ok', $formatted_items);
    }

    /**
     * 查询
     * @param Request $request
     * @return \support\Response
     */
    public function select(Request $request)
    {
        $page = $request->get('page', 1);
        $field = $request->get('field');
        $order = $request->get('order', 'descend');
        $format = $request->get('format', 'normal');
        $page_size = $request->get('pageSize', $format === 'tree' ? 1000 : 10);

        $table = $this->model->getTable();

        $allow_column = Db::select("desc $table");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = current($allow_column);
        }
        $order = $order === 'ascend' ? 'asc' : 'desc';
        $paginator = $this->model;
        // 不展示超级管理员角色
        $paginator = $paginator->where('rules', '<>', '*');
        foreach ($request->get() as $column => $value) {
            if (!$value) {
                continue;
            }
            if (isset($allow_column[$column])) {
                if (is_array($value)) {
                    if ($value[0] == 'undefined' || $value[1] == 'undefined') {
                        continue;
                    }
                    $paginator = $paginator->whereBetween($column, $value);
                } else {
                    $paginator = $paginator->where($column, $value);
                }
            }
        }
        $paginator = $paginator->orderBy($field, $order)->paginate($page_size, '*', 'page', $page);

        $items = $paginator->items();
        if ($format == 'tree') {
            $items_map = [];
            foreach ($items as $item) {
                $items_map[$item->id] = $item->toArray();
            }
            $formatted_items = [];
            foreach ($items_map as $item) {
                if ($item['pid'] && isset($items_map[$item['pid']])) {
                    $items_map[$item['pid']]['children'][] = $item;
                }
            }
            foreach ($items_map as $item) {
                if (!$item['pid']) {
                    $formatted_items[] = $item;
                }
            }
            $items = $formatted_items;
        }

        return $this->json(0, 'ok', [
            'items' => $items,
            'total' => $paginator->total()
        ]);
    }

    /**
     * 更新
     * @param Request $request
     * @return \support\Response
     */
    public function update(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        $data = $request->post('data');
        $table = $this->model->getTable();
        $allow_column = Db::select("desc $table");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }

        $data['rules'] = (array)$data['rules'];
        $pids = $data['rules'];
        if ($pids) {
            $pids = AdminRule::whereIn('id', $pids)->pluck('pid')->toArray();
            $data['rules'] = array_merge($data['rules'], $pids);
        }
        $data['rules'] = array_unique($data['rules']);

        $columns = array_column($allow_column, 'Field', 'Field');
        foreach ($data as $col => $item) {
            if (is_array($item)) {
                $data[$col] = implode(',', $item);
            }
            if ($col === 'password') {
                // 密码为空，则不更新密码
                if ($item == '') {
                    unset($data[$col]);
                    continue;
                }
                $data[$col] = Util::passwordHash($item);
            }
        }
        $datetime = date('Y-m-d H:i:s');
        if (isset($columns['updated_at']) && !isset($data['updated_at'])) {
            $data['updated_at'] = $datetime;
        }

        $this->model->where($column, $value)->update($data);
        return $this->json(0);
    }

}
