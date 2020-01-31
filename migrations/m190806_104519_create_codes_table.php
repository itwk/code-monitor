<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%codes}}`.
 */
class m190806_104519_create_codes_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%codes}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull()->unique(),
            'code' => $this->string()->notNull(),
            'status' => $this->tinyInteger()->defaultValue(1),
            'last_success_check' => $this->string(),
            'comment' => $this->text()
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%codes}}');
    }
}
