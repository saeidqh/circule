<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;

class MeasureController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['create', 'update', 'list'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'list'],
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

    public function actionCreate() {
        $measureModel = new \app\modules\workflow\models\Measure();
        $measureModel->setScenario('create');

        if ($measureModel->load(Yii::$app->request->Post())) {
            $measureModel->MeasureID = \app\modules\workflow\models\Measure::find()->max('MeasureID') + 1;
            if ($measureModel->validate()) {
                if ($measureModel->save()) {
                    $this->sendLog($measureModel->MeasureID, "New");
                    return \app\components\Utility::renderPjaxCallBack('measureCallbackPjax', [
                                'result' => 'true',
                                'message' => 'Measure created successfully.',
                    ]);
                } else {
                    return \app\components\Utility::renderPjaxCallBack('measureCallbackPjax', [
                                'result' => 'false',
                                'message' => 'Error on saving data!',
                    ]);
                }
            }
        }

        return $this->renderAjax('_measureForm', [
                    'measureModel' => $measureModel,
        ]);
    }
    
    public function actionUpdate($mid) {
        $measureModel = \app\modules\workflow\models\Measure::find()->where(['MeasureID' => $mid])->one();
        $measureModel->setScenario('update');

        if ($measureModel->load(Yii::$app->request->Post())) {
            if ($measureModel->validate()) {
                if ($measureModel->save()) {
                    $this->sendLog($measureModel->MeasureID, "Edit");
                    return \app\components\Utility::renderPjaxCallBack('measureCallbackPjax', [
                                'result' => 'true',
                                'message' => 'Measure update successfully.',
                    ]);
                } else {
                    return \app\components\Utility::renderPjaxCallBack('measureCallbackPjax', [
                                'result' => 'false',
                                'message' => 'Error on saving data!',
                    ]);
                }
            }
        }

        return $this->renderAjax('_measureForm', [
                    'measureModel' => $measureModel,
        ]);
    }

    public function actionList() {
        $measureSearchModel = new \app\modules\workflow\models\MeasureSearch();
        $measureSearchModel->setScenario('search');
        $dataProvider = $measureSearchModel->search(Yii::$app->request->queryParams);

        return $this->render('measureList', [
                    'measureSearchModel' => $measureSearchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    
    public function sendLog($id, $type) {
        $measureTable = \app\modules\workflow\models\Measure::tableName();
        $activityManagement = new \app\components\ActivityManagement();
        $actionType = $type;
        $subType = $type ."Measure";
        $subTypeDescription["TableName"] = $measureTable;
        $subTypeDescription["RecordPrimaryKey"] = $id;
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);
    }
}
