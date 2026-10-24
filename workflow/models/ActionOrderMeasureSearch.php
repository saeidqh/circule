<?php

namespace app\modules\workflow\models;

use yii\data\ActiveDataProvider;
use Yii;

class ActionOrderMeasureSearch extends ActionOrderMeasure {

    public $measureTitle;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ActionOrderMeasureID','measureTitle'], 'safe', 'on' => 'search']
        ];
    }

    public function search($params, $aoid) {
        $actionOrderMeasureTable= \app\modules\workflow\models\ActionOrderMeasure::tableName();

        $activeQuery = self::find()
                ->where(['ActionOrderIDRef' => $aoid])
                ->andWhere("COALESCE($actionOrderMeasureTable.isDeleted,0)<>1");

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
                ->andFilterWhere(['LIKE', 'ActionOrderMeasureID', $this->ActionOrderMeasureID])
                ->andFilterWhere(['LIKE', "MeasureIDRef", $this->measureTitle]);
        
        return $dataProvider;
    }

}
