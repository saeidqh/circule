<?php

namespace app\modules\workflow\models;

use app\modules\automation\models\EmployeeMembership;
use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityEmployeeMembershipSearch extends ProcessEntity {

    public $lastActionTitle;
    public $LastUserName;
    public $LastActionMessage;
    public $FullName;
    public $NationalCode;
    public $Mobile;
    public $created_at;
    public $CompanyIDRef;
    public $Position;
    public $IssueDate;
    // for workflow todo list
    public $EMPLOYEE_MEMBERSHIP_TODO_REPORT_TYPE = [
        [
            'name' => 'All',
            'icon' => 'fa-asterisk',
            'value' => 'All',
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
            [['ObjectIDRef','CompanyIDRef','Position', 'FullName', 'NationalCode','LastActionDateHijri','lastActionTitle','Mobile','created_at'], 'safe']
        ];
    }

    public function search($params, $type) {
        $actionTable = Action::tableName();
        $employeeMembershipTable = EmployeeMembership::tableName();
//        $networkTable = \app\modules\automation\models\Network::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->employeeMembership($type)
            ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef');

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'ObjectIDRef',
                    'LastActionDateHijri',
                    'LastActionMessage',
                    'CompanyIDRef' => [
                        'asc' => ["$employeeMembershipTable.CompanyIDRef" => SORT_ASC],
                        'desc' => ["$employeeMembershipTable.CompanyIDRef" => SORT_DESC],
                    ],
                    'RequestDate' => [
                        'asc' => ["$employeeMembershipTable.create_at" => SORT_ASC],
                        'desc' => ["$employeeMembershipTable.create_at" => SORT_DESC],
                    ],
                    'FullName' => [
                        'asc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_ASC],
                        'desc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_DESC],
                    ],
                    'NationalCode' => [
                        'asc' => ["$humanResourceTable.NationalCode" => SORT_ASC],
                        'desc' => ["$humanResourceTable.NationalCode" => SORT_DESC],
                    ],
                    'Position' => [
                        'asc' => ["concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)" => SORT_ASC],
                        'desc' => ["concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)" => SORT_DESC]
                    ],
                    'lastActionTitle' => [
                        'asc' => ["$actionTable.ActionTitle1" => SORT_ASC],
                        'desc' => ["$actionTable.ActionTitle1" => SORT_DESC],
                    ]
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

        $activeQuery
            ->andFilterWhere(['LIKE', "$employeeMembershipTable.EmployeeMembershipID", $this->ObjectIDRef])
            ->andFilterWhere(['LIKE', "$employeeMembershipTable.created_at", $this->created_at])
            ->andFilterWhere(['LIKE', "$employeeMembershipTable.CompanyIDRef", $this->CompanyIDRef])
            ->andFilterWhere(['LIKE', "concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)", $this->Position])
            ->andFilterWhere(['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", $this->FullName])
            ->andFilterWhere(['LIKE', "$humanResourceTable.NationalCode", $this->NationalCode])
            ->andFilterWhere(['LIKE', "$humanResourceTable.Mobile", $this->Mobile])
            ->andFilterWhere(['LIKE', "concat(lastUser.FirstName, ' ', lastUser.LastName)", $this->LastUserName])
            ->andFilterWhere(['LIKE', "LastActionDateHijri", $this->LastActionDateHijri])
            ->andFilterWhere(['or', ['LIKE', "$actionTable.ActionTitle1", str_replace('ی', 'ي', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ي', 'ی', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ک', 'ك', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ك', 'ک', $this->lastActionTitle)]
            ])
            ->andFilterWhere(['or', ['LIKE', "LastActionMessage", str_replace('ی', 'ي', $this->LastActionMessage)]
                , ['LIKE', "LastActionMessage", str_replace('ي', 'ی', $this->LastActionMessage)]
                , ['LIKE', "LastActionMessage", str_replace('ک', 'ك', $this->LastActionMessage)]
                , ['LIKE', "LastActionMessage", str_replace('ك', 'ک', $this->LastActionMessage)]
            ]);
        return $dataProvider;
    }

}
