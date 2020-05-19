<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model rabint\attachment\models\Attachment */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Attachments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="view-box attachment-view">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card block block-rounded">
                <div class="box-header">
                    <div class="action-box">
                        <h2 class="master-title">
                            <?= Html::encode($this->title) ?>
                            <?= Html::a(Yii::t('rabint', 'Create Attachment'), ['create'], ['class' => 'btn btn-success btn-xs btn-flat']) ?>
                            <?= Html::a(Yii::t('rabint', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs btn-flat']) ?>
                            <?= Html::a(Yii::t('rabint', 'Delete'), ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger btn-xs btn-flat',
                            'data' => [
                            'confirm' => Yii::t('rabint', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                            ],
                            ]) ?>
                        </h2>
                    </div>
                </div>
                <div class="card-body block-content block-content-full">

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
            </div>
        </div>
    </div>
</div>
