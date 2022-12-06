<?php

namespace plugin\admin\app\controller;

use Doctrine\Inflector\InflectorFactory;
use Illuminate\Database\Schema\Blueprint;
use plugin\admin\app\common\LayuiForm;
use plugin\admin\app\common\Util;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use plugin\admin\app\model\Option;
use Support\Exception\BusinessException;
use Support\Request;
use support\Response;

class TableController extends Base
{
    /**
     * 不需要鉴权的方法
     * @var string[]
     */
    public $noNeedAuth = ['types'];

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('table/index');
    }

    /**
     * 查看表
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function view(Request $request): Response
    {
        $table = $request->get('table');
        $table = Util::filterAlphaNum($table);
        $form = LayuiForm::buildForm($table, 'search');
        $table_info = Util::getSchema($table, 'table');
        $primary_key = $table_info['primary_key'][0] ?? null;
        return view("table/view", [
            'form' => $form,
            'table' => $table,
            'primary_key' => $primary_key,
        ]);
    }

    /**
     * 查询表
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function show(Request $request): Response
    {
        $database = config('database.connections')['plugin.admin.mysql']['database'];
        $field = $request->get('field', 'TABLE_NAME');
        $field = Util::filterAlphaNum($field);
        $order = $request->get('order', 'asc');
        $allow_column = ['TABLE_NAME','TABLE_COMMENT','ENGINE','TABLE_ROWS','CREATE_TIME','UPDATE_TIME','TABLE_COLLATION'];
        if (!in_array($field, $allow_column)) {
            $field = 'TABLE_NAME';
        }
        $order = $order === 'asc' ? 'asc' : 'desc';
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
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function create(Request $request): Response
    {
        if ($request->method() === 'GET') {
            return view("table/create", []);
        }
        $data = $request->post();
        $table_name = Util::filterAlphaNum($data['table']);
        $table_comment = Util::pdoQuote($data['table_comment']);
        $columns = $data['columns'];
        $forms = $data['forms'];
        $keys = $data['keys'];

        $primary_key_count = 0;
        foreach ($columns as $index => $item) {
            if (!$item['field']) {
                unset($columns[$index]);
                continue;
            }
            $columns[$index]['primary_key'] = !empty($item['primary_key']);
            if ($columns[$index]['primary_key']) {
                $primary_key_count++;
            }
            $columns[$index]['auto_increment'] = !empty($item['auto_increment']);
            $columns[$index]['nullable'] = !empty($item['nullable']);
            if ($item['default'] === '') {
                $columns[$index]['default'] = null;
            } else if ($item['default'] === "''") {
                $columns[$index]['default'] = '';
            }
        }

        if ($primary_key_count > 1) {
            throw new BusinessException('不支持复合主键');
        }

        foreach ($forms as $index => $item) {
            if (!$item['field']) {
                unset($forms[$index]);
                continue;
            }
            $forms[$index]['form_show'] = !empty($item['form_show']);
            $forms[$index]['list_show'] = !empty($item['list_show']);
            $forms[$index]['enable_sort'] = !empty($item['enable_sort']);
            $forms[$index]['searchable'] = !empty($item['searchable']);
        }

        foreach ($keys as $index => $item) {
            if (!$item['name'] || !$item['columns']) {
                unset($keys[$index]);
            }
        }

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

        Util::db()->statement("ALTER TABLE `$table_name` COMMENT $table_comment");

        // 索引
        Util::schema()->table($table_name, function (Blueprint $table) use ($keys) {
            foreach ($keys as $key) {
                $name = $key['name'];
                $columns = is_array($key['columns']) ? $key['columns'] : explode(',', $key['columns']);
                $type = $key['type'];
                if ($type == 'unique') {
                    $table->unique($columns, $name);
                    continue;
                }
                $table->index($columns, $name);
            }
        });
        $form_schema_map = [];
        foreach ($forms as $item) {
            $form_schema_map[$item['field']] = $item;
        }
        $form_schema_map = json_encode($form_schema_map, JSON_UNESCAPED_UNICODE);
        $this->updateSchemaOption($table_name, $form_schema_map);
        return $this->json(0, 'ok');
    }

    /**
     * 修改表
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function modify(Request $request): Response
    {
        if ($request->method() === 'GET') {
            return view("table/modify", ['table' => $request->get('table')]);
        }
        $data = $request->post();
        $old_table_name = Util::filterAlphaNum($data['old_table']);
        $table_name = Util::filterAlphaNum($data['table']);
        $table_comment = Util::pdoQuote($data['table_comment']);
        $columns = $data['columns'];
        $forms = $data['forms'];
        $keys = $data['keys'];
        $primary_key = null;
        $auto_increment_column = null;
        $schema = Util::getSchema($old_table_name);
        $old_columns = $schema['columns'];
        $old_primary_key = $schema['table']['primary_key'][0] ?? null;

        $primary_key_count = $auto_increment_count = 0;
        foreach ($columns as $index => $item) {
            if (!$item['field']) {
                unset($columns[$index]);
                continue;
            }
            $field = $item['field'];
            $columns[$index]['auto_increment'] = !empty($item['auto_increment']);
            $columns[$index]['nullable'] = !empty($item['nullable']);
            $columns[$index]['primary_key'] = !empty($item['primary_key']);
            if ($columns[$index]['primary_key']) {
                $primary_key = $item['field'];
                $columns[$index]['nullable'] = false;
                $primary_key_count++;
            }
            if ($item['default'] === '') {
                $columns[$index]['default'] = null;
            } else if ($item['default'] === "''") {
                $columns[$index]['default'] = '';
            }
            if ($columns[$index]['auto_increment']) {
                $auto_increment_count++;
                if (!isset($old_columns[$field]) || !$old_columns[$field]['auto_increment']) {
                    $auto_increment_column = $columns[$index];
                    unset($auto_increment_column['old_field']);
                    $columns[$index]['auto_increment'] = false;
                }
            }
        }

        if ($primary_key_count > 1) {
            throw new BusinessException('不支持复合主键');
        }

        if ($auto_increment_count > 1) {
            throw new BusinessException('一个表只能有一个自增字段，并且必须为key');
        }

        foreach ($forms as $index => $item) {
            if (!$item['field']) {
                unset($forms[$index]);
                continue;
            }
            $forms[$index]['form_show'] = !empty($item['form_show']);
            $forms[$index]['list_show'] = !empty($item['list_show']);
            $forms[$index]['enable_sort'] = !empty($item['enable_sort']);
            $forms[$index]['searchable'] = !empty($item['searchable']);
        }

        foreach ($keys as $index => $item) {
            if (!$item['name'] || !$item['columns']) {
                unset($keys[$index]);
            }
        }

        // 改表名
        if ($table_name != $old_table_name) {
            Util::schema()->rename($old_table_name, $table_name);
        }

        $type_method_map = Util::methodControlMap();

        foreach ($columns as $column) {
            if (!isset($type_method_map[$column['type']])) {
                throw new BusinessException("不支持的类型{$column['type']}");
            }
            $field = $column['old_field'] ?? $column['field'] ;
            $old_column = $old_columns[$field] ?? [];
            // 类型更改
            foreach ($old_column as $key => $value) {
                if (key_exists($key, $column) && ($column[$key] != $value || ($key === 'default' && $column[$key] !== $value))) {
                    $this->modifyColumn($column, $table_name);
                    break;
                }
            }
        }

        $table = Util::getSchema($table_name, 'table');
        if ($table_comment !== $table['comment']) {
            Util::db()->statement("ALTER TABLE `$table_name` COMMENT $table_comment");
        }

        $old_columns = Util::getSchema($table_name, 'columns');
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
        $old_columns = Util::getSchema($table_name, 'columns');
        $exists_column_names = array_column($columns, 'field', 'field');
        $old_columns_names = array_column($old_columns, 'field');
        $drop_column_names = array_diff($old_columns_names, $exists_column_names);
        $drop_column_names = Util::filterAlphaNum($drop_column_names);
        foreach ($drop_column_names as $drop_column_name) {
            Util::db()->statement("ALTER TABLE $table_name DROP COLUMN `$drop_column_name`");
        }

        $old_keys = Util::getSchema($table_name, 'keys');
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
                    $columns = is_array($key['columns']) ? $key['columns'] : explode(',', $key['columns']);
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

        // 变更主键
        if ($old_primary_key != $primary_key) {
            if ($old_primary_key) {
                Util::db()->statement("ALTER TABLE `$table_name` DROP PRIMARY KEY");
            }
            if ($primary_key) {
                $primary_key = Util::filterAlphaNum($primary_key);
                Util::db()->statement("ALTER TABLE `$table_name` ADD PRIMARY KEY(`$primary_key`)");
            }
        }

        // 一个表只能有一个 auto_increment 字段，并且是key，所以需要在最后设置
        if ($auto_increment_column) {
            $this->modifyColumn($auto_increment_column, $table_name);
        }

        $form_schema_map = [];
        foreach ($forms as $item) {
            $form_schema_map[$item['field']] = $item;
        }
        $form_schema_map = json_encode($form_schema_map, JSON_UNESCAPED_UNICODE);
        $option_name = $this->updateSchemaOption($table_name, $form_schema_map);

        return $this->json(0,$option_name);
    }



    /**
     * 一键菜单
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function crud(Request $request): Response
    {
        if ($request->method() === 'GET') {
            return view("table/crud", ['table' => $request->get('table')]);
        }
        $table_name = $request->input('table');
        Util::checkTableName($table_name);
        $title = $request->post('title');
        $pid = $request->post('pid', 0);
        $icon = $request->post('icon', '');
        $controller_path = $request->post('controller', '');
        $model_path = $request->post('model', '');
        $overwrite = $request->post('overwrite');

        $controller_namespace_base = 'plugin\\admin\\';
        $model_namespace_base = 'plugin\\admin\\';
        $controller_path_base = base_path() . '/plugin/admin';
        $url_path_base = '/app/admin';

        // 控制器生成到主项目
        if ($controller_path && $controller_path[0] === '/') {
            $controller_namespace_base = '';
            $controller_path_base = base_path();
            $url_path_base = '';
        }

        // model生成到主项目
        if ($model_path && $model_path[0] === '/') {
            $model_namespace_base = '';
        }

        $pid = (int)$pid;
        if ($pid) {
            $parent_menu = AdminRule::find($pid);
            if (!$parent_menu) {
                return $this->json(1, '父菜单不存在');
            }
        }

        $table_basename = strpos($table_name, 'wa_') === 0 ? substr($table_name, 3) : $table_name;
        $inflector = InflectorFactory::create()->build();
        $model_class = $inflector->classify($inflector->singularize($table_basename));
        $controller_suffix = $controller_namespace_base === '' ? config('app.controller_suffix') : config('plugin.admin.app.controller_suffix');
        $controller_class = "$model_class$controller_suffix";
        if ($controller_path && substr($controller_path, -1) === '/') {
            $controller_path .= $controller_class;
        }
        if ($model_path && substr($model_path, -1) === '/') {
            $model_path .= $model_class;
        }

        if ($controller_path) {
            $controller_path = trim($controller_path, '/');
            $controller_class = ucfirst(basename($controller_path));
            if (!strpos($controller_class, $controller_suffix)) {
                $controller_class = "$controller_class$controller_suffix";
            }
            $controller_path = pathinfo($controller_path, PATHINFO_DIRNAME);
            $controller_path = $controller_path === '.' ? '' : $controller_path;
        }

        $path_backslash = str_replace('/', '\\', trim($controller_path, '/'));
        if ($path_backslash) {
            $controller_namespace = "{$controller_namespace_base}app\\controller\\$path_backslash";
        } else {
            $controller_namespace = "{$controller_namespace_base}app\\controller";
        }
        $controller_file = base_path() . '/' . str_replace('\\', '/', $controller_namespace) . "/$controller_class.php";

        if ($model_path) {
            $model_path = trim($model_path, '/');
            $model_class = ucfirst(basename($model_path));
            $model_path = pathinfo($model_path, PATHINFO_DIRNAME);
            $model_path = $model_path === '.' ? '' : $model_path;
        }
        $path_backslash = str_replace('/', '\\', trim($model_path, '/'));
        if ($path_backslash) {
            $model_namespace = "{$model_namespace_base}app\\model\\$path_backslash";
        } else {
            $model_namespace = "{$model_namespace_base}app\\model";
        }

        $model_file = base_path() . '/' . str_replace('\\', '/', $model_namespace) . "/$model_class.php";
        if (!$overwrite) {
            if (is_file($controller_file)) {
                return $this->json(1, substr($controller_file, strlen(base_path())) . '已经存在');
            }
            if (is_file($model_file)) {
                return $this->json(1, substr($model_file, strlen(base_path())) . '已经存在');
            }
        }
        // 创建model
        $this->createModel($model_class, $model_namespace, $model_file, $table_name);

        // 创建controller
        $controller_url_name = $controller_suffix ? substr($controller_class, 0, -strlen($controller_suffix)) : $controller_class;
        $controller_url_name = str_replace('_', '-', $inflector->tableize($controller_url_name));
        $template_path = $controller_path ? "$controller_path/$controller_url_name" : $controller_url_name;
        $this->createController($controller_class, $controller_namespace, $controller_file, $model_class, $model_namespace, $title, $template_path);

        // 创建模版
        $template_file_path = "$controller_path_base/app/view/$template_path";
        $model_class_with_namespace = "$model_namespace\\$model_class";
        $primary_key = (new $model_class_with_namespace)->getKeyName();
        $this->createTemplate($template_file_path, $table_name, $template_path, $url_path_base, $primary_key);

        $reflection = new \ReflectionClass("$controller_namespace\\$controller_class");
        $controller_class_with_nsp = $reflection->getName();

        $menu = AdminRule::where('key', $controller_class_with_nsp)->first();
        if (!$menu) {
            $menu = new AdminRule;
        }
        $menu->pid = $pid;
        $menu->key = $controller_class_with_nsp;
        $menu->title = $title;
        $menu->icon = $icon;
        $controller_path = $controller_path ? "$controller_path/" : '';
        $menu->href = "$url_path_base/$controller_path$controller_url_name/index";
        $menu->save();

        $roles = admin('roles');
        $rules = AdminRole::whereIn('id', $roles)->pluck('rules');
        $rule_ids = [];
        foreach ($rules as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rule_ids = array_merge($rule_ids, explode(',', $rule_string));
        }

        // 不是超级管理员，则需要给当前管理员这个菜单的权限
        if (!in_array('*', $rule_ids) && $roles){
            $role = AdminRole::find(current($roles));
            if ($role) {
                $role->rules .= ",{$menu->id}";
            }
            $role->save();
        }

        return $this->json(0);
    }

    /**
     * 创建model
     * @param $class
     * @param $namespace
     * @param $file
     * @param $table
     * @return void
     */
    protected function createModel($class, $namespace, $file, $table)
    {
        $this->mkdir($file);
        $table_val = "'$table'";
        $pk = 'id';
        $properties = '';
        $timestamps = '';
        $columns = [];
        try {
            $database = config('database.connections')['plugin.admin.mysql']['database'];
            //plugin.admin.mysql
            foreach (Util::db()->select("select COLUMN_NAME,DATA_TYPE,COLUMN_KEY,COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS where table_name = '$table' and table_schema = '$database'") as $item) {
                if ($item->COLUMN_KEY === 'PRI') {
                    $pk = $item->COLUMN_NAME;
                    $item->COLUMN_COMMENT .= "(主键)";
                }
                $type = $this->getType($item->DATA_TYPE);
                $properties .= " * @property $type \${$item->COLUMN_NAME} {$item->COLUMN_COMMENT}\n";
                $columns[$item->COLUMN_NAME] = $item->COLUMN_NAME;
            }
        } catch (\Throwable $e) {echo $e;}
        if (!isset($columns['created_at']) || !isset($columns['updated_at'])) {
            $timestamps = <<<EOF
/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public \$timestamps = false;
EOF;

        }
        $properties = rtrim($properties) ?: ' *';
        $model_content = <<<EOF
<?php

namespace $namespace;

use plugin\admin\app\model\Base;

/**
$properties
 */
class $class extends Base
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected \$table = $table_val;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected \$primaryKey = '$pk';
    
    $timestamps
    
    
}

EOF;
        file_put_contents($file, $model_content);
    }

    /**
     * 创建控制器
     * @param $controller_class
     * @param $namespace
     * @param $file
     * @param $model_class
     * @param $model_namespace
     * @param $name
     * @param $template_path
     * @return void
     */
    protected function createController($controller_class, $namespace, $file, $model_class, $model_namespace, $name, $template_path)
    {
        $this->mkdir($file);
        $controller_content = <<<EOF
<?php

namespace $namespace;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use $model_namespace\\$model_class;
use support\\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * $name 
 */
class $controller_class extends Crud
{
    
    /**
     * @var $model_class
     */
    protected \$model = null;

    /**
     * 构造函数
     * @return void
     */
    public function __construct()
    {
        \$this->model = new $model_class;
    }
    
    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view('$template_path/index');
    }

    /**
     * 插入
     * @param Request \$request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request \$request): Response
    {
        if (\$request->method() === 'POST') {
            return parent::insert(\$request);
        }
        return view('$template_path/insert');
    }

    /**
     * 更新
     * @param Request \$request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request \$request): Response
    {
        if (\$request->method() === 'POST') {
            return parent::update(\$request);
        }
        return view('$template_path/update');
    }

}

EOF;
        file_put_contents($file, $controller_content);
    }

    /**
     * 创建控制器
     * @param $template_file_path
     * @param $table
     * @param $template_path
     * @param $url_path_base
     * @param $primary_key
     * @return void
     */
    protected function createTemplate($template_file_path, $table, $template_path, $url_path_base, $primary_key)
    {
        $this->mkdir($template_file_path . '/index.html');
        $form = LayuiForm::buildForm($table, 'search');
        $html = $form->html(3);
        $html = $html ? <<<EOF
<div class="layui-card">
    <div class="layui-card-body">
        <form class="layui-form top-search-from">
            $html
            <div class="layui-form-item layui-inline">
                <label class="layui-form-label"></label>
                <button class="pear-btn pear-btn-md pear-btn-primary" lay-submit lay-filter="table-query">
                    <i class="layui-icon layui-icon-search"></i>查询
                </button>
                <button type="reset" class="pear-btn pear-btn-md" lay-submit lay-filter="table-reset">
                    <i class="layui-icon layui-icon-refresh"></i>重置
                </button>
            </div>
            <div class="toggle-btn">
                <a class="layui-hide">展开<i class="layui-icon layui-icon-down"></i></a>
                <a class="layui-hide">收起<i class="layui-icon layui-icon-up"></i></a>
            </div>
        </form>
    </div>
</div>
EOF
            : '';
        $html = str_replace("\n", "\n" . str_repeat('    ', 2), $html);
        $js = $form->js(3);
        $table_js = LayuiForm::buildTable($table, 4);
        $template_content = <<<EOF

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>浏览页面</title>
        <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
        <link rel="stylesheet" href="/app/admin/admin/css/reset.css" />
    </head>
    <body class="pear-container">
    
        <!-- 顶部查询表单 -->
        $html
        
        <!-- 数据表格 -->
        <div class="layui-card">
            <div class="layui-card-body">
                <table id="data-table" lay-filter="data-table"></table>
            </div>
        </div>

        <!-- 表格顶部工具栏 -->
        <script type="text/html" id="table-toolbar">
            <button class="pear-btn pear-btn-primary pear-btn-md" lay-event="add">
                <i class="layui-icon layui-icon-add-1"></i>新增
            </button>
            <button class="pear-btn pear-btn-danger pear-btn-md" lay-event="batchRemove">
                <i class="layui-icon layui-icon-delete"></i>删除
            </button>
        </script>

        <!-- 表格行工具栏 -->
        <script type="text/html" id="table-bar">
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="edit">编辑</button>
            <button class="pear-btn pear-btn-xs tool-btn" lay-event="remove">删除</button>
        </script>

        <script src="/app/admin/component/layui/layui.js"></script>
        <script src="/app/admin/component/pear/pear.js"></script>
        <script src="/app/admin/admin/js/common.js"></script>
        <script>

            // 相关接口
            const PRIMARY_KEY = '$primary_key';
            const SELECT_API = "$url_path_base/$template_path/select";
            const UPDATE_API = "$url_path_base/$template_path/update";
            const DELETE_API = "$url_path_base/$template_path/delete";
            const INSERT_URL = "$url_path_base/$template_path/insert";
            const UPDATE_URL = "$url_path_base/$template_path/update";
            $js
            // 表格渲染
            layui.use(['table', 'form', 'common', 'popup', 'util'], function() {
                let table = layui.table;
                let form = layui.form;
                let $ = layui.$;
                let common = layui.common;
                let util = layui.util;
                $table_js
                // 编辑或删除行事件
                table.on('tool(data-table)', function(obj) {
                    if (obj.event === 'remove') {
                        remove(obj);
                    } else if (obj.event === 'edit') {
                        edit(obj);
                    }
                });

                // 表格顶部工具栏事件
                table.on('toolbar(data-table)', function(obj) {
                    if (obj.event === 'add') {
                        add();
                    } else if (obj.event === 'refresh') {
                        refreshTable();
                    } else if (obj.event === 'batchRemove') {
                        batchRemove(obj);
                    }
                });

                // 表格顶部搜索事件
                form.on('submit(table-query)', function(data) {
                    table.reload('data-table', {
                        where: data.field
                    })
                    return false;
                });
                
                // 表格顶部搜索重置事件
                form.on('submit(table-reset)', function(data) {
                    table.reload('data-table', {
                        where: []
                    })
                });

                // 表格排序事件
                table.on('sort(data-table)', function(obj){
                    table.reload('data-table', {
                        initSort: obj,
                        scrollPos: 'fixed',
                        where: {
                            field: obj.field,
                            order: obj.type
                        }
                    });
                });

                // 表格新增数据
                let add = function() {
                    layer.open({
                        type: 2,
                        title: '新增',
                        shade: 0.1,
                        area: [common.isModile()?'100%':'500px', common.isModile()?'100%':'450px'],
                        content: INSERT_URL
                    });
                }

                // 表格编辑数据
                let edit = function(obj) {
                    let value = obj.data[PRIMARY_KEY];
                    layer.open({
                        type: 2,
                        title: '修改',
                        shade: 0.1,
                        area: [common.isModile()?'100%':'500px', common.isModile()?'100%':'450px'],
                        content: UPDATE_URL + '?' + PRIMARY_KEY + '=' + value
                    });
                }

                // 删除一行
                let remove = function(obj) {
                    return doRemove(obj.data[PRIMARY_KEY]);
                }

                // 删除多行
                let batchRemove = function(obj) {
                    let checkIds = common.checkField(obj, PRIMARY_KEY);
                    if (checkIds === "") {
                        layui.popup.warning('未选中数据');
                        return false;
                    }
                    doRemove(checkIds.split(','));
                }

                // 执行删除
                let doRemove = function (ids) {
                    let data = {};
                    data[PRIMARY_KEY] = ids;
                    layer.confirm('确定删除?', {
                        icon: 3,
                        title: '提示'
                    }, function(index) {
                        layer.close(index);
                        let loading = layer.load();
                        $.ajax({
                            url: DELETE_API,
                            data: data,
                            dataType: 'json',
                            type: 'post',
                            success: function(res) {
                                layer.close(loading);
                                if (res.code) {
                                    return layui.popup.failure(res.msg);
                                }
                                return layui.popup.success('操作成功', refreshTable);
                            }
                        })
                    });
                }

                // 刷新表格数据
                window.refreshTable = function(param) {
                    table.reloadData('data-table', {
                        scrollPos: 'fixed'
                    });
                }
            })

        </script>
    </body>
</html>

EOF;
        file_put_contents("$template_file_path/index.html", $template_content);

        $form = LayuiForm::buildForm($table);
        $html = $form->html(5);
        $js = $form->js(3);
        $template_content = <<<EOF
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>新增页面</title>
        <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
        <link rel="stylesheet" href="/app/admin/admin/css/reset.css" />
    </head>
    <body>

        <form class="layui-form" action="">

            <div class="mainBox">
                <div class="main-container">
                    $html
                </div>
            </div>

            <div class="bottom">
                <div class="button-container">
                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit=""
                        lay-filter="save">
                        提交
                    </button>
                    <button type="reset" class="pear-btn pear-btn-md">
                        重置
                    </button>
                </div>
            </div>
            
        </form>

        <script src="/app/admin/component/layui/layui.js"></script>
        <script src="/app/admin/component/pear/pear.js"></script>
        <script>

            // 相关接口
            const INSERT_API = "$url_path_base/$template_path/insert";
            $js
            //提交事件
            layui.use(['form', 'popup'], function () {
                layui.form.on('submit(save)', function (data) {
                    layui.$.ajax({
                        url: INSERT_API,
                        type: 'POST',
                        dateType: 'json',
                        data: data.field,
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success('操作成功', function () {
                                parent.refreshTable();
                                parent.layer.close(parent.layer.getFrameIndex(window.name));
                            });
                        }
                    });
                    return false;
                });
            });

        </script>

    </body>
</html>

EOF;

        file_put_contents("$template_file_path/insert.html", $template_content);

        $form = LayuiForm::buildForm($table, 'update');
        $html = $form->html(5);
        $js = $form->js(6);
        $template_content = <<<EOF
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>更新页面</title>
        <link rel="stylesheet" href="/app/admin/component/pear/css/pear.css" />
        <link rel="stylesheet" href="/app/admin/admin/css/reset.css" />
    </head>
    <body>

        <form class="layui-form">

            <div class="mainBox">
                <div class="main-container">
                    $html
                </div>
            </div>

            <div class="bottom">
                <div class="button-container">
                    <button type="submit" class="pear-btn pear-btn-primary pear-btn-md" lay-submit="" lay-filter="save">
                        提交
                    </button>
                    <button type="reset" class="pear-btn pear-btn-md">
                        重置
                    </button>
                </div>
            </div>
            
        </form>

        <script src="/app/admin/component/layui/layui.js"></script>
        <script src="/app/admin/component/pear/pear.js"></script>
        <script>

            // 相关接口
            const PRIMARY_KEY = '$primary_key';
            const SELECT_API = "$url_path_base/$template_path/select" + location.search;
            const UPDATE_API = "$url_path_base/$template_path/update";

            // 获取数据库记录
            layui.use(['form', 'util'], function () {
                let $ = layui.$;
                $.ajax({
                    url: SELECT_API,
                    dataType: 'json',
                    success: function (e) {
                        
                        // 给表单初始化数据
                        layui.each(e.data[0], function (key, value) {
                            let obj = $('*[name="'+key+'"]');
                            if (key === 'password') {
                                obj.attr('placeholder', '不更新密码请留空');
                                return;
                            }
                            if (typeof obj[0] === 'undefined' || !obj[0].nodeName) return;
                            if (obj[0].nodeName.toLowerCase() === 'textarea') {
                                obj.val(layui.util.escape(value));
                            } else {
                                obj.attr('value', value);
                            }
                        });
                        $js

                    }
                });
            });

            //提交事件
            layui.use(['form', 'popup'], function () {
                layui.form.on('submit(save)', function (data) {
                    data.field[PRIMARY_KEY] = layui.url().search[PRIMARY_KEY];
                    layui.$.ajax({
                        url: UPDATE_API,
                        type: 'POST',
                        dateType: 'json',
                        data: data.field,
                        success: function (res) {
                            if (res.code) {
                                return layui.popup.failure(res.msg);
                            }
                            return layui.popup.success('操作成功', function () {
                                parent.refreshTable();
                                parent.layer.close(parent.layer.getFrameIndex(window.name));
                            });
                        }
                    });
                    return false;
                });
            });

        </script>

    </body>

</html>

EOF;

        file_put_contents("$template_file_path/update.html", $template_content);

    }

    /**
     * 创建目录
     * @param $file
     * @return void
     */
    protected function mkdir($file)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }


    /**
     * 查询记录
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {
        $page = $request->get('page', 1);
        $field = $request->get('field');
        $order = $request->get('order', 'asc');
        $table = Util::filterAlphaNum($request->get('table', ''));
        $format = $request->get('format', 'normal');
        $page_size = $request->get('limit', $format === 'tree' ? 5000 : 10);

        $allow_column = Util::db()->select("desc $table");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = current($allow_column);
        }
        $order = $order === 'asc' ? 'asc' : 'desc';
        $paginator = Util::db()->table($table);
        foreach ($request->get() as $column => $value) {
            if ($value === '') {
                continue;
            }
            if (isset($allow_column[$column])) {
                if (is_array($value)) {
                    if (in_array($value[0], ['', 'undefined']) || in_array($value[1], ['', 'undefined'])) {
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
            $items = $formatted_items;
        }

        return json(['code' => 0, 'msg' => 'ok', 'count' => $paginator->total(), 'data' => $items]);

    }

    /**
     * 插入记录
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'GET') {
            $table = $request->get('table');
            $form = LayuiForm::buildForm($table);
            return view("table/insert", [
                'form' => $form,
                'table' => $table
            ]);
        }
        $table = Util::filterAlphaNum($request->input('table', ''));
        $data = $request->post();
        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            throw new BusinessException('表不存在', 2);
        }
        $columns = array_column($allow_column, 'Type', 'Field');
        foreach ($data as $col => $item) {
            if (!isset($columns[$col])) {
                unset($data[$col]);
                continue;
            }
            // 非字符串类型传空则为null
            if ($item === '' && strpos(strtolower($columns[$col]), 'varchar') === false && strpos(strtolower($columns[$col]), 'text') === false) {
                $data[$col] = null;
            }
            if (is_array($item)) {
                $data[$col] = implode(',', $item);
                continue;
            }
            if ($col === 'password') {
                $data[$col] = Util::passwordHash($item);
            }
        }
        $datetime = date('Y-m-d H:i:s');
        if (isset($columns['created_at']) && empty($data['created_at'])) {
            $data['created_at'] = $datetime;
        }
        if (isset($columns['updated_at']) && empty($data['updated_at'])) {
            $data['updated_at'] = $datetime;
        }
        $id = Util::db()->table($table)->insertGetId($data);
        return $this->json(0, $id);
    }

    /**
     * 更新记录
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response
    {
        if ($request->method() === 'GET') {
            $table = $request->get('table');
            $table_info = Util::getSchema($table, 'table');
            $primary_key = $table_info['primary_key'][0] ?? null;
            $value = htmlspecialchars($request->get($primary_key, ''));
            $form = LayuiForm::buildForm($table,'update');
            return view("table/update", [
                'primary_key' => $primary_key,
                'value' => $value,
                'form' => $form,
                'table' => $table
            ]);
        }
        $table = Util::filterAlphaNum($request->post('table'));
        $table_info = Util::getSchema($table, 'table');
        $primary_keys = $table_info['primary_key'];
        if (empty($primary_keys)) {
            return $this->json(1, '该表没有主键，无法执行更新操作');
        }
        if (count($primary_keys) > 1) {
            return $this->json(1, '不支持复合主键更新');
        }
        $primary_key = $primary_keys[0];
        $value = $request->post($primary_key);
        $data = $request->post();
        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            throw new BusinessException('表不存在', 2);
        }
        $columns = array_column($allow_column, 'Type', 'Field');
        foreach ($data as $col => $item) {
            if (!isset($columns[$col])) {
                unset($data[$col]);
                continue;
            }
            // 非字符串类型传空则为null
            if ($item === '' && strpos(strtolower($columns[$col]), 'varchar') === false && strpos(strtolower($columns[$col]), 'text') === false) {
                $data[$col] = null;
            }
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
        if (isset($columns['updated_at']) && empty($data['updated_at'])) {
            $data['updated_at'] = $datetime;
        }
        Util::db()->table($table)->where($primary_key, $value)->update($data);
        return $this->json(0);
    }

    /**
     * 删除记录
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function delete(Request $request): Response
    {
        $table = $request->post('table');
        $table_info = Util::getSchema($table, 'table');
        $primary_keys = $table_info['primary_key'];
        if (empty($primary_keys)) {
            return $this->json(1, '该表没有主键，无法执行删除操作');
        }
        if (count($primary_keys) > 1) {
            return $this->json(1, '不支持复合主键删除');
        }
        $primary_key = $primary_keys[0];
        $value = (array)$request->post($primary_key);
        Util::db()->table($table)->whereIn($primary_key, $value)->delete();
        return $this->json(0);
    }


    /**
     * 删除表
     * @param Request $request
     * @return Response
     */
    public function drop(Request $request): Response
    {
        $tables = $request->post('tables');
        if (!$tables) {
            return $this->json(0, 'not found');
        }
        $table_not_allow_drop = ['wa_admins', 'wa_users', 'wa_options', 'wa_admin_roles', 'wa_admin_rules'];
        if ($found = array_intersect($tables, $table_not_allow_drop)) {
            return $this->json(400, implode(',', $found) . '不允许删除');
        }
        foreach ($tables as $table) {
            Util::schema()->drop($table);
            // 删除schema
            Util::db()->table('wa_options')->where('name', "table_form_schema_$table")->delete();
        }
        return $this->json(0, 'ok');
    }

    /**
     * 表摘要
     * @param Request $request
     * @return Response
     */
    public function schema(Request $request): Response
    {
        $table = $request->get('table');
        $data = Util::getSchema($table);

        return $this->json(0, 'ok', [
            'table' => $data['table'],
            'columns' => array_values($data['columns']),
            'forms' => array_values($data['forms']),
            'keys' => array_values($data['keys']),
        ]);
    }

    /**
     * 创建字段
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
                $column['default'] = null;
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

        if ($method != 'text' && $column['default'] !== null) {
            $column_def->default($column['default']);
        }
        return $column_def;
    }

    /**
     * 更改字段
     * @param $column
     * @param $table
     * @return mixed
     * @throws BusinessException
     */
    protected function modifyColumn($column, $table)
    {
        $table = Util::filterAlphaNum($table);
        $method = Util::filterAlphaNum($column['type']);
        $field = Util::filterAlphaNum($column['field']);
        $old_field = Util::filterAlphaNum($column['old_field'] ?? null);
        $nullable = $column['nullable'];
        $default = Util::filterAlphaNum($column['default']);
        $comment = Util::pdoQuote($column['comment']);
        $auto_increment = $column['auto_increment'];
        $length = (int)$column['length'];

        if ($column['primary_key']) {
            $default = null;
        }

        if ($old_field && $old_field !== $field) {
            $sql = "ALTER TABLE $table CHANGE COLUMN `$old_field` `$field` ";
        } else {
            $sql = "ALTER TABLE $table MODIFY `$field` ";
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
                $column['default'] = null;
                $sql .= 'AUTO_INCREMENT ';
            }
        } else {
            switch ($method) {
                case 'string':
                    $length = $length ?: 255;
                    $sql .= "varchar($length) ";
                    break;
                case 'char':
                case 'time':
                    $sql .= $length ? "$method($length) " : "$method ";
                    break;
                case 'enum':
                    $args = array_map('trim', explode(',', (string)$column['length']));
                    foreach ($args as $key => $value) {
                        $args[$key] = Util::pdoQuote($value);
                    }
                    $sql .= 'enum(' . implode(',', $args) . ') ';
                    break;
                case 'double':
                case 'float':
                case 'decimal':
                    if (trim($column['length'])) {
                        $args = array_map('intval', explode(',', $column['length']));
                        $args[1] = $args[1] ?? $args[0];
                        $sql .= "$method($args[0], $args[1]) ";
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

        if ($method != 'text' && $default !== null) {
            $sql .= "DEFAULT '$default' ";
        }

        if ($comment !== null) {
            $sql .= "COMMENT $comment ";
        }

        echo "$sql\n";

        Util::db()->statement($sql);
    }

    /**
     * 字段类型列表
     * @param Request $request
     * @return Response
     */
    public function types(Request $request): Response
    {
        $types = Util::methodControlMap();
        return $this->json(0, 'ok', $types);
    }


    /**
     * 更新表的form schema信息
     * @param $table_name
     * @param $data
     * @return string
     */
    protected function updateSchemaOption($table_name, $data): string
    {
        $option_name = "table_form_schema_$table_name";
        $option = Option::where('name', $option_name)->first();
        if ($option) {
            Option::where('name', $option_name)->update(['value' => $data]);
        } else {
            Option::insert(['name' => $option_name, 'value' => $data]);
        }
        return $option_name;
    }

    /**
     * 字段类型到php类型映射
     * @param string $type
     * @return string
     */
    protected function getType(string $type): string
    {
        if (strpos($type, 'int') !== false) {
            return 'integer';
        }
        switch ($type) {
            case 'varchar':
            case 'string':
            case 'text':
            case 'date':
            case 'time':
            case 'guid':
            case 'datetimetz':
            case 'datetime':
            case 'decimal':
            case 'enum':
                return 'string';
            case 'boolean':
                return 'integer';
            case 'float':
                return 'float';
            default:
                return 'mixed';
        }
    }

}
