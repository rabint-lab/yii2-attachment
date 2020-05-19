<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\Attachment */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="clearfix"></div>
<div class="form-box attachment-form">
    <div class="row">
        <div class="col-sm-8">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card block block-rounded">
                        <div class="card-header block-header">
                            <h3 class="block-title"><?= Html::encode($this->title) ?></h3>
                            <div class="box-tools pull-right float-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>

                        <div class="card-body block-content block-content-full">
                            
                            <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'component')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'path')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'size')->textInput() ?>

                            <?= $form->field($model, 'extension')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'mime')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'created_at')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'updated_at')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'ip')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'weight')->textInput() ?>

                            <?= $form->field($model, 'protected')->textInput() ?>

                            <?= $form->field($model, 'meta')->textarea(['rows' => 6]) ?>

                        </div>
                    </div>
                </div>
                <!-- =================================================================== -->
                <?php  if (FALSE AND !$model->isNewRecord) {  ?>
                <div class="col-sm-12">
                    <div class="card block block-rounded">
                        <div class="card-header block-header">
                            <h3 class="block-title"><?= Yii::t('rabint', 'Title') ?></h3>
                            <div class="box-tools pull-right float-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body block-content block-content-full">
                            ...
                        </div>
                    </div>
                </div>
                <?php   }  ?>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="row">
                <!-- =================================================================== -->
                <div class="col-sm-12">
                    <div class="box box-success">
                        <div class="card-header block-header">
                            <h3 class="block-title"><?= Yii::t('rabint', 'Publish') ?></h3>
                            <div class="box-tools pull-right float-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body block-content block-content-full">
                            <?php   //echo  $form->field($model, 'published_at')->widget('trntv\yii\datetimepicker\DatetimepickerWidget') ?>
                            <?php   //echo  $form->field($model, 'status')->checkbox() ?>
                        </div>
                        <div class="card-footer block-content block-content-full bg-gray-light">
                            <div class="pull-left float-left">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('rabint', 'Create') : Yii::t('rabint', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat']) ?>
                            </div>
                        </div><!-- /.box-footer-->
                    </div>
                </div>
                <!-- =================================================================== -->
                <?php  if (FALSE AND !$model->isNewRecord) {  ?>
                <div class="col-sm-12">
                    <div class="box box-warning box-solid">
                        <div class="card-header block-header">
                            <h3 class="block-title"><?= Yii::t('rabint', 'Stat') ?></h3>
                            <div class="box-tools pull-right float-right">
                                <button class="btn btn-box-tool" data-widget="collapse"><i class="fas fa-minus"></i></button>
                                <button class="btn btn-box-tool" data-widget="remove"><i class="fas fa-times"></i></button>
                            </div><!-- /.box-tools -->
                        </div><!-- /.box-header -->
                        <div class="card-body block-content block-content-full no-padding">
                            <ul class="nav nav-stacked">
                                <li>
                                    <a href="#">
                                        <?= Yii::t('rabint', 'visit count') ?>
                                        <span class="pull-left float-left badge bg-blue">0</span>
                                    </a>
                                </li>
                            </ul>
                        </div><!-- /.block-content block-content-full -->
                    </div><!-- /.box -->
                </div>
                <?php   }  ?>
                <!-- =================================================================== -->

            </div>
        </div>

    </div>
</div>

<?php ActiveForm::end(); ?>