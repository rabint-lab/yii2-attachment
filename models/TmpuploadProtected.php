<?php

namespace rabint\attachment\models;

use Yii;

class TmpuploadProtected extends \common\models\base\ActiveRecord {

    public $attachment;

    public static function tableName() {
        return 'attachment';
    }

    public function behaviors() {
        return [
            [
                'class' => 'rabint\attachment\behaviors\AttechmentBehavior',
                'attributes' => [
                    'attachment_id' => [
                        // local storages
                        'storage' => 'local',
                        'protected' => true,
                        // save path of the file in this attribute
                        'saveFileId' => true,
                        'component' => 'global',
                        'rules' => [
//                            'imageSize' => ['minWidth' => 300, 'minHeight' => 300],
//                            'mimeTypes' => ['image/png', 'image/jpg', 'image/jpeg'],
//                            'extensions' => ['jpg', 'jpeg', 'png'],
//                            'maxSize' => 1024 * 1024 * 1, // 1 MB
//                            'tooBig' => Yii::t('rabint', 'File size must not exceed') . ' 1Mb'
                        ],
                        // presets for the files, can be used on the fly
                        // or you can to apply them after upload
                        'preset' => \rabint\attachment\attachment::imgPresetsFn('global'),
                        // * — to apply all presets after upload
                        // or an array with the names[] of presets
                        'applyPresetAfterUpload' => '*'
                    ]
                ]
            ]
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('rabint', 'ID'),
            'attachment_id' => Yii::t('rabint', 'attachment id'),
        ];
    }

}
