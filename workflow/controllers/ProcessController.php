<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;

class ProcessController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['action-list', 'update', 'subprocess-action-list'],
                'rules' => [
                    // allow authenticated users
                    [
                        'actions' => ['action-list'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action, $exteraData = []) {
                            $pid = isset($exteraData['pid']) ? $exteraData['pid'] : Yii::$app->request->get('pid');
                            return \app\access\AccessControl::isAccess(['process', 'pid' => $pid, 'type' => \app\access\ProcessAccess::PROCESS_VIEW_TYPE]);
                        },
                    ],
                    [
                        'actions' => [
                            'update',
                        ],
                        'roles' => ['@'],
                        'allow' => true,
                        'matchCallback' => function($rule, $action, $exteraData = []) {
//                            $pid = isset($exteraData['pid']) ? $exteraData['pid'] : Yii::$app->request->get('pid');
//                            return \app\access\AccessControl::isAccess(['process', 'pid' => $pid, 'type' => \app\access\ProcessAccess::PROCESS_UPDATE_TYPE]);
                            return \app\access\AccessControl::isAccess(
                                ['obs', 'check' => 'isCandoAdmin']
                            );
                        },
                    ],
                    [
                        'actions' => ['subprocess-action-list'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action, $exteraData = []) {
                            $pid = isset($exteraData['pid']) ? $exteraData['pid'] : Yii::$app->request->get('pid');
                            return \app\access\AccessControl::isAccess(['process', 'pid' => $pid, 'type' => \app\access\ProcessAccess::PROCESS_VIEW_TYPE]);
                        },
                    ],
                // everything else is denied
                ],
            ],
        ];
    }

    public function actionUpdate($pid) {
        //Under Construction
    }

    public function actionActionList($pid) {
        
        $paramsList = [
            1 => 'param-invoice',
            2 => 'param-cash-request',
            3 => 'param-contract',
            4 => 'param-budget',
            5 => 'param-capital',
            6 => 'param-warranty',
            7 => 'param-debitmemo',
            8 => 'param-claim',
            9 => 'param-actual-progress-annual-budget',
            10 => 'param-permission',
            11 => 'param-sourcing',
            12 => 'param-tender',
            13 => 'param-contract-closing-document',
            14 => 'param-automation',
            15 => 'param-network',
            16 => 'employee-settlement',
        ];
        
        $actionSearchModel = new \app\modules\workflow\models\ActionSearch();
        $actionSearchModel->setScenario('search');
        $actionDataProvider = $actionSearchModel->searchProcess(Yii::$app->request->queryParams, $pid);
        $actionDataProvider->sort->defaultOrder=["ActionOrder"=>SORT_ASC];
        
        $actionOrderSearchModel = new \app\modules\workflow\models\ActionOrderSearch();
        $actionOrderSearchModel->setScenario('search');
        $actionOrderDataProvider = $actionOrderSearchModel->search(Yii::$app->request->queryParams, $pid);

        $processModel = \app\modules\workflow\models\Process::find()->where(['ProcessID' => $pid])->one();

        $arrProcessChilds = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\Process::find()->where(['parentProcessIdRef' => $pid])->asArray()->all(), 'ProcessID', 'ProcessTitle');
        return $this->render('processActions', [
                    'actionSearchModel' => $actionSearchModel,
                    'actionDataProvider' => $actionDataProvider,
                    'processModel' => $processModel,
                    'actionOrderSearchModel' => $actionOrderSearchModel,
                    'actionOrderDataProvider' => $actionOrderDataProvider,
                    'arrProcessChilds' => $arrProcessChilds,
                    'paramsList' => $paramsList,
        ]);
    }

    public function actionSubprocessActionList($pid) {
        $actionSearchModel = new \app\modules\workflow\models\ActionSearch();
        $actionSearchModel->setScenario('search');
        $actionDataProvider = $actionSearchModel->searchProcess(Yii::$app->request->queryParams, $pid);

        $actionOrderSearchModel = new \app\modules\workflow\models\ActionOrderSearch();
        $actionOrderSearchModel->setScenario('search');
        $actionOrderDataProvider = $actionOrderSearchModel->search(Yii::$app->request->queryParams, $pid);

        $processModel = \app\modules\workflow\models\Process::find()->where(['ProcessID' => $pid])->one();

        return $this->renderAjax('_subprocessActions', [
                    'actionSearchModel' => $actionSearchModel,
                    'actionDataProvider' => $actionDataProvider,
                    'processModel' => $processModel,
                    'actionOrderSearchModel' => $actionOrderSearchModel,
                    'actionOrderDataProvider' => $actionOrderDataProvider,
        ]);
    }

}
