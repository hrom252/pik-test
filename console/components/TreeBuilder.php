<?php

namespace console\components;

use console\models\Tree;
use yii\base\BaseObject;

class TreeBuilder extends BaseObject
{
    /**
     * @var array
     */
    private $tree = [];
    /**
     * @var string
     */
    private $_lastError;

    /**
     * @param array $data
     * @return TreeBuilder
     */
    public function loadTreeFromArray(array $data)
    {
        $positionColumn = array_column($data, 0);
        array_multisort($positionColumn, SORT_ASC, $data);

        foreach ($data as $row) {
            $positionPath = explode('.', $row[0]);

            $addInner = function($positionPath, &$arr, $value) use (&$addInner) {
                $key = array_shift($positionPath);
                $arr[$key]['ownData'] = $arr[$key]['ownData'] ?? [];

                if (empty($positionPath)) {
                    $arr[$key]['ownData'] = $value;
                    return;
                }

                $arr[$key]['childrenData'] = $arr[$key]['childrenData'] ?? [];
                $addInner($positionPath, $arr[$key]['childrenData'], $value);
            };

            $addInner($positionPath, $this->tree, ['title' => $row[1], 'price' => $row[2]]);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function saveTreeToDB()
    {
        $success = true;

        try {
            if (empty($this->tree)) {
                throw new \Exception('The tree is empty');
            }

            $saveLevel = function ($parentId, $level) use (&$saveLevel) {
                foreach ($level as $id => $item) {
                    $treeItem = Tree::find()->where(['position' => $id, 'parent_id' => $parentId])->one();
                    $treeItem = $treeItem ?? new Tree();
                    $treeItem->position = $id;
                    $treeItem->parent_id = $parentId;
                    $treeItem->title = $item['ownData']['title'];
                    $treeItem->price = explode(' ', $item['ownData']['price'])[0];

                    if (!$treeItem->save()) {
                        throw new \Exception('Can\'t save item');
                    }

                    if (!empty($item['childrenData'])) {
                        $saveLevel($treeItem->id, $item['childrenData']);
                    }
                }
            };

            $saveLevel(null, $this->tree);
        } catch (\Exception $e) {
            $success = false;
            $errorText = $e->getMessage();
            $this->lastError = $errorText;
        }

        return $success;
    }

    public function getLastError()
    {
        return $this->_lastError;
    }

    protected function setLastError($value)
    {
        $this->_lastError = $value;
    }
}



