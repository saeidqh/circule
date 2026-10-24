<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityEmployeeSettlementSearch extends ProcessEntity {

    public $lastActionTitle;
    public $LastUserName;
    public $LastActionMessage;
    public $FullName;
    public $NationalCode;
    public $Mobile;
    public $CompanyIDRef;
    public $CreatedCompanyIDRef;
    public $Assistance;
    public $Management;
    public $Position;
    public $IssueDate;
    // for workflow todo list
    public $EMPLOYEESETTLEMENT_TODO_REPORT_TYPE = [
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
            [['ObjectIDRef', 'CreatedCompanyIDRef', 'CompanyIDRef', 'Assistance', 'Management', 'Position', 'FullName', 'NationalCode', 'Mobile', 'IssueDate'], 'safe']
        ];
    }

    public function search($params, $type) {
        $processTable = Process::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = ActionEntity::tableName();
        $processEntityTable = ProcessEntity::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $employeeSettlementTable = \app\modules\automation\models\EmployeeSettlement::tableName();

        $activeQuery = self::find()
                ->employeeSettlement($type)
                ->addSelect([
                    "$processEntityTable.*",
                    "lastActionTitle" => "(
                        SELECT GROUP_CONCAT( a.ActionTitle1 SEPARATOR ' | ' ) 
                        FROM
                        $actionTable a 
                        WHERE
                            a.ProcessIDRef = $processTable.ProcessID
                            AND a.ActionID NOT IN (1011, 1052)
                            AND a.ActionID NOT IN ( SELECT ActionIDRef FROM $actionEntityTable ae WHERE ae.ProcessEntityIDRef = $processEntityTable.ProcessEntityID ) 
                            AND NOT EXISTS (
                                SELECT 1 
                                FROM
                                $actionTable a2 
                                WHERE
                                    a2.ProcessIDRef = $processTable.ProcessID 
                                    AND FIND_IN_SET( a2.ActionID, a.requiredAction ) 
                                    AND a2.ActionID NOT IN ( SELECT ActionIDRef FROM $actionEntityTable ae WHERE ae.ProcessEntityIDRef = $processEntityTable.ProcessEntityID ))
                            AND NOT EXISTS ( SELECT ActionIDRef FROM $actionEntityTable ae2 WHERE ae2.ProcessEntityIDRef = $processEntityTable.ProcessEntityID AND ae2.ActionIDRef in(1048,1070) )            
                    )"
                ])
                ->leftJoin($humanResourceTable, "$humanResourceTable.Id=$employeeSettlementTable.HumanResourceIdRef")
                ->leftJoin("$obsTable as position", "position.obsId=$employeeSettlementTable.Position")
                ->leftJoin("$positionTypeTable as positionType", "positionType.PositionTypeID=position.positionTypeIdRef")
                ->leftJoin("$obsTable as assistance", "assistance.obsId=$employeeSettlementTable.AssistanceOBSIDRef")
                ->leftJoin("$positionTypeTable as assistanceType", "assistanceType.PositionTypeID=assistance.positionTypeIdRef")
                ->andWhere(["$employeeSettlementTable.isDelete" =>0])
                ->groupBy("$processEntityTable.ProcessEntityID");
//        echo $activeQuery->createCommand()->rawSql;
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'ObjectIDRef',
                    'CompanyIDRef',
                    'CreatedCompanyIDRef',
                    'NationalCode',
                    'Assistance' => [
                        'asc' => ["assistance.obsTitle" => SORT_ASC],
                        'desc' => ["assistance.obsTitle" => SORT_DESC]
                    ],
                    'Position' => [
                        'asc' => ["position.obsTitle" => SORT_ASC],
                        'desc' => ["position.obsTitle" => SORT_DESC]
                    ],
                    'FullName' => [
                        'asc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)" => SORT_ASC],
                        'desc' => ["concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)`" => SORT_DESC],
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
            ->andFilterWhere(['LIKE', "$employeeSettlementTable.EmployeeSettlementID", $this->ObjectIDRef])
            ->andFilterWhere(['LIKE', "$employeeSettlementTable.CompanyIDRef", $this->CompanyIDRef])
            ->andFilterWhere(['LIKE', "$employeeSettlementTable.CreatedCompanyIDRef", $this->CreatedCompanyIDRef])
            ->andFilterWhere(['or',['LIKE', "concat(positionType.Title, ' ', position.obsTitle)",str_replace('ی', 'ي', $this->Position)]
               , ['LIKE', "concat(positionType.Title, ' ', position.obsTitle)",str_replace('ی', 'ي', $this->Position)]
               , ['LIKE', "concat(positionType.Title, ' ', position.obsTitle)",str_replace('ی', 'ي', $this->Position)]
               , ['LIKE', "concat(positionType.Title, ' ', position.obsTitle)",str_replace('ی', 'ي', $this->Position)]
               , ['LIKE', "concat(positionType.Title, ' ', position.obsTitle)",str_replace('ی', 'ي', str_replace('ک', 'ك', $this->Position))]
                ])     
            ->andFilterWhere(['or',['LIKE', "concat(assistanceType.Title, ' ', assistance.obsTitle)",str_replace('ی', 'ي', $this->Assistance)]
               , ['LIKE', "concat(assistanceType.Title, ' ', assistance.obsTitle)",str_replace('ی', 'ي', $this->Assistance)]
               , ['LIKE', "concat(assistanceType.Title, ' ', assistance.obsTitle)",str_replace('ی', 'ي', $this->Assistance)]
               , ['LIKE', "concat(assistanceType.Title, ' ', assistance.obsTitle)",str_replace('ی', 'ي', $this->Assistance)]
               , ['LIKE', "concat(assistanceType.Title, ' ', assistance.obsTitle)",str_replace('ی', 'ي', str_replace('ک', 'ك', $this->Assistance))]
                ])
            ->andFilterWhere(['or', ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ي', 'ی', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ک', 'ك', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ك', 'ک', $this->FullName)]
                    , ['LIKE', "concat($humanResourceTable.FirstName, ' ', $humanResourceTable.LastName)", str_replace('ی', 'ي', str_replace('ک', 'ك', $this->FullName))]
                ])
            ->andFilterWhere(['LIKE', "$humanResourceTable.NationalCode", $this->NationalCode])
            ->andFilterWhere(['LIKE', "$humanResourceTable.Mobile", $this->Mobile])
            ->andFilterWhere(['or'
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ی', 'ي', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ي', 'ی', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ک', 'ك', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ك', 'ک', $this->lastActionTitle)]
            ]);
        return $dataProvider;
    }

}
