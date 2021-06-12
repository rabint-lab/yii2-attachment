<?php

namespace rabint\attachment\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidParamException;

/**
 * Component of attachment
 *
 * @author mojtaba akbarzadeh <akbarzadeh.mojtaba@gmail.com>
 */
class attachment extends Component
{

    /**
     * @var string Directory to upload files protected, not accessible from the web
     */
    public $uploadDirProtected = '@runtime';

    /**
     * @var string Directory to upload files, accessible from the web
     */
    public $uploadDirUnprotected = '@app/web';

    /**
     * @var string Public path to files
     */
    public $publicPath = 'uploads';

    /**
     * @var string Public path to files
     */
    public $baseUrl = '';

    /**
     * @var array Type of owner in format: title:string => type:int
     */
    public $ownerTypes = [];

    /**
     * @internal
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * Get owner type
     *
     * @param string $ownerType The type of the owner
     * @return void
     * @throws InvalidParamException
     */
    public function getOwnerType($ownerType)
    {
        $ownerType = str_replace('{{%', '', $ownerType);
        $ownerType = str_replace('}}', '', $ownerType);
        if (!isset($this->ownerTypes[$ownerType])) {
            return 0;
//            throw new InvalidParamException('This type `' . $ownerType . '` is not found');
        }

        return $this->ownerTypes[$ownerType];
    }

    /**
     * Registers translator
     * @return void
     * @internal
     *
     */
    public function registerTranslations()
    {
        if (!isset(\Yii::$app->i18n->translations['rabint'])) {
            \Yii::$app->i18n->translations['rabint'] = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@vendor/rabint/attachment/messages',
                'sourceLanguage' => 'en',
            ];
        }
    }

}
