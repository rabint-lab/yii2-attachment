<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\search\attachmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="search_box attachment-search">

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
        ]); ?>
        
        <div class="col-sm-4"><?= $form->field($model, 'id') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'user_id') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'component') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'path') ?></div>

        <div class="col-sm-4"><?= $form->field($model, 'title') ?></div>

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'name') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'size') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'extension') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'type') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'mime') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'created_at') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'updated_at') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'ip') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'weight') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'protected') ?></div>-->

        <!--<div class="col-sm-4"><?php // echo $form->field($model, 'meta') ?></div>-->

        <div class="form-group  center center-block">
            <?= Html::submitButton(Yii::t('rabint', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?php // echo Html::resetButton(Yii::t('rabint', 'Reset'), ['class' => 'btn btn-default']) ?>
            <?= Html::a(Yii::t('rabint', 'Reset'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    
</div>
