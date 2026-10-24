<?php

namespace app\modules\workflow\models;

use app\components\Utility;
use app\modules\contract\models\ContractMeasureValue;
use app\modules\invoice\models\InvoiceAmountEdit;
use app\modules\invoice\models\InvoiceDocumentRequireModel;
use app\modules\invoice\models\InvoiceEdit;
use app\modules\contract\models\Contract;
use app\modules\contract\models\ContractDocument;
use Yii;
use app\modules\invoice\models\InvoicePksk;
use app\modules\workflowContract\models\WorkflowContract;
use app\modules\workflowContract\models\WorkflowContractBudget;
use app\components\jDateTime;
use app\modules\cashRequest\models\CashRequestAmount;
use app\modules\budget\models\Budget;
use app\modules\tender\models\TenderDocumentRequireDocumentType;
use app\modules\document\models\StampDocument;
use app\modules\workflow\models\ActionEntity;

class ProcessEntity extends \yii\db\ActiveRecord {

    public $actionTitle;

    /**
     * for model ProcessEntityCapitalSearch.php
     * @var type 
     * @author <mail:mortezakml@gmail.com & Tel:09366263397>
     */
    public $balanceAmounts;
    public $obsTitle_475; // Used ProcessEntityBudgetSearch

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_process_entity';
        } else {
            return 'WorkFlow.tblProcessEntity';
        }
    }

    public static function primaryKey() {
        return ["ProcessEntityID"];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function getColumns() {
        return array('ProcessEntityID', 'ProcessIDRef', 'LastUserIDRef', 'LastActionIDRef', 'LastActionDateHijri', 'LastActionMessage', 'LastActionAutoMessage', 'InvoiceIDRef', 'CashRequestIDRef', 'WorkflowContractIDRef', 'BudgetIDRef', 'warrantyIdRef', 'debitmemoIdRef', 'Status', 'hasChange', 'PersonID', 'TenderIDRef', 'ContractClosingDocumentIDRef');
    }

    public function rules() {
        return [
            [['ProcessEntityID', 'ProcessIDRef'], 'required'],
            [['ProcessEntityID', 'ProcessIDRef', 'LastUserIDRef', 'LastActionIDRef', 'InvoiceIDRef', 'CashRequestIDRef', 'WorkflowContractIDRef', 'BudgetIDRef', 'Status', 'parentProcessEntityIDRef', 'capitalBalanceIdRef', 'warrantyIdRef', 'debitmemoIdRef', 'hasChange', 'PersonID', 'TenderIDRef', 'ContractClosingDocumentIDRef'], 'integer'],
            [['ProcessEntityID', 'ProcessIDRef', 'LastUserIDRef', 'LastActionIDRef'], 'integer', 'min' => 1],
            [['LastActionDateHijri', 'LastActionMessage', 'LastActionAutoMessage'], 'string'],
            [['ProcessEntityID', 'ProcessIDRef', 'LastUserIDRef', 'LastActionIDRef', 'LastActionDateHijri', 'LastActionMessage', 'LastActionAutoMessage', 'InvoiceIDRef', 'CashRequestIDRef', 'WorkflowContractIDRef', 'BudgetIDRef', 'Status', 'warrantyIdRef', 'TenderIDRef', 'ContractClosingDocumentIDRef'],
                'safe', 'on' => 'search']
        ];
    }

    public function attributeLabels() {
        return [
            'WorkflowContractID' => Yii::t('contract', 'ID'),
            'ContractIDRef' => Yii::t('contract', 'Contract ID'),
            'personTitle' => Yii::t('contract', 'Client'),
            'ContractNo' => Yii::t('contract', 'Contract No'),
            'ContractSubject' => Yii::t('contract', 'Contract Subject'),
            'SubContractorPersonTitle' => Yii::t('contract', 'Sub Contractor'),
            'ActionTitle2' => Yii::t('contract', 'Last Action'),
            'UserName' => Yii::t('workflow', 'Last User'),
            'RevisionNumber' => Yii::t('workflow', 'Rev.No'),
            'ContractRevisionType' => Yii::t('workflow', 'Revision Type'),
            'ProcessEntityID' => Yii::t('workflow', 'ProcessEntityID'),
            'ProcessIDRef' => Yii::t('workflow', 'ProcessIDRef'),
            'LastUserIDRef' => Yii::t('workflow', 'LastUserIDRef'),
            'LastActionIDRef' => Yii::t('workflow', 'LastActionIDRef'),
            'LastActionDateHijri' => Yii::t('workflow', 'LastActionDate'),
            'LastActionMessage' => Yii::t('workflow', 'LastActionMessage'),
            'LastActionAutoMessage' => Yii::t('workflow', 'Last Action AutoMessage'),
            'InvoiceIDRef' => Yii::t('workflow', 'InvoiceIDRef'),
            'CashRequestIDRef' => Yii::t('workflow', 'Cash Request ID'),
            'WorkflowContractIDRef' => Yii::t('workflow', 'WorkflowContractIDRef'),
            'BudgetIDRef' => Yii::t('workflow', 'BudgetIDRef'),
            'warrantyIdRef' => Yii::t('workflow', 'warrantyIdRef'),
            'debitmemoIdRef' => Yii::t('workflow', 'debitmemoIdRef'),
            'ContractClosingDocumentIDRef' => Yii::t('workflow', 'ContractClosingDocumentIDRef'),
            'Status' => Yii::t('workflow', 'Status'),
            'lastActionTitle' => Yii::t('workflow', 'Last Action'),
            'exportId' => Yii::t('workflow', 'ID'),
            'RequestDateHijri' => Yii::t('workflow', 'Request Date'),
            'Description' => Yii::t('workflow', 'Description'),
            'lastUserName' => Yii::t('workflow', 'Last User'),
            'ActionMessage' => Yii::t('workflow', 'Action Message'),
        ];
    }

    public static function graphHeaderInfo($peid) {
        $processEntity = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->where(['ProcessEntityID' => $peid])->one();
        if ($processEntity->InvoiceIDRef > 0) {
            return [
                'Invoice No' => $processEntity->invoice->Title,
                'Cut Off Date' => $processEntity->invoice->CutOffDateHijri,
                'Sub Contractor' => $processEntity->invoice->contract->subContractor->PersonTitle,
                'Contract No' => $processEntity->invoice->contract->ContractNo,
                'Contract Subject' => $processEntity->invoice->contract->ContractSubject,
            ];
        } else if ($processEntity->CashRequestIDRef > 0) {
            return [
                '' => '<span class="badge badge-purple">ID:' . $processEntity->CashRequestIDRef . '</span> &nbsp;&nbsp;' . $processEntity->cashrequest->Description
            ];
        } else if ($processEntity->WorkflowContractIDRef > 0) {
            return [
                'Sub Contractor' => $processEntity->contract->SubContractorPersonTitle,
                'Contract No' => $processEntity->contract->ContractNo,
                'Contract Subject' => $processEntity->contract->ContractSubject,
            ];
        } else if ($processEntity->warrantyIdRef > 0) {
            return [
                '' => 'متن آزمایشی'
            ];
        } else if ($processEntity->BudgetIDRef > 0) {
            return [
                '' => $processEntity->budget->SystemDescription
            ];
        } else {
            return [
                '' => 'Error'
            ];
        }
    }

    public static function workflowToDoCount() {
        $uid = Yii::$app->session->get('UserID');
        $obsId = Yii::$app->session->get('ObsPosition');
        $sql = "select * from user_table_todo_count where userIdRef=$uid and obsIdRef=$obsId;";

        $processUserObs = Yii::$app->dbTehran->createCommand($sql)->queryAll();
        $data[] = [];
        foreach ($processUserObs as $key => $value) {
            $data[$value['moduleIdRef']] = $value['count'];
        }

        $result['invoice'] = isset($data[1]) ? $data[1] : 0;
        $result['cash-request'] = isset($data[2]) ? $data[2] : 0;
        $result['contract'] = isset($data[3]) ? $data[3] : 0;
        $result['budget'] = isset($data[4]) ? $data[4] : 0;
        $result['capital'] = isset($data[5]) ? $data[5] : 0;
        $result['warranty'] = isset($data[6]) ? $data[6] : 0;
        $result['debitmemo'] = isset($data[7]) ? $data[7] : 0;
        $result['claim'] = isset($data[8]) ? $data[8] : 0;
        $result['actual-progress-annual-budget'] = isset($data[9]) ? $data[9] : 0;
        $result['permission'] = isset($data[10]) ? $data[10] : 0;
        $result['sourcing'] = isset($data[11]) ? $data[11] : 0;
        $total = $result['invoice'] + $result['cash-request'] + $result['contract'] + $result['budget'] + $result['capital'] + $result['warranty'] + $result['debitmemo'] + $result['claim'] + $result['actual-progress-annual-budget'] + $result['permission'] + $result['sourcing'];
        $result['total'] = $total > 0 ? $total : 0;
//        $result['total'] = $result['invoice'] + $result['cash-request'] + $result['contract'] + $result['budget'] + $result['capital'] + $result['warranty'] + $result['debitmemo'] + $result['claim'] + $result['actual-progress-annual-budget'] + $result['permission'] + $result['sourcing'];
        return \yii\helpers\Json::encode($result);
    }

    public static function workflowToDoCount_old_delete() {

        $result['invoice'] = \app\modules\workflow\models\ProcessEntity::find()->invoice('ToDo')->count('*');
        $result['cash-request'] = \app\modules\workflow\models\ProcessEntity::find()->cashRequest('ToDo')->count('*');
        $result['contract'] = \app\modules\workflow\models\ProcessEntity::find()->contract('ToDo')->count('*');
        $result['warranty'] = \app\modules\workflow\models\ProcessEntity::find()->warranty('ToDo')->count('*');
        $result['budget'] = \app\modules\workflow\models\ProcessEntity::find()->budget('ToDo')->count('*');
        $result['capital'] = \app\modules\workflow\models\ProcessEntity::find()->capital('ToDo')->count('*');
        $result['debitmemo'] = \app\modules\workflow\models\ProcessEntity::find()->debitmemo('ToDo')->count('*');
        $result['claim'] = \app\modules\workflow\models\ProcessEntity::find()->claim('ToDo')->count('*');
        $result['actual-progress-annual-budget'] = \app\modules\workflow\models\ProcessEntity::find()->actualProgress('ToDo')->count('*');
        $result['permission'] = \app\modules\workflow\models\ProcessEntity::find()->permission('ToDo')->count('*');
        $result['sourcing'] = \app\modules\workflow\models\ProcessEntity::find()->sourcing('ToDo')->count('*');

        $result['total'] = $result['cash-request'] + $result['invoice'] + $result['contract'] + $result['warranty'] + $result['budget'] + $result['capital'] + $result['debitmemo'] + $result['claim'] + $result['actual-progress-annual-budget'] + $result['permission'] + $result['sourcing'];
        return \yii\helpers\Json::encode($result);
    }

    private Static function canNextActionCapital($model, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $result = [
            'result' => true,
            'message' => [],
        ];
        $capitalModel = $model->capital;

        if (!isset($capitalModel)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "can't find Capital model!!");
        }


        $checkHasSignature = \app\modules\account\models\UserObs::find()
                ->andWhere(["UserIDRef" => $uid])
                ->andWhere(["OBSCategory3IDRef" => $obp])
                ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
                ->exists();
        if (!$checkHasSignature) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'You are don`t have any signature!!');
            return $result;
        }

        if ($model->LastActionIDRef != $currentaction) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }

        // درج اجباری داکیومنت
        if ($currentaction == 537 && in_array($toaction, [538, 855])) {
            $capitalDocumentTable = \app\modules\capital\models\CapitalBalanceDocument::tableName();

            $count = \app\modules\capital\models\CapitalBalanceDocument::find()
                    ->andWhere(['capitalBalanceIdRef' => $model->capitalBalanceIdRef])
                    ->andWhere("$capitalDocumentTable.isDeleted is null or $capitalDocumentTable.isDeleted != 1")
                    ->count();
            if ($count == 0) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Upload Document before next action');
                return $result;
            }
        }

        $capitalId = $capitalModel->capitalBalanceId;
        $budgetTable = \app\modules\budget\models\Budget::tableName();
        $annualBudgetTable = \app\modules\budget\models\AnnualBudget::tableName();
        $budgetNodeTable = \app\modules\budget\models\BudgetNode::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $capitalBalanceAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
        $capitalBalanceTable = \app\modules\capital\models\CapitalBalance::tableName();
        $cashrequestTable = \app\modules\cashRequest\models\CashRequest::tableName();
        $cashrequestAmountTable = \app\modules\cashRequest\models\CashRequestAmount::tableName();
        $actionParamTable = \app\modules\workflow\models\ActionParam::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $planTable = \app\modules\budget\models\Plan::tableName();

        $amountStatusArray = '1, 5';
        $sqlCashrequestLastAmount = "sum($cashrequestAmountTable.LastAmount)";

        $capitalBalanceAmountQuery = (new \yii\db\Query())
                ->from($capitalBalanceAmountTable)
                ->addSelect([
                    "($planTable.PlanAmount - (case 
                        when $budgetTable.PlanIDRef>0 then COALESCE((SELECT sum(cba1.balanceAmount)"
                    . " FROM $capitalBalanceAmountTable as cba1"
                    . " inner join $capitalBalanceTable as cb1 on cb1.capitalBalanceId=cba1.capitalBalanceIdRef"
                    . " inner join $processEntityTable as pe1 on pe1.capitalBalanceIdRef=cb1.capitalBalanceId"
                    . " inner join $actionParamTable on $actionParamTable.actionIdRef=pe1.LastActionIDRef"
                    . " where cba1.budgetIdRef=" . Budget::tableName() . ".BudgetID and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
                        +COALESCE((select $sqlCashrequestLastAmount from $cashrequestAmountTable"
                    . " inner join $cashrequestTable on $cashrequestTable.CashRequestID=$cashrequestAmountTable.CashRequestIDRef"
                    . " inner join $processEntityTable as pe1 on pe1.CashRequestIDRef=$cashrequestTable.CashRequestID"
                    . " inner join $actionParamTable on $actionParamTable.actionIdRef=pe1.LastActionIDRef"
                    . " where SUBSTRING($cashrequestAmountTable.BudgetRow, 4)=" . Budget::tableName() . ".BudgetID and $cashrequestAmountTable.budgetType like 'OperatingBudget' and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
                        when $budgetTable.NewContractIDRef>0 then 0 
                        when $budgetTable.ContractIDRef>0 then (select sum(AmountApproved1) from $invoiceAmountTable "
                    . "inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID "
                    . "where $invoiceTable.InvoiceType in (1,3,5) "
                    . "and $budgetTable.ContractIDRef=$invoiceTable.ContractID "
                    . "and $invoiceAmountTable.AmountType=1 "
                    . "and $invoiceAmountTable.CostCode = $budgetNodeTable.NodeIDRef "
                    . "and $invoiceTable.CutOffDateHijri >=CONCAT($annualBudgetTable.Year,'/01/01') "
                    . "and $invoiceTable.CutOffDateHijri <CONCAT($annualBudgetTable.Year,'/12/30') "
                    . "and $invoiceTable.AmountStatus in ($amountStatusArray)) 
                        else 0 
                        end)) as remainBudget"
                ])
                ->innerJoin($budgetTable, "$budgetTable.BudgetID=$capitalBalanceAmountTable.BudgetIDRef")
                ->innerJoin($annualBudgetTable, "$annualBudgetTable.AnnualBudgetID=$budgetTable.AnnualBudgetIDRef")
                ->innerJoin($budgetNodeTable, "$budgetNodeTable.BudgetIDRef=$budgetTable.BudgetID")
                ->innerJoin($capitalBalanceTable, "$capitalBalanceTable.capitalBalanceId=$capitalBalanceAmountTable.capitalBalanceIdRef")
//                ->innerJoin($processEntityTable, "$processEntityTable.capitalBalanceIdRef=$capitalBalanceTable.capitalBalanceId")
                ->innerJoin($planTable, "$budgetTable.PlanIDRef=$planTable.PlanID")
                ->where("$capitalBalanceAmountTable.capitalBalanceIdRef=$capitalId")
                ->andWhere("$annualBudgetTable.capitalProcessType=1")
                ->andWhere("$budgetTable.isDeleted is null or $budgetTable.isDeleted<>1")
                ->andWhere("$budgetNodeTable.isDeleted is null or $budgetNodeTable.isDeleted<>1")
                ->andWhere("$capitalBalanceAmountTable.isDeleted is null or  $capitalBalanceAmountTable.isDeleted != 1");

        $count = (new \yii\db\Query())
                ->from(['subQuery' => "(" . $capitalBalanceAmountQuery->createCommand()->rawSql . ")"])
                ->andWhere("subQuery.remainBudget<0")
                ->count();

//        echo $count;
        if ($count > 0) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'Remain Budget of one or mor plan is less than zero in this next action.');
        }
        return $result;
    }

    private Static function canNextActionDebitmemo($model, $currentaction, $toaction) {
        $result = [
            'result' => true,
            'message' => [],
        ];
        $debitmemoModel = $model->debitmemo;

        if (!isset($debitmemoModel)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "can't find debitmemo model!!");
        }

//        ّبررسی خالی نبودن فیلد های داکیومنت برای اعلام بدهی در اکشن هایی که در اکشن پارام تعیین کرده ایم
// فقط در صورت برفتن به اکشن جلویی       
        $currentActionModel = Action::find()
                ->where(['ActionID' => $currentaction])
                ->one();
        $toActionModel = Action::find()
                ->where(['ActionID' => $toaction])
                ->one();

        if ($currentActionModel->ActionOrder < $toActionModel->ActionOrder) {

            $actioParamModel = ActionParam::find()
                    ->where(['ActionIDRef' => $currentaction, 'paramTitle' => 'debitmemo_financialDocNumber_required'])
                    ->one();
            if ($actioParamModel->value1) {
                if (!isset($debitmemoModel->financialDocNumber) || $debitmemoModel->financialDocNumber == '') {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', "Please Fill Financial DocNumber!!");
                }
            }

            $actioParamModel = ActionParam::find()
                    ->where(['ActionIDRef' => $currentaction, 'paramTitle' => 'debitmemo_financialDocDateHijri_required'])
                    ->one();

            if ($actioParamModel->value1) {
                if (!isset($debitmemoModel->financialDocDateHijri) || $debitmemoModel->financialDocDateHijri == '') {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', "Please Fill Financial DocDateHijri!!");
                }
            }
        }


        // درج اجباری داکیومنت
        if (($currentaction == 762 && $toaction == 763) || ($currentaction == 747 && $toaction == 748) || ($currentaction == 756 && $toaction == 757)) {
            $debitmemoTable = \app\modules\debitmemo\models\DebitmemoDocument::tableName();

            $count = \app\modules\debitmemo\models\DebitmemoDocument::find()
                    ->andWhere(['debitmemoIdRef' => $model->debitmemoIdRef])
                    ->andWhere("$debitmemoTable.isDeleted is null or $debitmemoTable.isDeleted != 1")
                    ->count();
            if ($count == 0) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Upload Document before next action');
            }
        }


        $invoiceAllModels = InvoiceEdit::find()->debitmemo($model->debitmemoIdRef)->all();

// پیدا کردن بزرگ ترین تاریخ صورت وضعیت های
        $maxCutOffDateHijri = '1350/03/15';
        foreach ($invoiceAllModels as $invoiceModel) {
            if ($maxCutOffDateHijri < $invoiceModel->CutOffDateHijri) {
                $maxCutOffDateHijri = $invoiceModel->CutOffDateHijri;
                $maxInvoiceModel = $invoiceModel;
            }
        }
        if ($maxCutOffDateHijri == '1350/03/15') {
            $result['result'] = false;
            $result['message'][] = "this debitmemo don't has any Invoice";
            return $result;
        } else {

            if (round($maxInvoiceModel->cumulativeApproved1(5), 2) < (round($model->debitmemo->cumulativeApproved1ByInvoiceCutoffDate(6, $maxInvoiceModel), 2))) {
                $message = "<div style='direction: rtl;'>";
                $message .= "مبلغ تجمعی اعلامیه ها تا دوره کارکرد";
                $message .= $maxCutOffDateHijri;
                $message .= "برابر است با ";
                $message .= Utility::moneyFormat($model->debitmemo->cumulativeApproved1ByInvoiceCutoffDate(6, $maxInvoiceModel));
                $message .= ' (' . $maxInvoiceModel->currency1->CurrencyTitle . ') ';
                $message .= " و بیشتر از مبلغ تجمعی صورت وضعیت ها تا همان دوره کارکرد برابر ";
                $message .= Utility::moneyFormat($maxInvoiceModel->cumulativeApproved1(5));
                $message .= ' (' . $maxInvoiceModel->currency1->CurrencyTitle . ') ';
                $message .= "می باشدامکان ارسال وجود ندارد؟";
                $message .= "</div>";

                $result['result'] = false;
                $result['message'][] = $message;
//                var_dump(round(floatval($maxInvoiceModel->cumulativeApproved2(5)), 2));
//                var_dump($model->debitmemo->cumulativeApproved1ByInvoiceCutoffDate(6, $maxInvoiceModel));
                return $result;
            }
            if ($invoiceModel->Currency2IDRef > 0) {
                if (round($maxInvoiceModel->cumulativeApproved2(5), 2) < (round($model->debitmemo->cumulativeApproved2ByInvoiceCutoffDate(6, $maxInvoiceModel), 2))) {
                    $message = "<div style='direction: rtl;'>";
                    $message .= "مبلغ تجمعی اعلامیه ها تا دوره کارکرد";
                    $message .= $maxCutOffDateHijri;
                    $message .= "برابر است با ";
                    $message .= Utility::moneyFormat($model->debitmemo->cumulativeApproved2ByInvoiceCutoffDate(6, $maxInvoiceModel));
                    $message .= ' (' . $maxInvoiceModel->currency2->CurrencyTitle . ') ';
                    $message .= " و بیشتر از مبلغ تجمعی صورت وضعیت ها تا همان دوره کارکرد برابر ";
                    $message .= Utility::moneyFormat($maxInvoiceModel->cumulativeApproved2(5));
                    $message .= ' (' . $maxInvoiceModel->currency2->CurrencyTitle . ') ';
                    $message .= "می باشدامکان ارسال وجود ندارد؟";
                    $message .= "</div>";

                    $result['result'] = false;
                    $result['message'][] = $message;
                    return $result;
                }
            }
        }

        // الزام داکیومنت 
        if ($model->LastActionIDRef == 813 && $toaction == 814) {
            $count = \app\modules\debitmemo\models\DebitmemoDocument::find()
                    ->where(['debitmemoIdRef' => $model->debitmemoIdRef])
                    ->andWhere("isDeleted is null or isDeleted != 1")
                    ->count();
            if ($count == 0) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Upload Document before next action');
            }
        }
        return $result;
    }

    private Static function canNextActionTender($model, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $result = [
            'result' => true,
            'message' => [],
        ];
        $tenderModel = $model->tender;

        if (!isset($tenderModel)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "can't find Tender model!!");
        }


        /* $checkHasSignature = \app\modules\account\models\UserObs::find()
          ->andWhere(["UserIDRef" => $uid])
          ->andWhere(["OBSCategory3IDRef" => $obp])
          ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
          ->exists();
          if (!$checkHasSignature) {
          $result['result'] = false;
          $result['message'][] = Yii::t('workflow', 'You are don`t have any signature!!');
          return $result;
          } */

        if ($model->LastActionIDRef != $currentaction) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }

        //الزام درج داکیومنت
        $tenderDocumentTable = \app\modules\tender\models\TenderDocument::tableName();

        $noRequirementType = isset($tenderModel->noRequirementType) ? $tenderModel->noRequirementType : -1;
        $requireDocument = TenderDocumentRequireDocumentType::find()
                ->where(['fromAction' => $currentaction, 'toAction' => $toaction])
                ->andWhere(['tenderType' => $tenderModel->tenderType])
                ->andWhere(['RequireType' => 1])
                ->andWhere(['not in', 'TenderDocumentTypeIDRef', \app\modules\tender\models\TenderDocument::find()
            ->select('TenderDocumentTypeIDRef')
            ->andWhere(['TenderIDRef' => $model->TenderIDRef])
            ->andWhere("$tenderDocumentTable.isDeleted is null or $tenderDocumentTable.isDeleted!=1")
        ]);

        if ($noRequirementType > 0) {
            $requireDocument->andWhere(['noRequirementType' => $noRequirementType]);
        } else {
            $requireDocument->andWhere('noRequirementType is null');
        }
        $existRequireDocument = $requireDocument->one();
        if (isset($existRequireDocument)) {
            $message = $existRequireDocument->documentType->TenderDocumentTypeTitle;
            $result['result'] = false;
            $result['message'][] = "نوع سند $message ضمیمه نشده است";
            return $result;
        }

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $currentaction, 'NextActionIDRef' => $toaction])->one();
        $actionOrderParams = ActionOrderParam::find()->where(['actionOrderIdRef' => $actionOrderModel->ID])->all();
        foreach ($actionOrderParams as $actionParam) {
            if ($actionParam->paramTitle == 'requiredMinimumBidder' && ($tenderModel->tenderType == 4 || $tenderModel->tenderType == 5)) {
                // بررسی تعداد مناقصه گران با حدنصاب تعیین شده در پارام اکشن اردرها
                $bidderCount = \app\modules\tender\models\TenderBidder::find()->where(['tenderIDRef' => $model->TenderIDRef])->andWhere(['or', ['isDeleted' => null], ['<>', 'isDeleted', 1]]);

                if ($bidderCount->count() < $actionParam->value1) {
                    $result['result'] = false;
                    $result['message'][] = "تعداد مناقصه گران کمتر از$actionParam->value1  می باشد.";
                    return $result;
                }
            } else if ($actionParam->paramTitle == 'requiredTenderAttachment' && $actionParam->value1 == 'baselinePriceAttachment' && $actionParam->value2 == 1) {
                // بررسی ثبت شدن اسناد قیمت شاخص
                $baselinePriceTable = \app\modules\tender\models\TenderBaselinePrice::tableName();
                $baselinePriceAttachmentTable = \app\modules\tender\models\TenderBaselinePriceAttachment::tableName();
                $baselinePriceModel = \app\modules\tender\models\TenderBaselinePrice::find()
                        ->where(['TenderIDRef' => $model->TenderIDRef])
                        ->andWhere(['not', ['priceCurrency1' => null]])
                        ->andWhere(['not in', 'TenderBaselinePriceID', \app\modules\tender\models\TenderBaselinePriceAttachment::find()->select('BaselinePriceIDRef')->andWhere("COALESCE($baselinePriceAttachmentTable.isDeleted,0)<>1")])
                        ->andWhere("COALESCE($baselinePriceTable.isDeleted,0)<>1")
                        ->exists();

                if ($baselinePriceModel) {
                    $result['result'] = false;
                    $result['message'][] = "اسناد مقدار شاخص ثبت نشده است.";
                    return $result;
                }
            } else if ($actionParam->paramTitle == 'requiredTenderAttachment' && $actionParam->value1 == 'bidderAttachment' && $actionParam->value2 == 1) {
                // بررسی ثبت شدن اسناد مناقصه گران
                $bidderTable = \app\modules\tender\models\TenderBidder::tableName();
                $bidderAttachmentTable = \app\modules\tender\models\TenderBidderAttachment::tableName();
                $bidderAttachmentModel = \app\modules\tender\models\TenderBidder::find()
                        ->where(["$bidderTable.tenderIDRef" => $model->TenderIDRef])
                        ->andWhere(['or',
                            ['and', // بررسی این که سند پاکت الف ثبت شده باشد
                                ['not', ["$bidderTable.packet1WarrantyAmount" => null]],
                                ['not in', "$bidderTable.BidderID", \app\modules\tender\models\TenderBidderAttachment::find()->select('BidderIDRef')->where(["$bidderAttachmentTable.BidderAttachmentTypeIDRef" => 1])->andWhere("COALESCE($bidderAttachmentTable.isDeleted,0)<>1")]
                            ],
                            ['and', // بررسی این که سند پاکت ب ثبت شده باشد
                                ['not', ["$bidderTable.packet2TechnicalGrade" => null]],
                                ['not in', "$bidderTable.BidderID", \app\modules\tender\models\TenderBidderAttachment::find()->select('BidderIDRef')->where(["$bidderAttachmentTable.BidderAttachmentTypeIDRef" => 2])->andWhere("COALESCE($bidderAttachmentTable.isDeleted,0)<>1")]
                            ],
                            ['and', // بررسی این که سند پاکت ج ثبت شده باشد
                                ['not', ["$bidderTable.packet3UnitPrice1" => null]],
                                ['not in', "$bidderTable.BidderID", \app\modules\tender\models\TenderBidderAttachment::find()->select('BidderIDRef')->where(["$bidderAttachmentTable.BidderAttachmentTypeIDRef" => 3])->andWhere("COALESCE($bidderAttachmentTable.isDeleted,0)<>1")]
                            ]
                        ])
                        ->andWhere("COALESCE($bidderTable.isDeleted,0)<>1")
                        ->exists();
                if ($bidderAttachmentModel) {
                    $result['result'] = false;
                    $result['message'][] = "اسناد مناقصه گران ثبت نشده است.";
                    return $result;
                }
            }
        }
        $lastCommissionModel = \app\modules\tender\models\TenderCommission::find()
                ->where(['TenderIDRef' => $model->TenderIDRef])
                ->andWhere("isDeleted is null or isDeleted != 1")
                ->orderBy(['CommissionDate' => SORT_DESC])
                ->limit(1)
                ->one();
        if (isset($lastCommissionModel)) {
            // در صورتی که کمیسیون تایید شده باشد امکان ارجاع به مراحل قبل وجود نداشته باشد
            if ($lastCommissionModel->status == 1 && $toaction == 917 && ($currentaction == 330 || $currentaction == 331 || $currentaction == 333 || $currentaction == 334)) {
                $result['result'] = false;
                $result['message'][] = "امکان ارسال کمیسیون تایید شده به مراحل قبل وجود ندارد.";
                return $result;
            }
        }
        // الزام ثبت قیمت شاخص 
        if ($currentaction == 916 && $toaction == 917) {
            $baselinePriceModel = \app\modules\tender\models\TenderBaselinePrice::find()
                    ->where(['TenderIDRef' => $model->TenderIDRef])
                    ->andWhere(['is', 'CommissionIDRef', null])
                    ->andWhere(['<>', 'COALESCE(isDeleted, 0)', 1])
                    ->exists();
            if (!$baselinePriceModel) {
                $result['result'] = false;
                $result['message'][] = 'قیمت شاخص ثبت نشده است';
            }
        }
        // الزام ثبت مناقصه گر 
        if ($currentaction == 925 && $toaction == 926) {
            $bidderExist = \app\modules\tender\models\TenderBidder::find()
                    ->where(['TenderIDRef' => $model->TenderIDRef])
                    ->andWhere("COALESCE(deprecated,0)<>1")
                    ->andWhere("COALESCE(isDeleted,0)<>1")
                    ->exists();
            if (!$bidderExist) {
                $result['result'] = false;
                $result['message'][] = 'مناقصه گری ثبت نشده است';
            }
        }

        return $result;
    }

    private Static function canNextActionAutomation($model, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $result = [
            'result' => true,
            'message' => [],
        ];
        $automationModel = $model->automation;
        if (!isset($automationModel)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "can't find Automation!");
        }
        $automationModel->loadHumanResource();

        if (in_array($model->LastActionIDRef, [
                    964, 965, 966, 967, 968, 969, 970, 971, 972, 973, // Process 72
                    985, 988, 990, 991, 992, 993 // Process 73
                ])) {
            $checkHasSignature = \app\modules\account\models\UserObs::find()
                    ->andWhere(["UserIDRef" => $uid])
                    ->andWhere(["OBSCategory3IDRef" => $obp])
                    ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
                    ->exists();
            if (!$checkHasSignature) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Current OBS of User dont have <b>Signature</b>!');
                return $result;
            }
        }

        if ($model->LastActionIDRef != $currentaction) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }
//        $enableFields = $automationModel->enableFields;
        $requiredFields = $automationModel->requiredFields;
        foreach ($requiredFields as $requiredField) {
            if ($requiredField == "File") {
                $requiredField = "FileName";
            }
            if ($automationModel->$requiredField === null || $automationModel->$requiredField === '') {
//            if (empty($automationModel->$requiredField)) {
                $result['result'] = false;
                $result['message'][] = $automationModel->getAttributeLabel($requiredField) . ' ' . "مشخص نشده است";
            }
        }

        //الزام به تاییدیه ساختار سازمان
        if ($model->LastActionIDRef == 967 && $toaction != 975 && $toaction != 964) {
            $Obsmodel = $automationModel->position;
            if (isset($Obsmodel) && $Obsmodel->verified != 1) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'ساختار مورد نظر تایید نشده است');
            }
        }

        return $result;
    }

    private Static function canNextActionNetwork($model, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $result = [
            'result' => true,
            'message' => [],
        ];
        $networkModel = $model->network;

        if (!isset($networkModel)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "can't find Network!");
        }

        $networkModel->loadHumanResource();

        if (in_array($model->LastActionIDRef, [996, 998, 999, 1001, 1002])) {
            $checkHasSignature = \app\modules\account\models\UserObs::find()
                    ->andWhere(["UserIDRef" => $uid])
                    ->andWhere(["OBSCategory3IDRef" => $obp])
                    ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
                    ->exists();
            if (!$checkHasSignature) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Current OBS of User dont have <b>Signature</b>!');
                return $result;
            }
        }

        if ($model->LastActionIDRef != $currentaction) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }
        $networkModel->loadUsernames();
        $requiredFields = $networkModel->requiredFields;
        foreach ($requiredFields as $requiredField) {
            if ($requiredField == "File") {
                $requiredField = "FileName";
            }
            if (empty($networkModel->$requiredField)) {
                $result['result'] = false;
                $result['message'][] = $networkModel->getAttributeLabel($requiredField) . ' ' . "مشخص نشده است";
            }
        }

        return $result;
    }

    private Static function canNextActionEmployeeMembership($model, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $result = [
            'result' => true,
            'message' => [],
        ];
        $employeeMembershipModel = $model->employeeMembership;

        if (!isset($employeeMembershipModel)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "can't find Network!");
        }

        $employeeMembershipModel->loadHumanResource();

        if (in_array($model->LastActionIDRef, [1079, 1078])) {
            $checkHasSignature = \app\modules\account\models\UserObs::find()
                    ->andWhere(["UserIDRef" => $uid])
                    ->andWhere(["OBSCategory3IDRef" => $obp])
                    ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
                    ->exists();
            if (!$checkHasSignature) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Current OBS of User dont have <b>Signature</b>!');
                return $result;
            }
        }

        if ($model->LastActionIDRef != $currentaction) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }

        return $result;
    }

    private Static function canNextActionEmployeeSettlement($model, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $result = [
            'result' => true,
            'message' => [],
        ];

        $checkHasSignature = \app\modules\account\models\UserObs::find()
                ->andWhere(["UserIDRef" => $uid])
                ->andWhere(["OBSCategory3IDRef" => $obp])
                ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
                ->exists();
        if (!$checkHasSignature) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'Current OBS of User dont have <b>Signature</b>!');
            return $result;
        }
        if ($model->LastActionIDRef != $currentaction) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }

        if (in_array($toaction, [1012, 1053])) {
            $stampDocuments = \app\modules\automation\models\HumanResource::stampDocumentQuery($model->employeeSettlement->HumanResourceIdRef)->exists();
            if ($stampDocuments) {
                $result['result'] = false;
                $result['message'][] = 'مهرهای سازمانی فرد تعیین تکلیف نشده است!';
                return $result;
            }
        }

        return $result;
    }

    private static function canNextActionStampDocument($model, $currentaction, $toaction) {
        $result = ['result' => true, 'message' => []];        
        $stampDocumentModel = $model->getStampDocument()->one();
        if (!$stampDocumentModel) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', "مدل سند مهر یافت نشد!");
            return $result;
        }
        // ✅ چک کردن امضا (HasSignature)
    // =========================================================
    $userId = Yii::$app->user->id;
    $obsId = Yii::$app->session->get('ObsPosition');

    $userObsModel = \app\modules\account\models\UserObs::find()
        ->where(['UserIDRef' => $userId, 'OBSCategory3IDRef' => $obsId])
        ->andWhere(['isActive' => 1])
        ->andWhere(['or', ['isDeleted' => 0], ['isDeleted' => null]])
        ->one();

    // فقط برای مسیرهای غیر از 1183 (رد) و 1172 (شروع)
    if (!in_array($toaction, [1183, 1172]) && $toaction != 1172) {
        if (!$userObsModel || $userObsModel->HasSignature != 1) {
            $result['result'] = false;
            $result['message'][] = '❌ شما مجوز امضا در این جایگاه را ندارید! لطفاً با مدیر سیستم تماس بگیرید.';
            return $result;
        }
    }
        // ========== شرط بهره‌بردار قبل از تایید نهایی برای همه، حتی ادمین ==========
if ($currentaction == 1178 && $toaction == 1184) {

    $hasBeneficiary = \app\modules\document\models\StampFlowDocument::find()
        ->where(['StampIDRef' => $stampDocumentModel->StampId])
        ->exists();

    if (!$hasBeneficiary) {
        $result['result'] = false;
        $result['message'][] = "❌ قبل از تایید نهایی، باید حداقل یک بهره‌بردار ثبت کنید!";
        return $result;
    }
}

// ========== ادمین کل بعد از چک بهره‌بردار آزاد است ==========
$currentUserObsId = Yii::$app->session->get('ObsPosition');

if ($currentUserObsId == 606) {
    return $result;
}

        // ========== 1. رد درخواست (1183) همیشه مجاز است ==========
        if ($toaction == 1183) {
            return $result;
        }

        // ========== 2. تشخیص نوع نشان ==========
        $isPerson = ($stampDocumentModel->StampTypeIDRef == 4);
        $organizationalMaker = $stampDocumentModel->OrganizationalMaker ?? 2;
        $stampObs = $stampDocumentModel->obs;

        // بررسی مدیریتی بودن پست سازمانی نشان (برای مسیرهای 1175 و 1176)
        $isManagerial = false;
        if ($stampObs && $stampObs->positionTypeIdRef) {
            $positionType = \app\modules\automation\models\PositionType::find()
                    ->where(['PositionTypeID' => $stampObs->positionTypeIdRef])
                    ->select(['IsManagerial'])
                    ->scalar();
            $isManagerial = ($positionType == 1);
        }


        // ========== 4. اعتبارسنجی مسیر بر اساس سناریو ==========
// سناریوی «وجود دارد» (OrganizationalMaker = 1)
if ($organizationalMaker == 1) {

    // 1172 → 1173
    if ($currentaction == 1172 && $toaction == 1173) {
        // مجاز
    }
    // 1173 → 1179
    elseif ($currentaction == 1173 && $toaction == 1179) {
        // مجاز
    }
    // 1179 → 1175 (اشخاص)
    elseif ($currentaction == 1179 && $toaction == 1175 && $isPerson) {
        // مجاز
    }
    // 1179 → 1177 (غیراشخاص یا بعد از برگشت از 1175/1176)
    elseif ($currentaction == 1179 && $toaction == 1177) {
        // مجاز
    }
    // 1179 → 1181 (غیراشخاص یا بعد از برگشت از 1175/1176)
    elseif ($currentaction == 1179 && $toaction == 1181) {
        // مجاز
    }
    // 1179 → 1172 (فقط برای غیراشخاص در بار اول)
    elseif ($currentaction == 1179 && $toaction == 1172 && !$isPerson) {
        // مجاز
    }
    // 1175 → 1176 (مدیریتی)
    elseif ($currentaction == 1175 && $toaction == 1176 && $isManagerial) {
        // مجاز
    }
    // 1175 → 1179 (غیرمدیریتی)
    elseif ($currentaction == 1175 && $toaction == 1179 && !$isManagerial) {
        // مجاز
    }
    // 1176 → 1179
    elseif ($currentaction == 1176 && $toaction == 1179) {
        // مجاز
    }
    // 1177 → 1178
    elseif ($currentaction == 1177 && $toaction == 1178) {
        // مجاز
    }
    // 1181 → 1178
    elseif ($currentaction == 1181 && $toaction == 1178) {
        // مجاز
    }
    // 1178 → 1184
    elseif ($currentaction == 1178 && $toaction == 1184) {
        // مجاز
    }
    // مسیر نامعتبر
    else {
        $result['result'] = false;
        $result['message'][] = "مسیر نامعتبر برای سناریوی «وجود دارد»!";
        return $result;
    }
}
        // سناریوی «وجود ندارد» (OrganizationalMaker = 2)
        else {

            // 1172 → 1173
            if ($currentaction == 1172 && $toaction == 1173) {
                // مجاز
            }
            // 1173 → 1174 (اشخاص)
        elseif ($currentaction == 1173 && $toaction == 1174){
            }
            // 1174 → 1175 (اشخاص)
            elseif ($currentaction == 1174 && $toaction == 1175 && $isPerson) {
                // مجاز
            }
            // 1174 → 1177 (غیراشخاص)
            elseif ($currentaction == 1174 && $toaction == 1177 && !$isPerson) {
                // مجاز
            }
            // 1174 → 1172 (برگشت)
            elseif ($currentaction == 1174 && $toaction == 1172) {
                // مجاز
            }
            // 1175 → 1176 (مدیریتی)
            elseif ($currentaction == 1175 && $toaction == 1176 && $isManagerial) {
                // مجاز
            }
            // 1175 → 1177 (غیرمدیریتی)
            elseif ($currentaction == 1175 && $toaction == 1177 && !$isManagerial) {
                // مجاز
            }
            // 1175 → 1174 (برگشت)
            elseif ($currentaction == 1175 && $toaction == 1174) {
                // مجاز
            }
            // 1176 → 1177
            elseif ($currentaction == 1176 && $toaction == 1177) {
                // مجاز
            }
            // 1176 → 1174 (برگشت)
            elseif ($currentaction == 1176 && $toaction == 1174) {
                // مجاز
            }
            // 1177 → 1178
            elseif ($currentaction == 1177 && $toaction == 1178) {
                // مجاز
            }
            // 1178 → 1184 (تایید نهایی)
            elseif ($currentaction == 1178 && $toaction == 1184) {
                // مجاز
            }
            // مسیر نامعتبر
            else {
                $result['result'] = false;
                $result['message'][] = "مسیر نامعتبر برای سناریوی «وجود ندارد»!";
                return $result;
            }
        }
// ========== 6. شرط بهره‌بردار قبل از تایید نهایی (1178 → 1184) ==========
// =========================================================
if ($currentaction == 1178 && $toaction == 1184) {
    $hasBeneficiary = \app\modules\document\models\StampFlowDocument::find()
            ->where(['StampIDRef' => $stampDocumentModel->StampId])
            ->exists();

    if (!$hasBeneficiary) {
        $url = \yii\helpers\Url::to(['/document/stamp-flow-document/create', 'sid' => $stampDocumentModel->StampId]);

        $result['result'] = false;
      //  $result['message'][] = "❌ قبل از تایید نهایی، باید حداقل یک بهره‌بردار ثبت کنید!";
        $result['message'][] = "📌 <a href='#' onclick='showBeneficiaryModalFromAlert(\"$url\", {$stampDocumentModel->StampId}); return false;' style='color:#ff9800; text-decoration:underline; cursor:pointer;'>برای ثبت بهره‌بردار جدید کلیک کنید</a>";

        return $result;
    }
}

        return $result;
    }

    public static function canNextAction($peid, $currentaction, $toaction) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $preContractDocumentTable = \app\modules\contract\models\PreContractDocument::tableName();
        $contractPmsTable = \app\modules\contract\models\ContractPms::tableName();
        $contractPlanTable = \app\modules\contract\models\ContractPlan::tableName();

        $result = [
            'result' => true,
            'message' => [],
        ];
        $model = self::find()
                ->where(['ProcessEntityID' => $peid, 'LastActionIDRef' => $currentaction])
                ->one();
        //check exists process
        if (!isset($model->ProcessEntityID)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }
        //find by $toaction
        $actionModel = Action::find()
                ->where(['ActionID' => $toaction])
                ->one();
        if (!isset($actionModel->ActionID)) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'Next step not selected');
            return $result;
        }

        $actionOrderModel = ActionOrder::find()
                        ->where([
                            'ActionIDRef' => $currentaction,
                            'NextActionIDRef' => $toaction,
                        ])->one();

        //section workflowContract
        if (!empty($model->WorkflowContractIDRef)) {
            //الزام درج شماره ی قرارداد
            if (($model->LastActionIDRef == 797 || $model->LastActionIDRef == 1049) && ($toaction == 485)) {
                $exists = WorkflowContract::find()
                        ->andWhere(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->andWhere("ContractNo is NULL")
                        ->exists();
                if ($exists) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Please insert contract No.');
                }
            }

            // اجبار درج درجه بندی
            if (($model->LastActionIDRef == 485 && $toaction == 486) || ($model->LastActionIDRef == 485 && $toaction == 491) || ($model->LastActionIDRef == 488 && $toaction == 493)) {
                $workflowContractModel = WorkflowContract::find()
                                ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])->one();
                $contractMeasureValueModel = ContractMeasureValue::find()->where(['WorkflowContractID' => $workflowContractModel->WorkflowContractID])->one();
                if (empty($contractMeasureValueModel)) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Complete closing grade form before next action');
                }
            }

            //الزام ثبت اطلاعات پیمانکار
            if ($model->LastActionIDRef == 491 || $model->LastActionIDRef == 602 || $model->LastActionIDRef == 660) {
                $workflowContractModel = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
//                        ->andWhere("(SubContractorPersonIDRef is null) or (SubContractorPersonIDRef=0)")
//                        ->exists()
                        ->one();
                if (empty($workflowContractModel->subcontractor) || $workflowContractModel->subcontractor->verified != 1) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Select subcontractor from List');
                }
            }


            //درج اجباری اسناد پیش از قراردادی
            if ($model->LastActionIDRef == 485 || $model->LastActionIDRef == 796 || $model->LastActionIDRef == 596 || $model->LastActionIDRef == 654 || $model->LastActionIDRef == 525) {
                $count = \app\modules\contract\models\PreContractDocument::find()
                        ->andWhere(['workflowContractIdRef' => $model->WorkflowContractIDRef])
                        ->andWhere("$preContractDocumentTable.isDeleted is null or $preContractDocumentTable.isDeleted != 1")
                        ->count();
                if ($count == 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Upload Pre-ContractDocument before next action');
                }
            }


            //الزام درج داکیومنت
            if (($model->LastActionIDRef == 485 && ($toaction == 486 || $toaction == 491)) || ($model->LastActionIDRef == 525 && ($toaction == 526 || $toaction == 867)) || ($model->LastActionIDRef == 618 && $toaction == 621) || ($model->LastActionIDRef == 599 && $toaction == 603) || ($model->LastActionIDRef == 657 && $toaction == 661) || ($model->LastActionIDRef == 676 && $toaction == 679)
            ) {
                $count = \app\modules\contract\models\ContractRevisionDocument::find()
                        ->workflowContract($model->WorkflowContractIDRef)
                        ->count();
                if ($count == 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Upload revision document before next action');
                }
            }

            //الزام درج اطلاعات رویژن
            if (
                    ($model->LastActionIDRef == 485 && ($toaction == 486 || $toaction == 491)) || ($model->LastActionIDRef == 796 && $toaction == 797) || ($model->LastActionIDRef == 654 && $toaction == 655) || ($model->LastActionIDRef == 654 && $toaction == 660) || ($model->LastActionIDRef == 596 && $toaction == 597) || ($model->LastActionIDRef == 596 && $toaction == 602)
            ) {
                $count = WorkflowContract::find()
                        ->andWhere(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->andWhere('RevisionTypeIDRef is null or RevisionTypeIDRef<>1')
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Save contract rider info before next action');
                }
            }



            //الزام ثبت 
            //FinancialContractCode
            if (($model->LastActionIDRef == 492 && $toaction == 518) || ($model->LastActionIDRef == 605 && $toaction == 606) || ($model->LastActionIDRef == 663 && $toaction == 664)) {
                $exists = WorkflowContract::find()
                        ->andWhere(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->andWhere(['FinancialContractCode' => null])
                        ->exists();
                if ($exists) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Financial Contract Code can not blank.');
                }
            }

            //الزام درج ای پی اس
            if (
                    ($model->LastActionIDRef == 488 && $toaction == 493) || ($model->LastActionIDRef == 489 && $toaction == 490) || ($model->LastActionIDRef == 599 && $toaction == 603) || ($model->LastActionIDRef == 600 && $toaction == 601) || ($model->LastActionIDRef == 657 && $toaction == 661) || ($model->LastActionIDRef == 658 && $toaction == 659)
            ) {
                $companyId = Yii::$app->session->get('CompanyID');
                $count = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->andWhere("(EPS1IDRef is null or EPS1IDRef=0) and (EPS2IDRef is null or EPS2IDRef=0) and (EPS3IDRef is null or EPS3IDRef=0)")
                        ->andWhere("SubContractorPersonIDRef <> $companyId")
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Select EPS before next action');
                }
            }

            //الزام انتخاب category و type و assignment
            if (($model->LastActionIDRef == 488 && $toaction == 493) || ($model->LastActionIDRef == 599 && $toaction == 603) || ($model->LastActionIDRef == 657 && $toaction == 661)) {
                $count = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->andWhere('ContractCategoryIDRef is null or ContractCategoryIDRef=0 or ContractTypeIDRef is null or ContractTypeIDRef=0 or assignmentTypeIdRef is null or assignmentTypeIdRef=0')
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Select Contract Category,Contract Type and Assignment Type before next action');
                }
            }

            //الزام درج پی ام اس
            if (
                    ($model->LastActionIDRef == 519 && $toaction == 523) || ($model->LastActionIDRef == 532 && $toaction == 536) || ($model->LastActionIDRef == 607 && $toaction == 611) || ($model->LastActionIDRef == 622 && $toaction == 626) || ($model->LastActionIDRef == 665 && $toaction == 669) || ($model->LastActionIDRef == 680 && $toaction == 684)
            ) {
                $workflowContractModel = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->one();
                if ($workflowContractModel->RequiredPMS == 1) {
                    $count = \app\modules\contract\models\ContractPms::find()
                            ->andWhere(['workflowContractIDRef' => $model->WorkflowContractIDRef])
                            ->andWhere("$contractPmsTable.isDeleted is null or $contractPmsTable.isDeleted != 1")
                            ->count();
                    if ($count == 0) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Upload PMS before next action');
                    }
                }
            }

            //الزام درج پلن
            if (($model->LastActionIDRef == 519 && $toaction == 523) || ($model->LastActionIDRef == 532 && $toaction == 536) || ($model->LastActionIDRef == 607 && $toaction == 611) || ($model->LastActionIDRef == 622 && $toaction == 626) || ($model->LastActionIDRef == 665 && $toaction == 669) || ($model->LastActionIDRef == 680 && $toaction == 684)) {
                $workflowContractModel = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->one();
                if ($workflowContractModel->RequiredPlan == 1) {
                    $count = \app\modules\contract\models\ContractPlan::find()
                            ->andWhere(['workflowContractIDRef' => $model->WorkflowContractIDRef])
                            ->andWhere("$contractPlanTable.isDeleted is null or $contractPlanTable.isDeleted != 1")
                            ->count();
                    if ($count == 0) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Upload Plan before next action');
                    }
                }
            }

            //الزام انتخاب فرایند برای صورت وضعیت
            if (
                    ($model->LastActionIDRef == 490 && $toaction == 488) || ($model->LastActionIDRef == 488 && $toaction == 493) || ($model->LastActionIDRef == 601 && $toaction == 599) || ($model->LastActionIDRef == 599 && $toaction == 603) || ($model->LastActionIDRef == 659 && $toaction == 657) || ($model->LastActionIDRef == 657 && $toaction == 661)
            ) {
                $count = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->andWhere("InvoiceProcessIDRef1 is null or InvoiceProcessIDRef1=0")
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Select process for invoices before next action');
                }
            }

            //الزام درج کد های هزینه
            if (
                    ($model->LastActionIDRef == 488 && $toaction == 493) || ($model->LastActionIDRef == 489 && $toaction == 490) || ($model->LastActionIDRef == 528 && $toaction == 531) || ($model->LastActionIDRef == 599 && $toaction == 603) || ($model->LastActionIDRef == 600 && $toaction == 601) || ($model->LastActionIDRef == 618 && $toaction == 621) || ($model->LastActionIDRef == 657 && $toaction == 661) || ($model->LastActionIDRef == 658 && $toaction == 659) || ($model->LastActionIDRef == 676 && $toaction == 679)
            ) {
                //wf moel
                $companyId = Yii::$app->session->get('CompanyID');
                $wfModel = WorkflowContract::find()
                        ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                        ->one();
                if ($wfModel->SubContractorPersonIDRef != $companyId) {// رد شدن در حالتی قرارداد اصلی است
                    //owner price1
                    $ownerPrice1 = WorkflowContractBudget::find()
                            ->joinWith('contract')
                            ->where(['WorkflowContractIDRef' => $model->WorkflowContractIDRef])
                            ->andWhere('(AnotherContractIsOwner is null or AnotherContractIsOwner=0) and isActive=1')
                            ->andWhere(WorkflowContractBudget::tableName() . '.isDeleted is null '
                                    . 'or ' . WorkflowContractBudget::tableName() . '.isDeleted != 1')
                            ->andWhere("SubContractorPersonIDRef <> $companyId")
                            ->sum('OwnerPrice1');

                    //owner price2
                    $ownerPrice2 = WorkflowContractBudget::find()
                            ->joinWith('contract')
                            ->where(['WorkflowContractIDRef' => $model->WorkflowContractIDRef])
                            ->andWhere('(AnotherContractIsOwner is null or AnotherContractIsOwner=0) and isActive=1')
                            ->andWhere(WorkflowContractBudget::tableName() . '.isDeleted is null '
                                    . 'or ' . WorkflowContractBudget::tableName() . '.isDeleted != 1')
                            ->andWhere("SubContractorPersonIDRef <> $companyId")
                            ->sum('OwnerPrice2');

                    //executerPrice1
                    $executerPrice1 = WorkflowContractBudget::find()
                            ->joinWith('contract')
                            ->where(['WorkflowContractIDRef' => $model->WorkflowContractIDRef])
                            ->andWhere('(AnotherContractIsExecuter is null or AnotherContractIsExecuter=0) and isActive=1')
                            ->andWhere(WorkflowContractBudget::tableName() . '.isDeleted is null '
                                    . 'or ' . WorkflowContractBudget::tableName() . '.isDeleted != 1')
                            ->andWhere("SubContractorPersonIDRef <> $companyId")
                            ->sum('executerPrice1');

                    //executerPrice2
                    $executerPrice2 = WorkflowContractBudget::find()
                            ->joinWith('contract')
                            ->where(['WorkflowContractIDRef' => $model->WorkflowContractIDRef])
                            ->andWhere('(AnotherContractIsExecuter is null or AnotherContractIsExecuter=0) and isActive=1')
                            ->andWhere(WorkflowContractBudget::tableName() . '.isDeleted is null '
                                    . 'or ' . WorkflowContractBudget::tableName() . '.isDeleted != 1')
                            ->andWhere("SubContractorPersonIDRef <> $companyId")
                            ->sum('executerPrice2');

                    //contractPrice1
                    $contractPrice1 = $wfModel->Price1;

                    //contractPrice2
                    $contractPrice2 = $wfModel->Price2;

                    //wheres-------
                    //owner
                    if (abs($ownerPrice1 - $contractPrice1) > 1) {
                        $result['result'] = false;
                        $result['message'][] = 'Contract Price1 is ' . Yii::$app->utility->moneyFormat($contractPrice1, 2)
                                . ' and Owner Budget1 is ' . Yii::$app->utility->moneyFormat($ownerPrice1, 2) .
                                '.Sum of OwnerContractbudgets in rate 1 are not equal to contract price1'; //هنوزکامل نشده
                    }
                    if (abs($ownerPrice2 - $contractPrice2) > 1) {
                        $result['result'] = false;
                        $result['message'][] = 'Contract Price2 is' . Yii::$app->utility->moneyFormat($contractPrice2, 2)
                                . ' and Owner Budget2 is ' . Yii::$app->utility->moneyFormat($ownerPrice2, 2)
                                . ' .Sum of OwnerContractbudgets in rate 2 are not equal to contract price2'; //هنوزکامل نشده
                    }
                    //executer
                    if (abs($executerPrice1 - $contractPrice1) > 1) {
                        $result['result'] = false;
                        $result['message'][] = 'Contract Price1 is ' . Yii::$app->utility->moneyFormat($contractPrice1, 2)
                                . ' and executer Budget1 is ' . Yii::$app->utility->moneyFormat($executerPrice1, 2)
                                . ' .Sum of ExecuterContractbudgets in rate 1 are not equal to contract price1'; //هنوزکامل نشده
                    }
                    if (abs($executerPrice2 - $contractPrice2) > 1) {
                        $result['result'] = false;
                        $result['message'][] = 'Contract Price2 is' . Yii::$app->utility->moneyFormat($contractPrice2, 2)
                                . ' and Owner Budget2 is ' . Yii::$app->utility->moneyFormat($executerPrice2, 2)
                                . ' .Sum of OwnerContractbudgets in rate 2 are not equal to contract price2'; //هنوزکامل نشده
                    }
                }
            }
        } else if (!empty($model->CashRequestIDRef)) {//check cashRequest
            $budgetTable = \app\modules\budget\models\Budget::tableName();
            $budgetNodeTable = \app\modules\budget\models\BudgetNode::tableName();
            $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
            $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
            $capitalBalanceAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
            $capitalBalanceTable = \app\modules\capital\models\CapitalBalance::tableName();
            $cashrequestTable = \app\modules\cashRequest\models\CashRequest::tableName();
            $cashrequestAmountTable = \app\modules\cashRequest\models\CashRequestAmount::tableName();
            $actionParamTable = \app\modules\workflow\models\ActionParam::tableName();
            $processEntityTable = ProcessEntity::tableName();
            $planTable = \app\modules\budget\models\Plan::tableName();

            $sqlCashrequestLastAmount = "sum($cashrequestAmountTable.LastAmount)";
            /* $query = \app\modules\budget\models\Budget::find()
              ->select([
              "(case
              when $budgetTable.PlanIDRef>0 then COALESCE((SELECT sum($capitalBalanceAmountTable.balanceAmount)"
              . " FROM $capitalBalanceAmountTable"
              . " inner join $capitalBalanceTable on $capitalBalanceTable.capitalBalanceId=$capitalBalanceAmountTable.capitalBalanceIdRef"
              . " inner join $processEntityTable on $processEntityTable.capitalBalanceIdRef=$capitalBalanceTable.capitalBalanceId"
              . " inner join $actionParamTable on $actionParamTable.actionIdRef=$processEntityTable.LastActionIDRef"
              . " where $capitalBalanceAmountTable.budgetIdRef=" . Budget::tableName() . ".BudgetID and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
              +COALESCE((select $sqlCashrequestLastAmount from $cashrequestAmountTable"
              . " inner join $cashrequestTable on $cashrequestTable.CashRequestID=$cashrequestAmountTable.CashRequestIDRef"
              . " inner join $processEntityTable on $processEntityTable.CashRequestIDRef=$cashrequestTable.CashRequestID"
              . " inner join $actionParamTable on $actionParamTable.actionIdRef=$processEntityTable.LastActionIDRef"
              //                    . " where $cashrequestAmountTable.RecipientPlanIDRef=" . Budget::tableName() . ".PlanIDRef and $cashrequestAmountTable.budgetType like 'OperatingBudget' and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
              . " where SUBSTRING($cashrequestAmountTable.BudgetRow, 4)=" . Budget::tableName() . ".BudgetID and $cashrequestAmountTable.budgetType like 'OperatingBudget' and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
              when $budgetTable.NewContractIDRef>0 then 0
              when $budgetTable.ContractIDRef>0 then (select sum(AmountApproved1) from $invoiceAmountTable "
              . "inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID "
              . "where $invoiceTable.InvoiceType in (1,3,5) "
              . "and $budgetTable.ContractIDRef=$invoiceTable.ContractID "
              . "and $invoiceAmountTable.AmountType=1 "
              . "and $invoiceAmountTable.CostCode = $budgetNodeTable.NodeIDRef "
              . "and $invoiceTable.CutOffDateHijri >='$this->year/01/01' "
              . "and $invoiceTable.CutOffDateHijri <'$this->year/12/30' "
              . "and $invoiceTable.AmountStatus in ($amountStatusArray))
              else 0
              end) as AC1_95",
              ])
              ->innerJoin($budgetNodeTable, "$budgetNodeTable.BudgetIDRef=$budgetTable.BudgetID")
              ->leftJoin($planTable, "$budgetTable.PlanIDRef=$planTable.PlanID")
              ->where("$budgetTable.BudgetID=$bid")
              ->andWhere("$budgetTable.isDeleted is null or $budgetTable.isDeleted<>1")
              ->andWhere("$budgetNodeTable.isDeleted is null or $budgetNodeTable.isDeleted<>1")
              ->asArray()
              ->all(); */

            $cashrequestModel = $model->cashrequest;
            $cashrequestId = $cashrequestModel->CashRequestID;
            $amountStatusArray = '1, 5';

            $annualBudgetTable = \app\modules\budget\models\AnnualBudget::tableName();

            //در صورت ذخیره ای دی budget نیازی به کد زیر نمی باشد
            $cashrequestAmounsubQuery = \app\modules\cashRequest\models\CashRequestAmount::find()
                            ->select([
                                "CashrequestAmountId" => "ID",
                                "BudgetIdRef" => "SUBSTRING(BudgetRow, 4)",
                            ])
                            ->where("CashRequestIDRef=$cashrequestId")->createCommand()->rawSql;

//            $cashrequestAmounQuery =  (new \yii\db\Query())
//                ->from($cashrequestAmounsubQuery)
            $cashrequestAmounQuery = \app\modules\cashRequest\models\CashRequestAmount::find()
                    ->select([
//                    ->addSelect([
                        "($planTable.PlanAmount - (case 
                        when $budgetTable.PlanIDRef>0 then COALESCE((SELECT sum($capitalBalanceAmountTable.balanceAmount)"
                        . " FROM $capitalBalanceAmountTable"
                        . " inner join $capitalBalanceTable on $capitalBalanceTable.capitalBalanceId=$capitalBalanceAmountTable.capitalBalanceIdRef"
                        . " inner join $processEntityTable on $processEntityTable.capitalBalanceIdRef=$capitalBalanceTable.capitalBalanceId"
                        . " inner join $actionParamTable on $actionParamTable.actionIdRef=$processEntityTable.LastActionIDRef"
                        . " where $capitalBalanceAmountTable.budgetIdRef=" . Budget::tableName() . ".BudgetID and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
                        +COALESCE((select $sqlCashrequestLastAmount from $cashrequestAmountTable"
                        . " inner join $cashrequestTable on $cashrequestTable.CashRequestID=$cashrequestAmountTable.CashRequestIDRef"
                        . " inner join $processEntityTable on $processEntityTable.CashRequestIDRef=$cashrequestTable.CashRequestID"
                        . " inner join $actionParamTable on $actionParamTable.actionIdRef=$processEntityTable.LastActionIDRef"
                        . " where SUBSTRING($cashrequestAmountTable.BudgetRow, 4)=" . Budget::tableName() . ".BudgetID and $cashrequestAmountTable.budgetType like 'OperatingBudget' and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
                        when $budgetTable.NewContractIDRef>0 then 0 
                        when $budgetTable.ContractIDRef>0 then (select sum(AmountApproved1) from $invoiceAmountTable "
                        . "inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID "
                        . "where $invoiceTable.InvoiceType in (1,3,5) "
                        . "and $budgetTable.ContractIDRef=$invoiceTable.ContractID "
                        . "and $invoiceAmountTable.AmountType=1 "
                        . "and $invoiceAmountTable.CostCode = $budgetNodeTable.NodeIDRef "
                        . "and $invoiceTable.CutOffDateHijri >=CONCAT($annualBudgetTable.Year,'/01/01') "
                        . "and $invoiceTable.CutOffDateHijri <CONCAT($annualBudgetTable.Year,'/12/30') "
                        . "and $invoiceTable.AmountStatus in ($amountStatusArray)) 
                        else 0 
                        end)) as remainBudget"
                    ])
                    ->innerJoin(['subQuery' => "(" . $cashrequestAmounsubQuery . ")"], "subQuery.CashrequestAmountId=$cashrequestAmountTable.ID")
                    ->innerJoin($budgetTable, "$budgetTable.BudgetID=subQuery.BudgetIdRef")
                    ->innerJoin($annualBudgetTable, "$annualBudgetTable.AnnualBudgetID=$budgetTable.AnnualBudgetIDRef")
                    ->innerJoin($budgetNodeTable, "$budgetNodeTable.BudgetIDRef=$budgetTable.BudgetID")
                    ->innerJoin($planTable, "$budgetTable.PlanIDRef=$planTable.PlanID")->andWhere("$annualBudgetTable.capitalProcessType=1")
                    ->where("$budgetTable.isDeleted is null or $budgetTable.isDeleted<>1")
                    ->andWhere("$annualBudgetTable.cashrequestProcessType=1")
                    ->andWhere("$budgetNodeTable.isDeleted is null or $budgetNodeTable.isDeleted<>1")
                    ->andWhere("$cashrequestAmountTable.isDeleted is null or  $cashrequestAmountTable.isDeleted != 1");

            $count = (new \yii\db\Query())
                    ->from(['subQuery' => "(" . $cashrequestAmounQuery->createCommand()->rawSql . ")"])
                    ->andWhere("subQuery.remainBudget<0")
                    ->count();

            if ($count > 0) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Remain Budget of one or mor plan is less than zero in this next action.');
                return $result;
            }


            // بررسی الزام وجود امضا
            $uid = Yii::$app->session->get('UserID');
            $obp = Yii::$app->session->get('ObsPosition');
            $userObsModel = \app\modules\account\models\UserObs::find()
                    ->where(['UserIDRef' => $uid, 'OBSCategory3IDRef' => $obp])
                    ->andWhere("COALESCE(isDeleted,0)<>1")
                    ->one();
            if (!$userObsModel->HasSignature) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Current OBS of User dont have <b>Signature</b>!');
                return $result;
            }

            //الزام درج مبلغ توسط مدیر دریافت کننده
            if (($currentaction == 468 && $toaction != 439) ||
                    ($currentaction == 424 && $toaction != 426) ||
                    ($currentaction == 425 && $toaction != 426)
            ) {
                $count = CashRequestAmount::find()
                        ->andWhere(['CashRequestIDRef' => $model->CashRequestIDRef])
                        ->andWhere("TempNo is not NULL")
                        ->andWhere("ReceiveDateHijri is not NULL")
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'TempNo OR ReceiveDateHijri is empty');
                }
            }

            // بررسی
            if (($currentaction == 405 && $toaction != 406) || ($currentaction == 432 && $toaction == 434) || ($currentaction == 378 && $toaction != 379)) {
                $count = CashRequestAmount::find()
                        ->where(['CashRequestIDRef' => $model->CashRequestIDRef, 'Manager1Amount' => NULL])
                        ->count();
//                die(var_dump($count));
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Manager 1 Amount is empty');
                }
            }

            //الزام درج مبلغ توسط برنامه ریزی
            if (($currentaction == 442 && $toaction == 408) || ($currentaction == 408 && $toaction == 420) || ($currentaction == 409 && $toaction == 441) || ($currentaction == 441 && $toaction == 410) || ($currentaction == 434 && $toaction == 443) || ($currentaction == 443 && $toaction == 435) || ($currentaction == 507 && $toaction == 381) || ($currentaction == 381 && $toaction == 393) || ($currentaction == 508 && $toaction == 382) || ($currentaction == 382 && $toaction == 383)) {

                $count = CashRequestAmount::find()
                        ->where(['CashRequestIDRef' => $model->CashRequestIDRef, 'PlanningAmount' => null])
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Planning Amount is empty');
                }
            }

            //الزام درج مبلغ توسط رئیس کارگاه یا مدیر عامل
            if (($currentaction == 420 && $toaction == 424) || ($currentaction == 410 && $toaction == 414) || ($currentaction == 435 && $toaction == 437) || ($currentaction == 393 && $toaction == 398) || ($currentaction == 383 && $toaction == 509) || ($currentaction == 393 && $toaction == 395) || ($currentaction == 383 && $toaction == 385)) {
                $count = CashRequestAmount::find()
                        ->where(['CashRequestIDRef' => $model->CashRequestIDRef, 'Manager2Amount' => null])
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Director Manager/Site Manager Amount is empty');
                }
            }

            //الزام درج مبلغ توسط کارفرما
            if (($currentaction == 395 && $toaction == 393) || ($currentaction == 385 && $toaction == 383)) {
                $count = CashRequestAmount::find()
                        ->where(['CashRequestIDRef' => $model->CashRequestIDRef, 'ClientAmount' => null])
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Client Amount is empty');
                }
            }

            //الزام درج مبلغ توسط مالی
            if (($currentaction == 424 && $toaction == 426) || ($currentaction == 414 && $toaction == 472) || ($currentaction == 437 && $toaction == 467) || ($currentaction == 467 && $toaction == 468) || ($currentaction == 414 && $toaction == 472) || ($currentaction == 472 && $toaction == 473) || ($currentaction == 472 && $toaction == 440)) {

                $count = CashRequestAmount::find()
                        ->where(['CashRequestIDRef' => $model->CashRequestIDRef])
                        ->andWhere(['or',
                            ['FinancialAmount' => null],
                            ['FinancialAmountInManager2Currency' => null]
                        ])
                        ->count();

                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Financial Amount is empty');
                }
            }
            $array1 = [464, 465, 466];
            $array2 = [469, 470, 471];
            $array3 = [510, 511, 512];
            if ((in_array($currentaction, $array1) && $toaction == 437) || (in_array($currentaction, $array2) && $toaction == 414) || (in_array($currentaction, $array3) && $toaction == 509)) {
                $autoMessage = Yii::$app->session['EntityID-' . $model->ProcessEntityID]['ActionAutoMessage'];
                if ($autoMessage != 'پیشنهاد جهت رد') {
                    $count = CashRequestAmount::find()
                            ->where(['CashRequestIDRef' => $model->CashRequestIDRef])
                            ->andWhere(['or',
                                ['FinancialAmount' => null],
                                ['FinancialAmountInManager2Currency' => null]
                            ])
                            ->count();
                    if ($count > 0) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Financial Amount is empty');
                    }
                }
            }

            //الزام درج اطلاعات گیرنده چک توسط مالی
            if (($currentaction == 473 && $toaction == 416) || ($currentaction == 468 && $toaction == 439) || ($currentaction == 515 && $toaction == 389)) {

                $count = CashRequestAmount::find()
                        ->where(['CashRequestIDRef' => $model->CashRequestIDRef])
                        ->andWhere(['or',
                            ['ReceiptNumber' => ''],
                            ['Receiver' => ''],
                            ['ReceiveDateHijri' => ''],
                            ['TempNo' => '']
                        ])
                        ->count();
                if ($count > 0) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Receipt informations (Cheque Number,Cheque Date,Receiver,TempNo) are empty');
                }
            }

            //  بررسی این که دارای ردیف بودجه با باقیمانده بودجه منفی نباشد


            /* $capitalId = $capitalModel->capitalBalanceId;
              $budgetTable = \app\modules\budget\models\Budget::tableName();
              $annualBudgetTable = \app\modules\budget\models\AnnualBudget::tableName();
              $budgetNodeTable = \app\modules\budget\models\BudgetNode::tableName();
              $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
              $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
              $capitalBalanceAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
              $capitalBalanceTable = \app\modules\capital\models\CapitalBalance::tableName();
              $cashrequestTable = \app\modules\cashRequest\models\CashRequest::tableName();
              $cashrequestAmountTable = \app\modules\cashRequest\models\CashRequestAmount::tableName();
              $actionParamTable = \app\modules\workflow\models\ActionParam::tableName();
              $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
              $planTable = \app\modules\budget\models\Plan::tableName();

              $amountStatusArray = '1, 5';
              $sqlCashrequestLastAmount = "sum($cashrequestAmountTable.LastAmount)";

              $capitalBalanceAmountQuery = (new \yii\db\Query())
              ->from($capitalBalanceAmountTable)
              ->addSelect([
              "($planTable.PlanAmount - (case
              when $budgetTable.PlanIDRef>0 then COALESCE((SELECT sum(cba1.balanceAmount)"
              . " FROM $capitalBalanceAmountTable as cba1"
              . " inner join $capitalBalanceTable as cb1 on cb1.capitalBalanceId=cba1.capitalBalanceIdRef"
              . " inner join $processEntityTable as pe1 on pe1.capitalBalanceIdRef=cb1.capitalBalanceId"
              . " inner join $actionParamTable on $actionParamTable.actionIdRef=pe1.LastActionIDRef"
              . " where cba1.budgetIdRef=" . Budget::tableName() . ".BudgetID and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
              +COALESCE((select $sqlCashrequestLastAmount from $cashrequestAmountTable"
              . " inner join $cashrequestTable on $cashrequestTable.CashRequestID=$cashrequestAmountTable.CashRequestIDRef"
              . " inner join $processEntityTable as pe1 on pe1.CashRequestIDRef=$cashrequestTable.CashRequestID"
              . " inner join $actionParamTable on $actionParamTable.actionIdRef=pe1.LastActionIDRef"
              . " where SUBSTRING($cashrequestAmountTable.BudgetRow, 4)=" . Budget::tableName() . ".BudgetID and $cashrequestAmountTable.budgetType like 'OperatingBudget' and $actionParamTable.paramTitle='AmountStatus' and CAST($actionParamTable.value1 AS INT) in ($amountStatusArray)),0)
              when $budgetTable.NewContractIDRef>0 then 0
              when $budgetTable.ContractIDRef>0 then (select sum(AmountApproved1) from $invoiceAmountTable "
              . "inner join $invoiceTable on $invoiceTable.InvoiceID=$invoiceAmountTable.InvoiceID "
              . "where $invoiceTable.InvoiceType in (1,3,5) "
              . "and $budgetTable.ContractIDRef=$invoiceTable.ContractID "
              . "and $invoiceAmountTable.AmountType=1 "
              . "and $invoiceAmountTable.CostCode = $budgetNodeTable.NodeIDRef "
              . "and $invoiceTable.CutOffDateHijri >=CONCAT($annualBudgetTable.Year,'/01/01') "
              . "and $invoiceTable.CutOffDateHijri <CONCAT($annualBudgetTable.Year,'/12/30') "
              . "and $invoiceTable.AmountStatus in ($amountStatusArray))
              else 0
              end)) as remainBudget"
              ])
              ->innerJoin($budgetTable, "$budgetTable.BudgetID=$capitalBalanceAmountTable.BudgetIDRef")
              ->innerJoin($annualBudgetTable, "$annualBudgetTable.AnnualBudgetID=$budgetTable.AnnualBudgetIDRef")
              ->innerJoin($budgetNodeTable, "$budgetNodeTable.BudgetIDRef=$budgetTable.BudgetID")
              ->innerJoin($capitalBalanceTable, "$capitalBalanceTable.capitalBalanceId=$capitalBalanceAmountTable.capitalBalanceIdRef")
              //                ->innerJoin($processEntityTable, "$processEntityTable.capitalBalanceIdRef=$capitalBalanceTable.capitalBalanceId")
              ->innerJoin($planTable, "$budgetTable.PlanIDRef=$planTable.PlanID")
              ->where("$capitalBalanceAmountTable.capitalBalanceIdRef=$capitalId")
              ->andWhere("$annualBudgetTable.capitalProcessType=1")
              ->andWhere("$budgetTable.isDeleted is null or $budgetTable.isDeleted<>1")
              ->andWhere("$budgetNodeTable.isDeleted is null or $budgetNodeTable.isDeleted<>1")
              ->andWhere("$capitalBalanceAmountTable.isDeleted is null or  $capitalBalanceAmountTable.isDeleted != 1");

              $count = (new \yii\db\Query())
              ->from(['subQuery' => "(" . $capitalBalanceAmountQuery->createCommand()->rawSql . ")"])
              ->andWhere("subQuery.remainBudget<0")
              ->count(); */
        } else if (!empty($model->InvoiceIDRef)) {//check invoice
            $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
            $invoiceModel = $model->invoice;
            if (empty($invoiceModel)) {
                throw new \yii\web\HttpException(403, 'Not found invoice data');
            }

            $contractModel = Contract::find()
                    ->where(['ContractID' => $invoiceModel->ContractID])
                    ->one();
            if (empty($contractModel)) {
                throw new \yii\web\HttpException(403, 'Not found contract data');
            }


            //الزام ذخیره شدن اینویس امونت
            $count = InvoiceAmountEdit::find()
                    ->where(['InvoiceID' => $model->InvoiceIDRef])
                    ->count();
            if ($count == 0) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'There is no saved amount for this invoice');
            }

            //الزام درج داکیومنت
            $numberOfEntityInThisAction = ActionEntity::find()
                    ->joinWith('processEntity')
                    ->where(['InvoiceIDRef' => $model->InvoiceIDRef, 'ActionIDRef' => $toaction])
                    ->count();

            $documentRequireModel = !empty($actionOrderModel->documentModelIdRef) ? $actionOrderModel->documentModelIdRef : 0;
            $invoiceDocumentRequireModelTable = InvoiceDocumentRequireModel::tableName();
            $invoiceDocumentTable = \app\modules\invoice\models\InvoiceDocument::tableName();
            $sql = "SELECT count(*) FROM $invoiceDocumentRequireModelTable where DocumentRequireModel=" . $documentRequireModel . "
                and RequireType='Required' and ((select COUNT(*) from $invoiceDocumentTable where invoiceidref=" . $model->InvoiceIDRef . " and InvoiceDocumentTypeIDRef=$invoiceDocumentRequireModelTable.InvoiceDocumentTypeIDRef and InvoiceActionIDRef>0)-" . $numberOfEntityInThisAction . ")<=0";
            $requireDocumentCount = Yii::$app->dbTehran->createCommand($sql)->queryScalar();
            if ($requireDocumentCount > 0) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Attach required documents before next action');
            }
            $array = [77, 193, 91, 123, 140, 161, 181, 225, 291, 262, 317, 344, 370, 389, 463, 646, 699, 715, 735, 739, 778, 791, 795, 1105, 1121, 1137, 1150];
            if (in_array($toaction, $array)) {
                if (empty($invoiceModel->financialDocNumber) || empty($invoiceModel->financialDocDateHijri)) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Receipt informations (Temp Number,Doc. Date) are empty');
                }
//                    $invCount = InvoiceEdit::find()->where(['InvoiceID' => $model->InvoiceIDRef])
//                            ->andWhere(['financialDocNumber' => ''])
//                            ->andWhere(['financialDocDateHijri' => ''])
//                            ->count();
//                    if ($invCount > 0) {
//                        $result['result'] = false;
//                        $result['message'][] = Yii::t('workflow','Receipt informations (Temp Number,Doc. Date) are empty');
//                    }
            }

            //مبلغ تجمعی صورت وضعیت قطعی باید حداقل 75 درصد مبلغقرارداد باشد
            if (($invoiceModel->InvoiceType == 3) && $invoiceModel->CutOffDateHijri > '1399/12/30' && $invoiceModel->contract->completePermission == 0 && $invoiceModel->contract->ContractType != 13 && $invoiceModel->contract->ContractType != 18) {
                $approvedTotal = \app\modules\invoice\models\InvoiceAmountEdit::find()
                        ->select([
                            "AmountApproved1" => "SUM(AmountApproved1)",
                            "AmountApproved2" => "SUM(AmountApproved2)"
                        ])
                        ->joinWith(['invoice'])
                        ->where(["$invoiceTable.ContractID" => $invoiceModel->ContractID, "$invoiceTable.AmountStatus" => [1, 5], "$invoiceTable.InvoiceType" => [1, 3, 5], 'AmountType' => 1])
                        ->andWhere("$invoiceTable.CutOffDateHijri<='$invoiceModel->CutOffDateHijri'")
                        ->one();

                $lastRider = $invoiceModel->contract->lastRider;
                if (($lastRider->Price1 * 0.75 > $approvedTotal["AmountApproved1"] || (isset($lastRider->Price2) && $lastRider->Price2 * 0.75 > $approvedTotal["AmountApproved2"]))) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Cumulative Approved amount must grater than 75 Percent of contract price');
                }
            }
            // الزام مدارک اختصاصی فرایند ۸۸ (کارپردازی)
if ((int)$model->ProcessIDRef === 88) {

    $current = (int)$currentaction;
    $next    = (int)$toaction;

    // لیست نوع مدارک موردنیاز بر اساس انتقال
    $requiredTypes = [];

    if ($current === 1203 && $next === 1204) {
        $requiredTypes = [23]; // MR
        $msg = 'بارگذاری مدرک MR قبل از رفتن به مرحله بعد الزامی است.';
    } elseif ($current === 1205 && $next === 1211) {
        $requiredTypes = [24]; // IVR
        $msg = 'بارگذاری مدرک IVR قبل از رفتن به مرحله بعد الزامی است.';
    } elseif ($current === 1211 && $next === 1206) {
        $requiredTypes = [11, 25]; // PL + Purchase Invoice
        $msg = 'بارگذاری مدارک PL و Purchase Invoice قبل از رفتن به مرحله بعد الزامی است.';
    } elseif ($current === 1206 && $next === 1207) {
        $requiredTypes = [13, 26]; // MSR + BMR
        $msg = 'بارگذاری مدارک MSR و BMR قبل از رفتن به مرحله بعد الزامی است.';
    }

    if (!empty($requiredTypes)) {
        $invoiceDocumentTable = \app\modules\invoice\models\InvoiceDocument::tableName();

        foreach ($requiredTypes as $typeId) {
            $exists = (new \yii\db\Query())
                ->from($invoiceDocumentTable)
                ->where([
                    'invoiceidref'             => $model->InvoiceIDRef,
                    'InvoiceDocumentTypeIDRef' => $typeId,
                ])
                ->andWhere('InvoiceActionIDRef > 0')
                ->exists();

            if (!$exists) {
                $result['result'] = false;
                $result['message'][] = $msg;
                break;
            }
        }
    }
}

            //pksk
            if ($contractModel->ContractCategory == 13) {
                //sum AmountApproved1
                $amountApproved1 = InvoiceAmountEdit::find()
                        ->where(['InvoiceID' => $invoiceModel->InvoiceID])
                        ->sum('AmountApproved1');

                //sum AmountApproved2
                $amountApproved2 = InvoiceAmountEdit::find()
                        ->where(['InvoiceID' => $invoiceModel->InvoiceID])
                        ->sum('AmountApproved2');

                //sum ci_amount1
                $ci1 = InvoicePksk::find()
                        ->where(['InvoiceIDRef' => $invoiceModel->InvoiceID])
                        ->sum('CI_Amount1');
                //sum ci_amount2
                $ci2 = InvoicePksk::find()
                        ->where(['InvoiceIDRef' => $invoiceModel->InvoiceID])
                        ->sum('CI_Amount2');
                $pkskcount = InvoicePksk::find()->where(['InvoiceIDRef' => $invoiceModel->InvoiceID])->count();
                if ($pkskcount > 0) {
                    if (round($ci1) != round($amountApproved1) || round($ci2) != round($amountApproved2)) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Commercial invoice amounts (PKSK Info) should be equal to Invoice Amount.');
                    }
                }


                //@toaction if(111 or 119)
                if (($toaction == 111 || $toaction == 119 || $toaction == 634 || $toaction == 642) &&
                        (round($ci1) != round($amountApproved1) || round($ci2) != round($amountApproved2))) {
                    if ($ci1 != $amountApproved1 || $ci2 != $amountApproved2) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Commercial invoice amounts (PKSK Info) should be equal to Invoice Amount.');
                    }
                }
                $array = [495, 498, 501, 502, 505];
                if (in_array($toaction, $array)) {
                    $checkHasSignature = \app\modules\account\models\UserObs::find()
                            ->andWhere(["UserIDRef" => $uid])
                            ->andWhere(["OBSCategory3IDRef" => $obp])
                            ->andWhere("HasSignature=1 and isActive=1 and COALESCE(isDeleted,0)<>1")
                            ->exists();

                    if (!$checkHasSignature) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'You are don`t have any signature!!');
                        return $result;
                    }
                }
            }
        } else if (!empty($model->warrantyIdRef)) {
            
        } else if (!empty($model->debitmemoIdRef)) {
            $result = ProcessEntity::canNextActionDebitmemo($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        } else if (!empty($model->capitalBalanceIdRef)) {
            $result = ProcessEntity::canNextActionCapital($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        } else if (!empty($model->BudgetIDRef)) {
            if ($model->LastActionIDRef != $currentaction) {
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
                return $result;
            }
            //الزام درج بودجه برای همه کدهای قرارداد یا برنامه

            $budgetModel = \app\modules\budget\models\Budget::find()
                    ->where(['BudgetID' => $model->BudgetIDRef])
                    ->one();

            if (!$budgetModel) {
                throw new \yii\web\HttpException('Not found Budget', 501);
            }
            $contractID = $budgetModel->ContractIDRef;
            $newContractID = $budgetModel->NewContractIDRef;
            $planID = $budgetModel->PlanIDRef;
            $annualBudgetID = $budgetModel->AnnualBudgetIDRef;
//            $numberOfBudgets = Yii::$app->db->createCommand('select count(*) from [view].[annual_budgeting_detail](@userID,@OBSCategory3ID,@BudgetID)')->queryScalar();
//
//            if ($contractID > 0) {
//                $numberOfCostCodes = \app\modules\contract\models\ContractNodeBudget::find()
//                        ->select(['Count(distinct left([NodeID],12))'])
//                        ->where(['ExecuterContractIDRef' => $contractID])
//                        ->andWhere(['>', 'isActive', 0])
//                        ->scalar();
//            }
//
//            if ($newContractID > 0) {
//                $numberOfCostCodes = \app\modules\budget\models\NcontractNodeBudget::find()
//                        ->select(['count(left([NodeIDRef],12))'])
//                        ->where(['NewContractIDRef' => $newContractID])
//                        ->scalar();
//            }
//
//            if ($planID > 0) {
//                $numberOfCostCodes = \app\modules\budget\models\Plan::find()
//                        ->select(['count(left([NodeIDRef],12))'])
//                        ->where(['PlanID' => $planID])
//                        ->scalar();
//            }
//
//            if ($numberOfCostCodes != $numberOfBudgets) {
//                $result['result'] = false;
//                $result['message'][] = 'Number of budget records should be equal to cost codes';
//                return $result;
//            }

            if ($planID > 0) {
                $planAmount = \app\modules\budget\models\Plan::find()
                        ->select(['PlanAmount'])
                        ->where(['PlanID' => $planID])
                        ->andWhere(\app\modules\budget\models\Plan::tableName() . '.isDeleted is null '
                                . 'or ' . \app\modules\budget\models\Plan::tableName() . '.isDeleted != 1')
                        ->scalar();
                $budget1Amount = \app\modules\budget\models\BudgetNodeAmount::find()
                        ->joinWith('budgetNode')
                        ->where(['BudgetIDRef' => $budgetModel->BudgetID])
                        ->andWhere(\app\modules\budget\models\BudgetNode::tableName() . '.isDeleted is null '
                                . 'or ' . \app\modules\budget\models\BudgetNode::tableName() . '.isDeleted != 1')
                        ->andWhere(\app\modules\budget\models\BudgetNodeAmount::tableName() . '.isDeleted is null '
                                . 'or ' . \app\modules\budget\models\BudgetNodeAmount::tableName() . '.isDeleted != 1')
                        ->sum('Budget1Amount');
                if (abs($planAmount - $budget1Amount) > 1) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', "Plan value and it's Budget must be equal ");
                    return $result;
                }

                $budgetNodeModel = \app\components\ActiveModel::find('\app\modules\budget\models\BudgetNode')
                        ->where(['BudgetIDRef' => $budgetModel->BudgetID])
                        ->one();
                $budgetNodeAmountFormModel = new \app\modules\budget\models\BudgetNodeAmountForm();
                $budgetNodeAmountFormModel->loadAmount($budgetNodeModel);
                $totalPecent = ceil($budgetNodeAmountFormModel->totalPercent());

                if ($totalPecent < 100) {
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', "جمع درصد پیشرفت های برنامه ای صددرصد نشده است");
                    return $result;
                }
            }
        } else if (!empty($model->TenderIDRef)) {
            $result = ProcessEntity::canNextActionTender($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        } else if (!empty($model->ContractClosingDocumentIDRef)) {
            
        } else if ($model->process->ProccessTypeIDRef == Process::STAMP_DOCUMENT_PROCESS_TYPE) { // ✅ NEW: برای سند مهر (ProccessTypeIDRef = 18)
            $result = ProcessEntity::canNextActionStampDocument($model, $currentaction, $toaction);
        } else if (!empty($model->AutomationIDRef)) {
            $result = ProcessEntity::canNextActionAutomation($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        } else if (!empty($model->NetworkIDRef)) {
            $result = ProcessEntity::canNextActionNetwork($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        } else if ($model->process->ProccessTypeIDRef == Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
            $result = ProcessEntity::canNextActionEmployeeSettlement($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        } else if ($model->process->ProccessTypeIDRef == Process::EMPLOYEE_MEMBERSHIP_PROCESS_TYPE) {
            $result = ProcessEntity::canNextActionEmployeeMembership($model, $currentaction, $toaction);

            if ($result['result'] == false) {
                return $result;
            }
        }

        return $result;
    }

    public static function goToNextAction($peid) 
            {
        $ip = Yii::$app->request->userIP;
        $result = [
            'result' => true,
            'message' => [],
        ];
        $session = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = self::find()
                ->where(['ProcessEntityID' => $peid, 'LastActionIDRef' => $session['FromActionIDRef']])
                ->one();

        if (!isset($processEntityModel->ProcessEntityID) || !isset($session['ProcessEntityIDRef'])) {
            throw new \yii\web\HttpException(403, 'Not found Process Entity or Action Entity');
        }
        if ($processEntityModel->LastActionIDRef != $session['FromActionIDRef']) {
            $result['result'] = false;
            $result['message'][] = Yii::t('workflow', 'This action has passed by another user');
            return $result;
        }

        if ($processEntityModel->process->ProccessTypeIDRef != Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
            //find Action Order
            //for set @subProcessId1,@subProcessId2,@subProcessId3
            $ActionOrderModel = ActionOrder::find()
                            ->where([
                                'ActionIDRef' => $session['FromActionIDRef'], //current action
                                'NextActionIDRef' => $session['ActionIDRef']//next action
                            ])->one();

            if (!isset($ActionOrderModel->ID)) {
                throw new \yii\web\HttpException(403, 'Not Found Action Order');
            }
        }
        //begin Transaction
        $transaction = Yii::$app->dbTehran->beginTransaction();
        try {
            //find Action Entity and editing
            $actionEntityModel = ActionEntity::find()
                    ->where(['ProcessEntityIDRef' => $peid, 'ActionIDRef' => $session['FromActionIDRef']])
                    ->orderBy('ID desc')
                    ->one();
            if (!isset($actionEntityModel->ID)) {
                throw new \yii\web\HttpException(501, 'Not found Action Entity');
            }

            $actionEntityModel->toActionIdRef = $session['ActionIDRef']; //next action
            $actionEntityModel->toActionDate = $session['ActionDate'];
            $actionEntityModel->toActionDateHijri = $session['ActionDateHijri'];
            $actionEntityModel->hasChange = 1;
            $resEntityModel = $actionEntityModel->save(); //update Action Entity
            if (!$resEntityModel) {
                $transaction->rollBack();
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Error on updating current action entity.Please try letter!');
                return $result;
            }

            //new Action Entity width fill session data
            $newActionEntityModel = new ActionEntity();
            $newActionEntityModel->log = \app\components\Logger::getLog($processEntityModel->ProcessEntityID);

            $newActionEntityModel->ProcessEntityIDRef = $processEntityModel->ProcessEntityID;
            $newActionEntityModel->FromActionIDRef = $session['FromActionIDRef'];
            $newActionEntityModel->ActionIDRef = $session['ActionIDRef'];
            $newActionEntityModel->ActionDate = $session['ActionDate'];
            $newActionEntityModel->ActionDateHijri = $session['ActionDateHijri'];
            $newActionEntityModel->UserIDRef = Yii::$app->user->id;
            $newActionEntityModel->OBSCategory3IDRef = Yii::$app->session->get('ObsPosition');
            $newActionEntityModel->ipAddress = $ip;
            $newActionEntityModel->ComputerName = \app\components\Utility::findComputerNameByIp($ip);

            $newActionEntityModel->ActionMessage = $session['ActionMessage'];
            $newActionEntityModel->ActionAutoMessage = $session['ActionAutoMessage'];
            $newActionEntityModel->LetterNumber = $session['LetterNumber'];
            $newActionEntityModel->hasChange = 1;
            $resNewEntityModel = $newActionEntityModel->save(); //save new Action Entity
            if (!$resNewEntityModel) {
                $transaction->rollBack();
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Error on saving new action entity.Please try letter!');
                return $result;
            }

            //CalculateDuration
            Yii::$app->dbTehran->createCommand()->update(ActionEntity::tableName(), [
                        'durationInMinute' => ActionEntity::duration(0),
                            ], 'ProcessEntityIDRef=' . $processEntityModel->ProcessEntityID)
                    ->execute();

            //update current Process Entity model
            $processEntityModel->LastUserIDRef = Yii::$app->user->id;
            $processEntityModel->LastActionIDRef = $session['ActionIDRef'];
            $processEntityModel->LastActionDateHijri = $session['ActionDateHijri'];
            $processEntityModel->LastActionMessage = $session['ActionMessage'];
            $processEntityModel->LastActionAutoMessage = $session['ActionAutoMessage'];
            $processEntityModel->hasChange = 1;
            $resProcessEntity = $processEntityModel->save(); //update Process Entity
            if (!$resProcessEntity) {
                $transaction->rollBack();
                $result['result'] = false;
                $result['message'][] = Yii::t('workflow', 'Error on updating process entity.Please try later!');
                return $result;
            }

            //if the action end of the processing end
            $toactionEntityModel = Action::find()
                    ->where(['ActionID' => $session['ActionIDRef']])
                    ->one();
            if ($toactionEntityModel->isEndAction > 0) {
                $processEntityModel->Status = 0;
                $processEntityModel->hasChange = 1;
                if (!$processEntityModel->save()) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Error on updating process entity.Please try later!');
                    return $result;
                }
            }
            //==============
            //Stamp_LicenseType صدور مجوز
            //==============

            if ($processEntityModel->process->ProccessTypeIDRef == Process::STAMP_DOCUMENT_PROCESS_TYPE) {

                $stampId = !empty($processEntityModel->ObjectIDRef)
                    ? $processEntityModel->ObjectIDRef
                    : $processEntityModel->StampIDRef;

                if (!empty($stampId)) {
                    $stampModel = \app\modules\document\models\StampDocument::find()
                        ->where(['StampId' => $stampId, 'isDeleted' => 0])
                        ->one();

                    if ($stampModel) {
                        // از 1177 به 1181 → نوع مجوز = 1
                        if ($session['FromActionIDRef'] == 1177 && $session['ActionIDRef'] == 1181) {
                            $stampModel->LicenseIssueType = 1;
                            $stampModel->save(false);
                        }

                        // از 1177 به 1178 → نوع مجوز = 2
                        if ($session['FromActionIDRef'] == 1177 && $session['ActionIDRef'] == 1178) {
                            $stampModel->LicenseIssueType = 2;
                            $stampModel->save(false);
                        }

                        // اگر بخواهی resolvePermit را هم نگه داری:
                        $permitResult = $stampModel->resolvePermit($processEntityModel->ProcessEntityID);
                        if ($permitResult['result'] == false) {
                            $transaction->rollBack();
                            $result['result'] = false;
                            foreach ($permitResult['message'] as $message) {
                                $result['message'][] = $message;
                            }
                            return $result;
                        }
                    }
                }
            }
            
            //find process
            $processModel = Process::find()
                    ->where(['ProcessID' => $processEntityModel->ProcessIDRef])
                    ->one();
            if (!isset($processModel->ProcessID)) {
                throw new \yii\web\HttpException(403, 'Not found Process');
            }
            if ($processModel->ProccessTypeIDRef == 7) { // WorkFLow Debit Memo
                $debitModel = \app\modules\debitmemo\models\Debitmemo::find()
                        ->where(['id' => $processEntityModel->debitmemoIdRef])
                        ->one();
                if (!isset($debitModel)) {
                    throw new \yii\web\HttpException(403, "Not found DebitMemo!!");
                }
                if ($debitModel->contract->ContractType != 13) {
                    if (floatval($debitModel->cumulativeApproved1(6)) > floatval($debitModel->contract->lastRider->Price1)) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Debitmemo Cumulative more than Contract Price1');

                        return $result;
                    }

                    if (floatval($debitModel->cumulativeApproved2(6)) > floatval($debitModel->contract->lastRider->Price2)) {
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Debitmemo Cumulative more than Contract Price2');
                        return $result;
                    }
                }
            }

            if ($processModel->ProccessTypeIDRef == 4) { // WorkFLow Budget
                //اگر ارسال شده به اکشن آخری ان هنگام ذخیره ی درصد های برنامه انجام گیرد
                $budgetNodeTable = \app\modules\budget\models\BudgetNode::tableName();
                $budgetNodeAmountTable = \app\modules\budget\models\BudgetNodeAmount::tableName();
                if ($session['ActionIDRef'] == 853 || $session['ActionIDRef'] == 653) {
                    // اگر از نوع برنامه بود
                    // و اگر مبلغ بودجه صفر نبود باید بر اساس مبالغ وارد شده درج گردد
                    $budgetNode = \app\modules\budget\models\BudgetNode::find()->where(["$budgetNodeTable.BudgetIDRef" => $processEntityModel->BudgetIDRef])->all();

                    if (isset($processEntityModel->budget->PlanIDRef) and $processEntityModel->budget->plan->PlanAmount > 0) {
                        // بدست آوردن در صد هم ماه به تفکیک
                        $budgetNodeModel = \app\modules\budget\models\BudgetNode::find()
                                        ->innerJoin($budgetNodeAmountTable, "$budgetNodeTable.BudgetNodeID=$budgetNodeAmountTable.BudgetNodeIDRef")
                                        ->addSelect([
                                            "rate" => "(($budgetNodeAmountTable.Budget1Amount/$budgetNodeTable.Committed1L)*100)",
                                            "month" => "right($budgetNodeAmountTable.Month,2)"
                                        ])
                                        ->where(["$budgetNodeTable.BudgetIDRef" => $processEntityModel->BudgetIDRef])->asArray()->all();
                        // ارایه یندی مقدار درصد ها برای جای گذاری بهتر در داخل تابع ذخیره سازی
                        $progressRate[] = [];
                        foreach ($budgetNodeModel as $key => $value) {
                            $progressRate[$value['month']] = $value['rate'];
                        }
                        // ذخیره ی درصد های برنامه ها
                        $resPlan = Yii::$app->dbTehran->createCommand()->update(\app\modules\budget\models\Plan::tableName(), [
                                    'progressRate01' => $progressRate['01'],
                                    'progressRate02' => $progressRate['02'],
                                    'progressRate03' => $progressRate['03'],
                                    'progressRate04' => $progressRate['04'],
                                    'progressRate05' => $progressRate['05'],
                                    'progressRate06' => $progressRate['06'],
                                    'progressRate07' => $progressRate['07'],
                                    'progressRate08' => $progressRate['08'],
                                    'progressRate09' => $progressRate['09'],
                                    'progressRate10' => $progressRate['10'],
                                    'progressRate11' => $progressRate['11'],
                                    'progressRate12' => $progressRate['12'],
                                        ], 'PlanID=' . $processEntityModel->budget->PlanIDRef)
                                ->execute();
                    }
                } else if ($session['ActionIDRef'] == 479) {
                    $budget = \app\modules\budget\models\Budget::find()->where(["BudgetID" => $processEntityModel->BudgetIDRef])->one();
                    $budget->DepartementObsIDRef = Yii::$app->session->get('ObsPosition');
                    $budget->save();
                }
            }

            if ($processModel->ProccessTypeIDRef == 8) { // اگر فرایند از نوع کلیم هست
                $claimModel = \app\modules\claim\models\Claim::find()
                        ->where(['id' => $processEntityModel->claimIdRef])
                        ->one();
                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $session['ActionIDRef'], 'paramTitle' => 'AmountStatus'])
                        ->one();
                $claimModel->status = $actioParamModel->value1;
                $resClaim = $claimModel->save(false);
                if (!$resClaim) {
                    throw new \yii\web\HttpException(501, 'Not saved Claim');
                }
            }
            if ($processModel->ProccessTypeIDRef == 10) { // Permission
                $permissionModel = \app\modules\permission\models\Permission::find()
                        ->where(['id' => $processEntityModel->permission])
                        ->one();

                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $session['ActionIDRef'], 'paramTitle' => 'permission_amountStatus'])
                        ->one();
                $permissionModel->amountStatus = $actioParamModel->value1;
            }
            if ($processModel->ProccessTypeIDRef == 11) { // sourcing
                $sourcingModel = \app\modules\OBS\models\Person::find()
                        ->where(['PersonID' => $processEntityModel->PersonID])
                        ->one();
                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $session['ActionIDRef'], 'paramTitle' => 'verify', 'value1' => 1])
                        ->one();

                if (isset($actioParamModel)) {
                    $sourcingModel->setScenario('changePersonVerified');
                    $sourcingModel->verified = $actioParamModel->value1;
                    $sourcingModel->save();
                }
            }
            if ($processModel->ProccessTypeIDRef == 1) {   //اگر فرایند از نوع صورت وضعیت بود
                $invoiceModel = InvoiceEdit::find()
                        ->where(['InvoiceID' => $processEntityModel->InvoiceIDRef])
                        ->one();
                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $session['ActionIDRef'], 'paramTitle' => 'AmountStatus'])
                        ->one();
                $invoiceModel->AmountStatus = $actioParamModel->value1;
                $invoiceModel->hasChange = 1;
                $resInvoice = $invoiceModel->save(false);
                if (!$resInvoice) {
                    throw new \yii\web\HttpException(501, 'Not saved Invoice');
                }
                if ($invoiceModel->InvoiceType == 3) {
                    $contractModel = $invoiceModel->contract;
                    $contractModel->setScenario('updateStatus');
                    if ($actioParamModel->value1 == 5) {
                        // Convert ContractStatus to "Completed"
                        if (!isset($contractModel->ContractID)) {
                            throw new \yii\web\BadRequestHttpException;
                        }

                        if ($contractModel->ContractStatusIDRef != 12) {
                            $contractModel->ContractStatusIDRef = 12;
                            if (!$contractModel->save()) {
                                $transaction->rollBack();
                                $result['result'] = false;
                                $result['message'][] = Yii::t('workflow', 'Data Not saved. try later!!');
                                return $result;
                            }
                        }
                    } else if ($actioParamModel->value1 == 1) {
                        // Convert ContractStatus to "In Progress"
                        if (!isset($contractModel->ContractID)) {
                            throw new \yii\web\BadRequestHttpException;
                        }

                        if ($contractModel->ContractStatusIDRef != 11) {
                            $contractModel->ContractStatusIDRef = 11;
                            if (!$contractModel->save()) {
                                $transaction->rollBack();
                                $result['result'] = false;
                                $result['message'][] = Yii::t('workflow', 'Data Not saved. try later!!');
                                return $result;
                            }
                        }
                    }

                    // For Commission Contract
                    $workFlowArray3_Comm = [88, 93, 294, 106, 94, 103, 105, 199, 295];         // workFLowNo=3
                    $workFlowArray6_Comm = [154, 155, 157, 158, 159, 160, 200, 345];          // workFLowNo=6
                    $workFlowArray10_Comm = [251, 252, 254, 255, 256, 257, 258, 259, 261];     // workFLowNo=10
                    $workFlowArray12_Comm = [336, 337, 338, 339, 340, 341, 342, 343];         // workFLowNo=12
                    $commissionArray = array_merge($workFlowArray3_Comm, $workFlowArray6_Comm, $workFlowArray10_Comm, $workFlowArray12_Comm);

                    if (in_array($session['ActionIDRef'], $commissionArray)) {
                        if (!isset($contractModel->ContractID)) {
                            throw new \yii\web\BadRequestHttpException;
                        }

                        if ($contractModel->ContractStatusIDRef != 14) {
                            $contractModel->ContractStatusIDRef = 14;
                            if (!$contractModel->save()) {
                                $transaction->rollBack();
                                $result['result'] = false;
                                $result['message'][] = Yii::t('workflow', 'Data Not saved. try later!!');
                                return $result;
                            }
                        }
                    }
                }
            }
            if ($processModel->ProccessTypeIDRef == 13) {   //اگر فرایند از نوع سند اختتام قرارداد بود
                $contractDocumentModel = ContractDocument::find()
                        ->where(['ContractDocumentID' => $processEntityModel->ContractClosingDocumentIDRef])
                        ->one();
                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $session['ActionIDRef'], 'paramTitle' => 'status'])
                        ->one();
                $contractDocumentModel->status = $actioParamModel->value1;
                $contractDocumentModel->hasChange = 1;
                $res = $contractDocumentModel->save(false);
                if (!$res) {
                    throw new \yii\web\HttpException(501, 'Not saved Document');
                }
                $contractModel = $contractDocumentModel->contract;
                $contractModel->setScenario('updateClosingProgress');
                if (!$contractModel->updateClosingProgress()) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Data Not saved. try later!!');
                    return $result;
                }
            }

            if ($processEntityModel->process->ProccessTypeIDRef != Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
                //insert subprocess
                //subProcess1
                if ($ActionOrderModel->subProcessIdRef1 > 0) {
                    $actionModel = Action::find()
                            ->where(['processIdRef' => $ActionOrderModel->subProcessIdRef1])
                            ->andWhere('isStartAction > 0')
                            ->one();
                    if (!isset($actionModel->ActionID)) {
                        throw new \yii\web\HttpException(501, 'Not find Action');
                    }

                    $newSubProcessEntityModel = new ProcessEntity();
                    $newSubProcessEntityModel->ProcessEntityID = ProcessEntity::find()->max('ProcessEntityID') + 1;
                    $newSubProcessEntityModel->ProcessIDRef = $ActionOrderModel->subProcessIdRef1;
                    $newSubProcessEntityModel->LastUserIDRef = Yii::$app->user->id;
                    $newSubProcessEntityModel->LastActionIDRef = $actionModel->ActionID; //@subProcessFirstActionId1
                    $date = new jDateTime(true, true, 'Asia/Tehran');
                    $newSubProcessEntityModel->LastActionDateHijri = $date->date("Y/m/d-Hi", false, false);
                    $newSubProcessEntityModel->LastActionMessage = '';
                    $newSubProcessEntityModel->LastActionAutoMessage = '';
                    $newSubProcessEntityModel->InvoiceIDRef = $processEntityModel->InvoiceIDRef;
                    $newSubProcessEntityModel->CashRequestIDRef = $processEntityModel->CashRequestIDRef;
                    $newSubProcessEntityModel->WorkflowContractIDRef = $processEntityModel->WorkflowContractIDRef;
                    $newSubProcessEntityModel->BudgetIDRef = $processEntityModel->BudgetIDRef;
                    $newSubProcessEntityModel->Status = 1;
                    $newSubProcessEntityModel->parentProcessEntityIDRef = $processEntityModel->ProcessEntityID;
                    $newSubProcessEntityModel->hasChange = 1;
                    $resNewProcessEntity = $newSubProcessEntityModel->save();
                    if (!$resNewProcessEntity) {
                        $transaction->rollBack();
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Error on creating first sub process.Please try letter!');
                        return $result;
                    }

                    $newSubActionEntityModel = new ActionEntity();
                    $newSubActionEntityModel->ProcessEntityIDRef = $newSubProcessEntityModel->ProcessEntityID;
                    $newSubActionEntityModel->FromActionIDRef = null;
                    $newSubActionEntityModel->ActionIDRef = $actionModel->ActionID;
                    $newSubActionEntityModel->UserIDRef = Yii::$app->user->id;
                    $newSubActionEntityModel->ActionDate = $date->date("Y/m/d-Hi", false, false, false);
                    $newSubActionEntityModel->ActionDateHijri = $date->date("Y/m/d-Hi", false, false);
                    $newSubActionEntityModel->OBSCategory3IDRef = Yii::$app->session->get('ObsPosition');
                    $newSubActionEntityModel->ActionMessage = '';
                    $newSubActionEntityModel->ActionAutoMessage = '';
                    $newSubActionEntityModel->LetterNumber = '';
                    $newSubActionEntityModel->ComputerName = \app\components\Utility::findComputerNameByIp($ip);
                    $newSubActionEntityModel->ipAddress = $ip;
                    $newSubActionEntityModel->hasChange = 1;
                    $resNewsubAction = $newSubActionEntityModel->save();
                    if (!$resNewsubAction) {
                        $transaction->rollBack();
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Error on creating first sub process action entity.Please try letter!');
                        return $result;
                    }
                }
                //subProcess2
                if ($ActionOrderModel->subProcessIdRef2 > 0) {
                    $actionModel_2 = Action::find()
                            ->where(['processIdRef' => $ActionOrderModel->subProcessIdRef2])
                            ->andWhere('isStartAction > 0')
                            ->one();
                    if (!isset($actionModel_2->ActionID)) {
                        throw new \yii\web\HttpException(501, 'Not find Action');
                    }
                    $newSubProcessEntityModel_2 = new ProcessEntity();
                    $newSubProcessEntityModel_2->ProcessEntityID = ProcessEntity::find()->max('ProcessEntityID') + 1;
                    $newSubProcessEntityModel_2->ProcessIDRef = $ActionOrderModel->subProcessIdRef2;
                    $newSubProcessEntityModel_2->LastUserIDRef = Yii::$app->user->id;
                    $newSubProcessEntityModel_2->LastActionIDRef = $actionModel_2->ActionID; //@subProcessFirstActionId1
                    $date = new jDateTime(true, true, 'Asia/Tehran');
                    $newSubProcessEntityModel_2->LastActionDateHijri = $date->date("Y/m/d-Hi", false, false);
                    $newSubProcessEntityModel_2->LastActionMessage = '';
                    $newSubProcessEntityModel_2->LastActionAutoMessage = '';
                    $newSubProcessEntityModel_2->InvoiceIDRef = $processEntityModel->InvoiceIDRef;
                    $newSubProcessEntityModel_2->CashRequestIDRef = $processEntityModel->CashRequestIDRef;
                    $newSubProcessEntityModel_2->WorkflowContractIDRef = $processEntityModel->WorkflowContractIDRef;
                    $newSubProcessEntityModel_2->BudgetIDRef = $processEntityModel->BudgetIDRef;
                    $newSubProcessEntityModel_2->Status = 1;
                    $newSubProcessEntityModel_2->parentProcessEntityIDRef = $processEntityModel->ProcessEntityID;
                    $newSubProcessEntityModel_2->hasChange = 1;
                    if (!$newSubProcessEntityModel_2->save()) {
                        $transaction->rollBack();
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Error on creating second sub process.Please try letter!');
                        return $result;
                    }

                    $newSubActionEntityModel_2 = new ActionEntity();
                    $newSubActionEntityModel_2->ProcessEntityIDRef = $newSubProcessEntityModel_2->ProcessEntityID;
                    $newSubActionEntityModel_2->FromActionIDRef = null;
                    $newSubActionEntityModel_2->ActionIDRef = $actionModel_2->ActionID;
                    $newSubActionEntityModel_2->UserIDRef = Yii::$app->user->id;
                    $newSubActionEntityModel_2->ActionDate = $date->date("Y/m/d-Hi", false, false, false);
                    $newSubActionEntityModel_2->ActionDateHijri = $date->date("Y/m/d-Hi", false, false);
                    $newSubActionEntityModel_2->OBSCategory3IDRef = Yii::$app->session->get('ObsPosition');
                    $newSubActionEntityModel_2->ActionMessage = '';
                    $newSubActionEntityModel_2->ActionAutoMessage = '';
                    $newSubActionEntityModel_2->LetterNumber = '';
                    $newSubActionEntityModel_2->ComputerName = \app\components\Utility::findComputerNameByIp($ip);
                    $newSubActionEntityModel_2->ipAddress = $ip;
                    $newSubActionEntityModel_2->hasChange = 1;
                    $resNewsubAction_2 = $newSubActionEntityModel_2->save();
                    if (!$resNewsubAction_2) {
                        $transaction->rollBack();
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Error on creating second sub process action entity.Please try letter!');
                        return $result;
                    }
                }

                //subProcess3
                if ($ActionOrderModel->subProcessIdRef3 > 0) {
                    $actionModel_3 = Action::find()
                            ->where(['processIdRef' => $ActionOrderModel->subProcessIdRef3])
                            ->andWhere('isStartAction > 0')
                            ->one();
                    if (!isset($actionModel_3->ActionID)) {
                        throw new \yii\web\HttpException(501, 'Not find Action');
                    }
                    $newSubProcessEntityModel_3 = new ProcessEntity();
                    $newSubProcessEntityModel_3->ProcessEntityID = ProcessEntity::find()->max('ProcessEntityID') + 1;
                    $newSubProcessEntityModel_3->ProcessIDRef = $ActionOrderModel->subProcessIdRef3;
                    $newSubProcessEntityModel_3->LastUserIDRef = Yii::$app->user->id;
                    $newSubProcessEntityModel_3->LastActionIDRef = $actionModel_3->ActionID; //@subProcessFirstActionId1
                    $date = new jDateTime(true, true, 'Asia/Tehran');
                    $newSubProcessEntityModel_3->LastActionDateHijri = $date->date("Y/m/d-Hi", false, false);
                    $newSubProcessEntityModel_3->LastActionMessage = '';
                    $newSubProcessEntityModel_3->LastActionAutoMessage = '';
                    $newSubProcessEntityModel_3->InvoiceIDRef = $processEntityModel->InvoiceIDRef;
                    $newSubProcessEntityModel_3->CashRequestIDRef = $processEntityModel->CashRequestIDRef;
                    $newSubProcessEntityModel_3->WorkflowContractIDRef = $processEntityModel->WorkflowContractIDRef;
                    $newSubProcessEntityModel_3->BudgetIDRef = $processEntityModel->BudgetIDRef;
                    $newSubProcessEntityModel_3->Status = 1;
                    $newSubProcessEntityModel_3->parentProcessEntityIDRef = $processEntityModel->ProcessEntityID;
                    $newSubProcessEntityModel_3->hasChange = 1;
                    $resNewProcessEntity_3 = $newSubProcessEntityModel_3->save();
                    if (!$resNewProcessEntity_3) {
                        $transaction->rollBack();
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Error on creating third sub process.Please try letter!');
                        return $result;
                    }

                    $newSubActionEntityModel_3 = new ActionEntity();
                    $newSubActionEntityModel_3->ProcessEntityIDRef = $newSubProcessEntityModel_3->ProcessEntityID;
                    $newSubActionEntityModel_3->FromActionIDRef = null;
                    $newSubActionEntityModel_3->ActionIDRef = $actionModel_3->ActionID;
                    $newSubActionEntityModel_3->UserIDRef = Yii::$app->user->id;
                    $newSubActionEntityModel_3->ActionDate = $date->date("Y/m/d-Hi", false, false, false);
                    $newSubActionEntityModel_3->ActionDateHijri = $date->date("Y/m/d-Hi", false, false);
                    $newSubActionEntityModel_3->OBSCategory3IDRef = Yii::$app->session->get('ObsPosition');
                    $newSubActionEntityModel_3->ActionMessage = '';
                    $newSubActionEntityModel_3->ActionAutoMessage = '';
                    $newSubActionEntityModel_3->LetterNumber = '';
                    $newSubActionEntityModel_3->ComputerName = \app\components\Utility::findComputerNameByIp($ip);
                    $newSubActionEntityModel_3->ipAddress = $ip;
                    $newSubActionEntityModel_3->hasChange = 1;
                    $resNewsubAction_3 = $newSubActionEntityModel_3->save();
                    if (!$resNewsubAction_3) {
                        $transaction->rollBack();
                        $result['result'] = false;
                        $result['message'][] = Yii::t('workflow', 'Error on creating third sub process action entity.Please try letter!');
                        return $result;
                    }
                }
            }

            if (($session['FromActionIDRef'] == 488 && $session['ActionIDRef'] == 493) || ($session['FromActionIDRef'] == 599 && $session['ActionIDRef'] == 603) || ($session['FromActionIDRef'] == 657 && $session['ActionIDRef'] == 661)) {
                if (Yii::$app->params['dmbs'] == 'mysql') {
                    $res = false;
                    $settingModel = \app\modules\management\models\Setting::findOne(['`key`' => 'UpdateStaticContractList']);
                    if (isset($settingModel)) {
                        $settingModel->value1 = 'waiting';
                        $res = $settingModel->save();
                    }
                    if (Yii::$app->params['autoReplicate'] == true) {
                        $settingModelLocal = \app\modules\management\models\SettingLocal::findOne(['`key`' => 'UpdateStaticContractList']);
                        if (isset($settingModelLocal)) {
                            $settingModelLocal->value1 = 'waiting';
                            $settingModelLocal->save();
                        }
                    }
                } else {
                    $res = Yii::$app->dbTehran->createCommand()
                            ->update('tblSoftwareInfo', ['value' => 'waiting'], 'ID = 20')
                            ->execute();
                }
                if (!$res) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message'][] = Yii::t('workflow', 'Error on updating SoftwareInfo table.Please try letter!');
                    return $result;
                }
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToList($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'][] = $value;
                    }

                    return $result;
                }
            }
            // ?? [model].[workflowContractAddToListFinancialCode]
            if (($session['FromActionIDRef'] == 492 && $session['ActionIDRef'] == 518) || ($session['FromActionIDRef'] == 605 && $session['ActionIDRef'] == 606) || ($session['FromActionIDRef'] == 663 && $session['ActionIDRef'] == 664)) {
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToListFinancialCode($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'][] = $value;
                    }

                    return $result;
                }
            }

            //??[model].[workflowContractAddToListPmsPlan]
            if (
                    ($session['FromActionIDRef'] == 519 && $session['ActionIDRef'] == 523) || ($session['FromActionIDRef'] == 532 && $session['ActionIDRef'] == 536) || ($session['FromActionIDRef'] == 607 && $session['ActionIDRef'] == 611) || ($session['FromActionIDRef'] == 622 && $session['ActionIDRef'] == 626) || ($session['FromActionIDRef'] == 665 && $session['ActionIDRef'] == 669) || ($session['FromActionIDRef'] == 680 && $session['ActionIDRef'] == 684)
            ) {
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToListPmsPlan($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'][] = $value;
                    }

                    return $result;
                }
            }

            // ?? [model].[workflowContractAddToListRevision]
            if (($session['FromActionIDRef'] == 528 && $session['ActionIDRef'] == 531) || ($session['FromActionIDRef'] == 618 && $session['ActionIDRef'] == 621) || ($session['FromActionIDRef'] == 676 && $session['ActionIDRef'] == 679)) {
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToListRevision($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'][] = $value;
                    }

                    return $result;
                }
            }
            // درخواست اتوماسیون
            if ((($session['FromActionIDRef'] == 968 || $session['FromActionIDRef'] == 978) && ($session['ActionIDRef'] == 972 || $session['ActionIDRef'] == 973))
            ) {
                // || ($session['FromActionIDRef'] == 999 && ($session['ActionIDRef'] == 997 || $session['ActionIDRef'] == 1000))
                $jdate = new jDateTime(true, true, 'Asia/Tehran');
                $current = $jdate->date("Y/m/d", false, false);
                $automationModel = $processEntityModel->automation;
                if ($session['FromActionIDRef'] == 968 || $session['FromActionIDRef'] == 978) {
                    $humanModel = $automationModel->humanResource;
                    $humanObsModel = \app\modules\automation\models\HumanResourceObs::find()->where(['ObsIdRef' => $automationModel->Position, 'HumanIdRef' => $humanModel->Id])->andWhere(['State' => 'New'])->andWhere(['<>', 'COALESCE(isDeleted,0)', '1'])->one();

                    if (!empty($automationModel->DisableOldPosition)) {
                        $obsIds = explode(',', $automationModel->DisableOldPosition);
                        if (count($obsIds) > 0) {
                            $userModel = $humanModel->user;
                            if (!in_array('other', $obsIds)) {
                                \app\modules\automation\models\HumanResourceObs::updateAll([
                                    'State' => 'Settlement',
                                    'LastUpdate' => $current,
                                    'LastUserIDRef' => Yii::$app->user->id
                                        ], ['and', ['=', 'HumanIdRef', $humanModel->Id], ['in', 'ObsIdRef', $obsIds], ['=', 'State', 'Employment'], ['<>', 'COALESCE(isDeleted,0)', '1']]);

                                if (isset($userModel)) {
                                    \app\modules\account\models\UserObs::updateAll([
                                        'isActive' => 0,
                                        'LastUpdate' => $current,
                                        'LastUserIDRef' => Yii::$app->user->id
                                            ], ['and', ['=', 'UserIDRef', $userModel->UserID], ['in', 'OBSCategory3IDRef', $obsIds], ['=', 'isActive', 1], ['<>', 'COALESCE(isDeleted,0)', '1']]);
                                }
                            } else {
                                $activeObsIds = \app\modules\automation\models\HumanResourceObs::find()->select('ObsIdRef')->where(['HumanIdRef' => $humanModel->Id])->andWhere(['=', 'State', 'Employment'])->andWhere(['<>', 'COALESCE(isDeleted,0)', '1'])->column();
                                array_push($activeObsIds, $automationModel->Position);
                                $array_diff = array_diff($activeObsIds, $obsIds);
                                \app\modules\automation\models\HumanResourceObs::updateAll([
                                    'State' => 'Settlement',
                                    'LastUpdate' => $current,
                                    'LastUserIDRef' => Yii::$app->user->id
                                        ], ['and', ['=', 'HumanIdRef', $humanModel->Id], ['not in', 'ObsIdRef', $array_diff], ['=', 'State', 'Employment'], ['<>', 'COALESCE(isDeleted,0)', '1']]);

                                if (isset($userModel)) {
                                    \app\modules\account\models\UserObs::updateAll([
                                        'isActive' => 0,
                                        'LastUpdate' => $current,
                                        'LastUserIDRef' => Yii::$app->user->id
                                            ], ['and', ['=', 'UserIDRef', $userModel->UserID], ['not in', 'OBSCategory3IDRef', $array_diff], ['=', 'isActive', 1], ['<>', 'COALESCE(isDeleted,0)', '1']]);
                                }
                            }
                        }
                    }

                    $oldHumanModel = $automationModel->oldHumanResource;
                    if (isset($oldHumanModel)) {
                        $oldHumanObsModel = \app\modules\automation\models\HumanResourceObs::find()->where(['ObsIdRef' => $automationModel->Position, 'HumanIdRef' => $oldHumanModel->Id])->andWhere(['=', 'State', 'Employment'])->andWhere(['<>', 'COALESCE(isDeleted,0)', '1'])->one();
                        if (isset($oldHumanObsModel)) { // تسویه کردن نفر قبلی
                            $oldHumanObsModel->setScenario('workflow');
                            $oldHumanObsModel->State = 'Settlement';
                            $oldHumanObsModel->LastUpdate = $current;
                            $oldHumanObsModel->LastUserIDRef = Yii::$app->user->id;
                            $oldHumanObsModel->save();
                        }

                        $oldUserModel = $oldHumanModel->user;
                        if (isset($oldUserModel)) { // غیرفعال کردن جایگاه های نفر قبلی
                            $oldUserObs = \app\modules\account\models\UserObs::find()->where(['OBSCategory3IDRef' => $automationModel->Position, 'UserIDRef' => $oldUserModel->UserID])->andWhere(['isActive' => 1])->andWhere(['<>', 'COALESCE(isDeleted,0)', '1'])->one();
                            if (isset($oldUserObs)) {
                                $oldUserObs->isActive = 0;
                                $oldUserObs->LastUpdate = $current;
                                $oldUserObs->LastUserIDRef = Yii::$app->user->id;
                                $oldUserObs->save();
                            }
                        }
                    }
                } else {
                    $humanModel = $processEntityModel->network->humanResource;
                    $humanObsModel = \app\modules\automation\models\HumanResourceObs::find()->where(['ObsIdRef' => $processEntityModel->network->Position, 'HumanIdRef' => $processEntityModel->network->HumanResourceIdRef])->andWhere(['<>', 'COALESCE(isDeleted,0)', '1'])->andWhere(['State' => 'New'])->one();
                }
                $humanModel->setScenario('workflow');
                $humanModel->Verified = 1;
                $humanModel->Status = 'Employment';
                $humanModel->LastUpdate = $current;
                $humanModel->LastUserIDRef = Yii::$app->user->id;
                $humanModel->save();

                if (isset($humanObsModel)) {
                    $humanObsModel->setScenario('workflow');
                    $humanObsModel->State = 'Employment';
                    $humanObsModel->LastUpdate = $current;
                    $humanObsModel->LastUserIDRef = Yii::$app->user->id;
                    $humanObsModel->save();
                }
            }
            // رد درخواست اتوماسیون
            if ($session['ActionIDRef'] == 975) {
                $jdate = new jDateTime(true, true, 'Asia/Tehran');
                $current = $jdate->date("Y/m/d", false, false);
                $automationModel = $processEntityModel->automation;
                \app\modules\automation\models\HumanResourceObs::updateAll([
                    'isDeleted' => 1,
                    'LastUpdate' => $current,
                    'LastUserIDRef' => Yii::$app->user->id
                        ], ['and', ['=', 'HumanIdRef', $automationModel->HumanResourceIdRef], ['=', 'ObsIdRef', $automationModel->Position], ['=', 'State', 'New'], ['<>', 'COALESCE(isDeleted,0)', '1']]);
            }

            // درخواست قطع دسترسی
            if ($session['ActionIDRef'] == 1193) {
                $jdate = new jDateTime(true, true, 'Asia/Tehran');
                $now = $jdate->date("Y/m/d", false, false);
                $userObsTable = \app\modules\account\models\UserObs::tableName();
                $obsTable = \app\modules\OBS\models\Obsnew::tableName();
                $humanObsTable = \app\modules\automation\models\HumanResourceObs::tableName();

                $automationModel = $processEntityModel->automation;
                $humanModel = $automationModel->humanResource;
                $userModel = $humanModel->user;
                $obsIds = explode(',', $automationModel->DisableOldPosition);

                if ($automationModel->IsSettlement === 1) {
                    if ($humanModel->CompanyIDRef == $automationModel->CompanyIDRef) {
                        $humanModel->setScenario('workflow');
                        $humanModel->Status = 'Settlement';
                        $humanModel->SettlementDate = $now;
                        $humanModel->LastUserIDRef = 1; // admin;
                        $humanModel->LastUpdate = $now;
                        $humanModel->save();
                    }

                    $accessLevelModel = \app\modules\automation\models\AccessLevel::find()
                            ->where(['CompanyIDRef' => $automationModel->CompanyIDRef])
                            ->andWhere(['HumanResourceIdRef' => $automationModel->HumanResourceIdRef])
                            ->one();
                    if (!empty($accessLevelModel)) {
                        $accessLevelModel->HasSecret = 0;
                        $accessLevelModel->HasTopSecret = 0;
                        $accessLevelModel->LastUserIDRef = 1; // admin
                        $accessLevelModel->lastUpdate = $now;
                        $accessLevelModel->SecretLastUpdate = $now;
                        $accessLevelModel->TopSecretLastUpdate = $now;
                        $accessLevelModel->Description = 'بروز شده توسط سیستم به علت قطع دسترسی سامانه ها';
                        $accessLevelModel->setScenario('workflow');
                        $accessLevelModel->save();
                    }

                    \app\modules\OBS\models\Management::updateAll([
                        'LastUpdate' => $now,
                        'LastUserIDRef' => 1, // admin
                        'expirationDate' => $now,
                            ],
                            "CompanyIDRef=$automationModel->CompanyIDRef && HumanIDRef=$automationModel->HumanResourceIdRef && expirationDate is null"
                    );
                    $activeHumanObsIds = \app\modules\automation\models\HumanResourceObs::find()->innerJoin("$obsTable", "$obsTable.obsId=$humanObsTable.ObsIdRef")->select("$humanObsTable.ObsIdRef")->where(['HumanIdRef' => $humanModel->Id])->andWhere(["$obsTable.companyIdref" => $automationModel->CompanyIDRef])->andWhere(['=', 'State', 'Employment'])->andWhere(['<>', "COALESCE($humanObsTable.isDeleted,0)", '1'])->column();
                } else {
                    $activeHumanObsIds = $obsIds;
                }
                \app\modules\automation\models\HumanResourceObs::updateAll([
                    'State' => 'Settlement',
                    'LastUpdate' => $now,
                    'LastUserIDRef' => 1, // admin
                        ], ['and', ['=', 'HumanIdRef', $humanModel->Id], ['in', 'ObsIdRef', $activeHumanObsIds], ['=', 'State', 'Employment'], ['<>', 'COALESCE(isDeleted,0)', '1']]);

                if (isset($userModel)) {
                    if ($automationModel->IsSettlement === 1) {
                        $activeObsIds = \app\modules\account\models\UserObs::find()->innerJoin("$obsTable", "$obsTable.obsId=$userObsTable.OBSCategory3IDRef")->select("$obsTable.obsId")->where(['UserIDRef' => $userModel->UserID])->andWhere(["$obsTable.companyIdref" => $automationModel->CompanyIDRef])->andWhere(["$userObsTable.isActive" => 1])->andWhere(['<>', "COALESCE($userObsTable.isDeleted,0)", '1'])->column();
                    } else {
                        $activeObsIds = $obsIds;
                    }
                    \app\modules\account\models\UserObs::updateAll([
                        'isActive' => 0,
                        'LastUpdate' => $now,
                        'LastUserIDRef' => 1, // admin
                            ], ['and', ['=', 'UserIDRef', $userModel->UserID], ['in', 'OBSCategory3IDRef', $activeObsIds], ['=', 'isActive', 1], ['<>', 'COALESCE(isDeleted,0)', '1']]);
                    
                    $exists = \app\modules\account\models\UserObs::find()->where(['isActive' => 1])->andWhere(['<>', 'COALESCE(isDeleted,0)', '1'])->exists();
                    if($automationModel->IsSettlement === 1 && !$exists) {
                        $userModel->setScenario('deActive');
                        $userModel->isActive = 0;
                        $userModel->save();
                    }
                }
            }

            // درخواست اتوماسیون
            /* if (($session['FromActionIDRef'] == 978 && ($session['ActionIDRef'] == 972 || $session['ActionIDRef'] == 973))) {
              $jdate = new jDateTime(true, true, 'Asia/Tehran');
              $automationModel = $processEntityModel->automation;
              $humanModel = $automationModel->humanResource;
              $humanModel->setScenario('workflow');
              $humanModel->Status = 'Employment';
              $humanModel->ObsIDRef = $automationModel->Position;
              $humanModel->CompanyIDRef = $automationModel->CompanyIDRef;
              $humanModel->LastUserIDRef = Yii::$app->user->id;
              $humanModel->LastUpdate = $jdate->date("Y/m/d", false, false);
              $humanModel->save();
              } */
            // درخواست شناسه کاربری
            if (($session['FromActionIDRef'] == 999 && ($session['ActionIDRef'] == 997 || $session['ActionIDRef'] == 1000))) {
                $jdate = new jDateTime(true, true, 'Asia/Tehran');
                $current = $jdate->date("Y/m/d", false, false);
                $humanModel = $processEntityModel->network->humanResource;
                $humanModel->setScenario('workflow');
                $humanModel->Verified = 1;
                $humanModel->Status = 'Employment';
                $humanModel->LastUpdate = $current;
                $humanModel->LastUserIDRef = Yii::$app->user->id;
                $humanModel->save();
            }
            // درخواست سطح دسترسی
            if (($session['FromActionIDRef'] == 991 && ($session['ActionIDRef'] == 992 || $session['ActionIDRef'] == 993))) {
                $automationModel = $processEntityModel->automation;
                $accessLevelModel = \app\modules\automation\models\AccessLevel::find()
                        ->where(['CompanyIDRef' => $automationModel->CompanyIDRef])
                        ->andWhere(['HumanResourceIdRef' => $automationModel->HumanResourceIdRef])
                        ->one();
                if (empty($accessLevelModel)) {
                    $accessLevelModel = new \app\modules\automation\models\AccessLevel();
                    $accessLevelModel->CompanyIDRef = $automationModel->CompanyIDRef;
                    $accessLevelModel->HumanResourceIdRef = $automationModel->HumanResourceIdRef;
                }
                if (strpos($automationModel->SequerityLevel, 'top-secret') !== false) {
                    $accessLevelModel->HasSecret = 1;
                    $accessLevelModel->HasTopSecret = 1;
                } else {
                    $accessLevelModel->HasSecret = 1;
                    $accessLevelModel->HasTopSecret = 0;
                }
                $jdate = new jDateTime(true, true, 'Asia/Tehran');
                $accessLevelModel->LastUserIDRef = Yii::$app->user->id;
                $accessLevelModel->lastUpdate = $jdate->date("Y/m/d", false, false);
                $accessLevelModel->SecretLastUpdate = $accessLevelModel->lastUpdate;
                $accessLevelModel->TopSecretLastUpdate = $accessLevelModel->lastUpdate;
                $accessLevelModel->setScenario('workflow');
                $accessLevelModel->save();
            }
            if ($session['FromActionIDRef'] == 1005) {
                /* $human = $networkModel->humanResource;
                  $userModel = \app\modules\account\models\User::find()->where(['nationalCode' => $human->NationalCode])->one();
                  if(isset($userModel)) {
                  $userModel->setScenario('create');
                  $networkUsername = \app\modules\automation\models\NetworkUsername::find()->where(['NetworkIDRef' => $this->NetworkID, 'Type' => 'CandoUsername'])->one();
                  $userModel->UserName = $networkUsername->Username;
                  } else {
                  $userModel = new \app\modules\account\models\User();
                  $userModel->setScenario('update');
                  }
                  $userModel->nationalCode = $human->NationalCode;
                  $userModel->FirstName = $human->FirstName;
                  $userModel->LastName = $human->LastName;
                  $userModel->mobile = $human->Mobile;
                  $userModel->isActive = 1;
                  $userModel->RequireChangePassword = 1;
                  $userModel->Password = hash('sha256', "123456");
                  $userModel->save(); */
            }

            // درخواست تسویه حساب
            if ($session['ActionIDRef'] == 1046 || $session['ActionIDRef'] == 1066) {
                $jdate = new jDateTime(true, true, 'Asia/Tehran');
                $now = $jdate->date("Y/m/d", false, false);

                $employeeSettlementModel = $processEntityModel->employeeSettlement;
                $humanModel = $employeeSettlementModel->humanResource;

                if ($humanModel->CompanyIDRef == $employeeSettlementModel->CompanyIDRef) {
                    $humanModel->setScenario('workflow');
                    $humanModel->Status = 'Settlement';
                    $humanModel->SettlementDate = $now;
                    $humanModel->LastUserIDRef = 1; // admin;
                    $humanModel->LastUpdate = $now;
                    $humanModel->save();
                }

                $accessLevelModel = \app\modules\automation\models\AccessLevel::find()
                        ->where(['CompanyIDRef' => $employeeSettlementModel->CompanyIDRef])
                        ->andWhere(['HumanResourceIdRef' => $employeeSettlementModel->HumanResourceIdRef])
                        ->one();
                if (!empty($accessLevelModel)) {
                    $accessLevelModel->HasSecret = 0;
                    $accessLevelModel->HasTopSecret = 0;
                    $accessLevelModel->LastUserIDRef = 1; // admin
                    $accessLevelModel->lastUpdate = $now;
                    $accessLevelModel->SecretLastUpdate = $now;
                    $accessLevelModel->TopSecretLastUpdate = $now;
                    $accessLevelModel->Description = 'بروز شده توسط سیستم به علت تسویه';
                    $accessLevelModel->setScenario('workflow');
                    $accessLevelModel->save();
                }

                \app\modules\OBS\models\Management::updateAll([
                    'LastUpdate' => $now,
                    'LastUserIDRef' => 1, // admin
                    'expirationDate' => $now,
                        ],
                        "CompanyIDRef=$employeeSettlementModel->CompanyIDRef && HumanIDRef=$employeeSettlementModel->HumanResourceIdRef && expirationDate is null"
                );
            }

            if ($processEntityModel->process->ProccessTypeIDRef != Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
                $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()
                        ->where(['ActionOrderIDRef' => $ActionOrderModel->ID])
                        ->andWhere(['<>', 'COALESCE(isDeleted, 0)', 1])
                        ->all();
                if (!empty($actionOrderMeasures)) {
                    $data = Yii::$app->request->post();
                    foreach ($actionOrderMeasures as $actionOrderMeasure) {
                        $id = $actionOrderMeasure->ActionOrderMeasureID;
                        if (!array_key_exists('value' . $id, $data)) {
                            $transaction->rollBack();
                            $result['result'] = false;
                            $result['message'][] = 'چک لیست ها تکمیل نشده است!';
                            return $result;
                        } else if ($data['value' . $id] == 2 && $data['description' . $id] == '') {
                            $transaction->rollBack();
                            $result['result'] = false;
                            $result['message'][] = 'درج توضیحات برای موارد عدم تایید الزامی است!';
                            return $result;
                        }
                        $actionOrderMeasureValue = new ActionOrderMeasureValue();
                        $actionOrderMeasureValue->ID = ActionOrderMeasureValue::find()->max('ID') + 1;
                        $actionOrderMeasureValue->ActionOrderMeasureIDRef = $id;
                        $actionOrderMeasureValue->value = $data['value' . $id];
                        $actionOrderMeasureValue->description = $data['description' . $id];
                        $actionOrderMeasureValue->ActionEntityIDRef = $actionEntityModel->ID;
                        $res = $actionOrderMeasureValue->save();
                        if (!$res) {
                            $transaction->rollBack();
                            $result['result'] = false;
                            $result['message'][] = 'Error on creating Measure!';
                            return $result;
                        }
                    }
                }
            }

            $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
            $activityManagement = new \app\components\ActivityManagement();
            $actionType = "Edit";
            $subType = "EditProcessEntity";
            $subTypeDescription["TableName"] = $processEntityTable;
            $subTypeDescription["RecordPrimaryKey"] = $processEntityModel->ProcessEntityID;
            $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

            $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
            $activityManagement1 = new \app\components\ActivityManagement();
            $subType = "EditActionEntity";
            $subTypeDescription["TableName"] = $actionEntityTable;
            $subTypeDescription["RecordPrimaryKey"] = $actionEntityModel->ID;
            $activityManagement1->sendActitvityLog($actionType, $subType, $subTypeDescription);

            $activityManagement2 = new \app\components\ActivityManagement();
            $subType = "NewActionEntity";
            $subTypeDescription["TableName"] = $actionEntityTable;
            $subTypeDescription["RecordPrimaryKey"] = $newActionEntityModel->ID;
            $activityManagement2->sendActitvityLog($actionType, $subType, $subTypeDescription);

            $transaction->commit();
            // ========== اضافه شده برای سند مهر ==========
if ((int)$processEntityModel->process->ProccessTypeIDRef === (int)Process::STAMP_DOCUMENT_PROCESS_TYPE) {
    $stampModel = $processEntityModel->getStampDocument()->one();
    $actionId = (int)$session['ActionIDRef'];

    if ($stampModel) {
        if ($actionId === 1184) {
            $stampModel->Status = \app\modules\document\models\StampDocument::STATUS_APPROVED;
            $stampModel->save(false, ['Status']);
        } elseif ($actionId === 1183) {
            $stampModel->Status = \app\modules\document\models\StampDocument::STATUS_REJECTED;
            $stampModel->save(false, ['Status']);
        }
    }
}
            
            // ========== پایان اضافه شده برای سند مهر ==========
/*
 * تغییر وضعیت نشان هنگام رد درخواست امحا
 */
if ((int)$processEntityModel->process->ProccessTypeIDRef === (int)Process::REFUND_STAMP_PROCESS_TYPE&& (int)$session['ActionIDRef'] === 1202) {
    $stampId = (int)$processEntityModel->ObjectIDRef;
    $stampModel =StampDocument::find()
        ->where(['StampId' => $stampId, 'isDeleted' => 0])
        ->one();
    $stampModel->Status = StampDocument::STATUS_REJECTED;
    if (!$stampModel->save(false, ['Status'])) {
        throw new \Exception('وضعیت نشان پس از رد درخواست امحا ذخیره نشد.');
    }
}
            Yii::$app->replication->batchSynchChange(\app\modules\workflow\models\ProcessEntity::className());
            Yii::$app->replication->batchSynchChange(\app\modules\workflow\models\ActionEntity::className());

            if (!empty($processEntityModel->InvoiceIDRef)) {
                Yii::$app->replication
                        ->where(['InvoiceID' => $processEntityModel->InvoiceIDRef])
                        ->batchSynchChange(InvoiceEdit::className());
            }
            if (isset($processModel) && $processModel->ProccessTypeIDRef == 1) {
                Yii::$app->replication
                        ->where(['ContractID' => $invoiceModel->ContractID])
                        ->batchSynchChange(Contract::className());
            }



            if (($session['FromActionIDRef'] == 488 && $session['ActionIDRef'] == 493) || ($session['FromActionIDRef'] == 599 && $session['ActionIDRef'] == 603) || ($session['FromActionIDRef'] == 657 && $session['ActionIDRef'] == 661)) {
//                $uid = Yii::$app->session->get('UserID');
//                \app\components\CandoHelper::updateUserObsEntityList('Contract', $uid);
//                $settingModel = \app\modules\management\models\Setting::findOne(['`key`' => 'UpdateStaticContractList']);
//                if (isset($settingModel)) {
//                    $settingModel->value1 = 'waiting';
//                    $settingModel->save();
//                }
//                if (Yii::$app->params['autoReplicate'] == true) {
//                    $settingModelLocal = \app\modules\management\models\SettingLocal::findOne(['`key`' => 'UpdateStaticContractList']);
//                    if (isset($settingModelLocal)) {
//                        $settingModelLocal->value1 = 'waiting';
//                        $settingModelLocal->save();
//                    }
//                }
//                Yii::$app->replication->batchSynchChange(\app\modules\OBS\models\Person::className());
//                Yii::$app->replication->batchSynchChange(Contract::className());
//                Yii::$app->replication->batchSynchChange(\app\modules\contract\models\ContractRevision::className());
//                Yii::$app->replication->batchSynchChange(\app\modules\contract\models\ContractEps::className());
//                Yii::$app->replication->batchSynchChange(\app\modules\contract\models\ContractNodeBudget::className());
//                Yii::$app->replication->batchSynchChange(ContractInvoiceTypeProcess::className());
//                Yii::$app->replication->batchSynchChange(\app\modules\contract\models\PreContractDocument::className());
//                Yii::$app->replication->batchSynchChange(WorkflowContract::className());
            }


            if (
                    ($session['FromActionIDRef'] == 519 && $session['ActionIDRef'] == 523) || ($session['FromActionIDRef'] == 532 && $session['ActionIDRef'] == 536) || ($session['FromActionIDRef'] == 607 && $session['ActionIDRef'] == 611) || ($session['FromActionIDRef'] == 622 && $session['ActionIDRef'] == 626) || ($session['FromActionIDRef'] == 665 && $session['ActionIDRef'] == 669) || ($session['FromActionIDRef'] == 680 && $session['ActionIDRef'] == 684)
            ) {
                Yii::$app->replication->batchSynchChange(\app\modules\contract\models\ContractPms::className());
            }

            if (($session['FromActionIDRef'] == 528 && $session['ActionIDRef'] == 531) || ($session['FromActionIDRef'] == 618 && $session['ActionIDRef'] == 621) || ($session['FromActionIDRef'] == 676 && $session['ActionIDRef'] == 679)) {
                Yii::$app->replication->batchSynchChange(\app\modules\contract\models\ContractRevision::className());
                Yii::$app->replication->batchSynchChange(Contract::className());
            }
            Yii::$app->session->remove('EntityID-' . $peid);
            return $result;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }
    }

    public static function find() {
        return new ProcessEntityQuery(get_called_class());
    }

    public function getProcess() {
        return $this->hasOne(\app\modules\workflow\models\Process::className(), ['ProcessID' => 'ProcessIDRef']);
    }

    public function getInvoice() {
        return $this->hasOne(\app\modules\invoice\models\InvoiceEdit::className(), ['InvoiceID' => 'InvoiceIDRef']);
    }

    public function getContract() {
        return $this->hasOne(\app\modules\workflowContract\models\WorkflowContract::className(), ['WorkflowContractID' => 'WorkflowContractIDRef']);
    }

    public function getCashrequest() {
        return $this->hasOne(\app\modules\cashRequest\models\CashRequest::className(), ['CashRequestID' => 'CashRequestIDRef']);
    }

    public function getCapital() {
        return $this->hasOne(\app\modules\capital\models\CapitalBalance::className(), ['capitalBalanceId' => 'capitalBalanceIdRef']);
    }

    public function getPermission() {
        return $this->hasOne(\app\modules\permission\models\Permission::className(), ['id' => 'permissionId']);
    }

    public function getSourcing() {
        return $this->hasOne(\app\modules\OBS\models\Person::className(), ['PersonID' => 'PersonID']);
    }

    public function getActualProgress() {
        return $this->hasOne(\app\modules\budget\models\ActualProgress::className(), ['id' => 'actualProgressAnnualBudgetIdRef']);
    }

    public function getWarranty() {
        return $this->hasOne(\app\modules\warranty\models\Warranty::className(), ['warrantyId' => 'warrantyIdRef']);
    }

    public function getBudget() {
        return $this->hasOne(\app\modules\budget\models\Budget::className(), ['BudgetID' => 'BudgetIDRef']);
    }

    public function getDebitmemo() {
        return $this->hasOne(\app\modules\debitmemo\models\Debitmemo::className(), ['id' => 'debitmemoIdRef']);
    }

    public function getClaim() {
        return $this->hasOne(\app\modules\claim\models\Claim::className(), ['id' => 'claimIdRef']);
    }

    public function getTender() {
        return $this->hasOne(\app\modules\tender\models\Tender::className(), ['TenderID' => 'TenderIDRef']);
    }

    public function getAutomation() {
        return $this->hasOne(\app\modules\automation\models\Automation::className(), ['AutomationID' => 'AutomationIDRef']);
    }

    public function getNetwork() {
        return $this->hasOne(\app\modules\automation\models\Network::className(), ['NetworkID' => 'NetworkIDRef']);
    }

    public function getEmployeeSettlement() {
        return $this->hasOne(\app\modules\automation\models\EmployeeSettlement::className(), ['EmployeeSettlementID' => 'ObjectIDRef']);
    }

    public function getEmployeeMembership() {
        return $this->hasOne(\app\modules\automation\models\EmployeeMembership::className(), ['EmployeeMembershipID' => 'ObjectIDRef']);
    }

    public function getForeignMission() {
        return $this->hasOne(\app\modules\automation\models\ForeignMission::className(), ['MissionID' => 'ObjectIDRef']);
    }

    public function getStampDocument() {

        return $this->hasOne(\app\modules\document\models\StampDocument::className(), ['StampId' => 'ObjectIDRef']);
    }

    public function getContractClosingDocument() {
        return $this->hasOne(\app\modules\contract\models\ContractDocument::className(), ['ContractDocumentID' => 'ContractClosingDocumentIDRef']);
    }

    public function getLastUser() {
        return $this->hasOne(\app\modules\account\models\User::className(), ['UserID' => 'LastUserIDRef']);
    }

    public function getLastAction() {
        return $this->hasOne(\app\modules\workflow\models\Action::className(), ['ActionID' => 'LastActionIDRef']);
    }

    public function getLastActionEntity() {
        $actionEntityModel = ActionEntity::find()
                ->where(['ProcessEntityIDRef' => $this->ProcessEntityID, 'ActionIDRef' => $this->LastActionIDRef])
                ->orderBy('ID desc')
                ->one();

        return $actionEntityModel;
    }

    public function getActionEntities() {
        return $this->hasMany(ActionEntity::className(), ['ProcessEntityIDRef' => 'ProcessEntityID']);
    }

    public function getFirstActionEntity() {
        return ActionEntity::find()
                        ->where(['ProcessEntityIDRef' => $this->ProcessEntityID])
                        ->orderBy(['ID' => SORT_ASC])
                        ->one();
    }

}
