<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;

class ActionOrderController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['update', 'param-permission', 'param-invoice', 'param-cash-request', 'param-debitmemo', 'param-contract', 'param-pudget', 'param-waranty', 'param-capital', 'param-claim', 'param-sourcing', 'param-sourcing', 'document-required-type'],
                'rules' => [
                    [
                        'actions' => ['update', 'param-permission', 'param-invoice', 'param-cash-request', 'param-debitmemo', 'param-contract', 'param-pudget', 'param-waranty', 'param-capital', 'param-claim', 'param-sourcing', 'param-sourcing', 'document-required-type'],
                        'roles' => ['@'],
                        'allow' => true,
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            return \app\access\AccessControl::isAccess(
                                    ['obs', 'check' => 'isCandoAdmin']
                            );
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionUpdate($pid, $aoid) {
        $letterTable = \app\modules\workflow\models\Letter::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();

        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ID' => $aoid])->one();
        $processId = $actionOrderModel->action->ProcessIDRef;
        if ($processId != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }

        $processTypeId = $actionOrderModel->action->process->ProccessTypeIDRef;
        $processCompanyId = $actionOrderModel->action->process->OBSCategory3CompanyIDRef;
        if ($processCompanyId > 0) {
            $arrLetter = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\Letter::find()->andWhere("processTypeIdRef=$processTypeId and OBSCategory3CompanyIDRef=$processCompanyId")->asArray()->all(), 'letterId', 'letterTitle');
        } else {
            $arrLetter = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\Letter::find()
                                    ->innerJoin($personTable, "$personTable.PersonID=$letterTable.OBSCategory3CompanyIdRef")
                                    ->addSelect([
                                        'letterId',
                                        'letterTitle',
                                        'company' => "$personTable.PersonTitle",
                                    ])->andWhere("processTypeIdRef=$processTypeId")->asArray()->all(), 'letterId', 'letterTitle', 'company');
        }

        $arrDocumentModel = \yii\helpers\ArrayHelper::map(\app\components\ActiveModel::find('\app\modules\workflow\models\DocumentModel')->asArray()->all(), 'modelId', 'modelTitle');

        if ($actionOrderModel->load(Yii::$app->request->Post())) {
            if ($actionOrderModel->validate()) {
                $actionOrderModel->hasChange = 1;
                if ($actionOrderModel->save()) {
                    $actionOrderTable = \app\modules\workflow\models\ActionOrder::tableName();
                    $activityManagement = new \app\components\ActivityManagement();
                    $actionType = "Edit";
                    $subType = "EditActionOrder";
                    $subTypeDescription["TableName"] = $actionOrderTable;
                    $subTypeDescription["RecordPrimaryKey"] = $actionOrderModel->ID;
                    $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

                    return \app\components\Utility::renderPjaxCallBack('callbackActionOrder', [
                                'result' => 'true',
                                'message' => 'Action Order updated successfully.',
                    ]);
                } else {
                    return \app\components\Utility::renderPjaxCallBack('callbackActionOrder', [
                                'result' => 'false',
                                'message' => 'Error on saving data!',
                    ]);
                }
            }
        }

        return $this->renderAjax('_editActionOrdreForm', [
                    'actionOrderModel' => $actionOrderModel,
                    'arrLetter' => $arrLetter,
                    'arrDocumentModel' => $arrDocumentModel,
        ]);
    }

    public function actionParamInvoice($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamCashRequest($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamDebitmemo($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamContract($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamBudget($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamPermission($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamCapital($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamClaim($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamSourcing($pid, $aid) {
        return 'Under Constuction';
    }

    public function actionParamTender($pid, $aoid) {
        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ID' => $aoid])->one();
        $processId = $actionOrderModel->action->ProcessIDRef;
        if ($processId != $pid) {
            throw new \yii\web\HttpException(403, "Invalid Arguments");
        }

        $actionOrderParamTenderModel = new \app\modules\workflow\models\ActionOrderParamTender();
        $actionOrderParamTenderModel->find($aoid);

        if ($actionOrderParamTenderModel->load(Yii::$app->request->Post())) {
            if ($actionOrderParamTenderModel->validate()) {
                if ($actionOrderParamTenderModel->save($aoid)) {
                    $res = 'true*Action Order Param saved successfully.';
                } else {
                    $res = 'false*Is a problem on saving data!';
                }
            } else {
                $res = 'false*Is a problem on validating data!<br />';
            }
            return $res;
        }

        return $this->renderAjax('_actionOrderParamTenderForm', [
                    'actionOrderModel' => $actionOrderModel,
                    'actionOrderParamTenderModel' => $actionOrderParamTenderModel,
        ]);
    }

    public function actionDocumentRequiredType($pid, $aoid) {
        if ($pid == 69) {
//            $processModel = \app\modules\workflow\models\Process::find()->where(['ProcessID' => $pid])->one();
            $tenderDocumentRequireDocumentTypeSearchModel = new \app\modules\tender\models\TenderDocumentRequireDocumentTypeSearch;
            $tenderDocumentRequireDocumentTypeSearchModel->setScenario('search');
            $tenderDocumentRequireDocumentTypeProvider = $tenderDocumentRequireDocumentTypeSearchModel->search(Yii::$app->request->queryParams, $aoid);

            $tenderDocumentTypeArray = \yii\helpers\ArrayHelper::map(\app\modules\tender\models\TenderDocumentType::find()->asArray()->all(), 'TenderDocumentTypeID', 'TenderDocumentTypeTitle');
            return $this->renderAjax('_documentRequireTypeTender', [
                        'pid' => $pid,
                        'aoid' => $aoid,
                        'tenderDocumentTypeArray' => $tenderDocumentTypeArray,
                        'tenderDocumentRequireDocumentTypeSearchModel' => $tenderDocumentRequireDocumentTypeSearchModel,
                        'tenderDocumentRequireDocumentTypeProvider' => $tenderDocumentRequireDocumentTypeProvider,
            ]);
        }
    }

    public function actionMeasureList($pid, $aoid) {
        $actionOrderMeasureModelSearch = new \app\modules\workflow\models\ActionOrderMeasureSearch();
        $actionOrderMeasureModelSearch->setScenario('search');
        $actionOrderMeasureProvider = $actionOrderMeasureModelSearch->search(Yii::$app->request->queryParams, $aoid);

        $measureArray = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\Measure::find()->asArray()->all(), 'MeasureID', 'MeasureTitle');
//        var_dump($actionOrderMeasureProvider->query->asArray()->all());
//        die();
        return $this->renderAjax('_measureList', [
                    'pid' => $pid,
                    'aoid' => $aoid,
                    'measureArray' => $measureArray,
                    'actionOrderMeasureModelSearch' => $actionOrderMeasureModelSearch,
                    'actionOrderMeasureProvider' => $actionOrderMeasureProvider,
        ]);
    }

}
