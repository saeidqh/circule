<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;

class ActionOrderMeasureController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['create', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'delete'],
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

    public function actionCreate($aoid) {
//        $personTable = \app\modules\OBS\models\Person::tableName();
        $actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ID' => $aoid])->one();
        $actionOrderMeasureModel = new \app\modules\workflow\models\ActionOrderMeasure();
        $actionOrderMeasureModel->ActionOrderIDRef = $aoid;

        if ($actionOrderMeasureModel->load(Yii::$app->request->Post())) {
            $actionOrderMeasureModel->ActionOrderMeasureID = \app\modules\workflow\models\ActionOrderMeasure::find()->max('ActionOrderMeasureID') + 1;
            if ($actionOrderMeasureModel->validate()) {
                if ($actionOrderMeasureModel->save()) {
                    $this->sendLog($actionOrderMeasureModel->ActionOrderMeasureID, "New");
                    return \app\components\Utility::renderPjaxCallBack('callbackActionOrderMeasure', [
                                'result' => 'true',
                                'message' => 'Action Order Measure created successfully.',
                    ]);
                } else {
                    return \app\components\Utility::renderPjaxCallBack('callbackActionOrderMeasure', [
                                'result' => 'false',
                                'message' => 'Error on saving data!',
                    ]);
                }
            }
        }
        $type = $actionOrderMeasureModel->actionOrder->action->process->ProccessTypeIDRef;
        $measureArray = \yii\helpers\ArrayHelper::map(\app\modules\workflow\models\Measure::find()->where("ProccessTypeIDRef=$type or ProccessTypeIDRef is null")->asArray()->all(), 'MeasureID', 'MeasureTitle');

        return $this->renderAjax('_actionOrdreMeasureForm', [
                    'measureArray' => $measureArray,
                    'actionOrderModel' => $actionOrderModel,
                    'actionOrderMeasureModel' => $actionOrderMeasureModel,
        ]);
    }

    public function actionDelete($aom) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $actionOrderMeasureModel = \app\modules\workflow\models\ActionOrderMeasure::find()
                ->where(['ActionOrderMeasureID' => $aom])
                ->one();

        if (empty($actionOrderMeasureModel)) {
            throw new \yii\web\HttpException(403, 'Action Order Measure Not Found!!');
        }
        
        $actionOrderMeasureModel->isDeleted = 1;
        if ($actionOrderMeasureModel->save()) {
            $this->sendLog($actionOrderMeasureModel->ActionOrderMeasureID, "Delete");
            return [
                'result' => 1,
                'message' => 'successfull deleted Action Order Measure!!'
            ];
        }
        return [
            'result' => 0,
            'message' => 'Cannot deleted Action Order Measure!!'
        ];
    }

    public function sendLog($id, $type) {
        $actionOrderMeasureTable = \app\modules\workflow\models\ActionOrderMeasure::tableName();
        $activityManagement = new \app\components\ActivityManagement();
        $actionType = $type;
        $subType = $type ."ActionOrderMeasure";
        $subTypeDescription["TableName"] = $actionOrderMeasureTable;
        $subTypeDescription["RecordPrimaryKey"] = $id;
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);
    }
}
