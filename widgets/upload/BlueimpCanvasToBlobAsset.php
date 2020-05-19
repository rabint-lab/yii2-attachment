<?php
namespace rabint\attachment\widgets\upload;

use yii\web\AssetBundle;

class BlueimpCanvasToBlobAsset extends AssetBundle
{
    public $sourcePath = '@npm/blueimp-canvas-to-blob';

    public $js = [
        'js/canvas-to-blob.min.js'
    ];
}
