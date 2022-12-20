<?php

namespace plugin\admin\app\controller;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use plugin\admin\app\common\Tree;
use plugin\admin\app\common\Util;
use support\exception\BusinessException;
use support\Model;
use support\Request;
use support\Response;

class Crud extends Base
{

    /**
     * @var Model
     */
    protected $model = null;

    /**
     * 查询
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function select(Request $request): Response
    {
        [$where, $format, $limit, $field, $order] = $this->selectInput($request);
        $query = $this->doSelect($where, $field, $order);
        return $this->doFormat($query, $format, $limit);
    }

    /**
     * 添加
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        $data = $this->insertInput($request);
        $id = $this->doInsert($data);
        return $this->json(0, 'ok', ['id' => $id]);
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function update(Request $request): Response
    {
        [$id, $data] = $this->updateInput($request);
        $this->doUpdate($id, $data);
        return $this->json(0);
    }

    /**
     * 删除
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        $ids = $this->deleteInput($request);
        $this->doDelete($ids);
        return $this->json(0);
    }

    /**
     * 查询前置
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function selectInput(Request $request): array
    {
        $field = $request->get('field');
        $order = $request->get('order', 'asc');
        $format = $request->get('format', 'normal');
        $limit = $request->get('limit', $format === 'tree' ? 1000 : 10);
        $order = $order === 'asc' ? 'asc' : 'desc';
        $where = $request->get();
        $page = (int)$request->get('page');
        $page = $page > 0 ? $page : 1;
        $table = $this->model->getTable();

        $allow_column = Util::db()->select("desc `$table`");
        if (!$allow_column) {
            throw new BusinessException('表不存在');
        }
        $allow_column = array_column($allow_column, 'Field', 'Field');
        if (!in_array($field, $allow_column)) {
            $field = null;
        }
        foreach ($where as $column => $value) {
            if ($value === '' || !isset($allow_column[$column]) ||
                (is_array($value) && (in_array($value[0], ['', 'undefined']) || in_array($value[1], ['', 'undefined'])))) {
                unset($where[$column]);
            }
        }
        // 按照数据限制字段返回数据
        if ($this->dataLimit === 'personal') {
            $where[$this->dataLimitField] = admin_id();
        }

        return [$where, $format, $limit, $field, $order, $page];
    }

    /**
     * 执行查询
     * @param array $where
     * @param string|null $field
     * @param string $order
     * @return EloquentBuilder|QueryBuilder|Model
     */
    protected function doSelect(array $where, string $field = null, string $order= 'desc')
    {
        $model = $this->model;
        foreach ($where as $column => $value) {
            if (is_array($value)) {
                if (in_array($value[0], ['>', '=', '<', '<>', 'like'])) {
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
        if ($field) {
            $model = $model->orderBy($field, $order);
        }
        return $model;
    }

    /**
     * @param $query
     * @param $format
     * @param $limit
     * @return Response
     */
    protected function doFormat($query, $format, $limit): Response
    {
        if (in_array($format, ['select', 'tree', 'table_tree'])) {
            $items = $query->get();
            if ($format == 'select') {
                return $this->formatSelect($items);
            } elseif ($format == 'tree') {
                return $this->formatTree($items);
            }
            return $this->formatTableTree($items);
        }
        $paginator = $query->paginate($limit);
        return json(['code' => 0, 'msg' => 'ok', 'count' => $paginator->total(), 'data' => $paginator->items()]);
    }

    /**
     * 插入前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function insertInput(Request $request): array
    {
        $data = $this->inputFilter($request->post());
        $password_filed = 'password';
        if (isset($data[$password_filed])) {
            $data[$password_filed] = Util::passwordHash($data[$password_filed]);
        }
        return $data;
    }

    /**
     * 执行插入
     * @param array $data
     * @return mixed|null
     */
    protected function doInsert(array $data)
    {
        $primary_key = $this->model->getKeyName();
        $model_class = get_class($this->model);
        $model = new $model_class;
        foreach ($data as $key => $val) {
            $model->{$key} = $val;
        }
        $model->save();
        return $primary_key ? $model->$primary_key : null;
    }

    /**
     * 更新前置方法
     * @param Request $request
     * @return array
     * @throws BusinessException
     */
    protected function updateInput(Request $request): array
    {
        $primary_key = $this->model->getKeyName();
        $id = $request->post($primary_key);
        $data = $this->inputFilter($request->post());
        $password_filed = 'password';
        if (isset($data[$password_filed])) {
            // 密码为空，则不更新密码
            if ($data[$password_filed] === '') {
                unset($data[$password_filed]);
            } else {
                $data[$password_filed] = Util::passwordHash($data[$password_filed]);
            }
        }
        unset($data[$primary_key]);
        return [$id, $data];
    }

    /**
     * 执行更新
     * @param $id
     * @param $data
     * @return void
     * @throws BusinessException
     */
    protected function doUpdate($id, $data)
    {
        $model = $this->model->find($id);
        if (!$model) {
            throw new BusinessException('记录不存在', 2);
        }
        foreach ($data as $key => $val) {
            $model->{$key} = $val;
        }
        $model->save();
    }

    /**
     * 对用户输入表单过滤
     * @param array $data
     * @return array
     * @throws BusinessException
     */
    protected function inputFilter(array $data): array
    {
        $table = $this->model->getTable();
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
        }
        if (empty($data['created_at'])) {
            unset($data['created_at']);
        }
        if (empty($data['updated_at'])) {
            unset($data['updated_at']);
        }
        return $data;
    }

    /**
     * 删除前置方法
     * @param Request $request
     * @return array
     */
    protected function deleteInput(Request $request): array
    {
        $primary_key = $this->model->getKeyName();
        return (array)$request->post($primary_key, []);
    }

    /**
     * 执行删除
     * @param array $ids
     * @return void
     */
    protected function doDelete(array $ids)
    {
        if (!$ids) {
            return;
        }
        $primary_key = $this->model->getKeyName();
        $this->model->whereIn($primary_key, $ids)->delete();
    }

    /**
     * 格式化树
     * @param $items
     * @return Response
     */
    protected function formatTree($items): Response
    {
        $items_map = [];
        foreach ($items as $item) {
            $items_map[] = [
                'name' => $item->title ?? $item->name ?? $item->id,
                'value' => (string)$item->id,
                'pid' => $item->pid,
            ];
        }
        $tree = new Tree($items_map);
        return  $this->json(0, 'ok', $tree->getTree());
    }

    /**
     * 格式化表格树
     * @param $items
     * @return Response
     */
    protected function formatTableTree($items): Response
    {
        $tree = new Tree($items);
        return $this->json(0, 'ok', $tree->getTree());
    }

    /**
     * 格式化下拉列表
     * @param $items
     * @return Response
     */
    protected function formatSelect($items): Response
    {
        $formatted_items = [];
        foreach ($items as $item) {
            $formatted_items[] = [
                'name' => $item->title ?? $item->name ?? $item->id,
                'value' => $item->id
            ];
        }
        return  $this->json(0, 'ok', $formatted_items);
    }

}
