<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;
use app\modules\cashRequest\models\CashRequest;

class ProcessEntityClaimSearch extends ProcessEntity {

    public $id;
    public $RequestDateHijri;
    public $Description;
    public $subject;
    public $lastActionTitle;
    public $processTitle;
    public $lastUserName;
    public $contractNo;
    public $contractSubject;
    public $subContractor;
    public $lastUser;
    public $claimNo;
    // for workflow todo list 
    public $CLAIM_TODO_REPORT_TYPE = [
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
            [['id', 'subject', 'RequestDateHijri', 'Description', 'lastActionTitle', 'LastActionDateHijri', 'LastActionAutoMessage', 'LastActionMessage', 'processTitle', 'lastUserName'
            , 'contractNo', 'contractSubject', 'subContractor', 'lastUser', 'claimNo'], 'safe']
        ];
    }

    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $claimTableName = \app\modules\claim\models\Claim::tableName();
        $actionTable = Action::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $userTable = User::tableName();

        $activeQuery = static::find()->claim($type)
                ->leftJoin($actionTable, "$actionTable.ActionID=.$processEntityTable.LastActionIDRef")
                ->leftJoin($userTable, "$userTable.UserID=$processEntityTable.LastUserIDRef")
                ->innerjoin($contractTable, "$contractTable.ContractID=$claimTableName.contractIdRef")
                ->innerJoin(["subContractor" => $personTable], "$contractTable.SubContractorPersonIDRef=subContractor.PersonID")
        ;

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
                ->andFilterWhere(['LIKE', "$contractTable.ContractNo", $this->contractNo])
                ->andFilterWhere(['LIKE', "$contractTable.ContractSubject", $this->contractSubject])
                ->andFilterWhere(['or', ['LIKE', 'subContractor.PersonTitle', str_replace('ی', 'ي', $this->subContractor)]
                    , ['LIKE', 'subContractor.PersonTitle', str_replace('ي', 'ی', $this->subContractor)]
                    , ['LIKE', 'subContractor.PersonTitle', str_replace('ک', 'ك', $this->subContractor)]
                    , ['LIKE', 'subContractor.PersonTitle', str_replace('ك', 'ک', $this->subContractor)]
                ])
                ->andFilterWhere(['LIKE', "$claimTableName.id", $this->id])
                ->andFilterWhere(['LIKE', "$claimTableName.claimNo", $this->claimNo])
                ->andFilterWhere(['LIKE', "$claimTableName.createdAt", $this->RequestDateHijri])
                ->andFilterWhere(['LIKE', "$claimTableName.description", $this->Description])
                ->andFilterWhere(['LIKE', "$claimTableName.subject", $this->subject])
                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionDateHijri", $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', "$actionTable.ActionTitle2", $this->lastActionTitle])
                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionMessage", $this->LastActionMessage])
                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionAutoMessage", $this->LastActionAutoMessage])
                ->andFilterWhere(['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->lastUser]);
        
        return $dataProvider;
    }

    public function list( $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $claimTableName = \app\modules\claim\models\Claim::tableName();
        $actionTable = Action::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $userTable = User::tableName();

        $activeQuery = static::find()->claim($type)
            ->leftJoin($actionTable, "$actionTable.ActionID=.$processEntityTable.LastActionIDRef")
            ->leftJoin($userTable, "$userTable.UserID=$processEntityTable.LastUserIDRef")
            ->innerjoin($contractTable, "$contractTable.ContractID=$claimTableName.contractIdRef")
            ->innerJoin(["subContractor" => $personTable], "$contractTable.SubContractorPersonIDRef=subContractor.PersonID")
            ;
        ;
        return $activeQuery;
    }

    public function searchWorkflowReport($params, $itemId) {
        $type = 'All';
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $claimTableName = \app\modules\claim\models\Claim::tableName();
        $actionTable = Action::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $userTable = User::tableName();

        $activeQuery = static::find()->claim($type)->reportItem($itemId)
                ->leftJoin($actionTable, "$actionTable.ActionID=.$processEntityTable.LastActionIDRef")
                ->leftJoin($userTable, "$userTable.UserID=$processEntityTable.LastUserIDRef")
                ->innerjoin($contractTable, "$contractTable.ContractID=$claimTableName.contractIdRef")
                ->innerJoin(["subContractor" => $personTable], "$contractTable.SubContractorPersonIDRef=subContractor.PersonID")
//                ->all()
        ;
//        echo $activeQuery->createCommand()->rawSql;
//        die();
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => false,
            /*'pagination' => [
//                'pageSize' => Yii::$app->params['pageSize'],
                'pageSize' => 50,
            ],*/
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', "$contractTable.ContractNo", $this->contractNo])
                ->andFilterWhere(['LIKE', "$contractTable.ContractSubject", $this->contractSubject])
                ->andFilterWhere(['or', ['LIKE', 'subContractor.PersonTitle', str_replace('ی', 'ي', $this->subContractor)]
                    , ['LIKE', 'subContractor.PersonTitle', str_replace('ي', 'ی', $this->subContractor)]
                    , ['LIKE', 'subContractor.PersonTitle', str_replace('ک', 'ك', $this->subContractor)]
                    , ['LIKE', 'subContractor.PersonTitle', str_replace('ك', 'ک', $this->subContractor)]
                ])
                ->andFilterWhere(['LIKE', "$claimTableName.id", $this->id])
                ->andFilterWhere(['LIKE', "$claimTableName.claimNo", $this->claimNo])
                ->andFilterWhere(['LIKE', "$claimTableName.createdAt", $this->RequestDateHijri])
                ->andFilterWhere(['LIKE', "$claimTableName.description", $this->Description])
                ->andFilterWhere(['LIKE', "$claimTableName.subject", $this->subject])
                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionDateHijri", $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', "$actionTable.ActionTitle2", $this->lastActionTitle])
                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionMessage", $this->LastActionMessage])
                ->andFilterWhere(['LIKE', "$processEntityTable.LastActionAutoMessage", $this->LastActionAutoMessage])
                ->andFilterWhere(['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->lastUser]);
        
        return $dataProvider;
    }

}
