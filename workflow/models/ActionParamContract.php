<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamContract extends \yii\base\Model {

    public $preContractVisible;
    public $preContractEditable;
    public $contractVisible;
    public $contractEditable;
    public $riderVisible;
    public $riderEditable;
    public $budgetVisible;
    public $budgetEditable;
    public $invoiceProcessVisible;
    public $invoiceProcessEditable;
    public $pmsPlanVisible;
    public $pmsPlanEditable;
    public $financialCodeVisible;
    public $financialCodeEditable;
    public $warantiesVisible;
    public $warantiesEditable;
    public $closingGradeVisible;
    public $closingGradeEditable;
    public $contractNo; // --> ContractNo
    public $contractSubject; // -> ContractSubject
    public $startDate; // -> [ContractDate,ContractDateHijri]
    public $subcontractor; // --> [SubContractorPersonTitle,chkAddNewSubContractor,btnSelectSubContractorPerson,fraPersonType]
    public $category; // --> ContractCategory
    public $type; // --> ContractType
    public $pms; // --> RequiredPMS
    public $plan; // --> RequiredPlan
    public $project; // --> btnNewProject
    public $eps; // --> [btnNewEPS,btnDeleteEPS]
    public $executionType; // --> btnNewExecutionType
    public $requiredContractNo;

    public function rules() {
        return [
            [['requiredContractNo', 'preContractVisible', 'preContractEditable', 'contractVisible', 'contractEditable', 'riderVisible', 'riderEditable', 'budgetVisible', 'budgetEditable', 'invoiceProcessVisible', 'invoiceProcessEditable', 'pmsPlanVisible', 'pmsPlanEditable', 'financialCodeVisible', 'financialCodeEditable', 'warantiesVisible', 'closingGradeVisible', 'closingGradeEditable', 'warantiesEditable', 'contractNo', 'contractSubject', 'startDate', 'subcontractor', 'category', 'type', 'pms', 'plan', 'eps', 'project', 'executionType'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'preContractVisible' => 'Visible',
            'preContractEditable' => 'Editable',
            'contractVisible' => 'Visible',
            'contractEditable' => 'Editable',
            'riderVisible' => 'Visible',
            'riderEditable' => 'Editable',
            'budgetVisible' => 'Visible',
            'budgetEditable' => 'Editable',
            'invoiceProcessVisible' => 'Visible',
            'invoiceProcessEditable' => 'Editable',
            'pmsPlanVisible' => 'Visible',
            'pmsPlanEditable' => 'Editable',
            'financialCodeVisible' => 'Visible',
            'financialCodeEditable' => 'Editable',
            'warantiesVisible' => 'Visible',
            'warantiesEditable' => 'Editable',
            'closingGradeVisible' => 'Visible',
            'closingGradeEditable' => 'Editable',
            'requiredContractNo' => 'Required ContractNo',
        ];
    }

    public function find($actionId) {
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step0') {
                $this->preContractVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step0') {
                $this->preContractEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step1') {
                $this->contractVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'requiredContractNo') {
                $this->requiredContractNo = $value['value1'];
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step1') {
                $this->contractEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step2') {
                $this->riderVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step2') {
                $this->riderEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step3') {
                $this->budgetVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step3') {
                $this->budgetEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step4') {
                $this->invoiceProcessVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step4') {
                $this->invoiceProcessEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step5') {
                $this->pmsPlanVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step5') {
                $this->pmsPlanEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step6') {
                $this->financialCodeVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step6') {
                $this->financialCodeEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step7') {
                $this->warantiesVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step7') {
                $this->warantiesEditable = $value['value2'] == '1';
            }else if ($value['paramTitle'] == 'ContractStepVisible' && $value['value1'] == 'Step8') {
                $this->closingGradeVisible = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'ContractStepEditable' && $value['value1'] == 'Step8') {
                $this->closingGradeEditable = $value['value2'] == '1';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'ContractNo') {
                $this->contractNo = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'ContractSubject') {
                $this->contractSubject = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'ContractDate') {
                $this->startDate = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'SubContractorPersonTitle') {
                $this->subcontractor = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'ContractCategory') {
                $this->category = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'ContractType') {
                $this->type = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'RequiredPMS') {
                $this->pms = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'RequiredPlan') {
                $this->plan = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'btnNewEPS') {
                $this->eps = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'btnNewProject') {
                $this->project = $value['value2'] == 'true';
            } else if ($value['paramTitle'] == 'fieldEnabled' && $value['value1'] == 'btnNewExecutionType') {
                $this->executionType = $value['value2'] == 'true';
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step0', 'value2' => ($this->preContractVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step0', 'value2' => ($this->preContractEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step1', 'value2' => ($this->contractVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step1', 'value2' => ($this->contractEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step2', 'value2' => ($this->riderVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step2', 'value2' => ($this->riderEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step3', 'value2' => ($this->budgetVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step3', 'value2' => ($this->budgetEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step4', 'value2' => ($this->invoiceProcessVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step4', 'value2' => ($this->invoiceProcessEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step5', 'value2' => ($this->pmsPlanVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step5', 'value2' => ($this->pmsPlanEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step6', 'value2' => ($this->financialCodeVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step6', 'value2' => ($this->financialCodeEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step7', 'value2' => ($this->warantiesVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step7', 'value2' => ($this->warantiesEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepVisible', 'value1' => 'Step8', 'value2' => ($this->closingGradeVisible) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'ContractStepEditable', 'value1' => 'Step8', 'value2' => ($this->closingGradeEditable) ? '1' : '0', 'value4' => 'frmToDoList_WorkflowContractEdit', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ContractNo', 'value2' => ($this->contractNo) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ContractSubject', 'value2' => ($this->contractSubject) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ContractDate', 'value2' => ($this->startDate) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ContractDateHijri', 'value2' => ($this->startDate) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'SubContractorPersonTitle', 'value2' => ($this->subcontractor) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'chkAddNewSubContractor', 'value2' => ($this->subcontractor) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'btnSelectSubContractorPerson', 'value2' => ($this->subcontractor) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'fraPersonType', 'value2' => ($this->subcontractor) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ContractCategory', 'value2' => ($this->category) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'ContractType', 'value2' => ($this->type) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RequiredPMS', 'value2' => ($this->pms) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'RequiredPlan', 'value2' => ($this->plan) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'btnNewProject', 'value2' => ($this->project) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'btnNewEPS', 'value2' => ($this->eps) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'btnDeleteEPS', 'value2' => ($this->eps) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'fieldEnabled', 'value1' => 'btnNewExecutionType', 'value2' => ($this->executionType) ? 'true' : 'false', 'value3' => 'frmToDoList_WorkflowContractEdit', 'value4' => 'frmToDoList_WorkflowContractEdit_Step1_Subform', 'description' => 'contract', 'actionIdRef' => $actionId,];
        $rows[count($rows)] = ['paramTitle' => 'requiredContractNo', 'value1' => $this->requiredContractNo, 'value2' => NULL, 'value3' => NULL, 'value4' => NULL, 'description' => 'contract', 'actionIdRef' => $actionId,];

        //$transaction = Yii::$app->dbTehran->beginTransaction();
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
