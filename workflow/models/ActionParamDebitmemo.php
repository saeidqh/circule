<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamDebitmemo extends \yii\base\Model {

    public $ACCESS_TYPES = [
        1 => 'Hide',
        2 => 'Show Only',
        3 => 'Edit',
    ];
    public $AMOUNT_STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    public $debitmemo_financialDocNumber_show;
    public $debitmemo_financialDocNumber_required;
    public $debitmemo_financialDocDateHijri_show;
    public $debitmemo_financialDocDateHijri_required;
    public $debitmemo_canEditRequest;
    public $debitmemo_canEditApprove;
    public $debitmemo_insertDocument;
    public $debitmemo_amountStatus;

    public function rules() {
        return [
            [['debitmemo_financialDocNumber_show', 'debitmemo_financialDocNumber_required', 'debitmemo_financialDocDateHijri_show', 'debitmemo_financialDocDateHijri_required', 'debitmemo_canEditRequest', 'debitmemo_canEditApprove'
            , 'debitmemo_insertDocument', 'debitmemo_amountStatus'], 'integer'],
            [['debitmemo_financialDocNumber_show', 'debitmemo_financialDocNumber_required', 'debitmemo_financialDocDateHijri_show', 'debitmemo_financialDocDateHijri_required', 'debitmemo_canEditRequest', 'debitmemo_canEditApprove'
            , 'debitmemo_insertDocument', 'debitmemo_amountStatus'], 'required'],
        ];
    }

    public function find($actionId) {
        //@TODO: hasChange and isDelete 
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])
                        ->asArray()->all();

        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'debitmemo_financialDocNumber_show') {
                $this->debitmemo_financialDocNumber_show = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_financialDocNumber_required') {
                $this->debitmemo_financialDocNumber_required = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_financialDocDateHijri_show') {
                $this->debitmemo_financialDocDateHijri_show = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_financialDocDateHijri_required') {
                $this->debitmemo_financialDocDateHijri_required = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_canEditRequest') {
                $this->debitmemo_canEditRequest = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_canEditApprove') {
                $this->debitmemo_canEditApprove = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_insertDocument') {
                $this->debitmemo_insertDocument = $value['value1'];
            } else if ($value['paramTitle'] == 'debitmemo_amountStatus') {
                $this->debitmemo_amountStatus = $value['value1'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_financialDocNumber_show',
            'value1' => $this->debitmemo_financialDocNumber_show,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_financialDocNumber_required',
            'value1' => $this->debitmemo_financialDocNumber_required,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_financialDocDateHijri_show',
            'value1' => $this->debitmemo_financialDocDateHijri_show,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_financialDocDateHijri_required',
            'value1' => $this->debitmemo_financialDocDateHijri_required,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_canEditRequest',
            'value1' => $this->debitmemo_canEditRequest,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_canEditApprove',
            'value1' => $this->debitmemo_canEditApprove,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_insertDocument',
            'value1' => $this->debitmemo_insertDocument,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'debitmemo_amountStatus',
            'value1' => $this->debitmemo_amountStatus,
            'description' => 'debitmemo',
            'actionIdRef' => $actionId,
        ];

//$transaction = Yii::$app->dbTehran->beginTransaction();
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->exists();

            if ($recordExists) {

                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;

                if ($actionParamModel->save()) {
//                     $transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId . ", ";
                } else {
//                    $transaction->rollback();
                    return false;
                }
            } else { // create new record
                $actionParamModel = new ActionParam();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;

                if ($actionParamModel->save()) {
//                     $transaction->commit();
                    $actionParamCreateIds .= $actionParamModel->paramId . ", ";
                } else {
//                    $transaction->rollback();
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
