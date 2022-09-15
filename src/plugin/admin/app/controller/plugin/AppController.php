<?php

namespace plugin\admin\app\controller\plugin;

use GuzzleHttp\Client;
use plugin\admin\api\Auth;
use plugin\admin\app\controller\Base;
use plugin\admin\app\Util;
use support\Log;
use support\Request;
use function base_path;
use function config;
use function get_realpath;

class AppController extends Base
{
    protected $noNeedAuth = ['schema', 'captcha'];

    /**
     * 列表
     *
     * @param Request $request
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(Request $request)
    {
        $installed = [];
        clearstatcache();
        foreach (glob(base_path() . '/plugin/*') as $dir) {
            $name = ltrim(strrchr($dir, DIRECTORY_SEPARATOR), DIRECTORY_SEPARATOR);
            if (is_dir($dir) && $version = $this->getPluginVersion($name)) {
                $installed[$name] = $version;
            }
        }
        $client = $this->httpClient();
        $response = $client->get('/api/app/list', ['query' => $request->get()]);
        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);
        if (!$data) {
            $msg = "/api/app/list return $content";
            echo "msg\r\n";
            Log::error($msg);
            return $this->json(1, '获取数据出错');
        }
        foreach ($data['result']['items'] as $key => $item) {
            $name = $item['name'];
            $data['result']['items'][$key]['installed'] = $installed[$name] ?? 0;
        }
        return $this->json(0, 'ok', $data['result']);
    }

    /**
     * 摘要
     *
     * @param Request $request
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function schema(Request $request)
    {
        $client = $this->httpClient();
        $response = $client->get('/api/app/schema', ['query' => $request->get()]);
        $data = json_decode($response->getBody()->getContents(), true);
        return $this->json(0, 'ok', $data['result']);
    }

    /**
     * 安装
     *
     * @param Request $request
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function install(Request $request)
    {
        $name = $request->post('name');
        $version = $request->post('version');
        $installed_version = $this->getPluginVersion($name);
        $host = $request->host(true);
        if (!$name || !$version) {
            return $this->json(1, '缺少参数');
        }

        $user = session('app-plugin-user');
        if (!$user) {
            return $this->json(0, '请登录', [
                'code' => 401,
                'message' => '请登录'
            ]);
        }
        $client = $this->httpClient();
        $response = $client->post('/api/app/download', [
            'form_params' => [
                'name' => $name,
                'uid' => $user['uid'],
                'token' => session('app-plugin-token'),
                'referer' => $host,
                'version' => $version,
            ]
        ]);

        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);
        if (!$data) {
            $msg = "/api/app/download return $content";
            echo "msg\r\n";
            Log::error($msg);
        }
        if ($data['code']) {
            if ($data['code'] == -1) {
                return $this->json(0, '请登录', [
                    'code' => 401,
                    'message' => '请登录'
                ]);
            }
            return $this->json($data['code'], $data['msg']);
        }

        $url = $data['result']['url'];
        $client = $this->downloadClient();
        $response = $client->get($url);
        $body = $response->getBody();
        $status = $response->getStatusCode();
        if ($status == 404) {
            return $this->json(1, '安装包不存在');
        }
        $zip_content = $body->getContents();
        if (empty($zip_content)) {
            return $this->json(1, '安装包不存在');
        }
        $base_path = base_path() . "/plugin/$name";
        $zip_file = "$base_path.zip";
        file_put_contents($zip_file, $zip_content);

        // 解压zip到plugin目录
        $zip = new \ZipArchive;
        $zip->open($zip_file, \ZIPARCHIVE::CHECKCONS);

        $context = null;
        $install_class = "\\plugin\\$name\\api\\Install";
        if ($installed_version) {
            // 执行beforeUpdate
            if (class_exists($install_class) && method_exists($install_class, 'beforeUpdate')) {
                $context = call_user_func([$install_class, 'beforeUpdate'], $installed_version, $version);
            }
        }

        $zip->extractTo(base_path() . '/plugin/');
        unlink($zip_file);

        if ($installed_version) {
            // 执行update更新
            if (class_exists($install_class) && method_exists($install_class, 'update')) {
                call_user_func([$install_class, 'update'], $installed_version, $version, $context);
            }
        } else {
            // 执行install安装
            if (class_exists($install_class) && method_exists($install_class, 'install')) {
                call_user_func([$install_class, 'install'], $version);
            }
        }

        Util::reloadWebman();

        return $this->json(0);
    }

    /**
     * 卸载
     *
     * @param Request $request
     * @return \support\Response
     */
    public function uninstall(Request $request)
    {
        $name = $request->post('name');
        $version = $request->post('version');
        if (!$name || !preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            return $this->json(1, '参数错误');
        }

        // 获得插件路径
        clearstatcache();
        $path = get_realpath(base_path() . "/plugin/$name");
        if (!$path || !is_dir($path)) {
            return $this->json(1, '已经删除');
        }

        // 执行uninstall卸载
        $install_class = "\\plugin\\$name\\api\\Install";
        if (class_exists($install_class) && method_exists($install_class, 'uninstall')) {
            call_user_func([$install_class, 'uninstall'], $version);
        }

        // 删除目录
        clearstatcache();
        if (is_dir($path)) {
            $this->rmDir($path);
        }
        clearstatcache();

        Util::reloadWebman();

        return $this->json(0);
    }

    /**
     * 登录验证码
     *
     * @param Request $request
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function captcha(Request $request)
    {
        $client = $this->httpClient();
        $response = $client->get('/user/captcha?type=login');
        $sid_str = $response->getHeaderLine('Set-Cookie');
        if(preg_match('/PHPSID=([a-zA-z_0-9]+?);/', $sid_str, $match)) {
            $sid = $match[1];
            session()->set('app-plugin-token', $sid);
        }
        return response($response->getBody()->getContents())->withHeader('Content-Type', 'image/jpeg');
    }

    /**
     * 登录
     *
     * @param Request $request
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(Request $request)
    {
        $client = $this->httpClient();
        $response = $client->post('/api/user/login', [
            'form_params' => [
                'email' => $request->post('username'),
                'password' => $request->post('password'),
                'captcha' => $request->post('captcha')
            ]
        ]);
        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);
        if (!$data) {
            $msg = "/api/user/login return $content";
            echo "msg\r\n";
            Log::error($msg);
            return $this->json(1, '发生错误');
        }
        if ($data['code'] != 0) {
            return $this->json($data['code'], $data['msg']);
        }
        session()->set('app-plugin-user', [
            'uid' => $data['data']['uid']
        ]);
        return $this->json(0);
    }

    protected function getPluginVersion($name)
    {
        return config("plugin.$name.app.version");
    }

    protected function rmDir($src)
    {
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $this->rmDir($full);
                } else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    protected function httpClient()
    {
        // 下载zip
        $options = [
            'base_uri' => config('plugin.admin.app.plugin_market_host'),
            'timeout' => 30,
            'connect_timeout' => 5,
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                'Referer' => \request()->fullUrl(),
                'User-Agent'  => 'webman-app-plugin',
                'Accept' => 'application/json;charset=UTF-8',
            ]
        ];
        if ($token = session('app-plugin-token')) {
            $options['headers']['Cookie'] = "PHPSID=$token;";
        }
        return new Client($options);
    }

    protected function downloadClient()
    {
        // 下载zip
        $options = [
            'timeout' => 30,
            'connect_timeout' => 5,
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                'Referer' => \request()->fullUrl(),
                'User-Agent'  => 'webman-app-plugin',
            ]
        ];
        if ($token = session('app-plugin-token')) {
            $options['headers']['Cookie'] = "PHPSID=$token;";
        }
        return new Client($options);
    }

}
