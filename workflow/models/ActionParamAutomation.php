<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamAutomation extends \yii\base\Model {

    public $AutomationModelVisible;
    public $AutomationModelEditable;
    public $FirstName;
    public $LastName;
    public $Father;
    public $EmploymentType; 
    public $NationalCode; 
    public $Mobile; 
    public $Assistance; 
    public $Position; 
    public $PositionCount; 
    public $PositionTypeIDRef;
    public $OldFullName;
    public $HasAppointment;
    public $SequerityLevel; 
    public $AutomationType;
    public $DisableOldPosition;
    public $IsSettlement;
    public $File;

    public function rules() {
        return [
            [['AutomationModelVisible', 'AutomationModelEditable', 'FirstName', 'LastName', 'Father', 'EmploymentType', 'NationalCode', 'Mobile', 'Assistance', 'Position', 'PositionCount', 'PositionTypeIDRef', 'OldFullName', 'HasAppointment', 'SequerityLevel', 'AutomationType', 'DisableOldPosition', 'IsSettlement', 'File'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'AutomationModelVisible' => 'Visible',
            'AutomationModelEditable' => 'Editable',
        ];
    }

    public function find($actionId) {
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'AutomationModelVisible' && $value['value1'] == 'Automation') {
                $this->AutomationModelVisible = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'AutomationModelEditable' && $value['value1'] == 'Automation') {
                $this->AutomationModelEditable = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'FirstName') { 
                $this->FirstName = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'LastName') {
                $this->LastName = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'Father') {
                $this->Father = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'EmploymentType') {
                $this->EmploymentType = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'NationalCode') {
                $this->NationalCode = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'Mobile') {
                $this->Mobile = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'Assistance') {
                $this->Assistance = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'Position') {
                $this->Position = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'OldFullName') {
                $this->OldFullName = $value['value2'] == 'true';
            } /*else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'PositionCount') {
                $this->PositionCount = $value['value2'] == 'true';
            } 
            else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'PositionTypeIDRef') {
                $this->PositionTypeIDRef = $value['value2'] == 'true';
            } 
            else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'HasAppointment') {
                $this->HasAppointment = $value['value2'] == 'true';
            } */else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'SequerityLevel') {
                $this->SequerityLevel = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'AutomationType') {
                $this->AutomationType = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'DisableOldPosition') {
                $this->DisableOldPosition = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'IsSettlement') {
                $this->IsSettlement = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'File') {
                $this->File = $value['value2'] == 'true';
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = ['paramTitle' => 'AutomationModelVisible', 'value1' => 'Automation', 'value2' => ($this->AutomationModelVisible) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'AutomationModelEditable', 'value1' => 'Automation', 'value2' => ($this->AutomationModelEditable) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'FirstName', 'value2' => ($this->FirstName) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'LastName', 'value2' => ($this->LastName) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Father', 'value2' => ($this->Father) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'EmploymentType', 'value2' => ($this->EmploymentType) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'NationalCode', 'value2' => ($this->NationalCode) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Mobile', 'value2' => ($this->Mobile) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Assistance', 'value2' => ($this->Assistance) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Position', 'value2' => ($this->Position) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'OldFullName', 'value2' => ($this->OldFullName) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
//        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'PositionCount', 'value2' => ($this->PositionCount) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
//        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'PositionTypeIDRef', 'value2' => ($this->PositionTypeIDRef) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
//        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'HasAppointment', 'value2' => ($this->HasAppointment) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'SequerityLevel', 'value2' => ($this->SequerityLevel) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'AutomationType', 'value2' => ($this->AutomationType) ? 'true' : 'false',  'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'DisableOldPosition', 'value2' => ($this->DisableOldPosition) ? 'true' : 'false',  'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'IsSettlement', 'value2' => ($this->IsSettlement) ? 'true' : 'false',  'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'File', 'value2' => ($this->File) ? 'true' : 'false',  'description' => 'automation', 'actionIdRef' => $actionId];
        
        $transaction = Yii::$app->dbTehran->beginTransaction();
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->andWhere(['value1' => $row['value1']])
                    ->exists();
            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->andWhere(['value1' => $row['value1']])
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
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
                    $actionParamCreateIds .= $actionParamModel->paramId . ", ";
                } else {
                    $transaction->rollback();
                    return false;
                }
            }
        }
        $transaction->commit();
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendActionParamCreateActitvityLog($actionParamCreateIds);

        $activityManagement1 = new \app\components\ActivityManagement();
        $activityManagement1->sendActionParamUpdateActitvityLog($actionParamUpdateIds);

        return true;
    }

}
