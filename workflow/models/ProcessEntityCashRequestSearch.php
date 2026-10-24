<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;
use app\modules\cashRequest\models\CashRequest;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntityCashRequestSearch extends ProcessEntity {

    public $exportId;
    public $RequestDateHijri;
    public $Description;
    public $lastActionTitle;
    public $processTitle;
    public $lastUserName;
    public $createBy;
    // for workflow todo list 
    public $CASH_REQUEST_TODO_REPORT_TYPE = [
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

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function rules() {
        return [
            [['exportId', 'RequestDateHijri', 'Description', 'createBy', 'LastActionDateHijri', 'processTitle', 'lastActionTitle', 'lastUserName', 'LastActionMessage', 'LastActionAutoMessage'], 'safe']
        ];
    }

    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $userTable = \app\modules\account\models\User::tableName();
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->cashRequest($type)
                ->leftJoin(Action::tableName(), Action::tableName() . '.ActionID=' . ProcessEntity::tableName() . '.LastActionIDRef')
                ->leftJoin(CandoHelper::braketTable(User::tableName()), CandoHelper::braketTable(User::tableName()) . '.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef')
                ->leftJoin(['requesterUser' => $userTable], 'requesterUser.UserID = ' . CashRequest::tableName() . '.UserIDRef');
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'exportId' => [
                        'asc' => [ProcessEntity::tableName() . '.CashRequestIDRef' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.CashRequestIDRef' => SORT_DESC],
                    ],
                    'RequestDateHijri' => [
                        'asc' => [CashRequest::tableName() . '.RequestDateHijri' => SORT_ASC],
                        'desc' => [CashRequest::tableName() . '.RequestDateHijri' => SORT_DESC],
                    ],
                    'Description' => [
                        'asc' => [CashRequest::tableName() . '.Description' => SORT_ASC],
                        'desc' => [CashRequest::tableName() . '.Description' => SORT_DESC],
                    ],
                    'LastActionDateHijri' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_DESC],
                    ],
                    'LastActionMessage' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionMessage' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionMessage' => SORT_DESC],
                    ],
                    'lastUserName' => [
//                        'asc' => [CandoHelper::braketTable(User::tableName()) . '.UserName' => SORT_ASC],
//                        'desc' => [CandoHelper::braketTable(User::tableName()) . '.UserName' => SORT_DESC],
                        'asc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_ASC,],
                        'desc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_DESC],
                    ],
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionAutoMessage' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionAutoMessage' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionAutoMessage' => SORT_DESC],
                    ],
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'CashRequestIDRef', $this->exportId])
                ->andFilterWhere(['LIKE', CashRequest::tableName() . '.RequestDateHijri', $this->RequestDateHijri])
                ->andFilterWhere(['LIKE', CashRequest::tableName() . '.Description', $this->Description])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', Action::tableName() . '.ActionTitle2', $this->lastActionTitle])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['LIKE', 'LastActionAutoMessage', $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', CandoHelper::braketTable(User::tableName()) . '.UserName', $this->lastUserName])
                ->andFilterWhere([
                    'or',
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ی', 'ي', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ي', 'ی', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ک', 'ك', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ك', 'ک', $this->lastUserName)],
                ])
                ->andFilterWhere([
    'or',
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ی', 'ي', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ي', 'ی', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ک', 'ك', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ك', 'ک', $this->createBy)],
]);

        return $dataProvider;
    }
    
    public function searchByContract($params, $cid) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $processEntityTable=  \app\modules\workflow\models\ProcessEntity::tableName();
        $cashrequestAmountTable= \app\modules\cashRequest\models\CashRequestAmount::tableName();
        $userTable = \app\modules\account\models\User::tableName();
        
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->cashRequest("Graph")
            ->leftJoin(Action::tableName(), Action::tableName() . '.ActionID=' . ProcessEntity::tableName() . '.LastActionIDRef')
            ->leftJoin(CandoHelper::braketTable(User::tableName()), CandoHelper::braketTable(User::tableName()) . '.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef')
            ->andWhere(["in","$processEntityTable.CashRequestIDRef",(new \yii\db\Query())->from($cashrequestAmountTable)->andWhere(["RecipientContractIDRef"=>$cid])->select("CashRequestIDRef")]);
        
        
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'exportId' => [
                        'asc' => [ProcessEntity::tableName() . '.CashRequestIDRef' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.CashRequestIDRef' => SORT_DESC],
                    ],
                    'RequestDateHijri' => [
                        'asc' => [CashRequest::tableName() . '.RequestDateHijri' => SORT_ASC],
                        'desc' => [CashRequest::tableName() . '.RequestDateHijri' => SORT_DESC],
                    ],
                    'Description' => [
                        'asc' => [CashRequest::tableName() . '.Description' => SORT_ASC],
                        'desc' => [CashRequest::tableName() . '.Description' => SORT_DESC],
                    ],
                    'LastActionDateHijri' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_DESC],
                    ],
                    'LastActionMessage' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionMessage' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionMessage' => SORT_DESC],
                    ],
                    'lastUserName' => [
//                        'asc' => [CandoHelper::braketTable(User::tableName()) . '.UserName' => SORT_ASC],
//                        'desc' => [CandoHelper::braketTable(User::tableName()) . '.UserName' => SORT_DESC],
                        'asc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_ASC,],
                        'desc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_DESC],
                    ],
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionAutoMessage' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionAutoMessage' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionAutoMessage' => SORT_DESC],
                    ],
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'CashRequestIDRef', $this->exportId])
                ->andFilterWhere(['LIKE', CashRequest::tableName() . '.RequestDateHijri', $this->RequestDateHijri])
                ->andFilterWhere(['LIKE', CashRequest::tableName() . '.Description', $this->Description])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', Action::tableName() . '.ActionTitle2', $this->lastActionTitle])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['LIKE', 'LastActionAutoMessage', $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', CandoHelper::braketTable(User::tableName()) . '.UserName', $this->lastUserName])
               ->andFilterWhere([
                    'or',
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ی', 'ي', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ي', 'ی', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ک', 'ك', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ك', 'ک', $this->lastUserName)],
                              ])
                ->andFilterWhere([
    'or',
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ی', 'ي', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ي', 'ی', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ک', 'ك', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ك', 'ک', $this->createBy)],
]);

        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $userTable = \app\modules\account\models\User::tableName();
        
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->cashRequest('CashRequestWorkflowReport')->reportItem($itemId)
                ->leftJoin(Action::tableName(), Action::tableName() . '.ActionID=' . ProcessEntity::tableName() . '.LastActionIDRef')
                ->leftJoin(Process::tableName(), Action::tableName() . '.ProcessIDRef=' . Process::tableName() . '.ProcessID')
                ->leftJoin(CandoHelper::braketTable(User::tableName()), CandoHelper::braketTable(User::tableName()) . '.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef');



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
                ->andFilterWhere(['LIKE', 'CashRequestIDRef', $this->exportId])
                ->andFilterWhere(['LIKE', CashRequest::tableName() . '.RequestDateHijri', $this->RequestDateHijri])
                ->andFilterWhere(['or', ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ی', 'ي', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ي', 'ی', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ک', 'ك', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ك', 'ک', $this->processTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', CashRequest::tableName() . '.Description', str_replace('ی', 'ي', $this->Description)]
                    , ['LIKE', CashRequest::tableName() . '.Description', str_replace('ي', 'ی', $this->Description)]
                    , ['LIKE', CashRequest::tableName() . '.Description', str_replace('ک', 'ك', $this->Description)]
                    , ['LIKE', CashRequest::tableName() . '.Description', str_replace('ك', 'ک', $this->Description)]
                ])
                ->andFilterWhere(['or', ['LIKE', 'LastActionMessage', str_replace('ی', 'ي', $this->LastActionMessage)]
                    , ['LIKE', 'LastActionMessage', str_replace('ي', 'ی', $this->LastActionMessage)]
                    , ['LIKE', 'LastActionMessage', str_replace('ک', 'ك', $this->LastActionMessage)]
                    , ['LIKE', 'LastActionMessage', str_replace('ك', 'ک', $this->LastActionMessage)]
                ])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionAutoMessage', $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', CandoHelper::braketTable(User::tableName()) . '.UserName', $this->lastUserName])
               ->andFilterWhere([
                    'or',
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ی', 'ي', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ي', 'ی', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ک', 'ك', $this->lastUserName)],
                    ['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", str_replace('ك', 'ک', $this->lastUserName)],
                             ])
                ->andFilterWhere([
    'or',
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ی', 'ي', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ي', 'ی', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ک', 'ك', $this->createBy)],
    ['LIKE', "concat(requesterUser.FirstName, ' ', requesterUser.LastName)", str_replace('ك', 'ک', $this->createBy)],
]);

        return $dataProvider;
    }

}
