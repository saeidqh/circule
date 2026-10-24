<?php

namespace app\modules\workflow\models;

use Yii;

class ActionOrderParamTender extends \yii\base\Model {

    public $requiredMinimumBidder;
    public $bidderAttachment;
    public $baselinePriceAttachment;
    
    public function rules() {
        return [
            [['requiredMinimumBidder', 'bidderAttachment', 'baselinePriceAttachment'], 'integer'],
            [['requiredMinimumBidder', 'bidderAttachment', 'baselinePriceAttachment'], 'required'],
        ];
    }

    public function find($actionOrderId) {
        //@TODO: hasChange and isDelete 
        $actionOrderParamModel = ActionOrderParam::find()->where(['actionOrderIdRef' => $actionOrderId])
                        ->asArray()->all();
        foreach ($actionOrderParamModel as $key => $value) {
            if ($value['paramTitle'] == 'requiredMinimumBidder') {
                $this->requiredMinimumBidder = $value['value1'];
            } else if($value['paramTitle'] == 'requiredTenderAttachment'){
                if ($value['value1'] == 'bidderAttachment') {
                    $this->bidderAttachment = $value['value2'];
                } else if ($value['value1'] == 'baselinePriceAttachment') {
                    $this->baselinePriceAttachment = $value['value2'];
                }
            }
        }
    }

    public function save($actionOrderId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'requiredMinimumBidder',
            'value1' => $this->requiredMinimumBidder,
            'value2' => null,
            'description' => 'tender',
            'actionOrderIdRef' => $actionOrderId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'requiredTenderAttachment',
            'value1' => 'bidderAttachment',
            'value2' => $this->bidderAttachment,
            'description' => 'tender',
            'actionOrderIdRef' => $actionOrderId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'requiredTenderAttachment',
            'value1' => 'baselinePriceAttachment',
            'value2' => $this->baselinePriceAttachment,
            'description' => 'tender',
            'actionOrderIdRef' => $actionOrderId,
        ];
        $actionOrderParamCreateIds = "";
        $actionOrderParamUpdateIds = "";
        //$transaction = Yii::$app->dbTehran->beginTransaction();
        foreach ($rows as $row) {
            $recordExists = ActionOrderParam::find()->where(['actionOrderIdRef' => $row['actionOrderIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->andWhere(($row['paramTitle'] != 'requiredMinimumBidder') ? ['value1' => $row['value1']] : '1=1')
                    ->exists();

            if ($recordExists) { // update exists record
                $actionOrderParamModel = ActionOrderParam::find()->where(['actionOrderIdRef' => $row['actionOrderIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->andWhere(($row['paramTitle'] != 'requiredMinimumBidder') ? ['value1' => $row['value1']] : '1=1')
                        ->one();
                $actionOrderParamModel->attributes = $row;
                if ($actionOrderParamModel->save()) {
                    //$transaction->commit();
                    $actionOrderParamUpdateIds .= $actionOrderParamModel->paramId . ", ";
                } else {
                    //$transaction->rollback();
                    return false;
                }
            } else { // create new record
                $actionOrderParamModel = new ActionOrderParam();
                $actionOrderParamModel->attributes = $row;
                if ($actionOrderParamModel->save()) {
                    //$transaction->commit();
                    $actionOrderParamCreateIds .= $actionOrderParamModel->paramId . ", ";
                } else {
                    //$transaction->rollback();
                    return false;
                }
            }
        }
        if($actionOrderParamCreateIds != ""){
            $actionOrderParamModel->sendLog("[$actionOrderParamCreateIds]", "New");
        } else {
            $actionOrderParamModel->sendLog("[$actionOrderParamUpdateIds]", "Edit");
        }

        return true;
    }

}
