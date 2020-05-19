<?php

namespace rabint\attachment;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * filemanager module definition class
 */
class attachment extends \yii\base\Module {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'rabint\attachment\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
        // custom initialization code goes here
    }

    public static function adminMenu() {
        return
                    [
                    'label' => \Yii::t('rabint', 'مدیریت فایل'),
                    'icon' => '<i class="fas fa-upload"></i>',
                    'url' => '#',
                    'options' => ['class' => 'treeview'],
                    'visible' =>\rabint\helpers\user::can('administrator'),
                    'items' => [
                            [
                                'label' => \Yii::t('rabint', 'فایل های آپلود شده'),
                                 'url' => ['/attachment/admin'], 
                                 'icon' => '<i class="far fa-circle"></i>',
                                 
                            ],
//                ['label' => \Yii::t('rabint', 'آپلود فایل'), 'url' => ['/filemanager/admin/create'], 'icon' => '<i class="far fa-circle"></i>'],
                    ]
        ];
    }

    /**
     * 
     * @param models\Attachment $attachment 
     */
    public static function removeOtherSize($attachment) {
        $path = $attachment->getFullPath();
        $dir = dirname($path);
        $exclude = [basename($path)];
        \rabint\helpers\file::deleteDir($dir, $exclude, TRUE);
    }

    public static function imgPresetsFn($component = 'global') {
        if (!static::hasComponnet($component)) {
            return false;
        }
        $globalPresets = [];
        foreach (config('filemanager.presets.' . $component) as $pName => $pSizeData) {
            $globalPresets[$pName] = function ($realPath, $publicPath, $thumbPath) use($pSizeData) {
                // any manipulation on the file BY $pSizeData[2]
                $opration = isset($pSizeData[2]) ? $pSizeData[2] : 'normal';
                $fullPath = \yii\helpers\FileHelper::normalizePath($realPath . '/' . $publicPath);
                try {
                    $Image = Image::make($fullPath);
                    switch ($opration) {
                        case 'resizeToX':
                            $Image = $Image->widen($pSizeData[0]);
                            break;
                        case 'resizeToY':
                            $Image = $Image->heighten($pSizeData[1]);
                            break;
                        case 'resize':
                            $Image = $Image->resize($pSizeData[0], $pSizeData[1]);
                            break;
                        case 'normal':
                        default:
                            if ($Image->height() > $Image->width()) {
                                $Image = $Image->heighten($pSizeData[1]);
//                            $Image = $Image->resizeCanvas($pSizeData[0], $pSizeData[1],'center', false, '#000000');
                                $Image = $Image->resizeCanvas($pSizeData[0], $pSizeData[1]);
                            } else {
                                $Image = $Image->fit($pSizeData[0], $pSizeData[1], NULL, 'top');
                            }
                            break;
                    }
                    $fullPath = \yii\helpers\FileHelper::normalizePath($realPath . '/' . $thumbPath);
//                $Image->save($fullPath, 100);
                    $Image->save($fullPath, 90);
                } catch (\Exception $exc) {
                    \Yii::warning($exc->getTraceAsString(), 'handled_exc');
                }
            };
        }
        return $globalPresets;
    }

    public static function getPresetBySize($component = 'global', array $requestedSize) {
        if (!static::hasComponnet($component)) {
            return false;
        }
        $componentSizes = config('filemanager.presets.' . $component);
        foreach (config('filemanager.presets.' . $component) as $preset => $size) {
            if ($requestedSize[0] == $size[0] AND $requestedSize[1] == $size[1]) {
                return $preset;
            }
        }

        $res = \rabint\helpers\collection::closest2d($componentSizes, $requestedSize);
        return $res;
    }

    public static function hasComponnet($component) {
        $allComponents = config('filemanager.presets');
        if (isset($allComponents[$component])) {
            return TRUE;
        }
        return false;
    }

}
