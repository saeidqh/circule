<?php

namespace app\modules\workflow\models;

use app\modules\contract\models\ContractEps;
use app\modules\wbs\models\Epsnew;
use app\modules\workflowContract\models\WorkflowContractEps;
use Yii;
use yii\data\ActiveDataProvider;
use app\modules\contract\models\Contract;
use app\modules\invoice\models\InvoiceTypes;
use app\modules\contract\models\ContractType;
use app\modules\OBS\models\Person;
use app\modules\invoice\models\InvoiceEdit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProcessEntityInvoiceSearch extends ProcessEntity {

    public $contractNo;
    public $contractSubject;
    public $subContractorTitle;
    public $contractTypeTitle;
    public $Title;
    public $projectTitle;
    public $CutOffDateHijri;
    public $InsertDateHijri;
    public $Duration;
    public $InvoiceType;
    public $lastActionTitle;
    public $processTitle;
    // for workflow todo list 
    public $INVOICE_TODO_REPORT_TYPE = [
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
            [['InvoiceIDRef','contractNo', 'contractSubject','projectTitle', 'subContractorTitle', 'contractTypeTitle', 'Title', 'CutOffDateHijri', 'InvoiceType', 'processTitle', 'lastActionTitle', 'LastActionDateHijri', 'LastActionMessage', 'InsertDateHijri'], 'safe']
        ];
    }

    public static function getDb() {
        return \app\components\Utility::db();
    }

    public function search($params, $type,$maincontract=0) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = \app\modules\workflow\models\ProcessEntity::find()
                ->invoice($type,$maincontract)
                ->leftJoin(Contract::tableName(), InvoiceEdit::tableName() . '.ContractID=' . Contract::tableName() . '.ContractID')
                ->leftJoin(InvoiceTypes::tableName(), InvoiceEdit::tableName() . '.InvoiceType=' . InvoiceTypes::tableName() . '.InvoiceTypeID')
                ->leftJoin(ContractType::tableName(), Contract::tableName() . '.ContractType=' . ContractType::tableName() . '.ContractTypeID')
                ->leftJoin(Person::tableName(), Contract::tableName() . '.SubContractorPersonIDRef=' . Person::tableName() . '.PersonID')
                ->joinWith(['process', 'lastAction']);  
//        echo $activeQuery->createCommand()->rawSql;
//        die();
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => ['contractNo',
                    'contractSubject',
                    'InvoiceIDRef',
                    'subContractorTitle' => [
                        'asc' => [Person::tableName() . '.PersonTitle' => SORT_ASC],
                        'desc' => [Person::tableName() . '.PersonTitle' => SORT_DESC]
                    ],
                    'contractTypeTitle',
                    'Title',
                    'projectTitle',
                    'CutOffDateHijri',
                    'InsertDateHijri',
                    'InvoiceType' => [
                        'asc' => [InvoiceTypes::tableName() . '.InvoiceType' => SORT_ASC],
                        'desc' => [InvoiceTypes::tableName() . '.InvoiceType' => SORT_DESC],
                    ],
                    'processTitle',
                    'lastActionTitle' => [
                        'asc' => [Action::tableName() . '.ActionTitle2' => SORT_ASC],
                        'desc' => [Action::tableName() . '.ActionTitle2' => SORT_DESC],
                    ],
                    'LastActionDateHijri',
                    'LastActionMessage'],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        $activeQuery
                ->andFilterWhere(['LIKE', ProcessEntity::tableName() . '.InvoiceIDRef', $this->InvoiceIDRef])
                ->andFilterWhere(['LIKE', Contract::tableName() . '.ContractNo', $this->contractNo])
                ->andFilterWhere(['or', ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ی', 'ي', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ي', 'ی', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ک', 'ك', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ك', 'ک', $this->contractSubject)]
                ])
                ->andFilterWhere(['or', ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                    , ['LIKE', Person::tableName() . '.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                ])
                ->andFilterWhere(['LIKE', InvoiceEdit::tableName() . '.Title', $this->Title])
                ->andFilterWhere(['LIKE', InvoiceEdit::tableName() . '.CutOffDateHijri', $this->CutOffDateHijri])
                ->andFilterWhere(['LIKE', InvoiceEdit::tableName() . '.InsertDateHijri', $this->InsertDateHijri])
                ->andFilterWhere(['LIKE', InvoiceTypes::tableName() . '.InvoiceType', $this->InvoiceType])
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

    public function searchWorkflowReport($params, $itemId) {
        $uid = Yii::$app->session->get('UserID');
        $obp = Yii::$app->session->get('ObsPosition');
        $activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')->invoice('Graph')->reportItem($itemId)
                ->leftJoin(Contract::tableName(), InvoiceEdit::tableName() . '.ContractID=' . Contract::tableName() . '.ContractID')
                ->leftJoin(InvoiceTypes::tableName(), InvoiceEdit::tableName() . '.InvoiceType=' . InvoiceTypes::tableName() . '.InvoiceTypeID')
                ->leftJoin(ContractType::tableName(), Contract::tableName() . '.ContractType=' . ContractType::tableName() . '.ContractTypeID')
                ->leftJoin(ContractEps::tableName(), Contract::tableName() . '.ContractID=' . ContractEps::tableName() . '.ContractIDRef')
                ->leftJoin(Epsnew::tableName(), ContractEps::tableName() . '.EPSIDRef=' . Epsnew::tableName() . '.EPSID')
                ->leftJoin(Epsnew::tableName().' as project', Epsnew::tableName() . '.ParentEPSIDRef = project.EPSID')
                ->leftJoin(Person::tableName() . " as subcontractor", "subcontractor.PersonID=" . Contract::tableName() . '.SubContractorPersonIDRef')
                ->joinWith(['process', 'lastAction'])
            ->andWhere(ContractEps::tableName() . '.isDeleted is null '
                . 'or ' . ContractEps::tableName() . '.isDeleted != 1');
//        echo $activeQuery->createCommand()->rawSql;
//        die();
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
                ->andFilterWhere(['LIKE', Contract::tableName() . '.ContractNo', $this->contractNo])
                ->andFilterWhere(['or', ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ی', 'ي', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ي', 'ی', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ک', 'ك', $this->contractSubject)]
                    , ['LIKE', Contract::tableName() . '.ContractSubject', str_replace('ك', 'ک', $this->contractSubject)]
                ])
                ->andFilterWhere(['or', ['LIKE', 'subcontractor.PersonTitle', str_replace('ی', 'ي', $this->subContractorTitle)]
                    , ['LIKE', 'subcontractor.PersonTitle', str_replace('ي', 'ی', $this->subContractorTitle)]
                    , ['LIKE', 'subcontractor.PersonTitle', str_replace('ک', 'ك', $this->subContractorTitle)]
                    , ['LIKE', 'subcontractor.PersonTitle', str_replace('ك', 'ک', $this->subContractorTitle)]
                ])
                ->andFilterWhere(['or', ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ی', 'ي', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ي', 'ی', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ک', 'ك', $this->lastActionTitle)]
                    , ['LIKE', Action::tableName() . '.ActionTitle2', str_replace('ك', 'ک', $this->lastActionTitle)]
                ])
                ->andFilterWhere(['LIKE', ContractType::tableName() . '.ContractTypeTitle', $this->contractTypeTitle])
                ->andFilterWhere(['LIKE', 'project.EPSTitle', $this->projectTitle])
                ->andFilterWhere(['LIKE', InvoiceEdit::tableName() . '.Title', $this->Title])
                ->andFilterWhere(['LIKE', InvoiceEdit::tableName() . '.CutOffDateHijri', $this->CutOffDateHijri])
                ->andFilterWhere(['LIKE', InvoiceEdit::tableName() . '.InsertDateHijri', $this->InsertDateHijri])
                ->andFilterWhere(['LIKE', InvoiceTypes::tableName() . '.InvoiceType', $this->InvoiceType])
                ->andFilterWhere(['LIKE', 'LastActionDateHijri', $this->LastActionDateHijri])
                ->andFilterWhere(['LIKE', 'LastActionMessage', $this->LastActionMessage])
                ->andFilterWhere(['or', ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ی', 'ي', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ي', 'ی', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ک', 'ك', $this->processTitle)]
                    , ['LIKE', Process::tableName() . '.ProcessTitle', str_replace('ك', 'ک', $this->processTitle)]
                ])
        ;
        return $dataProvider;
    }

}
