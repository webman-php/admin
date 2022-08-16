<?php

namespace plugin\admin\app\controller\common;

use plugin\admin\app\controller\Base;
use plugin\admin\app\model\Admin;
use plugin\admin\app\Util;
use support\Request;

class AccountController extends Base
{
    /**
     * @var Admin
     */
    protected $model = null;

    public function __construct()
    {
        $this->model = new Admin;
    }

    public function info(Request $request)
    {
        return $this->json(0, 'ok', admin(['nickname', 'mobile', 'avatar', 'email']));
    }

    public function update(Request $request)
    {
        $allow_column = [
            'nickname',
            'avatar',
            'email',
            'mobile',
        ];

        $data = $request->post();
        $update_data = [];
        foreach ($allow_column as $column) {
            if (isset($data[$column])) {
                $update_data[$column] = $data[$column];
            }
        }
        if (isset($update_data['password'])) {
            $update_data['password'] = Util::passwordHash($update_data['password']);
        }
        Admin::where('id', admin_id())->update($update_data);
        return $this->json(0);
    }

    public function password(Request $request)
    {
        $hash = admin('password');
        $password = $request->post('password');
        if (!$password) {
            return $this->json(2, '密码不能为空');
        }
        if (!Util::passwordVerify($request->post('old_password'), $hash)) {
            return $this->json(1, '原始密码不正确');
        }
        $update_data = [
            'password' => Util::passwordHash($password)
        ];
        Admin::where('id', admin_id())->update($update_data);
        return $this->json(0);
    }

}
