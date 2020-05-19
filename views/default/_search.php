<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\search\attachmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
'action' => ['index'],
'method' => 'get',
]); ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Yii::t('rabint', 'Search') ?></h3>
    </div>
    <div class="panel-body">

        <div class="search_box attachment-search">

            <div class="row">

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


            </div>

        </div>
    </div>
    <div class="panel-footer">
        <?php // echo Html::resetButton(Yii::t('rabint', 'Reset'), ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::a(Yii::t('rabint', 'Reset'), ['index'], ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::submitButton(Yii::t('rabint', 'Search'), ['class' => 'btn btn-primary pull-left']) ?>
        <div class="clearfix"></div>
    </div>
</div>
<?php ActiveForm::end(); ?>