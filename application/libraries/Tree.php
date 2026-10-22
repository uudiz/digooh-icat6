<?php
/**
 */

class Tree
{
    public function getTree($list, $pid = 0)
    {
        $tree = [];
        if (!empty($list)) {
            //先修改为以id为下标的列表
            $newList = [];
            foreach ($list as $k => $v) {
                $cast['id'] = $v['id'];
                $cast['text'] = $v['name'];
                $cast['pId'] = $v['pId'];
                $newList[$v["id"]] = $cast;
            }
            //然后开始组装成特殊格式
            foreach ($newList as $value) {
                if ($pid == $value['pId']) {
                    $tree[] = &$newList[$value['id']];
                } elseif (isset($newList[$value['pId']])) {
                    $newList[$value['pId']]['inc'][] = &$newList[$value['id']];
                    $ids [] = $value['id'];
                }
            }
        }
        return $tree;
    }
    public function formatTree($tree)
    {
        $options = [];
        if (!empty($tree)) {
            foreach ($tree as $value) {
                $options[] = $value['id'];
                if (isset($value['inc'])) {
                    $optionsTmp = $this->formatTree($value['inc']);
                    if (!empty($optionsTmp)) {
                        foreach ($optionsTmp as $opt) {
                            $options[] = $opt;
                        }
                    }
                }
            }
        }

        return $options;
    }
    public function getList($list, $pid = 0)
    {
        $tree = [];
        if (!empty($list)) {
            //先修改为以id为下标的列表
            $newList = [];
            foreach ($list as $k => $v) {
                $newList[$v["id"]] = $v;
            }
            //然后开始组装成特殊格式
            foreach ($newList as $value) {
                if ($pid == $value['pId']) {
                    $tree[] = &$newList[$value['id']];
                } elseif (isset($newList[$value['pId']])) {
                    $newList[$value['pId']]['child'][] = &$newList[$value['id']];
                    $ids [] = $value['id'];
                }
            }
        }
        return $tree;
    }

    public function formatList($tree)
    {
        $options = [];
        if (!empty($tree)) {
            foreach ($tree as $value) {
                $options[] = $value;
                if (isset($value['child'])) {
                    $optionsTmp = $this->formatList($value['child']);
                    if (!empty($optionsTmp)) {
                        foreach ($optionsTmp as $opt) {
                            $options[] = $opt;
                        }
                    }
                }
            }
        }
        return $options;
    }
}
