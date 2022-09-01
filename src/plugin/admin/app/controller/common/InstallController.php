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

        $dsn = "mysql:dbname=$database;host=$host;port=$port;";
        try {
            $params = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4", //设置编码
                \PDO::ATTR_EMULATE_PREPARES   => false,
                \PDO::ATTR_TIMEOUT => 5
            ];
            $db = new \PDO($dsn, $user, $password, $params);
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
        $admin = new Admin;
        $admin->username = $username;
        $admin->password = Util::passwordHash($password);
        $admin->nickname = '超级管理员';
        $admin->roles = '1';
        $admin->save();
        return $this->json(0);
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

}
