<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\model\Option;
use plugin\admin\app\Util;
use support\Db;
use support\Model;
use support\Request;

trait Crud
{

    /**
     * @var Model
     */
    protected $model = null;


    /**
     * 查询
     *
     * @param Request $request
     * @return \support\Response
     */
    public function select(Request $request)
    {
        [$where, $format, $page_size, $field, $order] = $this->selectInput($request);

        $model = $this->model;
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                if (in_array($value[0], ['>', '=', '<', '<>'])) {
                    $model = $model->where($column, $value[0], $value[1]);
                } elseif ($value[0] == 'in') {
                    $model = $model->whereIn($column, $value[1]);
                } else {
                    $model = $model->whereBetween($column, $value);
                }
            } else {
                $model = $model->where($column, $value);
            }
        }
        $model = $model->orderBy($field, $order);
        if (in_array($format, ['select', 'tree', 'table_tree'])) {
            $items = $model->get();
            if ($format == 'select') {
                return $this->formatSelect($items);
            } elseif ($format == 'tree') {
                return $this->formatTree($items);
            }
            return $this->formatTableTree($items);
        }

        $paginator = $model->paginate($page_size);
        return $this->json(0, 'ok', [
            'items' => $paginator->items(),
            'total' => $paginator->total()
        ]);
    }

    /**
     * 添加
     * @param Request $request
     * @return \support\Response
     */
    public function insert(Request $request)
    {
        $data = $request->post('data');
        $table = $this->model->getTable();
        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
        $columns = array_column($allow_column, 'Field', 'Field');
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
        $id = $this->model->insertGetId($data);
        return $this->json(0, $id);
    }

    /**
     * 更新
     * @param Request $request
     * @return \support\Response
     */
    public function update(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        $data = $request->post('data');
        $table = $this->model->getTable();
        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
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
        $this->model->where($column, $value)->update($data);
        return $this->json(0);
    }

    /**
     * 删除
     * @param Request $request
     * @return \support\Response
     * @throws \Support\Exception\BusinessException
     */
    public function delete(Request $request)
    {
        $column = $request->post('column');
        $value = $request->post('value');
        $this->model->where([$column => $value])->delete();
        return $this->json(0);
    }

    /**
     * 摘要
     * @param Request $request
     * @return \support\Response
     * @throws \Support\Exception\BusinessException
     */
    public function schema(Request $request)
    {
        $table = $this->model->getTable();
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
     * 按表获取摘要
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
        $indexes = $section == 'keys' || !$section ? Util::db()->select("SHOW INDEX FROM `$table`") : [];
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
     * @param Request $request
     * @return array|\support\Response
     */
    protected function selectInput(Request $request)
    {
        $field = $request->get('field');
        $order = $request->get('order', 'descend');
        $format = $request->get('format', 'normal');
        $page_size = $request->get('pageSize', $format === 'tree' ? 1000 : 10);
        $order = $order === 'ascend' ? 'asc' : 'desc';
        $where = $request->get();
        $table = $this->model->getTable();

        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = current($allow_column);
        }
        foreach ($where as $column => $value) {
            if (!$value || !isset($allow_column[$column]) ||
                (is_array($value) && ($value[0] == 'undefined' || $value[1] == 'undefined'))) {
                unset($where[$column]);
            }
        }

        return [$where, $format, $page_size, $field, $order];
    }

    /**
     * 树
     *
     * @param $items
     * @return \support\Response
     */
    protected function formatTree($items)
    {
        $items_map = [];
        foreach ($items as $item) {
            $items_map[$item->id] = [
                'title' => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'key' => (string)$item->id,
                'pid' => $item->pid,
            ];
        }
        $formatted_items = [];
        foreach ($items_map as $index => $item) {
            if ($item['pid'] && isset($items_map[$item['pid']])) {
                $items_map[$item['pid']]['children'][] = &$items_map[$index];
            }
        }
        foreach ($items_map as $item) {
            if (!$item['pid']) {
                $formatted_items[] = $item;
            }
        }
        return  $this->json(0, 'ok', $formatted_items);
    }

    /**
     * 表格树
     *
     * @param $items
     * @return \support\Response
     */
    protected function formatTableTree($items)
    {
        $items_map = [];
        foreach ($items as $item) {
            $items_map[$item->id] = $item->toArray();
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
        return $this->json(0, 'ok', $formatted_items);
    }

    /**
     * @param $items
     * @return \support\Response
     */
    protected function formatSelect($items)
    {
        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'title' => $item->title ?? $item->name ?? $item->id,
                'value' => $item->id,
                'key' => $item->id,
            ];
        }
        return  $this->json(0, 'ok', $formatted_items);
    }

    protected function json(int $code, string $msg = 'ok', array $data = [])
    {
        return json(['code' => $code, 'result' => $data, 'message' => $msg, 'type' => $code ? 'error' : 'success']);
    }

}
