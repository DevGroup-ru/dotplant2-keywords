<?php

use yii\db\Migration;

class m150827_071712_initial extends Migration
{

    public function up()
    {
        mb_internal_encoding("UTF-8");
        $tableOptions = $this->db->driverName === 'mysql' ? 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB' : null;
        $this->createTable(
            '{{%object_keyword}}',
            [
                'id' => $this->primaryKey(),
                'object_id' => $this->integer()->notNull(),
                'object_model_id' => $this->integer()->notNull(),
                'keywords' => $this->string()->notNull(),
            ],
            $tableOptions
        );
        $this->createTable(
            '{{%dynamic_content_keywords}}',
            [
                'id' => $this->primaryKey(),
                'dynamic_content_id' => $this->integer()->notNull(),
                'keywords' => $this->string()->notNull(),
            ],
            $tableOptions
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
        $this->dropTable('{{%dynamic_content_keywords}}');
        $this->delete('{{%configurable}}', ['module' => 'Keywords']);
    }

}
