<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rabint\components\grid\AttachmentColumn;

/* @var $this yii\web\View */
/* @var $searchModel rabint\attachment\models\search\attachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rabint', 'Attachments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-box attachment-index">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card block block-rounded">
                <?= Html::beginForm(['bulk'], 'post'); ?>
                <div class="box-header">
                    <div class="box-tools pull-right float-right">
                        <div class="input-group input-group-sm" style="width: 350px;">
                            <span class="input-group-addon bg-gray"><?= \Yii::t('rabint', 'عملیات گروهی'); ?></span>
                            <?= Html::dropDownList('action', '', ['remove' => \Yii::t('rabint', 'حذف')], ['class' => 'form-control', 'prompt' => '']); ?>
                            <div class="input-group-btn">
                                <?= Html::submitButton(\Yii::t('rabint', 'اعمال'), ['class' => 'btn btn-info',]); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body block-content block-content-full">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'layout' => "<div class=\"pull-left float-left\">{summary}</div>\n{items}\n{pager}",
                        'columns' => [
                                ['class' => 'yii\grid\CheckboxColumn'],
                                [
                                'class' => AttachmentColumn::className(),
                                'attribute' => 'id',
                                'label' => '',
                            ],
                            'id',
                                [
                                'attribute' => 'user_id',
                                'value' => function ($model) {
                                    return ($model->user) ? $model->user->displayName : '';
                                }
                            ],
//                            'component',
                            'path',
                            'title',
                                [
                                'attribute' => 'size',
                                'value' => function ($model) {
                                    return \rabint\helpers\str::sizeToText($model->size);
                                }
                            ],
                                [
                                'class' => \rabint\components\grid\EnumColumn::className(),
                                'attribute' => 'type',
                                'enum' => \yii\helpers\ArrayHelper::getColumn(rabint\attachment\models\Attachment ::types(), 'title')
                            ],
                                [
                                'class' => \rabint\components\grid\JDateColumn::className(),
                                'attribute' => 'created_at',
                            ],
//                             'name',
//                             'extension',
                            // 'mime',
                            // 'updated_at',
                            // 'ip',
                            // 'weight',
                            // 'protected',
                            // 'meta:ntext',
                            ['class' => 'yii\grid\ActionColumn',
                                'template' => '{delete} {download} {regenerate}',
                                'buttons' => [
                                    'download' => function ($url, $model) {
                                        $url = $model->getUrl();
                                        return Html::a('<span class="fas fa-download"></span>', $url, [
                                                    'title' => Yii::t('rabint', 'دانلود'), 'target' => '_BLANK']);
                                    },
                                    'regenerate' => function ($url, $model) {
                                        return Html::a('<span class="fas fa-sync-alt"></span>', $url, [
                                                    'title' => Yii::t('rabint', 'تبدیل دوباره'), 'target' => '_BLANK']);
                                    },
                                ],
                            ],
                        ],
                    ]);
                    ?>
                </div>
                <?= Html::endForm(); ?> 
            </div>
        </div>
    </div>
</div>

