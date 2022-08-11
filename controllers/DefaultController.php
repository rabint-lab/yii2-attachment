<?php

namespace rabint\attachment\controllers;

use Yii;
use rabint\attachment\models\Attachment;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * DefaultController implements the CRUD actions for Attachment model.
 */
class DefaultController extends \rabint\controllers\DefaultController
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'file-upload' => ['post'],
                    'wysiwyg-upload' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'file-upload' => [
                'class' => 'rabint\attachment\actions\UploadAction',
                'modelName' => 'rabint\attachment\models\Tmpupload',
                'attribute' => 'attachment_id',
                // the type of the file (`image` or `file`)
                'type' => 'file',
            ],
            'file-upload-protected' => [
                'class' => 'rabint\attachment\actions\UploadAction',
                'modelName' => 'rabint\attachment\models\TmpuploadProtected',
                'attribute' => 'attachment_id',
                // the type of the file (`image` or `file`)
                'type' => 'file',
            ],
            'wysiwyg-upload' => [
                'class' => 'rabint\attachment\actions\UploadRedactorAction',
                'modelName' => 'rabint\attachment\models\Tmpupload',
                'attribute' => 'attachment_id',
                'inputName' => 'file',
                // the type of the file (`image` or `file`)
                'type' => 'file',
            ],
        ];
    }

    /**
     * Lists all Attachment models.
     * @return mixed
     */
//    public function actionIndex() {
//        $searchModel = new attachmentSearch();
//        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
//
//        return $this->render('index', [
//                    'searchModel' => $searchModel,
//                    'dataProvider' => $dataProvider,
//        ]);
//    }

    /**
     * This action has security problem!
     * please limit it by basePath AND ext and mime
     * /
     * public function actionPathToUrl($path = '') {
     * die('security exception');
     * if(!file_exists($path)){
     * throw new NotFoundHttpException('file not exist');
     * }
     *
     * $title = pathinfo($path,PATHINFO_BASENAME);
     * $ext = pathinfo($path,PATHINFO_EXTENSION);
     * $mime = \rabint\helpers\file::extToMime($ext);
     * header('Content-Disposition: attachment;filename=' . $title);
     * header("Content-Type: " . $mime);
     * readfile($path);
     * exit();
     * }
     */
    /**
     * Displays a single Attachment model.
     * @param string $id
     * @return mixed
     */
    public function actionView($path = '')
    {
        if (empty($path)) {
            throw new \yii\web\NotFoundHttpException();
        }
        return $this->actionDownload(['path' => $path]);
    }

    public function actionDownload($id, $size = '')
    {
        $model = $this->findModel($id);
        if ($model == null) {
            throw new \yii\web\NotFoundHttpException();
        }
        /**
         * find path of file
         */
        if ($model->isProtected()) {
            if (\rabint\helpers\user::isGuest()) {
                throw new \yii\web\ForbiddenHttpException();
            }
            if ((\rabint\helpers\user::id() != $model->user_id) && (!\rabint\helpers\user::can('administrator'))) {
                throw new \yii\web\ForbiddenHttpException();
            }
        }
        $path = $model->path;
        if (!empty($size)) {
            if (strpos($size, ',') > 0) {
                $size = explode(',', $size);
            }
            if (is_array($size)) {
                $size = \rabint\attachment\attachment::getPresetBySize($model->component, $size);
                if (!$size) {
                    //  if not find ... create on the fly and use it ...
                    $size = '';
                }
            }
            if (!empty($size)) {
                $pos = strrpos($path, '/');
                $path = substr_replace($path, $size . '_', $pos + 1, 0);
            }
        }
        /**
         * check read file or redirect
         */
        header("HTTP/1.1 200 OK");

        if (\rabint\helpers\user::isGuest()) {
            header('Content-Type: application/octet-stream');
            //        header("Content-Type: " . $model->mime);
            header('Content-Disposition: attachment;filename=' . $model->title);
            redirect($model->storageObject->baseUrl() . \yii\helpers\FileHelper::normalizePath($path, '/'));
            exit();
        }

        if ($model->storageObject instanceof \rabint\attachment\storages\LocalStorage) {
            $fullPath = $model->storageObject->uploadDir() . $path;
            /**
             * readfile or xsendfile
             */
//            $modules = apache_get_modules();
//            var_dump($modules);
//            die('--');
            if (false and in_array("mod_xsendfile", $modules)) {
                /**
                 * xsendfile====================================================
                 * need add in htaccess
                 * <IfModule mod_xsendfile.c>
                 * XSendFile On
                 * #XSendFilePath /path/to/files/directory
                 * #XSendFileAllowAbove On
                 * </IfModule>
                 */
                header('Content-Disposition: attachment;filename=' . $model->title);
                header("Content-Type: " . $model->mime);
                header('X-Sendfile: ' . $fullPath);
//                flush();
//                exit();
            } else {
                $mimetype = "mime/type";
                header("Content-Type: " . $model->mime);
                echo readfile($fullPath);
//                \rabint\helpers\file::readfileChunked($filename);
                exit();
            }
        }

    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (false and \rabint\helpers\user::id() == $model->user_id or \rabint\helpers\user::can('administrator')) {
            //todo: check file not used.
            if ($model->delete()) {
                die('1');
            } else {
                die('-1');
            }
        }
        die('0');
    }

//    public function actionDownload($id) {
//        $model = $this->findModel($id);
//        $file = $model->path;
//        if (file_exists($file)) {
//            Yii::$app->response->sendFile($file);
//         }
//    }

    /**
     * Finds the Attachment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Attachment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Attachment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionScenes($id)
    {
        $model = $this->findModel($id);
        $sceneDir = dirname($model->getFullPath()) . '/scene';
        $scenUrl = dirname($model->getDirectUrl()) . '/scene';
        $scenes = scandir($sceneDir);
        unset($scenes[0]);
        unset($scenes[1]);
        $sceRes = [];
        foreach ($scenes as $sce) {
            $sceRes[] = $scenUrl . '/' . $sce;
        }
        header('Content-Type: application/json');
        echo json_encode($sceRes);
        die('');
    }

    public function actionRegenerate()
    {
        die('Hard Locked!');
        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);
        $res = Yii::$app->keyStorage->get('Attachment.RegenerateStatus');
        $output = '';
        //$logPath = Yii::getAlias('@app/runtime/logs/attachment-regenerate.log');
        $logPath = Yii::getAlias('@app/web/attachment-regenerate.log');
        /* ------------------------------------------------------ */
        if ($res === NULL) {
            Yii::$app->keyStorage->set('Attachment.RegenerateStatus', 'stop');
            $output .= 'Attachment.RegenerateStatus is `stop`. please change it to `start`' . "-------------------\n\r\n\r";
            file_put_contents($logPath, $output, FILE_APPEND);
            die('Attachment.RegenerateStatus is `stop`. please change it to `start`');
            /* ------------------------------------------------------ */
        } elseif ($res === 'stop') {
            $output .= 'Attachment.RegenerateStatus is `stop`. please change it to `start`' . "-------------------\n\r\n\r";
            file_put_contents($logPath, $output, FILE_APPEND);
            die('Attachment.RegenerateStatus is `stop`. please change it to `start`');
            /* ------------------------------------------------------ */
        } elseif ($res === 'end') {
            $output .= 'Attachment.RegenerateStatus is `ended`' . "-------------------\n\r\n\r";
            file_put_contents($logPath, $output, FILE_APPEND);
            die('Attachment.RegenerateStatus is `ended`.');
            /* ------------------------------------------------------ */
        } elseif ($res === 'start') {
            Yii::$app->keyStorage->set('Attachment.RegenerateStatus', 0);
            $res = 0;
            $output .= "OK:" . 'regeneration started' . "\n\r";
            /* ------------------------------------------------------ */
        }

        if (is_numeric($res)) {
            ignore_user_abort(TRUE);
            set_time_limit(0);
            $time_start = microtime(true);
            /* ################################################################### */
            $attachments = Attachment::find()
                ->where(['>', 'id', $res])
                ->limit(100)
                ->orderBy(['id' => SORT_ASC])
                ->all();
            if (empty($attachments)) {
                Yii::$app->keyStorage->set('Attachment.RegenerateStatus', 'end');
                $output .= 'Attachment Regeneration ended at:' . date('Y-m-d H:i:s') . "\n\r";
                file_put_contents($logPath, $output, FILE_APPEND);
                die('Attachment Regeneration ended at:' . date('Y-m-d H:i:s'));
            }
            foreach ($attachments as $attach) {
                /* =================================================================== */
                /* =================================================================== */
                $publicPath = $attach->getFullPath();
                if (!file_exists($publicPath)) {
                    $output .= "WRN:" . 'file not exist:' . $attach->id . "\n\r";
                    continue;
                }
                switch ($attach->type) {
                    case Attachment::TYPE_IMAGE:
                        /* =================================================================== */
                        \rabint\attachment\attachment::removeOtherSize($attach);
                        $precents = \rabint\attachment\attachment::imgPresetsFn($attach->component);
                        foreach ($precents as $preset => $presetFn) {
                            $realPath = dirname($publicPath);
                            $fileName = basename($publicPath);
//                            $attach->thumb($this->attribute, $preset, $path);
//                            thumb($attribute, $preset, $pathToFile = null, $returnRealPath = false) {
                            $thumbPath = \rabint\attachment\behaviors\AttechmentBehavior::generateThumbName($fileName, $preset);
                            $res = $presetFn($realPath, $fileName, $thumbPath);
                        }
                        $output .= 'start:' . 'generateThumbnailSize:' . $attach->id . " - ";
                        file_put_contents($logPath, $output, FILE_APPEND);
                        $output = '';
                        /* =================================================================== */
                        break;
                    case Attachment::TYPE_AUDIO:
                    case Attachment::TYPE_VIDEO:
                        /* =================================================================== */
                        $output .= 'start :' . 'convertAttachment:' . $attach->id . " - ";
                        file_put_contents($logPath, $output, FILE_APPEND);
                        $output = '';
                        \rabint\helpers\media::convertAttachment($attach);
                        /* =================================================================== */
                        break;
                    default :
                        $output .= 'OK:type of ' . $attach->id . ':' . $attach->type . " - ";
                }
                /* =================================================================== */
                $time_end = microtime(true);
                $execution_time = intval($time_end - $time_start);
                Yii::$app->keyStorage->set('Attachment.RegenerateStatus', $attach->id);
                $output .= 'note:end Work of ' . $attach->id . " in " . $execution_time . " Sec \n\r";
                file_put_contents($logPath, $output, FILE_APPEND);
                $output = '';
                if ($execution_time >= 60) {
                    $output .= 'execution_time:' . $execution_time . "Sec, at:" . date('Y-m-d H:i:s') . "----------------\n\r\n\r";
                    file_put_contents($logPath, $output, FILE_APPEND);
                    die('execution_time: ' . $execution_time . ', latest_id: ' . $attach->id);
                }
            }
            $output .= 'execution_time:' . $execution_time . "Sec, at:" . date('Y-m-d H:i:s') . "----------------\n\r\n\r";
            file_put_contents($logPath, $output, FILE_APPEND);
            die('execution_time: ' . $execution_time . 'Sec, at:' . date('Y-m-d H:i:s') . ', latest_id: ' . $attach->id);
            /* ################################################################### */
        }
        $output .= "ـــــــــــــــــــــــENDـــــــــــــــــــــ\n\r\n\r";
        file_put_contents($logPath, $output, FILE_APPEND);
        die('_____________end_____________');
    }

}
