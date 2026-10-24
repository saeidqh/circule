<?php

namespace app\modules\workflow\models;

/**
 * This is the ActiveQuery class for [[CalenderDay]].
 *
 * @see CalenderDay
 */
class CalenderDayQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return CalenderDay[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return CalenderDay|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
