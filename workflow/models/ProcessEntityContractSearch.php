<?php

namespace app\modules\workflow\models;

use app\modules\capital\models\CapitalBalanceDocument;
use app\modules\contract\models\Contract;
use app\modules\contract\models\ContractEps;
use app\modules\wbs\models\Epsnew;
use app\modules\workflowContract\models\WorkflowContractEps;
use Yii;
use yii\data\ActiveDataProvider;
use app\modules\contract\models\RevisionType;
use app\modules\workflowContract\models\WorkflowContract;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntityContractSearch extends ProcessEntity {

    public $ID;
    public $ContractID;
    public $ContractIDRef;
    public $contractNo;
    public $ContractNo;
    public $contractSubject;
    public $ContractSubject;
    public $projectTitle;
    public $subcontractor;
    public $revisionNumber;
    public $RevisionNumber;
    public $revisionType;
    public $ContractRevisionType;
    public $lastActionTitle;
    public $lastUserName;
    public $processTitle;
    public $WorkflowContractID;
    public $SubContractorPersonTitle;
    
    //use method searchWorkflowReport()
    public $personTitle;
    public $ActionTitle2;
    public $UserName;
    
    // for workflow todo list 
    public $WORKFLOW_CONTRACT_TODO_REPORT_TYPE = [
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
            [['WorkflowContractID','ContractRevisionType','RevisionNumber','ContractSubject','UserName','ActionTitle2','SubContractorPersonTitle','ContractIDRef','ContractNo','contractNo','lastUserName', 'contractSubject', 'subcontractor', 'revisionNumber', 'revisionType', 'processTitle', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage','projectTitle','personTitle'], 'safe'],
            [['contractNo', 'ID','ContractID','lastUserName', 'contractSubject', 'subcontractor', 'revisionNumber', 'revisionType', 'processTitle', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage','projectTitle','personTitle'], 'safe', 'on' => 'search'],
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type) {
        $processEntityTable = ProcessEntity::tableName();
        $personTable= \app\modules\OBS\models\Person::tableName();
        $revisionTypeTable = RevisionType::tableName();
        $workflowContractTable = WorkflowContract::tableName();
        $actionTable = Action::tableName();
        $processTableName = Process::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = ProcessEntity::find()
                ->contract($type)
                ->leftJoin($revisionTypeTable, $revisionTypeTable . '.ContractRevisionTypeID=' . $workflowContractTable . '.RevisionTypeIDRef')
                ->leftJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
                ->leftJoin($personTable, "$personTable.PersonID = $workflowContractTable.SignerPersonIDRef")
                ->leftJoin($userTable, "$userTable.UserID = $processEntityTable.LastUserIDRef")
                ->leftJoin($processTableName, "$processTableName.ProcessID=$processEntityTable.ProcessIDRef");
        
//        echo $activeQuery->createCommand()->rawSql;
//        die();
//        \app\components\Console::log($activeQuery->createCommand()->rawSql);
        $dataProvider = new ActiveDataProvider([
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'LastActionMessage',
                    'WorkflowContractID' => [
                        'asc' => [$workflowContractTable . '.WorkflowContractID' => SORT_ASC],
                        'desc' => [$workflowContractTable . '.WorkflowContractID' => SORT_DESC],
                    ],
                    'ContractNo' => [
                        'asc' => [$workflowContractTable . '.ContractNo' => SORT_ASC],
                        'desc' => [$workflowContractTable . '.ContractNo' => SORT_DESC],
                    ],
                    'ContractIDRef' => [
                        'asc' => [$workflowContractTable . '.ContractIDRef' => SORT_ASC],
                        'desc' => [$workflowContractTable . '.ContractIDRef' => SORT_DESC],
                    ],
                    'ContractSubject' => [
                        'asc' => [$workflowContractTable . '.ContractSubject' => SORT_ASC],
                        'desc' => [$workflowContractTable . '.ContractSubject' => SORT_DESC],
                    ],
                    'SubContractorPersonTitle' => [
                        'asc' => [$workflowContractTable . '.SubContractorPersonTitle' => SORT_ASC],
                        'desc' => [$workflowContractTable . '.SubContractorPersonTitle' => SORT_DESC],
                    ],
                    'ActionTitle2' => [
                        'asc' => [$actionTable . '.ActionTitle2' => SORT_ASC],
                        'desc' => [$actionTable . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionDateHijri' => [
                        'asc' => [$processEntityTable . '.LastActionDateHijri' => SORT_ASC],
                        'desc' => [$processEntityTable . '.LastActionDateHijri' => SORT_DESC],
                    ],
                    'processTitle' => [
                        'asc' => [$processTableName . '.ProcessTitle' => SORT_ASC],
                        'desc' => [$processTableName . '.ProcessTitle' => SORT_DESC],
                    ],
                    'RevisionNumber' => [
                        'asc' => [$workflowContractTable . '.RevisionNumber' => SORT_ASC],
                        'desc' => [$workflowContractTable . '.RevisionNumber' => SORT_DESC],
                    ],
                    'ContractRevisionType' => [
                        'asc' => [$revisionTypeTable . '.ContractRevisionType' => SORT_ASC],
                        'desc' => [$revisionTypeTable . '.ContractRevisionType' => SORT_DESC],
                    ],
                    'personTitle' => [
                        'asc' => ["$personTable.PersonTitle" => SORT_ASC],
                        'desc' => ["$personTable.PersonTitle" => SORT_DESC],
                    ],
                    'UserName' => [
//                        'asc' => ["$userTable.UserName" => SORT_ASC],
//                        'desc' => ["$userTable.UserName" => SORT_DESC],
                        'asc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_ASC],
                        'desc' => ["concat($userTable.FirstName, ' ', $userTable.LastName)" => SORT_DESC],
                    ],
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }

        $activeQuery
                ->andFilterWhere(['LIKE', $workflowContractTable . '.WorkflowContractID', $this->WorkflowContractID])
                ->andFilterWhere(['LIKE', $workflowContractTable . '.ContractIDRef', $this->ContractIDRef])
                ->andFilterWhere(['LIKE', $workflowContractTable . '.ContractNo', $this->ContractNo])
                ->andFilterWhere(['LIKE', $workflowContractTable . '.ContractSubject', $this->ContractSubject])
                ->andFilterWhere(['LIKE', $workflowContractTable . '.SubContractorPersonTitle', $this->SubContractorPersonTitle])
                ->andFilterWhere(['LIKE', $workflowContractTable . '.RevisionNumber', $this->RevisionNumber])
                ->andFilterWhere(['LIKE', $revisionTypeTable . '.ContractRevisionType', $this->ContractRevisionType])
                ->andFilterWhere(['LIKE', $actionTable . '.ActionTitle2', $this->ActionTitle2])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['LIKE', "$personTable.PersonTitle", $this->personTitle])
                ->andFilterWhere(['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->UserName])
                ;

//                echo $activeQuery->createCommand()->rawSql;

        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $personTable = \app\modules\OBS\models\Person::tableName();
        $workflwoContractTable = WorkflowContract::tableName();
        $activeQuery =  ProcessEntity::find()->contract('Graph')->reportItem($itemId)
                ->leftJoin(RevisionType::tableName(), RevisionType::tableName() . '.ContractRevisionTypeID=' . WorkflowContract::tableName() . '.RevisionTypeIDRef')
            ->leftJoin(WorkflowContractEps::tableName(), WorkflowContract::tableName() . '.WorkflowContractID=' . WorkflowContractEps::tableName() . '.WorkflowContractIDRef')
            ->leftJoin(Epsnew::tableName(), WorkflowContractEps::tableName() . '.EPSIDRef=' . Epsnew::tableName() . '.EPSID')
            ->leftJoin(Epsnew::tableName().' as project', Epsnew::tableName() . '.ParentEPSIDRef = project.EPSID')
                ->innerJoin(['signer' => $personTable], "signer.PersonID=$workflwoContractTable.SignerPersonIDRef")
                ->joinWith(['process', 'lastAction'])
            ->andWhere(WorkflowContractEps::tableName() . '.isDeleted is null '
                . 'or ' . WorkflowContractEps::tableName() . '.isDeleted != 1');

//        echo $activeQuery->createCommand()->rawSql;
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
                ->andFilterWhere(['LIKE', WorkflowContract::tableName() . '.ContractNo', $this->contractNo])
                ->andFilterWhere(['LIKE', WorkflowContract::tableName() . '.ContractSubject', $this->ContractSubject])
                ->andFilterWhere(['LIKE', WorkflowContract::tableName() . '.SubContractorPersonTitle', $this->subcontractor])
                ->andFilterWhere(['LIKE', WorkflowContract::tableName() . '.RevisionNumber', $this->revisionNumber])
                ->andFilterWhere(['LIKE', RevisionType::tableName() . '.ContractRevisionType', $this->revisionType])
                ->andFilterWhere(['LIKE', Action::tableName() . '.ActionTitle2', $this->lastActionTitle])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'signer.PersonTitle', $this->personTitle])
                ->andFilterWhere(['LIKE', Process::tableName() . '.ProcessTitle', $this->processTitle]);

        return $dataProvider;
    }

}
