<?php

namespace app\modules\workflow\models;

/**
 * This is the ActiveQuery class for [[Action]].
 *
 * @see Action
 */
class ActionQuery extends \yii\db\ActiveQuery
{

    public function process($pid)
    {
        return $this->andWhere('ProcessIDRef=' . $pid);
    }

    public function obs($obsid)
    {
        $obsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        return $this->joinWith('obs')->andWhere(["$obsProcessActionTable.OBSCategory3IDRef" => $obsid])
            ->andWhere("$obsProcessActionTable.isDeleted is null or $obsProcessActionTable.isDeleted !=1");
    }

    public function processCode($pcId): ActionQuery
    {
        $processCodeActionTable =\app\modules\document\models\ProcessCodeAction::tableName();
        return $this->joinWith('processCodeAction')->andWhere(["$processCodeActionTable.CodeIDRef" => $pcId])
            ->andWhere("$processCodeActionTable.ActionType = 'workflow'");
    }

    public function all($db = null)
    {
        return parent::all($db);
    }

    public function one($db = null)
    {
        return parent::one($db);
    }

    public function reportItemAction($itemId)
    {
        return $this->joinWith(['reportItemAction', 'process'])->where([\app\modules\reportWorkflow\models\ReportItemAction::tableName() . '.id' => $itemId]);
    }

}
