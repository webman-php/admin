<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\Admin;
use support\Request;

/**
 * 管理员设置
 */
class AdminController extends Base
{

    /**
     * @var Admin
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
        $this->model = new Admin;
    }

    /**
     * 删除
     *
     * @param Request $request
     * @return \support\Response
     * @throws \Support\Exception\BusinessException
     */
    public function delete(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        if ($value == admin_id()) {
            return $this->json(1, '不能删除自己');
        }
        $this->model->where([$column => $value])->delete();
        return $this->json(0);
    }

}
