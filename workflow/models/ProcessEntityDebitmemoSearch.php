<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\modules\contract\models\Contract;
use app\modules\invoice\models\InvoiceTypes;
use app\modules\contract\models\ContractType;
use app\modules\OBS\models\Person;
use app\components\CandoHelper;
use app\modules\account\models\User;

class ProcessEntityDebitmemoSearch extends ProcessEntity {

    public $signer;
    public $contractNo;
    public $contractSubject;
    public $subContractorTitle;
    public $debitmemoNo;
    public $debitmemoDateHijri;
    public $lastActionTitle;
    public $processTitle;
    public $description;
    public $receiver;
    public $typeId;
    public $lastUserName;
    // for workflow todo list 
    public $DEBIT_MEMO_TODO_REPORT_TYPE = [
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
            [['typeId','signer','contractNo', 'contractSubject', 'subContractorTitle', 'debitmemoNo', 'debitmemoDateHijri', 'processTitle', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage', 'description', 'lastUserName', 'receiver'], 'safe']
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $debitmemoTable = \app\modules\debitmemo\models\Debitmemo::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        $activeQuery = ProcessEntityDebitmemoSearch::find()->debitmemo($type)
                ->leftJoin($contractTable, "$debitmemoTable.contractIdRef=$contractTable.ContractID")
                ->leftJoin(ContractType::tableName(), Contract::tableName() . '.ContractType=' . ContractType::tableName() . '.ContractTypeID')
                ->leftJoin(Person::tableName(), Contract::tableName() . '.SubContractorPersonIDRef=' . Person::tableName() . '.PersonID')
                ->joinWith(['process', 'process', 'lastAction']);
        
//        echo $activeQuery->createCommand()->rawSql;
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => ['contractNo',
                    'contractSubject',
                    'subContractorTitle' => [
                        'asc' => [Person::tableName() . '.PersonTitle' => SORT_ASC],
                        'desc' => [Person::tableName() . '.PersonTitle' => SORT_DESC]
                    ],
                    'typeId' => [
                        'asc' => ["$debitmemoTable.typeId" => SORT_ASC],
                        'desc' => ["$debitmemoTable.typeId" => SORT_DESC]
                    ],
                    'debitmemoNo',
                    'debitmemoDateHijri',
                    'processTitle',
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionDateHijri',
                    'LastActionMessage'
                    ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->innerJoin("$personTable as reciver", "reciver.PersonID = $debitmemoTable.ownerCompanyIdRef")
                ->andFilterWhere(['LIKE', "$debitmemoTable.typeId", $this->typeId])
                ->andFilterWhere(['LIKE', "$contractTable.ContractNo", $this->contractNo])
                ->andFilterWhere(['or', ['LIKE', "$contractTable.ContractSubject", str_replace('ی', 'ي', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ي', 'ی', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ک', 'ك', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ك', 'ک', $this->contractSubject)]
                ])
                ->andFilterWhere(['or', ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                ])
                ->andFilterWhere(['LIKE', "$debitmemoTable.debitmemoNo", $this->debitmemoNo])
                ->andFilterWhere(['LIKE', "$debitmemoTable.description", $this->description])
                ->andFilterWhere(['LIKE', "reciver.PersonTitle", $this->receiver])
//                ->andFilterWhere(['LIKE', "$debitmemoTable.ownerCompanyIdRef", $this->receiver])
//                ->andFilterWhere(['LIKE', "$processEntityTable.ProcessIDRef", $this->receiver])
                ->andFilterWhere(['LIKE', "$debitmemoTable.debitmemoDateHijri", $this->debitmemoDateHijri])
                ->andFilterWhere(['or', ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['or', ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ی', 'ي', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ي', 'ی', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ک', 'ك', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ك', 'ک', $this->processTitle)]])
//                ->andFilterWhere(['LIKE', CandoHelper::braketTable(User::tableName()) . '.UserName', $this->lastUserName])
                ->andFilterWhere(['LIKE', "concat($userTable.FirstName, ' ', $userTable.LastName)", $this->lastUserName]);
//        echo $activeQuery->createCommand()->rawSql;
//        die();
        return $dataProvider;
    }

    public function searchWorkflowReport($params, $itemId) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');

        $debitmemoTable = \app\modules\debitmemo\models\Debitmemo::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();

        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->reportItem($itemId)
            ->debitmemo('graph')
            ->leftJoin($contractTable, "$debitmemoTable.contractIdRef=$contractTable.ContractID")
            ->leftJoin(ContractType::tableName(), Contract::tableName() . '.ContractType=' . ContractType::tableName() . '.ContractTypeID')
            ->leftJoin(Person::tableName(), Contract::tableName() . '.SubContractorPersonIDRef=' . Person::tableName() . '.PersonID')
            ->leftJoin(["signerTable"=>Person::tableName()], "$contractTable.SignerPersonIDRef=signerTable.PersonID")
            ->joinWith(['process', 'lastAction']);

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [
                    'contractNo' => [
                        'asc' => ["$contractTable.ContractNo" => SORT_ASC],
                        'desc' => ["$contractTable.ContractNo" => SORT_DESC]
                    ],
                    'contractSubject' => [
                        'asc' => ["$contractTable.contractSubject" => SORT_ASC],
                        'desc' => ["$contractTable.contractSubject" => SORT_DESC]
                    ],
                    'subContractorTitle' => [
                        'asc' => [Person::tableName() . '.PersonTitle' => SORT_ASC],
                        'desc' => [Person::tableName() . '.PersonTitle' => SORT_DESC]
                    ],
                    'debitmemoNo',
                    'debitmemoDateHijri',
                    'processTitle',
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'signer' => [
                        'asc' => ['signerTable.PersonTitle' => SORT_ASC],
                        'desc' => ['signerTable.PersonTitle' => SORT_DESC],
                    ],
                    'LastActionDateHijri',
                    'LastActionMessage'],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->innerJoin("$personTable as resiver", "resiver.PersonID = $debitmemoTable.ownerCompanyIdRef")
                ->andFilterWhere(['LIKE', "$contractTable.ContractNo", $this->contractNo])
                ->andFilterWhere(['or', ['LIKE', "$contractTable.ContractSubject", str_replace('ی', 'ي', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ي', 'ی', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ک', 'ك', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ك', 'ک', $this->contractSubject)]
                ])
                ->andFilterWhere(['or', ['LIKE', "signerTable.PersonTitle", str_replace('ی', 'ي', $this->signer)]
                    , ['LIKE', "signerTable.PersonTitle", str_replace('ي', 'ی', $this->signer)]
                    , ['LIKE', "signerTable.PersonTitle", str_replace('ک', 'ك', $this->signer)]
                    , ['LIKE', "signerTable.PersonTitle", str_replace('ك', 'ک', $this->signer)]
                ])
                ->andFilterWhere(['or', ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                ])
                ->andFilterWhere(['LIKE', "$debitmemoTable.debitmemoNo", $this->debitmemoNo])
                ->andFilterWhere(['LIKE', "$debitmemoTable.description", $this->description])
                ->andFilterWhere(['LIKE', "resiver.PersonTitle", $this->receiver])
//                ->andFilterWhere(['LIKE', "$debitmemoTable.ownerCompanyIdRef", $this->receiver])
//                ->andFilterWhere(['LIKE', "$processEntityTable.ProcessIDRef", $this->receiver])
                ->andFilterWhere(['LIKE', "$debitmemoTable.debitmemoDateHijri", $this->debitmemoDateHijri])
                ->andFilterWhere(['or', ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['or', ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ی', 'ي', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ي', 'ی', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ک', 'ك', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ك', 'ک', $this->processTitle)]
        ]);
        
        return $dataProvider;
    }

}
