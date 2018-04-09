<?php

use yuncms\db\Migration;

/**
 * Handles the creation of table `comment`.
 */
class m180409_031448_create_comment_table extends Migration
{
    /**
     * @var string The table name.
     */
    public $tableName = '{{%comment}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable($this->tableName, [
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
        $this->addForeignKey('comment_fk_1', $this->tableName, 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('comment_fk_2', $this->tableName, 'to_user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('comment_fk_3', $this->tableName, 'parent', $this->tableName, 'id', 'CASCADE', 'CASCADE');
        $this->createIndex('comment_status', $this->tableName, ['status']);
        $this->createIndex('comment_id_model', $this->tableName, ['model_id', 'model_class']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
