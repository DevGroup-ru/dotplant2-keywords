<?php

use yii\db\Migration;

class m150827_071712_initial extends Migration
{

    public function up()
    {
        $this->createTable(
            '{{%object_keyword}}',
            [
                'id' => $this->primaryKey(),
                'object_id' => $this->integer()->notNull(),
                'object_model_id' => $this->integer()->notNull(),
                'keywords' => $this->string()->notNull(),
            ]
        );
        $this->insert(
            '{{%configurable}}',
            [
                'module' => 'Keywords',
                'sort_order' => 99,
                'section_name' => 'Keywords',
                'display_in_config' => 0,
            ]
        );
    }

    public function down()
    {
        $this->dropTable('{{%object_keyword}}');
        $this->delete('{{%configurable}}', ['module' => 'Keywords']);
    }

}
