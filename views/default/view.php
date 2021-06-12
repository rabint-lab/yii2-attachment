<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\Attachment */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
pr(123,1);

?>
<div class="attachment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'component',
            'path',
            'title',
            'name',
            'size',
            'extension',
            'type',
            'mime',
            'created_at',
            'updated_at',
            'ip',
            'weight',
            'protected',
            'meta:ntext',
        ],
    ]) ?>

</div>
