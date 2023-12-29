<?php

namespace rabint\attachment\controllers;

use Yii;
use rabint\attachment\models\Attachment;
use rabint\attachment\models\search\attachmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdminController implements the CRUD actions for Attachment model.
 */
class AdminController extends \rabint\controllers\AdminController {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return parent::behaviors() + [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionBulk() {
        $action = Yii::$app->request->post('action');
        $selection = (array) Yii::$app->request->post('selection');
        switch ($action) {
            case 'remove':
                foreach ($selection as $attach) {
                    $atModel = Attachment::find()->where(['id' => $attach])->one();
                    if (NULL == $atModel) {
                        Yii::$app->session->setFlash('danger', \Yii::t('rabint', 'عملیات ناموفق بود'));
                        return $this->redirect(\yii\helpers\Url::previous());
                    }
                    $atModel->delete();
                }
                Yii::$app->session->setFlash('success', \Yii::t('rabint', 'عملیات با موفقیت انجام شد.'));
                return $this->redirect(\yii\helpers\Url::previous());
                break;

            case 'regenerate':
                foreach ($selection as $attach_id) {
                    \rabint\helpers\file::regenerateAttachment($attach_id);
                }
                Yii::$app->session->setFlash('success', \Yii::t('rabint', 'عملیات با موفقیت انجام شد.'));
                return $this->redirect(\yii\helpers\Url::previous());
                break;

            default:
                break;
        }
        Yii::$app->session->setFlash('danger', \Yii::t('rabint', 'عملیات ناموفق بود'));
        return $this->redirect(\yii\helpers\Url::previous());
    }

    /**
     * Lists all Attachment models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new attachmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        \yii\helpers\Url::remember();
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Attachment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    public function actionRegenerate($id) {
        ignore_user_abort(TRUE);
        set_time_limit(0);
        $time_start = microtime(true);
        /* ################################################################### */
        $output = \rabint\helpers\file::regenerateAttachment($id);
        /* ################################################################### */
        $time_end = microtime(true);
        $execution_time = round($time_end - $time_start, 2);
        $output .= "\n\r".'end Work of ' . $id . " in " . $execution_time . " Sec";
        echo '<pre>' . $output . '</pre>';
        die('');
    }

    /**
     * Creates a new Attachment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Attachment();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Attachment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Attachment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Attachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Attachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Attachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
