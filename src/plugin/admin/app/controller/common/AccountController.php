<?php

namespace plugin\admin\app\controller\common;

use Webman\Captcha\CaptchaBuilder;
use plugin\admin\app\controller\Base;
use plugin\admin\app\model\Admin;
use plugin\admin\app\Util;
use support\Db;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * Administrator Account
 */
class AccountController extends Base
{
    /**
     * Methods that do not require login
     * @var string[]
     */
    public $noNeedLogin = ['login', 'logout', 'captcha'];

    /**
     * Methods that do not require authentication
     * @var string[]
     */
    public $noNeedAuth = ['info', 'getPermCode'];

    /**
     * @var Admin
     */
    protected $model = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->model = new Admin;
    }

    /**
     * Log in
     *
     * @param Request $request
     * @return Response
     */
    public function login(Request $request)
    {
        $captcha = $request->post('captcha');
        if (strtolower($captcha) !== session('captcha-login')) {
            return $this->json(1, 'Verification code error');
        }
        $request->session()->forget('captcha-login');
        $username = $request->post('username', '');
        $password = $request->post('password', '');
        if (!$username) {
            return $this->json(1, 'Username can not be empty');
        }
        $this->checkLoginLimit($username);
        $admin = Admin::where('username', $username)->first();
        if (!$admin || !Util::passwordVerify($password, $admin->password)) {
            return $this->json(1, 'Account does not exist or password is incorrect');
        }
        $this->removeLoginLimit($username);
        $admin = $admin->toArray();
        $session = $request->session();
        unset($admin['password']);
        $admin['roles'] = $admin['roles'] ? explode(',', $admin['roles']) : [];
        $session->set('admin', $admin);
        return $this->json(0, 'login successful', [
            'nickname' => $admin['nickname'],
            'token' => $request->sessionId(),
        ]);
    }

    /**
     * quit
     *
     * @param Request $request
     * @return Response
     */
    public function logout(Request $request)
    {
        $request->session()->delete('admin');
        return $this->json(0);
    }


    /**
     * Get login information
     *
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
            'nickname' => $admin['nickname'],
            'desc' => 'manager',
            'avatar' => $admin['avatar'],
            'token' => $request->sessionId(),
            'userId' => $admin['id'],
            'username' => $admin['username'],
            'email' => $admin['email'],
            'mobile' => $admin['mobile'],
            'roles' => []
        ];
        return $this->json(0, 'ok', $info);
    }

    /**
     * Verification Code
     * @param Request $request
     * @param $type
     * @return Response
     */
    public function captcha(Request $request, $type = 'login')
    {
        $builder = new CaptchaBuilder;
        $builder->build();
        $request->session()->set("captcha-$type", strtolower($builder->getPhrase()));
        $img_content = $builder->get();
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Get permission code (currently has no effect)
     * @return Response
     */
    public function getPermCode()
    {
        return $this->json(0, 'ok', ['1000', '3000', '5000']);
    }

    /**
     * renew
     *
     * @param Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $allow_column = [
            'nickname' => 'nickname',
            'avatar' => 'avatar',
            'email' => 'email',
            'mobile' => 'mobile',
        ];

        $data = $request->post();
        $update_data = [];
        foreach ($allow_column as $key => $column) {
            if (isset($data[$key])) {
                $update_data[$column] = $data[$key];
            }
        }
        if (isset($update_data['password'])) {
            $update_data['password'] = Util::passwordHash($update_data['password']);
        }
        Admin::where('id', admin_id())->update($update_data);
        $admin = admin();
        unset($update_data['password']);
        foreach ($update_data as $key => $value) {
            $admin[$key] = $value;
        }
        $request->session()->set('admin', $admin);
        return $this->json(0);
    }

    /**
     * change Password
     *
     * @param Request $request
     * @return Response
     */
    public function password(Request $request)
    {
        $hash = admin('password');
        $password = $request->post('password');
        if (!$password) {
            return $this->json(2, 'password can not be blank');
        }
        if (!Util::passwordVerify($request->post('old_password'), $hash)) {
            return $this->json(1, 'Original password is incorrect');
        }
        $update_data = [
            'password' => Util::passwordHash($password)
        ];
        Admin::where('id', admin_id())->update($update_data);
        return $this->json(0);
    }

    /**
     * Check login frequency limit
     *
     * @param $username
     * @return void
     * @throws BusinessException
     */
    protected function checkLoginLimit($username)
    {
        $limit_log_path = runtime_path() . '/login';
        if (!is_dir($limit_log_path)) {
            mkdir($limit_log_path, 0777, true);
        }
        $limit_file = $limit_log_path . '/' . md5($username) . '.limit';
        $time = date('YmdH') . ceil(date('i')/5);
        $limit_info = [];
        if (is_file($limit_file)) {
            $json_str = file_get_contents($limit_file);
            $limit_info = json_decode($json_str, true);
        }

        if (!$limit_info || $limit_info['time'] != $time) {
            $limit_info = [
                'username' => $username,
                'count' => 0,
                'time' => $time
            ];
        }
        $limit_info['count']++;
        file_put_contents($limit_file, json_encode($limit_info));
        if ($limit_info['count'] >= 5) {
            throw new BusinessException('Too many login failures, please try again in 5 minutes');
        }
    }


    /**
     * Remove login restrictions
     *
     * @param $username
     * @return void
     */
    protected function removeLoginLimit($username)
    {
        $limit_log_path = runtime_path() . '/login';
        $limit_file = $limit_log_path . '/' . md5($username) . '.limit';
        if (is_file($limit_file)) {
            unlink($limit_file);
        }
    }

}
