<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;
use app\components\Session;
use app\components\jDateTime;
use app\modules\workflow\models\Process;
use app\modules\workflow\models\Action;
use app\modules\workflow\models\ActionEntity;
use app\modules\workflow\models\ProcessEntity;
use app\modules\contract\models\ContractRevisionDocumentSearch;
use app\modules\contract\models\ContractRevisionSearch;
use app\modules\workflow\models\Letter;
use app\components\ActiveModel;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\modules\document\models\StampDocument;

class NextActionController extends Controller {

    public $defaultAction = "";
    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['next-action', 'previous-action', 'sign', 'all-next-action', 'all-sign', 'previous-action-confirm', 'check-next-action-actual-progress', 'check-next-action-budget', 'check-next-action-capital-balance', 'check-next-action-cash-request', 'check-next-action-claim', 'check-next-action-contract', 'check-next-action-debitmemo', 'check-next-action-invoice', 'check-next-action-permission', 'check-next-action-sourcing', 'check-next-action-warranty', 'check-next-action-actual-progress', 'check-next-action-stamp','check-next-action-refund-stamp'],
                'rules' => [
                    [
                        'actions' => ['next-action', 'sign'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            $peid = isset($exteraData['peid']) ? $exteraData['peid'] : Yii::$app->request->get('peid');
                            $currentUserObsId = Yii::$app->session->get('ObsPosition');
                            if ($currentUserObsId == 606) {
                                return true;
                            }
                            return \app\access\AccessControl::ProcessEntity([
                                'peid' => $peid,
                                'type' => 'ToDo'
                            ])->isAccess();
                        },
                    ],
                    [
                        'actions' => ['previous-action'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            return \app\access\AccessControl::isAccess(
                                    ['obs', 'check' => 'isCandoAdmin']
                            );
                            /* return \app\access\AccessControl::isAccess([
                              'and',
                              ['user', 'check' => 'isCandoManager'],
                              ['obs', 'check' => 'isCandoAdmin']
                              ]); */
                        },
                    ],
                    [
                        'actions' => ['previous-action-all'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            return \app\access\AccessControl::isAccess(
                                    ['obs', 'check' => 'isCandoAdmin']
                            );
                        },
                    ],
                    [
                        'actions' => ['all-next-action', 'all-sign', 'previous-action-confirm', 'check-next-action-actual-progress', 'check-next-action-budget', 'check-next-action-capital-balance', 'check-next-action-cash-request', 'check-next-action-claim', 'check-next-action-contract', 'check-next-action-debitmemo', 'check-next-action-invoice', 'check-next-action-permission', 'check-next-action-sourcing', 'check-next-action-warranty', 'check-next-action-actual-progress'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    /**
     * 
     * @param string $type
     * @param integer $peid
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionNextAction($peid, $clearValue = true) {
        if (!is_numeric($peid)) {
            throw new \yii\web\BadRequestHttpException();
        }
        //processEntity find model
        $processEntityModel = ActiveModel::find(ProcessEntity::className())
                ->where(['ProcessEntityID' => $peid])
                ->one();
        
        if (empty($processEntityModel)) {
            throw new \yii\web\BadRequestHttpException();
        }

        //jDateTime new object
        $jDate = new jDateTime(true, true, 'Asia/Tehran');

        //actionEntity new model
        $actionEntityModel = new ActionEntity();
        $actionEntityModel->setScenario('nextaction');
        $actionEntityModel->ActionDateHijri = $jDate->date("Y/m/d-Hi", false, false);
        $actionEntityModel->ActionDate = $jDate->date("Y-m-d", false, false, false);
        $actionEntityModel->ProcessEntityIDRef = $processEntityModel->ProcessEntityID;
        $actionEntityModel->FromActionIDRef = $processEntityModel->LastActionIDRef;
        $actionEntityModel->ActionAutoMessage = '';

        if ($processEntityModel->process->ProccessTypeIDRef == Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
            $actionTable = Action::tableName();
            $dataArrayNextActions = ProcessEntity::find()
                            ->employeeSettlement('ToDo')
                            ->addSelect([
                                "$actionTable.ActionID", "$actionTable.ActionTitle1"
                            ])
                            ->andWhere(['ProcessEntityID' => $peid])
                            ->asArray()->all();
            $actionEntityModel->ActionIDRef = $dataArrayNextActions[0]['ActionID'];
            $dropDownDataActions = \yii\helpers\ArrayHelper::map($dataArrayNextActions, 'ActionID', 'ActionTitle1');
        } else {
            //fetch data dropDown next step
            $dataArrayNextActions = ActiveModel::find(\app\modules\workflow\models\ActionOrder::className())
                    ->nextAction($peid)
                    ->asArray()
                    ->all();
//        echo $dataArrayNextActions->createCommand()->rawSql;   
//        die();
//        $dataArrayNextActions = $dataArrayNextActions->asArray()
//                ->all();
            foreach ($dataArrayNextActions as $dt) {
                if ($dt['isDefaultNextAction'] == 1) {
                    $actionEntityModel->ActionIDRef = $dt['NextActionIDRef'];
                    $actionEntityModel->ActionAutoMessage = $dt['AutoMessage1'];
                }
            }

            $isDefaultActions = ArrayHelper::getColumn($dataArrayNextActions, 'isDefaultNextAction');
            if (!in_array(1, $isDefaultActions)) {
                $actionEntityModel->ActionIDRef = $dataArrayNextActions[0]['NextActionIDRef'];
            }

            $dropDownDataActions = \yii\helpers\ArrayHelper::map($dataArrayNextActions, 'nextAction.ActionID', 'nextAction.ActionTitle1');
//        $dataAutoMessagefield = \yii\helpers\ArrayHelper::map($dataArrayNextActions, 'nextAction.ActionID', 'AutoMessage1');
//        $dataAutoMessagefield = array_merge($dataAutoMessagefield, \yii\helpers\ArrayHelper::map($dataArrayNextActions, 'nextAction.ActionID', 'AutoMessage2'));
        }

        //load and validate
        if ($actionEntityModel->load(Yii::$app->request->post()) && $actionEntityModel->validate() && Yii::$app->request->post('dropDown') == false) {

            $result = ProcessEntity::canNextAction($peid, $actionEntityModel->FromActionIDRef, $actionEntityModel->ActionIDRef);
            if ($result['result']) {

                $actionOrderModel = ActiveModel::find(\app\modules\workflow\models\ActionOrder::className())
                        ->where(['ActionIDRef' => $actionEntityModel->FromActionIDRef, 'NextActionIDRef' => $actionEntityModel->ActionIDRef])
                        ->asArray()
                        ->one();

//                if (empty($actionOrderModel->AutoMessage1) || empty($actionOrderModel->AutoMessage2) || empty($actionOrderModel->AutoMessage3)) {
//                    $actionEntityModel->ActionAutoMessage = '';
//                }

                $actionEntityModel->setStoreValues();
                $actionEntityModel->setFeildIdStoreValue($processEntityModel);
                if (!empty($processEntityModel->WorkflowContractIDRef)) {
                    return $this->actionCheckNextActionContract($peid);
                } else if (!empty($processEntityModel->StampIDRef)) {
                     return $this->actionCheckNextActionStamp($peid);
                } else if (!empty($processEntityModel->InvoiceIDRef)) {
                    return $this->actionCheckNextActionInvoice($peid);
                } else if (!empty($processEntityModel->CashRequestIDRef)) {
                    return $this->actionCheckNextActionCashRequest($peid);
                } else if (!empty($processEntityModel->warrantyIdRef)) {
                    return $this->actionCheckNextActionWarranty($peid);
                } else if (!empty($processEntityModel->BudgetIDRef)) {
                    return $this->actionCheckNextActionBudget($peid);
                } else if (!empty($processEntityModel->capitalBalanceIdRef)) {
                    return $this->actionCheckNextActionCapitalBalance($peid);
                } else if (!empty($processEntityModel->debitmemoIdRef)) {
                    return $this->actionCheckNextActionDebitmemo($peid);
                } else if (!empty($processEntityModel->claimIdRef)) {
                    return $this->actionCheckNextActionClaim($peid);
                } else if (!empty($processEntityModel->actualProgressAnnualBudgetIdRef)) {
                    return $this->actionCheckNextActionActualProgress($peid);
                } else if (!empty($processEntityModel->permissionId)) {
                    return $this->actionCheckNextActionPermission($peid);
                } else if (!empty($processEntityModel->PersonID)) {
                    return $this->actionCheckNextActionSourcing($peid);
                } else if (!empty($processEntityModel->TenderIDRef)) {
                    return $this->actionCheckNextActionTender($peid);
                } else if (!empty($processEntityModel->ContractClosingDocumentIDRef)) {
                    return $this->actionCheckNextActionContractClosingDocument($peid);
                } else if (!empty($processEntityModel->AutomationIDRef)) {
                    return $this->actionCheckNextActionAutomation($peid);
                } else if (!empty($processEntityModel->NetworkIDRef)) {
                    return $this->actionCheckNextActionNetwork($peid);
                } else if ($processEntityModel->process->ProccessTypeIDRef == Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
                    return $this->actionCheckNextActionEmployeeSettlement($peid);
                } else if ($processEntityModel->process->ProccessTypeIDRef == Process::EMPLOYEE_MEMBERSHIP_PROCESS_TYPE) {
                    return $this->actionCheckNextActionEmployeeMembership($peid);
                } else if ($processEntityModel->process->ProccessTypeIDRef == Process::FOREIGN_MISSION_PROCESS_TYPE) {
                    return $this->actionCheckNextActionForeignMission($peid);
                } else if ($processEntityModel->process->ProccessTypeIDRef == Process::STAMP_DOCUMENT_PROCESS_TYPE) {
                    return $this->actionCheckNextActionStamp($peid);
                } else if ($processEntityModel->process->ProccessTypeIDRef == Process::REFUND_STAMP_PROCESS_TYPE) {
                    return $this->actionCheckNextActionRefundStamp($peid);
                }
            } else {
                foreach ($result['message'] as $message) {
                    Yii::$app->session->addFlash('danger', $message);
                }
            }
        } else {

            //چک کردن برای رفتن به مرحله بعد 
            $result = ProcessEntity::canNextAction($peid, $actionEntityModel->FromActionIDRef, $actionEntityModel->ActionIDRef);
            if ($result['result'] == false) {
                foreach ($result['message'] as $message) {
                    Yii::$app->session->addFlash('danger', $message);
                }
            }
        }


        if (($actionEntityModel->FromActionIDRef == 443 && $actionEntityModel->ActionIDRef == 435) || ($actionEntityModel->FromActionIDRef == 441 && $actionEntityModel->ActionIDRef == 410) || ($actionEntityModel->FromActionIDRef == 541 && $actionEntityModel->ActionIDRef == 542)) {
            $actionEntityModel->ActionMessage = ActionEntity::find()->where(['ActionIDRef' => $actionEntityModel->FromActionIDRef, 'ProcessEntityIDRef' => $processEntityModel->ProcessEntityID])->orderBy('ID DESC')->one()->ActionMessage;
        }

        if (($actionEntityModel->FromActionIDRef == 431 && $actionEntityModel->ActionIDRef == 432) || ($actionEntityModel->FromActionIDRef == 431 && $actionEntityModel->ActionIDRef == 854) || ($actionEntityModel->FromActionIDRef == 432 && $actionEntityModel->ActionIDRef == 434)) {
            $cashrequestAmountModel = ActiveModel::find(\app\modules\cashRequest\models\CashRequestAmount::className())
                    ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef])
                    ->andWhere(['CashRequestTypeIDRef' => 4]) // در صورت داشتن امانت از نوع کارپردازی پیام به صورت پیش فرض انتخاب می شود
                    ->exists();
            if ($cashrequestAmountModel) {
                $actionEntityModel->ActionMessage = "مبلغ فوق بصورت کارپردازی انجام و توسط این رده حداکثر تا 15 روز کاری از تاریخ پرداخت، نسبت به تسویه حساب آن اقدام خواهد شد.";
            }
        }

        //clear values model off session
        if ($clearValue) {
            $actionEntityModel->clearStoreValues();
        }

        //create dropdown auto message
        $fromToActionMessageArray = ActiveModel::find(\app\modules\workflow\models\ActionOrder::className())
                ->where(['ActionIDRef' => $actionEntityModel->fetchStoreValue('FromActionIDRef', $actionEntityModel->FromActionIDRef), 'NextActionIDRef' => $actionEntityModel->fetchStoreValue('ActionIDRef', $actionEntityModel->ActionIDRef)])
                ->asArray()
                ->one();
//        echo $fromToActionMessageArray->createCommand()->rawSql;
//        $fromToActionMessageArray = $fromToActionMessageArray->asArray()
//                ->one();
        $dropDownDataAutoMessage = [];
        if (!empty($fromToActionMessageArray['AutoMessage1'])) {
            $dropDownDataAutoMessage[$fromToActionMessageArray['AutoMessage1']] = $fromToActionMessageArray['AutoMessage1'];
        }
        if (!empty($fromToActionMessageArray['AutoMessage2'])) {
            $dropDownDataAutoMessage[$fromToActionMessageArray['AutoMessage2']] = $fromToActionMessageArray['AutoMessage2'];
        }
        if (!empty($fromToActionMessageArray['AutoMessage3'])) {
            $dropDownDataAutoMessage[$fromToActionMessageArray['AutoMessage3']] = $fromToActionMessageArray['AutoMessage3'];
        }


        //fetch data current action of Action Model
        $currentActionModel = ActiveModel::find('\app\modules\workflow\models\Action')
                ->where(['ActionID' => $processEntityModel->LastActionIDRef])
                ->one();
        //در آوردن لیست نامه های قابل انتخاب
        $actionEntityModel->ActionIDRef = $actionEntityModel->fetchStoreValue('ActionIDRef', $actionEntityModel->ActionIDRef);
        if (!empty($actionEntityModel->ActionIDRef)) {
            $letterTitles = Letter::letterItemTitle($processEntityModel->LastActionIDRef, $actionEntityModel->ActionIDRef);
            $letterTitlesMap = \yii\helpers\ArrayHelper::map($letterTitles, 'letterIdRef1', 'letterTitle');
        }

        return $this->renderAjax('nextActionForm', [
                    'actionEntityModel' => $actionEntityModel,
                    'processEntityModel' => $processEntityModel,
                    'currentActionModel' => $currentActionModel,
                    'dropDownDataActions' => $dropDownDataActions,
                    'dropDownDataAutoMessage' => $dropDownDataAutoMessage,
                    'letterTitlesMap' => empty($letterTitlesMap) ? [] : $letterTitlesMap,
//                    'dataAutoMessagefield' => $dataAutoMessagefield,
        ]);
    }

    public function actionPreviousActionConfirm($peid) {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            throw new \yii\web\BadRequestHttpException();
        }
        $peid = Html::encode($peid);
        $processEntityModel = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!isset($processEntityModel)) {
            throw new \yii\web\HttpException("CashRequest Not found", 404);
        }
        return $this->renderAjax('_previousActionConfirm', [
                    'processEntityModel' => $processEntityModel,
        ]);
    }

    public function actionPreviousAction($peid) {
        if (!Yii::$app->request->isAjax && !Yii::$app->request->isPjax) {
            throw new \yii\web\BadRequestHttpException();
        }
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $peid = Html::encode($peid);

        $processEntityModel = \app\modules\workflow\models\ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        if (!$processEntityModel) {
            return [
                'result' => 0,
                'message' => 'Invalid Arguments',
            ];
        }

        if ($processEntityModel->lastAction->isStartAction > 0) {
            return [
                'result' => 0,
                'message' => 'This record is in first step and you can not send it back',
            ];
        }

        $childCount = \app\modules\workflow\models\ProcessEntity::find()
                ->andWhere(["parentProcessEntityIDRef" => $peid])
                ->count();
        if ($childCount > 0) {
            return [
                'result' => 0,
                'message' => 'This record has subprocess and you can not send it back',
            ];
        }

        $lastActionEntityModel = \app\modules\workflow\models\ActionEntity::find()
                        ->andWhere(["ProcessEntityIDRef" => $peid])->orderBy(['ID' => SORT_DESC])->limit(1)->one();
        $previousActionEntityModel = \app\modules\workflow\models\ActionEntity::find()
                ->andWhere(['and', ['ProcessEntityIDRef' => $peid],
                    ['<>', 'ID', $lastActionEntityModel->ID]
                ])
                ->orderBy(['ID' => SORT_DESC])
                ->limit(1)
                ->one();

        $transaction = Yii::$app->dbTehran->beginTransaction();
        try {
            $processEntityModel->LastActionIDRef = $previousActionEntityModel->ActionIDRef;
            $processEntityModel->LastUserIDRef = $previousActionEntityModel->UserIDRef;
            $processEntityModel->LastActionDateHijri = $previousActionEntityModel->ActionDateHijri;
            $processEntityModel->LastActionMessage = $previousActionEntityModel->ActionMessage;
            $processEntityModel->LastActionAutoMessage = $previousActionEntityModel->ActionAutoMessage;
            if ($processEntityModel->lastAction->isEndAction > 0) {
                $processEntityModel->Status = 1;
            }
            $processEntityModel->hasChange = 1;
            $processEntityModel->save();
            if ($processEntityModel->InvoiceIDRef > 0) {
                $invoiceModel = \app\modules\invoice\models\InvoiceEdit::find()
                        ->where(['InvoiceID' => $processEntityModel->InvoiceIDRef])
                        ->one();
                $actionParamModel = \app\modules\workflow\models\ActionParam::find()
                        ->andWhere([
                            'and',
                            ['ActionIDRef' => $previousActionEntityModel->ActionIDRef],
                            ['paramTitle' => 'AmountStatus']
                        ])
                        ->one();
                $invoiceModel->AmountStatus = $actionParamModel->value1;
                $invoiceModel->hasChange = 1;
                if ($invoiceModel->validate()) {
                    $invoiceModel->save();
                } else {
                    $transaction->rollBack();
                    $errors = $invoiceModel->errors;
                    return [
                        'result' => 0,
                        'message' => reset($errors)[0],
                    ];
                }
            }
            $previousActionEntityModel->toActionIdRef = null;
            $previousActionEntityModel->toActionDate = null;
            $previousActionEntityModel->toActionDateHijri = null;
            $previousActionEntityModel->durationInMinute = null;
            $previousActionEntityModel->hasChange = 1;
            $previousActionEntityModel->save();
            \app\modules\workflow\models\ActionEntity::deleteAll(['ID' => $lastActionEntityModel->ID]);

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
            $subTypeDescription["RecordPrimaryKey"] = $previousActionEntityModel->ID;
            $activityManagement1->sendActitvityLog($actionType, $subType, $subTypeDescription);

            if (isset($invoiceModel)) {
                $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
                $activityManagement2 = new \app\components\ActivityManagement();
                $subType = "EditInvoice";
                $subTypeDescription["TableName"] = $invoiceTable;
                $subTypeDescription["RecordPrimaryKey"] = $invoiceModel->InvoiceID;
                $activityManagement2->sendActitvityLog($actionType, $subType, $subTypeDescription);
            }

            $transaction->commit();
            return [
                'result' => 1,
                'message' => 'Sending back has done successfully',
            ];
        } catch (Exception $exc) {
            $transaction->rollBack();
            return [
                'result' => 0,
                'message' => 'Error in saving data',
            ];
        }

        return [
            'result' => 0,
            'message' => 'Under Construction',
        ];
    }

    public function actionPreviousActionAll() {
        // برای برگرداندن ورکفلوها به صورت چندتایی دقت شود از کامنت خارج نشود
        $peids = array();

        var_dump($peids);
        die();
        $transaction = Yii::$app->dbTehran->beginTransaction();

        foreach ($peids as $peid) {
            $processEntityModel = \app\modules\workflow\models\ProcessEntity::find()
                    ->where(['ProcessEntityID' => $peid])
                    ->one();

            if (!$processEntityModel) {
                $transaction->rollBack();
                var_dump('Invalid Arguments');
                die();
            }

            if ($processEntityModel->lastAction->isStartAction > 0) {
                $transaction->rollBack();
                var_dump('This record is in first step and you can not send it back');
                die();
            }

            $childCount = \app\modules\workflow\models\ProcessEntity::find()
                    ->andWhere(["parentProcessEntityIDRef" => $peid])
                    ->count();
            if ($childCount > 0) {
                $transaction->rollBack();
                var_dump('This record has subprocess and you can not send it back');
                die();
            }

            $lastActionEntityModel = \app\modules\workflow\models\ActionEntity::find()
                            ->andWhere(["ProcessEntityIDRef" => $peid])->orderBy(['ID' => SORT_DESC])->limit(1)->one();
            $previousActionEntityModel = \app\modules\workflow\models\ActionEntity::find()
                    ->andWhere(['and', ['ProcessEntityIDRef' => $peid],
                        ['<>', 'ID', $lastActionEntityModel->ID]
                    ])
                    ->orderBy(['ID' => SORT_DESC])
                    ->limit(1)
                    ->one();

            try {
                $processEntityModel->LastActionIDRef = $previousActionEntityModel->ActionIDRef;
                $processEntityModel->LastUserIDRef = $previousActionEntityModel->UserIDRef;
                $processEntityModel->LastActionDateHijri = $previousActionEntityModel->ActionDateHijri;
                $processEntityModel->LastActionMessage = $previousActionEntityModel->ActionMessage;
                $processEntityModel->LastActionAutoMessage = $previousActionEntityModel->ActionAutoMessage;
                if ($processEntityModel->lastAction->isEndAction > 0) {
                    $processEntityModel->Status = 1;
                }
                $processEntityModel->hasChange = 1;
                $processEntityModel->save();
                /* if ($processEntityModel->InvoiceIDRef > 0) {
                  $invoiceModel = \app\modules\invoice\models\InvoiceEdit::find()
                  ->where(['InvoiceID' => $processEntityModel->InvoiceIDRef])
                  ->one();
                  $actionParamModel = \app\modules\workflow\models\ActionParam::find()
                  ->andWhere([
                  'and',
                  ['ActionIDRef' => $previousActionEntityModel->ActionIDRef],
                  ['paramTitle' => 'AmountStatus']
                  ])
                  ->one();
                  $invoiceModel->AmountStatus = $actionParamModel->value1;
                  $invoiceModel->hasChange = 1;
                  $invoiceModel->save();
                  } */
                $previousActionEntityModel->toActionIdRef = null;
                $previousActionEntityModel->toActionDate = null;
                $previousActionEntityModel->toActionDateHijri = null;
                $previousActionEntityModel->durationInMinute = null;
                $previousActionEntityModel->hasChange = 1;
                $previousActionEntityModel->save();

                \app\modules\workflow\models\ActionEntity::deleteAll(['ID' => $lastActionEntityModel->ID]);
            } catch (\Exception $exc) {
                $transaction->rollBack();
                var_dump("error");
                var_dump($exc);
                die();
            }
        }
        $transaction->commit();
        var_dump("ok");
        die();
    }

    public function actionCheckNextActionContract($peid) {
        $peid = Html::encode($peid);

        $model = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        $riderDocumentSearchModel = new ContractRevisionDocumentSearch();
        $riderDocumentDataProvider = $riderDocumentSearchModel->searchWorkflowContract(Yii::$app->request->queryParams, $model->WorkflowContractIDRef);

        //tab rider
        $workflowContractModel = \app\modules\workflowContract\models\WorkflowContract::find()
                ->where(['WorkflowContractID' => $model->WorkflowContractIDRef])
                ->one();

        $contractRevisionModel = new ContractRevisionSearch();
        $contractRevisionDataProvider = $contractRevisionModel->searchContract(Yii::$app->request->queryParams, $workflowContractModel->ContractIDRef);

        $processWorkflowContractModel = new \app\modules\workflowContract\models\WorkflowContractSearch();
        $workflowContractDataProvider = $processWorkflowContractModel->searchHistoryContract(Yii::$app->request->queryParams, $workflowContractModel->ContractIDRef);

        //tab Budget
        //-------------------------------------------------
        $contractNodeBudgetModel = new \app\modules\workflowContract\models\WorkflowContractBudgetSearch();
        $contractNodeBudgetModel->setScenario('search');
        $contractNodeBudgetProviders = $contractNodeBudgetModel->search(Yii::$app->request->queryParams, $model->WorkflowContractIDRef);
        $contractNodeBudgetProvider = $contractNodeBudgetProviders['dataProvider'];
        //contract and pms
        $contractPmsTable = \app\modules\contract\models\ContractPms::tableName();
        $contractPMSModel = \app\modules\contract\models\ContractPms::find()
                ->where(['WorkflowContractIDRef' => $model->contract->WorkflowContractID])
                ->andWhere($contractPmsTable . '.isDeleted is null or ' . $contractPmsTable . '.isDeleted != 1')
                ->one();

        $contractPlanTable = \app\modules\contract\models\ContractPlan::tableName();
        $contractPlanModel = \app\modules\contract\models\ContractPlan::find()
                ->where(['WorkflowContractIDRef' => $model->contract->WorkflowContractID])
                ->andWhere($contractPlanTable . '.isDeleted is null or ' . $contractPlanTable . '.isDeleted != 1')
                ->one();
        return $this->renderAjax('_contractDetail', [
                    'processEntityModel' => $model,
                    'riderDocumentSearchModel' => $riderDocumentSearchModel,
                    'riderDocumentDataProvider' => $riderDocumentDataProvider,
                    'contractRevisionModel' => $contractRevisionModel,
                    'contractRevisionDataProvider' => $contractRevisionDataProvider,
                    'processWorkflowContractModel' => $processWorkflowContractModel,
                    'workflowContractDataProvider' => $workflowContractDataProvider,
                    'contractNodeBudgetModel' => $contractNodeBudgetModel,
                    'contractNodeBudgetProvider' => $contractNodeBudgetProvider,
                    'contractPMSModel' => $contractPMSModel,
                    'contractPlanModel' => $contractPlanModel
        ]);
    }

    public function actionCheckNextActionInvoice($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->InvoiceIDRef > 0)) {
            return false;
        }

        $invoiceModel = \app\modules\invoice\models\InvoiceEdit::find()->where(['InvoiceID' => $processEntityModel->InvoiceIDRef])->one();
        $documentRequireModelDataProvider = \app\modules\invoice\models\InvoiceEdit::documentRequireModel($processEntityModel->InvoiceIDRef, $actionData['ActionIDRef']);

        $actionEntityFormData = [];
        $actionEntityFormData["ProcessID"] = $processEntityModel->ProcessIDRef;
        $actionEntityFormData["ProcessEntityID"] = $processEntityModel->ProcessEntityID;

        $actionEntityFormData["CurrentActionID"] = $processEntityModel->LastActionIDRef;
        $actionEntityFormData["CurrentActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['FromActionIDRef']])
                        ->one()
                ->ActionTitle1;
        $actionEntityFormData["NextActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['ActionIDRef']])
                        ->one()->ActionTitle1;
        $actionEntityFormData["Message"] = $actionData['ActionMessage'];        //get
        $actionEntityFormData["AutoMessage"] = $actionData['ActionAutoMessage'];    // 
        $actionEntityFormData["NextActionID"] = $actionData['ActionIDRef']; //get
        $actionEntityFormData['ActionDate'] = $actionData['ActionDate'];
        $actionEntityFormData['LetterNumber'] = $actionData['LetterNumber'];

        $amountDataProvider = \app\modules\contract\models\Contract::totalAmount($invoiceModel->ContractID, 'Approved Or Checking', $invoiceModel->CutOffDateHijri);
        return $this->renderAjax('_invoiceDetail', [
                    'invoiceModel' => $invoiceModel,
                    'actionEntityFormData' => $actionEntityFormData,
                    'documentRequireModelDataProvider' => $documentRequireModelDataProvider,
                    'amountDataProvider' => $amountDataProvider,
        ]);
    }

    public function actionCheckNextActionPermission($peid) {
        $peid = Html::encode($peid);
        $actionData = Yii::$app->session['EntityID-' . $peid];

        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->permissionId > 0)) {
            return false;
        }

        $permissionModel = \app\modules\permission\models\Permission::find()
                ->where(['id' => $processEntityModel->permissionId])
                ->one();
//        $documentRequireModelDataProvider = \app\modules\invoice\models\InvoiceEdit::documentRequireModel($processEntityModel->InvoiceIDRef, $actionData['ActionIDRef']);


        $actionEntityFormData = [];
        $actionEntityFormData["ProcessID"] = $processEntityModel->ProcessIDRef;
        $actionEntityFormData["ProcessEntityID"] = $processEntityModel->ProcessEntityID;

        $actionEntityFormData["CurrentActionID"] = $processEntityModel->LastActionIDRef;
        $actionEntityFormData["CurrentActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['FromActionIDRef']])->one()->ActionTitle1;
        $actionEntityFormData["NextActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['ActionIDRef']])->one()->ActionTitle1;
        $actionEntityFormData["Message"] = $actionData['ActionMessage'];        //get
        $actionEntityFormData["AutoMessage"] = $actionData['ActionAutoMessage'];    // 
        $actionEntityFormData["NextActionID"] = $actionData['ActionIDRef']; //get
        $actionEntityFormData['ActionDate'] = $actionData['ActionDate'];
        $actionEntityFormData['LetterNumber'] = $actionData['LetterNumber'];

//        $amountDataProvider = \app\modules\contract\models\Contract::totalAmount($invoiceModel->ContractID, 'Approved Or Checking', $invoiceModel->CutOffDateHijri);
        //------------- permission Amount List
        $permission_amount_model = new \app\modules\permission\models\PermissionAmountSearch();
        $permission_amount_model->setScenario('search');
        $permissionAmountDataProvider = $permission_amount_model->search(NULL, $processEntityModel->permissionId);

        return $this->renderAjax('_permissionDetail', [
                    'permissionModel' => $permissionModel,
                    'actionEntityFormData' => $actionEntityFormData,
//                    'documentRequireModelDataProvider' => $documentRequireModelDataProvider,
                    'amountDataProvider' => $permissionAmountDataProvider,
        ]);
    }

    public function actionCheckNextActionSourcing($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->PersonID > 0)) {
            return false;
        }

        $sourcingModel = \app\modules\OBS\models\Person::find()
                ->where(['PersonID' => $processEntityModel->PersonID])
                ->one();

        $actionEntityFormData = [];
        $actionEntityFormData["ProcessID"] = $processEntityModel->ProcessIDRef;
        $actionEntityFormData["ProcessEntityID"] = $processEntityModel->ProcessEntityID;

        $actionEntityFormData["CurrentActionID"] = $processEntityModel->LastActionIDRef;
        $actionEntityFormData["CurrentActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['FromActionIDRef']])->one()->ActionTitle1;
        $actionEntityFormData["NextActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['ActionIDRef']])->one()->ActionTitle1;
        $actionEntityFormData["Message"] = $actionData['ActionMessage'];        //get
        $actionEntityFormData["AutoMessage"] = $actionData['ActionAutoMessage'];    // 
        $actionEntityFormData["NextActionID"] = $actionData['ActionIDRef']; //get
        $actionEntityFormData['ActionDate'] = $actionData['ActionDate'];
        // $actionEntityFormData['LetterNumber'] = $actionData['LetterNumber'];

        return $this->renderAjax('_sourcingDetail', [
                    'sourcingModel' => $sourcingModel,
                    'actionEntityFormData' => $actionEntityFormData,
                    'processEntityModel' => $processEntityModel,
        ]);
    }

    public function actionCheckNextActionCashRequest($peid) {

        $model = new ProcessEntity();
        $model->setScenario('add');

        $processEntityModel = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')
                        ->where(['ProcessEntityID' => $peid])->one();
        $cashRequestAmountModels = \app\components\ActiveModel::find('\app\modules\cashRequest\models\CashRequestAmount')
                        ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef])->all();

        $crModel = \app\modules\cashRequest\models\CashRequest::cashRequestFormData($processEntityModel->CashRequestIDRef);
        //-------------< update session >-------------

        $session = Session::get('cash_request_all_data');
        $formData = $this->generateCreateFormData($crModel, $processEntityModel->ProcessIDRef);
        $session[$peid]['createForm'] = $formData;
        Session::set('cash_request_all_data', $session);
        $location = $formData['location']; // ore "site"
        $formLabel = $this->getCreateFormLabels();
        $this->layout = 'blank';

        //-------------< header images >-------------
        $pathImg = Yii::$app->getModule('cashRequest')->getBasePath() . '/bower/img/';
        $type = 'png';

        $file = file_get_contents($pathImg . 'cando-logo.png');
        $cando = "data:image/" . $type . ';base64,' . base64_encode($file);
        $companyId = $processEntityModel->cashrequest->obs->companyIdRef;
        $pic_file = \app\components\Templates::getTemplate("logo", $companyId . ".png");
        if ($pic_file != "") {
            $file = file_get_contents($pic_file);
            $psa = "data:image/" . $type . ';base64,' . base64_encode($file);
        } else {
            $psa = "";
        }

        //-------------< now date >-------------
        $jDate = new jDateTime(true, true, 'Asia/Tehran');
        $nowDateFa = $jDate->date("Y/m/d-H:i", false, false);

        $activityManagement = new \app\components\ActivityManagement();
        $description = "پرینت فرم گردش کار اعلام بدهی";
        $activityManagement->sendCreateFormActitvityLog($description);

        return $this->renderAjax('_cashRequestDetail', array(
                    'model' => $model,
                    'peid' => $peid,
                    'processEntityModel' => $processEntityModel,
                    'cashRequestAmountModels' => $cashRequestAmountModels,
                    'formLabel' => $formLabel,
                    'location' => $location,
                    'formData' => $formData,
                    'cando' => $cando,
                    'psa' => $psa,
                    'nowDateFa' => $nowDateFa,
        ));
    }

    private function generateCreateFormData($data, $pid) {

        return array(
            'sgn-1' => [// مدیر کنترل هزینه
                'obs' => (isset($data['CostControlOBSCategory3ID'])) ? $data['CostControlOBSCategory3ID'] : 0,
                'uid' => (isset($data['CostControlUserID'])) ? $data['CostControlUserID'] : 0,
                'UserName' => (isset($data['CostControlUserName'])) ? $data['CostControlUserName'] : 0,
                'ActionDate' => (isset($data['CostControlActionDate'])) ? $data['CostControlActionDate'] : 0,
                'ActionMessage' => (isset($data['CostControlActionMessage'])) ? $data['CostControlActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['CostControlActionAutoMessage'])) ? $data['CostControlActionAutoMessage'] : 0,
                'is-active' => ((isset($data['CostControlOBSCategory3ID']) && (isset($data['CostControlUserID']))) ?
                ($data['CostControlOBSCategory3ID'] . '' != '0' && $data['CostControlUserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-2' => [// رئیس کارگاه
                'obs' => (isset($data['SiteManagerOBSCategory3ID'])) ? $data['SiteManagerOBSCategory3ID'] : 0,
                'uid' => (isset($data['SiteManagerUserID'])) ? $data['SiteManagerUserID'] : 0,
                'UserName' => (isset($data['SiteManagerUserName'])) ? $data['SiteManagerUserName'] : 0,
                'ActionDate' => (isset($data['SiteManagerActionDate'])) ? $data['SiteManagerActionDate'] : 0,
                'ActionMessage' => (isset($data['SiteManagerActionMessage'])) ? $data['SiteManagerActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['SiteManagerActionAutoMessage'])) ? $data['SiteManagerActionAutoMessage'] : 0,
                'is-active' => ((isset($data['SiteManagerOBSCategory3ID']) && (isset($data['SiteManagerUserID']))) ?
                ($data['SiteManagerOBSCategory3ID'] . '' != '0' && $data['SiteManagerUserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-3' => [// مدیر مالی 
                'obs' => (isset($data['FinancialSubManagerOBSCategory3ID'])) ? $data['FinancialSubManagerOBSCategory3ID'] : 0,
                'uid' => (isset($data['FinancialSubManagerID'])) ? $data['FinancialSubManagerID'] : 0,
                'UserName' => (isset($data['FinancialSubManagerUserName'])) ? $data['FinancialSubManagerUserName'] : 0,
                'ActionDate' => (isset($data['FinancialSubManagerActionDate'])) ? $data['FinancialSubManagerActionDate'] : 0,
                'ActionMessage' => (isset($data['FinancialSubManagerActionMessage'])) ? $data['FinancialSubManagerActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['FinancialSubManagerActionAutoMessage'])) ? $data['FinancialSubManagerActionAutoMessage'] : 0,
                'ActionMessage' => (isset($data['FinancialSubManagerActionMessage'])) ? $data['FinancialSubManagerActionMessage'] : NULL,
                'is-active' => ((isset($data['FinancialSubManagerOBSCategory3ID']) && (isset($data['FinancialSubManagerID']))) ?
                ($data['FinancialSubManagerOBSCategory3ID'] . '' != '0' && $data['FinancialSubManagerID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-4' => [// رسیدگی کننده
                'obs' => (isset($data['FinancialUserOBSCategory3ID'])) ? $data['FinancialUserOBSCategory3ID'] : 0,
                'uid' => (isset($data['FinancialUserID'])) ? $data['FinancialUserID'] : 0,
                'UserName' => (isset($data['FinancialUserName'])) ? $data['FinancialUserName'] : 0,
                'ActionDate' => (isset($data['FinancialActionDate'])) ? $data['FinancialActionDate'] : 0,
                'ActionAutoMessage' => (isset($data['FinancialUserActionAutoMessage'])) ? $data['FinancialUserActionAutoMessage'] : 0,
                'ActionMessage' => (isset($data['FinancialUserActionMessage'])) ? $data['FinancialUserActionMessage'] : NULL,
                'is-active' => ((isset($data['FinancialUserOBSCategory3ID']) && (isset($data['FinancialUserID']))) ?
                ($data['FinancialUserOBSCategory3ID'] . '' != '0' && $data['FinancialUserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-5' => [// مدیر مربوطه
                'obs' => (isset($data['Manager1OBSCategory3ID'])) ? $data['Manager1OBSCategory3ID'] : 0,
                'uid' => (isset($data['Manager1UserID'])) ? $data['Manager1UserID'] : 0,
                'UserName' => (isset($data['Manager1UserName'])) ? $data['Manager1UserName'] : 0,
                'ActionDate' => (isset($data['Manager1ActionDate'])) ? $data['Manager1ActionDate'] : 0,
                'ActionMessage' => (isset($data['Manager1ActionMessage'])) ? $data['Manager1ActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['Manager1ActionAutoMessage'])) ? $data['Manager1ActionAutoMessage'] : 0,
                'is-active' => ((isset($data['Manager1OBSCategory3ID']) && (isset($data['Manager1UserID']))) ?
                ($data['Manager1OBSCategory3ID'] . '' != '0' && $data['Manager1UserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-6' => [// مدیر عامل
                'obs' => (isset($data['Manager2OBSCategory3ID'])) ? $data['Manager2OBSCategory3ID'] : 0,
                'uid' => (isset($data['Manager2UserID'])) ? $data['Manager2UserID'] : 0,
                'UserName' => (isset($data['Manager2UserName'])) ? $data['Manager2UserName'] : 0,
                'ActionDate' => (isset($data['Manager2ActionDate'])) ? $data['Manager2ActionDate'] : 0,
                'ActionMessage' => (isset($data['Manager2ActionMessage'])) ? $data['Manager2ActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['Manager2ActionAutoMessage'])) ? $data['Manager2ActionAutoMessage'] : 0,
                'is-active' => ((isset($data['Manager2OBSCategory3ID']) && (isset($data['Manager2UserID']))) ?
                ($data['Manager2OBSCategory3ID'] . '' != '0' && $data['Manager2UserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-7' => [// معاون برنامه ریزی و کنترل پروژه
                'obs' => (isset($data['PlanningOBSCategory3ID'])) ? $data['PlanningOBSCategory3ID'] : 0,
                'uid' => (isset($data['PlanningUserID'])) ? $data['PlanningUserID'] : 0,
                'UserName' => (isset($data['PlanningUserName'])) ? $data['PlanningUserName'] : 0,
                'ActionDate' => (isset($data['PlanningActionDate'])) ? $data['PlanningActionDate'] : 0,
                'ActionMessage' => (isset($data['PlanningActionMessage'])) ? $data['PlanningActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['PlanningActionAutoMessage'])) ? $data['PlanningActionAutoMessage'] : 0,
                'is-active' => ((isset($data['PlanningOBSCategory3ID']) && (isset($data['PlanningUserID']))) ?
                ($data['PlanningOBSCategory3ID'] . '' != '0' && $data['PlanningUserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-8' => [// درخواست کننده
                'obs' => (isset($data['RequesterOBSCategory3ID'])) ? $data['RequesterOBSCategory3ID'] : 0,
                'uid' => (isset($data['RequesterUserID'])) ? $data['RequesterUserID'] : 0,
                'UserName' => (isset($data['RequesterUserName'])) ? $data['RequesterUserName'] : 0,
                'ActionDate' => (isset($data['RequesterActionDate'])) ? $data['RequesterActionDate'] : 0,
                'ActionMessage' => (isset($data['RequesterActionMessage'])) ? $data['RequesterActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['RequesterActionAutoMessage'])) ? $data['RequesterActionAutoMessage'] : 0,
                'is-active' => ((isset($data['RequesterOBSCategory3ID']) && (isset($data['RequesterUserID']))) ?
                ($data['RequesterOBSCategory3ID'] . '' != '0' && $data['RequesterUserID'] . '' != '0' ) :
                false
                )
            ],
            'sgn-9' => [// مدیر مربوطه      //معاون مالی و اقتصادی
                'obs' => (isset($data['FinancialManagerOBSCategory3ID'])) ? $data['FinancialManagerOBSCategory3ID'] : 0,
                'uid' => (isset($data['FinancialManagerID'])) ? $data['FinancialManagerID'] : 0,
                'UserName' => (isset($data['FinancialManagerUserName'])) ? $data['FinancialManagerUserName'] : 0,
                'ActionDate' => (isset($data['FinancialManagerActionDate'])) ? $data['FinancialManagerActionDate'] : 0,
                'ActionMessage' => (isset($data['FinancialManagerActionMessage'])) ? $data['FinancialManagerActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['FinancialManagerActionAutoMessage'])) ? $data['FinancialManagerActionAutoMessage'] : 0,
                'ActionMessage' => (isset($data['FinancialManagerActionMessage'])) ? $data['FinancialManagerActionMessage'] : NULL,
                'is-active' => ((isset($data['FinancialManagerOBSCategory3ID']) && (isset($data['FinancialManagerID']))) ?
                ($data['FinancialManagerOBSCategory3ID'] . '' != '0' && $data['FinancialManagerID'] . '' != '0' ) :
                false
                )
            ],
            // @@Add Kamali
            'sgn-10' => [//        امضا خزانه
                'obs' => (isset($data['TreasuryOBSCategory3ID'])) ? $data['TreasuryOBSCategory3ID'] : 0,
                'uid' => (isset($data['TreasuryID'])) ? $data['TreasuryID'] : 0,
                'UserName' => (isset($data['TreasuryUserName'])) ? $data['TreasuryUserName'] : 0,
                'ActionDate' => (isset($data['TreasuryActionDate'])) ? $data['TreasuryActionDate'] : 0,
                'ActionMessage' => (isset($data['TreasuryActionMessage'])) ? $data['TreasuryActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['TreasuryActionAutoMessage'])) ? $data['TreasuryActionAutoMessage'] : 0,
                'is-active' => ((isset($data['TreasuryOBSCategory3ID']) && (isset($data['TreasuryID']))) ?
                ($data['TreasuryOBSCategory3ID'] . '' != '0' && $data['TreasuryID'] . '' != '0' ) :
                false
                )
            ],
            // @@Add Kamali
            'sgn-11' => [//        امضا خزانه
                'obs' => (isset($data['karpardaziOBSCategory3ID'])) ? $data['karpardaziOBSCategory3ID'] : 0,
                'uid' => (isset($data['karpardaziID'])) ? $data['karpardaziID'] : 0,
                'UserName' => (isset($data['karpardaziUserName'])) ? $data['karpardaziUserName'] : 0,
                'ActionDate' => (isset($data['karpardaziActionDate'])) ? $data['karpardaziActionDate'] : 0,
                'ActionMessage' => (isset($data['karpardaziActionMessage'])) ? $data['karpardaziActionMessage'] : 0,
                'ActionAutoMessage' => (isset($data['karpardaziActionAutoMessage'])) ? $data['karpardaziActionAutoMessage'] : 0,
                'is-active' => ((isset($data['karpardaziOBSCategory3ID']) && (isset($data['karpardaziID']))) ?
                ($data['karpardaziOBSCategory3ID'] . '' != '0' && $data['karpardaziID'] . '' != '0' ) :
                false
                )
            ],
            'Manager2Message' => (isset($data["Process" . $pid . "Manager2Message"])) ? $data["Process" . $pid . "Manager2Message"] : '',
            'PlanningAutoMessage' => (isset($data["Process" . $pid . "PlanningAutoMessage"])) ? $data["Process" . $pid . "PlanningAutoMessage"] : '',
            'PlanningMessage' => (isset($data["Process" . $pid . "PlanningMessage"])) ? $data["Process" . $pid . "PlanningMessage"] : '',
            'location' => (isset($data["WorkflowLocation"])) ? $data["WorkflowLocation"] : 'tehran',
        );
    }

    private function getCreateFormLabels() {
        $arrLable = array(
            0 => 'Integrated Cost Management system',
            1 => 'سيستم مديريت کیفیت <br />فرم درخواست وجه',
            2 => 'شناسه:',
            3 => array(
                'tehran' => ' معاون برنامه ریزی و کنترل پروژه',
                'site' => 'مدیر برنامه ریزی و کنترل پروژه',
            ),
            4 => array(
                'tehran' => 'مدیر/ معاون مربوطه',
                'site' => 'معاون مربوطه',
            ),
            67 => array(
                'tehran' => 'رئیس کارگاه',
                'site' => 'مدیر مربوطه',
            ),
            5 => array(
                'tehran' => 'مسئول/ مدیر کنترل هزینه',
                'site' => 'مسئول کنترل هزینه',
            ),
            6 => array(
                'tehran' => 'مدیر/ معاون برنامه ریزی و کنترل پروژه',
                'site' => 'مدیر برنامه ریزی و کنترل پروژه',
            ),
            7 => array(
                'tehran' => 'معاون برنامه ریزی و کنترل پروژه',
                'site' => 'مدیر برنامه ریزی و کنترل پروژه',
            ),
            8 => array(
                'tehran' => 'مدیرعامل',
                'site' => 'رئیس کارگاه',
            ),
            9 => array(
                'tehran' => 'مسئول / مدیرمالی',
                'site' => 'مسئول مالی',
            ),
            10 => array(
                'tehran' => 'مدیر / معاون مالی و اقتصادی',
                'site' => 'مدیر مالی و اقتصادی',
            ),
            11 => array(
                'tehran' => 'مدیر عامل',
                'site' => 'رئیس کارگاه',
            ),
            12 => array(
                'tehran' => 'معاونت محترم مالی و اقتصادی',
                'site' => 'مدیریت محترم مالی و اقتصادی',
            ),
            13 => 'ردیف',
            14 => 'گیرنده وجه حقیقی/حقوقی', // ' شخص حقیقی/ حقوقی',
            15 => 'مسئول محترم خزانه داری',
            16 => 'شماره قرارداد',
            17 => 'موضوع', //'بابت',
            18 => 'شماره حساب- بانک- صاحب حساب', //'شماره حساب- نزد بانک- بنام',
            19 => 'مبلغ',
            20 => 'ارز',
            21 => 'مبلغ به حروف',
            22 => 'درخواست کننده',
            23 => 'با احترام و باتوجه به درخواست فوق و بررسی های بعمل آمده و با عنایت به توضیحات ذیل، نظر این معاونت عبارتست از :',
            24 => 'توضیحات',
            25 => 'ردیف',
            26 => 'شخص حقیقی/ حقوقی',
            27 => 'شماره قرارداد',
            28 => 'ردیف بودجه',
            29 => 'عنوان بودجه',
            30 => 'مبلغ',
            31 => 'ارز',
            32 => 'مبلغ به حروف',
            33 => 'با احترام، پرداخت بشرح ذیل صورت گیرد؛',
            34 => 'ردیف',
            35 => 'شخص حقیقی/ حقوقی',
            36 => 'شماره قرارداد',
            37 => 'مبلغ',
            38 => 'ارز',
            39 => 'مبلغ به حروف',
            40 => 'توضیحات :',
            41 => 'با احترام، پرداخت بشرح ذیل صورت گیرد؛',
            42 => 'معادل ارز',
            43 => 'ردیف',
            44 => 'شخص حقیقی/ حقوقی',
            45 => 'شماره قرارداد',
            46 => 'مبلغ',
            47 => 'ارز',
            48 => 'مبلغ به حروف',
            49 => 'مبلغ',
            50 => 'ارز',
            51 => 'مبلغ به حروف',
            52 => 'رسیدگی کننده',
            53 => 'ردیف',
            54 => 'چک/ حواله شماره ',
            55 => 'به مبلغ',
            56 => 'ارز',
            57 => 'مبلغ به حروف',
            58 => 'تحویل گیرنده',
            59 => 'امضا',
            60 => 'خزانه دار',
            61 => 'Doc Type',
            62 => 'Doc Type',
            63 => 'Discipline',
            64 => 'Field Code',
            65 => 'Prj ID',
            66 => ' سلام علیکم، با احترام، خواهشمند است، درصورت صلاحدید با پرداخت وجه به شرح و مشخصات ذیل موافقت فرمائید :',
            67 => 'تحویل گیرنده چک / حواله',
            68 => 'بررسی مسئول / مدیر',
            69 => 'باقیمانده بودجه سالیانه',
        );

        return $arrLable;
    }

    public function actionCheckNextActionWarranty($peid) {
        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        return $this->renderAjax('_warrantyDetail', [
                    'processEntityModel' => $processEntityModel,
        ]);
    }

    public function actionCheckNextActionDebitmemo($peid) {
        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        $debitmemoAmountSearchModel = new \app\modules\debitmemo\models\DebitmemoAmountSearch();
        $debitmemoAmountSearchModel->setScenario('search');
        $res = $debitmemoAmountSearchModel->searchDebitmemo(\Yii::$app->request->queryParams, $processEntityModel->debitmemoIdRef);
        $debitmemoAmountDataProvider = $res['dataProvider'];
        $debitmemoAmountTotalDataProvider = $res['totalDataProvider'];

        $invoiceSearchModel = new \app\modules\invoice\models\InvoiceSearchDebitmemo();
        $invoiceSearchModel->setScenario('search');
        $res = $invoiceSearchModel->search(Yii::$app->request->queryParams, $processEntityModel->debitmemoIdRef);
        $invoiceDataProvider = $res['dataProvider'];
        return $this->renderAjax('_debitmemoDetail', [
                    'processEntityModel' => $processEntityModel,
                    'debitmemoAmountDataProvider' => $debitmemoAmountDataProvider,
                    'invoiceDataProvider' => $invoiceDataProvider,
                    'debitmemoAmountTotalDataProvider' => $debitmemoAmountTotalDataProvider,
        ]);
    }

    public function actionCheckNextActionActualProgress($peid) {
        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        $budgetNodeSearchModel = new \app\modules\budget\models\BudgetNodeSearch();
        $budgetNodeSearchModel->setScenario('search');
        $budgetNodeDataProvider = $budgetNodeSearchModel->searchBudget(NULL, $processEntityModel->actualProgress->plan->budget->BudgetID);

        return $this->renderAjax('_actualProgressDetail', [
                    'processEntityModel' => $processEntityModel,
                    'budgetNodeDataProvider' => $budgetNodeDataProvider,
        ]);
    }

    public function actionCheckNextActionBudget($peid) {
        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        return $this->renderAjax('_budgetDetail', [
                    'processEntityModel' => $processEntityModel,
        ]);
    }

    public function actionCheckNextActionCapitalBalance($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $actionEntityFormData["CurrentActionTitle"] = \app\modules\workflow\models\Action::find()->where('ActionID=' . $actionData['FromActionIDRef'])->one()->ActionTitle1;
        $actionEntityFormData["NextActionTitle"] = \app\modules\workflow\models\Action::find()->where('ActionID=' . $actionData['ActionIDRef'])->one()->ActionTitle1;

        $peid = Html::encode($peid);
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!isset($processEntityModel->capitalBalanceIdRef)) {
            throw new \yii\web\HttpException("data not found", 501);
        }
        $capitalBalaceModel = \app\modules\capital\models\CapitalBalance::find()
                ->where(['capitalBalanceId' => $processEntityModel->capitalBalanceIdRef])
                ->one();

        $capitalAmountSearchModel = new \app\modules\capital\models\CapitalBalanceAmountSearch();
        $capitalAmountDataProvider = $capitalAmountSearchModel->searchCapital2(\Yii::$app->request->queryParams, $processEntityModel->capitalBalanceIdRef);

        $capitalAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
        $sumCapitalAmount = ActiveModel::find('\app\modules\capital\models\CapitalBalanceAmount')
                ->where(['capitalBalanceIdRef' => $capitalBalaceModel->capitalBalanceId])
                ->andWhere($capitalAmountTable . '.isDeleted is null or ' . $capitalAmountTable . '.isDeleted != 1')
                ->sum('financialAmount');
        return $this->renderAjax('_capitalBalanceDetail', [
                    'processEntityModel' => $processEntityModel,
                    'capitalBalaceModel' => $capitalBalaceModel,
                    'capitalAmountSearchModel' => $capitalAmountSearchModel,
                    'capitalAmountDataProvider' => $capitalAmountDataProvider,
                    'actionEntityFormData' => $actionEntityFormData,
                    'sumCapitalAmount' => $sumCapitalAmount,
        ]);
    }

    public function actionCheckNextActionClaim($peid) {
        $peid = Html::encode($peid);
        $actionData = Yii::$app->session['EntityID-' . $peid];

        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        $claimModel = \app\modules\claim\models\Claim::find()
                ->where(['id' => $processEntityModel->claimIdRef])
                ->one();

        $contractModel = \app\modules\contract\models\Contract::find()
                ->where(['ContractID' => $claimModel->contractIdRef])
                ->one();

        $debitmemoAmountSearchModel = new \app\modules\debitmemo\models\DebitmemoAmountSearch();
        $debitmemoAmountSearchModel->setScenario('search');
        $res = $debitmemoAmountSearchModel->searchDebitmemo(\Yii::$app->request->queryParams, $processEntityModel->debitmemoIdRef);

        $claimSearchModel = new \app\modules\claim\models\ClaimSearch();
        $claimSearchModel->setScenario('search');
        $res = $claimSearchModel->searchClaim(\Yii::$app->request->queryParams, $processEntityModel->claimIdRef);
        $claimDataProvider = $res['dataProvider'];
        $claimTotal = $res['dataTotal'];

        $claimAmountSearchModel = new \app\modules\claim\models\ClaimAmountSearch();
        $claimAmountSearchModel->setScenario('search');
        $claimAmountSearchDataProvider = $claimAmountSearchModel->searchClaim(NULL, $processEntityModel->claimIdRef);
        $claimAmountSearchDataProvider->pagination->pageSize = false;

        $actionEntityFormData = [];

        $actionEntityFormData["CurrentActionID"] = $processEntityModel->LastActionIDRef;
        $actionEntityFormData["CurrentActionTitle"] = \app\modules\workflow\models\Action::find()->where('ActionID=' . $actionData['FromActionIDRef'])->one()->ActionTitle1;
        $actionEntityFormData["NextActionTitle"] = \app\modules\workflow\models\Action::find()
                        ->where(['ActionID' => $actionData['ActionIDRef']])
                        ->one()
                ->ActionTitle1;
        $actionEntityFormData["Message"] = $actionData['ActionMessage'];        //get
        $actionEntityFormData["AutoMessage"] = $actionData['ActionAutoMessage'];    // 
        $actionEntityFormData["NextActionID"] = $actionData['ActionIDRef']; //get
        $actionEntityFormData['ActionDate'] = $actionData['ActionDate'];
        $actionEntityFormData['LetterNumber'] = $actionData['LetterNumber'];

        return $this->renderAjax('_claimDetail', [
                    'processEntityModel' => $processEntityModel,
                    'claimAmountSearchDataProvider' => $claimAmountSearchDataProvider,
                    'claimDataProvider' => $claimDataProvider,
                    'claimTotal' => $claimTotal,
                    'claimModel' => $claimModel,
                    'contractModel' => $contractModel,
                    'actionEntityFormData' => $actionEntityFormData,
        ]);
    }

    public function actionCheckNextActionTender($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->TenderIDRef > 0)) {
            return false;
        }

        $tenderModel = \app\modules\tender\models\Tender::find()->where(['TenderID' => $processEntityModel->TenderIDRef])->one();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
        $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->all();
        return $this->renderAjax('_tenderDetail', [
                    'tenderModel' => $tenderModel,
                    'processEntityModel' => $processEntityModel,
                    'actionOrderMeasures' => $actionOrderMeasures,
        ]);
    }

    public function actionCheckNextActionContractClosingDocument($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->ContractClosingDocumentIDRef > 0)) {
            return false;
        }

        $contractDocumentModel = \app\modules\contract\models\ContractDocument::find()->where(['ContractDocumentID' => $processEntityModel->ContractClosingDocumentIDRef])->one();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
        $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->all();
        return $this->renderAjax('_contractClosingDocumentDetail', [
                    'contractDocumentModel' => $contractDocumentModel,
                    'processEntityModel' => $processEntityModel,
                    'actionOrderMeasures' => $actionOrderMeasures,
        ]);
    }

    public function actionCheckNextActionAutomation($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->AutomationIDRef > 0)) {
            return false;
        }

        $automationModel = \app\modules\automation\models\Automation::find()->where(['AutomationID' => $processEntityModel->AutomationIDRef])->one();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
        $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->all();
        return $this->renderAjax('_automationDetail', [
                    'automationModel' => $automationModel,
                    'processEntityModel' => $processEntityModel,
                    'actionOrderMeasures' => $actionOrderMeasures,
        ]);
    }

    public function actionCheckNextActionNetwork($peid) {
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->NetworkIDRef > 0)) {
            return false;
        }

        $networkModel = \app\modules\automation\models\Network::find()->where(['NetworkID' => $processEntityModel->NetworkIDRef])->one();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
        $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->all();
        return $this->renderAjax('_networkDetail', [
                    'networkModel' => $networkModel,
                    'processEntityModel' => $processEntityModel,
                    'actionOrderMeasures' => $actionOrderMeasures,
        ]);
    }

    public function actionCheckNextActionEmployeeMembership($peid) {
//        echo $peid;
//        die();
        $actionData = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->ObjectIDRef > 0)) {
            return false;
        }

        $employeeMembershipModel = \app\modules\automation\models\EmployeeMembership::find()->where(['EmployeeMembershipID' => $processEntityModel->ObjectIDRef])->one();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
        $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->all();
        return $this->renderAjax('_employeeMembershipDetail', [
                    'employeeMembershipModel' => $employeeMembershipModel,
                    'processEntityModel' => $processEntityModel,
                    'actionOrderMeasures' => $actionOrderMeasures,
        ]);
    }

    public function actionCheckNextActionForeignMission($peid) {

        $actionData = Yii::$app->session['EntityID-' . $peid];
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }
        if (!($processEntityModel->ObjectIDRef > 0)) {
            return false;
        }

        $foreignMissionModel = \app\modules\automation\models\ForeignMission::find()->where(['MissionID' => $processEntityModel->ObjectIDRef])->one();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
        $actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->all();
        return $this->renderAjax('_foreignMissionDetail', [
                    'foreignMissionModel' => $foreignMissionModel,
                    'processEntityModel' => $processEntityModel,
                    'actionOrderMeasures' => $actionOrderMeasures,
        ]);
    }

    public function actionCheckNextActionEmployeeSettlement($peid) {
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();
        if (!$processEntityModel) {
            return false;
        }

        $employeeSettlementModel = \app\modules\automation\models\EmployeeSettlement::find()->where(['EmployeeSettlementID' => $processEntityModel->ObjectIDRef])->one();

        return $this->renderAjax('_employeeSettlementDetail', [
                    'employeeSettlementModel' => $employeeSettlementModel,
                    'processEntityModel' => $processEntityModel,
        ]);
    }

       public function actionCheckNextActionStamp($peid) {
    $actionData = Yii::$app->session['EntityID-' . $peid];
    $processEntityModel = ProcessEntity::find()
        ->where(['ProcessEntityID' => $peid])
        ->one();
    if (!$processEntityModel || !($processEntityModel->StampIDRef > 0)) {
        return false;
    }
    $stampModel = StampDocument::find()
        ->where(['StampID' => $processEntityModel->StampIDRef])
        ->one();
    if (!$stampModel) {
        return "سند مهر یافت نشد.";
    }
        $internalScopeTitles = [];
    $externalScopeTitles = [];
        if ($stampModel->internalScope) {
        $internalScopeTitles = array_column($stampModel->internalScope, 'ScopeTitle');
    }

    if ($stampModel->externalScope) {
        $externalScopeTitles = array_column($stampModel->externalScope, 'ScopeTitle');
    }
    return $this->renderAjax('_stampDetail', [
        'stampModel' => $stampModel,
        'processEntityModel' => $processEntityModel,
        'actionOrderMeasures' => [], // خالی (چک‌لیست نداریم)
        'internalScope' => $internalScopeTitles,
        'externalScope' => $externalScopeTitles,
    ]);
}   /**
 * مودال جزئیات امحا نشان سازمانی قبل از Sign
 */
    public function actionCheckNextActionRefundStamp($peid)
    {
        $actionData = Yii::$app->session['EntityID-' . $peid] ?? null;
        $processEntityModel = ProcessEntity::find()
            ->where(['ProcessEntityID' => $peid])
            ->one();
        if (!$processEntityModel) {
            return false;
        }
        $stampId = !empty($processEntityModel->ObjectIDRef)
            ? $processEntityModel->ObjectIDRef
            : $processEntityModel->StampIDRef;
        if (empty($stampId)) {
            return false;
        }
        $stampModel = \app\modules\document\models\StampDocument::find()
            ->where(['StampId' => $stampId, 'isDeleted' => 0])
            ->one();
        if (!$stampModel) {
            return 'سند نشان یافت نشد.';
        }
        return $this->renderAjax('_refundDetail', [
            'stampModel'          => $stampModel,
            'processEntityModel'  => $processEntityModel,
            'actionOrderMeasures' => [], // فعلاً چک‌لیست نداریم
        ]);
    }
    /**
     *  section sign
     * @param type $peid
     * 
     */
    public function actionSign($peid) {
        //هنوز کامل نشده
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        //fetch current ActionEntity from session
        $session = Yii::$app->session['EntityID-' . $peid];
        if (empty($session)) {
            return [
                'result' => false,
                'message' => ['This action has passed by another user, StoreValue expired!!'],
            ];
        }
        //check for go to next action
        $res = ProcessEntity::canNextAction($peid, $session['FromActionIDRef'], $session['ActionIDRef']);
        // ارسال اکشن به مرحله بعدی
        if ($res['result'] == true) {
            $res = ProcessEntity::goToNextAction($peid);
        }

        return $res;
    }

    public function actionAllNextAction($peids) {
        $peidsArr = explode(',', $peids);
        //processEntity find model
        $processEntityQuery = ProcessEntity::find()
                ->where(['IN', 'ProcessEntityID', $peidsArr]);
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $processEntityQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);
        //یکی بودنProcess
        $processEntityModels = $dataProvider->getModels();
        $procesIds = ArrayHelper::getColumn($processEntityModels, 'ProcessIDRef');
        $id = $procesIds[0];
        foreach ($procesIds as $procesId) {
            if ($id != $procesId) {
                return \app\components\Utility::renderPjaxCallBack('allmodalCallbackPjax', [
                            'result' => false,
                            'message' => 'You can not submit multiple inequality workflow'
                ]);
            }
        }

        $actionIds = ArrayHelper::getColumn($processEntityModels, 'LastActionIDRef');
        $aid = $actionIds[0];

        foreach ($actionIds as $actionId) {
            if ($aid != $actionId) {
                return \app\components\Utility::renderPjaxCallBack('allmodalCallbackPjax', [
                            'result' => false,
                            'message' => 'You can not submit multiple inequality workflow'
                ]);
            }
        }
        //jDateTime new object
        $jDate = new jDateTime(true, true, 'Asia/Tehran');

        $nextactionForm = new \app\modules\workflow\models\NextactionForm();
        $nextactionForm->actionDateHijri = $jDate->date("Y/m/d-Hi", false, false);
        $nextactionForm->actionDate = $jDate->date("Y-m-d", false, false, false);
        $nextactionForm->processEntityIDs = $peids;
        $nextactionForm->fromActionId = $processEntityModels[0]->LastActionIDRef;
        $nextactionForm->actionAutoMessage = '';

        if ($nextactionForm->load(\Yii::$app->request->post()) && $nextactionForm->validate()) {
            
        }

        //--------------------------------------------------------
        //fetch data dropDown next step
        $actionItems = \app\modules\workflow\models\ActionOrder::find()
                ->nextAction(ArrayHelper::getColumn($processEntityModels, 'ProcessEntityID'))
                ->asArray()
                ->all();
        if (Yii::$app->request->isGet) {
            //set default action
            foreach ($actionItems as $dt) {
                if ($dt['isDefaultNextAction'] == 1) {
                    $nextactionForm->actionId = $dt['NextActionIDRef'];
                    $nextactionForm->actionAutoMessage = $dt['AutoMessage1'];
                }
            }
        }

        $dropDownDataActions = \yii\helpers\ArrayHelper::map($actionItems, 'nextAction.ActionID', 'nextAction.ActionTitle1');

        //fetch data current action of Action Model
        $currentActionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $processEntityModels[0]->LastActionIDRef])
                ->one();

        //در آوردن لیست نامه های قابل انتخاب
        $nextactionForm->actionId = $nextactionForm->actionId;
        $letterTitlesMap = [];
        if (!empty($nextactionForm->actionId)) {
            $letterTitles = Letter::letterItemTitle($processEntityModels[0]->LastActionIDRef, $nextactionForm->actionId);
            $letterTitlesMap = \yii\helpers\ArrayHelper::map($letterTitles, 'letterIdRef1', 'letterTitle');
        }

        foreach ($processEntityModels as $processEntityModel) {
            //چک کردن برای رفتن به مرحله بعد 
            $result = ProcessEntity::canNextAction($processEntityModel->ProcessEntityID, $nextactionForm->fromActionId, $nextactionForm->actionId);
            if ($result['result'] == false) {
                foreach ($result['message'] as $message) {
                    Yii::$app->session->addFlash('danger', $message);
                }
            }
        }

        return $this->renderAjax('all_nextaction', [
                    'dataProvider' => $dataProvider,
                    'dropDownDataActions' => $dropDownDataActions,
                    'nextactionForm' => $nextactionForm,
                    'currentActionModel' => $currentActionModel,
                    'processEntityModels' => $processEntityModels,
                    'letterTitlesMap' => $letterTitlesMap,
                    'peids' => $peids
        ]);
    }

    /**
     * 
     * @param type $peids
     * @param type $faId FromActionIDRef 
     * @param type $aId ActionIDRef
     * @return type
     */
    public function actionAllSign() {
        $nextactionForm = new \app\modules\workflow\models\NextactionForm();
        //load and validate
        if ($nextactionForm->load(Yii::$app->request->post()) && $nextactionForm->validate()) {
            $result = $nextactionForm->goMultiple();
            return \app\components\Utility::renderPjaxCallBack('allmodalCallbackPjax', [
                        'result' => $result['result'],
                        'message' => $result['message'],
            ]);
        }
    }

}
