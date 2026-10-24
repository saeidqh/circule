<?php

namespace app\modules\workflow\models;

use Yii;
use app\modules\workflow\models\ProcessEntity;
use app\components\Utility;

class UserActivitySearch extends \yii\base\Model {

    public $viewMode;
    public $fromDate;
    public $toDate;
    public $uName;
    public $obsTitle;

    public function rules() {
        return [
            [['uName', 'obsTitle', 'toDate', 'fromDate'], 'safe', 'on' => 'search'],
        ];
    }

    public function search2($params) {
        $processTable = \app\modules\workflow\models\Process::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actinTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $userObsTable = \app\modules\account\models\UserObs::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        $uid = Yii::$app->session->get('UserID');
        $obsId = Yii::$app->session->get('ObsPosition');
        $companyId = Yii::$app->session->get('CompanyID');

//        $obsChildsModel = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $obsChildsModel = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'array');
        $this->load($params);
        $durationDate = "ActionDateHijri between '" . $this->fromDate . "' and '" . $this->toDate . "' and";
        
        /*$activeQuery = \app\components\ActiveModel::find('\app\modules\workflow\models\ProcessEntity')
                ->addSelect([
                    "UserID" => "$userTable.UserID",
                    "uName" => "$userTable.UserName",
                    "obsId" => "$obsTable.obsId",
                    "obsTitle" => "$obsTable.obsTitle",
                    "Action_CashRequest" => "count($processEntityTable.CashRequestIDRef)",
                    ])
                ->innerJoin($actionEntityTable, "$actionEntityTable.ProcessEntityIDRef=$processEntityTable.ProcessEntityID")
                ->innerJoin($userTable, "$userTable.UserID=$actionEntityTable.UserIDRef")
                ->innerJoin($obsTable, "$obsTable.obsId=$actionEntityTable.OBSCategory3IDRef")
                ->where(['between', 'ActionDateHijri', $this->fromDate, $this->toDate])
                ->andWhere(["$userTable.isActive" => 1])
                ->andWhere(["$actionEntityTable.OBSCategory3IDRef" => $obsChildsModel])
                ->groupBy(["$userTable.UserID", "$obsTable.obsId"]);*/
        
        $activeQuery = \app\components\ActiveModel::find('\app\modules\account\models\User')
                ->addSelect([
                    "UserID" => "$userTable.UserID",
                    "uName" => "$userTable.UserName",
                    "obsId" => "$obsTable.obsId",
                    "obsTitle" => "$obsTable.obsTitle",
                    "Action_CashRequest" => "count($processEntityTable.CashRequestIDRef)",
                    "Action_claim" => "count($processEntityTable.claimIdRef)",
                    "Action_invoice" => "count($processEntityTable.InvoiceIDRef)",
                    "Action_Contract" => "count($processEntityTable.WorkflowContractIDRef)",
                    "Action_Budget" => "count($processEntityTable.BudgetIDRef)",
                    "Action_capital" => "count($processEntityTable.capitalBalanceIdRef)",
                    "Action_debitmemo" => "count($processEntityTable.debitmemoIdRef)",
                    "Action_warranty" => "count($processEntityTable.warrantyIdRef)",
                    "Action_actualProgress" => "count($processEntityTable.actualProgressAnnualBudgetIdRef)",
                    ])
                ->innerJoin($userObsTable, "$userObsTable.UserIDRef=$userTable.UserID")
                ->innerJoin($obsTable, "$obsTable.obsId=$userObsTable.OBSCategory3IDRef")
                ->leftJoin($actionEntityTable, "$actionEntityTable.UserIDRef=$userObsTable.UserIDRef AND $actionEntityTable.OBSCategory3IDRef=$userObsTable.OBSCategory3IDRef")
                ->innerJoin($processEntityTable, "$processEntityTable.ProcessEntityID=$actionEntityTable.ProcessEntityIDRef")
                ->where(['between', "$actionEntityTable.ActionDateHijri", $this->fromDate, $this->toDate])
                ->andWhere(["$userTable.isActive" => 1])
                ->andWhere(["$actionEntityTable.OBSCategory3IDRef" => $obsChildsModel])
                ->groupBy(["$userTable.UserID", "$obsTable.obsId"]);

        echo $activeQuery->createCommand()->rawSql;
        die();
                
        $dataProvider = new ActiveDataProvider([
            'db' => \app\components\Utility::db(),
            'query' => $activeQuery,
            'pagination' => [
                'pageSize' => Yii::$app->params['pageSize'],
            ],
            'sort' => [
                'attributes' => [ 
                ],
            ],
        ]);
        if ((!$this->load($params)) and $this->validate()) {
            return $dataProvider;
        }
        return $dataProvider;
        
        $sql = "
SELECT user_table_user.UserID,user_table_user.UserName as uName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.CashRequestIDRef is not null) as 'Action_CashRequest'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.CashRequestIDRef is not null) as 'Process_CashRequest'
 

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.claimIdRef is not null) as 'Action_claim'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.claimIdRef is not null) as 'Process_claim'
 
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.InvoiceIDRef is not null) as 'Action_invoice'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.InvoiceIDRef is not null) as 'Process_Invoice'


,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.WorkflowContractIDRef is not null) as 'Action_Contract'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.WorkflowContractIDRef is not null) as 'Process_Contract'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.BudgetIDRef is not null) as 'Action_Budget'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.BudgetIDRef is not null) as 'Process_Budget'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.capitalBalanceIdRef is not null) as 'Action_capital'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.capitalBalanceIdRef is not null) as 'Process_capital'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.debitmemoIdRef is not null) as 'Action_debitmemo'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.debitmemoIdRef is not null) as 'Process_debitmemo'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.warrantyIdRef is not null) as 'Action_warranty'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.warrantyIdRef is not null) as 'Process_warranty'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as 'Action_actualProgress'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as 'Process_actualProgress'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId) as 'total_action'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)) as 'total_Process'

FROM  user_table_user 
INNER JOIN user_table_user_obs on user_table_user_obs.UserIDRef=user_table_user.UserID
INNER JOIN obs_table_obs_new ON obs_table_obs_new.obsId=user_table_user_obs.OBSCategory3IDRef

WHERE
	( `obs_table_obs_new`.`obsId` IN (" . $obsChildsModel . ") ) 
	AND ( `user_table_user_obs`.`isActive` = 1 ) 
	AND ( COALESCE ( user_table_user_obs.isDeleted, 0 ) <> 1 ) 
	AND ( `user_table_user`.`isActive` = 1 ) 
group by user_table_user.UserName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId

";

        $having = '';
        $having.= (isset($this->uName)) ? "having user_table_user.UserName like ('%" . $this->uName . "%') " : '';
        $having.= (isset($this->obsTitle)) ? (($having == '') ? 'having ' : ' and ') . " obs_table_obs_new.obsTitle like ('%" . $this->obsTitle . "%') " : '';
        $sql.=$having;
         $sql.="order by  total_Process DESC;";
        $totalDataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'pagination' => false,
        ]);
        return $totalDataProvider;
    }

    public function search($params) {
        $processTable = \app\modules\workflow\models\Process::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actinTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $userObsTable = \app\modules\account\models\UserObs::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        $uid = Yii::$app->session->get('UserID');
        $obsId = Yii::$app->session->get('ObsPosition');
        $companyId = Yii::$app->session->get('CompanyID');

        $obsChildsModel = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $this->load($params);
        $durationDate = "ActionDateHijri between '" . $this->fromDate . "' and '" . $this->toDate . "' and";
        $sql = "
SELECT user_table_user.UserID,user_table_user.UserName as uName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.CashRequestIDRef is not null) as 'Action_CashRequest'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.CashRequestIDRef is not null) as 'Process_CashRequest'
 

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.claimIdRef is not null) as 'Action_claim'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.claimIdRef is not null) as 'Process_claim'
 
 ,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.TenderIDRef is not null) as 'Action_tender'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.TenderIDRef is not null) as 'Process_Tender'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.InvoiceIDRef is not null) as 'Action_invoice'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.InvoiceIDRef is not null) as 'Process_Invoice'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.PersonID is not null) as 'Action_person'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.PersonID is not null) as 'Process_Person'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.WorkflowContractIDRef is not null) as 'Action_Contract'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.WorkflowContractIDRef is not null) as 'Process_Contract'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.BudgetIDRef is not null) as 'Action_Budget'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.BudgetIDRef is not null) as 'Process_Budget'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.capitalBalanceIdRef is not null) as 'Action_capital'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.capitalBalanceIdRef is not null) as 'Process_capital'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.debitmemoIdRef is not null) as 'Action_debitmemo'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.debitmemoIdRef is not null) as 'Process_debitmemo'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.warrantyIdRef is not null) as 'Action_warranty'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.warrantyIdRef is not null) as 'Process_warranty'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as 'Action_actualProgress'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as 'Process_actualProgress'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId) as 'total_action'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)) as 'total_Process'

FROM  user_table_user 
INNER JOIN user_table_user_obs on user_table_user_obs.UserIDRef=user_table_user.UserID
INNER JOIN obs_table_obs_new ON obs_table_obs_new.obsId=user_table_user_obs.OBSCategory3IDRef

WHERE
	( `obs_table_obs_new`.`obsId` IN (" . $obsChildsModel . ") ) 
	AND ( `user_table_user_obs`.`isActive` = 1 ) 
	AND ( COALESCE ( user_table_user_obs.isDeleted, 0 ) <> 1 ) 
	AND ( `user_table_user`.`isActive` = 1 ) 
group by user_table_user.UserName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId

";

        $having = '';
        $having.= (isset($this->uName)) ? "having user_table_user.UserName like ('%" . $this->uName . "%') " : '';
        $having.= (isset($this->obsTitle)) ? (($having == '') ? 'having ' : ' and ') . " obs_table_obs_new.obsTitle like ('%" . $this->obsTitle . "%') " : '';
        $sql.=$having;
         $sql.="order by  total_Process DESC;";
        $totalDataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'pagination' => false,
        ]);
        return $totalDataProvider;
    }
    
    public function totalActivity_old($params) {
        $processTable = \app\modules\workflow\models\Process::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actinTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $userObsTable = \app\modules\account\models\UserObs::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        $uid = Yii::$app->session->get('UserID');
        $obsId = Yii::$app->session->get('ObsPosition');
        $companyId = Yii::$app->session->get('CompanyID');

        $obsChildsModel = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $this->load($params);
        $durationDate = "ActionDateHijri between '" . $this->fromDate . "' and '" . $this->toDate . "' and";
        $sql = "SELECT obs_table_obs_new.obsTitle,obs_table_obs_new.obsId

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.CashRequestIDRef is not null) as `Total_Action_CashRequest`

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.CashRequestIDRef is not null) as `Total_Process_CashRequest`
 

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.claimIdRef is not null) as `Total_Action_claim` 

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.claimIdRef is not null) as `Total_Process_claim`
 
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.InvoiceIDRef is not null) as `Total_Action_invoice`

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.InvoiceIDRef is not null) as `Total_Process_Invoice`


,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.WorkflowContractIDRef is not null) as `Total_Action_Contract`

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.WorkflowContractIDRef is not null) as `Total_Process_Contract`

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.BudgetIDRef is not null) as `Total_Action_Budget`

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.BudgetIDRef is not null) as `Total_Process_Budget`
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.capitalBalanceIdRef is not null) as `Total_Action_capital`

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.capitalBalanceIdRef is not null) as `Total_Process_capital`

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.debitmemoIdRef is not null) as `Total_Action_debitmemo`
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.debitmemoIdRef is not null) as `Total_Process_debitmemo`

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.warrantyIdRef is not null) as `Total_Action_warranty`

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.warrantyIdRef is not null) as `Total_Process_warranty`
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as `Total_Action_actualProgress`
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as `Total_Process_actualProgress`,
 
 (select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId) as total_action
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where  $durationDate workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)) as total_Process
 
FROM  user_table_user 
INNER JOIN user_table_user_obs on user_table_user_obs.UserIDRef=user_table_user.UserID
INNER JOIN obs_table_obs_new ON obs_table_obs_new.obsId=user_table_user_obs.OBSCategory3IDRef

WHERE
	( `obs_table_obs_new`.`obsId` IN (" . $obsChildsModel . " ) ) 
	AND ( `user_table_user_obs`.`isActive` = 1 ) 
	AND ( COALESCE ( user_table_user_obs.isDeleted, 0 ) <> 1 ) 
	AND ( `user_table_user`.`isActive` = 1 ) 
 group by obs_table_obs_new.obsTitle,obs_table_obs_new.obsId



";

        $having = '';
        $having.= (isset($this->uName)) ? "having user_table_user.UserName like ('%" . $this->uName . "%') " : '';
        $having.= (isset($this->obsTitle)) ? (($having == '') ? 'having ' : ' and ') . " obs_table_obs_new.obsTitle like ('%" . $this->obsTitle . "%') " : '';
        $sql.=$having;
        $sql.="order by  total_Process DESC;";
        $totalDataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'pagination' => false,
        ]);
        return $totalDataProvider;
    }

    public function totalActivity($params,$uid= null,$obsId = null) {
        $processTable = \app\modules\workflow\models\Process::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actinTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $userObsTable = \app\modules\account\models\UserObs::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        if($uid == null)
            $uid = Yii::$app->session->get('UserID');
        if($obsId == null)
            $obsId = Yii::$app->session->get('ObsPosition');
        $companyId = Yii::$app->session->get('CompanyID');

        $obsChildsModel = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $this->load($params);
        $durationDate = "ActionDateHijri between '" . $this->fromDate . "' and '" . $this->toDate . "' and";
        
        $whereInUser="SELECT user_table_user.UserID
                    FROM user_table_user 
                    INNER JOIN user_table_user_obs on user_table_user_obs.UserIDRef=user_table_user.UserID
                    INNER JOIN obs_table_obs_new ON obs_table_obs_new.obsId=user_table_user_obs.OBSCategory3IDRef

                    WHERE
                    ( `obs_table_obs_new`.`obsId` IN (" . $obsChildsModel . ") ) 
                    AND ( `user_table_user_obs`.`isActive` = 1 ) 
                    AND ( COALESCE ( user_table_user_obs.isDeleted, 0 ) <> 1 ) 
                    AND ( `user_table_user`.`isActive` = 1 ) 
                    group by user_table_user.UserName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId";
        $whereInOBS="SELECT user_table_user_obs.OBSCategory3IDRef
                    FROM user_table_user 
                    INNER JOIN user_table_user_obs on user_table_user_obs.UserIDRef=user_table_user.UserID
                    INNER JOIN obs_table_obs_new ON obs_table_obs_new.obsId=user_table_user_obs.OBSCategory3IDRef

                    WHERE
                    ( `obs_table_obs_new`.`obsId` IN (" . $obsChildsModel . ") ) 
                    AND ( `user_table_user_obs`.`isActive` = 1 ) 
                    AND ( COALESCE ( user_table_user_obs.isDeleted, 0 ) <> 1 ) 
                    AND ( `user_table_user`.`isActive` = 1 ) 
                    group by user_table_user.UserName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId";
        
        $sql="SELECT 
            (select count(*) from workflow_table_process_entity where 
                    CashRequestIDRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_CashRequest'

           ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where CashRequestIDRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_CashRequest'
                                        
            ,(select count(*) from workflow_table_process_entity where 
                    claimIdRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_claim'
            
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where claimIdRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_claim'
                    
            ,(select count(*) from workflow_table_process_entity where 
                    InvoiceIDRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_Invoice'
                    
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where InvoiceIDRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_invoice'
                    
            ,(select count(*) from workflow_table_process_entity where 
                    WorkflowContractIDRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_Contract'
              
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where WorkflowContractIDRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_Contract'
            
            
            ,(select count(*) from workflow_table_process_entity where 
                    TenderIDRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_Tender'
              
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where TenderIDRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_Tender'        
             
            ,(select count(*) from workflow_table_process_entity where 
                    PersonID IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_Person'
              
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where PersonID IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_Person'  
             
                    
            ,(select count(*) from workflow_table_process_entity where 
                    BudgetIDRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_Budget'
                    
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where BudgetIDRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_Budget'
                    
            ,(select count(*) from workflow_table_process_entity where 
                    capitalBalanceIdRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_capital'
             
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where capitalBalanceIdRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_capital'
                    
            ,(select count(*) from workflow_table_process_entity where 
                    debitmemoIdRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_debitmemo'
                    
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where debitmemoIdRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_debitmemo'
                    
                    
            ,(select count(*) from workflow_table_process_entity where 
                    warrantyIdRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_warranty'
             
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where warrantyIdRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_warranty'
                    
            ,(select count(*) from workflow_table_process_entity where 
                    actualProgressAnnualBudgetIdRef IS NOT NULL
                    AND	ProcessEntityID in(select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
                        Where $durationDate
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )
                    ) AS 'Total_Process_actualProgress'
                    
            ,(select count(*) from workflow_table_action_entity
                inner join workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
                    where actualProgressAnnualBudgetIdRef IS NOT NULL
                    AND $durationDate 
                        workflow_table_action_entity.UserIDRef IN (". $whereInUser .")
                        and workflow_table_action_entity.OBSCategory3IDRef in (". $whereInOBS .")
                    )AS 'Total_Action_actualProgress'
                    
";
        
//        echo $sql;
//        die();
        $totalDataProvider = new \yii\data\SqlDataProvider([
            'sql' => $sql,
            'pagination' => false,
        ]);
        return $totalDataProvider;
    }
    public function userActivity($params,$uid= null,$obsId = null) {
        $processTable = \app\modules\workflow\models\Process::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actinTable = \app\modules\workflow\models\Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $userObsTable = \app\modules\account\models\UserObs::tableName();
        $userTable = \app\modules\account\models\User::tableName();

        

        if($uid == null)
            $uid = Yii::$app->session->get('UserID');
        if($obsId == null)
            $obsId = Yii::$app->session->get('ObsPosition');
        $companyId = Yii::$app->session->get('CompanyID');

        $obsChildsModel = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $this->load($params);
        $durationDate = "ActionDateHijri between '" . $this->fromDate . "' and '" . $this->toDate . "' and";
        $sql = "
SELECT user_table_user.UserName as uName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.CashRequestIDRef is not null) as 'Action_CashRequest'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.CashRequestIDRef is not null) as 'Process_CashRequest'
 

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.claimIdRef is not null) as 'Action_claim'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.claimIdRef is not null) as 'Process_claim'
 
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.InvoiceIDRef is not null) as 'Action_invoice'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.InvoiceIDRef is not null) as 'Process_Invoice'


,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.WorkflowContractIDRef is not null) as 'Action_Contract'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.WorkflowContractIDRef is not null) as 'Process_Contract'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.BudgetIDRef is not null) as 'Action_Budget'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.BudgetIDRef is not null) as 'Process_Budget'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.capitalBalanceIdRef is not null) as 'Action_capital'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.capitalBalanceIdRef is not null) as 'Process_capital'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.debitmemoIdRef is not null) as 'Action_debitmemo'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.debitmemoIdRef is not null) as 'Process_debitmemo'

,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.warrantyIdRef is not null) as 'Action_warranty'

,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.warrantyIdRef is not null) as 'Process_warranty'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as 'Action_actualProgress'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate  workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)
 and workflow_table_process_entity.actualProgressAnnualBudgetIdRef is not null) as 'Process_actualProgress'
 
,(select count(*) from workflow_table_action_entity
inner join  workflow_table_process_entity on workflow_table_process_entity.ProcessEntityID=workflow_table_action_entity.ProcessEntityIDRef
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId) as 'total_action'
 
,(select count(*) from workflow_table_process_entity where 
ProcessEntityID in  (select workflow_table_action_entity.ProcessEntityIDRef from workflow_table_action_entity
Where $durationDate workflow_table_action_entity.UserIDRef=user_table_user.UserID and workflow_table_action_entity.OBSCategory3IDRef=obs_table_obs_new.obsId)) as 'total_Process'

FROM  user_table_user 
INNER JOIN user_table_user_obs on user_table_user_obs.UserIDRef=user_table_user.UserID
INNER JOIN obs_table_obs_new ON obs_table_obs_new.obsId=user_table_user_obs.OBSCategory3IDRef

WHERE
	( `obs_table_obs_new`.`obsId` IN (" . $obsChildsModel . ") ) 
	AND ( `user_table_user_obs`.`isActive` = 1 ) 
	AND ( COALESCE ( user_table_user_obs.isDeleted, 0 ) <> 1 ) 
	AND ( `user_table_user`.`isActive` = 1 ) 
group by user_table_user.UserName,obs_table_obs_new.obsTitle,user_table_user.UserID,obs_table_obs_new.obsId ";

        $having = '';
        $having.= "having user_table_user.UserID = $uid ";
        $having.= "and obs_table_obs_new.obsId = $obsId ";
        $sql.=$having;

        $arrayDate = Yii::$app->db->createCommand($sql)->queryAll();
        $arrayDate = $arrayDate[0];
        
        $strJsonData = "[";
        $strJsonData .="   ['Budget',".$arrayDate['Process_Budget']."],";
        $strJsonData .="   ['CashRequest',".$arrayDate['Process_CashRequest']."],";
        $strJsonData .="   ['Contract',".$arrayDate['Process_Contract']."],";
        $strJsonData .="   ['Invoice',".$arrayDate['Process_Invoice']."],";
        $strJsonData .="   ['actualProgress',".$arrayDate['Process_actualProgress']."],";
        $strJsonData .="   ['capital',".$arrayDate['Process_capital']."],";
        $strJsonData .="   ['claim',".$arrayDate['Process_claim']."],";
        $strJsonData .="   ['debitmemo',".$arrayDate['Process_debitmemo']."],";
        $strJsonData .="   ['warranty',".$arrayDate['Process_warranty']."]";
        $strJsonData .="]";

        return $strJsonData;
    }

}
