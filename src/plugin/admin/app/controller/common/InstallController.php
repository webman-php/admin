<?php

namespace plugin\admin\app\controller\common;

use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Database\Capsule\Manager;
use plugin\admin\app\controller\Base;
use plugin\admin\app\model\Admin;
use plugin\admin\app\Util;
use support\exception\BusinessException;
use support\Request;
use support\Response;
use support\Db;

/**
 * 安装
 */
class InstallController extends Base
{
    /**
     * 不需要登录的方法
     * @var string[]
     */
    public $noNeedLogin = ['step1', 'step2'];

    /**
     * 设置数据库
     *
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function step1(Request $request)
    {
        $database_config_file = base_path() . '/plugin/admin/config/database.php';
        clearstatcache();
        if (is_file($database_config_file)) {
            return $this->json(1, '管理后台已经安装！如需重新安装，请删除该插件数据库配置文件并重启');
        }

        if (!class_exists(CaptchaBuilder::class) || !class_exists(Manager::class)) {
            return $this->json(1, '请先restart重启webman后再进行此页面的设置');
        }

        $user = $request->post('user');
        $password = $request->post('password');
        $database = $request->post('database');
        $host = $request->post('host');
        $port = $request->post('port');
        $overwrite = $request->post('overwrite');

        try {
            $db = $this->getPdo($host, $user, $password, $port);
            $smt = $db->query("show databases like '$database'");
            if (empty($smt->fetchAll())) {
                $db->exec("create database $database");
            }
            $db->exec("use $database");
            $smt = $db->query("show tables");
            $tables = $smt->fetchAll();
        } catch (\Throwable $e) {
            if (stripos($e, 'Access denied for user')) {
                return $this->json(1, '数据库用户名或密码错误');
            }
            if (stripos($e, 'Connection refused')) {
                return $this->json(1, 'Connection refused. 请确认数据库IP端口是否正确，数据库已经启动');
            }
            if (stripos($e, 'timed out')) {
                return $this->json(1, '数据库连接超时，请确认数据库IP端口是否正确，安全组及防火墙已经放行端口');
            }
            throw $e;
        }

        $tables_to_install = [
            'wa_admins',
            'wa_admin_roles',
            'wa_admin_rules',
            'wa_options',
            'wa_users',
        ];

        if (!$overwrite) {
            $tables_exist = [];
            foreach ($tables as $table) {
                $tables_exist[] = current($table);
            }
            $tables_conflict = array_intersect($tables_to_install, $tables_exist);
            if ($tables_conflict) {
                return $this->json(1, '以下表' . implode(',', $tables_conflict) . '已经存在，如需覆盖请选择强制覆盖');
            }
        }

        $sql_file = base_path() . '/plugin/admin/webman-admin.sql';
        if (!is_file($sql_file)) {
            return $this->json(1, '数据库SQL文件不存在');
        }

        $sql_query = file_get_contents($sql_file);
        $sql_query = $this->removeComments($sql_query);
        $sql_query = $this->splitSqlFile($sql_query, ';');
        foreach ($sql_query as $sql) {
            $db->exec($sql);
        }

        // 导入菜单
        $menus = include base_path() . '/plugin/admin/config/menu.php';
        $this->import($menus, $db);

        $config_content = <<<EOF
<?php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '$host',
            'port'        => '$port',
            'database'    => '$database',
            'username'    => '$user',
            'password'    => '$password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
        ],
    ],
];
EOF;

        file_put_contents($database_config_file, $config_content);

        // 尝试reload
        if (function_exists('posix_kill')) {
            set_error_handler(function () {});
            posix_kill(posix_getppid(), SIGUSR1);
            restore_error_handler();
        }

        return $this->json(0);
    }

    /**
     * 设置管理员
     *
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function step2(Request $request)
    {
        $username = $request->post('username');
        $password = $request->post('password');
        $password2 = $request->post('password2');
        if ($password != $password2) {
            return $this->json(1, '两次密码不一致');
        }
        if (Admin::first()) {
            return $this->json(1, '后台已经安装完毕，无法通过此页面创建管理员');
        }
        if (!is_file($config_file = base_path() . '/plugin/admin/config/database.php')) {
            return $this->json(1, '请先完成第一步数据库配置');
        }
        $config = include $config_file;
        $connection = $config['connections']['mysql'];
        $pdo = $this->getPdo($connection['host'], $connection['username'], $connection['password'], $connection['port'], $connection['database']);
        $smt = $pdo->prepare("insert into `wa_admins` (`username`, `password`, `nickname`, `roles`, `created_at`, `updated_at`) values (:username, :password, :nickname, :roles, :created_at, :updated_at)");
        $time = date('Y-m-d H:i:s');
        $data = [
            'username' => $username,
            'password' => Util::passwordHash($password),
            'nickname' => '超级管理员',
            'roles' => '1',
            'created_at' => $time,
            'updated_at' => $time
        ];
        foreach ($data as $key => $value) {
            $smt->bindValue($key, $value);
        }
        $smt->execute();
        return $this->json(0);
    }

    /**
     * 添加菜单
     *
     * @param array $menu
     * @param \PDO $pdo
     * @return int
     */
    public function add(array $menu, \PDO $pdo)
    {
        $allow_columns = ['title', 'name', 'path', 'component', 'icon', 'hide_menu', 'frame_src', 'pid'];
        $data = [];
        foreach ($allow_columns as $column) {
            if (isset($menu[$column])) {
                $data[$column] = $menu[$column];
            }
        }
        $time = date('Y-m-d H:i:s');
        $data['created_at'] = $data['updated_at'] = $time;
        $values = [];
        foreach ($data as $k => $v) {
            $values[] = ":$k";
        }
        $sql = "insert into wa_admin_rules (" .implode(',', array_keys($data)). ") values (" . implode(',', $values) . ")";
        $smt = $pdo->prepare($sql);
        foreach ($data as $key => $value) {
            $smt->bindValue($key, $value);
        }
        $smt->execute();
        return $pdo->lastInsertId();
    }

    /**
     * 导入菜单
     *
     * @param array $menu_tree
     * @param \PDO $pdo
     * @return void
     */
    public function import(array $menu_tree, \PDO $pdo)
    {
        if (is_numeric(key($menu_tree)) && !isset($menu_tree['name'])) {
            foreach ($menu_tree as $item) {
                $this->import($item, $pdo);
            }
            return;
        }
        $children = $menu_tree['children'] ?? [];
        unset($menu_tree['children']);
        $smt = $pdo->prepare("select * from wa_admin_rules where name=:name limit 1");
        $smt->execute(['name' => $menu_tree['name']]);
        $old_menu = $smt->fetch();
        if ($old_menu) {
            $pid = $old_menu['id'];
            $params = [
                'title' => $menu_tree['title'],
                'path' => $menu_tree['path'],
                'icon' => $menu_tree['icon'],
                'name' => $menu_tree['name'],
            ];
            if (!isset($menu_tree['component'])) {
                $sql = "update wa_admin_rules set title=:title, path=:path, icon=:icon where name=:name";
            } else {
                $sql = "update wa_admin_rules set title=:title, path=:path, icon=:icon, component=:component where name=:name";
                $params['component'] = $menu_tree['component'];
            }
            $smt = $pdo->prepare($sql);
            $smt->execute($params);
        } else {
            $pid = $this->add($menu_tree, $pdo);
        }
        foreach ($children as $menu) {
            $menu['pid'] = $pid;
            $this->import($menu, $pdo);
        }
    }

    /**
     * 去除sql文件中的注释
     *
     * @param $sql
     * @return string
     */
    protected function removeComments($sql)
    {
        return preg_replace("/(\n--[^\n]*)/","", $sql);
    }

    /**
     * @param $sql
     * @param $delimiter
     * @return array
     */
    function splitSqlFile($sql, $delimiter)
    {
        $tokens = explode($delimiter, $sql);
        $output = array();
        $matches = array();
        $token_count = count($tokens);
        for ($i = 0; $i < $token_count; $i++) {
            if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0))) {
                $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
                $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);
                $unescaped_quotes = $total_quotes - $escaped_quotes;

                if (($unescaped_quotes % 2) == 0) {
                    $output[] = $tokens[$i];
                    $tokens[$i] = "";
                } else {
                    $temp = $tokens[$i] . $delimiter;
                    $tokens[$i] = "";

                    $complete_stmt = false;
                    for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++) {
                        $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                        $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);
                        $unescaped_quotes = $total_quotes - $escaped_quotes;
                        if (($unescaped_quotes % 2) == 1) {
                            $output[] = $temp . $tokens[$j];
                            $tokens[$j] = "";
                            $temp = "";
                            $complete_stmt = true;
                            $i = $j;
                        } else {
                            $temp .= $tokens[$j] . $delimiter;
                            $tokens[$j] = "";
                        }

                    }
                }
            }
        }

        return $output;
    }

    /**
     * 获取pdo连接
     *
     * @param $host
     * @param $username
     * @param $password
     * @param $port
     * @param $database
     * @return \PDO
     */
    protected function getPdo($host, $username, $password, $port, $database = null)
    {
        $dsn = "mysql:host=$host;port=$port;";
        if ($database) {
            $dsn .= "dbname=$database";
        }
        $params = [
            \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",
            \PDO::ATTR_EMULATE_PREPARES => false,
            \PDO::ATTR_TIMEOUT => 5,
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];
        return new \PDO($dsn, $username, $password, $params);
    }

}
