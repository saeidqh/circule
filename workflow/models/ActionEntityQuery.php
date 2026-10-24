<?php

namespace app\modules\workflow\models;

/**
 * This is the ActiveQuery class for [[ActionEntity]].
 *
 * @see ActionEntity
 */
class ActionEntityQuery extends \yii\db\ActiveQuery {

    public function all($db = null) {
        return parent::all($db);
    }

    public function one($db = null) {
        return parent::one($db);
    }

}
