<?php

namespace rabint\attachment\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * @property \rabint\attachment\storages\LocalStorage $storageObject
 */
class Attachment extends AttachmentBase
{

    public $fullPath = '';
    private $storageObj = null;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ],
        ];
    }

    public function events()
    {
        return [
            \yii\db\ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            //            if ($insert) {
            if (!file_exists($this->getFullPath())) {
                return false;
            }
            $this->ip = Yii::$app->request->getUserIP(); // @codeCoverageIgnore

            $this->fillMetaInfo();
            //            }
            return true;
        }

        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (parent::afterSave($insert, $changedAttributes)) {
            $this->setStorageObject();
            return true;
        }

        return false;
    }

    private function fillMetaInfo()
    {
        $pathInfo = pathinfo($this->fullPath);
        if ($this->title === null) {
            $this->title = $pathInfo['filename'];
        }

        $this->size = filesize($this->getFullPath());
        $this->mime = FileHelper::getMimeType($this->getFullPath());
        $this->type = \rabint\helpers\file::mimeToType($this->mime);
        if ('N/A' == $this->type) {
            $type = \rabint\helpers\media::getMediaType($this->getFullPath());
            if ($type) {
                $this->type = $type;
            }
        }
        if (empty($this->extension)) {
            $this->extension = \rabint\helpers\file::mimeToExt($this->mime);
        }
        if (empty($this->name)) {
            $this->name = $this->generateName();
        }
    }

    public function generateName()
    {
        //        $name = time() . substr(uniqid(), -5);
        $name = uniqid();
        return $name . '.' . $this->extension;
    }

    public function getTitleTag()
    {
        $latestDot = strrpos($this->title, ".");
        if ($latestDot) {
            return substr($this->title, 0, $latestDot);
        }
        return $this->title;
    }

    //

    /**
     * Checks whether the file is protected
     *
     * @return bool
     */
    public function isProtected()
    {
        return $this->protected == static::PROTECTED_YES ? true : false;
    }

    /**
     * Checks whether the file is unprotected
     *
     * @return bool
     */
    public function isUnprotected()
    {
        return $this->protected == static::PROTECTED_NO ? true : false;
    }

    public function getDateOfFile()
    {
        if ($this->isNewRecord || is_object($this->created_at)) {
            return date('Y-m');
        } else {
            return date('Y-m', $this->created_at);
        }
    }

    public function afterDelete()
    {
        $this->getStorageObject()->delete();
        return true;
    }

    protected function setStorageObject()
    {
        $class = $this->storages()[$this->storage]['class'];
        $this->storageObj = new $class;
        $this->storageObj->setFile($this);
        return $this;
    }

    public function getStorageObject()
    {
        if ($this->storageObj === null) {
            $this->setStorageObject();
        }
        return $this->storageObj;
    }



    public function getUrl($size = '')
    {
        return $this->getDirectUrl($size);

        if (empty($size)) {
            return \rabint\uri::to(['/attachment/default/download', 'ext' => $this->extension, 'id' => $this->id]);
        }
        if (is_array($size)) {
            $size = implode(',', $size);
        }
        return \rabint\uri::to([
            '/attachment/default/download',
            'ext' => $this->extension,
            'id' => $this->id,
            'size' => $size
        ]);
    }

    public function getDirectUrl($size = '')
    {
        if ($this->isUnprotected()) {
            $path = $this->path;
            if (!empty($size)) {
                if (is_array($size)) {
                    $size = \rabint\attachment\attachment::getPresetBySize($this->component, $size);
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
            return $this->storageObject->baseUrl() . FileHelper::normalizePath($path, '/');
        }
        return \Yii::t('rabint', 'FILE IS PROTECTED');
    }

    public function getFullPath()
    {
        return $this->storageObject->uploadDir() . '/' . $this->path;
    }

    /**
     * @return type
     * @deprecated
     */
    public function getBase_url()
    {
        return $this->getBaseUrl();
    }

    public function getBaseUrl()
    {
        if ($this->isUnprotected()) {
            return $this->storageObject->baseUrl() . '/' . Yii::$app->Attachment->publicPath . $this->path . '/' . $this->name;
        }
        return \Yii::t('rabint', 'FILE IS PROTECTED');
    }

    public function incDownloadCount()
    {
        $this->download_count = $this->download_count + 1;
        return $this->save();
    }

    public static function getUploaderFileAttribute($value)
    {
        if($value=='' or $value == null) return null;
        if (is_numeric($value)) {
            
            $file = static::findOne($value);
            if ($file == null) {
                return null;
            }
            return [
                [
                    'base_url' => $file->storageObject->baseUrl(),
                    'delete_url' => \yii\helpers\Url::to(['/attachment/default/delete', 'id' => $file->id]),
                    'path' => $file->path,
                    'name' => $file->name,
                    'size' => $file->size,
                    'type' => $file->mime,
                    'order' => $file->weight,
                    'attachment_id' => $file->id,
                    //          'url' => $file->url,
                ]
            ];
        } else {
            if (is_string($value)) {
                $file = static::findOne(['path' => $value]);
                if ($file == null) {
                    return null;
                }
                return [
                    [
                        'base_url' => $file->storageObject->baseUrl(),
                        'delete_url' => \yii\helpers\Url::to(['/attachment/default/delete', 'id' => $file->id]),
                        'path' => $file->path,
                        'name' => $file->name,
                        'size' => $file->size,
                        'type' => $file->mime,
                        'order' => $file->weight,
                        'attachment_id' => $file->id,
                        //          'url' => $file->url,
                    ]
                ];
            } else {
                if (is_array($value)) {
                    $files = static::find()->where(['id' => $value])->all();
                    $return = [];
                    foreach ($files as $file) {

                        $return[] = [
                            'base_url' => $file->storageObject->baseUrl(),
                            'delete_url' => \yii\helpers\Url::to(['/attachment/default/delete', 'id' => $file->id]),
                            'path' => $file->path,
                            'name' => $file->name,
                            'size' => $file->size,
                            'type' => $file->mime,
                            'order' => $file->weight,
                            'attachment_id' => $file->id,
                            //          'url' => $file->url,
                        ];
                    }
                    return $return;
                } else {
                    return null;
                    throw new \yii\base\ErrorException("value of atachment not valid: " . print_r($value, true));
                }
            }
        }
    }

    public static function findByPath($path)
    {
        if (empty($path)) {
            return null;
        }
        //http://razavitv.aqr.ir/plus-alpha\upload\global\2016-08\266\small_57a2b90c75edd.jpg
        $attachId = basename(dirname($path));
        return static::findOne(['id' => $attachId]);
    }


    public static function getUrlById($id, $size = '', $default = "")
    {
        $model = static::findOne($id);
        if ($model == null) {
            return $default;
        }
        return $model->getUrl($size);
    }

    public static function url($value, $size = '', $default = "")
    {
        if (is_numeric($value)) {
            return static::getUrlById($value, $size, $default);
        }
        return static::getUrlByPath($value, $size, $default);
    }

    public static function getUrlByPath($value, $size = '', $default = "")
    {
        $pos = strpos($value, "download?id=");
        if ($pos > 0) {
            $id = intval(substr($value, $pos + 12));
            $file = static::findOne($id);
        } else {
            $file = static::findOne(['path' => $value]);
        }
        if ($file == null) {
            return $default;
        }
        return $file->getUrl($size);
    }

    public function rename($Name = null, $prefix = null, $sufix = null)
    {
        $newName = '';
        $ext = pathinfo($this->name, PATHINFO_EXTENSION);
        $oldName = pathinfo($this->name, PATHINFO_FILENAME);
        /* ------------------------------------------------------ */
        if (empty($Name)) {
            $newName = $oldName;
        } else {
            $newName = $Name;
        }
        if (!empty($prefix)) {
            $newName = $prefix . $newName;
        }
        if (!empty($sufix)) {
            $newName = $newName . $sufix;
        }
        $newName = $newName . '.' . $ext;
        /* ------------------------------------------------------ */
        return $this->getStorageObject()->rename($newName);
    }

    static function createByPath($fullPath)
    {

        $upload = new \rabint\attachment\models\Tmpupload();
        $file = $upload->createFile('attachment_id', $fullPath, basename($fullPath));

        if ($file == null) {
            return false;
        }
        $presetAfterUpload = $upload->getFilePresetAfterUpload('attachment_id');
        if (count($presetAfterUpload)) {
            foreach ($presetAfterUpload as $preset) {
                $upload->thumb('attachment_id', $preset, $file->storageObject->path());
            }
        }
        return $file->id;
        //        if ($file->type == 'video') {
        //            \rabint\helpers\collection::startObToKeepProcessing();
        //            echo json_encode($this->successOutput($file));
        //            \rabint\helpers\collection::endObAndKeepProcessing();
        //            return \rabint\helpers\media::convertAttachment($file);
        //        }
        //        return $this->successOutput($file);
        ////            }
        //        } else {
        //            return $this->errorOutput(Yii::t('rabint', 'Error saving file'));
        //        }
    }

    static function urlToPath($url)
    {
        $baseUrl = \rabint\uri::home();
        $rel = str_replace($baseUrl, '', $url);
        $urlBase = str_replace($rel, '', $url);
        if ($urlBase != $baseUrl) {
            return false;
        } else {
            return $rel;
        }
        //        echo '<br/>';
    }

    public function mediaData()
    {
        if (empty($this->meta)) {
            $ret = [];
            switch ($this->type) {
                case static::TYPE_AUDIO:
                case static::TYPE_VIDEO:
                    //bitrate , width, height , duration, stream_count
                    $info = \rabint\helpers\media::getMediaInfo($this->getFullPath());
                    if (isset($info['format'])) {
                        $ret['stream_count'] = $info['format']['nb_streams'];
                        $ret['duration'] = (int)$info['format']['duration'];
                        $ret['bit_rate'] = $info['format']['bit_rate'];
                    }
                    if (isset($info['streams'])) {
                        if (isset($info['streams'][0]) && isset($info['streams'][0]['width'])) {
                            $ret['width'] = $info['streams'][0]['width'];
                            $ret['height'] = $info['streams'][0]['height'];
                        } elseif (isset($info['streams'][1]) && isset($info['streams'][1]['width'])) {
                            $ret['width'] = $info['streams'][1]['width'];
                            $ret['height'] = $info['streams'][1]['height'];
                        } elseif (isset($info['streams'][2]) && isset($info['streams'][2]['width'])) {
                            $ret['width'] = $info['streams'][2]['width'];
                            $ret['height'] = $info['streams'][2]['height'];
                        }
                    }
                    break;
                case static::TYPE_IMAGE:
                    $Image = Image::make($this->getFullPath());
                    $ret['height'] = $Image->height();
                    $ret['width'] = $Image->width();
                    //$ret['resolution'] = 72;
                    break;
                default:
                    //docs ... 
                    //
                    break;
            }
            $this->meta = json_encode($ret);
            $this->save();
            return $ret;
        }
        return json_decode($this->meta, true);
    }

    public static function uploadMultipartEntities($modelName, $inputName, $attribute, $return = "id", $type = 'file')
    {
        /**
         * init
         */
        $model = new $modelName;
        $rules = $model->getFileRules($attribute);

        if (isset($rules['imageSize'])) {
            $rules = array_merge($rules, $rules['imageSize']);
            unset($rules['imageSize']);
        }

        /**
         * run
         */
        $files = \yii\web\UploadedFile::getInstancesByName($inputName);
        $results = [];
        foreach ($files as $file) {
            if (!\rabint\helpers\security::checkAllowedUploadedFile($file)) {
                continue;
            }
            $dnModel = new \yii\base\DynamicModel(compact('file'));
            \rabint\helpers\file::mimeToType($file->type);
            $dnModel->addRule('file', $type, $rules)->validate();
            if ($dnModel->hasErrors()) {
                //                var_dump($dnModel->getFirstError('file'));
                continue;
                //
            }
            /**
             * upload
             */
            $attachment = $model->createFile($attribute, $file->tempName, $file->name, $file);
            if ($attachment) {
                $presetAfterUpload = $model->getFilePresetAfterUpload($attribute);
                if (count($presetAfterUpload)) {
                    foreach ($presetAfterUpload as $preset) {
                        $model->thumb($attribute, $preset, $attachment->storageObject->path());
                    }
                }
                //            if (FALSE != config('FFMPEG_BIN_DIR', false)) {
                //                if ($attachment->type == 'video' OR $attachment->type == 'audio') {
                ////                    \rabint\helpers\collection::startObToKeepProcessing();
                ////                    echo json_encode($this->successOutput($attachment));
                ////                    \rabint\helpers\collection::endObAndKeepProcessing();
                //                    \rabint\helpers\media::getAttachmentScenes($attachment, 0.2);
                //                    \rabint\helpers\media::convertAttachment($attachment);
                //                } elseif ($attachment->type == 'image') {
                //                    \rabint\helpers\media::getAttachmentScenes($attachment, 0.2);
                //                }
                //            }
                switch ($return) {
                    case "id":
                        $results[] = $attachment->id;
                        break;
                    case "url":
                        $results[] = $attachment->getUrl();
                        break;
                    case "path":
                        $results[] = $attachment->path;
                        break;

                    default:
                        $results[] = $attachment;
                        break;
                }
            }
        }
        return $results;
    }

    public static function uploadMultipartEntity(
        $modelName,
        $inputName,
        $attribute,
        $return = "id",
        $immediateConvert = false,
        $type = 'file'
    ) {
        /**
         * init
         */
        $model = new $modelName;
        $rules = $model->getFileRules($attribute);

        if (isset($rules['imageSize'])) {
            $rules = array_merge($rules, $rules['imageSize']);
            unset($rules['imageSize']);
        }

        /**
         * run
         */
        if (isset($_FILES[$inputName]['name']) and is_array($_FILES[$inputName]['name'])) {
            $newFiles = \rabint\helpers\collection::rotateArray($_FILES[$inputName]);
            $_FILES[$inputName] = $newFiles[0];
        }
        $file = \yii\web\UploadedFile::getInstanceByName($inputName);
        if (!$file) {
            return null;
        }
        if (!\rabint\helpers\security::checkAllowedUploadedFile($file)) {
            return null;
        }

        $dnModel = new \yii\base\DynamicModel(compact('file'));
        \rabint\helpers\file::mimeToType($file->type);
        $dnModel->addRule('file', $type, $rules)->validate();
        if ($dnModel->hasErrors()) {
            return $dnModel->getFirstError('file');
        }
        /**
         * upload
         */
        $attachment = $model->createFile($attribute, $file->tempName, $file->name, $file);
        if ($attachment) {
            $presetAfterUpload = $model->getFilePresetAfterUpload($attribute);
            if (count($presetAfterUpload)) {
                foreach ($presetAfterUpload as $preset) {
                    $model->thumb($attribute, $preset, $attachment->storageObject->path());
                }
            }
            $outputPath = null;
            if ($immediateConvert and false != config('FFMPEG_BIN_DIR', false)) {
                if ($attachment->type == 'video' or $attachment->type == 'audio') {
                    //                    \rabint\helpers\collection::startObToKeepProcessing();
                    //                    echo json_encode($this->successOutput($attachment));
                    //                    \rabint\helpers\collection::endObAndKeepProcessing();
                    \rabint\helpers\media::getAttachmentScenes($attachment, 0.2);
                    $outputPath = \rabint\helpers\media::convertAttachment($attachment);
                } elseif ($attachment->type == 'image') {
                    $outputPath = \rabint\helpers\media::getAttachmentScenes($attachment, 0.2);
                }
            } else {
                //add to qeuee
            }
            switch ($return) {
                case "id":
                    $result = $attachment->id;
                    break;
                case "url":
                    $result = $attachment->getUrl();
                    break;
                case "path":
                    $result = $attachment->path;
                    break;
                case "scenePath":
                    $result = $outputPath;
                    break;
                default:
                    $result = $attachment;
                    break;
            }
            return $result;
        } else {
            return null;
        }
    }
}
