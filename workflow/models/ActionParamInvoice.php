<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamInvoice extends \yii\base\Model {

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
    public $invoiceInfo;
    public $invoiceAmount;
    public $invoiceDocument;
    public $amountStatus;
    public $invoiceNumber;
    public $revisionNumber;
    public $cutOfDate;
    public $description;
    public $tempNumber;
    public $financialDocDate;
    public $se2Field;
    public $se3Field;
    public $showObsChilds;

    public function rules() {
        return [
            [['invoiceInfo', 'invoiceAmount', 'invoiceDocument', 'amountStatus', 'invoiceNumber', 'revisionNumber', 'cutOfDate', 'description', 'tempNumber', 'financialDocDate', 'se2Field', 'se3Field', 'showObsChilds'], 'integer'],
            [['invoiceInfo', 'invoiceAmount', 'invoiceDocument', 'amountStatus'], 'required'],
        ];
    }

    public function find($actionId) {
        //@TODO: hasChange and isDelete 
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])
                        /* ->andWhere('isnull(isDeleted,0)<>1') */->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'invoiceAccessId') {
                $val = str_split($value['value1']);
                $this->invoiceInfo = $val[2];
                $this->invoiceAmount = $val[1];
                $this->invoiceDocument = $val[0];
            } else if ($value['paramTitle'] == 'AmountStatus') {
                $this->amountStatus = $value['value1'];
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            } else if ($value['paramTitle'] == 'fieldEnabled') {
                if ($value['value1'] == 'title') {
                    $this->invoiceNumber = $value['value2'] == 'true';
                } else if ($value['value1'] == 'CutOffDate') {
                    $this->cutOfDate = $value['value2'] == 'true';
                    //}else if ($value['value1'] == 'CutOffDateHijri'){
                } else if ($value['value1'] == 'financialDocNumber') {
                    $this->tempNumber = $value['value2'] == 'true';
                } else if ($value['value1'] == 'financialDocDateHijri') {
                    $this->financialDocDate = $value['value2'] == 'true';
                } else if ($value['value1'] == 'approved1UsdF2') {
                    $this->se2Field = $value['value2'] == 'true';
//                }else if ($value['value1'] == 'approved2UsdF2'){
//                }else if ($value['value1'] == 'approved1IrrF2'){
//                }else if ($value['value1'] == 'approved2IrrF2'){
                } else if ($value['value1'] == 'approved1UsdF3') {
                    $this->se3Field = $value['value2'] == 'true';
//                }else if ($value['value1'] == 'approved2UsdF3'){
//                }else if ($value['value1'] == 'approved1IrrF3'){
//                }else if ($value['value1'] == 'approved2IrrF3'){
                } else if ($value['value1'] == 'RevisionNumber') {
                    $this->revisionNumber = $value['value2'] == 'true';
                } else if ($value['value1'] == 'Description') {
                    $this->description = $value['value2'] == 'true';
                }
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'invoiceAccessId',
            'value1' => $this->invoiceDocument . $this->invoiceAmount . $this->invoiceInfo,
            'value2' => null,
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'AmountStatus',
            'value1' => $this->amountStatus,
            'value2' => null,
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'title',
            'value2' => ($this->invoiceNumber) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'CutOffDate',
            'value2' => ($this->cutOfDate) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'CutOffDateHijri',
            'value2' => ($this->cutOfDate) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'financialDocNumber',
            'value2' => ($this->tempNumber) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'financialDocDateHijri',
            'value2' => ($this->financialDocDate) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved1UsdF2',
            'value2' => ($this->se2Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved2UsdF2',
            'value2' => ($this->se2Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved1IrrF2',
            'value2' => ($this->se2Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved2IrrF3',
            'value2' => ($this->se2Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved1UsdF3',
            'value2' => ($this->se3Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved2UsdF3',
            'value2' => ($this->se3Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved1IrrF3',
            'value2' => ($this->se3Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'approved2IrrF3',
            'value2' => ($this->se3Field) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'RevisionNumber',
            'value2' => ($this->revisionNumber) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'fieldEnabled',
            'value1' => 'Description',
            'value2' => ($this->description) ? 'true' : 'false',
            'description' => 'invoice',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'showObsChilds',
            'value1' => 'invoice',
            'value2' => $this->showObsChilds,
            'description' => 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند',
            'actionIdRef' => $actionId,
        ];
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        //$transaction = Yii::$app->dbTehran->beginTransaction();
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->andWhere(($row['paramTitle'] != 'invoiceAccessId' && $row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                    ->exists();

            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->andWhere(($row['paramTitle'] != 'invoiceAccessId' && $row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
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
