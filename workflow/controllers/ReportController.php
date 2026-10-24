<?php

namespace app\modules\workflow\controllers;
use Yii;
use yii\web\Controller;
use app\modules\account\models\User;
use app\components\Console;

class ReportController extends Controller {

    public $defaultAction = "user-activity";
    public $access = null;

    public function behaviors() {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['user-activity'],
                'rules' => [
                    // allow authenticated users
                    [
                        'actions' => ['user-activity'],
                        'roles' => ['@'],
                        'allow' => true,
                        'matchCallback' => function($rule, $action, $exteraData = []) {
                            return \app\access\AccessControl::isAccess(['formAction', 'aid' => 348]);
                        },
                    ],
                ],
            ],
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'deleteObsProcessDuties' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionUserActivity($viewMode='perAction') {
        $userAcivitySearchModel = new \app\modules\workflow\models\UserActivitySearch();
        if(!$userAcivitySearchModel->load(Yii::$app->request->get())){

            
            $date = new \app\components\jDateTime(true, true, 'Asia/Tehran');
            $userAcivitySearchModel->toDate = $date->date("Y/m/d",time(), false);
            $userAcivitySearchModel->fromDate = $date->date("Y/m/d", strtotime( date('Y/m/d',((round(microtime(true))) -15552000))), false);
        }
        $userAcivitySearchModel->setScenario('search');
        $userAcivityDataProvider = $userAcivitySearchModel->search(Yii::$app->request->queryParams);
        $totalUserAcivityDataProvider = $userAcivitySearchModel->totalActivity(Yii::$app->request->queryParams);
        
        // total graph data
        $graphArrayData = $userAcivityDataProvider->getModels();
        Console::log($graphArrayData);
        $graphData ='';
        foreach ($graphArrayData as $row){
            $graphData .=(($graphData != '')?',':'')."['".$row['uName'].'('.$row['obsTitle'].')'."',".$row['total_Process']."]";
        }
        $graphData = '['.$graphData.']';
        Console::log($graphData);
        
        // total graph data
        return $this->render('userActivity', array(
                'userAcivitySearchModel'=>$userAcivitySearchModel,
                'userAcivityDataProvider'=>$userAcivityDataProvider,
                'totalUserAcivityDataProvider'=>$totalUserAcivityDataProvider,
                'viewMode'=>$viewMode,
                'graphData'=>$graphData,
        ));
        
    }
    
    public function actionUserActivityGraph($uid,$obsId) {
        $userModel = User::find()->where(['UserID'=>$uid])->one();
        $obsModel = \app\modules\OBS\models\Obsnew::find()->where(['obsId'=>$obsId])->one();
        
        $userFullName = $userModel->FirstName. ' ' . $userModel->LastName;
        $obsTitle  = $obsModel->obsTitle;
                
        $userAcivitySearchModel = new \app\modules\workflow\models\UserActivitySearch();
        if($userAcivitySearchModel->load(Yii::$app->request->get())){
            $userAcivitySearchModel->setScenario('search');
            $userActivityChartData = $userAcivitySearchModel->userActivity(Yii::$app->request->queryParams,$uid,$obsId);
        }
        
        return $this->renderAjax('_userActivityChart', [
            'userActivityChartData'=>$userActivityChartData,
            'userFullName'=>$userFullName,
            'obsTitle'=>$obsTitle,
        ]);
        
    }

}
