<?php
use yii\helpers\Url;
use yuncms\comment\frontend\widgets\Comment;
use yuncms\comment\frontend\widgets\assets\CommentAsset;

CommentAsset::register($this);
/** @var \yii\web\View $this */
?>
<div class="collapse widget-comments mb-20" id="comments-<?= $model_class ?>-<?= $model_id ?>"
     data-source_type="<?= $model_class ?>" data-model_id="<?= $model_id ?>">
    <div class="widget-comment-list">
    </div>
    <?php if (!Yii::$app->user->isGuest): ?>
        <div class="widget-comment-form row">
            <form class="col-md-12">
                <div class="form-group">
                    <textarea name="content" placeholder="<?= Comment::t('comment', 'Write your comment') ?>"
                              class="form-control"
                              id="comment-<?= $model_class ?>-content-<?= $model_id ?>"></textarea>
                </div>
            </form>
            <div class="col-md-12 text-right">
                <?php if ($hide_cancel): ?>
                    <a href="#" class="text-muted collapse-cancel"
                       data-collapse_id="comments-<?= $model_class ?>-<?= $model_id ?>"><?= Comment::t('comment', 'Clean') ?></a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-sm ml-10 comment-btn"
                        id="<?= $model_class ?>-comment-6-btn"
                        data-model_id="<?= $model_id ?>"
                        data-model_class="<?= $model_class ?>"
                        data-to_user_id="0"
                ><?= Comment::t('comment', 'Submit Comment') ?></button>
            </div>
        </div>
    <?php else: ?>
        <div class="widget-comment-form row">
            <div class="col-md-12">
                请先 <a href="<?= Url::to(['/user/security/login']) ?>">登录</a> 后评论
            </div>
        </div>
    <?php endif ?>
</div>
