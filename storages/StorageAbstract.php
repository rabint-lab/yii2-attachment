<?php

namespace rabint\attachment\storages;

use yii\base\InvalidParamException;
use rabint\attachment\StorageInterface;

/**
 * The base storage for all storages
 *
 * @author mojtaba akbarzadeh <akbarzadeh.mojtaba@gmail.com>
 * @since 1.0
 */
abstract class StorageAbstract extends \yii\base\BaseObject {

    /**
     * @var File
     */
    private $file;

    abstract public function baseUrl();

    /**
     * Path to the file
     *
     * @return string
     */
    abstract public function path();

    /**
     * Save the file to the storage
     * If the file is temporary, then in the temporary directory
     *
     * @return \rabint\attachment\models\File|bool
     */
    abstract public function save($path);

    /**
     * Deletes the file from the storage
     */
    abstract public function delete();

    /**
     * Set a file
     *
     * @param File $file File
     * @return string
     */
    public function setFile(\rabint\attachment\models\Attachment $file) {
        $this->file = $file;
    }

    /**
     * Get a file
     *
     * @return File
     * @throws InvalidParamException
     */
    public function getFile() {
        if ($this->file === null) {
            throw new InvalidParamException('The file is not initialized');
        }

        return $this->file;
    }

}
