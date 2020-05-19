<?php

namespace rabint\attachment\models;

use Yii;
use common\models\User;
use rabint\attachment\models\query\AttachmentQuery;

/**
 * This is the model class for table "{{%attachment}}".
 *
 * @property string $id
 * @property string $user_id
 * @property string $component
 * @property string $path
 * @property string $title
 * @property string $name
 * @property integer $size
 * @property string $extension
 * @property string $type
 * @property string $mime
 * @property string $created_at
 * @property string $updated_at
 * @property string $ip
 * @property integer $weight
 * @property integer $protected
 * @property string $storage
 * @property string $download_count
 * @property string $meta
 *
 * @property User $user
 */
class AttachmentBase extends \common\models\base\ActiveRecord {
    /* storage */

    const STORAGE_LOCAL = 'local';
//    const STORAGE_FTP = 'ftp';
    /* protected */
    const PROTECTED_NO = 0;
    const PROTECTED_YES = 1;
    /* typs */
    const TYPE_IMAGE = 'image';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_DOCUMENT = 'document';
    const TYPE_SPREADSHEET = 'spreadsheet';
    const TYPE_INTERACTIVE = 'interactive';
    const TYPE_TEXT = 'text';
    const TYPE_ARCHIVE = 'archive';
    const TYPE_CODE = 'code';
    const TYPE_UNKNOWN = 'unknown';

    public static function storages() {
        return [
            static::STORAGE_LOCAL => [
                'title' => \Yii::t('rabint', 'محلی'),
                'class' => \rabint\attachment\storages\LocalStorage::className(),
            ],
        ];
    }

    public static function protecteds() {
        return [
            static::PROTECTED_NO => ['title' => \Yii::t('rabint', 'عمومی'),],
            static::PROTECTED_YES => ['title' => \Yii::t('rabint', 'حفاظت شده'),],
        ];
    }

    public static function types() {
        return [
            static::TYPE_IMAGE => ['title' => \Yii::t('rabint', 'تصویر'),],
            static::TYPE_AUDIO => ['title' => \Yii::t('rabint', 'صوت'),],
            static::TYPE_VIDEO => ['title' => \Yii::t('rabint', 'ویدئو'),],
            static::TYPE_DOCUMENT => ['title' => \Yii::t('rabint', 'سند متنی'),],
            static::TYPE_SPREADSHEET => ['title' => \Yii::t('rabint', 'صفحه گسترده'),],
            static::TYPE_INTERACTIVE => ['title' => \Yii::t('rabint', 'سند تعاملی'),],
            static::TYPE_TEXT => ['title' => \Yii::t('rabint', 'فایل متنی'),],
            static::TYPE_ARCHIVE => ['title' => \Yii::t('rabint', 'فایل آرشیو'),],
            static::TYPE_CODE => ['title' => \Yii::t('rabint', 'کد'),],
            static::TYPE_UNKNOWN => ['title' => \Yii::t('rabint', 'ناشناخته'),],
        ];
    }

    /*
     * @inheritdoc
     */

    public static function tableName() {
        return '{{%attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
                [['user_id', 'size', 'created_at', 'updated_at', 'weight', 'protected', 'download_count'], 'integer'],
                [['meta'], 'string'],
//            [['storage'], 'required'],
            [['storage'], 'string'],
                [['storage'], 'in', 'range' => [static::STORAGE_LOCAL]],
                ['storage', 'default', 'value' => static::STORAGE_LOCAL],
                [['component'], 'string', 'max' => 32],
                [['path', 'title', 'name'], 'string', 'max' => 255],
                [['extension'], 'string', 'max' => 10],
                [['type'], 'string', 'max' => 20],
                [['mime'], 'string', 'max' => 100],
                [['ip'], 'string', 'max' => 48],
                [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
                [['meta', 'storage', 'component', 'path', 'title', 'name', 'extension', 'type', 'mime', 'ip'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('rabint', 'شناسه'),
            'user_id' => Yii::t('rabint', 'کاربر'),
            'component' => Yii::t('rabint', 'کامپوننت'),
            'path' => Yii::t('rabint', 'پوشه آپلود'),
            'title' => Yii::t('rabint', 'عنوان'),
            'name' => Yii::t('rabint', 'نام فایل'),
            'size' => Yii::t('rabint', 'اندازه'),
            'extension' => Yii::t('rabint', 'پسوند'),
            'type' => Yii::t('rabint', 'نوع فایل'),
            'mime' => Yii::t('rabint', 'جنس فایل'),
            'created_at' => Yii::t('rabint', 'تاریخ ایجاد'),
            'updated_at' => Yii::t('rabint', 'تاریخ بروزرسانی'),
            'ip' => Yii::t('rabint', 'آی پی آپلود کننده'),
            'weight' => Yii::t('rabint', 'ترتیب'),
            'protected' => Yii::t('rabint', 'محافظت شده'),
            'download_count' => Yii::t('rabint', 'تعداد دانلود'),
            'meta' => Yii::t('rabint', 'متا'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return \rabint\attachment\models\query\AttachmentQuery the active query used by this AR class.
     */
    public static function find() {
        return new AttachmentQuery(get_called_class());
    }

}
