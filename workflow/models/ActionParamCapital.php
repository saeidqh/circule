<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamCapital extends \yii\base\Model {

    public $AMOUNT_STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    public $capitalShowBalanceAmount;
    public $capitalShowFinancialAmount;
    public $amountStatus;
    public $showObsChilds;

    public function rules() {
        return [
            [['capitalShowBalanceAmount', 'capitalShowFinancialAmount', 'amountStatus', 'showObsChilds'], 'integer'],
            [['amountStatus'], 'required'],
        ];
    }

    public function find($actionId) {
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])->asArray()->all();

        foreach ($actionParamModel as $key => $value) {
//            if ($value['paramTitle']== 'capitalShowBalanceAmount'){
//                $this->capitalShowBalanceAmount= $value['value1']=='true';
//            }else if ($value['paramTitle']== 'capitalShowFinancialAmount'){
//                $this->capitalShowFinancialAmount =$value['value1']=='true';
//            }
            if ($value['value1'] == 'capitalShowBalanceAmount') {
                $this->capitalShowBalanceAmount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'capitalShowFinancialAmount') {
                $this->capitalShowFinancialAmount = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'AmountStatus') {
                $this->amountStatus = $value['value1'];
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'capitalShowBalanceAmount',
            'value2' => ($this->capitalShowBalanceAmount) ? 'true' : 'false',
            'description' => 'Capital',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'capitalShowFinancialAmount',
            'value2' => ($this->capitalShowFinancialAmount) ? 'true' : 'false',
            'description' => 'Capital',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'AmountStatus',
            'value1' => $this->amountStatus,
            'value2' => null,
            'description' => 'Capital',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'showObsChilds',
            'value1' => 'capital',
            'value2' => $this->showObsChilds,
            'description' => 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند',
            'actionIdRef' => $actionId,
        ];

        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        foreach ($rows as $row) {
            $transaction = Yii::$app->dbTehran->beginTransaction();
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
//                    ->andWhere(['value1'=>$row['value1']])       کد قدیمی
//                    کد جدید
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->andWhere(($row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                    ->exists();

            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
//                    ->andWhere(['value1'=>$row['value1']])       کد قدیمی
//                    کد جدید
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->andWhere(($row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    $transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId .", ";
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
                    $actionParamCreateIds .= $actionParamModel->paramId .", ";
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
