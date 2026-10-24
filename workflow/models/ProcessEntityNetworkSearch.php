<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityNetworkSearch extends ProcessEntity {

    public $lastActionTitle;
    public $LastUserName;
    public $LastActionMessage;
    public $FullName;
    public $NationalCode;
    public $CompanyIDRef;
    public $Assistance;
    public $Management;
    public $Position;
    public $RequestDate;
    public $ExpireRemain;
    // for workflow todo list
    public $NETWORK_TODO_REPORT_TYPE = [
        [
            'name' => 'All',
            'icon' => 'fa-asterisk',
            'value' => 'All',
        ],
        /* [
          'name' => 'Finished',
          'icon' => 'im-checkmark2',
          'value' => 'Finished',
          ],
          [
          'name' => 'Not Finished',
          'icon' => 'en-ellipsis',
          'value' => 'NotFinished',
          ], */
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
            [['NetworkIDRef', 'Assistance', 'Position', 'CompanyIDRef', 'FullName', 'NationalCode', 'LastUserName', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage', 'RequestDate'], 'safe']
//            [['TenderIDRef', 'CompanyIDRef', 'subject','tenderType', 'LastUserName','lastActionTitle','LastActionDateHijri','LastActionMessage'], 'safe']
        ];
    }

    public function search($params, $type) {
        $actionTable = Action::tableName();
        $networkTable = \app\modules\automation\models\Network::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->network($type)
                ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef');

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'NetworkIDRef',
                    'LastActionDateHijri',
                    'LastActionMessage',
                    'CompanyIDRef' => [
                        'asc' => ["$networkTable.CompanyIDRef" => SORT_ASC],
                        'desc' => ["$networkTable.CompanyIDRef" => SORT_DESC],
                    ],
                    'RequestDate' => [
                        'asc' => ["$networkTable.RequestDate" => SORT_ASC],
                        'desc' => ["$networkTable.RequestDate" => SORT_DESC],
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
                ->andFilterWhere(['LIKE', "$networkTable.NetworkID", $this->NetworkIDRef])
                ->andFilterWhere(['LIKE', "$networkTable.CompanyIDRef", $this->CompanyIDRef])
                ->andFilterWhere(['LIKE', "concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)", $this->Position])
                ->andFilterWhere(['or', ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ي', 'ی', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ک', 'ك', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ك', 'ک', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', str_replace('ک', 'ك', $this->FullName))]
                ])
                ->andFilterWhere(['LIKE', "$humanResourceTable.NationalCode", $this->NationalCode])
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
