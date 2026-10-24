<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamFactor extends \yii\base\Model {

    public $AMOUNT_STATUS_TYPES = [
        1 => 'Hide',
        2 => 'Show Only',
        3 => 'Edit',
    ];
    public static $STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    public $contractClosingDocument;
    public $status;
    
    public function rules() {
        return [
            [['contractClosingDocument', 'status'], 'integer'],
            [['contractClosingDocument', 'status'], 'required'],
        ];
    }

    public function find($actionId) {
        //@TODO: hasChange and isDelete 
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])
                        ->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'contractClosingDocumentAccessId') {
                $val = str_split($value['value1']);
                $this->contractClosingDocument = $val[0];
            } else if ($value['paramTitle'] == 'status') {
                $this->status = $value['value1'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'contractClosingDocumentAccessId',
            'value1' => $this->contractClosingDocument,
            'value2' => null,
            'description' => 'contractClosingDocument',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'status',
            'value1' => $this->status,
            'value2' => null,
            'description' => 'contractClosingDocument',
            'actionIdRef' => $actionId,
        ];

        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        //$transaction = Yii::$app->dbTehran->beginTransaction();
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->andWhere(($row['paramTitle'] != 'contractClosingDocumenAccessId' && $row['paramTitle'] != 'status') ? ['value1' => $row['value1']] : '1=1')
                    ->exists();

            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->andWhere(($row['paramTitle'] != 'contractClosingDocumenAccessId' && $row['paramTitle'] != 'status') ? ['value1' => $row['value1']] : '1=1')
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    //$transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId . ", ";
                } else {
                    //$transaction->rollback();
                    return false;
                }
            } else { // create new record
                $actionParamModel = new ActionParam();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    //$transaction->commit();
                    $actionParamCreateIds .= $actionParamModel->paramId . ", ";
                } else {
                    //$transaction->rollback();
                    return false;
                }
            }
        }
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendActionParamCreateActitvityLog($actionParamCreateIds);

        $activityManagement1 = new \app\components\ActivityManagement();
        $activityManagement1->sendActionParamUpdateActitvityLog($actionParamUpdateIds);

        return true;
    }

}
