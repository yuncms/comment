<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace yuncms\comment\frontend\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Url;
use yii\base\InvalidConfigException;
use yuncms\comment\frontend\models\CommentForm;

/**
 * Class Comment
 * @package yuncms\comment\widgets
 */
class Comment extends Widget
{
    /**
     * @var string 内容类型
     */
    public $model_class;

    /**
     * @var int 关系ID
     */
    public $model_id;

    /**
     * @var bool 隐藏取消
     */
    public $hide_cancel = false;

    /** @var bool */
    public $validate = true;

    /** @inheritdoc */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
        if (empty ($this->model_class)) {
            throw new InvalidConfigException ('The "model_class" property must be set.');
        }
        if (empty ($this->model_id)) {
            throw new InvalidConfigException ('The "model_id" property must be set.');
        }
        Url::remember('', 'actions-redirect');
    }

    /**
     * 注册翻译
     */
    public function registerTranslations()
    {
        if (!isset(Yii::$app->i18n->translations['widgets/comment/*'])) {
            Yii::$app->i18n->translations['widgets/comment/*'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'sourceLanguage' => 'en-US',
                'basePath' => __DIR__ . '/messages',
                'fileMap' => [
                    'widgets/comment/comment' => 'comment.php',
                ],
            ];
        }
    }

    /**
     * 显示语言包
     * @param string $category
     * @param string $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('widgets/comment/' . $category, $message, $params, $language);
    }

    /** @inheritdoc */
    public function run()
    {
        $model = new CommentForm([
            'model_class' => $this->model_class,
            'model_id' => $this->model_id
        ]);
        return $this->render('comment', [
            'model' => $model,
            'model_class' => $this->model_class,
            'model_id' => $this->model_id,
            'hide_cancel' => $this->hide_cancel,
        ]);
    }
}