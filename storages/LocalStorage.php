<?php

namespace rabint\attachment\storages;

use Yii;
use yii\helpers\FileHelper;
use \rabint\attachment\storages\StorageAbstract;

/**
 * The local storage for files
 *
 * @author mojtaba akbarzadeh <akbarzadeh.mojtaba@gmail.com>
 * @since 2.0
 */
class LocalStorage extends StorageAbstract
{

    /**
     * Upload directory
     *
     * @return string
     */
    public function baseUrl()
    {
        if ($this->getFile()->isProtected()) {
            return '';
        } else {
            $base = Yii::getAlias(Yii::$app->Attachment->baseUrl);
            if (empty($base)) {
                $base = \rabint\helpers\uri::home();
                $base = trim($base, '/');
            }
            return $base;
        }
    }

    public function uploadDir()
    {
        if ($this->getFile()->isProtected()) {
            return Yii::getAlias(Yii::$app->Attachment->uploadDirProtected);
        } else {
            return Yii::getAlias(Yii::$app->Attachment->uploadDirUnprotected);
        }
    }

    /**
     * Path to the directory of the file
     *
     * @param bool $realPath The real path of the directory
     * @return string
     */
    private function dir($realPath = false)
    {
        $file = $this->getFile();

        $path = $realPath ? $this->uploadDir() : '';
        $path .= '/' . Yii::$app->Attachment->publicPath;
        if (!empty($file->component)
//                AND $file->component != 'global'
        ) {
            $path .= '/' . $file->component;
        } elseif (!empty($file->user_id)) {
//            $path .= '/' . 'user-media/' . $file->user_id;
//            $path .= '/' . $file->extension;
        } else {
//            $path .= '/' . $file->extension;
        }
        $path .= '/' . $file->getDateOfFile();
        $path .= '/' . $file->id;
        return $path;
    }

    /**
     * Path to the file
     *
     * @param bool $realPath The real path of the file
     * @return string
     */
    public function path($realPath = false)
    {
        return $this->dir($realPath) . '/' . $this->getFile()->name;
    }

    /**
     * Save the file to the storage
     * If the file is temporary, then in the temporary directory
     *
     * @param string $path The path of the file
     * @return \rabint\attachment\models\File|bool
     */
    public function save($path)
    {
        if (file_exists($path)) {
            if (FileHelper::createDirectory($this->dir(true))) {
                $isConsole = Yii::$app instanceof \yii\console\Application;
                if (!is_uploaded_file($path) || $isConsole) {
                    $saved = rename($path, $this->path(true));
                } else {
                    $saved = move_uploaded_file($path, $this->path(true)); // @codeCoverageIgnore
                }

                if ($saved) {
                    $file = $this->getFile();
                    $file->path = $this->path();
                    $file->save();
                    return $file;
                }
            } // @codeCoverageIgnore
        } // @codeCoverageIgnore

        return false;
    }

    public function delete()
    {
        FileHelper::removeDirectory($this->dir(true));
    }

    public function rename($newName)
    {
        $file = $this->getFile();
        $dir = $this->dir(true);
        $dirFiles = scandir($dir);
        print_r($dirFiles);
        foreach ($dirFiles as $fname) {
            if (in_array($fname, ['.', '..'])) {
                continue;
            }
            $newer = str_replace($file->name, $newName, $fname);
            rename($dir . '/' . $fname, $dir . '/' . $newer);
        }
        $file->path = $this->dir(false) . '/' . $newName;
        $file->name = $newName;
        $file->save();
        return true;
    }

}
