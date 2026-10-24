<?php

namespace app\modules\workflow\models;

use yii\data\ActiveDataProvider;
use Yii;

class ProcessSearch extends Process {

    public $ProccessTypeTitle;
    public $CompanyTitle;

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function rules() {
        return [
            [['ProcessID', 'ProcessTitle', 'ProccessTypeIDRef', 'OBSCategory3CompanyIDRef', 'parentProcessIdRef', 'ProccessTypeTitle', 'CompanyTitle'
                ], 'safe', 'on' => 'search']
        ];
    }

    public function search($params) {
        $activeQuery = Process::find()->joinWith(['type','company'])->processList();

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);
        
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'ProcessID', $this->ProcessID])
                ->andFilterWhere(['LIKE', 'ProcessTitle', $this->ProcessTitle])
                ->andFilterWhere(['LIKE', ProcessType::tableName() . '.ProccessTypeTitle', $this->ProccessTypeTitle])
                ->andFilterWhere(['LIKE', \app\modules\OBS\models\Person::tableName() . '.PersonTitle', $this->CompanyTitle]);
        return $dataProvider;
    }

}
