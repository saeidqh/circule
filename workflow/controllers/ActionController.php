<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;

class ActionController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['update', 'param-permission', 'param-invoice', 'param-cash-request', 'param-debitmemo', 'param-contract', 'param-pudget', 'param-waranty', 'param-capital', 'param-claim', 'param-sourcing', 'param-tender', 'param-contract-closing-document', 'param-automation', 'param-network'],
                'rules' => [
                    // allow authenticated users
                    [
                        'actions' => ['update', 'edit-action-order', 'param-permission', 'param-invoice', 'param-cash-request', 'param-debitmemo', 'param-contract', 'param-pudget', 'param-waranty', 'param-capital', 'param-claim', 'param-sourcing', 'param-tender', 'param-contract-closing-document', 'param-automation', 'param-network'],
                        'roles' => ['@'],
                        'allow' => true,
                        'matchCallback' => function($rule, $action, $exteraData = []) {
//                    $pid = isset($exteraData['pid']) ? $exteraData['pid'] : Yii::$app->request->get('pid');
//                    return \app\access\AccessControl::isAccess(['process', 'pid' => $pid, 'type' => \app\access\ProcessAccess::PROCESS_UPDATE_TYPE]);
                    return \app\access\AccessControl::isAccess(
                                    ['obs', 'check' => 'isCandoAdmin']
                    );
                },
                    ],
                ],
            ],
        ];
    }

    public function actionUpdate($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()->where(['ActionID' => $aid])->one();
        $actionModel->companyIdList = explode('-', $actionModel->companyIdList);
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }

        $companyArray = \yii\helpers\ArrayHelper::map(\app\modules\OBS\models\Company::find()->joinWith('company')->asArray()->all(), 'company.PersonID', 'company.PersonTitle');

        $arrCalender = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\Calender::find()->asArray()->all(), 'calenderId', 'calenderTitle');
        $characterTypeArray = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\ActionCharacterType::find()->asArray()->all(), 'CharacterTypeID', 'CharacterTypeTitle');
        if ($actionModel) {
            if ($actionModel->load(Yii::$app->request->Post())) {
                // create company list for companyIdList field
                $actionModel->companyIdList = '';
                foreach (Yii::$app->request->Post('Action')['companyIdList'] as $company)
                    $actionModel->companyIdList .= (($actionModel->companyIdList != '') ? '-' : '') . $company;
                if ($actionModel->validate()) {
                    $actionModel->hasChange = 1;
                    if ($actionModel->save()) {
                        $actionTable = \app\modules\workflow\models\Action::tableName();
                        $activityManagement = new \app\components\ActivityManagement();
                        $actionType = "Edit";
                        $subType = "EditAction";
                        $subTypeDescription["TableName"] = $actionTable;
                        $subTypeDescription["RecordPrimaryKey"] = $actionModel->ActionID;
                        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

                        $res = 'true*Process Action updated successfully.';
                    } else {
                        $res = 'false*Is a problem on saving data!';
                    }
                } else {
                    $res = 'false*Is a problem on validating data!';
                }
                return $res;
            }
        } else {
            return 'false*Record not found';
        }
        return $this->renderAjax('actionForm', [
                    'actionModel' => $actionModel,
                    'arrCalender' => $arrCalender,
                    'companyArray' => $companyArray,
                    'characterTypeArray' => $characterTypeArray,
        ]);
    }

    public function actionParamInvoice($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()->where(['ActionID' => $aid])->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamInvoiceModel = new \app\modules\workflow\models\ActionParamInvoice();
        $actionParamInvoiceModel->find($aid);

        if ($actionParamInvoiceModel->load(Yii::$app->request->Post())) {
            if ($actionParamInvoiceModel->validate()) {
                if ($actionParamInvoiceModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramInvoiceForm', [
                    'actionModel' => $actionModel,
                    'actionParamInvoiceModel' => $actionParamInvoiceModel,
        ]);
    }

    public function actionParamCashRequest($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamCashRequestModel = new \app\modules\workflow\models\ActionParamCashRequest();
        $actionParamCashRequestModel->find($aid);

        // check param alrady saved
        if ($actionParamCashRequestModel->load(Yii::$app->request->Post())) {
            if ($actionParamCashRequestModel->validate()) {
                if ($actionParamCashRequestModel->save($aid)) {
//                    Yii::$app->replication->batchSynchChange('\app\modules\workflow\models\ActionParam');
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramCashRequestForm', [
                    'actionModel' => $actionModel,
                    'actionParamCashRequestModel' => $actionParamCashRequestModel,
        ]);
    }

    public function actionParamDebitmemo($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamDebitmemoModel = new \app\modules\workflow\models\ActionParamDebitmemo();
        $actionParamDebitmemoModel->find($aid);

        // check param alrady saved
        if ($actionParamDebitmemoModel->load(Yii::$app->request->Post())) {
            if ($actionParamDebitmemoModel->validate()) {
                if ($actionParamDebitmemoModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramDebitmemoForm', [
                    'actionModel' => $actionModel,
                    'actionParamDebitmemoModel' => $actionParamDebitmemoModel,
        ]);
    }

    public function actionParamContract($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamContractModel = new \app\modules\workflow\models\ActionParamContract();
        $actionParamContractModel->find($aid);
        // check param alrady saved
        if ($actionParamContractModel->load(Yii::$app->request->Post())) {
            if ($actionParamContractModel->validate()) {
                if ($actionParamContractModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        return $this->renderAjax('_paramContractForm', [
                    'actionModel' => $actionModel,
                    'actionParamContractModel' => $actionParamContractModel,
        ]);
    }

    public function actionParamBudget($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamBudgetModel = new \app\modules\workflow\models\ActionParamBudget();
        $actionParamBudgetModel->find($aid);

        if ($actionParamBudgetModel->load(Yii::$app->request->Post())) {
            if ($actionParamBudgetModel->validate()) {
                if ($actionParamBudgetModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramBudgetForm', [
                    'actionModel' => $actionModel,
                    'actionParamBudgetModel' => $actionParamBudgetModel,
        ]);
    }

    public function actionParamWaranty($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamPermission($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamPermissionModel = new \app\modules\workflow\models\ActionParamPermission();
        $actionParamPermissionModel->find($aid);

        if ($actionParamPermissionModel->load(Yii::$app->request->Post())) {
            if ($actionParamPermissionModel->validate()) {
                if ($actionParamPermissionModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramPermissionForm', [
                    'actionModel' => $actionModel,
                    'actionParamPermissionModel' => $actionParamPermissionModel,
        ]);
    }

    public function actionParamCapital($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamCapitalModel = new \app\modules\workflow\models\ActionParamCapital();
        $actionParamCapitalModel->find($aid);

        if ($actionParamCapitalModel->load(Yii::$app->request->Post())) {
            if ($actionParamCapitalModel->validate()) {
                if ($actionParamCapitalModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramCapitalForm', [
                    'actionModel' => $actionModel,
                    'actionParamCapitalModel' => $actionParamCapitalModel,
        ]);
    }

    public function actionParamClaim($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamClaimModel = new \app\modules\workflow\models\ActionParamClaim();
        $actionParamClaimModel->find($aid);

        if ($actionParamClaimModel->load(Yii::$app->request->Post())) {
            if ($actionParamClaimModel->validate()) {
                if ($actionParamClaimModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramClaimForm', [
                    'actionModel' => $actionModel,
                    'actionParamClaimModel' => $actionParamClaimModel,
        ]);
    }

    public function actionParamSourcing($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamSourcingModel = new \app\modules\workflow\models\ActionParamSourcing();
        $actionParamSourcingModel->find($aid);

        if ($actionParamSourcingModel->load(Yii::$app->request->Post())) {
            if ($actionParamSourcingModel->validate()) {
                if ($actionParamSourcingModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramSourcingForm', [
                    'actionModel' => $actionModel,
                    'actionParamSourcingModel' => $actionParamSourcingModel,
        ]);
    }

    public function actionParamTender($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()->where(['ActionID' => $aid])->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamTenderModel = new \app\modules\workflow\models\ActionParamTender();
        $actionParamTenderModel->find($aid);

        if ($actionParamTenderModel->load(Yii::$app->request->Post())) {
            if ($actionParamTenderModel->validate()) {
                if ($actionParamTenderModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramTenderForm', [
                    'actionModel' => $actionModel,
                    'actionParamTenderModel' => $actionParamTenderModel,
        ]);
    }
    
    public function actionParamContractClosingDocument($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()->where(['ActionID' => $aid])->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamContractClosingDocumentModel = new \app\modules\workflow\models\ActionParamContractClosingDocument();
        $actionParamContractClosingDocumentModel->find($aid);

        if ($actionParamContractClosingDocumentModel->load(Yii::$app->request->Post())) {
            if ($actionParamContractClosingDocumentModel->validate()) {
                if ($actionParamContractClosingDocumentModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        // if not save add new params
        return $this->renderAjax('_paramContractClosingDocumentForm', [
                    'actionModel' => $actionModel,
                    'actionParamContractClosingDocumentModel' => $actionParamContractClosingDocumentModel,
        ]);
    }
    
    public function actionParamAutomation($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamAutomationModel = new \app\modules\workflow\models\ActionParamAutomation();
        $actionParamAutomationModel->find($aid);
        // check param alrady saved
        if ($actionParamAutomationModel->load(Yii::$app->request->Post())) {
            if ($actionParamAutomationModel->validate()) {
                if ($actionParamAutomationModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        return $this->renderAjax('_paramAutomationForm', [
                    'actionModel' => $actionModel,
                    'actionParamAutomationModel' => $actionParamAutomationModel,
        ]);
    }

    public function actionParamNetwork($pid, $aid) {
        $actionModel = \app\modules\workflow\models\Action::find()
                ->where(['ActionID' => $aid])
                ->one();
        if ($actionModel->ProcessIDRef != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }
        $actionParamNetworkModel = new \app\modules\workflow\models\ActionParamNetwork();
        $actionParamNetworkModel->find($aid);
        // check param alrady saved
        if ($actionParamNetworkModel->load(Yii::$app->request->Post())) {
            if ($actionParamNetworkModel->validate()) {
                if ($actionParamNetworkModel->save($aid)) {
                    $res = 'true*Action Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }
        return $this->renderAjax('_paramNetworkForm', [
                    'actionModel' => $actionModel,
                    'actionParamNetworkModel' => $actionParamNetworkModel,
        ]);
    }
}
