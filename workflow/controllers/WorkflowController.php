<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;
use app\modules\account\models\User;
use app\components\CandoHelper;
use app\components\jDateTime;
use yii\data\ActiveDataProvider;
use app\modules\workflow\models\ProcessEntityStampDocumentSearch;
use yii\helpers\Html;
use app\modules\workflow\models\ProcessEntity;
use app\modules\workflow\models\ActionEntity;
use app\modules\workflow\models\Action;
use app\modules\document\models\StampDocument;
use app\modules\OBS\models\Obsnew;
use app\modules\OBS\models\ObsProcessAction;
use app\modules\account\models\UserObs;
use app\modules\automation\models\PositionType;
use app\modules\document\models\StampRefundWorkflow;

/**
 * @see WorkflowController
 *
 *
 * @author <mail:mortezakml@gmail.com & Tel:09366263397>
 */
class WorkflowController extends Controller {

    public $defaultAction = "cashrequest";
    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['graph', 'permission', 'sourcing', 'actual-progress', 'cashrequest', 'invoice', 'contract', 'workflowlist', 'process-action', 'budget', 'debitmemo', 'capital', 'warranty', 'process-detail', 'create-process-invoice', 'delete-process-invoice', 'invoice-process-contract-picker', 'process-select', 'claim', 'tender', 'contract-closing-document', 'automation', 'network', 'employee-settlement','employee-membership','foreign-mission','stamp-document','refund-stamp'],
                'rules' => [
                    // allow authenticated users
                    [
                        'actions' => ['invoice', 'permission', 'sourcing', 'actual-progress', 'contract', 'budget', 'debitmemo', 'capital', 'warranty', 'cashrequest', 'claim', 'tender', 'invoice-process-contract-picker', 'process-select', 'tender', 'contract-closing-document', 'automation', 'network', 'employee-settlement','employee-membership','foreign-mission','stamp-document','refund-stamp'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['graph'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            $peid = isset($exteraData['peid']) ? $exteraData['peid'] : Yii::$app->request->get('peid');
                            return \app\access\AccessControl::isAccess(['processEntity', 'peid' => $peid, 'type' => 'Graph']);
                        },
                    ],
                    [
                        'actions' => ['workflowlist'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            return \app\access\AccessControl::isAccess([
                                'or',
                                ['obs', 'check' => 'isCandoAdmin'],
                                ['obs', 'check' => 'isCompanyAdmin'],
                            ]);
                        },
                    ],
                    [
                        'actions' => ['process-detail', 'create-process-invoice', 'delete-process-invoice'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action, $exteraData = []) {
                            $pid = isset($exteraData['pid']) ? $exteraData['pid'] : Yii::$app->request->get('pid');
                            return \app\access\AccessControl::isAccess([
                                'process',
                                'pid' => $pid,
                                'type' => \app\access\ProcessAccess::PROCESS_VIEW_TYPE
                            ]);
                        }
                    ]
                // everything else is denied
                ],
            ],
        ];
    }

    public function actionCashrequest($type = 'ToDo') {
        $cashRequestSearchModel = new \app\modules\workflow\models\ProcessEntityCashRequestSearch();
        $cashRequestDataProvider = $cashRequestSearchModel->search(Yii::$app->request->queryParams, $type);
        $cashRequestDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        $uid = Yii::$app->session->get('UserID');
        $obsId = Yii::$app->session->get('ObsPosition');
        $userObsModel = \app\components\ActiveModel::find('\app\modules\account\models\UserObs')
                        ->where(['UserIDRef' => $uid, 'OBSCategory3IDRef' => $obsId])
                        ->andWhere("hasSignature>0")->one();

        if (!$userObsModel) {
            $hasSign = false;
        } else {
            $hasSign = true;
        }

        return $this->render('cashRequestToDoList', array(
                    'type' => $type,
                    'hasSign' => $hasSign,
                    'cashRequestSearchModel' => $cashRequestSearchModel,
                    'cashRequestDataProvider' => $cashRequestDataProvider,
        ));
    }

    public function actionWarranty($type = 'ToDo') {

        $model = new \app\modules\workflow\models\ProcessEntityWarrantySearch();
        $warrantyDataProvider = $model->search(Yii::$app->request->queryParams, $type);
        $warrantyDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];
        return $this->render('warrantyToDoList', array(
                    'model' => $model,
                    'type' => $type,
                    'warrantyDataProvider' => $warrantyDataProvider,
        ));
    }

    public function actionContract($type = 'ToDo') {
        $model = new \app\modules\workflow\models\ProcessEntityContractSearch();
        $model->setScenario('search');
        $contractDataProvider = $model->search(Yii::$app->request->queryParams, $type);
        $contractDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];
//        echo $contractDataProvider->query->createCommand()->rawSql;

        return $this->render('contractToDoList', array(
                    'model' => $model,
                    'type' => $type,
                    'contractDataProvider' => $contractDataProvider,
        ));
    }

    public function actionBudget($type = 'ToDo') {
        $budgetModel = new \app\modules\workflow\models\ProcessEntityBudgetSearch();
        $budgetDataProvider = $budgetModel->search(Yii::$app->request->queryParams, $type);
        $budgetDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        $budgetTable = \app\modules\budget\models\Budget::tableName();
        $obsnewTable = \app\modules\OBS\models\Obsnew::tableName();

        $departementQuery = \app\modules\workflow\models\ProcessEntity::find()->budget($type)
                        ->innerJoin("$obsnewTable as budgetObs", "budgetObs.obsId = $budgetTable.DepartementObsIDRef")
                        ->addSelect([
                            "budgetObs.obsId",
                            "budgetObs.obsTitle"
                        ])
                        ->addGroupBy("budgetObs.obsId")
                        ->asArray()->all();
        $departementArray = yii\helpers\ArrayHelper::map($departementQuery, 'obsId', 'obsTitle');
//        $departementArray = null;
        $costTypeArray = \app\modules\budget\models\Plan::costTypeArray();
        return $this->render('budgetToDoList', array(
                    'budgetModel' => $budgetModel,
                    'type' => $type,
                    'budgetDataProvider' => $budgetDataProvider,
                    'departementArray' => $departementArray,
                    'costTypeArray' => $costTypeArray,
        ));
    }

    public function actionInvoice($type = 'ToDo', $main = 0) {
        $invoiceSearchModel = new \app\modules\workflow\models\ProcessEntityInvoiceSearch();
        $invoiceDataProvider = $invoiceSearchModel->search(Yii::$app->request->queryParams, $type, $main);
        $invoiceDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];
        $mainContractUpdateList = \yii\helpers\ArrayHelper::getColumn(\app\modules\contract\models\Contract::find()
                                ->maincontract('update')->asArray()->all(), 'ContractID');

        return $this->render('invoiceToDoList', array(
                    'type' => $type,
                    'invoiceSearchModel' => $invoiceSearchModel,
                    'invoiceDataProvider' => $invoiceDataProvider,
                    'mainContractUpdateList' => $mainContractUpdateList,
        ));
    }

    public function actionPermission($type = 'ToDo') {

        $permissionSearchModel = new \app\modules\workflow\models\ProcessEntityPermissionSearch();
        $permissionDataProvider = $permissionSearchModel->search(Yii::$app->request->queryParams, $type);
        $permissionDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        return $this->render('permissionToDoList', array(
                    'type' => $type,
                    'permissionSearchModel' => $permissionSearchModel,
                    'permissionDataProvider' => $permissionDataProvider,
        ));
    }

    public function actionSourcing($type = 'ToDo') {

        $sourcingSearchModel = new \app\modules\workflow\models\ProcessEntitySourcingSearch();
        $sourcingSearchModel->setScenario('search');
        $sourcingDataProvider = $sourcingSearchModel->search(Yii::$app->request->queryParams, $type);
        $sourcingDataProvider->sort->defaultOrder = ['PersonID' => SORT_ASC];
//        var_dump("88");
//        die();
        return $this->render('sourcingToDoList', array(
                    'type' => $type,
                    'sourcingSearchModel' => $sourcingSearchModel,
                    'sourcingDataProvider' => $sourcingDataProvider,
        ));
    }

    public function actionDebitmemo($type = 'ToDo') {
        $debitmemoSearchModel = new \app\modules\workflow\models\ProcessEntityDebitmemoSearch();
        $debitmemoDataProvider = $debitmemoSearchModel->search(Yii::$app->request->queryParams, $type);
        $debitmemoDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        $helpTable = \app\modules\management\models\Help::tableName();
        $helpModel = \app\modules\management\models\Help::find()
                ->select(['Description'])
                ->where("$helpTable.HelpType='DebitMemo-ToDo'")
                ->one();

        return $this->render('debitmemoToDoList', array(
                    'type' => $type,
                    'debitmemoSearchModel' => $debitmemoSearchModel,
                    'debitmemoDataProvider' => $debitmemoDataProvider,
                    'help' => $helpModel["Description"],
        ));
    }

    public function actionCapital($type = 'ToDo') {
        $capitalSearchModel = new \app\modules\workflow\models\ProcessEntityCapitalSearch();
        $capitalSearchModel->setScenario('search');
        $capitalDataProvider = $capitalSearchModel->search(Yii::$app->request->queryParams, $type);
        $capitalDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

//        echo $capitalDataProvider->query->createCommand()->rawSql;
//        die();

        return $this->render('capitalToDoList', array(
                    'capitalSearchModel' => $capitalSearchModel,
                    'type' => $type,
                    'capitalDataProvider' => $capitalDataProvider,
        ));
    }

    public function actionClaim($type = 'ToDo') {
        $claimSearchModel = new \app\modules\workflow\models\ProcessEntityClaimSearch();
        $claimDataProvider = $claimSearchModel->search(Yii::$app->request->queryParams, $type);
        $claimDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        return $this->render('claimToDoList', [
                    'type' => $type,
                    'claimSearchModel' => $claimSearchModel,
                    'claimDataProvider' => $claimDataProvider,
        ]);
    }

    public function actionTender($type = 'ToDo') {
        $tenderModel = new \app\modules\tender\models\Tender();
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
                ->where(['in', 'PersonID', \app\modules\tender\models\Tender::find()->select('companyIdRef')->distinct()]);

        $companyList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');
        $companyRank = \app\modules\OBS\models\CompanyRank::find()->where(['companyIdRef' => Yii::$app->session->get('CompanyID'), 'status' => 1])->one();
        $tenderSearchModel = new \app\modules\workflow\models\ProcessEntityTenderSearch();
        $tenderDataProvider = $tenderSearchModel->search(Yii::$app->request->queryParams, $type);
        $tenderDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];
        if (!$companyRank) {
            $hasRank = false;
        } else {
            $hasRank = true;
        }
        return $this->render('tenderToDoList', [
                    'type' => $type,
                    'tenderModel' => $tenderModel,
                    'companyList' => $companyList,
                    'hasRank' => $hasRank,
                    'tenderSearchModel' => $tenderSearchModel,
                    'tenderDataProvider' => $tenderDataProvider,
        ]);
    }

    public function actionContractClosingDocument($type = 'ToDo') {
        $documentTypeQuery = \app\modules\contract\models\ContractDocumentType::find()->select(['ContractDocumentTypeID', 'ContractDocumentTypeTitle'])->asArray()->all();

        $documentTypeList = \yii\helpers\ArrayHelper::map($documentTypeQuery, 'ContractDocumentTypeID', 'ContractDocumentTypeTitle');

        $contractClosingDocumentSearchModel = new \app\modules\workflow\models\ProcessEntityContractClosingDocumentSearch();
        $contractClosingDocumentDataProvider = $contractClosingDocumentSearchModel->search(Yii::$app->request->queryParams, $type);
        $contractClosingDocumentDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        return $this->render('contractClosingDocumentToDoList', [
                    'type' => $type,
                    'documentTypeList' => $documentTypeList,
                    'contractClosingDocumentSearchModel' => $contractClosingDocumentSearchModel,
                    'contractClosingDocumentDataProvider' => $contractClosingDocumentDataProvider,
        ]);
    }

    public function actionAutomation($type = 'ToDo', $automation) {
//        $automationType = Yii::$app->request->get('automation');
//        $automationType = Yii::$app->request->getQueryParams();
//        var_dump('666');
//        var_dump($automationType);
//        die();
        $automationModel = new \app\modules\automation\models\Automation();
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
                ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);

        $companyList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');

        $automationSearchModel = new \app\modules\workflow\models\ProcessEntityAutomationSearch();
        $automationDataProvider = $automationSearchModel->search(Yii::$app->request->queryParams, $type, $automation);
        $automationDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];
//        echo  $automationDataProvider->query->createCommand()->rawSql;
//        die();

        return $this->render('automationToDoList', [
                    'type' => $type,
                    'automationType' => $automation,
                    'automationModel' => $automationModel,
                    'companyList' => $companyList,
                    'automationSearchModel' => $automationSearchModel,
                    'automationDataProvider' => $automationDataProvider,
        ]);
    }

    public function actionEmployeeSettlement($type = 'ToDo') {
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
                ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);

        $personList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');
        $companyList = \yii\helpers\ArrayHelper::map(\app\modules\OBS\models\Company::find()->asArray()->all(), 'companyIdRef', 'shortTitle');

        $employeeSettlementSearchModel = new \app\modules\workflow\models\ProcessEntityEmployeeSettlementSearch();
        $employeeSettlementDataProvider = $employeeSettlementSearchModel->search(Yii::$app->request->queryParams, $type);
        $employeeSettlementDataProvider->sort->defaultOrder = ['ObjectIDRef' => SORT_DESC];
        return $this->render('employeeSettlementToDoList', [
                    'type' => $type,
                    'personList' => $personList,
                    'companyList' => $companyList,
                    'employeeSettlementSearchModel' => $employeeSettlementSearchModel,
                    'employeeSettlementDataProvider' => $employeeSettlementDataProvider,
        ]);
    }

    public function actionEmployeeMembership($type = 'ToDo'){
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
            ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);

        $personList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');
        $companyList = \yii\helpers\ArrayHelper::map(\app\modules\OBS\models\Company::find()->asArray()->all(), 'companyIdRef', 'shortTitle');

        $employeeMembershipSearchModel = new \app\modules\workflow\models\ProcessEntityEmployeeMembershipSearch();
        $employeeMembershipDataProvider = $employeeMembershipSearchModel->search(Yii::$app->request->queryParams, $type);
        $employeeMembershipDataProvider->sort->defaultOrder = ['ObjectIDRef' => SORT_DESC];

        return $this->render('employeeMembershipToDoList', [
            'type' => $type,
            'personList' => $personList,
            'companyList' => $companyList,
            'employeeMembershipSearchModel' => $employeeMembershipSearchModel,
            'employeeMembershipDataProvider' => $employeeMembershipDataProvider,
        ]);
    }

    public function actionForeignMission($type = 'ToDo'){
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
            ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);

        $personList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');
        $companyList = \yii\helpers\ArrayHelper::map(\app\modules\OBS\models\Company::find()->asArray()->all(), 'companyIdRef', 'shortTitle');

        $foreignMissionSearchModel = new \app\modules\workflow\models\ProcessEntityForeignMissionSearch();
        $foreignMissionDataProvider = $foreignMissionSearchModel->search(Yii::$app->request->queryParams, $type);
        $foreignMissionDataProvider->sort->defaultOrder = ['ObjectIDRef' => SORT_DESC];

        return $this->render('foreignMissionToDoList', [
            'type' => $type,
            'personList' => $personList,
            'companyList' => $companyList,
            'foreignMissionSearchModel' => $foreignMissionSearchModel,
            'foreignMissionDataProvider' => $foreignMissionDataProvider,
        ]);
    }
    public function actionStampDocument($type = 'ToDo')
    {
        // آماده‌سازی لیست شرکت‌ها و اشخاص (مشابه ForeignMission)
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
            ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);

        $personList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');
        $companyList = \yii\helpers\ArrayHelper::map(\app\modules\OBS\models\Company::find()->asArray()->all(), 'companyIdRef', 'shortTitle');

        // استفاده از مدل جستجوی جدید ما
        $stampDocumentSearchModel = new ProcessEntityStampDocumentSearch();
        $stampDocumentDataProvider = $stampDocumentSearchModel->search(Yii::$app->request->queryParams, $type);
        $stampDocumentDataProvider->sort->defaultOrder = ['StampId' => SORT_DESC]; // مرتب‌سازی پیش‌فرض بر اساس StampId نزولی

        return $this->render('stampDocumentToDoList', [ // ✅ رندر کردن ویو جدید
            'type' => $type,
            'personList' => $personList, // ارسال به ویو
            'companyList' => $companyList, // ارسال به ویو
            'stampDocumentSearchModel' => $stampDocumentSearchModel,
            'stampDocumentDataProvider' => $stampDocumentDataProvider,
        ]);
    }
    public function actionRefundStamp($type = 'ToDo')
{
    $companyQuery = \app\modules\OBS\models\Person::find()
        ->select(['PersonID', 'companyName'])
        ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);
    $personList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');
    $companyList = \yii\helpers\ArrayHelper::map(
        \app\modules\OBS\models\Company::find()->asArray()->all(),
        'companyIdRef',
        'shortTitle'
    );
    $refundSearchModel = new \app\modules\workflow\models\ProcessEntityRefundStampSearch();
    $refundDataProvider = $refundSearchModel->search(Yii::$app->request->queryParams, $type);
    $refundDataProvider->sort->defaultOrder = ['ProcessEntityID' => SORT_DESC];
    return $this->render('refundStampToDoList', [
        'type' => $type,
        'personList' => $personList,
        'companyList' => $companyList,
        'refundSearchModel' => $refundSearchModel,
        'refundDataProvider' => $refundDataProvider,
    ]);
}
    public function actionActualProgress($type = 'ToDo') {
        $actualProgressSearchModel = new \app\modules\workflow\models\ProcessEntityActualProgressSearch();
        $actualProgressDataProvider = $actualProgressSearchModel->search(Yii::$app->request->queryParams, $type);
        $actualProgressDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        $arrayMouth = [
            1 => "فروردین",
            2 => "اردیبهشت",
            3 => "خرداد",
            4 => "تیر",
            5 => "مرداد",
            6 => "شهریور",
            7 => "مهر",
            8 => "آبان",
            9 => "آذر",
            10 => "دی",
            11 => "بهمن",
            12 => "اسفند",
        ];

        return $this->render('actualProgressToDoList', [
                    'type' => $type,
                    'actualProgressSearchModel' => $actualProgressSearchModel,
                    'actualProgressDataProvider' => $actualProgressDataProvider,
                    'arrayMouth' => $arrayMouth,
        ]);
    }

    public function actionGraph($peid) {
        $processEntityModel = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->where(['ProcessEntityID' => $peid])->one();
        $userTable = \app\modules\account\models\User::tableName();

        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $actionTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $userTable = User::tableName();
        if (!($processEntityModel->parentProcessEntityIDRef > 0)) {
            $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')->joinWith(['action'])
                            ->leftJoin($userTable, "$userTable.UserID=$actionEntityTable.UserIDRef")
                            ->leftJoin($obsTable, "$obsTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                            ->where(['IN', 'ProcessEntityIDRef', (new \yii\db\Query())
                                ->select(['ProcessEntityId'])
                                ->from([\app\modules\workflow\models\ProcessEntity::tableName()])
                                ->where(['ProcessEntityId' => $peid])
                                ->orWhere(['parentProcessEntityIdRef' => $peid])])
                            ->select(["$actionEntityTable.ID", 'isEndAction', 'ActionDate', 'ActionDateHijri', 'toActionDate', 'toActionDateHijri', "$actionTable.ActionTitle2", 'ActionIDRef', 'UserName' => "concat($userTable.FirstName, ' ', $userTable.LastName)", 'OBSCategory3Title' => $obsTable . '.obsTitle', 'ActionMessage', 'ActionAutoMessage', 'LetterNumber'])
//                ->orderBy(['ActionDateHijri' => SORT_ASC])->asArray()->all();
                            ->orderBy(["$actionEntityTable.ID" => SORT_ASC])->asArray()->all();
        } else {
            $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')->joinWith(['action'])
                            ->leftJoin($userTable, "$userTable.UserID=$actionEntityTable.UserIDRef")
                            ->leftJoin($obsTable, "$obsTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                            ->where(['IN', 'ProcessEntityIDRef', (new \yii\db\Query())
                                ->select(['ProcessEntityId'])
                                ->from([\app\modules\workflow\models\ProcessEntity::tableName()])
                                ->where(['ProcessEntityId' => $processEntityModel->parentProcessEntityIDRef])
                                ->orWhere(['parentProcessEntityIdRef' => $processEntityModel->parentProcessEntityIDRef])])
                            ->select(["$actionEntityTable.ID", 'isEndAction', 'ActionDate', 'ActionDateHijri', "$actionTable.ActionTitle2", 'ActionIDRef', 'UserName' => "concat($userTable.FirstName, ' ', $userTable.LastName)", 'OBSCategory3Title' => $obsTable . '.obsTitle', 'ActionMessage', 'ActionAutoMessage', 'LetterNumber'])
//                ->orderBy(['ActionDateHijri' => SORT_ASC])->asArray()->all();
                            ->orderBy(["$actionEntityTable.ID" => SORT_ASC])->asArray()->all();
        }

//      محاسبه زمان گردش اکشن های ورکفلو
        $sum_month = $sum_day = $sum_hore = $sum_minut = 0;
        $hore = $minut = '00';
        $jDate = new jDateTime(true, true, 'Asia/Tehran');
        for ($i = 0; $i < count($actionEntityData) - 1; $i++) {
            $actionEntity1 = $actionEntityData[$i];
            $actionEntity2 = $actionEntityData[$i + 1];
            $duration = "";
            if (!($actionEntity1['isEndAction'] == 1) && $actionEntity1['ActionDateHijri'] != null && $actionEntity2['ActionDateHijri'] != null) {
                $yer1 = intval(substr($actionEntity1['ActionDateHijri'], 0, 4));
                $month1 = intval(substr($actionEntity1['ActionDateHijri'], 5, 2));
                $day1 = intval(substr($actionEntity1['ActionDateHijri'], 8, 2));
                $yer2 = intval(substr($actionEntity2['ActionDateHijri'], 0, 4));
                $month2 = intval(substr($actionEntity2['ActionDateHijri'], 5, 2));
                $day2 = intval(substr($actionEntity2['ActionDateHijri'], 8, 2));
                $timeBetween = $jDate->timeBetweenDate($yer1, $month1, $day1, $yer2, $month2, $day2);
                if ($timeBetween[0] > 0) {
                    $duration = $duration . $timeBetween[0] . ' month ';
                    $sum_month += $timeBetween[0];
                }
                if ($timeBetween[1] > 0) {
                    $duration = $duration . $timeBetween[1] . ' day ';
                    $sum_day += $timeBetween[1];
                }
                if ((strlen($actionEntity1['ActionDateHijri']) >= 15) && (strlen($actionEntity2['ActionDateHijri']) >= 15)) {
                    $firstTimeString = substr($actionEntity1['ActionDateHijri'], -4);
                    $firstTimeString = substr($firstTimeString, 0, 2) . ':' . substr($firstTimeString, 2) . ':00';
                    $secondTimeString = substr($actionEntity2['ActionDateHijri'], -4);
                    $secondTimeString = substr($secondTimeString, 0, 2) . ':' . substr($secondTimeString, 2) . ':00';
                    $firstTime = new \DateTime($firstTimeString);
                    $secondTime = new \DateTime($secondTimeString);
                    $interval2 = $firstTime->diff($secondTime);
                    $hore = ($interval2->h >= 10) ? $interval2->h : '0' . $interval2->h;
                    $minut = ($interval2->i >= 10) ? $interval2->i : '0' . $interval2->i;
                    $duration .= $hore . ':' . $minut;
                    $sum_hore += $interval2->h;
                    $sum_minut += $interval2->i;
                } else {
                    $duration .= ' 00:00';
                }
                $actionEntityData[$i]['Duration'] = $duration;
            }
        }

        if (!isset($actionEntity2)) {
            $actionEntity2 = $actionEntityData[0];
        }

        $duration = "";
        if (!($actionEntity2['isEndAction'] == 1) && $actionEntity2['ActionDateHijri'] != null && $processEntityModel->process->ProccessTypeIDRef != \app\modules\workflow\models\Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
            $currentDateHijri = $jDate->date("Y/m/d-hi", false, false); // SHAMSI
            $yer1 = intval(substr($actionEntity2['ActionDateHijri'], 0, 4));
            $month1 = intval(substr($actionEntity2['ActionDateHijri'], 5, 2));
            $day1 = intval(substr($actionEntity2['ActionDateHijri'], 8, 2));
            $yer2 = intval(substr($currentDateHijri, 0, 4));
            $month2 = intval(substr($currentDateHijri, 5, 2));
            $day2 = intval(substr($currentDateHijri, 8, 2));

            $timeBetween = $jDate->timeBetweenDate($yer1, $month1, $day1, $yer2, $month2, $day2);
            if ($timeBetween[0] > 0) {
                $duration = $duration . $timeBetween[0] . ' month ';
                $sum_month += $timeBetween[0];
            }
            if ($timeBetween[1] > 0) {
                $duration = $duration . $timeBetween[1] . ' day ';
                $sum_day += $timeBetween[1];
            }
            if ((strlen($actionEntity2['ActionDateHijri']) >= 15)) {
                $firstTimeString = substr($actionEntity2['ActionDateHijri'], -4);
                $firstTimeString = substr($firstTimeString, 0, 2) . ':' . substr($firstTimeString, 2);
                $firstTime = new \DateTime($firstTimeString);
                $currentTimeString = $jDate->date("H:i:s", false, false); // SHAMSI
                $currentTime = new \DateTime($currentTimeString);
                $interval2 = $firstTime->diff($currentTime);
                $hore = ($interval2->h >= 10) ? $interval2->h : '0' . $interval2->h;
                $minut = ($interval2->i >= 10) ? $interval2->i : '0' . $interval2->i;
                $duration = $duration . $hore . ':' . $minut;
                $sum_hore += $interval2->h;
                $sum_minut += $interval2->i;
            } else {
                $duration .= ' 00:00';
            }
            $actionEntityData[$i]['Duration'] = $duration;
        }

        $sum_hore += $sum_minut / 60;
        $sum_minut = $sum_minut % 60;

        $sum_day += $sum_hore / 24;
        $sum_hore = $sum_hore % 24;

        $sum_day += $sum_month * 30;
        ($sum_day >= 30) ? $sum_month = floor($sum_day / 30) : 0;
        $sum_day = $sum_day % 30;

        $sum_duration = "Total Duration: ";
        if ($sum_month > 0) {
            $sum_duration = $sum_duration . $sum_month . ' month ';
        }
        if ($sum_day > 0) {
            $sum_duration = $sum_duration . $sum_day . ' day ';
        }
        $sum_duration = strval($sum_duration . $sum_hore . ':' . $sum_minut);

        $strActionIdList = '';
        foreach ($actionEntityData as $item) {
            $strActionIdList .= ',' . $item['ActionIDRef'];
        }
        $jsonActions = \yii\helpers\Json::encode($actionEntityData);
        // Select Gragh FileID  
        $process = isset($processEntityModel->process->parentProcessIdRef) ? $processEntityModel->process->parentProcessIdRef : $processEntityModel->ProcessIDRef;

        // graph header info 
        $gInfo = \app\modules\workflow\models\ProcessEntity::graphHeaderInfo($peid);

        foreach ($gInfo as $k => $v) {
            if (\app\components\Utility::isJson($v)) {
                $data = json_decode($v);
                $gInfo['Sub Contractor'] = $data->SubContractorPersonTitle;
            }
        }

        if ($processEntityModel->process->ProccessTypeIDRef != \app\modules\workflow\models\Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
            for ($i = 0; $i < count($actionEntityData) - 1; $i++) {
                $actionEntityData[$i]['UserName'] = $actionEntityData[$i + 1]['UserName'];
                $actionEntityData[$i]['ActionMessage'] = $actionEntityData[$i + 1]['ActionMessage'];
                $actionEntityData[$i]['ActionAutoMessage'] = $actionEntityData[$i + 1]['ActionAutoMessage'];
                $actionEntityData[$i]['ActionDateHijri'] = $actionEntityData[$i + 1]['ActionDateHijri'];
            }
            $actionEntityData[$i]['UserName'] = null;
            $actionEntityData[$i]['ActionMessage'] = null;
            $actionEntityData[$i]['ActionAutoMessage'] = null;
            $actionEntityData[$i]['ActionDateHijri'] = null;
        } else {
            for ($i = count($actionEntityData)-1 ; $i > 0; $i--) {
                $actionEntityData[$i]['Duration'] = $actionEntityData[$i - 1]['Duration'];
            }
            $actionEntityData[0]['Duration'] = '00:00';
        }

        $actionEntityProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $actionEntityData,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => ['ActionDateHijri'],
            ],
        ]);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('graph', array(
                        'data' => $strActionIdList,
                        'jsonActions' => $jsonActions,
                        'gInfo' => $gInfo,
                        'processEntityModel' => $processEntityModel,
                        'mode' => 'ajax',
                        'actionEntityProvider' => $actionEntityProvider,
            ));
        }

        return $this->render('graph', array(
                    'sumDuration' => $sum_duration,
                    'graphId' => $process,
                    'data' => $strActionIdList,
                    'jsonActions' => $jsonActions,
                    'gInfo' => $gInfo,
                    'processEntityModel' => $processEntityModel,
                    'mode' => 'normal',
                    'actionEntityProvider' => $actionEntityProvider,
        ));
    }

    public function actionGraph2($peid) {
        $processEntityModel = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->where(['ProcessEntityID' => $peid])->one();
        $userTable = \app\modules\account\models\User::tableName();

        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        if (!($processEntityModel->parentProcessEntityIDRef > 0)) {
            $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')->joinWith(['action'])
                            ->leftJoin(CandoHelper::braketTable(User::tableName()), CandoHelper::braketTable(User::tableName()) . '.UserID=' . \app\modules\workflow\models\ActionEntity::tableName() . '.UserIDRef')
                            ->leftJoin($obsTable, $obsTable . '.obsId=' . \app\modules\workflow\models\ActionEntity::tableName() . '.OBSCategory3IDRef')
                            ->where(['IN', 'ProcessEntityIDRef', (new \yii\db\Query())
                                ->select(['ProcessEntityId'])
                                ->from([\app\modules\workflow\models\ProcessEntity::tableName()])
                                ->where(['ProcessEntityId' => $peid])
                                ->orWhere(['parentProcessEntityIdRef' => $peid])])
                            ->select(['isEndAction', 'ActionDate', 'ActionDateHijri', \app\modules\workflow\models\Action::tableName() . '.ActionTitle2', 'ActionIDRef', 'UserName' => "concat($userTable.FirstName, ' ', $userTable.LastName)", 'OBSCategory3Title' => $obsTable . '.obsTitle', 'ActionMessage', 'ActionAutoMessage', 'LetterNumber'])
                            ->orderBy(['ActionDateHijri' => SORT_ASC])->asArray()->all();
        } else {
            $actionEntityData = \app\components\ActiveModel::find('\app\modules\workflow\models\ActionEntity')->joinWith(['action'])
                            ->leftJoin(CandoHelper::braketTable(User::tableName()), CandoHelper::braketTable(User::tableName()) . '.UserID=' . \app\modules\workflow\models\ActionEntity::tableName() . '.UserIDRef')
                            ->leftJoin($obsTable, $obsTable . '.obsId=' . \app\modules\workflow\models\ActionEntity::tableName() . '.OBSCategory3IDRef')
                            ->where(['IN', 'ProcessEntityIDRef', (new \yii\db\Query())
                                ->select(['ProcessEntityId'])
                                ->from([\app\modules\workflow\models\ProcessEntity::tableName()])
                                ->where(['ProcessEntityId' => $processEntityModel->parentProcessEntityIDRef])
                                ->orWhere(['parentProcessEntityIdRef' => $processEntityModel->parentProcessEntityIDRef])])
                            ->select(['isEndAction', 'ActionDate', 'ActionDateHijri', \app\modules\workflow\models\Action::tableName() . '.ActionTitle2', 'ActionIDRef', 'UserName' => "concat($userTable.FirstName, ' ', $userTable.LastName)", 'OBSCategory3Title' => $obsTable . '.obsTitle', 'ActionMessage', 'ActionAutoMessage', 'LetterNumber'])
                            ->orderBy(['ActionDateHijri' => SORT_ASC])->asArray()->all();
        }

//      محاسبه زمان گردش اکشن های ورکفلو
//        $finalActions = [897];
        $sum_month = $sum_day = $sum_hore = $sum_minut = 0;
        for ($i = 0; $i < count($actionEntityData) - 1; $i++) {
            $actionEntity1 = $actionEntityData[$i];
            if (!($actionEntity1['isEndAction'] == 1)) {
                $actionEntity2 = $actionEntityData[$i + 1];
                $firstDate = new \DateTime($actionEntity1['ActionDate']);
                $secondDate = new \DateTime($actionEntity2['ActionDate']);
                $interval = $firstDate->diff($secondDate);
                $firstTimeString = substr($actionEntity1['ActionDateHijri'], -4);
                $firstTimeString = substr($firstTimeString, 0, 2) . ':' . substr($firstTimeString, 2) . ':00';
                $secondTimeString = substr($actionEntity2['ActionDateHijri'], -4);
                $secondTimeString = substr($secondTimeString, 0, 2) . ':' . substr($secondTimeString, 2) . ':00';
                $firstTime = new \DateTime($firstTimeString);
                $secondTime = new \DateTime($secondTimeString);
                $interval2 = $firstTime->diff($secondTime);
                $hore = ($interval2->h >= 10) ? $interval2->h : '0' . $interval2->h;
                $minut = ($interval2->i >= 10) ? $interval2->i : '0' . $interval2->i;
                $sum_hore += $hore;
                $sum_minut += $minut;
                $duration = "";
                if ($interval->m > 0) {
                    $duration = $duration . $interval->m . ' month ';
                    $sum_month += $interval->m;
                }
                if ($interval->d > 0) {
                    $duration = $duration . $interval->d . ' day ';
                    $sum_day += $interval->d;
                }
                $duration = $duration . $hore . ':' . $minut . ':00';

                $actionEntityData[$i]['Duration'] = $duration;
            }
        }

        $duration = "";
        if (!($actionEntity2['isEndAction'] == 1)) {
            $jDate = new jDateTime(true, true, 'Asia/Tehran');
            $currentTime = $jDate->date("H:i:s", false, false); // SHAMSI
            $currentDate = $jDate->date("Y-m-d", false, false, false);  // MILADI
            $currentTime = new \DateTime($currentTime);
            $currentDate = new \DateTime($currentDate);
            $interval = $secondDate->diff($currentDate);
            $interval2 = $secondTime->diff($currentTime);

            $hore = ($interval2->h >= 10) ? $interval2->h : '0' . $interval2->h;
            $minut = ($interval2->i >= 10) ? $interval2->i : '0' . $interval2->i;
            if ($interval->m > 0) {
                $duration = $duration . $interval->m . ' month ';
            }
            if ($interval->d > 0) {
                $duration = $duration . $interval->d . ' day ';
            }
            $duration = $duration . $hore . ':' . $minut . ':00';
        }
        $actionEntityData[$i]['Duration'] = $duration;

        $sum_hore += $sum_minut / 60;
        $sum_minut = $sum_minut % 60;

        $sum_day += $sum_hore / 24;
        $sum_hore = $sum_hore % 24;

        $sum_day += $sum_month * 30;
        ($sum_day >= 30) ? $sum_month = floor($sum_day / 30) : 0;
        $sum_day = $sum_day % 30;

        $sum_duration = "Total Duration: ";
        if ($sum_month > 0) {
            $sum_duration = $sum_duration . $sum_month . ' month ';
        }
        if ($sum_day > 0) {
            $sum_duration = $sum_duration . $sum_day . ' day ';
        }
        $sum_duration = $sum_duration . $hore . ':' . $minut . ':00';
        $sum_duration = strval($sum_duration);
//        $sum_duration = $sum_month . ' month ' . $sum_day . ' day ' . $hore . ':' . $minut . ':00';

        $strActionIdList = '';
        foreach ($actionEntityData as $item) {
            $strActionIdList .= ',' . $item['ActionIDRef'];
        }
        $jsonActions = \yii\helpers\Json::encode($actionEntityData);
        // Select Gragh FileID  
        $process = isset($processEntityModel->process->parentProcessIdRef) ? $processEntityModel->process->parentProcessIdRef : $processEntityModel->ProcessIDRef;

        // graph header info 
        $gInfo = \app\modules\workflow\models\ProcessEntity::graphHeaderInfo($peid);

        foreach ($gInfo as $k => $v) {
            if (\app\components\Utility::isJson($v)) {
                $data = json_decode($v);
                $gInfo['Sub Contractor'] = $data->SubContractorPersonTitle;
            }
        }

        $actionEntityProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $actionEntityData,
            'pagination' => [
                'pageSize' => 50,
            ],
            'sort' => [
                'attributes' => ['ActionDateHijri'],
            ],
        ]);

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('graph', array(
                        'data' => $strActionIdList,
                        'jsonActions' => $jsonActions,
                        'gInfo' => $gInfo,
                        'processEntityModel' => $processEntityModel,
                        'mode' => 'ajax',
                        'actionEntityProvider' => $actionEntityProvider,
            ));
        }

        return $this->render('graph', array(
                    'sumDuration' => $sum_duration,
                    'graphId' => $process,
                    'data' => $strActionIdList,
                    'jsonActions' => $jsonActions,
                    'gInfo' => $gInfo,
                    'processEntityModel' => $processEntityModel,
                    'mode' => 'normal',
                    'actionEntityProvider' => $actionEntityProvider,
        ));
    }

    public function actionWorkflowlist() {

        $processModel = new \app\modules\workflow\models\ProcessSearch();
        $processModel->setScenario('search');
        $processDataProvider = $processModel->search(Yii::$app->request->queryParams);

        return $this->render('processList', [
                    'processDataProvider' => $processDataProvider,
                    'processModel' => $processModel,
        ]);
    }
    //اکشن انتخاب فرایند برای شروع یک کار
    public function actionProcessSelect($type) {
        if (Yii::$app->request->isAjax) {
            $data = \app\modules\workflow\models\Process::find()->newProcessEntity($type)->all();
            return $this->renderPartial('_processSelect', [
                        'type' => $type,
                        'data' => $data,
                            ], False, True);
        } else {
            throw new \yii\web\BadRequestHttpException();
        }
    }
    //@TODO : مهندس فرزام لطفا یه نگاهی به این اکشن جدید بندازید
    public function actionProcessDetail($pid) {
        $contractInvoiceTypeProcessModelSearch = new \app\modules\workflow\models\ContractInvoiceTypeProcessSearch();
        $contractInvoiceTypeProcessModelSearch->setScenario('search');
        $contractInvoiceTypeDataProvider = $contractInvoiceTypeProcessModelSearch->search(Yii::$app->request->queryParams, $pid);

        $processModel = \app\components\ActiveModel::find('\app\modules\workflow\models\Process')->where(['ProcessID' => $pid])->one();
//        $processModel = \app\modules\workflow\models\Process::find()->where(['ProcessID' => $pid])->one();
        return $this->render('processDetail', [
                    'pid' => $pid,
                    'contractInvoiceTypeDataProvider' => $contractInvoiceTypeDataProvider,
                    'contractInvoiceTypeProcessModelSearch' => $contractInvoiceTypeProcessModelSearch,
                    'processModel' => $processModel,
        ]);
    }

    public function actionCreateProcessInvoice($pid) {
        // new model 
        $contractInvoiceTypeProcessModel = new \app\modules\workflow\models\ContractInvoiceTypeProcess();
        // invoice type array
        $arrInvoiceTypes = \yii\helpers\ArrayHelper::map(\app\modules\invoice\models\InvoiceTypes::find()
                                ->asArray()
                                ->all(), 'InvoiceTypeID', 'InvoiceType');

        // process model
        $processModel = \app\modules\workflow\models\Process::find()
                ->where(['ProcessID' => $pid])
                ->one();

        //contract list
        $subcontractorData = \app\modules\contract\models\ContractSearch::searchSelect2Picker(Yii::$app->request->queryParams, 'def');
        $subcontractorArray = [];
        foreach ($subcontractorData as $item) {
            $subcontractorArray[count($subcontractorArray)] = ['ContractID' => $item['ContractID'],
                'ContractDesc' => $item['ContractNo'] . '|' . $item['PersonTitle'] . '|' . $item['ContractSubject'],
            ];
        }

        if ($contractInvoiceTypeProcessModel->load(Yii::$app->request->Post())) {
            $transaction = Yii::$app->dbTehran->beginTransaction();
            try {
                $contractInvoiceTypeProcessTable = \app\modules\workflow\models\ContractInvoiceTypeProcess::tableName();
                $contractInvoiceTypeProcessQuery = (new \yii\db\Query())
                        ->select(["strIds" => "GROUP_CONCAT(ID)"])
                        ->from($contractInvoiceTypeProcessTable)
                        ->where(['ContractIDRef' => $contractInvoiceTypeProcessModel->ContractIDRef, 'InvoiceTypeIDRef' => $contractInvoiceTypeProcessModel->InvoiceTypeIDRef])
                        ->andWhere("$contractInvoiceTypeProcessTable.isDeleted is null or $contractInvoiceTypeProcessTable.isDeleted <> 1")
                        ->one();
                $contractInvoiceTypeProcessIds = $contractInvoiceTypeProcessQuery['strIds'];

                Yii::$app->dbTehran->createCommand()->update(\app\modules\workflow\models\ContractInvoiceTypeProcess::tableName(), [
                            'hasChange' => 1,
                            'isDeleted' => 1,
                                ], "ContractIDRef=$contractInvoiceTypeProcessModel->ContractIDRef and InvoiceTypeIDRef=$contractInvoiceTypeProcessModel->InvoiceTypeIDRef and (isDeleted is null or isDeleted<>1)")
                        ->execute();

                $contractInvoiceTypeProcessModel->ProcessIDRef = $pid;
                $contractInvoiceTypeProcessModel->hasChange = 1;
                if ($contractInvoiceTypeProcessModel->validate()) {
                    if ($contractInvoiceTypeProcessModel->save()) {

                        $activityManagement = new \app\components\ActivityManagement();
                        $actionType = "New";
                        $subType = "NewContractInvoiceTypeProcess";
                        $subTypeDescription["TableName"] = $contractInvoiceTypeProcessTable;
                        $subTypeDescription["RecordPrimaryKey"] = "$contractInvoiceTypeProcessModel->ID";
                        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

                        $activityManagement1 = new \app\components\ActivityManagement();
                        $actionType = "Delete";
                        $subType = "DeleteContractInvoiceTypeProcess";
                        $subTypeDescription["RecordPrimaryKey"] = "[$contractInvoiceTypeProcessIds]";
                        $activityManagement1->sendActitvityLog($actionType, $subType, $subTypeDescription);

                        $transaction->commit();
                        Yii::$app->replication->batchSynchChange('\app\modules\workflow\models\ContractInvoiceTypeProcess');
                        $res = 'true*Invoice Type added successfully.';
                    } else {
                        $transaction->rollback();
                        $res = 'false*Is a problem on saving data!';
                    }
                } else {
                    $res = 'false*Is a problem on validating data!';
                }
            } catch (\Exception $exc) {
                $transaction->rollBack();
                $res = 'false*Is a problem on saving data!';
            }
            return $res;
        }
        return $this->renderAjax('_processInvoiceForm', [
                    'contractInvoiceTypeProcessModel' => $contractInvoiceTypeProcessModel,
                    'arrInvoiceTypes' => $arrInvoiceTypes,
                    'processModel' => $processModel,
                    'subcontractorArray' => $subcontractorArray,
        ]);
    }

    public function actionNetwork($type = 'ToDo') {
        $networkModel = new \app\modules\automation\models\Network();
        $companyQuery = \app\modules\OBS\models\Person::find()->select(['PersonID', 'companyName'])
                ->where(['in', 'PersonID', \app\modules\OBS\models\Company::find()->select('companyIdRef')->distinct()]);

        $companyList = \yii\helpers\ArrayHelper::map($companyQuery->asArray()->all(), 'PersonID', 'companyName');

        $networkSearchModel = new \app\modules\workflow\models\ProcessEntityNetworkSearch();
        $networkDataProvider = $networkSearchModel->search(Yii::$app->request->queryParams, $type);
        $networkDataProvider->sort->defaultOrder = ['LastActionDateHijri' => SORT_DESC];

        return $this->render('networkToDoList', [
                    'type' => $type,
                    'networkModel' => $networkModel,
                    'companyList' => $companyList,
                    'networkSearchModel' => $networkSearchModel,
                    'networkDataProvider' => $networkDataProvider,
        ]);
    }

    public function actionCreateProcessInvoiceList($pid, $invoiceTypeID = 1) {
        // invoice type array
        $arrInvoiceTypes = \yii\helpers\ArrayHelper::map(\app\modules\invoice\models\InvoiceTypes::find()
                                ->asArray()
                                ->all(), 'InvoiceTypeID', 'InvoiceType');

        // process model
        /* $processModel = \app\modules\workflow\models\Process::find()
          ->where(['ProcessID' => $pid])
          ->one(); */

        //contract list
        $contractModelSearch = new \app\modules\contract\models\ContractSearch();
        $contractModelSearch->setScenario('search');
        $contractDataProvider = $contractModelSearch->searchProcessInvoice(Yii::$app->request->queryParams, $invoiceTypeID);

        return $this->renderAjax('_processInvoiceList', [
                    'arrInvoiceTypes' => $arrInvoiceTypes,
//            'processModel' => $processModel,
                    'contractModelSearch' => $contractModelSearch,
                    'contractDataProvider' => $contractDataProvider,
        ]);
    }

    public function actionDeleteProcessInvoice($keys) {
        $res = \app\modules\workflow\models\ContractInvoiceTypeProcess::updateAll([
                    'isDeleted' => 1,
                    'hasChange' => 1,
                        ], (($keys != '') ? "ID in ($keys) " : '') .
                        " and (isDeleted is null or isDeleted != 1)");

        if ($res) {
            $contractInvoiceTypeProcessTable = \app\modules\workflow\models\ContractInvoiceTypeProcess::tableName();
            $activityManagement = new \app\components\ActivityManagement();
            $actionType = "Delete";
            $subType = "DeleteContractInvoiceTypeProcess";
            $subTypeDescription["TableName"] = $contractInvoiceTypeProcessTable;
            $subTypeDescription["RecordPrimaryKey"] = "[$keys]";
            $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);

            Yii::$app->replication->batchSynchChange('\app\modules\workflow\models\ContractInvoiceTypeProcess');
            return 'true*Invoice Type has been deleted from process.';
        } else {
            return 'false*error onn deleting item!';
        }
    }

    function actionInvoiceProcessContractPicker() {
        $modelSearch = new \app\modules\contract\models\ContractSearchPickerPro();
        $modelSearch->setScenario('search');
        $modelDataProvider = $modelSearch->searchPickerPro(Yii::$app->request->queryParams, 'def');

        $layoutName = '_contractPickerList';
        return $this->renderAjax($layoutName, [
                    'modelSearch' => $modelSearch,
                    'modelDataProvider' => $modelDataProvider,
        ]);
    }
public function actionStampSummary($peid)
{
    $peid = \yii\helpers\Html::encode($peid);

    $processEntityModel = \app\modules\workflow\models\ProcessEntity::find()
        ->where(['ProcessEntityID' => $peid, 'isDeleted' => 0])
        ->one();

    if (!$processEntityModel) {
        throw new \yii\web\NotFoundHttpException('ProcessEntity not found');
    }

    $userTable = \app\modules\account\models\User::tableName();
    $obsTable = \app\modules\OBS\models\Obsnew::tableName();
    $actionTable = \app\modules\workflow\models\Action::tableName();
    $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();

    /**
     * منطق اصلی:
     * - برای رکورد شروع فرایند (FromActionIDRef = null)، عنوان مرحله از ActionIDRef گرفته می‌شود
     * - برای رکوردهای اقدام واقعی (FromActionIDRef != null)، عنوان مرحله از FromActionIDRef گرفته می‌شود
     *
     * پس باید دو بار به جدول action وصل شویم:
     * 1) actionCurrent  => برای ActionIDRef
     * 2) actionFrom     => برای FromActionIDRef
     */
    $actionCurrentAlias = 'actionCurrent';
    $actionFromAlias = 'actionFrom';

    $actionEntityData = (new \yii\db\Query())
        ->from(["ae" => $actionEntityTable])

        ->leftJoin(["u" => $userTable], "u.UserID = ae.UserIDRef")
        ->leftJoin(["o" => $obsTable], "o.obsId = ae.OBSCategory3IDRef")

        // عنوان مرحله مقصد / مرحله جاری رکورد
        ->leftJoin(["{$actionCurrentAlias}" => $actionTable], "{$actionCurrentAlias}.ActionID = ae.ActionIDRef")

        // عنوان مرحله‌ای که واقعاً روی آن اقدام انجام شده
        ->leftJoin(["{$actionFromAlias}" => $actionTable], "{$actionFromAlias}.ActionID = ae.FromActionIDRef")

        ->where([
            'ae.ProcessEntityIDRef' => $peid,
            'ae.isDeleted' => 0,
        ])
        ->select([
            'ae.ID',
            'ae.ProcessEntityIDRef',
            'ae.FromActionIDRef',
            'ae.ActionIDRef',
            'ae.ActionDate',
            'ae.ActionDateHijri',
            'ae.UserIDRef',
            'ae.OBSCategory3IDRef',
            'ae.ActionMessage',
            'ae.ActionAutoMessage',
            'ae.toActionIdRef',
            'ae.toActionDate',
            'ae.toActionDateHijri',

            // نام کاربر انجام‌دهنده
            "CONCAT(COALESCE(u.FirstName, ''), ' ', COALESCE(u.LastName, '')) AS UserName",

            // عنوان obs
            'o.obsTitle AS OBSCategory3Title',

            // عنوان مرحله‌ای که باید در گزارش نمایش داده شود
            new \yii\db\Expression("
                CASE
                    WHEN ae.FromActionIDRef IS NULL THEN {$actionCurrentAlias}.ActionTitle1
                    ELSE {$actionFromAlias}.ActionTitle1
                END AS ActionTitle1
            "),
            new \yii\db\Expression("
                CASE
                    WHEN ae.FromActionIDRef IS NULL THEN {$actionCurrentAlias}.ActionTitle2
                    ELSE {$actionFromAlias}.ActionTitle2
                END AS ActionTitle2
            "),

            // اکشن واقعی برای نمایش/دیباگ
            new \yii\db\Expression("
                CASE
                    WHEN ae.FromActionIDRef IS NULL THEN ae.ActionIDRef
                    ELSE ae.FromActionIDRef
                END AS DisplayActionID
            "),
        ])
        ->orderBy(['ae.ID' => SORT_ASC])
        ->all();

    /**
     * فیلتر رکوردهای تکراری:
     * - فقط یک بار هر مرحله را نمایش بده
     * - رکورد شروع (FromActionIDRef = null) فقط اگر مرحله 1172 بود نگه دار
     * - برای اقدامات واقعی، مرحله بر اساس DisplayActionID یکتا شود
     */
    $uniqueActions = [];
    foreach ($actionEntityData as $record) {
        $displayActionId = $record['DisplayActionID'];

        // مرحله شروع فرایند
        if ($record['FromActionIDRef'] === null) {
            if ((int)$record['ActionIDRef'] === 1172 && !isset($uniqueActions[$displayActionId])) {
                $uniqueActions[$displayActionId] = $record;
            }
            continue;
        }

        // رکوردهای اقدام واقعی
        if (!isset($uniqueActions[$displayActionId])) {
            $uniqueActions[$displayActionId] = $record;
        }
    }

    // تبدیل به آرایه نهایی و حفظ ترتیب اصلی
    $actionEntityData = array_values($uniqueActions);
    usort($actionEntityData, function ($a, $b) {
        return (int)$a['ID'] <=> (int)$b['ID'];
    });

    /**
     * محاسبه مدت زمان بین هر اقدام
     */
    $jDate = new \app\components\jDateTime(true, true, 'Asia/Tehran');

    if (count($actionEntityData) > 1) {
        for ($i = 0; $i < count($actionEntityData) - 1; $i++) {
            $current = $actionEntityData[$i];
            $next = $actionEntityData[$i + 1];

            $currentDate = isset($current['ActionDateHijri']) ? $current['ActionDateHijri'] : null;
            $nextDate = isset($next['ActionDateHijri']) ? $next['ActionDateHijri'] : null;

            $actionEntityData[$i]['Duration'] = '-';

            if ($currentDate && $nextDate) {
                // فقط بخش تاریخ را برمی‌داریم، چون ممکن است همراه ساعت باشد
                $currentDateOnly = substr($currentDate, 0, 10);
                $nextDateOnly = substr($nextDate, 0, 10);

                $currentParts = explode('/', $currentDateOnly);
                $nextParts = explode('/', $nextDateOnly);

                if (count($currentParts) === 3 && count($nextParts) === 3) {
                    $year1 = (int)$currentParts[0];
                    $month1 = (int)$currentParts[1];
                    $day1 = (int)$currentParts[2];

                    $year2 = (int)$nextParts[0];
                    $month2 = (int)$nextParts[1];
                    $day2 = (int)$nextParts[2];

                    $isValidDate1 = $year1 > 1300 && $month1 >= 1 && $month1 <= 12 && $day1 >= 1 && $day1 <= 31;
                    $isValidDate2 = $year2 > 1300 && $month2 >= 1 && $month2 <= 12 && $day2 >= 1 && $day2 <= 31;

                    if ($isValidDate1 && $isValidDate2) {
                        $timeBetween = $jDate->timeBetweenDate($year1, $month1, $day1, $year2, $month2, $day2);

                        $duration = '';
                        if (!empty($timeBetween[0])) {
                            $duration .= $timeBetween[0] . ' ماه ';
                        }
                        if (!empty($timeBetween[1])) {
                            $duration .= $timeBetween[1] . ' روز ';
                        }

                        $actionEntityData[$i]['Duration'] = trim($duration) !== '' ? trim($duration) : '0 روز';
                    }
                }
            }
        }
    }

    // برای آخرین رکورد یا رکوردهای فاقد Duration
    foreach ($actionEntityData as $index => $row) {
        if (!isset($actionEntityData[$index]['Duration']) || $actionEntityData[$index]['Duration'] === '') {
            $actionEntityData[$index]['Duration'] = '-';
        }

        // تمیز کردن فاصله اضافه نام کاربر
        if (isset($actionEntityData[$index]['UserName'])) {
            $actionEntityData[$index]['UserName'] = trim(preg_replace('/\s+/', ' ', $actionEntityData[$index]['UserName']));
            if ($actionEntityData[$index]['UserName'] === '') {
                $actionEntityData[$index]['UserName'] = '-';
            }
        } else {
            $actionEntityData[$index]['UserName'] = '-';
        }

        if (empty($actionEntityData[$index]['ActionTitle1'])) {
            $actionEntityData[$index]['ActionTitle1'] = '-';
        }

        if (empty($actionEntityData[$index]['ActionMessage'])) {
            $actionEntityData[$index]['ActionMessage'] = '-';
        }

        if (empty($actionEntityData[$index]['ActionAutoMessage'])) {
            $actionEntityData[$index]['ActionAutoMessage'] = '-';
        }

        if (empty($actionEntityData[$index]['ActionDateHijri'])) {
            $actionEntityData[$index]['ActionDateHijri'] = '-';
        }
    }

    $actionEntityProvider = new \yii\data\ArrayDataProvider([
        'allModels' => $actionEntityData,
        'pagination' => [
            'pageSize' => 50,
        ],
        'sort' => [
            'attributes' => [
                'ActionTitle1',
                'UserName',
                'ActionDateHijri',
                'ActionAutoMessage',
                'ActionMessage',
                'Duration',
                'DisplayActionID',
            ],
        ],
    ]);

    $renderMethod = \Yii::$app->request->isAjax ? 'renderAjax' : 'render';

    return $this->$renderMethod('stampSummary', [
        'actionEntityProvider' => $actionEntityProvider,
        'processEntityModel' => $processEntityModel,
    ]);
}
    
 public function actionGetWorkflowSteps()
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $stampId = Yii::$app->request->get('stampId');
    $currentActionId = (int) Yii::$app->request->get('currentActionId');
    $peid = Yii::$app->request->get('peid');

    if (empty($stampId)) {
        return ['error' => 'شناسه نشان یافت نشد'];
    }

    // 1) دریافت اطلاعات سند نشان
    $stampModel = \app\modules\document\models\StampDocument::find()
        ->where([
            'StampId' => $stampId,
            'isDeleted' => 0,
        ])
        ->one();

    if (!$stampModel) {
        return ['error' => 'سند نشان سازمانی یافت نشد'];
    }

    // 2) دریافت OBS سند و تعیین شرکت بر اساس خود سند
    $stampObs = \app\modules\OBS\models\Obsnew::find()
        ->where(['obsId' => $stampModel->ObsIDRef])
        ->one();

    if (!$stampObs) {
        return ['error' => 'OBS مرتبط با سند یافت نشد'];
    }

    $companyId = (int) $stampObs->companyIdRef;
    $isPerson = ((int) $stampModel->StampTypeIDRef === 4);
    $organizationalMaker = (int) ($stampModel->OrganizationalMaker ?? 2);

    // 3) محاسبه مسیر هوشمند بر اساس اطلاعات خود سند
    $smartPath = $this->calculateSmartPath(
        $currentActionId,
        $organizationalMaker,
        $isPerson,
        $companyId,
        $stampId,
        $peid,
        $stampModel
    );

    if (empty($smartPath) || !is_array($smartPath)) {
        return ['error' => 'مسیر گردش کار قابل محاسبه نیست'];
    }

    // 4) دریافت عنوان اکشن‌ها
    $actions = \app\modules\workflow\models\Action::find()
        ->select(['ActionID', 'ActionTitle1', 'ActionTitle2'])
        ->where(['in', 'ActionID', $smartPath])
        ->andWhere(['or', ['isDeleted' => null], ['<>', 'isDeleted', 1]])
        ->indexBy('ActionID')
        ->asArray()
        ->all();

    $currentIndex = array_search($currentActionId, $smartPath);
    if ($currentIndex === false) {
        $currentIndex = null;
    }
    
    
    // کاربر ثبت‌کننده درخواست برای مرحله 1172
$starterUsers1172 = [];

if (!empty($peid)) {
    $userTable = \app\modules\account\models\User::tableName();
    $obsTable = \app\modules\OBS\models\Obsnew::tableName();
    $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();

    $starterRecord = (new \yii\db\Query())
        ->from(['ae' => $actionEntityTable])
        ->leftJoin(['u' => $userTable], 'u.UserID = ae.UserIDRef')
        ->leftJoin(['o' => $obsTable], 'o.obsId = ae.OBSCategory3IDRef')
        ->where([
            'ae.ProcessEntityIDRef' => $peid,
            'ae.ActionIDRef' => 1172,
            'ae.FromActionIDRef' => null,
            'ae.isDeleted' => 0,
        ])
        ->select([
            'ae.UserIDRef',
            'ae.OBSCategory3IDRef',
            'u.FirstName',
            'u.LastName',
            'o.obsTitle AS positionTitle',
        ])
        ->orderBy(['ae.ID' => SORT_ASC])
        ->one();

    if (!empty($starterRecord) && !empty($starterRecord['UserIDRef'])) {
        $firstName = trim((string)($starterRecord['FirstName'] ?? ''));
        $lastName = trim((string)($starterRecord['LastName'] ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        $starterUsers1172[] = [
            'id' => (int)$starterRecord['UserIDRef'],
            'FirstName' => $firstName,
            'LastName' => $lastName,
            'name' => $fullName !== '' ? $fullName : 'نامشخص',
            'positionTitle' => !empty($starterRecord['positionTitle'])
                ? $starterRecord['positionTitle']
                : 'نامشخص',
        ];
    }
}
    // 5) ساخت مراحل برای نمایش
    $steps = [];
    foreach ($smartPath as $index => $actionId) {
        $actionId = (int) $actionId;

        $isCurrent = ($currentActionId === $actionId);
        $isPast = ($currentIndex !== null && $index < $currentIndex);
        $isFuture = ($currentIndex !== null && $index > $currentIndex);

        // previousActionId واقعی در مسیر نمایشی
        $previousActionId = ($index > 0) ? (int) $smartPath[$index - 1] : null;

        $steps[] = [
            'actionId' => $actionId,
            'title' => $actions[$actionId]['ActionTitle1'] ?? 'نامشخص',
            'subtitle' => $actions[$actionId]['ActionTitle2'] ?? '',
            'isCurrent' => $isCurrent,
            'isPast' => $isPast,
            'isFuture' => $isFuture,
            'previousActionId' => $previousActionId,
'users' => ($actionId === 1172)
    ? $starterUsers1172
    : $this->getUsersForAction(
        $actionId,
        $organizationalMaker,
        $stampId,
        $previousActionId,
        $peid
    ),
        ];
    }

    return [
        'steps' => $steps,
        'currentActionId' => $currentActionId,
        'currentIndex' => $currentIndex,
        'isPerson' => $isPerson,
        'organizationalMaker' => $organizationalMaker,
        'companyId' => $companyId,
        'smartPath' => $smartPath,
    ];
}
    
private function calculateSmartPath($currentActionId, $organizationalMaker, $isPerson, $companyId, $stampId, $peid, $stampModel)
{
    $isHolding = ((int) $companyId === 248);
    $isManagerial = $stampModel ? $stampModel->isPositionManagerial() : false;

    $paths = [
        'exists_holding_nonperson' => [1172, 1173, 1179, 1177, 1181, 1178, 1184],
        'exists_nonholding_nonperson' => [1172, 1173, 1179, 1177, 1181, 1178, 1184],

        'exists_holding_person_managerial' => [1172, 1173, 1179, 1175, 1176, 1179, 1177, 1181, 1178, 1184],
        'exists_holding_person_nonmanagerial' => [1172, 1173, 1179, 1175, 1179, 1177, 1181, 1178, 1184],
        'exists_nonholding_person_managerial' => [1172, 1173, 1179, 1175, 1176, 1179, 1177, 1181, 1178, 1184],
        'exists_nonholding_person_nonmanagerial' => [1172, 1173, 1179, 1175, 1179, 1177, 1181, 1178, 1184],

        'notexists_holding_nonperson' => [1172, 1173, 1174, 1177, 1178, 1184],
        'notexists_nonholding_nonperson' => [1172, 1173, 1174, 1177, 1178, 1184],

        'notexists_holding_person_managerial' => [1172, 1173, 1174, 1175, 1176, 1177, 1178, 1184],
        'notexists_holding_person_nonmanagerial' => [1172, 1173, 1174, 1175, 1177, 1184],
        'notexists_nonholding_person_managerial' => [1172, 1173, 1174, 1175, 1176, 1177, 1178, 1184],
        'notexists_nonholding_person_nonmanagerial' => [1172, 1173, 1174, 1175, 1177, 1178, 1184],
    ];

    $key = ((int) $organizationalMaker === 1) ? 'exists_' : 'notexists_';
    $key .= $isHolding ? 'holding_' : 'nonholding_';

    if ($isPerson) {
        $key .= 'person_';
        $key .= $isManagerial ? 'managerial' : 'nonmanagerial';
    } else {
        $key .= 'nonperson';
    }

    $path = $paths[$key] ?? [1172, 1173, 1179, 1175, 1176, 1177, 1181, 1178, 1184];

    // =========================================================================
    // اصلاح هوشمند مسیر بر اساس تصمیم گرفته‌شده در 1179 (تفکیک 1177 و 1181)
    // =========================================================================
    if ((int) $organizationalMaker === 1 && !empty($peid)) {
        // بررسی اکشنی که بلافاصله بعد از 1179 اجرا شده است
        $chosenAction = (new \yii\db\Query())
            ->select(['ActionIDRef'])
            ->from(\app\modules\workflow\models\ActionEntity::tableName())
            ->where([
                'ProcessEntityIDRef' => $peid,
                'FromActionIDRef' => 1179,
                'isDeleted' => 0
            ])
            ->orderBy(['ID' => SORT_DESC])
            ->scalar();

        if ($chosenAction) {
            $chosenAction = (int)$chosenAction;
            if ($chosenAction === 1177) {
                // اگر 1177 انتخاب شده، 1181 از مسیر حذف می‌شود
                $path = array_values(array_diff($path, [1181]));
            } elseif ($chosenAction === 1181) {
                // اگر 1181 انتخاب شده، 1177 از مسیر حذف می‌شود
                $path = array_values(array_diff($path, [1177]));
            }
        }
    }

    return $path;
}
    
private function getUsersForAction(
    $actionId,
    $organizationalMaker,
    $stampId = null,
    $previousActionId = null,
    $peid = null
) {
    $actionId = (int)$actionId;
    $organizationalMaker = (int)$organizationalMaker;
    $previousActionId = $previousActionId !== null ? (int)$previousActionId : null;

    // =========================================================
    // اگر PEID داریم، اکشن قبلی واقعی را از ActionEntity می‌گیریم
    // =========================================================
    if (!empty($peid)) {
        $realPreviousAction = (new \yii\db\Query())
            ->select(['FromActionIDRef'])
            ->from(\app\modules\workflow\models\ActionEntity::tableName())
            ->where([
                'ProcessEntityIDRef' => $peid,
                'ActionIDRef' => $actionId,
            ])
            ->orderBy(['ID' => SORT_DESC])
            ->scalar();

        if ($realPreviousAction !== false && $realPreviousAction !== null) {
            $previousActionId = (int)$realPreviousAction;
        }
    }

    // =========================================================
    // اطلاعات سند
    // =========================================================
    if (empty($stampId)) {
        return [];
    }

    $stampModel = \app\modules\document\models\StampDocument::find()
        ->where([
            'StampId' => $stampId,
            'isDeleted' => 0,
        ])
        ->one();

    if (!$stampModel) {
        return [];
    }

    $stampObs = \app\modules\OBS\models\Obsnew::find()
        ->where(['obsId' => $stampModel->ObsIDRef])
        ->one();

    if (!$stampObs) {
        return [];
    }

    $stampCompanyId = (int)$stampObs->companyIdRef;
    $stampCategoryId = (int)$stampObs->categoryIdRef;
    $stampProjectId = (int)$stampObs->ProjectIDRef;
    $stampSiteId = (int)$stampObs->SiteIDRef;
    $stampRelatedObsId = (int)$stampObs->relatedObsIdRef;

    // =========================================================
    // OBSهایی که اساساً به اکشن جاری دسترسی دارند
    // =========================================================
    $candidateObsIds = \app\modules\OBS\models\ObsProcessAction::find()
        ->select(['OBSCategory3IDRef'])
        ->where(['ActionIDRef' => $actionId])
        ->andWhere("COALESCE(isDeleted, 0) <> 1")
        ->column();

    if (empty($candidateObsIds)) {
        return [];
    }

    // =========================================================
    // برای 1172:
    // فقط OBSای که رکورد به آن ارجاع داده شده باید نمایش داده شود.
    // مشابه شرط stampDocument
    // =========================================================
    $action1172TargetObsId = null;

    if ($actionId === 1172 && !empty($peid)) {
        $action1172TargetObsId = (new \yii\db\Query())
            ->select(['OBSCategory3IDRef'])
            ->from(\app\modules\workflow\models\ActionEntity::tableName())
            ->where([
                'ProcessEntityIDRef' => $peid,
                'ActionIDRef' => 1172,
            ])
            ->orderBy(['ID' => SORT_DESC])
            ->scalar();

        $action1172TargetObsId = $action1172TargetObsId !== null
            ? (int)$action1172TargetObsId
            : null;
    }

    // =========================================================
    // مدل OBSهای کاندید
    // =========================================================
    $obsModels = \app\modules\OBS\models\Obsnew::find()
        ->where(['in', 'obsId', $candidateObsIds])
        ->andWhere(['or', ['isActive' => null], ['isActive' => 1]])
        ->all();

    if (empty($obsModels)) {
        return [];
    }

    $filteredObsIds = [];
    $fullTitleMap = [];

    foreach ($obsModels as $obs) {
        $keep = false;

        $obsId = (int)$obs->obsId;
        $obsCompanyId = (int)$obs->companyIdRef;
        $obsCategoryId = (int)$obs->categoryIdRef;
        $obsProjectId = (int)$obs->ProjectIDRef;
        $obsSiteId = (int)$obs->SiteIDRef;
        $isDirector = (int)$obs->isDirector;

        // =====================================================
        // معادل AccessCondition در stampDocument
        //
        // ستاد:
        // تمام رکوردهای شرکت خودش
        //
        // پروژه:
        // خود پروژه + همه کارگاه‌های همان پروژه
        //
        // کارگاه:
        // فقط همان کارگاه
        // =====================================================
        $accessConditionMatch = false;

        if ($obsCategoryId === 2) {
            // OBS ستادی: همه رکوردهای شرکت خودش
            $accessConditionMatch = ($stampCompanyId === $obsCompanyId);
        } elseif ($obsCategoryId === 1 && $obsProjectId > 0 && $obsSiteId === 0) {
            // OBS پروژه‌ای: پروژه و کارگاه‌های همان پروژه
            $accessConditionMatch =
                $stampCompanyId === $obsCompanyId &&
                $stampCategoryId === 1 &&
                $stampProjectId === $obsProjectId;
        } elseif ($obsCategoryId === 1 && $obsProjectId > 0 && $obsSiteId > 0) {
            // OBS کارگاهی: فقط همان کارگاه
            $accessConditionMatch =
                $stampCompanyId === $obsCompanyId &&
                $stampCategoryId === 1 &&
                $stampProjectId === $obsProjectId &&
                $stampSiteId === $obsSiteId;
        }

        // =====================================================
        // معادل publicConditions در stampDocument
        // =====================================================
        $publicConditionMatch = false;

        if ($stampCompanyId === $obsCompanyId) {
            // ستاد با ستاد
            if ($stampCategoryId === 2 && $obsCategoryId === 2) {
                $publicConditionMatch = true;
            }

            // سند کارگاهی؛ OBS با همان Site
            elseif (
                $stampCategoryId === 1 &&
                $stampSiteId > 0 &&
                $stampSiteId === $obsSiteId
            ) {
                $publicConditionMatch = true;
            }

            // سند پروژه‌ای؛ OBS با همان Project
            elseif (
                $stampCategoryId === 1 &&
                $stampSiteId === 0 &&
                $stampProjectId > 0 &&
                $stampProjectId === $obsProjectId
            ) {
                $publicConditionMatch = true;
            }
        }

        // =====================================================
        // 1179 و 1181
        // در stampDocument شرط ساختاری ندارند.
        // هر OBS دارای دسترسی به اکشن، مجاز است.
        // =====================================================
        if (in_array($actionId, [ 1179,1181], true)) {
       if ($obsCompanyId === 248) {
        $keep = true;
    }
}

        // =====================================================
        // 1172
        // فقط OBS انتخاب‌شده در ActionEntity
        // =====================================================
        elseif ($actionId === 1172) {
            if (
                $action1172TargetObsId !== null &&
                $action1172TargetObsId === $obsId
            ) {
                $keep = true;
            }
        }

        // =====================================================
        // 1173
        //
        // OrganizationalMaker = 1:
        // - شرکت غیر 248 => مدیرعامل همان شرکت
        //
        // OrganizationalMaker = 2:
        // - شرکت غیر 248 => publicConditions
        //
        // شرکت 248:
        // - در هر دو حالت فقط relatedObsIdRef
        // =====================================================
        elseif ($actionId === 1173) {
            // شرکت 248، مستقل از OrganizationalMaker
            if (
                $stampCompanyId === 248 &&
                $stampRelatedObsId === $obsId
            ) {
                $keep = true;
            }

            // وجود دارد: فقط مدیرعامل همان شرکت غیر 248
            elseif (
                $organizationalMaker === 1 &&
                $stampCompanyId !== 248 &&
                $stampCompanyId === $obsCompanyId &&
                $isDirector === 1
            ) {
                $keep = true;
            }

            // وجود ندارد: شروط پابلیک برای شرکت غیر 248
            elseif (
                $organizationalMaker === 2 &&
                $stampCompanyId !== 248 &&
                $publicConditionMatch
            ) {
                $keep = true;
            }
        }

        // =====================================================
        // 1174
        // فقط publicConditions
        // =====================================================
        elseif ($actionId === 1174) {
            if ($publicConditionMatch) {
                $keep = true;
            }
        }

        // =====================================================
        // 1175 و 1176
        //
        // اگر OrganizationalMaker = 1:
        // فقط OBSهای شرکت 248
        //
        // اگر OrganizationalMaker = 2:
        // OBSهای همان شرکت سند
        // =====================================================
        elseif (in_array($actionId, [1175, 1176], true)) {
            if (
                $organizationalMaker === 1 &&
                $obsCompanyId === 248
            ) {
                $keep = true;
            } elseif (
                $organizationalMaker === 2 &&
                $stampCompanyId === $obsCompanyId
            ) {
                $keep = true;
            }
        }

        // =====================================================
        // 1177
        // فقط AccessCondition
        // =====================================================
        elseif ($actionId === 1177) {
            if ($accessConditionMatch) {
                $keep = true;
            }
        }

        // =====================================================
        // 1178
        //
        // OrganizationalMaker = 2:
        // AccessCondition
        //
        // OrganizationalMaker = 1 و مسیر از 1177:
        // AccessCondition
        //
        // OrganizationalMaker = 1 و مسیر از 1181:
        // سند مربوط به شرکت 248
        // =====================================================
        elseif ($actionId === 1178) {
            if (
                $organizationalMaker === 2 &&
                $accessConditionMatch
            ) {
                $keep = true;
            } elseif (
                $organizationalMaker === 1 &&
                $previousActionId === 1177 &&
                $accessConditionMatch
            ) {
                $keep = true;
            } elseif (
                $organizationalMaker === 1 &&
                $previousActionId === 1181 &&
                $obsCompanyId === 248
            ) {
                $keep = true;
            }
        }

        if ($keep) {
            $filteredObsIds[] = $obsId;

            $fullTitleMap[$obsId] = !empty($obs->fullTitle)
                ? $obs->fullTitle
                : (!empty($obs->obsTitle) ? $obs->obsTitle : 'نامشخص');
        }
    }

    $filteredObsIds = array_values(array_unique($filteredObsIds));

    if (empty($filteredObsIds)) {
        return [];
    }

    // =========================================================
    // کاربران فعال متصل به OBSهای مجاز
    // =========================================================
    $userObsData = \app\modules\account\models\UserObs::find()
        ->select(['UserIDRef', 'OBSCategory3IDRef'])
        ->where(['in', 'OBSCategory3IDRef', $filteredObsIds])
        ->andWhere(['isActive' => 1])
        ->andWhere("COALESCE(isDeleted, 0) <> 1")
        ->asArray()
        ->all();

    if (empty($userObsData)) {
        return [];
    }

    $userIds = array_values(array_unique(array_column($userObsData, 'UserIDRef')));

    if (empty($userIds)) {
        return [];
    }

    // =========================================================
    // اطلاعات کاربران فعال
    // =========================================================
    $users = \app\modules\account\models\User::find()
        ->select(['UserID', 'FirstName', 'LastName'])
        ->where(['in', 'UserID', $userIds])
        ->andWhere(['isActive' => 1])
        ->andWhere("COALESCE(isDeleted, 0) <> 1")
        ->asArray()
        ->all();

    if (empty($users)) {
        return [];
    }

    $userInfoMap = [];

    foreach ($users as $user) {
        $userInfoMap[(int)$user['UserID']] = $user;
    }

    // عنوان سازمانی هر کاربر
    $userFullTitleMap = [];

    foreach ($userObsData as $userObs) {
        $userId = (int)$userObs['UserIDRef'];
        $obsId = (int)$userObs['OBSCategory3IDRef'];

        if (
            !isset($userFullTitleMap[$userId]) &&
            isset($fullTitleMap[$obsId])
        ) {
            $userFullTitleMap[$userId] = $fullTitleMap[$obsId];
        }
    }

    // =========================================================
    // خروجی نهایی
    // =========================================================
    $result = [];

    foreach ($userIds as $userId) {
        if (!isset($userInfoMap[$userId])) {
            continue;
        }

        $firstName = trim((string)$userInfoMap[$userId]['FirstName']);
        $lastName = trim((string)$userInfoMap[$userId]['LastName']);
        $fullName = trim($firstName . ' ' . $lastName);

        $result[] = [
            'id' => (int)$userId,
            'FirstName' => $firstName,
            'LastName' => $lastName,
            'name' => $fullName !== '' ? $fullName : 'نامشخص',
            'positionTitle' => $userFullTitleMap[$userId] ?? 'نامشخص',
        ];
    }

    return $result;
}
//مسیر هوشمند امحاء
    public function actionRefundStampSummary($peid)
    {
        $peid = (int)$peid;
        $processEntityModel = \app\modules\workflow\models\ProcessEntity::find()
            ->where(['ProcessEntityID' => $peid, 'isDeleted' => 0])
            ->one();
        if (!$processEntityModel) {
            throw new \yii\web\NotFoundHttpException('فرایند یافت نشد.');
        }
        $actionEntityData = (new StampRefundWorkflow())->getActionHistoryData($peid);
        $actionEntityProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $actionEntityData,
            'pagination' => ['pageSize' => 50],
            'sort' => [
                'attributes' => [
                    'ActionTitle1',
                    'UserName',
                    'ActionDateHijri',
                    'ActionAutoMessage',
                    'ActionMessage',
                    'Duration',
                ],
            ],
        ]);
        $renderMethod = Yii::$app->request->isAjax ? 'renderAjax' : 'render';
        return $this->$renderMethod('refundStampSummary', [
            'actionEntityProvider' => $actionEntityProvider,
            'processEntityModel' => $processEntityModel,
        ]);
    }
    public function actionGetRefundWorkflowSteps()
    {
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $stampId = Yii::$app->request->get('stampId');
    $currentActionId = (int)Yii::$app->request->get('currentActionId');
    $peid = Yii::$app->request->get('peid');

        return (new StampRefundWorkflow())->getRefundWorkflowSteps(
            $stampId,
            $currentActionId,
            $peid
        );
}
}
