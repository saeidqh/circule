<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamClaim extends \yii\base\Model {

    public $AMOUNT_STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    public $amountStatus;
    public $claimUpdate;
    public $claimDocumentUpdate;
    public $claimDocumentDelete;
    public $claimDocumentCreate;
    public $claimAmountStep1Update;
    public $claimAmountStep2Update;
    public $claimAmountStep3Update;
    public $claimAmountStep4Update;
    public $claimAmountStep5Update;

    public function rules() {
        return [
            [['amountStatus', 'claimUpdate', 'claimDocumentUpdate', 'claimDocumentDelete', 'claimDocumentCreate'
            , 'claimAmountStep1Update', 'claimAmountStep2Update', 'claimAmountStep3Update', 'claimAmountStep4Update', 'claimAmountStep5Update'], 'integer'],
        ];
    }

    public function find($actionId) {
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])->asArray()->all();

        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'claimUpdate') {
                $this->claimUpdate = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimDocumentUpdate') {
                $this->claimDocumentUpdate = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimDocumentDelete') {
                $this->claimDocumentDelete = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimDocumentCreate') {
                $this->claimDocumentCreate = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimAmountStep1Update') {
                $this->claimAmountStep1Update = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimAmountStep2Update') {
                $this->claimAmountStep2Update = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimAmountStep3Update') {
                $this->claimAmountStep3Update = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimAmountStep4Update') {
                $this->claimAmountStep4Update = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'claimAmountStep5Update') {
                $this->claimAmountStep5Update = $value['value1'] == 'true';
            } else if ($value['paramTitle'] == 'AmountStatus') {
                $this->amountStatus = $value['value1'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'claimUpdate',
            'value1' => ($this->claimUpdate) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimDocumentUpdate',
            'value1' => ($this->claimDocumentUpdate) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimDocumentDelete',
            'value1' => ($this->claimDocumentDelete) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimDocumentCreate',
            'value1' => ($this->claimDocumentCreate) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimAmountStep1Update',
            'value1' => ($this->claimAmountStep1Update) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimAmountStep2Update',
            'value1' => ($this->claimAmountStep2Update) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimAmountStep3Update',
            'value1' => ($this->claimAmountStep3Update) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimAmountStep4Update',
            'value1' => ($this->claimAmountStep4Update) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'claimAmountStep5Update',
            'value1' => ($this->claimAmountStep5Update) ? 'true' : 'false',
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'AmountStatus',
            'value1' => $this->amountStatus,
            'value2' => null,
            'description' => 'claim',
            'actionIdRef' => $actionId,
        ];
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        foreach ($rows as $row) {
            $transaction = Yii::$app->dbTehran->beginTransaction();
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->exists();

            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    $transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId . ", ";
                } else {
                    $transaction->rollback();
                    return false;
                }
            } else { // create new record
                $actionParamModel = new ActionParam();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    $transaction->commit();
                    $actionParamCreateIds .= $actionParamModel->paramId . ", ";
                } else {
                    $transaction->rollback();
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
