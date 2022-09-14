<?php

namespace plugin\admin\app;

use support\Db;
use Support\Exception\BusinessException;
use Throwable;

class Util
{
    static public function passwordHash($password, $algo = PASSWORD_DEFAULT)
    {
        return password_hash($password, $algo);
    }

    static function db()
    {
        return Db::connection('plugin.admin.mysql');
    }

    static function schema()
    {
        return Db::schema('plugin.admin.mysql');
    }

    static public function passwordVerify($password, $hash)
    {
        return password_verify($password, $hash);
    }

    static public function checkTableName($table)
    {
        if (!preg_match('/^[a-zA-Z_0-9]+$/', $table)) {
            throw new BusinessException('表名不合法');
        }
        return true;
    }

    public static function camel($value)
    {
        static $cache = [];
        $key = $value;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return $cache[$key] = str_replace(' ', '', $value);
    }

    public static function smCamel($value)
    {
        return lcfirst(static::camel($value));
    }

    public static function getCommentFirstLine($comment)
    {
        if ($comment === false) {
            return false;
        }
        foreach (explode("\n", $comment) as $str) {
            if ($s = trim($str, "*/\ \t\n\r\0\x0B")) {
                return $s;
            }
        }
        return $comment;
    }

    public static function methodControlMap()
    {
        return  [
            //method=>[控件]
            'integer' => ['InputNumber'],
            'string' => ['Input'],
            'text' => ['InputTextArea'],
            'date' => ['DatePicker'],
            'enum' => ['Select'],
            'float' => ['Input'],

            'tinyInteger' => ['InputNumber'],
            'smallInteger' => ['InputNumber'],
            'mediumInteger' => ['InputNumber'],
            'bigInteger' => ['InputNumber'],

            'unsignedInteger' => ['InputNumber'],
            'unsignedTinyInteger' => ['InputNumber'],
            'unsignedSmallInteger' => ['InputNumber'],
            'unsignedMediumInteger' => ['InputNumber'],
            'unsignedBigInteger' => ['InputNumber'],

            'decimal' => ['Input'],
            'double' => ['Input'],

            'mediumText' => ['InputTextArea'],
            'longText' => ['InputTextArea'],

            'dateTime' => ['DatePicker'],

            'time' => ['DatePicker'],
            'timestamp' => ['DatePicker'],

            'char' => ['Input'],

            'binary' => ['Input'],
        ];
    }

    public static function typeToControl($type)
    {
        if (stripos($type, 'int') !== false) {
            return 'InputNumber';
        }
        if (stripos($type, 'time') !== false || stripos($type, 'date') !== false) {
            return 'DatePicker';
        }
        if (stripos($type, 'text') !== false) {
            return 'InputTextArea';
        }
        if ($type === 'enum') {
            return 'Select';
        }
        return 'Input';
    }

    public static function typeToMethod($type, $unsigned = false)
    {
        if (stripos($type, 'int') !== false) {
            $type = str_replace('int', 'Integer', $type);
            return $unsigned ? "unsigned" . ucfirst($type) : lcfirst($type);
        }
        $map = [
            'int' => 'integer',
            'varchar' => 'string',
            'mediumtext' => 'mediumText',
            'longtext' => 'longText',
            'datetime' => 'dateTime',
        ];
        return $map[$type] ?? $type;
    }

    /**
     * reload webman
     *
     * @return bool
     */
    public static function reloadWebman()
    {
        if (function_exists('posix_kill')) {
            try {
                posix_kill(posix_getppid(), SIGUSR1);
                return true;
            } catch (Throwable $e) {}
        }
        return false;
    }

}