<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\comment\rest\models;


use yii\db\ActiveQuery;
use yuncms\rest\models\User;

class Comment extends \yuncms\comment\models\Comment
{

    public function fields()
    {
        return [
            'id',
            'user',
            'toUser',
            'source',
            'parent',
            'content',
            'status',
            'created_at'
        ];
    }


    /**
     * 关联用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * at 某人
     * @return ActiveQuery
     */
    public function getToUser()
    {
        return $this->hasOne(User::class, ['id' => 'to_user_id']);
    }


}