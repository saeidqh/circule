<?php

namespace app\modules\workflow\models;

use yii\data\ActiveDataProvider;
use Yii;

class ActionOrderSearch extends ActionOrder {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ActionIDRef','NextActionIDRef'], 'safe', 'on' => 'search']
        ];
    }

    public function search($params,$pid) {
        $activeQuery = self::find()->process($pid);

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination'=>[
                'pageSize'=> Yii::$app->params['pageSize'],
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

       
        $activeQuery
                ->andFilterWhere(['LIKE', 'ActionIDRef', $this->ActionIDRef])
                ->andFilterWhere(['LIKE', 'NextActionIDRef', $this->NextActionIDRef]);

        return $dataProvider;
    }

}
