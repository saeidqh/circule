<?php

namespace app\modules\workflow\models;

use yii\data\ActiveDataProvider;
use Yii;

class ActionOrderMeasureValueSearch extends ActionOrderMeasureValue {

    public $measureTitle;
    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ID','measureTitle', 'description', 'value'], 'safe', 'on' => 'search']
        ];
    }

    public function search($params) {
        $activeQuery = self::find()
                ->where(['ActionEntityIDRef' => $this->ActionEntityIDRef]);

        $dataProvider = new ActiveDataProvider([
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);
        
        return $dataProvider;
    }

}
