<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel rabint\attachment\models\search\attachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rabint', 'Attachments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="list_box attachment-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <div class="row">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(); ?>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            /*
             'id',  'user_id',  'component',  'path',  'title',  'name',  'size',  'extension',  'type',  'mime',  'created_at',  'updated_at',  'ip',  'weight',  'protected',  'meta:ntext', 
            */        
            ob_start(); ?>
        
            <div class="col-sm-12">
                <h4 class="title">
                    <?= Html::a(Html::encode($model->title), ['view', 'id' => $model->id]);?>
                </h4>
            </div>
            
            <?php  return ob_get_clean();
        },
    ]) ?>

    <?php Pjax::end(); ?>    
    </div>
</div>
