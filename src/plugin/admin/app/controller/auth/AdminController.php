<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\Admin;
use plugin\admin\app\Util;
use support\Db;
use support\Request;
use support\Response;
use function admin;

/**
 * 管理员设置
 */
class AdminController extends Base
{

    public $noNeedLogin = ['login', 'logout'];

    public $noNeedAuth = ['info', 'getPermCode'];

    /**
     * @var Admin
     */
    protected $model = null;

    use Crud;

    public function __construct()
    {
        $this->model = new Admin;
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
        if ($value == admin_id()) {
            return $this->json(1, '不能删除自己');
        }
        $this->model->where([$column => $value])->delete();
        return $this->json(0);
    }

    /**
     * 登录
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        $admin = Db::connection('plugin.admin.mysql')->table('wa_admins')->where('username', $username)->first();
        if (!$admin || !Util::passwordVerify($password, $admin->password)) {
            return $this->json(1, '账户不存在或密码错误');
        }
        $session = $request->session();
        $session->set('admin_id', $admin->id);
        return $this->json(0, '登录成功', [
            'realName' => $admin->nickname,
            'token' => $request->sessionId(),
        ]);
    }

    /**
     * 退出
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        $request->session()->delete('admin_id');
        return $this->json(0);
    }


    /**
     * 获取登录信息
     * @param Request $request
     * @return Response
     */
    public function info(Request $request)
    {
        $admin = admin();
        if (!$admin) {
            return $this->json(1);
        }
        $info = [
            'realName' => $admin['nickname'],
            'desc' => 'manager',
            'avatar' => $admin['avatar'],
            'token' => $request->sessionId(),
            'userId' => $admin['id'],
            'username' => $admin['username'],
            'roles' => []
        ];
        return $this->json(0, 'ok', $info);
    }

    /**
     * 获取权限码
     * @return Response
     */
    public function getPermCode()
    {
        return $this->json(0, 'ok', ['1000', '3000', '5000']);
    }

}
