<?php

namespace app\modules\workflow\models;

use yii\data\ActiveDataProvider;
use Yii;

class ActionSearch extends Action {

    public $ProcessID;
    public $ProcessTitle;
    
    //use method search()
    public $companyTitle;
    public $processTypeTitle;
    /**
     * @inheritdoc
     */
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['report-item-action'] = ['processTypeTitle','ActionID', 'ActionTitle1', 'ActionTitle2', 'ProcessID', 'ProcessTitle'];
        return $scenarios;
    }

    public function rules() {
        return [
            [['ActionID', 'ActionTitle1', 'ActionTitle2', 'ProcessID', 'ProcessTitle', 'companyTitle', 'processTypeTitle'], 'safe', 'on' => 'search'],
            [['ActionID', 'ActionTitle1', 'ActionTitle2', 'ProcessID', 'ProcessTitle'], 'safe', 'on' => 'report-item-action']
        ];
    }

    public function searchProcess($params, $pid) {
        $activeQuery = Action::find()->process($pid)->andWhere("isDeleted is null or isDeleted <> 1");

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'ActionID', $this->ActionID])
                ->andFilterWhere(['LIKE', 'ActionTitle1', $this->ActionTitle1])
                ->andFilterWhere(['LIKE', 'ActionTitle2', $this->ActionTitle2]);
        return $dataProvider;
    }

    public function searchObs($params, $obsid) {
        $activeQuery = Action::find()->obs($obsid);


        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'ActionID', $this->ActionID])
                ->andFilterWhere(['LIKE', 'ActionTitle1', $this->ActionTitle1])
                ->andFilterWhere(['LIKE', 'ActionTitle2', $this->ActionTitle2]);
        return $dataProvider;
    }

    public function search($params) {
        $companyId = Yii::$app->session->get('CompanyID');
        $processTypeTable = ProcessType::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processTable = \app\modules\workflow\models\Process::tableName();
        $actionTable = \app\modules\workflow\models\Action::tableName();


        $activeQuery = Action::find()->joinWith(['process', 'process.type', 'process.company']);
        
        if(!\app\components\Security::isCandoAdmin()){
            $activeQuery->andWhere(['LIKE',$actionTable.'.companyIdList',$companyId]); 
        }

        

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', "$actionTable.ActionID", $this->ActionID])
                ->andFilterWhere(['LIKE', "$actionTable.ActionTitle2", $this->ActionTitle2])
                ->andFilterWhere(['LIKE', "$processTable.ProcessID", $this->ProcessID])
                ->andFilterWhere(['or', ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ی', 'ي', $this->processTypeTitle)]
                    , ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ي', 'ی', $this->processTypeTitle)]
                    , ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ک', 'ك', $this->processTypeTitle)]
                    , ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ك', 'ک', $this->processTypeTitle)]
                ])
                ->andFilterWhere(['LIKE', "$personTable.PersonTitle", $this->companyTitle])
                ->andFilterWhere(['LIKE', "$processTable.ProcessTitle", $this->ProcessTitle]);
        return $dataProvider;
    }


    // این متد برای فیلتر کردن دسترسی ها بر اساس کدهای فرآیندی است.
    public function searchProcessCode($params, $processCodeId) {
        $companyId = Yii::$app->session->get('CompanyID');
        $processTypeTable = ProcessType::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processTable = \app\modules\workflow\models\Process::tableName();
        $actionTable = \app\modules\workflow\models\Action::tableName();

        $processCodeActionTable = \app\modules\document\models\ProcessCodeAction::tableName();

        $activeQuery = Action::find()->joinWith(['process', 'process.type', 'process.company'])->alias('pa')
            ->leftJoin("$processCodeActionTable pca", "pca.ActionIDRef = pa.ActionID")
            ->where(['pca.CodeIDRef' => $processCodeId, 'pca.ActionType'=>'workflow']);

        if(!\app\components\Security::isCandoAdmin()){
            $activeQuery->andWhere(['LIKE',$actionTable.'.companyIdList',$companyId]);
        }



        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
            ->andFilterWhere(['LIKE', "pa.ActionID", $this->ActionID])
            ->andFilterWhere(['LIKE', "pa.ActionTitle2", $this->ActionTitle2])
            ->andFilterWhere(['LIKE', "$processTable.ProcessID", $this->ProcessID])
            ->andFilterWhere(['or', ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ی', 'ي', $this->processTypeTitle)]
                , ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ي', 'ی', $this->processTypeTitle)]
                , ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ک', 'ك', $this->processTypeTitle)]
                , ['LIKE', "$processTypeTable.proccessTypeTitle", str_replace('ك', 'ک', $this->processTypeTitle)]
            ])
            ->andFilterWhere(['LIKE', "$personTable.PersonTitle", $this->companyTitle])
            ->andFilterWhere(['LIKE', "$processTable.ProcessTitle", $this->ProcessTitle]);
        return $dataProvider;
    }

    public function searchReportItemActionSearch($params, $itemId) {
        $activeQuery = Action::find()
                ->reportItemAction($itemId);

        $dataProvider = new ActiveDataProvider([
            'query' => $activeQuery,
        ]);

        if (!$this->load($params) && $this->validate()) {
            return $dataProvider;
        }

        return $dataProvider;
    }

}
