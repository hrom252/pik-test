<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "tree".
 *
 * @property integer    $id
 * @property string     $title
 * @property float      $price
 * @property integer    $position
 * @property integer    $parent_id
 */
class Tree extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tree';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'price', 'position'], 'required'],
            ['title', 'string'],
            ['price', 'double'],
            [['parent_id', 'position'], 'integer'],
            ['parent_id', 'exist', 'targetAttribute' => 'id', 'skipOnEmpty' => true],
            [['position', 'parent_id'], 'unique', 'targetAttribute' => ['position', 'parent_id']]
        ];
    }
}