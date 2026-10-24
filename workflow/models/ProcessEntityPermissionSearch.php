<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\contract\models\Contract;
use app\modules\invoice\models\typeIds;
use app\modules\contract\models\ContractType;
use app\modules\OBS\models\Person;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntityPermissionSearch extends ProcessEntity {

    public $contractNo;
    public $contractSubject;
    public $subContractorTitle;
    public $contractTypeTitle;
    public $Title;
    public $cutOffDateHijri;
    public $typeId;
    public $lastActionTitle;
    public $processTitle;
    // for workflow todo list 
    public $PERMISSION_TODO_REPORT_TYPE = [
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
            [['permissionId','contractNo', 'contractSubject', 'subContractorTitle', 'contractTypeTitle', 'Title', 'cutOffDateHijri', 'typeId', 'processTitle', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage',], 'safe']
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $contractTable=Contract::tableName();
        $permissionTable=  \app\modules\permission\models\Permission::tableName();
        $permissionTypesTable= \app\modules\permission\models\PermissionTypes::tableName();
        $personTable= Person::tableName();

        
        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->permission($type)
                ->leftJoin($permissionTypesTable,"$permissionTypesTable.id=$permissionTable.typeId")
                ->innerJoin($contractTable,"$contractTable.ContractID=$permissionTable.contractId")
                ->leftJoin(["subContractorTable"=>"$personTable"],"subContractorTable.PersonID=$contractTable.SubContractorPersonIDRef")
                ->joinWith(['process', 'lastAction'])
                ;
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
                    'contractSubject',
                    'permissionId',
                    'contractNo' => [
                        'asc' => ["$contractTable.ContractNo" => SORT_ASC],
                        'desc' => ["$contractTable.ContractNo" => SORT_DESC],
                    ],
                    'subContractorTitle' => [
                        'asc' => ['subContractorTable.PersonTitle' => SORT_ASC],
                        'desc' => ['subContractorTable.PersonTitle' => SORT_DESC]
                    ],
                    'Title' => [
                        'asc' => ["$permissionTable.title" => SORT_ASC],
                        'desc' => ["$permissionTable.title" => SORT_DESC]
                    ],
                    'contractTypeTitle',
                    'cutOffDateHijri',
                    'typeId' => [
                        'asc' => ["$permissionTypesTable.title" => SORT_ASC],
                        'desc' => ["$permissionTypesTable.title" => SORT_DESC],
                    ],
                    'processTitle',
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionDateHijri',
                    'LastActionMessage'],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', ProcessEntity::tableName() . '.permissionId', $this->permissionId])
                ->andFilterWhere(['LIKE', Contract::tableName() . '.ContractNo', $this->contractNo])
                ->andFilterWhere(['or', ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ی', 'ي', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ي', 'ی', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ک', 'ك', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ك', 'ک', $this->contractSubject)]
                ])
                ->andFilterWhere(['or', ['LIKE', 'subContractorTable.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                    , ['LIKE', 'subContractorTable.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                    , ['LIKE', 'subContractorTable.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                    , ['LIKE', 'subContractorTable.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', "$permissionTable.title", str_replace('ی', 'ي', $this->Title)]
                    , ['LIKE', "$permissionTable.title", str_replace('ي', 'ی', $this->Title)]
                    , ['LIKE', "$permissionTable.title", str_replace('ک', 'ك', $this->Title)]
                    , ['LIKE', "$permissionTable.title", str_replace('ك', 'ک', $this->Title)]
                ])
                ->andFilterWhere(['LIKE', 'cutOffDateHijri', $this->cutOffDateHijri])
                ->andFilterWhere(['LIKE', "$permissionTypesTable.title", $this->typeId])
                ->andFilterWhere(['or', ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['or', ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ی', 'ي', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ي', 'ی', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ک', 'ك', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ك', 'ک', $this->processTitle)]
        ]);
        return $dataProvider;
    }

    
    public function searchByContract($params, $cid) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $contractTable=Contract::tableName();
        $permissionTable=  \app\modules\permission\models\Permission::tableName();
        $permissionTypesTable= \app\modules\permission\models\PermissionTypes::tableName();
        $personTable= Person::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->permission('Graph')
                ->leftJoin($permissionTypesTable,"$permissionTypesTable.id=$permissionTable.typeId")
                ->innerJoin($contractTable,"$contractTable.ContractID=$permissionTable.contractId")
                ->leftJoin(["subContractorTable"=>"$personTable"],"subContractorTable.PersonID=$contractTable.SubContractorPersonIDRef")
                ->joinWith(['process', 'lastAction'])
                ->andWhere(["$permissionTable.contractId"=>$cid])
                ;

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'contractSubject',
                    'permissionId',
                    'contractNo' => [
                        'asc' => ["$contractTable.ContractNo" => SORT_ASC],
                        'desc' => ["$contractTable.ContractNo" => SORT_DESC],
                    ],
                    'subContractorTitle' => [
                        'asc' => ['subContractorTable.PersonTitle' => SORT_ASC],
                        'desc' => ['subContractorTable.PersonTitle' => SORT_DESC]
                    ],
                    'Title' => [
                        'asc' => ["$permissionTable.title" => SORT_ASC],
                        'desc' => ["$permissionTable.title" => SORT_DESC]
                    ],
                    'contractTypeTitle',
                    'cutOffDateHijri',
                    'typeId' => [
                        'asc' => ["$permissionTypesTable.title" => SORT_ASC],
                        'desc' => ["$permissionTypesTable.title" => SORT_DESC],
                    ],
                    'processTitle',
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionDateHijri',
                    'LastActionMessage'],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', ProcessEntity::tableName() . '.permissionId', $this->permissionId])
                ->andFilterWhere(['LIKE', Contract::tableName() . '.ContractNo', $this->contractNo])
                ->andFilterWhere(['or', ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ی', 'ي', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ي', 'ی', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ک', 'ك', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ك', 'ک', $this->contractSubject)]
                ])
                ->andFilterWhere(['or', ['LIKE', 'subContractorTable.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                    , ['LIKE', 'subContractorTable.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                    , ['LIKE', 'subContractorTable.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                    , ['LIKE', 'subContractorTable.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', "$permissionTable.title", str_replace('ی', 'ي', $this->Title)]
                    , ['LIKE', "$permissionTable.title", str_replace('ي', 'ی', $this->Title)]
                    , ['LIKE', "$permissionTable.title", str_replace('ک', 'ك', $this->Title)]
                    , ['LIKE', "$permissionTable.title", str_replace('ك', 'ک', $this->Title)]
                ])
                ->andFilterWhere(['LIKE', 'cutOffDateHijri', $this->cutOffDateHijri])
                ->andFilterWhere(['LIKE', "$permissionTypesTable.title", $this->typeId])
                ->andFilterWhere(['or', ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['or', ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ی', 'ي', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ي', 'ی', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ک', 'ك', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ك', 'ک', $this->processTitle)]
        ]);
        return $dataProvider;
    }

    
}