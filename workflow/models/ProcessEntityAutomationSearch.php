<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityAutomationSearch extends ProcessEntity {

    public $lastActionTitle;
    public $LastUserName;
    public $LastActionMessage;
    public $FullName;
    public $NationalCode;
    public $tenderType;
    public $CompanyIDRef;
    public $Assistance;
    public $ObsId;
    public $Position;
    public $PositionStatus;
    public $RequestDate;
    public $Project;
    public $ExpireRemain;
    public $SettlementDate;
    // for workflow todo list
    public $AUTOMATION_TODO_REPORT_TYPE = [
        [
            'name' => 'All',
            'icon' => 'fa-asterisk',
            'value' => 'All',
        ],
//        [
//            'name' => 'Finished',
//            'icon' => 'im-checkmark2',
//            'value' => 'Finished',
//        ],
//        [
//            'name' => 'Not Finished',
//            'icon' => 'en-ellipsis',
//            'value' => 'NotFinished',
//        ],
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
            [['AutomationIDRef', 'Assistance', 'ObsId', 'Position', 'PositionStatus', 'Project', 'CompanyIDRef', 'FullName', 'NationalCode', 'LastUserName', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage', 'RequestDate'], 'safe']
        ];
    }

    public function search($params, $type, $automation) {
        $pid = ($automation == 'automation') ? 72 : (($automation == 'secret') ? 73 : 86);
        $actionTable = Action::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $automationTable = \app\modules\automation\models\Automation::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $projectTable = \app\modules\wbs\models\Epsnew::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->automation($type)
                ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef')
                ->andWhere(['=', "$processEntityTable.ProcessIDRef", $pid]);

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'AutomationIDRef',
                    'LastActionDateHijri',
                    'LastActionMessage',
                    'CompanyIDRef' => [
                        'asc' => ["$automationTable.CompanyIDRef" => SORT_ASC],
                        'desc' => ["$automationTable.CompanyIDRef" => SORT_DESC],
                    ],
                    'RequestDate' => [
                        'asc' => ["$automationTable.RequestDate" => SORT_ASC],
                        'desc' => ["$automationTable.RequestDate" => SORT_DESC],
                    ],
                    'FullName' => [
                        'asc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_ASC],
                        'desc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_DESC],
                    ],
                    'NationalCode' => [
                        'asc' => ["$humanResourceTable.NationalCode" => SORT_ASC],
                        'desc' => ["$humanResourceTable.NationalCode" => SORT_DESC],
                    ],
                    'Project' => [
                        'asc' => ["$projectTable.EPSTitle" => SORT_ASC],
                        'desc' => ["$projectTable.EPSTitle" => SORT_DESC],
                    ],
                    'ObsId' => [
                        'asc' => ["$obsTable.obsId" => SORT_ASC],
                        'desc' => ["$obsTable.obsId" => SORT_DESC],
                    ],
                    'Position' => [
                        'asc' => ["concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)" => SORT_ASC],
                        'desc' => ["concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)" => SORT_DESC]
                    ],
                    'PositionStatus' => [
                        'asc' => ["$obsTable.status" => SORT_ASC],
                        'desc' => ["$obsTable.status" => SORT_DESC],
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
                ->andFilterWhere(['LIKE', "$automationTable.AutomationID", $this->AutomationIDRef])
                ->andFilterWhere(['LIKE', "$automationTable.CompanyIDRef", $this->CompanyIDRef])
                ->andFilterWhere(['LIKE', "$obsTable.obsId", $this->ObsId])
                ->andFilterWhere(['LIKE', "$obsTable.status", $this->PositionStatus])
                ->andFilterWhere(['LIKE', "concat($positionTypeTable.Title, ' ', $obsTable.obsTitle)", $this->Position])
                ->andFilterWhere(['or', ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ي', 'ی', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ک', 'ك', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ك', 'ک', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', str_replace('ک', 'ك', $this->FullName))]
                ])
                ->andFilterWhere(['LIKE', "$humanResourceTable.NationalCode", $this->NationalCode])
                ->andFilterWhere(['LIKE', "$projectTable.EPSTitle", $this->Project])
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
    
    public function searchObs($params, $obsId) {
        $pid = 72;
        $actionTable = Action::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $automationTable = \app\modules\automation\models\Automation::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $projectTable = \app\modules\wbs\models\Epsnew::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()
                ->innerJoin($automationTable, "$processEntityTable.AutomationIDRef=$automationTable.AutomationID")
                ->innerJoin($humanResourceTable, "$humanResourceTable.Id=$automationTable.HumanResourceIdRef")
                ->where(["$processEntityTable.ProcessIDRef" => $pid, "$automationTable.Position" => $obsId])
//                ->andWhere(["$processEntityTable.status" => 0])
                ->andWhere(["$processEntityTable.LastActionIDRef" => 974]);

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'AutomationIDRef',
                    'LastActionDateHijri',
                    'LastActionMessage',
                    'RequestDate' => [
                        'asc' => ["$automationTable.RequestDate" => SORT_ASC],
                        'desc' => ["$automationTable.RequestDate" => SORT_DESC],
                    ],
                    'FullName' => [
                        'asc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_ASC],
                        'desc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_DESC],
                    ],
                    'NationalCode' => [
                        'asc' => ["$humanResourceTable.NationalCode" => SORT_ASC],
                        'desc' => ["$humanResourceTable.NationalCode" => SORT_DESC],
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
                ->andFilterWhere(['LIKE', "$automationTable.AutomationID", $this->AutomationIDRef])
                ->andFilterWhere(['or', ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ي', 'ی', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ک', 'ك', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ك', 'ک', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', str_replace('ک', 'ك', $this->FullName))]
                ])
                ->andFilterWhere(['LIKE', "$humanResourceTable.NationalCode", $this->NationalCode])
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
