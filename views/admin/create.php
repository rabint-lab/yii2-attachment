<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\Attachment */

$this->title = Yii::t('rabint', 'Create Attachment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="create-box attachment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
