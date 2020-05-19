<?php

namespace rabint\attachment\models\query;

/**
 * This is the ActiveQuery class for [[\rabint\attachment\models\Attachment]].
 *
 * @see \rabint\attachment\models\Attachment
 */
class AttachmentQuery extends \common\models\base\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \rabint\attachment\models\Attachment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \rabint\attachment\models\Attachment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
