<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityActualProgressSearch extends ProcessEntity {

    public $lastActionTitle;
    public $epsTitle;
    public $LastUserName;
    public $planType;
    public $PlanTitle;
    public $PlanID;
    public $actualProgressid;
    public $fromMonth;
    public $toMonth;
    public $BudgetID;
    public $Year;
    public $obsTitle_475;
    // for workflow todo list
    public $ACTUAL_PROGRESS_ANNUAL_BUDGET_TODO_REPORT_TYPE = [
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
            [['id', 'obsTitle_475', 'fromMonth', 'BudgetID', 'PlanTitle', 'toMonth', 'LastActionDateHijri', 'PlanID', 'epsTitle', 'LastUserName', 'epsTitle', 'lastActionTitle', 'planType', 'actualProgressid'], 'safe']
        ];
    }

    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $personTableName = \app\modules\OBS\models\Person::tableName();
        $budgetTableName = \app\modules\budget\models\Budget::tableName();
        $epsTable = \app\modules\wbs\models\Epsnew::tableName();
        $annualBudgetTable = \app\modules\budget\models\AnnualBudget::tableName();

        $actionTable = Action::tableName();
        $planTable = \app\modules\budget\models\Plan::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $userTable = User::tableName();
        $actualProgressTable = \app\modules\budget\models\ActualProgress::tableName();
        $actionEntityTable = ActionEntity::tableName();
        $obsnewTable = \app\modules\OBS\models\Obsnew::tableName();

        $activeQuery = static::find()->actualProgress($type)
                ->leftJoin(Action::tableName(), Action::tableName() . '.ActionID=' . ProcessEntity::tableName() . '.LastActionIDRef')
                ->leftJoin($personTableName . ' as budgetObs', "budgetObs.PersonID=$budgetTableName.OBSCategory3IDRef")
                ->innerJoin($epsTable, $epsTable . ".EPSID=$budgetTableName.EPSIDRefL")
                ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef')
                ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as budgetUser', "budgetUser.UserID=$budgetTableName.UserIDRef")
                ->leftJoin($annualBudgetTable, "$annualBudgetTable.AnnualBudgetID=$budgetTableName.AnnualBudgetIDRef")
                ->leftJoin("$obsnewTable as departementObs", "departementObs.obsId=$budgetTableName.DepartementObsIDRef")
                ->addSelect([
            "LastActionDateHijri" => "$processEntityTable.LastActionDateHijri",
            "ProcessEntityID" => "$processEntityTable.ProcessEntityID",
            "epsTitle" => "$epsTable.EPSTitle",
//            "LastUserName" => "lastUser.UserName",
            "LastUserName" => "concat(lastUser.FirstName, ' ', lastUser.LastName)",
            "lastActionTitle" => "$actionTable.ActionTitle1",
            "planType" => "$planTable.PlanType",
            "PlanID" => "$planTable.PlanID",
            "PlanTitle" => "$planTable.PlanTitle",
            "actualProgressid" => "$actualProgressTable.id",
            "fromMonth" => "$actualProgressTable.fromMonth",
            "toMonth" => "$actualProgressTable.toMonth",
            "Year" => "$annualBudgetTable.Year",
            "BudgetID" => "$budgetTableName.BudgetID",
            /* "obsTitle_475" => (
              (new \yii\db\Query())->from($processEntityTable)
              ->innerJoin($actionEntityTable, "$processEntityTable.ProcessEntityID=$actionEntityTable.ProcessEntityIDRef")
              ->innerJoin($obsnewTable, "$obsnewTable.obsId=$actionEntityTable.OBSCategory3IDRef")
              ->innerJoin("$budgetTableName as obsBudget", "obsBudget.BudgetID=$processEntityTable.BudgetIDRef")
              ->addSelect("obsTitle")
              ->andWhere("ActionIDRef=479 and $processEntityTable.BudgetIDRef=$budgetTableName.BudgetID")->limit(1)->orderBy(['ActionDate' => SORT_DESC])
              ), */
            "obsTitle_475" => "departementObs.obsTitle",
            //  برای خروجی اکسل 
            "description" => "$actualProgressTable.description",
            "achievements" => "$actualProgressTable.achievements",
            "ImportanceID" => "$planTable.ImportanceIDRef",
            "PriorityID" => "$planTable.PriorityIDRef",        
            "obsId" => "$planTable.OBSCategory3CompanyIDRef",        
            "rate1" => "$actualProgressTable.rate1",
            "rate2" => "$actualProgressTable.rate2",
            "rate3" => "$actualProgressTable.rate3",
            "rate4" => "$actualProgressTable.rate4",
            "rate5" => "$actualProgressTable.rate5",
            "rate6" => "$actualProgressTable.rate6",
            "rate7" => "$actualProgressTable.rate7",
            "rate8" => "$actualProgressTable.rate8",
            "rate9" => "$actualProgressTable.rate9",
            "rate10" => "$actualProgressTable.rate10",
            "rate11" => "$actualProgressTable.rate11",
            "rate12" => "$actualProgressTable.rate12"
        ]);
//        echo $activeQuery->createCommand()->rawSql;
//        die();

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' =>
                [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'LastActionDateHijri',
                    'LastUserName' => [
//                        'asc' => ["lastUser.UserName" => SORT_ASC],
//                        'desc' => ["lastUser.UserName" => SORT_DESC],
                        'asc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_ASC],
                        'desc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_DESC],
                    ],
                    'lastActionTitle' => [
                        'asc' => ["$actionTable.ActionTitle1" => SORT_ASC],
                        'desc' => ["$actionTable.ActionTitle1" => SORT_DESC],
                    ],
                    'epsTitle' => [
                        'asc' => ["$epsTable.EPSTitle" => SORT_ASC],
                        'desc' => ["$epsTable.EPSTitle" => SORT_DESC],
                    ],
                    'planType' => [
                        'asc' => ["$planTable.PlanType" => SORT_ASC],
                        'desc' => ["$planTable.PlanType" => SORT_DESC],
                    ],
                    'PlanID' => [
                        'asc' => ["$planTable.PlanID" => SORT_ASC],
                        'desc' => ["$planTable.PlanID" => SORT_DESC],
                    ],
                    'PlanTitle' => [
                        'asc' => ["$planTable.PlanTitle" => SORT_ASC],
                        'desc' => ["$planTable.PlanTitle" => SORT_DESC],
                    ],
                    'BudgetID' => [
                        'asc' => ["$budgetTableName.BudgetID" => SORT_ASC],
                        'desc' => ["$budgetTableName.BudgetID" => SORT_DESC],
                    ],
                    'actualProgressid' => [
                        'asc' => ["$actualProgressTable.id" => SORT_ASC],
                        'desc' => ["$actualProgressTable.id" => SORT_DESC],
                    ],
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->obsTitle_475)) {
            $activeQuery->andWhere("(" . (new \yii\db\Query())->from($processEntityTable)
                            ->innerJoin($actionEntityTable, "$processEntityTable.ProcessEntityID=$actionEntityTable.ProcessEntityIDRef")
                            ->innerJoin($obsnewTable, "$obsnewTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                            ->innerJoin("$budgetTableName as obsBudget", "obsBudget.BudgetID=$processEntityTable.BudgetIDRef")
                            ->addSelect("count(*)")
                            ->andWhere("ActionIDRef=479 and $processEntityTable.BudgetIDRef=$budgetTableName.BudgetID")
                            ->andWhere(['like', 'obsTitle', $this->obsTitle_475])
                            ->limit(1)->orderBy(['ActionDate' => SORT_DESC])
                            ->createCommand()->rawSql . ")>=1");
        }

        $activeQuery
                ->andFilterWhere(['LIKE', "$actualProgressTable.id", $this->actualProgressid])
                ->andFilterWhere(['=', "$actualProgressTable.fromMonth", $this->fromMonth])
                ->andFilterWhere(['=', "$actualProgressTable.toMonth", $this->toMonth])
                ->andFilterWhere(['LIKE', "concat(lastUser.FirstName, ' ', lastUser.LastName)", $this->LastUserName])
                ->andFilterWhere(['LIKE', "$planTable.PlanID", $this->PlanID])
                /*->andFilterWhere(["or",
                    ['LIKE', "$budgetTableName.BudgetID", $this->BudgetID],
                    ['LIKE', "$annualBudgetTable.Year", $this->BudgetID]
                ])*/
                ->andFilterWhere(['LIKE', "concat($annualBudgetTable.Year, '-', $budgetTableName.BudgetID)", $this->BudgetID])
                ->andFilterWhere(['LIKE', "LastActionDateHijri", $this->LastActionDateHijri])
                ->andFilterWhere(['or', ['LIKE', "$planTable.PlanType", str_replace('ی', 'ي', $this->planType)]
                    , ['LIKE', "$planTable.PlanType", str_replace('ي', 'ی', $this->planType)]
                    , ['LIKE', "$planTable.PlanType", str_replace('ک', 'ك', $this->planType)]
                    , ['LIKE', "$planTable.PlanType", str_replace('ك', 'ک', $this->planType)]
                ])
                ->andFilterWhere(['or', ['LIKE', "$planTable.PlanTitle", str_replace('ی', 'ي', $this->PlanTitle)]
                    , ['LIKE', "$planTable.PlanTitle", str_replace('ي', 'ی', $this->PlanTitle)]
                    , ['LIKE', "$planTable.PlanTitle", str_replace('ک', 'ك', $this->PlanTitle)]
                    , ['LIKE', "$planTable.PlanTitle", str_replace('ك', 'ک', $this->PlanTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', "$epsTable.EPSTitle", str_replace('ی', 'ي', $this->epsTitle)]
                    , ['LIKE', "$epsTable.EPSTitle", str_replace('ي', 'ی', $this->epsTitle)]
                    , ['LIKE', "$epsTable.EPSTitle", str_replace('ک', 'ك', $this->epsTitle)]
                    , ['LIKE', "$epsTable.EPSTitle", str_replace('ك', 'ک', $this->epsTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', "$actionTable.ActionTitle1", str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', "$actionTable.ActionTitle1", str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', "$actionTable.ActionTitle1", str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', "$actionTable.ActionTitle1", str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
//                ->andFilterWhere(['LIKE', "$claimTableName.id", $this->id])
//                ->andFilterWhere(['LIKE', "$claimTableName.claimNo", $this->claimNo])
//                ->andFilterWhere(['LIKE', "$claimTableName.createdAt", $this->RequestDateHijri])
//                ->andFilterWhere(['LIKE', "$claimTableName.description", $this->Description])
//                ->andFilterWhere(['LIKE', "$claimTableName.subject", $this->subject])
//                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionDateHijri", $this->LastActionDateHijri])
//                ->andFilterWhere(['LIKE', "$actionTable.ActionTitle2", $this->lastActionTitle])
//                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionMessage", $this->LastActionMessage])
//                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionAutoMessage", $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', "$userTable.UserName", $this->lastUser]);
        ;

        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $type = 'All';

        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $personTableName = \app\modules\OBS\models\Person::tableName();
        $budgetTableName = \app\modules\budget\models\Budget::tableName();
        $epsTable = \app\modules\wbs\models\Epsnew::tableName();
        $annualBudgetTable = \app\modules\budget\models\AnnualBudget::tableName();

        $actionTable = Action::tableName();
        $planTable = \app\modules\budget\models\Plan::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $userTable = User::tableName();
        $actualProgressTable = \app\modules\budget\models\ActualProgress::tableName();
        $actionEntityTable = ActionEntity::tableName();
        $obsnewTable = \app\modules\OBS\models\Obsnew::tableName();

        $activeQuery = static::find()->actualProgress($type)->reportItem($itemId)
            ->leftJoin(Action::tableName(), Action::tableName() . '.ActionID=' . ProcessEntity::tableName() . '.LastActionIDRef')
            ->leftJoin($personTableName . ' as budgetObs', "budgetObs.PersonID=$budgetTableName.OBSCategory3IDRef")
            ->innerJoin($epsTable, $epsTable . ".EPSID=$budgetTableName.EPSIDRefL")
            ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef')
            ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as budgetUser', "budgetUser.UserID=$budgetTableName.UserIDRef")
            ->leftJoin($annualBudgetTable, "$annualBudgetTable.AnnualBudgetID=$budgetTableName.AnnualBudgetIDRef")
            ->leftJoin("$obsnewTable as departementObs", "departementObs.obsId=$budgetTableName.DepartementObsIDRef")
            ->addSelect([
                "LastActionDateHijri" => "$processEntityTable.LastActionDateHijri",
                "ProcessEntityID" => "$processEntityTable.ProcessEntityID",
                "epsTitle" => "$epsTable.EPSTitle",
//            "LastUserName" => "lastUser.UserName",
                "LastUserName" => "concat(lastUser.FirstName, ' ', lastUser.LastName)",
                "lastActionTitle" => "$actionTable.ActionTitle1",
                "planType" => "$planTable.PlanType",
                "PlanID" => "$planTable.PlanID",
                "PlanTitle" => "$planTable.PlanTitle",
                "actualProgressid" => "$actualProgressTable.id",
                "fromMonth" => "$actualProgressTable.fromMonth",
                "toMonth" => "$actualProgressTable.toMonth",
                "Year" => "$annualBudgetTable.Year",
                "BudgetID" => "$budgetTableName.BudgetID",
                /* "obsTitle_475" => (
                  (new \yii\db\Query())->from($processEntityTable)
                  ->innerJoin($actionEntityTable, "$processEntityTable.ProcessEntityID=$actionEntityTable.ProcessEntityIDRef")
                  ->innerJoin($obsnewTable, "$obsnewTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                  ->innerJoin("$budgetTableName as obsBudget", "obsBudget.BudgetID=$processEntityTable.BudgetIDRef")
                  ->addSelect("obsTitle")
                  ->andWhere("ActionIDRef=479 and $processEntityTable.BudgetIDRef=$budgetTableName.BudgetID")->limit(1)->orderBy(['ActionDate' => SORT_DESC])
                  ), */
                "obsTitle_475" => "departementObs.obsTitle",
                //  برای خروجی اکسل
                "description" => "$actualProgressTable.description",
                "achievements" => "$actualProgressTable.achievements",
                "ImportanceID" => "$planTable.ImportanceIDRef",
                "PriorityID" => "$planTable.PriorityIDRef",
                "obsId" => "$planTable.OBSCategory3CompanyIDRef",
                "rate1" => "$actualProgressTable.rate1",
                "rate2" => "$actualProgressTable.rate2",
                "rate3" => "$actualProgressTable.rate3",
                "rate4" => "$actualProgressTable.rate4",
                "rate5" => "$actualProgressTable.rate5",
                "rate6" => "$actualProgressTable.rate6",
                "rate7" => "$actualProgressTable.rate7",
                "rate8" => "$actualProgressTable.rate8",
                "rate9" => "$actualProgressTable.rate9",
                "rate10" => "$actualProgressTable.rate10",
                "rate11" => "$actualProgressTable.rate11",
                "rate12" => "$actualProgressTable.rate12"
            ]);

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => false,
//            'pagination' => [
//                'pageSize' => Yii::$app->params['pageSize'],
//            ],
            'sort' => [
                'attributes' => [
                    'LastActionDateHijri',
                    'LastUserName' => [
//                        'asc' => ["lastUser.UserName" => SORT_ASC],
//                        'desc' => ["lastUser.UserName" => SORT_DESC],
                        'asc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_ASC],
                        'desc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_DESC],
                    ],
                    'lastActionTitle' => [
                        'asc' => ["$actionTable.ActionTitle1" => SORT_ASC],
                        'desc' => ["$actionTable.ActionTitle1" => SORT_DESC],
                    ],
                    'epsTitle' => [
                        'asc' => ["$epsTable.EPSTitle" => SORT_ASC],
                        'desc' => ["$epsTable.EPSTitle" => SORT_DESC],
                    ],
                    'planType' => [
                        'asc' => ["$planTable.PlanType" => SORT_ASC],
                        'desc' => ["$planTable.PlanType" => SORT_DESC],
                    ],
                    'PlanID' => [
                        'asc' => ["$planTable.PlanID" => SORT_ASC],
                        'desc' => ["$planTable.PlanID" => SORT_DESC],
                    ],
                    'PlanTitle' => [
                        'asc' => ["$planTable.PlanTitle" => SORT_ASC],
                        'desc' => ["$planTable.PlanTitle" => SORT_DESC],
                    ],
                    'BudgetID' => [
                        'asc' => ["$budgetTableName.BudgetID" => SORT_ASC],
                        'desc' => ["$budgetTableName.BudgetID" => SORT_DESC],
                    ],
                    'actualProgressid' => [
                        'asc' => ["$actualProgressTable.id" => SORT_ASC],
                        'desc' => ["$actualProgressTable.id" => SORT_DESC],
                    ],
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

        if (!empty($this->obsTitle_475)) {
            $activeQuery->andWhere("(" . (new \yii\db\Query())->from($processEntityTable)
                    ->innerJoin($actionEntityTable, "$processEntityTable.ProcessEntityID=$actionEntityTable.ProcessEntityIDRef")
                    ->innerJoin($obsnewTable, "$obsnewTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                    ->innerJoin("$budgetTableName as obsBudget", "obsBudget.BudgetID=$processEntityTable.BudgetIDRef")
                    ->addSelect("count(*)")
                    ->andWhere("ActionIDRef=479 and $processEntityTable.BudgetIDRef=$budgetTableName.BudgetID")
                    ->andWhere(['like', 'obsTitle', $this->obsTitle_475])
                    ->limit(1)->orderBy(['ActionDate' => SORT_DESC])
                    ->createCommand()->rawSql . ")>=1");
        }

        $activeQuery
            ->andFilterWhere(['LIKE', "$actualProgressTable.id", $this->actualProgressid])
            ->andFilterWhere(['=', "$actualProgressTable.fromMonth", $this->fromMonth])
            ->andFilterWhere(['=', "$actualProgressTable.toMonth", $this->toMonth])
            ->andFilterWhere(['LIKE', "concat(lastUser.FirstName, ' ', lastUser.LastName)", $this->LastUserName])
            ->andFilterWhere(['LIKE', "$planTable.PlanID", $this->PlanID])
            /*->andFilterWhere(["or",
                ['LIKE', "$budgetTableName.BudgetID", $this->BudgetID],
                ['LIKE', "$annualBudgetTable.Year", $this->BudgetID]
            ])*/
            ->andFilterWhere(['LIKE', "concat($annualBudgetTable.Year, '-', $budgetTableName.BudgetID)", $this->BudgetID])
            ->andFilterWhere(['LIKE', "LastActionDateHijri", $this->LastActionDateHijri])
            ->andFilterWhere(['or', ['LIKE', "$planTable.PlanType", str_replace('ی', 'ي', $this->planType)]
                , ['LIKE', "$planTable.PlanType", str_replace('ي', 'ی', $this->planType)]
                , ['LIKE', "$planTable.PlanType", str_replace('ک', 'ك', $this->planType)]
                , ['LIKE', "$planTable.PlanType", str_replace('ك', 'ک', $this->planType)]
            ])
            ->andFilterWhere(['or', ['LIKE', "$planTable.PlanTitle", str_replace('ی', 'ي', $this->PlanTitle)]
                , ['LIKE', "$planTable.PlanTitle", str_replace('ي', 'ی', $this->PlanTitle)]
                , ['LIKE', "$planTable.PlanTitle", str_replace('ک', 'ك', $this->PlanTitle)]
                , ['LIKE', "$planTable.PlanTitle", str_replace('ك', 'ک', $this->PlanTitle)]
            ])
            ->andFilterWhere(['or', ['LIKE', "$epsTable.EPSTitle", str_replace('ی', 'ي', $this->epsTitle)]
                , ['LIKE', "$epsTable.EPSTitle", str_replace('ي', 'ی', $this->epsTitle)]
                , ['LIKE', "$epsTable.EPSTitle", str_replace('ک', 'ك', $this->epsTitle)]
                , ['LIKE', "$epsTable.EPSTitle", str_replace('ك', 'ک', $this->epsTitle)]
            ])
            ->andFilterWhere(['or', ['LIKE', "$actionTable.ActionTitle1", str_replace('ی', 'ي', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ي', 'ی', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ک', 'ك', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ك', 'ک', $this->lastActionTitle)]
            ])
//                ->andFilterWhere(['LIKE', "$claimTableName.id", $this->id])
//                ->andFilterWhere(['LIKE', "$claimTableName.claimNo", $this->claimNo])
//                ->andFilterWhere(['LIKE', "$claimTableName.createdAt", $this->RequestDateHijri])
//                ->andFilterWhere(['LIKE', "$claimTableName.description", $this->Description])
//                ->andFilterWhere(['LIKE', "$claimTableName.subject", $this->subject])
//                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionDateHijri", $this->LastActionDateHijri])
//                ->andFilterWhere(['LIKE', "$actionTable.ActionTitle2", $this->lastActionTitle])
//                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionMessage", $this->LastActionMessage])
//                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionAutoMessage", $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', "$userTable.UserName", $this->lastUser]);
        ;

        return $dataProvider;




    }

}
