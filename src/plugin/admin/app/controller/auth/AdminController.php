<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\Admin;
use support\Request;

/**
 * Administrator Settings
 */
class AdminController extends Base
{

    /**
     * @var Admin
     */
    protected $model = null;

    /**
     * Add, delete, modify and check
     */
    use Crud;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = new Admin;
    }

    /**
     * delete
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
            return $this->json(1, 'Cannot delete myself');
        }
        $this->model->where([$column => $value])->delete();
        return $this->json(0);
    }

}
