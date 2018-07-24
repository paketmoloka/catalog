<?php

use yii\db\Migration;

/**
 * Handles the creation of table `users`.
 */
class m180723_110937_create_users_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'login' => $this->string()->notNull()->unique(),
            'hash' => $this->string()->notNull(),
            'role' => $this->integer()->notNull(),
            'create_time' => $this->integer()->notNull(),
            'update_time' => $this->integer()->notNull()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('users');
    }
}
