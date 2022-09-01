<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use plugin\admin\app\Util;
use support\Db;
use support\Request;

/**
 * 管理员角色设置
 */
class AdminRoleController extends Base
{
    /**
     * @var AdminRole
     */
    protected $model = null;

    /**
     * 增删改查
     */
    use Crud;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new AdminRole;
    }

    /**
     * 更新
     *
     * @param Request $request
     * @return \support\Response
     */
    public function update(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        $data = $request->post('data');
        $table = $this->model->getTable();
        $allow_column = Util::db()->select("desc $table");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }

        $data['rules'] = array_filter(array_unique((array)$data['rules']));

        $item = $this->model->where($column, $value)->first();
        if (!$item) {
            return $this->json(1, '记录不存在');
        }
        if ($item->id == 1) {
            $data['rules'] = '*';
        }

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

        $this->model->where($column, $value)->update($data);
        return $this->json(0);
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @throws \Support\Exception\BusinessException
     */
    public function delete(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        $item = $this->model->where($column, $value)->first();
        if (!$item) {
            return $this->json(0);
        }
        if ($item->id == 1) {
            return $this->json(1, '无法删除超级管理员角色');
        }
        $this->model->where('id', $item->id)->delete();
        return $this->json(0);
    }

}
