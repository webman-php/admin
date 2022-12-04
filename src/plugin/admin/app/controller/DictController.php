<?php

namespace plugin\admin\app\controller;

use plugin\admin\app\model\Option;
use support\exception\BusinessException;
use support\Request;
use support\Response;

/**
 * 字典管理 
 */
class DictController extends Base
{
    /**
     * 不需要授权的方法
     */
    public $noNeedAuth = ['get'];

    /**
     * 浏览
     * @return Response
     */
    public function index(): Response
    {
        return view("dict/index");
    }

    /**
     * 插入
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function insert(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $option_name = $this->dictNameToOptionName($request->post('name'));
            if (Option::where('name', $option_name)->first()) {
                return $this->json(1, '字典已经存在' . $option_name);
            }
            $values = (array)$request->post('value', []);
            $format_values = $this->filterValue($values);
            $option = new Option;
            $option->name = $option_name;
            $option->value = json_encode($format_values, JSON_UNESCAPED_UNICODE);
            $option->save();
            return $this->json(0);
        }
        return view("dict/insert");
    }

    /**
     * 更新
     * @param Request $request
     * @return Response
     * @throws BusinessException
    */
    public function update(Request $request): Response
    {
        if ($request->method() === 'POST') {
            $name = $this->dictNameToOptionName($request->post('name', ''));
            $option = Option::where('name', $name)->first();
            if (!$option) {
                return $this->json(1, '字典不存在');
            }
            $format_values = $this->filterValue($request->post('value'));
            $option->name = $this->dictNameToOptionName($request->post('name'));
            $option->value = $format_values;
            $option->save();
        }
        return view("dict/update");
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        $names = (array)$request->post('name');
        foreach ($names as $index => $name) {
            $names[$index] = $this->dictNameToOptionName($name);
        }
        Option::whereIn('name', $names)->delete();
        return $this->json(0);
    }

    /**
     * 查询
     * @param Request $request
     * @return Response
     */
    public function select(Request $request): Response
    {
        $name = $request->get('name', '');
        if ($name && is_string($name)) {
            $items = Option::where('name', 'like', "dict_$name%")->get()->toArray();
        } else {
            $items = Option::where('name', 'like', 'dict_%')->get()->toArray();
        }
        foreach ($items as &$item) {
            $item['name'] = $this->optionNameTodictName($item['name']);
        }
        return $this->json(0, 'ok', $items);
    }

    /**
     * 获取
     * @param Request $request
     * @param $name
     * @return Response
     */
    public function get(Request $request, $name): Response
    {
        $value = Option::where('name', $this->dictNameToOptionName($name))->value('value');
        if ($value === null) {
            return $this->json(1, '字典不存在');
        }
        return $this->json(1, 'ok', json_decode($value, true));
    }

    /**
     * 过滤字典选项
     * @param array $values
     * @return array
     * @throws BusinessException
     */
    protected function filterValue(array $values): array
    {
        $format_values = [];
        foreach ($values as $item) {
            if (!isset($item['value']) || !isset($item['name'])) {
                throw new BusinessException('格式错误', 1);
            }
            $format_values[] =  ['value' => $item['value'], 'name' => $item['name']];
        }
        return $format_values;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function dictNameToOptionName(string $name): string
    {
        return "dict_$name";
    }

    /**
     * @param string $name
     * @return string
     */
    protected function optionNameTodictName(string $name): string
    {
        return substr($name, 5);
    }

}
