<?php

namespace rabint\attachment\widgets\upload;

use yii\web\AssetBundle;

class UploadAsset extends AssetBundle {

//        public $publishOptions = ['forceCopy' => true];
    public $css = [
        'css/upload-kit.css'
    ];
    public $js = [
        'js/upload-kit.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
//        'yii\bootstrap\BootstrapAsset',
//        'rabint\assets\deprecatedFixAsset',
        'rabint\attachment\widgets\upload\BlueimpFileuploadAsset'
    ];

    public function init() {
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }

}
