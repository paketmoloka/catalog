<?php

use yii\db\Migration;

/**
 * Handles the creation of table `recipes`.
 */
class m180724_054242_create_recipes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('recipes', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'user_id' => $this->integer(),
            'create_time' => $this->integer(),
            'update_time' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('recipes');
    }
}
