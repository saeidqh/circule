<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;
use app\modules\OBS\models\Person;
use app\modules\capital\models\CapitalBalance;

class ProcessEntityCapitalSearch extends ProcessEntity {

    public $capitalId;
    public $creatorUserName;
    public $creatorObs;
    public $balanceDescription;
    public $lastActionTitle;
    public $lastUserName;
    // for workflow todo list 
    public $CAPITAL_TODO_REPORT_TYPE = [
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
            [["LastActionMessage",'lastUserName','capitalId', 'creatorUserName', 'creatorObs', 'balanceDescription', 'LastActionDateHijri', 'lastActionTitle'], 'safe',"on"=>"search"]
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }
    
    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $processEntityTable = ProcessEntity::tableName();
        
        $capitalBalanceTable = CapitalBalance::tableName();
        $capitalBalanceAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
        $personTable = Person::tableName();
        $userTable = User::tableName();
        $actionTable = Action::tableName();

        $activeQuery = ProcessEntity::find()->capital($type)
                ->leftJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
                ->leftJoin( "$userTable as createorUser", "createorUser.UserID=$capitalBalanceTable.userIdRef")
                ->leftJoin($personTable, "$personTable.PersonID=$capitalBalanceTable.OBSCategory3IDRef")
                ->leftJoin($userTable, "$userTable.UserID = $processEntityTable.LastUserIDRef")
                ->addSelect([
                    "$processEntityTable.*",
                    "balanceAmounts" => "(SELECT sum(balanceAmount) FROM $capitalBalanceAmountTable where capitalBalanceIdRef = $capitalBalanceTable.capitalBalanceId)"
                ]);

//        echo $activeQuery->createCommand()->rawSql;
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'capitalId' => [
                        'asc' => [ProcessEntity::tableName() . '.capitalBalanceIdRef' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.capitalBalanceIdRef' => SORT_DESC],
                    ],
//                    'creatorUserName' => [
//                        'asc' => ['creatorUser.UserName' => SORT_ASC],
//                        'desc' => ['creatorUser.UserName' => SORT_DESC],
//                    ],
                    'balanceDescription' => [
                        'asc' => [CapitalBalance::tableName() . '.balanceDescription' => SORT_ASC],
                        'desc' => [CapitalBalance::tableName() . '.balanceDescription' => SORT_DESC],
                    ],
                    'LastActionDateHijri' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_DESC],
                    ],
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'lastUserName' => [
//                        'asc' => ["$userTable.UserName" => SORT_ASC],
//                        'desc' => ["$userTable.UserName" => SORT_DESC],
                        'asc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_ASC,],
                        'desc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_DESC],
                    ],
                    'LastActionMessage',
                ],
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'capitalBalanceIdRef', $this->capitalId])
//                ->andFilterWhere(['LIKE', 'createorUser.UserName', $this->creatorUserName])
                ->andFilterWhere(['LIKE', "concat(createorUser.FirstName, ' ', createorUser.LastName)", $this->creatorUserName])
                ->andFilterWhere(['LIKE', Person::tableName() . '.PersonTitle', $this->creatorObs])
                ->andFilterWhere(['LIKE', CapitalBalance::tableName() . '.balanceDescription', $this->balanceDescription])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
//                ->andFilterWhere(['LIKE', "$userTable.UserName", $this->lastUserName])
                ->andFilterWhere(['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->lastUserName])
                ->andFilterWhere(['LIKE', Action::tableName() . '.ActionTitle2', $this->lastActionTitle]);

        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $type = 'All';
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $processEntityTable = ProcessEntity::tableName();
        
        $capitalBalanceTable = CapitalBalance::tableName();
        $capitalBalanceAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
        $personTable = Person::tableName();
        $userTable = User::tableName();
        $actionTable = Action::tableName();

        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->capital($type)->reportItem($itemId)
                ->leftJoin(Action::tableName(), Action::tableName() . '.ActionID=' . ProcessEntity::tableName() . '.LastActionIDRef')
                ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as createorUser', 'createorUser.UserID=' . CapitalBalance::tableName() . '.userIdRef')
                ->leftJoin(Person::tableName(), Person::tableName() . '.PersonID=' . CapitalBalance::tableName() . '.OBSCategory3IDRef')
                ->addSelect([
                    "$processEntityTable.*",
                    "balanceAmounts" => "(SELECT sum(balanceAmount) FROM $capitalBalanceAmountTable where capitalBalanceIdRef = $capitalBalanceTable.capitalBalanceId)"
                ])
                ;



        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'capitalId' => [
                        'asc' => [ProcessEntity::tableName() . '.capitalBalanceIdRef' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.capitalBalanceIdRef' => SORT_DESC],
                    ],
//                    'creatorUserName' => [
//                        'asc' => ['creatorUser.UserName' => SORT_ASC],
//                        'desc' => ['creatorUser.UserName' => SORT_DESC],
//                    ],
                    'balanceDescription' => [
                        'asc' => [CapitalBalance::tableName() . '.balanceDescription' => SORT_ASC],
                        'desc' => [CapitalBalance::tableName() . '.balanceDescription' => SORT_DESC],
                    ],
                    'LastActionDateHijri' => [
                        'asc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_ASC],
                        'desc' => [ProcessEntity::tableName() . '.LastActionDateHijri' => SORT_DESC],
                    ],
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                ],
            ],
        ]);

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', 'capitalBalanceIdRef', $this->capitalId])
//                ->andFilterWhere(['LIKE', 'createorUser.UserName', $this->creatorUserName])
                ->andFilterWhere(['LIKE', "concat(createorUser.FirstName, ' ', createorUser.LastName)", $this->creatorUserName])
                ->andFilterWhere(['LIKE', Person::tableName() . '.PersonTitle', $this->creatorObs])
                ->andFilterWhere(['LIKE', CapitalBalance::tableName() . '.balanceDescription', $this->balanceDescription])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', Action::tableName() . '.ActionTitle2', $this->lastActionTitle]);

        return $dataProvider;
    }

}
