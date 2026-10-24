<?php

namespace app\modules\workflow\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\components\CandoHelper;
use app\modules\account\models\User;
use app\modules\document\models\StampDocument;
use app\modules\document\models\StampFlowDocument;
use app\modules\OBS\models\Obsnew;
use app\modules\OBS\models\Person;
use app\modules\automation\models\HumanResource;
use app\modules\automation\models\PositionType;

class ProcessEntityStampDocumentSearch extends ProcessEntity
{
    public $lastActionTitle;
    public $LastUserName;
    public $LastActionMessage;
    public $LastActionDateHijri;
    public $OrganizationalMaker;
    public $StampTypeIDRef;
    public $StampId;
    public $StampTitle;
    public $StampCode;
    public $RequestDate;
    public $Status;
    public $CompanyTitle;
    public $PositionTitle;
    public $StampPurpose;
    public $obsTitle;
    public $Company;
    public $NationalCode;
    public $LastFlowIDRef;

    public $STAMP_DOCUMENT_TODO_REPORT_TYPE = [
        ['name' => 'همه', 'icon' => 'fa-asterisk', 'value' => 'All'],
        ['name' => 'در حال انجام', 'icon' => 'fa-list-ul', 'value' => 'ToDo'],
    ];

    public static function getDb()
    {
        return \app\components\Utility::db();
    }

 public function rules()
{
    return [
        [[
            'StampId', 'StampTitle', 'StampCode', 'RequestDate', 'Status', 'CompanyTitle',
            'PositionTitle', 'StampPurpose', 'obsTitle', 'Company', 'OrganizationalMaker',
            'StampTypeIDRef', 'lastActionTitle', 'LastUserName', 'LastActionMessage',
            'LastActionDateHijri', 'NationalCode', 'LastFlowIDRef',
        ], 'safe'],
    ];
}

    public function search($params, $type)
    {
        $pe = ProcessEntity::tableName();
        $sd = StampDocument::tableName();
        $user = User::tableName();
        $obs = Obsnew::tableName();
        $person = Person::tableName();
        $action = Action::tableName();
        $sf = StampFlowDocument::tableName();
        $hr = HumanResource::tableName();
        $pt = PositionType::tableName();

        $query = ProcessEntity::find()->stampDocument($type)
            ->leftJoin(CandoHelper::braketTable($user) . ' AS lastUser', "lastUser.UserID = $pe.LastUserIDRef")
            ->leftJoin("$obs AS stampObs", "stampObs.obsId = $sd.ObsIDRef")
            ->leftJoin("$pt AS positionType", "positionType.PositionTypeID = stampObs.positionTypeIdRef")
            ->leftJoin("$person AS companyPerson", "companyPerson.PersonID = stampObs.companyIdRef")
            ->leftJoin("$action AS lastAction", "lastAction.ActionID = $pe.LastActionIDRef")
            ->leftJoin("$hr AS ownerHuman", "ownerHuman.Id = $sd.HumanResourceIDRef")
            ->leftJoin("$sf AS lastStampFlow", "lastStampFlow.FlowId = $sd.LastFlowIDRef")
            ->leftJoin("$hr AS holderHuman", "holderHuman.Id = lastStampFlow.HumanIDRef");

        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $query,
            'pagination' => ['pageSize' => Yii::$app->params['pageSize']],
            'sort' => [
                'defaultOrder' => ['StampId' => SORT_DESC],
                'attributes' => [
                    'StampId' => ['asc' => ["$sd.StampId" => SORT_ASC], 'desc' => ["$sd.StampId" => SORT_DESC]],
                    'StampTitle' => ['asc' => ["$sd.StampTitle" => SORT_ASC], 'desc' => ["$sd.StampTitle" => SORT_DESC]],
                    'StampCode' => ['asc' => ["$sd.StampCode" => SORT_ASC], 'desc' => ["$sd.StampCode" => SORT_DESC]],
                    'RequestDate' => ['asc' => ["$sd.RequestDate" => SORT_ASC], 'desc' => ["$sd.RequestDate" => SORT_DESC]],
                    'Status' => ['asc' => ["$sd.Status" => SORT_ASC], 'desc' => ["$sd.Status" => SORT_DESC]],
                    'CompanyTitle' => ['asc' => ['companyPerson.PersonTitle' => SORT_ASC], 'desc' => ['companyPerson.PersonTitle' => SORT_DESC]],
                    'PositionTitle' => ['asc' => ["CONCAT(IFNULL(positionType.Title,''), ' ', IFNULL(stampObs.obsTitle,''))" => SORT_ASC],'desc' => ["CONCAT(IFNULL(positionType.Title,''), ' ', IFNULL(stampObs.obsTitle,''))" => SORT_DESC],],
                    'StampPurpose' => ['asc' => ["$sd.StampPurpose" => SORT_ASC], 'desc' => ["$sd.StampPurpose" => SORT_DESC]],
                    'OrganizationalMaker' => ['asc' => ["$sd.OrganizationalMaker" => SORT_ASC], 'desc' => ["$sd.OrganizationalMaker" => SORT_DESC]],
                    'StampTypeIDRef' => ['asc' => ["$sd.StampTypeIDRef" => SORT_ASC], 'desc' => ["$sd.StampTypeIDRef" => SORT_DESC]],
                    'lastActionTitle' => ['asc' => ['lastAction.ActionTitle2' => SORT_ASC], 'desc' => ['lastAction.ActionTitle2' => SORT_DESC]],
                    'LastUserName' => [
                        'asc' => ["CONCAT(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_ASC],
                        'desc' => ["CONCAT(lastUser.FirstName, ' ', lastUser.LastName)" => SORT_DESC],
                    ],
                    'LastActionDateHijri' => ['asc' => ["$pe.LastActionDateHijri" => SORT_ASC], 'desc' => ["$pe.LastActionDateHijri" => SORT_DESC]],
                ],
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            return $dataProvider;
        }

        $query
            ->andFilterWhere(['LIKE', "$sd.StampId", $this->StampId])
            ->andFilterWhere(['LIKE', "$sd.StampTitle", $this->StampTitle])
            ->andFilterWhere(['LIKE', "$sd.StampCode", $this->StampCode])
            ->andFilterWhere(['LIKE', "$sd.RequestDate", $this->RequestDate])
            ->andFilterWhere(['=', "$sd.Status", $this->Status])
            ->andFilterWhere(['LIKE', "$sd.StampPurpose", $this->StampPurpose])
            ->andFilterWhere(['=', "$sd.OrganizationalMaker", $this->OrganizationalMaker])
            ->andFilterWhere(['=', "$sd.StampTypeIDRef", $this->StampTypeIDRef])
            ->andFilterWhere(['=', 'companyPerson.PersonID', $this->CompanyTitle])
            ->andFilterWhere(['LIKE', "CONCAT(IFNULL(positionType.Title,''), ' ', IFNULL(stampObs.obsTitle,''))", $this->PositionTitle])
            ->andFilterWhere(['or',
                ['LIKE', 'lastAction.ActionTitle1', $this->lastActionTitle],
                ['LIKE', 'lastAction.ActionTitle2', $this->lastActionTitle],
            ])
            ->andFilterWhere(['LIKE', "$pe.LastActionDateHijri", $this->LastActionDateHijri]);

        if (!empty($this->LastUserName)) {
            $s = trim($this->LastUserName);
            $full = "CONCAT(IFNULL(lastUser.FirstName,''), ' ', IFNULL(lastUser.LastName,''))";
            $query->andWhere(['or',
                ['LIKE', $full, $s],
                ['LIKE', $full, str_replace('ی', 'ي', $s)],
                ['LIKE', $full, str_replace('ي', 'ی', $s)],
                ['LIKE', $full, str_replace('ک', 'ك', $s)],
                ['LIKE', $full, str_replace('ك', 'ک', $s)],
                ['LIKE', $full, str_replace(['ی', 'ک'], ['ي', 'ك'], $s)],
                ['LIKE', $full, str_replace(['ي', 'ك'], ['ی', 'ک'], $s)],
                ['LIKE', 'lastUser.LastName', $s],
                ['LIKE', 'lastUser.LastName', str_replace('ی', 'ي', $s)],
                ['LIKE', 'lastUser.LastName', str_replace('ي', 'ی', $s)],
                ['LIKE', 'lastUser.FirstName', $s],
                ['LIKE', 'lastUser.FirstName', str_replace('ی', 'ي', $s)],
                ['LIKE', 'lastUser.FirstName', str_replace('ي', 'ی', $s)],
            ]);
        }

        if (!empty($this->NationalCode)) {
            $query->andWhere(['or',
                ['LIKE', 'ownerHuman.NationalCode', $this->NationalCode],
                ['LIKE', 'ownerHuman.FirstName', $this->NationalCode],
                ['LIKE', 'ownerHuman.LastName', $this->NationalCode],
                ['LIKE', "CONCAT(ownerHuman.FirstName, ' ', ownerHuman.LastName)", $this->NationalCode],
            ]);
        }

        if (!empty($this->LastFlowIDRef)) {
            $query->andWhere(['or',
                ['LIKE', 'holderHuman.NationalCode', $this->LastFlowIDRef],
                ['LIKE', 'holderHuman.FirstName', $this->LastFlowIDRef],
                ['LIKE', 'holderHuman.LastName', $this->LastFlowIDRef],
                ['LIKE', "CONCAT(holderHuman.FirstName, ' ', holderHuman.LastName)", $this->LastFlowIDRef],
            ]);
        }

        return $dataProvider;
    }

    public static function getStatusList()
    {
        return [
            'pending' => Yii::t('document', 'pending'),
            'approved' => Yii::t('document', 'approved'),
            'rejected' => Yii::t('document', 'rejected'),
            'destroyed' => Yii::t('document', 'destroyed'),
           'in_process' => Yii::t('document', 'in_process'),
        ];
    }

    public static function getOrganizationalMakerList()
    {
        return [
            1 => Yii::t('document', 'رده بالا دستی'),
            2 => Yii::t('document', 'رده'),
        ];
    }
}