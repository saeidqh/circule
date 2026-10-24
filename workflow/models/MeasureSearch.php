<?php

namespace app\modules\workflow\models;

use yii\data\ActiveDataProvider;
use Yii;

class MeasureSearch extends Measure {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['MeasureID','MeasureTitle'], 'safe', 'on' => 'search']
        ];
    }

    public function search($params) {

        $activeQuery = self::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);
//        $this->load($params);

        if ((!$this->load($params))) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'MeasureID', $this->MeasureID])
                ->andFilterWhere(['LIKE', "MeasureTitle", $this->MeasureTitle]);
        
        return $dataProvider;
    }

}
