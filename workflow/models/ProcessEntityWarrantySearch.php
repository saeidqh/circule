<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntityWarrantySearch extends ProcessEntity {

    public $contractNo;
    public $warrantySubject;
    public $warrantyType;
    public $WARRANTY_TODO_REPORT_TYPE = [
        [
            'name' => 'All',
            'icon' => 'fa-asterisk',
            'value' => 'All',
        ],
        [
            'name' => 'Finished',
            'icon' => 'im-checkmark2',
            'value' => 'Finished',
        ],
        [
            'name' => 'Not Finished',
            'icon' => 'en-ellipsis',
            'value' => 'NotFinished',
        ],
        [
            'name' => 'To Do',
            'icon' => 'fa-list-ul',
            'value' => 'ToDo',
        ],
    ];

    public function rules() {
        return [
            [['contractNo', 'warrantySubject', 'warrantyType', 'LastActionDateHijri', 'LastActionMessage',], 'safe']
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->warranty($type)
                ->leftJoin(\app\modules\warranty\models\WarrantySubject::tableName(), \app\modules\warranty\models\WarrantySubject::tableName(). '.warrantySubjectId='.\app\modules\warranty\models\Warranty::tableName().'.warrantySubjectIdRef')
                ->leftJoin(\app\modules\warranty\models\WarrantyType::tableName(), \app\modules\warranty\models\WarrantyType::tableName(). '.warrantyTypeId='.\app\modules\warranty\models\Warranty::tableName().'.warrantyTypeIdRef')
                ->leftJoin(\app\modules\contract\models\Contract::tableName(), \app\modules\contract\models\Contract::tableName(). '.ContractID='.\app\modules\warranty\models\Warranty::tableName().'.contractIdRef');

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
                ->andFilterWhere(['LIKE', \app\modules\contract\models\Contract::tableName(). '.ContractNo', $this->contractNo])
                ->andFilterWhere(['LIKE', \app\modules\warranty\models\WarrantySubject::tableName(). '.warrantySubjectTitle', $this->warrantySubject])
                ->andFilterWhere(['LIKE', \app\modules\warranty\models\WarrantyType::tableName(). '.warrantyTypeTitle', $this->warrantyType])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage]);
        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->warranty('All')->reportItem($itemId)
                ->leftJoin(\app\modules\warranty\models\WarrantySubject::tableName(), \app\modules\warranty\models\WarrantySubject::tableName(). '.warrantySubjectId='.\app\modules\warranty\models\Warranty::tableName().'.warrantySubjectIdRef')
                ->leftJoin(\app\modules\warranty\models\WarrantyType::tableName(), \app\modules\warranty\models\WarrantyType::tableName(). '.warrantyTypeId='.\app\modules\warranty\models\Warranty::tableName().'.warrantyTypeIdRef')
                ->leftJoin(\app\modules\contract\models\Contract::tableName(), \app\modules\contract\models\Contract::tableName(). '.ContractID='.\app\modules\warranty\models\Warranty::tableName().'.contractIdRef');

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
                ->andFilterWhere(['LIKE', \app\modules\contract\models\Contract::tableName(). '.ContractNo', $this->contractNo])
                ->andFilterWhere(['LIKE', \app\modules\warranty\models\WarrantySubject::tableName(). '.warrantySubjectTitle', $this->warrantySubject])
                ->andFilterWhere(['LIKE', \app\modules\warranty\models\WarrantyType::tableName(). '.warrantyTypeTitle', $this->warrantyType])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage]);
        return $dataProvider;
    }

}
