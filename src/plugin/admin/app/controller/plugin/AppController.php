<?php

namespace plugin\admin\app\controller\plugin;

use GuzzleHttp\Client;
use plugin\admin\app\controller\Base;
use plugin\admin\app\Util;
use support\exception\BusinessException;
use support\Log;
use support\Request;
use function base_path;
use function config;
use function get_realpath;

class AppController extends Base
{
    protected $noNeedAuth = ['schema', 'captcha'];

    /**
     * list
     *
     * @param Request $request
     * @return \support\Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(Request $request)
    {
        $installed = [];
        clearstatcache();
        $plugin_names = \array_diff(\scandir(base_path() . '/plugin/'), array('.', '..')) ?: [];
        foreach ($plugin_names as $plugin_name) {
            if (is_dir(base_path() . "/plugin/$plugin_name") && $version = $this->getPluginVersion($plugin_name)) {
                $installed[$plugin_name] = $version;
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
            return $this->json(1, 'Error getting data');
        }
        $disabled = is_phar();
        foreach ($data['result']['items'] as $key => $item) {
            $name = $item['name'];
            $data['result']['items'][$key]['installed'] = $installed[$name] ?? 0;
            $data['result']['items'][$key]['disabled'] = $disabled;
        }
        return $this->json(0, 'ok', $data['result']);
    }

    /**
     * Summary
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
     * Install
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
            return $this->json(1, 'Missing parameters');
        }

        $user = session('app-plugin-user');
        if (!$user) {
            return $this->json(0, 'please sign in', [
                'code' => 401,
                'message' => 'please sign in'
            ]);
        }

        // Get download zip fileurl
        $data = $this->getDownloadUrl($name, $user['uid'], $host, $version);
        if ($data['code'] == -1) {
            return $this->json(0, 'please sign in', [
                'code' => 401,
                'message' => 'please sign in'
            ]);
        }

        // download zip file
        $base_path = base_path() . "/plugin/$name";
        $zip_file = "$base_path.zip";
        $extract_to = base_path() . '/plugin/';
        $this->downloadZipFile($data['result']['url'], $zip_file);

        $has_zip_archive = class_exists(\ZipArchive::class, false);
        if (!$has_zip_archive) {
            $cmd = $this->getUnzipCmd($zip_file, $extract_to);
            if (!$cmd) {
                throw new BusinessException('Please install the zip module for php or install the unzip command for the system');
            }
            if (!function_exists('proc_open')) {
                throw new BusinessException('Please unblock the proc_open function or install the zip module for php');
            }
        }

        // Extract the zip to the plugin directory
        if ($has_zip_archive) {
            $zip = new \ZipArchive;
            $zip->open($zip_file, \ZIPARCHIVE::CHECKCONS);
        }

        $context = null;
        $install_class = "\\plugin\\$name\\api\\Install";
        if ($installed_version) {
            // implementbeforeUpdate
            if (class_exists($install_class) && method_exists($install_class, 'beforeUpdate')) {
                $context = call_user_func([$install_class, 'beforeUpdate'], $installed_version, $version);
            }
        }

        if (!empty($zip)) {
            $zip->extractTo(base_path() . '/plugin/');
            unset($zip);
        } else {
            $this->unzipWithCmd($cmd);
        }

        unlink($zip_file);

        if ($installed_version) {
            // Execute update update
            if (class_exists($install_class) && method_exists($install_class, 'update')) {
                call_user_func([$install_class, 'update'], $installed_version, $version, $context);
            }
        } else {
            // Execute install
            if (class_exists($install_class) && method_exists($install_class, 'install')) {
                call_user_func([$install_class, 'install'], $version);
            }
        }

        Util::reloadWebman();

        return $this->json(0);
    }

    /**
     * Uninstall
     *
     * @param Request $request
     * @return \support\Response
     */
    public function uninstall(Request $request)
    {
        $name = $request->post('name');
        $version = $request->post('version');
        if (!$name || !preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            return $this->json(1, 'Parameter error');
        }

        // Get plugin path
        clearstatcache();
        $path = get_realpath(base_path() . "/plugin/$name");
        if (!$path || !is_dir($path)) {
            return $this->json(1, 'deleted');
        }

        // Execute uninstall
        $install_class = "\\plugin\\$name\\api\\Install";
        if (class_exists($install_class) && method_exists($install_class, 'uninstall')) {
            call_user_func([$install_class, 'uninstall'], $version);
        }

        // delete directory
        clearstatcache();
        if (is_dir($path)) {
            $this->rmDir($path);
        }
        clearstatcache();

        Util::reloadWebman();

        return $this->json(0);
    }

    /**
     * Login verification code
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
     * Log in
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
            return $this->json(1, 'An error occurred');
        }
        if ($data['code'] != 0) {
            return $this->json($data['code'], $data['msg']);
        }
        session()->set('app-plugin-user', [
            'uid' => $data['data']['uid']
        ]);
        return $this->json(0);
    }

    /**
     * Get zip downloadurl
     *
     * @param $name
     * @param $uid
     * @param $host
     * @param $version
     * @return mixed
     * @throws BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getDownloadUrl($name, $uid, $host, $version)
    {
        $client = $this->httpClient();
        $response = $client->post('/api/app/download', [
            'form_params' => [
                'name' => $name,
                'uid' => $uid,
                'token' => session('app-plugin-token'),
                'referer' => $host,
                'version' => $version,
            ]
        ]);

        $content = $response->getBody()->getContents();
        $data = json_decode($content, true);
        if (!$data) {
            $msg = "/api/app/download return $content";
            Log::error($msg);
            throw new BusinessException('Failed to access official interface');
        }
        if ($data['code'] && $data['code'] != -1) {
            throw new BusinessException($data['msg']);
        }
        if ($data['code'] == 0 && !isset($data['result']['url'])) {
            throw new BusinessException('The official interface returned data error');
        }
        return $data;
    }

    /**
     * downloadzip
     *
     * @param $url
     * @param $file
     * @return void
     * @throws BusinessException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function downloadZipFile($url, $file)
    {
        $client = $this->downloadClient();
        $response = $client->get($url);
        $body = $response->getBody();
        $status = $response->getStatusCode();
        if ($status == 404) {
            throw new BusinessException('The installation package does not exist');
        }
        $zip_content = $body->getContents();
        if (empty($zip_content)) {
            throw new BusinessException('The installation package does not exist');
        }
        file_put_contents($file, $zip_content);
    }

    /**
     * Get the unzip command supported by the system
     *
     * @param $zip_file
     * @param $extract_to
     * @return mixed|string|null
     */
    protected function getUnzipCmd($zip_file, $extract_to)
    {
        if ($cmd = $this->findCmd('unzip')) {
            $cmd = "$cmd -qq $zip_file -d $extract_to";
        } else if ($cmd = $this->findCmd('7z')) {
            $cmd = "$cmd x -bb0 -y $zip_file -o$extract_to";
        } else if ($cmd= $this->findCmd('7zz')) {
            $cmd = "$cmd x -bb0 -y $zip_file -o$extract_to";
        }
        return $cmd;
    }

    /**
     * Unzip using the unzip command
     *
     * @param $cmd
     * @return void
     * @throws BusinessException
     */
    protected function unzipWithCmd($cmd)
    {
        $desc = [
            0 => STDIN,
            1 => STDOUT,
            2 => ["pipe", "w"],
        ];
        $handler = proc_open($cmd, $desc, $pipes);
        if (!is_resource($handler)) {
            throw new BusinessException("Error unpacking zip: proc_open call failed");
        }
        $err = fread($pipes[2], 1024);
        fclose($pipes[2]);
        proc_close($handler);
        if ($err) {
            throw new BusinessException("Error unzipping zip:$err");
        }
    }

    /**
     * Get local plugin version
     *
     * @param $name
     * @return array|mixed|null
     */
    protected function getPluginVersion($name)
    {
        if (!is_file($file = base_path() . "/plugin/$name/config/app.php")) {
            return null;
        }
        $config = include $file;
        return $config['version'] ?? null;
    }

    /**
     * delete directory
     *
     * @param $src
     * @return void
     */
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

    /**
     * Obtainhttpclient
     *
     * @return Client
     */
    protected function httpClient()
    {
        // downloadzip
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

    /**
     * Get Downloadhttpclient
     *
     * @return Client
     */
    protected function downloadClient()
    {
        // downloadzip
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

    /**
     * Find System Commands
     *
     * @param string $name
     * @param string|null $default
     * @param array $extraDirs
     * @return mixed|string|null
     */
    function findCmd(string $name, string $default = null, array $extraDirs = [])
    {
        if (\ini_get('open_basedir')) {
            $searchPath = array_merge(explode(\PATH_SEPARATOR, \ini_get('open_basedir')), $extraDirs);
            $dirs = [];
            foreach ($searchPath as $path) {
                if (@is_dir($path)) {
                    $dirs[] = $path;
                } else {
                    if (basename($path) == $name && @is_executable($path)) {
                        return $path;
                    }
                }
            }
        } else {
            $dirs = array_merge(
                explode(\PATH_SEPARATOR, getenv('PATH') ?: getenv('Path')),
                $extraDirs
            );
        }

        $suffixes = [''];
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $pathExt = getenv('PATHEXT');
            $suffixes = array_merge($pathExt ? explode(\PATH_SEPARATOR, $pathExt) : $this->suffixes, $suffixes);
        }
        foreach ($suffixes as $suffix) {
            foreach ($dirs as $dir) {
                if (@is_file($file = $dir.\DIRECTORY_SEPARATOR.$name.$suffix) && ('\\' === \DIRECTORY_SEPARATOR || @is_executable($file))) {
                    return $file;
                }
            }
        }

        return $default;
    }

}
