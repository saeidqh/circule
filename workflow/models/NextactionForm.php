<?php
namespace app\modules\workflow\models;

use Yii;
use app\modules\workflow\models\Process;
use app\modules\workflow\models\ActionEntity;
/**
 * Description of NextactionForm
 *
 * @author joudi_a
 */
class NextactionForm extends \yii\base\Model{
    
    /**
     *
     * @var integer
     */
    public $actionId;
    /**
     *
     * @var string 
     */
    public $actionDateHijri;
    
    /**
     *
     * @var string 
     */
    public $actionDate;
    
    /**
     *
     * @var string
     */
    public $processEntityIDs;
    
    /**
     *
     * @var integer 
     */
    public $fromActionId;
    
    /**
     *
     * @var string
     */
    public $actionAutoMessage;
    
    /**
     *
     * @var string 
     */
    public $actionMessage;


    /**
     *
     * @var string
     */
    public $letterNumber;

    public function rules() {
        return [
            [['fromActionId', 'actionId', 'actionDateHijri', 'actionDate', 'processEntityIDs', 'actionAutoMessage','actionMessage', 'etterNumber'], 'safe']
        ];
    }


    public function setStoreValues() {
        $array = [];
        foreach ($this->attributes() as $attribute) {
            $array[$attribute] = $this->$attribute;
        }
        if (!empty($this->ProcessEntityIDRef) && !empty($this->fromActionId)) {
            Yii::$app->session->set($uniqe = 'EntityID-' . $this->ProcessEntityID, $array);
            $array = [
                'ipAddress' => Yii::$app->request->UserIP,
            ];
            $data = array_merge($array, Yii::$app->session->get($uniqe));
            Yii::$app->session->set($uniqe, $data);
        } else {
            return false;
        }
    }

    /**
     * remove attributes value off model  
     */
    public function clearStoreValues() {
        if (!empty($this->ProcessEntityIDRef)) {
            Yii::$app->session->remove('EntityID-' . $this->ProcessEntityIDRef);
        }
    }

    /**
     * if empty key session variable $key , that time set session width key in $key
     * @param type $key
     * @param type $value
     * @return type
     */
    public function fetchStoreValue($key, $defaultValue = null) {
        if (Yii::$app->request->isPost && !empty($this->$key)) {
            return $this->$key;
        }
        $session = Yii::$app->session['EntityID-' . $this->processEntityId];
        if (isset($session[$key]) && !empty($defaultValue = $session[$key])) {
            return $defaultValue;
        }

        if (!empty($defaultValue)) {
            return $defaultValue;
        } else {
            $this->$key;
        }
    }

    /**
     * set field Entitys(invoice,contract,budget,capital,warranty) width $processEntityModel
     * @param type $peModel
     */
    public function setFeildIdStoreValue($peModel) {
        $uniqe = 'EntityID-' . $peModel->ProcessEntityID;

        if (isset($peModel->InvoiceIDRef) && !empty($peModel->InvoiceIDRef)) {
            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'InvoiecId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['InvoiecId' => $peModel->InvoiceIDRef]);
        } elseif (isset($peModel->CashRequestIDRef) && !empty($peModel->CashRequestIDRef)) {

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'CashRequestId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['CashRequestId' => $peModel->CashRequestIDRef]);
        } elseif (isset($peModel->WorkflowContractIDRef) && !empty($peModel->WorkflowContractIDRef)) {

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'ContractId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['ContractId' => $peModel->WorkflowContractIDRef]);
        } elseif (isset($peModel->warrantyIdRef) && !empty($peModel->warrantyIdRef)) {

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'WarrantyId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['WarrantyId' => $peModel->warrantyIdRef]);
        }
    }
    
    
    public function goNextAction($peid) {
        $ip=Yii::$app->request->userIP;
        $result = [
            'result' => true,
            'message' => [],
        ];
        $res = ProcessEntity::canNextAction($peid, $this->fromActionId, $this->actionId);
        /*if(!$res){
            return [
                'result' => false,
                'message' => 'can not next action!!',
            ];
        }*/
        if(!$res['result']){
            return $res;
        }
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid, 'LastActionIDRef' => $this->fromActionId])
                ->one();
        
        if (!isset($processEntityModel->ProcessEntityID)) {
            $result['result'] = false;
            $result['message'] = 'this action has passed by another user';
            return $result;
            throw new \yii\web\HttpException(403, 'Not found Process Entity or Action Entity');
        }
        if ($processEntityModel->LastActionIDRef != $this->fromActionId) {
            $result['result'] = false;
            $result['message'] = 'this action has passed by another user';
            return $result;
        }
        
        //find Action Order
        //for set @subProcessId1,@subProcessId2,@subProcessId3
        $ActionOrderModel = ActionOrder::find()
                        ->where([
                            'ActionIDRef' => $this->fromActionId, //current action
                            'NextActionIDRef' => $this->actionId//next action
                        ])->one();

        if (!isset($ActionOrderModel->ID)) {
            throw new \yii\web\HttpException(403, 'Not Found Action Order');
        }
        //begin Transaction
        $transaction = Yii::$app->dbTehran->beginTransaction();
        try {


            //find Action Entity and editing
            $actionEntityModel = ActionEntity::find()
                    ->where(['ProcessEntityIDRef' => $peid, 'ActionIDRef' =>$this->fromActionId])
                    ->orderBy('ID desc')
                    ->one();
            if (!isset($actionEntityModel->ID)) {
                throw new \yii\web\HttpException(501, 'Not found Action Entity');
            }

            $actionEntityModel->toActionIdRef = $this->actionId; //next action
            $actionEntityModel->toActionDate = $this->actionDate;
            $actionEntityModel->toActionDateHijri = $this->actionDate;
            $actionEntityModel->hasChange = 1;
            $resEntityModel = $actionEntityModel->save(); //update Action Entity
            if (!$resEntityModel) {
                $transaction->rollBack();
                $result['result'] = false;
                $result['message'] = 'Error on updating current action entity.Please try letter!';
                return $result;
            }
            //new Action Entity width fill session data
            $newActionEntityModel = new ActionEntity();
            $newActionEntityModel->ProcessEntityIDRef = $processEntityModel->ProcessEntityID;
            $newActionEntityModel->FromActionIDRef =  $this->fromActionId;
            $newActionEntityModel->ActionIDRef = $this->actionId;
            $newActionEntityModel->ActionDate = $this->actionDate;
            $newActionEntityModel->ActionDateHijri = $this->actionDateHijri;
            $newActionEntityModel->UserIDRef = Yii::$app->user->id;
            $newActionEntityModel->OBSCategory3IDRef = Yii::$app->session->get('ObsPosition');
            $newActionEntityModel->ipAddress = $ip;
//            $newActionEntityModel->ComputerName = php_uname('n');
            $newActionEntityModel->ComputerName = \app\components\Utility::findComputerNameByIp($ip);
            $newActionEntityModel->ActionMessage = $this->actionMessage;
            $newActionEntityModel->ActionAutoMessage = $this->actionAutoMessage;
            $newActionEntityModel->LetterNumber = $this->letterNumber;
            $newActionEntityModel->hasChange = 1;
            $resNewEntityModel = $newActionEntityModel->save(); //save new Action Entity
            if (!$resNewEntityModel) {
                $transaction->rollBack();
                $result['result'] = false;
                $result['message'] = 'Error on saving new action entity.Please try letter!';
                return $result;
            }

            //CalculateDuration
            Yii::$app->dbTehran->createCommand()->update(ActionEntity::tableName(), [
                        'durationInMinute' => ActionEntity::duration(0),
                            ], 'ProcessEntityIDRef=' . $processEntityModel->ProcessEntityID)
                    ->execute();

            //update current Process Entity model
            $processEntityModel->LastUserIDRef = Yii::$app->user->id;
            $processEntityModel->LastActionIDRef = $this->actionId;
            $processEntityModel->LastActionDateHijri = $this->actionDateHijri;
            $processEntityModel->LastActionMessage =  $this->actionMessage;
            $processEntityModel->LastActionAutoMessage = $this->actionAutoMessage;
            $processEntityModel->hasChange = 1;
            $resProcessEntity = $processEntityModel->save(); //update Process Entity
            if (!$resProcessEntity) {
                $transaction->rollBack();
                $result['result'] = false;
                $result['message'] = 'Error on updating process entity.Please try later!';
                return $result;
            }

            //if the action end of the processing end
            $toactionEntityModel = Action::find()
                    ->where(['ActionID'=>$this->actionId])
                    ->one();
            
            if ($toactionEntityModel->isEndAction > 0) {
                $processEntityModel->Status = 0;
                $processEntityModel->hasChange = 1;
                if (!$processEntityModel->save()) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message']= 'Error on updating process entity.Please try later!';
                    return $result;
                }
            }

            //find process
            $processModel = Process::find()
                    ->where(['ProcessID'=>$processEntityModel->ProcessIDRef])
                    ->one();
            if (!isset($processModel->ProcessID)) {
                throw new \yii\web\HttpException(403, 'Not found Process');
            }
            if ($processModel->ProccessTypeIDRef == 7) { // WorkFLow Debit Memo
                $debitModel = \app\modules\debitmemo\models\Debitmemo::find()
                        ->where(['id'=>$processEntityModel->debitmemoIdRef])
                        ->one();
                if (!isset($debitModel)) {
                    throw new \yii\web\HttpException(403, "Not found DebitMemo!!");
                }

                if($debitModel->contract->ContractType!=13){
                    if (floatval($debitModel->cumulativeApproved1(6)) > floatval($debitModel->contract->lastRider->Price1)) {
                        $result['result'] = false;
                        $result['message'] = 'Debitmemo Cumulative more than Contract Price1';
                        return $result;
                    }

                    if (floatval($debitModel->cumulativeApproved2(6)) > floatval($debitModel->contract->lastRider->Price2)) {
                        $result['result'] = false;
                        $result['message'] = 'Debitmemo Cumulative more than Contract Price2';
                        return $result;
                    }
                    
                }
            }

            if ($processModel->ProccessTypeIDRef == 4) { // WorkFLow Budget
                //اگر ارسال شده به اکشن آخری ان هنگام ذخیره ی درصد های برنامه انجام گیرد
                $budgetNodeTable = \app\modules\budget\models\BudgetNode::tableName();
                $budgetNodeAmountTable = \app\modules\budget\models\BudgetNodeAmount::tableName();
                if ($this->actionId == 853 || $this->actionId == 653) {
                    // اگر از نوع برنامه بود
                    // و اگر مبلغ بودجه صفر نبود باید بر اساس مبالغ وارد شده درج گردد
                    $budgetNode=  \app\modules\budget\models\BudgetNode::find()->where(["$budgetNodeTable.BudgetIDRef" => $processEntityModel->BudgetIDRef])->all();

                    if (isset($processEntityModel->budget->PlanIDRef) and $processEntityModel->budget->plan->PlanAmount>0) {
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
                } else if ($this->actionId == 479) {
                     $budget = \app\modules\budget\models\Budget::find()->where(["BudgetID" => $processEntityModel->BudgetIDRef])->one();
                     $budget->DepartementObsIDRef = Yii::$app->session->get('ObsPosition');
                     $budget->save();
                }
            }

            if ($processModel->ProccessTypeIDRef == 8) { // اگر فرایند از نوع کلیم هست
                $claimModel = \app\modules\claim\models\Claim::find()
                        ->where(['id'=>$processEntityModel->claimIdRef])
                        ->one();
                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $this->actionId, 'paramTitle' => 'AmountStatus'])
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
                        ->where(['ActionIDRef' => $this->actionId, 'paramTitle' => 'permission_amountStatus'])
                        ->one();
                $permissionModel->amountStatus = $actioParamModel->value1;
            }
            if ($processModel->ProccessTypeIDRef == 11) { // sourcing
                $sourcingModel = \app\modules\OBS\models\Person::find()
                        ->where(['PersonID' => $processEntityModel->PersonID])
                        ->one();
                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $this->actionId, 'paramTitle' => 'verify', 'value1' => 1])
                        ->one();

                if (isset($actioParamModel)) {
                    $sourcingModel->setScenario('changePersonVerified');
                    $sourcingModel->verified = $actioParamModel->value1;
                    $sourcingModel->save();
                }
            }
            if ($processModel->ProccessTypeIDRef == 1) {//اگر فرایند از نوع صورت وضعیت بود

                $invoiceModel = \app\modules\invoice\models\InvoiceEdit::find()
                        ->where(['InvoiceID'=>$processEntityModel->InvoiceIDRef])
                        ->one();

                $actioParamModel = ActionParam::find()
                        ->where(['ActionIDRef' => $this->actionId, 'paramTitle' => 'AmountStatus'])
                        ->one();
                
                $invoiceModel->AmountStatus = $actioParamModel->value1;
                $invoiceModel->hasChange = 1;
                $resInvoice = $invoiceModel->save(false);
                if (!$resInvoice) {
                    throw new \yii\web\HttpException(501, 'Not saved Invoice');
                }
                if ($invoiceModel->InvoiceType == 3) {
                    if ($actioParamModel->value1 == 5) {
                        // Convert ContractStatus to "Completed"
                        $contractModel = $invoiceModel->contract;
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
                    }

                    // Convert ContractStatus to "In Progress"
                    if ($actioParamModel->value1 == 1) {
                        // For Completed Contract
                        $contractModel = $invoiceModel->contract;
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

                    if (in_array($this->fromActionId, $commissionArray)) {
                        $contractModel = $invoiceModel->contract;
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
                    $result['message'] = 'Error on creating first sub process.Please try letter!';
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
                $newSubActionEntityModel->ComputerName = \app\components\Utility::findComputerNameByIp($ip);
                $newSubActionEntityModel->ipAddress = $ip;
                $newSubActionEntityModel->hasChange = 1;
                $resNewsubAction = $newSubActionEntityModel->save();
                if (!$resNewsubAction) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message'] = 'Error on creating first sub process action entity.Please try letter!';
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
                    $result['message'] = 'Error on creating second sub process.Please try letter!';
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
                $newSubActionEntityModel_2->ComputerName = \app\components\Utility::findComputerNameByIp($ip);
                $newSubActionEntityModel_2->ipAddress = $ip;
                $newSubActionEntityModel_2->hasChange = 1;
                $resNewsubAction_2 = $newSubActionEntityModel_2->save();
                if (!$resNewsubAction_2) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message'] = 'Error on creating second sub process action entity.Please try letter!';
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
                    $result['message'] = 'Error on creating third sub process.Please try letter!';
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
//                $newSubActionEntityModel_3->ComputerName = php_uname('n');
                $newSubActionEntityModel_3->ipAddress = $ip;
                $newSubActionEntityModel_3->hasChange = 1;
                $resNewsubAction_3 = $newSubActionEntityModel_3->save();
                if (!$resNewsubAction_3) {
                    $transaction->rollBack();
                    $result['result'] = false;
                    $result['message'] = 'Error on creating third sub process action entity.Please try letter!';
                    return $result;
                }
            }

            if (($this->fromActionId == 488 && $this->actionId == 493) || ($this->fromActionId == 599 && $this->actionId == 603) || ($this->fromActionId == 657 && $this->actionId == 661)) {
                if (Yii::$app->params['dmbs'] == 'mysql') {
                    $res = false;
                    $settingModel = \app\modules\management\models\Setting::findOne(['`key`' => 'UpdateStaticContractList']);
                    if (isset($settingModel)) {
                        $settingModel->value1 = 'waiting';
                        $res = $settingModel->save();
                    }
                    if (Yii::$app->params['autoReplicate'] == true) {
                        $settingModelLocal = \app\modules\management\models\SettingLocal::find()
                                ->where(['`key`' => 'UpdateStaticContractList'])
                                ->one();
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
                    $result['message'] = 'Error on updating SoftwareInfo table.Please try letter!';
                    return $result;
                }
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToList($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'] = $value;
                    }

                    return $result;
                }
            }
            // ?? [model].[workflowContractAddToListFinancialCode]
            if (($this->fromActionId == 492 && $this->actionId == 518) || ($this->fromActionId == 605 && $this->actionId == 606) || ($this->fromActionId == 663 && $this->actionId == 664)) {
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToListFinancialCode($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'] = $value;
                    }

                    return $result;
                }
            }

            //??[model].[workflowContractAddToListPmsPlan]
            if (
                    ($this->fromActionId == 519 && $this->actionId == 523) || ($this->fromActionId == 532 && $this->actionId == 536) || ($this->fromActionId == 607 && $this->actionId == 611) || ($this->fromActionId == 622 && $this->actionId == 626) || ($this->fromActionId == 665 && $this->actionId == 669) || ($this->fromActionId == 680 && $this->actionId == 684)
            ) {
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToListPmsPlan($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'] = $value;
                    }

                    return $result;
                }
            }

            // ?? [model].[workflowContractAddToListRevision]
            if (($this->fromActionId == 528 && $this->actionId == 531) || ($this->fromActionId == 618 && $this->actionId == 621) || ($this->fromActionId == 676 && $this->actionId == 679)) {
                $resultContract = \app\modules\workflowContract\models\WorkflowContract::workflowContractAddToListRevision($processEntityModel->WorkflowContractIDRef);
                if ($resultContract['result'] == false) {
                    $transaction->rollBack();
                    $result['result'] = $resultContract['result'];
                    foreach ($resultContract['message'] as $message => $value) {
                        $result['message'] = $value;
                    }

                    return $result;
                }
            }

            $transaction->commit();
            return $result;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }
    }
    
    public function goMultiple() {
        $peids = explode(',', $this->processEntityIDs);

        if(count($peids) > 0){
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($peids as $peid) {

                $res = $this->goNextaction($peid);
                if(!$res['result']){
                    $transaction->rollBack();
                    return [
                        'result' => false,
                        'message' => $res['message']
                    ];
                }
            }
            $transaction->commit();
            return [
                'result' => true,
                'message' => 'Successfull Next Action!!'
            ];
        }

        return [
            'result' => false,
            'message' => 'You have to select a few items'
        ];
    }
}
