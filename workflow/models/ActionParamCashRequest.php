<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamCashRequest extends \yii\base\Model {

    public $AMOUNT_STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    public $requestType; // --> fieldEnabled --- CashRequestTypeIDRef
    public $eps; // --> ControlVisible --- btnSelectEPS
    public $recipientType; // --> fieldEnabled ---  CashRequestRecipientTypeIDRef
    public $recipient; // --> fieldEnabled ---  [RecipientName,RecipientContractNo,RecipientSubContractor]
    public $budgetRow; // --> fieldEnabled ---  BudgetRow
    public $budgetTitle; // --> fieldEnabled ---  BudgetTitle
    public $paymentSubject; // --> fieldEnabled ---  AmountDescription
    public $accountType; // --> fieldEnabled ---  RecipientAccountType
    public $accountNumber; // --> fieldEnabled ---  RecipientAccountNumber
    public $bank; // --> fieldEnabled ---  RecipientAccountBank
    public $name; // --> fieldEnabled ---  RecipientAccountName
    public $requestAmount; // --> fieldEnabled ---  RequestedAmount
    public $requestCurrency; // --> fieldEnabled ---  RequestedCurrencyIDRef
    public $manager1Amount; // --> fieldEnabled ---  Manager1Amount
    public $planningAmount; // --> fieldEnabled ---  PlanningAmount
    public $manager2Amount; // --> fieldEnabled ---  Manager2Amount
    public $manager2Currency; // --> fieldEnabled ---  Manager2CurrencyIDRef
    public $clientAmount; // --> fieldEnabled ---  ClientAmount
    public $financialAmountInManager2Currency; // --> fieldEnabled ---  FinancialAmountInManager2Currency
    public $financialAmount; // --> fieldEnabled ---  FinancialAmount
    public $financialCurrency; // --> fieldEnabled ---  FinancialCurrencyIDRef
    public $chequeNumber; // --> fieldEnabled ---  ReceiptNumber
    public $chequeDate; // --> fieldEnabled ---  ReceiveDateHijri
    public $reciver; // --> fieldEnabled ---  Receiver
    public $tempNo; // --> fieldEnabled ---  TempNo
    public $amountStatus;
    public $showObsChilds;

    public function rules() {
        return [
            [['requestType', 'eps', 'recipientType', 'recipient', 'budgetRow', 'budgetTitle', 'paymentSubject', 'accountType', 'accountNumber', 'bank', 'name', 'requestAmount', 'requestCurrency', 'manager1Amount', 'planningAmount', 'manager2Amount', 'manager2Currency', 'clientAmount', 'financialAmountInManager2Currency', 'financialAmount', 'financialCurrency', 'chequeNumber', 'chequeDate', 'reciver', 'tempNo', 'amountStatus', 'showObsChilds'], 'integer'],
            [['amountStatus'], 'required'],
        ];
    }

    public function find($actionId) {
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['value1'] == 'CashRequestTypeIDRef') {
                $this->requestType = $value['value2'] == 'true';
            } else if ($value['value1'] == 'btnSelectEPS') {
                $this->eps = $value['value2'] == 'true';
            } else if ($value['value1'] == 'CashRequestRecipientTypeIDRef') {
                $this->recipientType = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            } else if ($value['value1'] == 'RecipientName') {
                $this->recipient = $value['value2'] == 'true';
            } else if ($value['value1'] == 'BudgetRow') {
                $this->budgetRow = $value['value2'] == 'true';
            } else if ($value['value1'] == 'BudgetTitle') {
                $this->budgetTitle = $value['value2'] == 'true';
            } else if ($value['value1'] == 'AmountDescription') {
                $this->paymentSubject = $value['value2'] == 'true';
            } else if ($value['value1'] == 'RecipientAccountType') {
                $this->accountType = $value['value2'] == 'true';
            } else if ($value['value1'] == 'RecipientAccountNumber') {
                $this->accountNumber = $value['value2'] == 'true';
            } else if ($value['value1'] == 'RecipientAccountBank') {
                $this->bank = $value['value2'] == 'true';
            } else if ($value['value1'] == 'RecipientAccountName') {
                $this->name = $value['value2'] == 'true';
            } else if ($value['value1'] == 'RequestedAmount') {
                $this->requestAmount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'RequestedCurrencyIDRef') {
                $this->requestCurrency = $value['value2'] == 'true';
            } else if ($value['value1'] == 'Manager1Amount') {
                $this->manager1Amount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'PlanningAmount') {
                $this->planningAmount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'Manager2Amount') {
                $this->manager2Amount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'Manager2CurrencyIDRef') {
                $this->manager2Currency = $value['value2'] == 'true';
            } else if ($value['value1'] == 'ClientAmount') {
                $this->clientAmount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'FinancialAmountInManager2Currency') {
                $this->financialAmountInManager2Currency = $value['value2'] == 'true';
            } else if ($value['value1'] == 'FinancialAmount') {
                $this->financialAmount = $value['value2'] == 'true';
            } else if ($value['value1'] == 'FinancialCurrencyIDRef') {
                $this->financialCurrency = $value['value2'] == 'true';
            } else if ($value['value1'] == 'ReceiptNumber') {
                $this->chequeNumber = $value['value2'] == 'true';
            } else if ($value['value1'] == 'ReceiveDateHijri') {
                $this->chequeDate = $value['value2'] == 'true';
            } else if ($value['value1'] == 'Receiver') {
                $this->reciver = $value['value2'] == 'true';
            } else if ($value['value1'] == 'TempNo') {
                $this->tempNo = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'AmountStatus') {
                $this->amountStatus = $value['value1'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'CashRequestTypeIDRef', 'value2' => ($this->requestType) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ControlVisible', 'value1' => 'btnSelectEPS', 'value2' => ($this->eps) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'CashRequestRecipientTypeIDRef', 'value2' => ($this->recipientType) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientName', 'value2' => ($this->recipient) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientContractNo', 'value2' => ($this->recipient) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientSubContractor', 'value2' => ($this->recipient) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'BudgetRow', 'value2' => ($this->budgetRow) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'BudgetTitle', 'value2' => ($this->budgetTitle) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'AmountDescription', 'value2' => ($this->paymentSubject) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientAccountType', 'value2' => ($this->accountType) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientAccountNumber', 'value2' => ($this->accountNumber) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientAccountBank', 'value2' => ($this->bank) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RecipientAccountName', 'value2' => ($this->name) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RequestedAmount', 'value2' => ($this->requestAmount) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RequestedCurrencyIDRef', 'value2' => ($this->requestCurrency) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Manager1Amount', 'value2' => ($this->manager1Amount) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'PlanningAmount', 'value2' => ($this->planningAmount) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Manager2Amount', 'value2' => ($this->manager2Amount) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Manager2CurrencyIDRef', 'value2' => ($this->manager2Currency) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ClientAmount', 'value2' => ($this->clientAmount) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'FinancialAmountInManager2Currency', 'value2' => ($this->financialAmountInManager2Currency) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'FinancialAmount', 'value2' => ($this->financialAmount) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'FinancialCurrencyIDRef', 'value2' => ($this->financialCurrency) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ReceiptNumber', 'value2' => ($this->chequeNumber) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ReceiveDateHijri', 'value2' => ($this->chequeDate) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Receiver', 'value2' => ($this->reciver) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'TempNo', 'value2' => ($this->tempNo) ? 'true' : 'false', 'description' => 'cash request', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = [
            'paramTitle' => 'showObsChilds',
            'value1' => 'cashRequest',
            'value2' => $this->showObsChilds,
            'description' => 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'AmountStatus',
            'value1' => $this->amountStatus,
            'value2' => null,
            'description' => 'cashrequest',
            'actionIdRef' => $actionId,
        ];
        //$transaction = Yii::$app->dbTehran->beginTransaction();
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
//                    ->andWhere(['value1' => $row['value1']])
                    ->andWhere(($row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                    ->exists();
            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
//                        ->andWhere(['value1' => $row['value1']])
                        ->andWhere(($row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    //$transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId .", ";
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
                    $actionParamCreateIds .= $actionParamModel->paramId .", ";
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
