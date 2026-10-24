<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamPermission extends \yii\base\Model {

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
    public $permission_edit;
    public $permission_insertAmount;
    public $permission_updateAmount;
    public $permission_canEditRequest;
    public $permission_canEditApprove;
    public $permission_insertDocument;
    public $permission_amountStatus;
    public $showObsChilds;

    public function rules() {
        return [
            [['showObsChilds', 'permission_edit', 'permission_insertAmount', 'permission_canEditRequest', 'permission_canEditApprove', 'permission_canEditApprove'
            , 'permission_insertDocument', 'permission_updateAmount', 'permission_amountStatus'], 'integer'],
            [['showObsChilds', 'permission_edit', 'permission_insertAmount', 'permission_canEditRequest', 'permission_canEditApprove', 'permission_canEditApprove'
            , 'permission_insertDocument', 'permission_updateAmount', 'permission_amountStatus'], 'required'],
        ];
    }

    public function find($actionId) {
        //@TODO: hasChange and isDelete 
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])
                        ->asArray()->all();
//        var_dump($actionParamModel);
//        die();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'permission_edit') {
                $this->permission_edit = $value['value1'];
            } else if ($value['paramTitle'] == 'permission_insertAmount') {
                $this->permission_insertAmount = $value['value1'];
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            } else if ($value['paramTitle'] == 'permission_canEditRequest') {
                $this->permission_canEditRequest = $value['value1'];
            } else if ($value['paramTitle'] == 'permission_canEditApprove') {
                $this->permission_canEditApprove = $value['value1'];
            } else if ($value['paramTitle'] == 'permission_insertDocument') {
                $this->permission_insertDocument = $value['value1'];
            } else if ($value['paramTitle'] == 'permission_updateAmount') {
                $this->permission_updateAmount = $value['value1'];
            } else if ($value['paramTitle'] == 'permission_amountStatus') {
                $this->permission_amountStatus = $value['value1'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_edit',
            'value1' => $this->permission_edit,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_insertAmount',
            'value1' => $this->permission_insertAmount,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_canEditRequest',
            'value1' => $this->permission_canEditRequest,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_canEditApprove',
            'value1' => $this->permission_canEditApprove,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_insertDocument',
            'value1' => $this->permission_insertDocument,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_updateAmount',
            'value1' => $this->permission_updateAmount,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'permission_amountStatus',
            'value1' => $this->permission_amountStatus,
            'description' => 'permission',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'showObsChilds',
            'value1' => 'permission',
            'value2' => $this->showObsChilds,
            'description' => 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند',
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
