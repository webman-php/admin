<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use plugin\admin\app\Util;
use support\Request;

class AdminRuleController extends Base
{
    /**
     * @var AdminRule
     */
    protected $model = null;

    /**
     * 增删改查
     */
    use Crud;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new AdminRule;
    }

    /**
     * 获取权限树
     *
     * @param Request $request
     * @return \support\Response
     */
    public function tree(Request $request)
    {
        $this->syncRules();
        $items = $this->model->get();
        return $this->formatTree($items);
    }

    /**
     * 根据类同步规则到数据库
     *
     * @return void
     */
    protected function syncRules()
    {
        $items = $this->model->where('name', 'like', '%\\\\%')->get()->keyBy('name');
        $methods_in_db = [];
        $methods_in_files = [];
        foreach ($items as $item) {
            $class = $item->name;
            if (strpos($class, '@')) {
                $methods_in_db[$class] = $class;
                continue;
            }
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $properties = $reflection->getDefaultProperties();
                $no_need_auth = array_merge($properties['noNeedLogin'] ?? [], $properties['noNeedAuth'] ?? []);
                $class = $reflection->getName();
                $pid = $item->id;
                $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    $method_name = $method->getName();
                    if (strpos($method_name, '__') === 0 || in_array($method_name, $no_need_auth)) {
                        continue;
                    }
                    $name = "$class@$method_name";
                    $methods_in_files[$name] = $name;
                    $title = Util::getCommentFirstLine($method->getDocComment()) ?: $method_name;
                    $menu = $items[$name] ?? [];
                    if ($menu) {
                        if ($menu->title != $title) {
                            AdminRule::where('name', $name)->update(['title' => $title]);
                        }
                        continue;
                    }
                    $menu = new AdminRule;
                    $menu->pid = $pid;
                    $menu->name = $name;
                    $menu->path = '';
                    $menu->component = '';
                    $menu->title = $title;
                    $menu->is_menu = 0;
                    $menu->save();
                }
            }
        }
        // 从数据库中删除已经不存在的方法
        $menu_names_to_del = array_diff($methods_in_db, $methods_in_files);
        if ($menu_names_to_del) {
            AdminRule::whereIn('name', $menu_names_to_del)->delete();
        }
    }

    /**
     * 查询
     *
     * @param Request $request
     * @return \support\Response
     */
    public function select(Request $request)
    {
        [$where, $format, $page_size, $field, $order] = $this->selectInput($request);
        $where['is_menu'] = 1;
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
        $allow_column = Util::db()->select("desc $table");
        if (!$allow_column) {
            return $this->json(2, '表不存在');
        }
        $name = $data['name'];
        if ($this->model->where('name', $name)->first()) {
            return $this->json(1, "菜单key $name 已经存在");
        }
        $columns = array_column($allow_column, 'Field', 'Field');
        foreach ($data as $col => $item) {
            if (is_array($item)) {
                $data[$col] = implode(',', $item);
            }
        }
        $datetime = date('Y-m-d H:i:s');
        if (isset($columns['created_at']) && !isset($data['created_at'])) {
            $data['created_at'] = $datetime;
        }
        if (isset($columns['updated_at']) && !isset($data['updated_at'])) {
            $data['updated_at'] = $datetime;
        }
        if (!empty($data['frame_src'])) {
            $data['component'] = '';
        }
        $id = $this->model->insertGetId($data);
        return $this->json(0, $id);
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
        $item = $this->model->where($column, $value)->first();
        if (!$item) {
            return $this->json(1, '记录不存在');
        }
        // 子规则一起删除
        $delete_ids = $children_ids = [$item['id']];
        while($children_ids) {
            $children_ids = $this->model->whereIn('pid', $children_ids)->pluck('id')->toArray();
            $delete_ids = array_merge($delete_ids, $children_ids);
        }
        $this->model->whereIn('id', $delete_ids)->delete();
        return $this->json(0);
    }

    /**
     * 一键生成菜单
     *
     * @param Request $request
     * @return \support\Response
     * @throws \Support\Exception\BusinessException
     */
    public function create(Request $request)
    {
        $table_name = $request->input('table');
        Util::checkTableName($table_name);
        $name = $request->post('name');
        $pid = $request->post('pid', 0);
        $icon = $request->post('icon', '');
        $path = '';
        $overwrite = $request->post('overwrite');

        $pid = (int)$pid;
        if ($pid) {
            $parent_menu = AdminRule::find($pid);
            if (!$parent_menu) {
                return $this->json(1, '父菜单不存在');
            }
            $path = $parent_menu['path'];
        }

        $table_basename = strpos($table_name, 'wa_') === 0 ? substr($table_name, 3) : $table_name;
        $model_class = Util::camel($table_basename);
        $suffix = substr($model_class, -2);
        if ($suffix != 'ss' && $suffix != 'es') {
            $model_class = rtrim($model_class, 's');
        }

        $controller_class = $model_class . config('plugin.admin.app.controller_suffix');
        $path = trim($path, '/');
        $path_backslash = str_replace('/', '\\', $path);
        if ($path_backslash) {
            $controller_namespace = "plugin\\admin\\app\\controller\\$path_backslash";
        } else {
            $controller_namespace = "plugin\\admin\\app\\controller";
        }
        $controller_file = base_path() . '/' . str_replace('\\', '/', $controller_namespace) . "/$controller_class.php";

        $model_file = base_path() . "/plugin/admin/app/model/$model_class.php";
        if (!$overwrite) {
            if (is_file($controller_file)) {
                return $this->json(1, substr($controller_file, strlen(base_path())) . '已经存在');
            }
            if (is_file($model_file)) {
                return $this->json(1, substr($model_file, strlen(base_path())) . '已经存在');
            }
        }

        // 创建model
        $this->createModel($model_class, "plugin\\admin\\app\\model", $model_file, $table_name);

        // 创建controller
        $this->createController($controller_class, $controller_namespace, $controller_file, $model_class, $name);

        // 菜单相关参数
        $menu_path = str_replace('_', '', $table_basename);
        $suffix = substr($menu_path, -2);
        if ($suffix != 'ss' && $suffix != 'es') {
            $menu_path = rtrim($menu_path, 's');
        }
        $componet = '/database/table/View';
        $reflection = new \ReflectionClass("$controller_namespace\\$controller_class");
        $controller_class_with_nsp = $reflection->getName();

        $menu = AdminRule::where('name', $controller_class_with_nsp)->first();
        if (!$menu) {
            $menu = new AdminRule;
        }
        $menu->pid = $pid;
        $menu->name = $controller_class_with_nsp;
        $menu->path = $pid ? $menu_path : "/$menu_path";
        $menu->component = $componet;
        $menu->title = $name;
        $menu->icon = $icon;
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
     *
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
        } catch (\Throwable $e) {}
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
     *
     * @param $controller_class
     * @param $namespace
     * @param $file
     * @param $model_class
     * @param $name
     * @return void
     */
    protected function createController($controller_class, $namespace, $file, $model_class, $name)
    {
        $this->mkdir($file);
        $controller_content = <<<EOF
<?php

namespace $namespace;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\\admin\\app\\model\\$model_class;
use support\Request;

/**
 * $name 
 */
class $controller_class extends Base
{
    /**
     * 开启增删改查 
     */
    use Crud;
    
    /**
     * @var $model_class
     */
    protected \$model = null;

    /**
     * 构造函数
     * 
     * @return void
     */
    public function __construct()
    {
        \$this->model = new $model_class;
    }

}

EOF;
        file_put_contents($file, $controller_content);
    }

    protected function mkdir($file)
    {
        $path = pathinfo($file, PATHINFO_DIRNAME);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    /**
     * 字段类型到php类型映射
     *
     * @param string $type
     * @return string
     */
    protected function getType(string $type)
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
