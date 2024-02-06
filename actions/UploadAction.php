<?php

namespace rabint\attachment\actions;

use Yii;
use yii\base\Action;
use yii\base\DynamicModel;
use yii\base\InvalidParamException;
use yii\web\UploadedFile;

class UploadAction extends Action
{

    /**
     * @var string $modelName The name of model
     */
    public $modelName;

    /**
     * @var string $attribute
     */
    public $attribute;

    /**
     * @var string $inputName The name of the file input field
     */
    public $inputName = '';

    /**
     * @var string $type The type of the file (`image` or `file`)
     */
    public $type = 'image';

    /**
     * @var string $multiple Multiple files
     */
    public $multiple = false;

    /**
     * @var string $multipleOptions Multiple files Option (by ext OR eachSize OR allSize)
     * example :
     *    ['ext'] => "png, jpg, jpeg, gif, bmp"
     *    ['type'] => "image"
     *    ['eachSize'] => 1024000
     *    ['allSize'] => 10240000
     */
    public $multipleOptions = [];

    /**
     *
     * @var boolean
     */
    public $guestCanUpload = false;

    /**
     *
     * @var type
     */
    public $convertMedia = true;

    /**
     * @var string $template Path to template for multiple files
     */
    public $template;

    /**
     * @var string $resultFieldId The name of the field that contains the id of the file in the response
     */
    public $resultFieldId = 'id';

    /**
     * @var string $resultFieldPath The name of the field that contains the path of the file in the response
     */
    public $resultFieldPath = 'path';

    /**
     * @var ActiveRecord $model
     */
    protected $model;

    /**
     * @see http://www.yiiframework.com/doc-2.0/guide-tutorial-core-validators.html
     * @var array $rules
     */
    protected $rules;

    public function init()
    {
        //http://localhost/rtv/attachment/default/file-upload?opt[multipleOptions][type]=image&opt[multiple]=1&fileparam=_fileinput_w1

        $get = Yii::$app->request->get();
        if (isset($get['fileparam']) AND empty($this->inputName)) {
            $this->inputName = $get['fileparam'];
        }

        if (isset($get['opt']) AND isset($get['opt']['multipleOptions'])) {
            $this->multipleOptions = $get['opt']['multipleOptions'];
        }

        if ($this->modelName === null) {
            throw new InvalidParamException('The "modelName" attribute must be set.');
        }


        $this->model = new $this->modelName();
        $this->rules = $this->model->getFileRules($this->attribute);

        if (isset($this->rules['imageSize'])) {
            $this->rules = array_merge($this->rules, $this->rules['imageSize']);
            unset($this->rules['imageSize']);
        }
    }

    public function run()
    {
        $userCanUpload = config("SECURITY.canUploadPermission", 'user');
//        if ($userCanUpload != "guest" and ! \rabint\helpers\user::can($userCanUpload)) {
        if ((!$this->guestCanUpload) && ($userCanUpload != "guest" and !\rabint\helpers\user::can($userCanUpload))) {
            return $this->errorOutput(Yii::t('rabint', 'You do not have access to upload File'));
        }


        if (isset($_FILES[$this->inputName]['name']) AND is_array($_FILES[$this->inputName]['name'])) {
            $newFiles = \rabint\helpers\collection::rotateArray($_FILES[$this->inputName]);
//            $_FILES[$this->inputName] = $newFiles[0];
            $files = UploadedFile::getInstancesByName($this->inputName);
        }
        $files[] = UploadedFile::getInstanceByName($this->inputName);

        $returnErrors = $returnFiles = [];
        foreach ($files as $i=>$file) {

            if (!$file) {
//            var_dump($_FILES);
//            var_dump($file->error);
//            var_dump($_FILES);
                $returnErrors[] = Yii::t('rabint', 'file {index} can`t be uploaded!',['index'=>($i+1)]);
                continue;
            }
            /**
             * check multipleOptions
             */
//            if(!$this->multiple && $i>0){
//                break;
//            }
            if(!$this->canMultipleUpload($i,$file)){
                $returnErrors[] = Yii::t('rabint', 'file {index} can`t be uploaded!',['index'=>($i+1)]);
                continue;
            }
            /**
             * global security check:
             */
            if (!\rabint\helpers\security::checkAllowedUploadedFile($file)) {
                $returnErrors[] = Yii::t('rabint', 'file type not allowed');
                continue;
            }else{
                $model = new DynamicModel(compact('file'));
                $model->addRule('file', $this->type, $this->rules)->validate();

                if ($model->hasErrors()) {
                    $returnErrors[] = $model->getFirstError('file');
                } else {
                    if($res = $this->upload($file)){
                        $returnFiles[] = $res;
                    }else{
                        $returnErrors[] =Yii::t('rabint', 'Error saving file');
                    }
                }
            }
        }
//        pr($returnErrors);
        if(!empty($returnFiles)){
            return $this->successOutput($returnFiles);
        }
        $this->errorOutput($returnErrors);
    }

    /**
     * Upload
     *
     * @param UploadedFile $file
     * @return string JSON
     */
    protected function upload($file)
    {

        $file = $this->model->createFile(
            $this->attribute, $file->tempName, $file->name, $file
        );
        if ($file) {
            $presetAfterUpload = $this->model->getFilePresetAfterUpload($this->attribute);
            if (count($presetAfterUpload)) {
                $this->applyPreset($file->storageObject->path(), $presetAfterUpload);
            }
            if ($this->convertMedia && FALSE != config('FFMPEG_BIN_DIR', false)) {
                if ($file->type == 'video' OR $file->type == 'audio') {
                    \rabint\helpers\collection::startObToKeepProcessing();
                    echo json_encode($this->successOutput($file));
                    \rabint\helpers\collection::endObAndKeepProcessing();
                    \rabint\helpers\media::getAttachmentScenes($file, 0.2);
                    return \rabint\helpers\media::convertAttachment($file);
                } elseif ($file->type == 'image') {
                    \rabint\helpers\media::getAttachmentScenes($file, 0.2);
                }
            }
            return $file;
        } else {
            return false;
        }
    }

    protected function errorOutput($errors)
    {
        if(!is_array($errors)){
            $errors = [$errors];
        }

        return $this->response(
            [
                'error' => true,
                'errors' => implode(",\n",$errors)
            ]
        );
    }

    protected function successOutput($files)
    {
        if(!is_array($files)){
            $files = [$files];
        }
        $resp = [];
        foreach ($files as $file){
            $resp[] = [
                'name' => $file->name,
                'type' => $file->mime,
                'size' => $file->size,
                'base_url' => $file->storageObject->baseUrl(),
                'path' => $file->storageObject->path(),
                'attachment_id' => $file->id,
                'url' => $file->storageObject->baseUrl() . $file->storageObject->path(),
                'delete_url' => \yii\helpers\Url::to(['/attachment/default/delete', 'id' => $file->id]),
                'update_url' => \yii\helpers\Url::to(['/attachment/default/update', 'id' => $file->id]),
            ];
        }

        return $this->response([
                'files' => $resp
//                'files' => [
//                        [
//                            'name' => $file->name,
//                            'type' => $file->mime,
//                            'size' => $file->size,
//                            'base_url' => $file->storageObject->baseUrl(),
//                            'path' => $file->storageObject->path(),
//                            'attachment_id' => $file->id,
//                            'url' => $file->storageObject->baseUrl() . $file->storageObject->path(),
//                            'delete_url' => \yii\helpers\Url::to(['/attachment/default/delete', 'id' => $file->id]),
//                            'update_url' => \yii\helpers\Url::to(['/attachment/default/update', 'id' => $file->id]),
//                        ],
//                    ],
//                        $this->resultFieldId => $file->id,
//                        $this->resultFieldPath => $file->storageObject->path()
            ]
        );
    }

    /**
     * Apply preset for file
     *
     * @param string $path
     * @param array $presetAfterUpload
     * @return void
     */
    protected function applyPreset($path, $presetAfterUpload)
    {
        foreach ($presetAfterUpload as $preset) {
            $this->model->thumb($this->attribute, $preset, $path);
        }
    }

    /**
     * JSON Response
     *
     * @param mixed $data
     * @return string JSON Only for yii\web\Application, for console app returns `mixed`
     */
    protected function response($data)
    {
        // @codeCoverageIgnoreStart
        if (!Yii::$app instanceof \yii\console\Application) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        }
        // @codeCoverageIgnoreEnd
        return $data;
    }

    /**
     *
     *
     * @return null| the instance of the uploaded file.
     * Null is returned if no file is uploaded for the specified name.
     */

    /**
     * @param integer $index
     * @param UploadedFile $file
     * @return boolean
     */
    protected function canMultipleUpload($index, $file)
    {
        if($index==0) {
            return true;
        }
        /**
         * check type
         */
        if(isset($this->multipleOptions['type'])){
            $mime = $file->type;
            $res = \rabint\helpers\file::mimeToType($mime);
            if($this->multipleOptions['type']!==$res){
                return false;
            }
        }
        /**
         * check ext
         */

        /**
         * check size
         */

        /**
         * check allsize
         */

        return true;
    }


}
