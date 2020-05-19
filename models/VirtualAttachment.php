<?php

namespace rabint\attachment\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\FileHelper;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * @property \rabint\attachment\storages\LocalStorage $storageObject
 */
class VirtualAttachment extends Attachment
{

    public $fullPath = '';
    private $storageObj = null;

    private function fillMetaInfo()
    {
        return;
    }

    public function generateName()
    {
        //        $name = time() . substr(uniqid(), -5);
        $name = uniqid();
        return $name . '.' . $this->extension;
    }

    public function getTitleTag()
    {
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
        return true;
    }

    protected function setStorageObject()
    {
        return $this;
    }

    public function getStorageObject()
    {
        return $this->storageObj;
    }



    public function getUrl($size = '')
    {
        return $this->path;
    }

    public function getDirectUrl($size = '')
    {
        return $this->path;
    }
    
    public function getFullPath()
    {
        return $this->path;
    }

    /**
     * @return type
     * @deprecated
     */
    public function getBase_url()
    {
        return $this->path;
    }

    public function getBaseUrl()
    {
        return $this->path;
    }

    public function incDownloadCount()
    {
        $this->download_count = $this->download_count + 1;
        return true;
    }

    public static function getUploaderFileAttribute($value)
    {
                return [
                    [
                        'base_url' => '',
                        'delete_url' => '',
                        'path' => '',//$this->path,
                        'name' => '',//$this->name,
                        'size' => '',//$this->size,
                        'type' => '',//$this->mime,
                        'order' => '',//$this->weight,
                        'attachment_id' => '',//$this->id,
                        //          'url' => $file->url,
                    ]
                ];
    }

    public static function findByPath($path)
    {
            return null;
    }


}
