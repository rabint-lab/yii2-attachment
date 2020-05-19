<?php

namespace rabint\attachment\actions;

use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidParamException;
use yii\web\UploadedFile;

class UploadRedactorAction extends UploadAction {

    protected function successOutput($files) {
        return $this->response([
                    'link' => $files[0]->storageObject->baseUrl() . $files[0]->storageObject->path(),
                    'filelink' => $files[0]->storageObject->baseUrl() . $files[0]->storageObject->path(),
                    'filename' => $files[0]->name,
                        ]
        );
    }

}
