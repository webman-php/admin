<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\AdminRule;
use support\Request;

class AdminRuleController extends Base
{
    /**
     * @var AdminRule
     */
    protected $model = null;

    use Crud;

    public function __construct()
    {
        $this->model = new AdminRule;
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
                'title' => $item->title,
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
     * 删除
     * @param Request $request
     * @return \support\Response
     * @throws \support\exception\BusinessException
     */
    public function delete(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        $item = $this->model->where($column, $value)->first();
        if (!$item) {
            return $this->json(1, '记录不存在');
        }
        $delete_ids = $children_ids = [$item['id']];
        while($children_ids) {
            $children_ids = $this->model->whereIn('pid', $children_ids)->pluck('id')->toArray();
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
        $this->model->whereIn('id', $delete_ids)->delete();
        return $this->json(0);
    }


}
