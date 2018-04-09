<?php

namespace yuncms\comment\migrations;

use yii\db\Migration;

class M171114041136Create_comment_table extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'user_id' => $this->integer()->unsigned()->notNull()->comment('User Id'),
            'to_user_id' => $this->integer()->unsigned()->comment('To User Id'),
            'model_id' => $this->integer()->notNull()->comment('Model ID'),
            'model_class' => $this->string(100)->notNull()->comment('Model Class'),
            'parent' => $this->integer()->unsigned()->comment('Parent'),
            'content' => $this->text()->notNull()->comment('Content'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(0)->comment('Status'),
            'created_at' => $this->integer()->notNull()->unsigned()->comment('Created At'),
        ], $tableOptions);
        $this->addForeignKey('comment_fk_1', '{{%comment}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('comment_fk_2', '{{%comment}}', 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('comment_fk_3', '{{%comment}}', 'parent', '{{%comment}}', 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('comment_status', '{{%comment}}', ['status']);
        $this->createIndex('comment_id_model', '{{%comment}}', ['model_id', 'model_class']);
    }

    public function safeDown()
    {
        $this->dropTable('{{%comment}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M171114041136Create_comment_table cannot be reverted.\n";

        return false;
    }
    */
}
