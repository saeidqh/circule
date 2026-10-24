<?php

namespace app\modules\workflow\models;

use app\modules\automation\models\EmployeeMembership;
use app\modules\automation\models\ForeignMission;
use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityForeignMissionSearch extends ProcessEntity {

    public $lastActionTitle;
    public $LastUserName;
    public $LastActionMessage;

    public $created_at;
    public $CompanyIDRef;
    public $IssueDate;
    public $EndDate;
    public $StartDate;
    public $country;

    // for workflow todo list
    public $FOREIGN_MISSION_TODO_REPORT_TYPE = [
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
            [['ObjectIDRef','CompanyIDRef','LastActionDateHijri','lastActionTitle','created_at','EndDate','StartDate','country'], 'safe']
        ];
    }

    public function search($params, $type) {
        $actionTable = Action::tableName();
        $foreignMissionTable = ForeignMission::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->foreignMission($type)
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
                        'asc' => ["$foreignMissionTable.CompanyIDRef" => SORT_ASC],
                        'desc' => ["$foreignMissionTable.CompanyIDRef" => SORT_DESC],
                    ],
                    'RequestDate' => [
                        'asc' => ["$foreignMissionTable.created_at" => SORT_ASC],
                        'desc' => ["$foreignMissionTable.created_at" => SORT_DESC],
                    ],
                    'EndDate' => [
                        'asc' => ["$foreignMissionTable.EndDate" => SORT_ASC],
                        'desc' => ["$foreignMissionTable.EndDate" => SORT_DESC],
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
            ->andFilterWhere(['LIKE', "$foreignMissionTable.MissionID", $this->ObjectIDRef])
            ->andFilterWhere(['LIKE', "$foreignMissionTable.created_at", $this->created_at])
            ->andFilterWhere(['LIKE', "$foreignMissionTable.EndDate", $this->EndDate])
            ->andFilterWhere(['LIKE', "$foreignMissionTable.StartDate", $this->StartDate])
            ->andFilterWhere(['LIKE', "$foreignMissionTable.CompanyIDRef", $this->CompanyIDRef])
            ->andFilterWhere(['LIKE', "$foreignMissionTable.CompanyIDRef", $this->CompanyIDRef])
            ->andFilterWhere(['LIKE', "country.persianName", $this->country])

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
