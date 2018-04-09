<?php

use yii\db\Query;
use yuncms\db\Migration;

/**
 * Class m180409_031519_add_backend_menu
 */
class m180409_031519_add_backend_menu extends Migration
{

    public function safeUp()
    {
        $this->insert('{{%admin_menu}}', [
            'name' => '评论管理',
            'parent' => 8,
            'route' => '/comment/comment/index',
            'icon' => 'fa-file-text-o',
            'sort' => NULL,
            'data' => NULL
        ]);

        $id = (new Query())->select(['id'])->from('{{%admin_menu}}')->where(['name' => '评论管理', 'parent' => 8,])->scalar($this->getDb());
        $this->batchInsert('{{%admin_menu}}', ['name', 'parent', 'route', 'visible', 'sort'], [
            ['评论审核', $id, '/comment/comment/audit', 0, NULL],
        ]);
    }

    public function safeDown()
    {
        $id = (new Query())->select(['id'])->from('{{%admin_menu}}')->where(['name' => '评论管理', 'parent' => 8,])->scalar($this->getDb());
        $this->delete('{{%admin_menu}}', ['parent' => $id]);
        $this->delete('{{%admin_menu}}', ['id' => $id]);
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180409_031519_add_backend_menu cannot be reverted.\n";

        return false;
    }
    */
}
