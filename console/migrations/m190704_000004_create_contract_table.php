<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%contract}}`.
 */
class m190704_000004_create_contract_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%contract}}', [
            'id' => $this->primaryKey(),
            'is_deleted' => $this->boolean(),
            'version' => $this->timestamp(),
            'code' => $this->string(12)->notNull(),
            'description' => $this->string(64)->notNull(),
            'date' => $this->timestamp()->notNull(),
            'number' => $this->string(12)->notNull(),
            'expires_at' => $this->timestamp()->notNull(),
            'counterparty_id' => $this->integer()->notNull(),
            'contract_type' => $this->smallInteger()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'contract-counterparty_id-fkey',
            'contract',
            'counterparty_id',
            'counterparty',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'contract-author_id-fkey',
            'contract',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%contract}}');
    }
}
