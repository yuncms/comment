<?php
use yii\helpers\Url;
use yuncms\helpers\Html;
use yuncms\helpers\HtmlPurifier;
?>
<div class="media">
    <?= $this->render(
        '_item.php', ['model' => $model]
    ) ?>
</div>