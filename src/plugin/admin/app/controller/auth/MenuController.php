<?php

namespace plugin\admin\app\controller\auth;

use plugin\admin\app\controller\Base;
use plugin\admin\app\controller\Crud;
use plugin\admin\app\model\AdminRole;
use plugin\admin\app\model\AdminRule;
use plugin\admin\app\model\Menu;
use plugin\admin\app\Util;
use support\Db;
use support\Request;
use function json;

class MenuController extends Base
{
    public $noNeedAuth = ['get'];

    /**
     * @var Menu
     */
    protected $model = null;

    use Crud;

    public function __construct()
    {
        $this->model = new Menu;
    }

    function get()
    {
        $roles = admin('roles');
        $roles = $roles ? explode(',', $roles) : [];
        $rules_strings = $roles ? AdminRole::whereIn('id', $roles)->pluck('rules') : [];
        $rules = [];
        foreach ($rules_strings as $rule_string) {
            if (!$rule_string) {
                continue;
            }
            $rules = array_merge($rules, explode(',', $rule_string));
        }

        if (in_array('*', $rules)) {
            $items = Db::connection('plugin.admin.mysql')->table('wa_admin_rules')->where('status', 'normal')->get();
        } else {
            $items = Db::connection('plugin.admin.mysql')->table('wa_admin_rules')->where('status', 'normal')->whereIn('id', $rules)->get();
        }

        $items_map = [];
        foreach ($items as $item) {
            $items_map[$item->id] = (array)$item;
        }
        $formatted_items = [];
        foreach ($items_map as $index => $item) {
            foreach (['title', 'icon', 'hide_menu', 'frame_src'] as $name) {
                $value = $item[$name];
                unset($items_map[$index][$name]);
                if (!$value) {
                    continue;
                }
                $items_map[$index]['meta'][Util::smCamel($name)] = $value;
            }
            if ($item['pid'] && isset($items_map[$item['pid']])) {
                $items_map[$item['pid']]['children'][] = $items_map[$index];
            }
        }
        foreach ($items_map as $item) {
            if (!$item['pid']) {
                $formatted_items[] = $item;
            }
        }
        return $this->json(0, 'ok', $formatted_items);
    }

    function tree()
    {
        $items = Db::connection('plugin.admin.mysql')->table('wa_admin_rules')->where('status', 'normal')->get();
        $items_map = [];
        foreach ($items as $item) {
            if ($item->hide_menu || !$item->is_menu) {
                continue;
            }
            $items_map[$item->id] = [
                'title' => $item->title,
                'value' => $item->id,
                'key' => $item->id,
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
        return $this->json(0, 'ok', $formatted_items);
    }

    public function create(Request $request)
    {
        $table_name = $request->input('table');
        Util::checkTableName($table_name);
        $name = $request->post('name');
        $pid = $request->post('pid');
        $path = '';

        if ($pid) {
            $parent_menu = Menu::find($pid);
            if (!$parent_menu) {
                return $this->json(1, '父菜单不存在');
            }
            $path = $parent_menu['path'];
        }

        $table_basename = strpos($table_name, 'wa_') === 0 ? substr($table_name, 3) : $table_name;
        $model_class = rtrim(Util::camel($table_basename), 's');
        // 创建model
        $this->createModel($model_class, "plugin\\admin\\app\\model",
            base_path() . "/plugin/admin/app/model/$model_class.php", $table_name);

        $controller_class = $model_class . config('plugin.admin.app.controller_suffix');
        $path = trim($path, '/');
        $path_backslash = str_replace('/', '\\', $path);
        if ($path_backslash) {
            $controller_namespace = "plugin\\admin\\app\\controller\\$path_backslash";
        } else {
            $controller_namespace = "plugin\\admin\\app\\controller";
        }
        $file = base_path() . '/' . str_replace('\\', '/', $controller_namespace) . "/$controller_class.php";
        // 创建controller
        $this->createController($controller_class, $controller_namespace, $file, $model_class, $name);

        // 创建vue
        $menu_path = rtrim(str_replace('_', '-', $table_basename), 's');
        $componet =  $parent_menu['path'] . "/$menu_path/index";
        $url_base = $path ? "/app/admin/$path/".strtolower($model_class) : "/app/admin/".strtolower($model_class);
        $file = base_path() . "/plugin/admin/vue-vben-admin/src/views/$componet.vue";
        $this->createVue($file, $url_base);

        $reflection = new \ReflectionClass("$controller_namespace\\$controller_class");
        $controller_class_with_nsp = $reflection->getName();

        $menu = new Menu();
        $menu->pid = $pid;
        $menu->name = $controller_class_with_nsp;
        $menu->path = $pid ? $menu_path : "/$menu_path";
        $menu->component = $componet;
        $menu->status = 'normal';
        $menu->title = $name;
        $menu->save();

        /*
        $pid = $menu->id;
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $method_name = $method->getName();
            if (strpos($method_name, '__') !== false) {
                continue;
            }
            $title = Util::getCommentFirstLine($method->getDocComment()) ?? $method_name;
            $menu = new Menu();
            $menu->pid = $pid;
            $menu->name = "$controller_class_with_nsp@$method_name";
            $menu->path = '';
            $menu->component = '';
            $menu->status = 'normal';
            $menu->title = $title;
            $menu->is_menu = 0;
            $menu->save();
        }*/

        return $this->json(0);
    }

    /**
     * @param $class
     * @param $namespace
     * @param $file
     * @return void
     */
    protected function createModel($class, $namespace, $file, $table)
    {
        $this->mkdir($file);
        $table_val = "'$table'";
        $pk = 'id';
        $properties = '';
        try {
            $database = config('database.connections')['plugin.admin.mysql']['database'];
            //plugin.admin.mysql
            foreach (Db::connection('plugin.admin.mysql')->select("select COLUMN_NAME,DATA_TYPE,COLUMN_KEY,COLUMN_COMMENT from INFORMATION_SCHEMA.COLUMNS where table_name = '$table' and table_schema = '$database'") as $item) {
                if ($item->COLUMN_KEY === 'PRI') {
                    $pk = $item->COLUMN_NAME;
                    $item->COLUMN_COMMENT .= "(主键)";
                }
                $type = $this->getType($item->DATA_TYPE);
                $properties .= " * @property $type \${$item->COLUMN_NAME} {$item->COLUMN_COMMENT}\n";
            }
        } catch (\Throwable $e) {}
        $properties = rtrim($properties) ?: ' *';
        $model_content = <<<EOF
<?php

namespace $namespace;

use support\Model;

/**
$properties
 */
class $class extends Model
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
    
    
}

EOF;
        file_put_contents($file, $model_content);
    }

    /**
     * @param $name
     * @param $namespace
     * @param $file
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

    protected function createVue($file, $url_base)
    {
        $this->mkdir($file);
        $vue_content = <<<EOF
<template>
  <div class="p-4">
    <BasicTable @register="registerTable" showTableSetting>
      <template #toolbar>
        <a-button type="primary" @click="openRowModal">
          添加记录
        </a-button>
      </template>
      <template #action="{ record }">
        <TableAction
          :actions="[
            {
              label: '编辑',
              onClick: handleEdit.bind(null, record),
            },
            {
              label: '删除',
              icon: 'ic:outline-delete-outline',
              popConfirm: {
                title: '是否删除？',
                confirm: handleDelete.bind(null, record),
              }
            },
          ]"
        />
      </template>
    </BasicTable>

    <ModalInserOrEdit @register="register" :minHeight="300" @reload="reload" />

  </div>
</template>
<script lang="ts">
import {defineComponent, h, onBeforeMount, ref} from 'vue';
import {BasicTable, useTable, TableAction, BasicColumn} from '/@/components/Table';
import {apiGet, apiPost, getApi} from "/@/api/common";
import { useModal } from '/@/components/Modal';
import ModalInserOrEdit from '/@/views/database/table/Update.vue';
import {useMessage} from "/@/hooks/web/useMessage";
import {error} from "/@/utils/log";

const selectUrl = '$url_base/select';
const insertUrl = '$url_base/insert';
const updateUrl = '$url_base/update';
const deleteUrl = '$url_base/delete';
const schemaUrl = '$url_base/schema';

const formSchemas = ref({schemas:[]});

export default defineComponent({
  components: {ModalInserOrEdit, BasicTable, TableAction },
  setup() {
    const {
      createMessage
    } = useMessage();
    const {success} = createMessage;
    const columns = ref([]);
    const primaryKey = ref('');
    onBeforeMount(async () => {
      const tableInfo =  await apiGet(schemaUrl);
      const schemas = tableInfo.columns;
      for (let item of schemas) {
        if (item.primary_key) {
          primaryKey.value = item.field;
          break;
        }
      }
      const forms = tableInfo.forms;
      formSchemas.value.schemas = [];
      for (let item of forms) {
        if (item.searchable) {
          if (item.search_type == 'between') {
            formSchemas.value.schemas.push({
              field: `\${item.field}[0]`,
              component: 'Input',
              label: item.comment || item.field,
              colProps: {
                offset: 1,
                span: 5,
              },
            });

            formSchemas.value.schemas.push({
              field: `\${item.field}[1]`,
              component: 'Input',
              label: '　到',
              colProps: {
                span: 5,
              },
            });
          } else {
            formSchemas.value.schemas.push({
              field: item.field,
              component: 'Input',
              label: item.comment || item.field,
              colProps: {
                offset: 1,
                span: 10,
              },
            });
          }
        }
        if (item.list_show) {
          let column: BasicColumn = {
            dataIndex: item.field,
            title: item.comment || item.field,
            sorter: item.enable_sort,
          };
          columns.value.push(column);
          if (item.field == 'avatar') {
            column.width = 50;
            column.customRender = ({ record }) => {
              return h('img', { src: record[item.field]});
            }
          }
        }
      }
    });


    const [register, { openModal: openModal }] = useModal();
    const [registerTable, { reload }] = useTable({
      api: getApi(selectUrl),
      columns: columns,
      useSearchForm: true,
      bordered: true,
      formConfig: formSchemas,
      actionColumn: {
        width: 250,
        title: 'Action',
        dataIndex: 'action',
        slots: { customRender: 'action' },
      },
    });

    async function handleEdit(record: Recordable) {
      if (!primaryKey.value) {
        error('当前表没有主键，无法编辑');
        return;
      }
      openModal(true, {
        selectUrl,
        insertUrl,
        updateUrl,
        schemaUrl,
        column: primaryKey.value,
        value: record[primaryKey.value]
      });
    }

    async function handleDelete(record: Recordable) {
      if (!primaryKey.value) {
        error('当前表没有主键，无法删除');
        return;
      }
      await apiPost(deleteUrl, {column: primaryKey.value, value:record[primaryKey.value]});
      success('删除成功');
      reload();
    }

    function openRowModal()
    {
      openModal(true, {
        selectUrl,
        insertUrl,
        updateUrl,
        schemaUrl
      });
    }

    return {
      registerTable,
      handleEdit,
      handleDelete,
      openRowModal,
      register,
      reload,
    };
  },
});
</script>
EOF;
        file_put_contents($file, $vue_content);
    }

}
