<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\Attachment */

$this->title = Yii::t('rabint', 'Update {modelClass}: ', [
    'modelClass' =>  Yii::t('rabint', 'Attachment'),
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rabint', 'Update');
?>
<div class="create-box attachment-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
