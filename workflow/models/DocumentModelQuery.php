<?php

namespace app\modules\workflow\models;

/**
 * This is the ActiveQuery class for [[DocumentModel]].
 *
 * @see DocumentModel
 */
class DocumentModelQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DocumentModel[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DocumentModel|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
