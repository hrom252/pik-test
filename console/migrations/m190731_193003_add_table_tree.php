<?php

use yii\db\Migration;
use \yii\db\Schema;

/**
 * Class m190731_193003_add_table_tree
 */
class m190731_193003_add_table_tree extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%tree}}';

        $this->createTable($tableName, [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'price' => Schema::TYPE_FLOAT . ' NOT NULL',
            'position' => Schema::TYPE_SMALLINT . ' NOT NULL',
            'parent_id' => Schema::TYPE_SMALLINT
        ]);

        $this->addForeignKey(
            'tree-parent_id-fk',
            $tableName,
            'parent_id',
            $tableName,
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'tree-position-parent_id-unique',
            $tableName,
            [
                'position',
                'parent_id'
            ],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190731_193003_add_table_tree cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190731_193003_add_table_tree cannot be reverted.\n";

        return false;
    }
    */
}
