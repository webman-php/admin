<?php

namespace plugin\admin\app\controller\database;

use Illuminate\Database\Schema\Blueprint;
use plugin\admin\app\controller\Base;
use plugin\admin\app\model\Option;
use plugin\admin\app\Util;
use Support\Db;
use Support\Exception\BusinessException;
use Support\Request;

class TableController extends Base
{
    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    public $noNeedAuth = ['types'];

    /**
     * 查询表
     *
     * @param Request $request
     * @return \support\Response
     */
    public function show(Request $request)
    {
        $database = config('database.connections')['plugin.admin.mysql']['database'];
        $field = $request->get('field', 'TABLE_NAME');
        $order = $request->get('order', 'ascend');
        $allow_column = ['TABLE_NAME','TABLE_COMMENT','ENGINE','TABLE_ROWS','CREATE_TIME','UPDATE_TIME','TABLE_COLLATION'];
        if (!in_array($field, $allow_column)) {
            $field = 'TABLE_NAME';
        }
        $order = $order === 'ascend' ? 'asc' : 'desc';
        $tables = Util::db()->select("SELECT TABLE_NAME,TABLE_COMMENT,ENGINE,TABLE_ROWS,CREATE_TIME,UPDATE_TIME,TABLE_COLLATION FROM  information_schema.`TABLES` WHERE  TABLE_SCHEMA='$database' order by $field $order");

        if ($tables) {
            $table_names = array_column($tables, 'TABLE_NAME');
            $table_rows_count = [];
            foreach ($table_names as $table_name) {
                $table_rows_count[$table_name] = Util::db()->table($table_name)->count();
            }
            foreach ($tables as $key => $table) {
                $tables[$key]->TABLE_ROWS = $table_rows_count[$table->TABLE_NAME] ?? $table->TABLE_ROWS;
            }
        }

        return $this->json(0, 'ok', $tables);
    }

    /**
     * 创建表
     *
     * @param Request $request
     * @return \support\Response
     */
    public function create(Request $request)
    {
        $data = $request->post();
        $table_name = $data['table']['name'];
        $table_comment = $data['table']['comment'];
        $columns = $data['columns'];
        $keys = $data['keys'];
        Util::schema()->create($table_name, function (Blueprint $table) use ($columns) {
            $type_method_map = Util::methodControlMap();
            foreach ($columns as $column) {
                if (!isset($column['type'])) {
                    throw new BusinessException("请为{$column['field']}选择类型");
                }
                if (!isset($type_method_map[$column['type']])) {
                    throw new BusinessException("不支持的类型{$column['type']}");
                }
                $this->createColumn($column, $table);
            }
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->engine = 'InnoDB';
        });
        // @todo 防注入
        Util::db()->statement("ALTER TABLE `$table_name` COMMENT '$table_comment'");

        // 索引
        Util::schema()->table($table_name, function (Blueprint $table) use ($keys) {
            foreach ($keys as $key) {
                $name = $key['name'];
                $columns = $key['columns'];
                $type = $key['type'];
                if ($type == 'unique') {
                    $table->unique($columns, $name);
                    continue;
                }
                $table->index($columns, $name);
            }
        });

        $form_schema = $request->post('forms', []);
        $form_schema_map = [];
        foreach ($form_schema as $item) {
            $form_schema_map[$item['field']] = $item;
        }
        $form_schema_map = json_encode($form_schema_map, JSON_UNESCAPED_UNICODE);
        $this->updateSchemaOption($table_name, $form_schema_map);
        return $this->json(0, 'ok');
    }

    /**
     * 修改表
     *
     * @param Request $request
     * @return \support\Response
     * @throws BusinessException
     */
    public function modify(Request $request)
    {
        $data = $request->post();
        $old_table_name = $data['table']['old_name'];
        $table_name = $data['table']['name'];
        $table_comment = $data['table']['comment'];
        $columns = $data['columns'];
        $keys = $data['keys'] ?? [];
        // 改表名
        if ($table_name != $old_table_name) {
            Util::checkTableName($table_name);
            Util::schema()->rename($old_table_name, $table_name);
        }

        $old_columns = $this->getSchema($table_name, 'columns');
        $type_method_map = Util::methodControlMap();
        foreach ($columns as $column) {
            if (!isset($type_method_map[$column['type']])) {
                throw new BusinessException("不支持的类型{$column['type']}");
            }
            $field = $column['field'];

            // 重命名的字段 mysql8才支持？
            if (isset($column['old_field']) && $column['old_field'] !== $field) {
                //Util::db()->statement("ALTER TABLE $table_name RENAME COLUMN {$column['old_field']} to $field");
            }

            $old_column = $old_columns[$field] ?? [];
            // 类型更改
            foreach ($old_column as $key => $value) {
                if (isset($column[$key]) && $column[$key] != $value) {
                    $this->modifyColumn($column, $table_name);
                    break;
                }
            }
        }

        $table = $this->getSchema($table_name, 'table');
        // @todo $table_comment 防止SQL注入
        if ($table_comment !== $table['comment']) {
            Util::db()->statement("ALTER TABLE `$table_name` COMMENT '$table_comment'");
        }

        $old_columns = $this->getSchema($table_name, 'columns');
        Util::schema()->table($table_name, function (Blueprint $table) use ($columns, $old_columns, $keys, $table_name) {
            foreach ($columns as $column) {
                $field = $column['field'];
                // 新字段
                if (!isset($old_columns[$field])) {
                    $this->createColumn($column, $table);
                }
            }
            // 更新索引名字
            foreach ($keys as $key) {
                if (!empty($key['old_name']) && $key['old_name'] !== $key['name']) {
                    $table->renameIndex($key['old_name'], $key['name']);
                }
            }
        });

        // 找到删除的字段
        $old_columns = $this->getSchema($table_name, 'columns');
        $exists_column_names = array_column($columns, 'field', 'field');
        $old_columns_names = array_column($old_columns, 'field');
        $drop_column_names = array_diff($old_columns_names, $exists_column_names);
        foreach ($drop_column_names as $drop_column_name) {
            //$table->dropColumn($drop_column_name); 无法使用
            Util::db()->statement("ALTER TABLE $table_name DROP COLUMN $drop_column_name");
        }

        $old_keys = $this->getSchema($table_name, 'keys');
        Util::schema()->table($table_name, function (Blueprint $table) use ($keys, $old_keys, $table_name) {
            foreach ($keys as $key) {
                $key_name = $key['name'];
                $old_key = $old_keys[$key_name] ?? [];
                // 如果索引有变动，则删除索引，重新建立索引
                if ($old_key && ($key['type'] != $old_key['type'] || $key['columns'] != $old_key['columns'])) {
                    $old_key = [];
                    unset($old_keys[$key_name]);
                    $table->dropIndex($key_name);
                }
                // 重新建立索引
                if (!$old_key) {
                    $name = $key['name'];
                    $columns = $key['columns'];
                    $type = $key['type'];
                    if ($type == 'unique') {
                        $table->unique($columns, $name);
                        continue;
                    }
                    $table->index($columns, $name);
                }
            }

            // 找到删除的索引
            $exists_key_names = array_column($keys, 'name', 'name');
            $old_keys_names = array_column($old_keys, 'name');
            $drop_keys_names = array_diff($old_keys_names, $exists_key_names);
            foreach ($drop_keys_names as $name) {
                $table->dropIndex($name);
            }
        });

        $form_schema = $request->post('forms', []);
        $form_schema_map = [];
        foreach ($form_schema as $item) {
            $form_schema_map[$item['field']] = $item;
        }
        $form_schema_map = json_encode($form_schema_map, JSON_UNESCAPED_UNICODE);
        $option_name = $this->updateSchemaOption($table_name, $form_schema_map);

        return $this->json(0,$option_name);
    }

    /**
     * 查询记录
     *
     * @param Request $request
     * @return \support\Response
     */
    public function select(Request $request)
    {
        $page = $request->get('page', 1);
        $field = $request->get('field');
        $order = $request->get('order', 'descend');
        $table = $request->get('table');
        $format = $request->get('format', 'normal');
        $page_size = $request->get('pageSize', $format === 'tree' ? 1000 : 10);

        if (!preg_match('/[a-zA-Z_0-9]+/', $table)) {
            return $this->json(1, '表不存在');
        }
        $allow_column = Util::db()->select("desc $table");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = current($allow_column);
        }
        $order = $order === 'ascend' ? 'asc' : 'desc';
        $paginator = Util::db()->table($table);
        foreach ($request->get() as $column => $value) {
            if (!$value) {
                continue;
            }
            if (isset($allow_column[$column])) {
                if (is_array($value)) {
                    if ($value[0] == 'undefined' || $value[1] == 'undefined') {
                        continue;
                    }
                    $paginator = $paginator->whereBetween($column, $value);
                } else {
                    $paginator = $paginator->where($column, $value);
                }
            }
        }
        $paginator = $paginator->orderBy($field, $order)->paginate($page_size, '*', 'page', $page);

        $items = $paginator->items();
        if ($format == 'tree') {
            $items_map = [];
            foreach ($items as $item) {
                $items_map[$item->id] = (array)$item;
            }
            $formatted_items = [];
            foreach ($items_map as $item) {
                if ($item['pid'] && isset($items_map[$item['pid']])) {
                    $items_map[$item['pid']]['children'][] = $item;
                }
            }
            foreach ($items_map as $item) {
                if (!$item['pid']) {
                    $formatted_items[] = $item;
                }
            }
            $items = $formatted_items;
        }

        return $this->json(0, 'ok', [
            'items' => $items,
            'total' => $paginator->total()
        ]);
    }

    /**
     * 插入记录
     *
     * @param Request $request
     * @return \support\Response
     */
    public function insert(Request $request)
    {
        $table = $request->input('table');
        $data = $request->post('data');
        $columns = $this->getSchema($table, 'columns');
        foreach ($data as $col => $item) {
            if (is_array($item)) {
                $data[$col] = implode(',', $item);
                continue;
            }
            if ($col === 'password') {
                $data[$col] = Util::passwordHash($item);
            }
        }
        $datetime = date('Y-m-d H:i:s');
        if (isset($columns['created_at']) && !isset($data['created_at'])) {
            $data['created_at'] = $datetime;
        }
        if (isset($columns['updated_at']) && !isset($data['updated_at'])) {
            $data['updated_at'] = $datetime;
        }
        $id = Util::db()->table($table)->insertGetId($data);
        return $this->json(0, $id);
    }

    /**
     * 更新记录
     *
     * @param Request $request
     * @return \support\Response
     * @throws BusinessException
     */
    public function update(Request $request)
    {
        $table = $request->input('table');
        $column = $request->post('column');
        $value = $request->post('value');
        $data = $request->post('data');
        $columns = $this->getSchema($table, 'columns');
        foreach ($data as $col => $item) {
            if (is_array($item)) {
                $data[$col] = implode(',', $item);
            }
            if ($col === 'password') {
                // 密码为空，则不更新密码
                if ($item == '') {
                    unset($data[$col]);
                    continue;
                }
                $data[$col] = Util::passwordHash($item);
            }
        }
        $datetime = date('Y-m-d H:i:s');
        if (isset($columns['updated_at']) && !isset($data['updated_at'])) {
            $data['updated_at'] = $datetime;
        }
        var_export($data);
        Util::checkTableName($table);
        Util::db()->table($table)->where($column, $value)->update($data);
        return $this->json(0);
    }

    /**
     * 删除记录
     *
     * @param Request $request
     * @return \support\Response
     * @throws BusinessException
     */
    public function delete(Request $request)
    {
        $table = $request->input('table');
        $column = $request->post('column');
        $value = $request->post('value');
        Util::checkTableName($table);
        Util::db()->table($table)->where([$column => $value])->delete();
        return $this->json(0);
    }

    /**
     * 表摘要
     *
     * @param Request $request
     * @return \support\Response
     * @throws BusinessException
     */
    public function schema(Request $request)
    {
        $table = $request->get('table');
        Util::checkTableName($table);
        $schema = Option::where('name', "table_form_schema_$table")->value('value');
        $form_schema_map = $schema ? json_decode($schema, true) : [];

        $data = $this->getSchema($table);
        foreach ($data['forms'] as $field => $item) {
            if (isset($form_schema_map[$field])) {
                $data['forms'][$field] = $form_schema_map[$field];
            }
        }

        return $this->json(0, 'ok', [
            'table' => $data['table'],
            'columns' => array_values($data['columns']),
            'forms' => array_values($data['forms']),
            'keys' => array_values($data['keys']),
        ]);
    }

    /**
     * 获取摘要
     *
     * @param $table
     * @param $section
     * @return array|mixed
     */
    protected function getSchema($table, $section = null)
    {
        $database = config('database.connections')['plugin.admin.mysql']['database'];
        $schema_raw = $section !== 'table' ? Util::db()->select("select * from information_schema.COLUMNS where TABLE_SCHEMA = '$database' and table_name = '$table'") : [];
        $forms = [];
        $columns = [];
        foreach ($schema_raw as $item) {
            $field = $item->COLUMN_NAME;
            $columns[$field] = [
                'field' => $field,
                'type' => Util::typeToMethod($item->DATA_TYPE, (bool)strpos($item->COLUMN_TYPE, 'unsigned')),
                'comment' => $item->COLUMN_COMMENT,
                'default' => $item->COLUMN_DEFAULT,
                'length' => $this->getLengthValue($item),
                'nullable' => $item->IS_NULLABLE !== 'NO',
                'primary_key' => $item->COLUMN_KEY === 'PRI',
                'auto_increment' => strpos($item->EXTRA, 'auto_increment') !== false
            ];

            $forms[$field] = [
                'field' => $field,
                'comment' => $item->COLUMN_COMMENT,
                'control' => Util::typeToControl($item->DATA_TYPE),
                'form_show' => $item->COLUMN_KEY !== 'PRI',
                'list_show' => true,
                'enable_sort' => false,
                'readonly' => $item->COLUMN_KEY === 'PRI',
                'searchable' => false,
                'search_type' => 'normal',
                'control_args' => '',
            ];
        }
        $table_schema = $section == 'table' || !$section ? Util::db()->select("SELECT TABLE_COMMENT FROM  information_schema.`TABLES` WHERE  TABLE_SCHEMA='$database' and TABLE_NAME='$table'") : [];
        $indexes = $section == 'keys' || !$section ? Util::db()->select("SHOW INDEX FROM $table") : [];
        $keys = [];
        foreach ($indexes as $index) {
            $key_name = $index->Key_name;
            if ($key_name == 'PRIMARY') {
                continue;
            }
            if (!isset($keys[$key_name])) {
                $keys[$key_name] = [
                    'name' => $key_name,
                    'columns' => [],
                    'type' => $index->Non_unique == 0 ? 'unique' : 'normal'
                ];
            }
            $keys[$key_name]['columns'][] = $index->Column_name;
        }

        $data = [
            'table' => ['name' => $table, 'comment' => $table_schema[0]->TABLE_COMMENT ?? ''],
            'columns' => $columns,
            'forms' => $forms,
            'keys' => array_reverse($keys, true)
        ];
        return $section ? $data[$section] : $data;
    }

    /**
     * 获取字段长度
     *
     * @param $schema
     * @return string
     */
    protected function getLengthValue($schema)
    {
        $type = $schema->DATA_TYPE;
        if (in_array($type, ['float', 'decimal', 'double'])) {
            return "{$schema->NUMERIC_PRECISION},{$schema->NUMERIC_SCALE}";
        }
        if ($type === 'enum') {
            return implode(',', array_map(function($item){
                return trim($item, "'");
            }, explode(',', substr($schema->COLUMN_TYPE, 5, -1))));
        }
        if (in_array($type, ['varchar', 'text', 'char'])) {
            return $schema->CHARACTER_MAXIMUM_LENGTH;
        }
        if (in_array($type, ['time', 'datetime', 'timestamp'])) {
            return $schema->CHARACTER_MAXIMUM_LENGTH;
        }
        return '';
    }

    /**
     * 删除表
     *
     * @param Request $request
     * @return \support\Response
     */
    public function drop(Request $request)
    {
        $table_name = $request->post('table');
        if (!$table_name) {
            return $this->json(0, 'not found');
        }
        $table_not_allow_drop = ['wa_admins', 'wa_users', 'wa_options', 'wa_admin_roles', 'wa_admin_rules'];
        if (in_array($table_name, $table_not_allow_drop)) {
            return $this->json(400, "$table_name 不允许删除");
        }
        Util::schema()->drop($table_name);
        // 删除schema
        Util::db()->table('wa_options')->where('name', "table_form_schema_$table_name")->delete();
        return $this->json(0, 'ok');
    }

    /**
     * 创建字段
     *
     * @param $column
     * @param Blueprint $table
     * @return mixed
     */
    protected function createColumn($column, Blueprint $table)
    {
        $method = $column['type'];
        $args = [$column['field']];
        if (stripos($method, 'int') !== false) {
            // auto_increment 会自动成为主键
            if ($column['auto_increment']) {
                $column['nullable'] = false;
                $column['default'] = '';
                $args[] = true;
            }
        } elseif (in_array($method, ['string', 'char']) || stripos($method, 'time') !== false) {
            if ($column['length']) {
                $args[] = $column['length'];
            }
        } elseif ($method === 'enum') {
            $args[] = array_map('trim', explode(',', $column['length']));
        } elseif (in_array($method, ['float', 'decimal', 'double'])) {
            if ($column['length']) {
                $args = array_merge($args, array_map('trim', explode(',', $column['length'])));
            }
        } else {
            $column['auto_increment'] = false;
        }

        $column_def = [$table, $method](...$args);
        if (!empty($column['comment'])) {
            $column_def = $column_def->comment($column['comment']);
        }

        if (!$column['auto_increment'] && $column['primary_key']) {
            $column_def = $column_def->primary(true);
        }

        if ($column['auto_increment'] && !$column['primary_key']) {
            $column_def = $column_def->primary(false);
        }
        $column_def = $column_def->nullable($column['nullable']);

        if ($column['primary_key']) {
            $column_def = $column_def->nullable(false);
        }

        if ($column['default'] && !in_array($method, ['text'])) {
            $column_def->default($column['default']);
        }
        return $column_def;
    }

    /**
     * 更改字段
     *
     * @param $column
     * @param Blueprint $table
     * @return mixed
     */
    protected function modifyColumn($column, $table)
    {
        $method = $column['type'];
        $field = $column['field'];
        $nullable = $column['nullable'];
        $default = $column['default'];
        $comment = $column['comment'];
        $auto_increment = $column['auto_increment'];
        $length = (int)$column['length'];
        $primary_key = $column['primary_key'];
        // @todo 防止SQL注入
        if (isset($column['old_field']) && $column['old_field'] !== $field) {
            $sql = "ALTER TABLE $table CHANGE COLUMN {$column['old_field']} $field ";
        } else {
            $sql = "ALTER TABLE $table MODIFY $field ";
        }

        if (stripos($method, 'integer') !== false) {
            $type = str_ireplace('integer', 'int', $method);
            if (stripos($method, 'unsigned') !== false) {
                $type = str_ireplace('unsigned', '', $type);
                $sql .= "$type ";
                $sql .= 'unsigned ';
            } else {
                $sql .= "$type ";
            }
            if ($auto_increment) {
                $column['nullable'] = false;
                $column['default'] = '';
                $sql .= 'AUTO_INCREMENT ';
            }
        } else {
            switch ($method) {
                case 'string':
                    $length = $length ?: 255
;                    $sql .= "varchar($length) ";
                    break;
                case 'char':
                case 'time':
                    $sql .= $length ? "$method($length) " : "$method ";
                    break;
                case 'enum':
                    // @todo 防止SQL注入
                    $args = array_map('trim', explode(',', $column['length']));
                    $sql .= "enum('" . implode("','", $args) . "') ";
                    break;
                case 'double':
                case 'float':
                case 'decimal':
                    if (trim($column['length'])) {
                        $args = array_map('intval', explode(',', $column['length']));
                        $args[1] = $args[1] ?? $args[0];
                        $sql .= "$method({$args[0]}, {$args[1]}) ";
                        break;
                    }
                    $sql .= "$method ";
                    break;
                default :
                    $sql .= "$method ";

            }
        }

        if (!$nullable) {
            $sql .= "NOT NULL ";
        }

        if ($default !== null && !in_array($method, ['text'])) {
            $sql .= "DEFAULT '$default' ";
        }

        if ($comment !== null) {
            $sql .= "COMMENT '$comment' ";
        }

        Util::db()->statement($sql);
    }

    /**
     * 字段类型列表
     *
     * @param Request $request
     * @return \support\Response
     */
    public function types(Request $request)
    {
        $types = Util::methodControlMap();
        return $this->json(0, 'ok', $types);
    }

    /**
     * 获取在options对用的name
     *
     * @param $table_name
     * @return string
     */
    protected function getSchemaOptionName($table_name)
    {
        return "table_form_schema_$table_name";
    }

    /**
     * 更新表的form schema信息
     *
     * @param $table_name
     * @param $data
     * @return string
     */
    protected function updateSchemaOption($table_name, $data)
    {
        $option_name = $this->getSchemaOptionName($table_name);
        $option = Option::where('name', $option_name)->first();
        if ($option) {
            Option::where('name', $option_name)->update(['value' => $data]);
        } else {
            Option::insert(['name' => $option_name, 'value' => $data]);
        }
        return $option_name;
    }

}
