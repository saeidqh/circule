<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\budget\models\Plan;
use app\modules\budget\models\Budget;
use app\modules\budget\models\Ncontract;
use app\modules\contract\models\Contract;
use app\modules\OBS\models\Person;
use app\modules\OBS\models\Obsnew;
use app\components\CandoHelper;
use app\modules\account\models\User;
use app\modules\budget\models\AnnualBudget;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntityBudgetSearch extends ProcessEntity {

    public $fullId;
    public $title;
    public $type;
    public $costType;
    public $SystemDescription;
    public $lastActionTitle;
    public $budgetUserName;
    public $budgetObs;
    public $epsTitle;
    public $lastUserName;
    public $obsTitle_475;
    // for workflow todo list 
    public $BUDGET_TODO_REPORT_TYPE = [
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
    public $BUDGET_TYPE = [
        '1' => 'برنامه', '2' => 'قرارداد جاری', '3' => 'قرارداد جدید'
    ];

    public function rules() {
        return [
            [['obsTitle_475','fullId', 'title', 'type', 'costType', 'SystemDescription', 'LastActionDateHijri', 'lastActionTitle', 'epsTitle', 'budgetUserName', 'budgetObs', 'lastUserName'], 'safe']
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type) {

        $epsTable= \app\modules\wbs\models\Epsnew::tableName();
        $processEntityTable= ProcessEntity::tableName();
        $obsnewTable= \app\modules\OBS\models\Obsnew::tableName();
        $actionEntityTable= ActionEntity::tableName();
        $budgetTable= Budget::tableName();
        $planTable= Plan::tableName();
        $ncontractTable= Ncontract::tableName();
        $contractTable= Contract::tableName();
        
        $costTypeArray = Plan::costTypeArray();
        
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->budget($type)
                ->leftJoin(Action::tableName(), Action::tableName(). '.ActionID='.ProcessEntity::tableName().'.LastActionIDRef')
                ->leftJoin(Plan::tableName(), Plan::tableName(). '.PlanID='.Budget::tableName().'.PlanIDRef')
                ->leftJoin(Obsnew::tableName(). ' as departementObs', 'departementObs.obsId='.Budget::tableName().'.DepartementObsIDRef')
                ->leftJoin(Contract::tableName(), Contract::tableName(). '.ContractID='.Budget::tableName().'.ContractIDRef')
                ->leftJoin(Person::tableName(). ' as subcontractor', 'subcontractor.PersonID='.Contract::tableName().'.SubContractorPersonIDRef')
                ->leftJoin(Obsnew::tableName(). ' as budgetObs', 'budgetObs.obsId='.Budget::tableName().'.OBSCategory3IDRef')
                ->innerJoin($epsTable, $epsTable. '.EPSID='.Budget::tableName().'.EPSIDRefL')
                ->leftJoin(CandoHelper::braketTable(User::tableName()). ' as lastUser', 'lastUser.UserID='.ProcessEntity::tableName().'.LastUserIDRef')
//                ->leftJoin(CandoHelper::braketTable(User::tableName()).' as budgetUser', 'budgetUser.UserID='.Budget::tableName().'.UserIDRef')
                ->leftJoin(\app\modules\budget\models\Ncontract::tableName(), \app\modules\budget\models\Ncontract::tableName(). '.NewContractID='.Budget::tableName().'.NewContractIDRef')
                ->leftJoin(AnnualBudget::tableName(), AnnualBudget::tableName(). '.AnnualBudgetID='.  Budget::tableName().'.AnnualBudgetIDRef')
                ->addSelect([
                    "*",
                    /*"obsTitle_475" => "(select obsTitle from $processEntityTable as processEnt 
                                inner join $actionEntityTable on $actionEntityTable.ProcessEntityIDRef =  processEnt.ProcessEntityID
                                inner join $obsnewTable on $obsnewTable.obsId =  $actionEntityTable.OBSCategory3IDRef
                                inner join $budgetTable as obsBudget on obsBudget.BudgetID =  processEnt.BudgetIDRef
                                where (ActionIDRef=479)and $budgetTable.BudgetID = obsBudget.BudgetID   
                                ORDER BY ActionDateHijri DESC limit 1 
                            )",*/
                    "obsTitle_475" => "departementObs.obsTitle",
                    "departementObsId" => "departementObs.obsId",
                ]);
//        echo $activeQuery->createCommand()->rawSql;
//        die();
        
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);
        
        $dataProvider->setSort(new \yii\data\Sort([
            'attributes' => [
                'LastActionDateHijri',
                'fullId' => [
                    'asc' => [Budget::tableName().'.BudgetID' => SORT_ASC,],
                    'desc' => [Budget::tableName().'.BudgetID' => SORT_DESC],
                ],
                'lastUserName' => [
//                    'asc' => [CandoHelper::braketTable(User::tableName()). '.UserName' => SORT_ASC,],
//                    'desc' => [CandoHelper::braketTable(User::tableName()).'.UserName' => SORT_DESC],
                    'asc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_ASC,],
                    'desc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_DESC],
                ],
                'epsTitle' => [
                    'asc' => [$epsTable. '.EPSTitle' => SORT_ASC,],
                    'desc' => [$epsTable. '.EPSTitle' => SORT_DESC],
                ],
                'lastActionTitle' => [
                    'asc' => [Action::tableName(). '.ActionTitle2' => SORT_ASC,],
                    'desc' => [Action::tableName(). '.ActionTitle2' => SORT_DESC],
                ],
                'SystemDescription' => [
                    'asc' => [Budget::tableName().'.SystemDescription' => SORT_ASC,],
                    'desc' => [Budget::tableName().'.SystemDescription' => SORT_DESC],
                ],
            ]
        ]));

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

        
        /*if ($this->type == 1) {
            $activeQuery->andWhere(Budget::tableName(). '.PlanIDRef>0');
        } elseif ($this->type == 2) {
            $activeQuery->andWhere(Budget::tableName(). '.ContractIDRef>0');
        } elseif ($this->type == 3) {
            $activeQuery->andWhere(Budget::tableName(). '.NewContractIDRef>0');
        }*/
        $costType = $this->costType == null ? null : array_search($this->costType, $costTypeArray);
        $activeQuery
                /*->andFilterWhere(['OR',
                    ['LIKE', Budget::tableName(). '.BudgetID', $this->fullId],
                    ['LIKE', 'SUBSTRING('.  AnnualBudget::tableName().'.Year,3,2)', $this->fullId]
                ])*/
                ->andFilterWhere(['LIKE', 'concat(SUBSTRING('. AnnualBudget::tableName().'.Year,3,2), "-",'. Budget::tableName(). '.BudgetID)', $this->fullId])
                ->andFilterWhere(['or',
                    ['or',
                        ['LIKE', Plan::tableName(). '.PlanTitle', str_replace('ی', 'ي', $this->title)]
                        , ['LIKE', Plan::tableName(). '.PlanTitle', str_replace('ي', 'ی', $this->title)]
                        , ['LIKE', Plan::tableName(). '.PlanTitle', str_replace('ک', 'ك', $this->title)]
                        , ['LIKE', Plan::tableName(). '.PlanTitle', str_replace('ك', 'ک', $this->title)]
                    ],
                    ['like', 'subcontractor.PersonTitle', $this->title],
                    ['like', \app\modules\budget\models\Ncontract::tableName(). '.NewContractNo', $this->title],
                ])
                ->andFilterWhere(['LIKE', Budget::tableName(). '.SystemDescription', $this->SystemDescription])
                ->andFilterWhere(['LIKE', $epsTable. '.EPSTitle', $this->epsTitle])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', Action::tableName(). '.ActionTitle1', $this->lastActionTitle])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['LIKE', 'LastActionAutoMessage', $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', 'budgetUser.UserName', $this->budgetUserName])
//                ->andFilterWhere(['LIKE', 'lastUser.UserName', $this->lastUserName])
                ->andFilterWhere(['LIKE', "concat(lastUser.FirstName, ' ', lastUser.LastName)", $this->lastUserName])
                ->andFilterWhere(['LIKE', 'budgetObs.obsTitle', $this->budgetObs])
                ->andFilterWhere(['LIKE', 'departementObs.obsTitle', $this->obsTitle_475])
                ->andFilterWhere(['or',
                    ['like', "$planTable.costType", $costType],
                    ['like', "$contractTable.costType", $costType],
                    ['like', "$ncontractTable.costType", $costType],
                ])
                ;

        /*if(!empty($this->obsTitle_475)){

            $activeQuery->andWhere(['LIKE',"(select obsTitle from workflow_table_process_entity as processEnt 
                inner join workflow_table_action_entity on workflow_table_action_entity.ProcessEntityIDRef = processEnt.ProcessEntityID
                inner join obs_table_obs_new on obs_table_obs_new.obsId = workflow_table_action_entity.OBSCategory3IDRef
                inner join budget_table_budget as obsBudget on obsBudget.BudgetID = processEnt.BudgetIDRef
                where (ActionIDRef=479) and budget_table_budget.BudgetID = obsBudget.BudgetID 
                ORDER BY ActionDateHijri DESC limit 1 
                )", $this->obsTitle_475 ]);
        }*/

//        echo $activeQuery->createCommand()->rawSql;
//        die();

        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $epsTable= \app\modules\wbs\models\Epsnew::tableName();
        $userTable = \app\modules\account\models\User::tableName();
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->budget('BudgetWorkflowReport')->reportItem($itemId)
                ->leftJoin(Action::tableName(), Action::tableName(). '.ActionID='.ProcessEntity::tableName().'.LastActionIDRef')
                ->leftJoin(CandoHelper::braketTable(User::tableName()), CandoHelper::braketTable(User::tableName()).'.UserID='.ProcessEntity::tableName().'.LastUserIDRef')
                ->innerJoin($epsTable, $epsTable. '.EPSID='.Budget::tableName().'.EPSIDRefL')
                ->leftJoin(AnnualBudget::tableName(), AnnualBudget::tableName(). '.AnnualBudgetID='.Budget::tableName().'.AnnualBudgetIDRef')
                ->leftJoin(Contract::tableName(), Contract::tableName(). '.ContractID='.Budget::tableName().'.ContractIDRef')
                ->leftJoin(\app\modules\budget\models\Ncontract::tableName(), \app\modules\budget\models\Ncontract::tableName(). '.NewContractID='.Budget::tableName().'.NewContractIDRef')
                ->leftJoin(Plan::tableName(), Plan::tableName(). '.PlanID='.Budget::tableName().'.PlanIDRef')
                ->leftJoin(Person::tableName(). ' as subcontractor', 'subcontractor.PersonID='.Contract::tableName().'.SubContractorPersonIDRef')

        ;



        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
        ]);


        $dataProvider->setSort(new \yii\data\Sort([
            'attributes' => [
                'LastActionDateHijri',
                'fullId' => [
                    'asc' => [Budget::tableName().'.BudgetID' => SORT_ASC,],
                    'desc' => [Budget::tableName().'.BudgetID' => SORT_DESC],
                ],
                'lastUserName' => [
//                    'asc' => [CandoHelper::braketTable(User::tableName()). '.UserName' => SORT_ASC,],
//                    'desc' => [CandoHelper::braketTable(User::tableName()).'.UserName' => SORT_DESC],
                    'asc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_ASC,],
                    'desc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_DESC],
                ],
                'epsTitle' => [
                    'asc' => [$epsTable. '.EPSTitle' => SORT_ASC,],
                    'desc' => [$epsTable. '.EPSTitle' => SORT_DESC],
                ],
                'lastActionTitle' => [
                    'asc' => [Action::tableName(). '.ActionTitle2' => SORT_ASC,],
                    'desc' => [Action::tableName(). '.ActionTitle2' => SORT_DESC],
                ],
                'SystemDescription' => [
                    'asc' => [Budget::tableName().'.SystemDescription' => SORT_ASC,],
                    'desc' => [Budget::tableName().'.SystemDescription' => SORT_DESC],
                ],
            ]
        ]));

        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        if ($this->type == 1) {
            $activeQuery->andWhere(Budget::tableName(). '.PlanIDRef>0');
        } elseif ($this->type == 2) {
            $activeQuery->andWhere(Budget::tableName(). '.ContractIDRef>0');
        } elseif ($this->type == 3) {
            $activeQuery->andWhere(Budget::tableName(). '.NewContractIDRef>0');
        }

        $activeQuery
                ->andFilterWhere(['OR',
                    ['LIKE', Budget::tableName(). '.BudgetID', $this->fullId],
                    ['LIKE', 'SUBSTRING(cast('. AnnualBudget::tableName().'.Year as varchar),3,2)', $this->fullId]
                ])
                ->andFilterWhere(['or',
                    ['or',
                        ['LIKE', Plan::tableName().'.PlanTitle', str_replace('ی', 'ي', $this->title)]
                        , ['LIKE', Plan::tableName().'.PlanTitle', str_replace('ي', 'ی', $this->title)]
                        , ['LIKE', Plan::tableName().'.PlanTitle', str_replace('ک', 'ك', $this->title)]
                        , ['LIKE', Plan::tableName().'.PlanTitle', str_replace('ك', 'ک', $this->title)]
                    ],
                    ['like', 'subcontractor.PersonTitle', $this->title],
                    ['like', \app\modules\budget\models\Ncontract::tableName(). '.NewContractNo', $this->title],
                ])
                ->andFilterWhere(['LIKE', Budget::tableName(). '.SystemDescription', $this->SystemDescription])
                ->andFilterWhere(['LIKE', $epsTable. '.EPSTitle', $this->epsTitle])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', Action::tableName(). '.ActionTitle2', $this->lastActionTitle])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['LIKE', 'LastActionAutoMessage', $this->LastActionAutoMessage])
//                ->andFilterWhere(['LIKE', CandoHelper::braketTable(User::tableName()).'.UserName', $this->lastUserName]);
                ->andFilterWhere(['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->lastUserName]);

        return $dataProvider;
    }

}
