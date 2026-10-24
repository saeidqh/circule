<?php


namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityContractClosingDocumentSearch extends ProcessEntity {

    public $lastActionTitle;
    public $LastUserName;
//    public $LastActionDateHijri;
    public $LastActionMessage;
    public $ContractClosingDocumentIDRef;
    public $ContractDocumentTypeIDRef;
    public $ContractIDRef;
    public $ContractNo;
    public $signerTitle;
    public $subContractorTitle;

    // for workflow todo list
    public $TENDER_TODO_REPORT_TYPE = [
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
            [['ContractClosingDocumentIDRef', 'ContractDocumentTypeIDRef', 'ContractIDRef', 'ContractNo', 'signerTitle', 'subContractorTitle', 'LastUserName','lastActionTitle','LastActionDateHijri','LastActionMessage'], 'safe']
        ];
    }

    public function search($params, $type) {
        $actionTable = Action::tableName();
        $personTable= \app\modules\OBS\models\Person::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $contractClosingDocumentTable = \app\modules\contract\models\ContractDocument::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->contractClosingDocument($type)  
            ->leftJoin(\app\modules\OBS\models\Person::tableName() . ' as signer', \app\modules\contract\models\Contract::tableName() . '.SignerPersonIDRef=signer.PersonID')    
            ->innerJoin(\app\modules\OBS\models\Person::tableName() . ' as subContractor', \app\modules\contract\models\Contract::tableName() . '.SubContractorPersonIDRef=subContractor.PersonID')    
            ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef');
        
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'companyIDRef',
                    'tenderType',
                    'LastActionDateHijri',
                    'LastUserName' => [
                        'asc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_ASC],
                        'desc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_DESC],
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
            ->andFilterWhere(['LIKE', "$processEntityTable.ContractClosingDocumentIDRef", $this->ContractClosingDocumentIDRef])
            ->andFilterWhere(['LIKE', "$contractClosingDocumentTable.ContractIDRef", $this->ContractIDRef])
            ->andFilterWhere(['LIKE', $contractTable . '.ContractNo', $this->ContractNo])
            ->andFilterWhere(['LIKE', 'signer.PersonTitle', $this->signerTitle])    
            ->andFilterWhere(['or', ['LIKE', 'subContractor.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                        , ['LIKE', 'subContractor.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                        , ['LIKE', 'subContractor.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                        , ['LIKE', 'subContractor.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                    ])  
            ->andFilterWhere(['LIKE', "$contractClosingDocumentTable.ContractDocumentTypeIDRef", $this->ContractDocumentTypeIDRef])    
            ->andFilterWhere(['LIKE', "concat(lastUser.FirstName, ' ', lastUser.LastName)", $this->LastUserName])
            ->andFilterWhere(['LIKE', "LastActionDateHijri", $this->LastActionDateHijri])
            ->andFilterWhere(['or', ['LIKE', "$actionTable.ActionTitle1", str_replace('ی', 'ي', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ي', 'ی', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ک', 'ك', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ك', 'ک', $this->lastActionTitle)]
            ]);
        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $actionTable = Action::tableName();
        $tenderTable = \app\modules\tender\models\Tender::tableName();

        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()->tender('Graph')->reportItem($itemId)
            ->leftJoin(CandoHelper::braketTable(User::tableName()) . ' as lastUser', 'lastUser.UserID=' . ProcessEntity::tableName() . '.LastUserIDRef');
//        echo $activeQuery->createCommand()->rawSql;
//        die();
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'companyIDRef',
                    'tenderType',
                    'LastActionDateHijri',
                    'LastUserName' => [
                        'asc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_ASC],
                        'desc' => ["concat(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_DESC],
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
            ->andFilterWhere(['LIKE', "$tenderTable.TenderID", $this->TenderIDRef])
            ->andFilterWhere(['LIKE', "$tenderTable.companyIDRef", $this->companyIDRef])
            ->andFilterWhere(['LIKE', "$tenderTable.tenderType", $this->tenderType])    
            ->andFilterWhere(['LIKE', "concat(lastUser.FirstName, ' ', lastUser.LastName)", $this->LastUserName])
            ->andFilterWhere(['LIKE', "LastActionDateHijri", $this->LastActionDateHijri])
            ->andFilterWhere(['or', ['LIKE', "$actionTable.ActionTitle1", str_replace('ی', 'ي', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ي', 'ی', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ک', 'ك', $this->lastActionTitle)]
                , ['LIKE', "$actionTable.ActionTitle1", str_replace('ك', 'ک', $this->lastActionTitle)]
            ]);
        return $dataProvider;
    }

}