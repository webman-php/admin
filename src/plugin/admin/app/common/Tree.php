<?php
namespace plugin\admin\app\common;


class Tree
{
    /**
     * 数据
     * @var array
     */
    protected $data = [];

    /**
     * 哈希树
     * @var array
     */
    protected $hashTree = [];

    /**
     * 父级字段名
     * @var string
     */
    protected $pidName = 'pid';

    /**
     * @param $data
     * @param string $pid_name
     */
    public function __construct($data, string $pid_name = 'pid')
    {
        $this->pidName = $pid_name;
        if (is_object($data) && method_exists($data, 'toArray')) {
            $this->data = $data->toArray();
        } else {
            $this->data = (array)$data;
        }
        $this->hashTree = $this->getHashTree();
    }

    /**
     * 获取子孙节点
     * @param int $id
     * @param bool $with_self
     * @return array
     */
    public function getDescendants(int $id, bool $with_self = false): array
    {
        if (!isset($this->hashTree[$id])) {
            return [];
        }
        $items = [];
        if ($with_self) {
            $item = $this->hashTree[$id];
            unset($item['children']);
            $items[$item['id']] = $item;
        }
        foreach ($this->hashTree[$id]['children'] ?? [] as $item) {
            unset($item['children']);
            $items[$item['id']] = $item;
            $items = array_merge($items, $this->getDescendants($item['id']));
        }
        return array_values($items);
    }

    /**
     * 获取哈希树
     * @param array $data
     * @return array
     */
    protected function getHashTree(array $data = []): array
    {
        $data = $data ?: $this->data;
        $hash_tree = [];
        foreach ($data as $item) {
            $hash_tree[$item['id']] = $item;
        }
        foreach ($hash_tree as $index => $item) {
            if ($item[$this->pidName] && isset($hash_tree[$item[$this->pidName]])) {
                $hash_tree[$item[$this->pidName]]['children'][$hash_tree[$index]['id']] = &$hash_tree[$index];
            }
        }
        return $hash_tree;
    }

    /**
     * 获取树
     * @param array $include
     * @return array|null
     */
    public function getTree(array $include = []): ?array
    {
        $hash_tree = $this->hashTree;
        $items = [];
        if ($include) {
            $map = [];
            foreach ($include as $id) {
                if (!isset($hash_tree[$id])) {
                    continue;
                }
                $item = $hash_tree[$id];
                $max_depth = 100;
                while ($max_depth-- > 0 && $item[$this->pidName] && isset($hash_tree[$item[$this->pidName]])) {
                    $last_item = $item;
                    $item = $hash_tree[$item[$this->pidName]];
                    $item_id = $item['id'];
                    if (empty($map[$item_id])) {
                        $map[$item_id] = 1;
                        $item['children'] = [];
                    }
                    $item['children'][$last_item['id']] = $last_item;
                }
                $items[$item['id']] = $item;
            }
        } else {
            $items = $hash_tree;
        }
        $formatted_items = [];
        foreach ($items as $item) {
            if (!$item[$this->pidName]) {
                $formatted_items[] = $item;
            }
        }
        $formatted_items = array_values($formatted_items);
        foreach ($formatted_items as &$item) {
            $this->arrayValues($item);
        }
        return $formatted_items;
    }

    /**
     * 递归重建数组下标
     * @return void
     */
    protected function arrayValues(&$array)
    {
        if (!is_array($array) || !isset($array['children'])) {
            return;
        }
        $array['children'] = array_values($array['children']);
        foreach ($array['children'] as &$child) {
            $this->arrayValues($child);
        }
    }

}