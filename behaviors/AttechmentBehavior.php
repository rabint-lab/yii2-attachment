<?php

namespace rabint\attachment\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\base\InvalidParamException;
use rabint\attachment\models\Attachment;
use rabint\attachment\helpers\FormatValidation;
use rabint\attachment\behaviors\FileBind;

class AttechmentBehavior extends Behavior {

    /**
     * @var array
     */
    public $attributes = [];

    /**
     * @var rabint\attachment\behaviors\FileBind
     */
    private static $bind;

    /**
     * @internal
     */
    public function init() {
        parent::init();
        Yii::$app->Attachment->registerTranslations();
    }

    /**
     * @internal
     * @return void
     */

    /**
     * @inheritdoc
     * @internal
     */
    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    /**
     * @internal
     */
    public function beforeSave($insert) {
//        foreach ($this->attributes as $attribute => $data) {
//            $oldValue = $this->owner->isNewRecord ? null : $this->owner->getOldAttribute($attribute);
//            $isAttributeChanged = $oldValue === null ? true : $this->owner->isAttributeChanged($attribute);
//
//            $this->attributes[$attribute]['isAttributeChanged'] = $isAttributeChanged;
//            $this->attributes[$attribute]['oldValue'] = $oldValue;
//        }
    }

    /**
     * @internal
     */
    public function afterSave() {
//        foreach ($this->attributes as $attribute => $data) {
//            $fileId = $this->owner->{$attribute};
//
//            if ($data['isAttributeChanged'] === false || $fileId === null) {
//                continue;
//            }
//
//            $storage = $this->getFileStorage($attribute);
//            $ownerId = $this->owner->primaryKey;
//            $ownerType = $this->getFileOwnerType($attribute);
//
//            if ($fileId === [] || $fileId === '') {
//                (new Attachment())->deleteByOwner($storage, $ownerId, $ownerType);
//                continue;
//            }
//        }
    }

    /**
     * @internal
     */
    public function beforeDelete() {
//        foreach ($this->attributes as $attribute => $data) {
//            $ownerType = $this->getFileOwnerType($attribute);
//            $storage = $this->getFileStorage($attribute);
//            (new Attachment())->deleteByOwner($storage, $this->owner->primaryKey, $ownerType);
//        }
    }

    /**
     * Prepare the path of the file
     *
     * @param mixed $file
     * @param mixed $oldValue
     * @return string
     */
    private function prepareFilePath($file, $oldValue) {
        if (is_object($file)) {
            return $file->storageObject->path();
        } elseif ($file === false && $oldValue !== null) {
            return $oldValue;
        }

        return '';
    }

    /**
     * Prepare the id of the file
     *
     * @param mixed $file
     * @param mixed $oldValue
     * @return int
     */
    private function prepareFileId($file, $oldValue) {
        if (is_object($file)) {
            return $file->id;
        } elseif ($file === false && $oldValue !== null) {
            return $oldValue;
        }

        return 0;
    }

    /**
     * Get the path to the upload directory
     *
     * @param string $attribute Attribute of a model
     * @return string
     */
    public function uploadDir($attribute) {
        if ($this->isFileProtected($attribute)) {
            return Yii::getAlias(Yii::$app->Attachment->uploadDirProtected);
        } else {
            return Yii::getAlias(Yii::$app->Attachment->uploadDirUnprotected);
        }
    }

    /**
     * Get files
     *
     * @param string $attribute Attribute of a model
     * @return array
     */
    public function getFiles($attribute) {
        $files = File::findAllByOwner($this->owner->primaryKey, $this->getFileOwnerType($attribute));
        foreach ($files as $file) {
            $file->setStorage($this->getFileStorage($attribute));
        }

        return $files;
    }

    /**
     * Get the file
     *
     * @param string $attribute Attribute of a model
     * @return File|null
     */
    public function getFile($attribute) {
        $file = File::findOneByOwner($this->owner->primaryKey, $this->getFileOwnerType($attribute));
        $file->setStorage($this->getFileStorage($attribute));
        return $file;
    }

    /**
     * Check whether the upload of multiple files
     *
     * @param string $attribute Attribute of a model
     * @return bool
     */
    public function isMultiple($attribute) {
        return ArrayHelper::getValue($this->attributes[$attribute], 'multiple', false);
    }

    /**
     * Checks whether the file is protected
     *
     * @param string $attribute Attribute of a model
     * @return bool
     */
    public function isFileProtected($attribute) {
        return ArrayHelper::getValue($this->attributes[$attribute], 'protected', false);
    }

    public function getOwnerComponent($attribute) {
        return ArrayHelper::getValue($this->attributes[$attribute], 'component', 'global');
    }

    /**
     * Get rules
     *
     * @param string $attribute Attribute of a model
     * @return array
     */
    public function getFileRules($attribute) {
        return ArrayHelper::getValue($this->attributes[$attribute], 'rules', []);
    }

    /**
     * Get the presets of the file
     *
     * @param string $attribute Attribute of a model
     * @return array
     */
    public function getFilePreset($attribute) {
        return array_keys(ArrayHelper::getValue($this->attributes[$attribute], 'preset', []));
    }

    /**
     * Get the presets of the file for apply after upload
     *
     * @param string $attribute Attribute of a model
     * @return array
     */
    public function getFilePresetAfterUpload($attribute) {
        $preset = ArrayHelper::getValue($this->attributes[$attribute], 'applyPresetAfterUpload', false);
        if (is_string($preset) && $preset === '*') {
            return $this->getFilePreset($attribute);
        } elseif (is_array($preset)) {
            return $preset;
        }

        return [];
    }

    /**
     * Get the storage of the file
     *
     * @param string $attribute Attribute of a model
     * @return Storage
     * @throws InvalidParamException
     */
    public function getFileStorage($attribute) {
        return ArrayHelper::getValue($this->attributes[$attribute], 'storage', 'local');
    }

    /**
     * Generate a thumb name
     *
     * @param string $path The path of the file
     * @param string $prefix Prefix for name of the file
     * @return string
     */
    public static function generateThumbName($path, $prefix) {
        $fileName = pathinfo($path, PATHINFO_FILENAME);
        return str_replace($fileName, $prefix . '_' . $fileName, $path);
    }

    /**
     * Resize image
     *
     * @param string $attribute Attribute of a model
     * @param string $preset The name of the preset
     * @param string $pathToFile Use this path to the file
     * @param bool $returnRealPath Return the real path to the file
     * @return string
     */
    public function thumb($attribute, $preset, $pathToFile = null, $returnRealPath = false) {
        $realPath = $this->uploadDir($attribute);
        $publicPath = $pathToFile ? $pathToFile : $this->owner->$attribute;
        $thumbPath = $this->generateThumbName($publicPath, $preset);
        $ext = pathinfo($publicPath, PATHINFO_EXTENSION);
        if (\rabint\helpers\file::extToType($ext) != 'image') {
            return $returnRealPath ? $realPath . $publicPath : $publicPath;
        }
        if (!file_exists($realPath . $thumbPath)) {
            if (file_exists($realPath . $publicPath)) {
                $thumbInit = ArrayHelper::getValue($this->attributes[$attribute]['preset'], $preset);
                if ($thumbInit) {
                    $thumbInit($realPath, $publicPath, $thumbPath);
                }
            }
        }

        return $returnRealPath ? $realPath . $thumbPath : $thumbPath;
    }

    /**
     * Create a file
     *
     * @param string $attribute Attribute of a model
     * @param string $path The path of the file
     * @param string $title The title of file
     * @return rabint\attachment\models\File
     */
    public function createFile($attribute, $path, $title, \yii\web\UploadedFile $fileInstance = null) {
        $attachment = new Attachment();
        $attachment->fullPath = $path;
        $attachment->title = $title;
        $attachment->component = $this->getOwnerComponent($attribute);
        $attachment->protected = $this->isFileProtected($attribute) ? 1 : 0;
        $attachment->storage = $this->getFileStorage($attribute);
        if ($fileInstance != NULL) {
            $attachment->extension = pathinfo($fileInstance->name, PATHINFO_EXTENSION);
        } else {
            $attachment->extension = pathinfo($path, PATHINFO_EXTENSION);
        }
        if ($attachment->save()) {
            return $attachment->storageObject->save($path);
        }
        return false; // @codeCoverageIgnore
    }

    /**
     * Get a description of the validation rules in as text
     *
     * Example
     *
     * ```php
     * $form->field($model, $attribute)->hint($model->getFileRulesDescription($attribute)
     * ```
     *
     * Output
     *
     * ```
     * Min. size of image: 300x300px
     * File types: JPG, JPEG, PNG
     * Max. file size: 1.049 MB
     * ```
     *
     * @param string $attribute Attribute of a model
     * @return string
     */
    public function getFileRulesDescription($attribute) {
        return FormatValidation::getDescription($this->attributes[$attribute]['rules']);
    }

}
