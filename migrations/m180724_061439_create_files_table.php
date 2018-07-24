<?php

use yii\db\Migration;

/**
 * Handles the creation of table `files`.
 */
class m180724_061439_create_files_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('files', [
            'id' => $this->primaryKey(),
            'obj_id' => $this->integer()->notNull(),
            'onj_type' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'md5' => $this->string()->notNull(),
            'path' => $this->string()->notNull(),
            'create_time' => $this->integer()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('files');
    }
}
