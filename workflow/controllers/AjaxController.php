<?php

namespace app\modules\workflow\controllers;

use Yii;
use yii\web\Controller;


class AjaxController extends Controller {

    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['taskscount', 'workflowgraph', 'workflow-action-order'],
                'rules' => [
                    [
                        'actions' => ['workflowgraph'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function($rule, $action, $exteraData = []) {
                            $pid = isset($exteraData['pid']) ? $exteraData['pid'] : Yii::$app->request->get('pid');
                            return \app\modules\workflow\models\Process::find()
                                            ->where('ProcessID=' . $pid)
                                            ->processList()
                                            ->exists();
                        }
                    ],
                    [
                        'actions' => ['workflow-action-order', 'taskscount'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function actionTaskscount($clearCache=0) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $uid = Yii::$app->user->id;
        $oid = Yii::$app->session->get('ObsPosition');
        if($clearCache==0){
            return Yii::$app->cache->getOrSet(["ToDoCount$uid:$oid"], function(){
                    return \app\modules\workflow\models\ProcessEntity::workflowToDoCount();
            }, 3600);
        }
        return Yii::$app->cache->set(["ToDoCount$uid:$oid"],\app\modules\workflow\models\ProcessEntity::workflowToDoCount());
    }

    public function actionWorkflowgraph($pid) {
        return $this->renderPartial('_graph', array(
                    'pid' => $pid,
        ));
    }

    public function actionWorkflowActionOrder($actionId) {
        $actionOrderModels = \app\modules\workflow\models\ActionOrder::find()
                ->where(['ActionIDRef'=>$actionId])->all();
        return $this->renderPartial('_workflowActionOrder', array(
                    'actionOrderModels' => $actionOrderModels,
        ));
    }






}