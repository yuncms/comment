<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\comment\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yuncms\comment\notifications\CommentNotification;
use yuncms\helpers\ArrayHelper;
use yuncms\helpers\HtmlPurifier;
use yuncms\db\ActiveRecord;
use yuncms\user\models\User;

/**
 * Comment 评论模型
 *
 * @property integer $id 评论ID
 * @property integer $user_id 用户ID
 * @property string $model_class 模型名称
 * @property integer $model_id 模型ID
 * @property string $content 评论内容
 * @property integer|null $to_user_id @某人
 * @property integer $parent 父评论ID
 * @property integer $status 评论状态
 * @property integer $created_at 发表时间
 *
 * @property ActiveRecord $source
 * @property-read boolean $isDraft 是否草稿
 * @property-read boolean $isPublished 是否发布
 * @property-read User $toUser 用户实例
 * @property-read User $user 用户实例
 */
class Comment extends ActiveRecord
{
    //场景定义
    const SCENARIO_CREATE = 'create';//创建
    const SCENARIO_UPDATE = 'update';//更新

    //状态定义
    const STATUS_DRAFT = 0;//草稿
    const STATUS_REVIEW = 1;//审核
    const STATUS_REJECTED = 2;//拒绝
    const STATUS_PUBLISHED = 3;//发布

    //事件定义
    const BEFORE_PUBLISHED = 'beforePublished';
    const AFTER_PUBLISHED = 'afterPublished';
    const BEFORE_REJECTED = 'beforeRejected';
    const AFTER_REJECTED = 'afterRejected';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'content'
                ],
                'value' => function ($event) {
                    return HtmlPurifier::process($event->sender->content);
                }
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id',
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return ArrayHelper::merge($scenarios, [
            static::SCENARIO_CREATE => ['model_class', 'model_id', 'content', 'to_user_id', 'parent'],
            static::SCENARIO_UPDATE => ['model_class', 'model_id', 'content', 'to_user_id'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['model_class', 'model_id', 'content'], 'required'],
            ['model_class', 'string', 'max' => 255],
            [['model_class', 'content'], 'filter', 'filter' => 'trim'],
            ['content', 'validateContent'],
            [['parent'], 'integer'],
            [['parent'], 'exist', 'skipOnError' => true, 'targetClass' => static::class, 'targetAttribute' => ['parent' => 'id']],
            [['to_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['to_user_id' => 'id']],
            ['status', 'default', 'value' => self::STATUS_DRAFT],
            ['status', 'in', 'range' => [
                self::STATUS_DRAFT, self::STATUS_REVIEW, self::STATUS_REJECTED, self::STATUS_PUBLISHED,
            ]]
        ];
    }

    /**
     * 验证评论内容
     *
     * @param string $attribute 目前正在验证的属性
     * @param array $params 规则中给出的附加名称值对
     */
    public function validateContent($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $model = static::findOne(['user_id' => $this->user_id]);
            if ($model) {
                //一分钟内多次提交
                if ((time() - $model->created_at) < 60) {
                    $this->addError($attribute, Yii::t('yuncms/comment', 'One minute only comment once.'));
                }
                //计算相似度
                $similar = similar_text($model->content, $this->content);
                if ($similar > 50) {
                    $this->addError($attribute, Yii::t('yuncms/comment', 'You can not submit the same comment.'));
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content' => Yii::t('yuncms/comment', 'Content'),
            'parent' => Yii::t('yuncms/comment', 'Parent Content'),
            'model_class' => Yii::t('yuncms/comment', 'Model Class'),
            'model_id' => Yii::t('yuncms/comment', 'Model Id'),
            'status' => Yii::t('yuncms/comment', 'Status'),
            'created_at' => Yii::t('yuncms/comment', 'Created At'),
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

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%comment}}';
    }

    /**
     * @inheritdoc
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }

    /**
     * 是否草稿状态
     * @return bool
     */
    public function getIsDraft()
    {
        return $this->status == static::STATUS_DRAFT;
    }

    /**
     * 是否发布状态
     * @return bool
     */
    public function getIsPublished()
    {
        return $this->status == static::STATUS_PUBLISHED;
    }

    /**
     * 审核通过
     * @return int
     */
    public function setPublished()
    {
        $this->trigger(self::BEFORE_PUBLISHED);
        $rows = $this->updateAttributes(['status' => static::STATUS_PUBLISHED]);
        $this->trigger(self::AFTER_PUBLISHED);
        return $rows;
    }

    /**
     * 拒绝通过
     * @param string $failedReason 拒绝原因
     * @return int
     */
    public function setRejected($failedReason)
    {
        $this->trigger(self::BEFORE_REJECTED);
        $rows = $this->updateAttributes(['status' => static::STATUS_REJECTED]);
        $this->trigger(self::AFTER_REJECTED);
        return $rows;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne($this->model_class, ['id' => 'model_id']);
    }

    /**
     * 获取源标题
     * @return string
     */
    public function getSourceTitle()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $this->source->updateCountersAsync(['comments' => -1]);
        parent::afterDelete();
    }

    /**
     * 保存后执行
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->source->updateCountersAsync(['comments' => 1]);
            try {
                Yii::$app->notification->send($this->source->user, new CommentNotification([
                    'data' => [
                        'username' => $this->user->nickname,
                        'entity' => $this->getSourceTitle(),
                        'source' => $this->source->toArray(),
                        'target' => $this->toArray()
                    ]
                ]));
            } catch (InvalidConfigException $e) {
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
