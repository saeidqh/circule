<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\OBS\models\Person;
use app\modules\OBS\models\PersonCountry;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntitySourcingSearch extends ProcessEntity {

    public $countryName;
    public $contactName;
    public $PersonTitle;
    public $personType;
    public $registrationNumber;
    public $LastActionDateHijri;
    public $actionTitle;
    public $economicCode;
    public $nationalCode;
    public $countryIDRef;
    public $companyNationalCode;
    // for workflow todo list 
    public $SOURCING_TODO_REPORT_TYPE = [
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
            [['countryIDRef', 'PersonID', 'PersonTitle', 'personType', 'registrationNumber', 'economicCode', 'nationalCode', 'companyNationalCode', 'lastUpdateHijri', 'personStatus', 'activities', 'lastName', 'firstName', 'companyName', 'companyNameEn', 'PersonCategoryIDRef', 'registrationPlace', 'email', 'mobile', 'companyPhone', 'companyFax', 'contactPerson', 'directorManager', 'contactName','LastActionDateHijri','actionTitle'], 'safe', 'on' => 'search'],
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type) {
        $personTable = Person::tableName();
        $personCountryTable = PersonCountry::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()
            ->sourcing($type)
            ->innerJoin($personTable, "$personTable.PersonID=$processEntityTable.PersonID")
            ->leftJoin($personCountryTable, "$personCountryTable.countryID=$personTable.countryIDRef")
            ->leftJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
            ->andWhere($personTable.".isDeleted is null or ".$personTable.".isDeleted != 1");

//        echo $activeQuery->createCommand()->rawSql;
//        die();

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'countryIDRef',
                    'PersonID' => [
                        'asc' => [ProcessEntity::tableName() . '.PersonID' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.PersonID' => SORT_DESC]
                    ],
                    'PersonTitle',
                    'personType',
                    'actionTitle',
                    'LastActionDateHijri',
                    'registrationNumber' => [
                        'asc' => [Person::tableName() . '.registrationNumber' => SORT_ASC],
                        'desc' => [Person::tableName() . '.registrationNumber' => SORT_DESC]
                    ],
                    'economicCode' => [
                        'asc' => [Person::tableName() . '.economicCode' => SORT_ASC],
                        'desc' => [Person::tableName() . '.economicCode' => SORT_DESC]
                    ],
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

        $activeQuery
            ->andFilterWhere(['LIKE', ProcessEntity::tableName() . '.PersonID', $this->PersonID])
            ->andFilterWhere(['or', ['LIKE', "PersonTitle", str_replace('ی', 'ي', $this->PersonTitle)]
                , ['LIKE', "PersonTitle", str_replace('ي', 'ی', $this->PersonTitle)]
                , ['LIKE', "PersonTitle", str_replace('ک', 'ك', $this->PersonTitle)]
                , ['LIKE', "PersonTitle", str_replace('ك', 'ک', $this->PersonTitle)]
            ])
            ->andFilterWhere(['LIKE', 'PersonType', $this->personType])
            ->andFilterWhere(['LIKE', $actionTable.'.actionTitle1', $this->actionTitle])
            ->andFilterWhere(['LIKE', ProcessEntity::tableName().'.LastActionDateHijri', $this->LastActionDateHijri])
            ->andFilterWhere(['LIKE', 'registrationNumber', $this->registrationNumber])
            ->andFilterWhere(['LIKE', 'economicCode', $this->economicCode])
            ->andFilterWhere(['or',
                ['LIKE', 'nationalCode', $this->nationalCode],
                ['LIKE', 'companyNationalCode', $this->nationalCode]])
            ->andFilterWhere(['LIKE', 'isoCode', $this->countryIDRef]);

//        echo $activeQuery->createCommand()->rawSql;
//        die();
        return $dataProvider;
    }

}
