<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamNetwork extends \yii\base\Model {

    public $NetworkModelVisible;
    public $NetworkModelEditable;
    public $FirstName;
    public $LastName; 
    public $Father;
    public $EmploymentType;
    public $NationalCode; 
    public $Mobile;
    public $Position;
    public $NetworkType;
    public $KosarNetwork;
    public $SecureNetwork;
    public $File;
    public $InternalUsername;
    public $SecureUsername;
    public $KosarUsername;
    public $InternetUsername;
    public $OrganizationEmailUsername;
    public $showObsChilds;

    public function rules() {
        return [
            [['NetworkModelVisible', 'NetworkModelEditable', 'FirstName', 'LastName', 'Father', 'EmploymentType', 'NationalCode', 'Mobile', 'Position', 'NetworkType', 'KosarNetwork', 'SecureNetwork', 'File', 'InternalUsername', 'SecureUsername', 'KosarUsername', 'InternetUsername', 'OrganizationEmailUsername', 'showObsChilds'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'NetworkModelVisible' => 'Visible',
            'NetworkModelEditable' => 'Editable',
        ];
    }

    public function find($actionId) {
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'NetworkModelVisible' && $value['value1'] == 'Network') {
                $this->NetworkModelVisible = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'NetworkModelEditable' && $value['value1'] == 'Network') {
                $this->NetworkModelEditable = $value['value2'] == 'true';
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
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'Position') {
                $this->Position = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'NetworkType') {
                $this->NetworkType = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'KosarNetwork') {
                $this->KosarNetwork = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'SecureNetwork') {
                $this->SecureNetwork = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'File') {
                $this->File = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'InternalUsername') {
                $this->InternalUsername = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'SecureUsername') {
                $this->SecureUsername = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'KosarUsername') {
                $this->KosarUsername = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'InternetUsername') {
                $this->InternetUsername = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'OrganizationEmailUsername') {
                $this->OrganizationEmailUsername = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = ['paramTitle' => 'NetworkModelVisible', 'value1' => 'Network', 'value2' => ($this->NetworkModelVisible) ? 'true' : 'false', 'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'NetworkModelEditable', 'value1' => 'Network', 'value2' => ($this->NetworkModelEditable) ? 'true' : 'false', 'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'FirstName', 'value2' => ($this->FirstName) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'LastName', 'value2' => ($this->LastName) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Father', 'value2' => ($this->Father) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'EmploymentType', 'value2' => ($this->EmploymentType) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'NationalCode', 'value2' => ($this->NationalCode) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Mobile', 'value2' => ($this->Mobile) ? 'true' : 'false', 'description' => 'automation', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'Position', 'value2' => ($this->Position) ? 'true' : 'false', 'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'NetworkType', 'value2' => ($this->NetworkType) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'KosarNetwork', 'value2' => ($this->KosarNetwork) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'SecureNetwork', 'value2' => ($this->SecureNetwork) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'File', 'value2' => ($this->File) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'InternalUsername', 'value2' => ($this->InternalUsername) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'SecureUsername', 'value2' => ($this->SecureUsername) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'KosarUsername', 'value2' => ($this->KosarUsername) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'InternetUsername', 'value2' => ($this->InternetUsername) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'OrganizationEmailUsername', 'value2' => ($this->OrganizationEmailUsername) ? 'true' : 'false',  'description' => 'network', 'actionIdRef' => $actionId];
        $rows[count($rows)] = ['paramTitle' => 'showObsChilds', 'value1' => 'network', 'value2' => $this->showObsChilds, 'description' => 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند', 'actionIdRef' => $actionId];
        
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
