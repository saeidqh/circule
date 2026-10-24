<?php

namespace app\modules\workflow\models;

use app\modules\OBS\models\PersonCountry;
use Yii;
use app\modules\budget\models\Budget;
use app\modules\contract\models\ContractEps;
use app\components\CandoHelper;
use app\components\ChromePhp;
use app\modules\document\models\StampDocument;
use app\modules\OBS\models\ObsProcessAction;
use app\modules\OBS\models\Obsnew;

/**
 * This is the ActiveQuery class for [[ProcessEntity]].
 *
 * @see ProcessEntity
 * @author <mail:mortezakml@gmail.com & Tel:09366263397>
 */
class ProcessEntityQuery extends \yii\db\ActiveQuery {

    public function all($db = null) {
        return parent::all($db);
    }

    public function one($db = null) {
        return parent::one($db);
    }

    public function budget($type, $user_id = -1, $obs_id = -1) {

        $budgetTable = Budget::tableName();
        $processEntityTable = ProcessEntity::tableName();

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $workflowActionTable = Action::tableName();

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');
        if ($epsList == '') {
            $epsList = '0';
        }

        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'BudgetWorkflowReport', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        $entityType = 4; // Annual Budgeting => 4
        $eps_entity = \app\components\CandoHelper::UserEpsEntity($uid, $obsId, $entityType, 'view', 'string');
//        $this->andWhere(Ncontract::tableName() . ".EPSIDRef in ($eps_entity)");
//        $this->andWhere("$budgetTable.EPSIDRefL in($eps_entity)");




        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $this->innerJoin(Budget::tableName(), Budget::tableName() . '.BudgetID=' . ProcessEntity::tableName() . '.BudgetIDRef')
                ->innerJoin($obsTable, "$obsTable.obsId=" . Budget::tableName() . ".OBSCategory3IDRef");

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        //        اگر ادمین کل نبود بتواند فقط برای شرکت خودش را ببیند
        if ($isCandoAdmin) {
            return $this;
        } else {
            $this->andWhere(["$obsTable.companyIdRef" => $companyId]);
        }
//        ادمین شرکت ها در
//        All
//     تمامی بودجه ها را ببینند
        if (($type == 'All' || $type == 'Graph') && $isAdmin) {
            return $this;
        }

        $this->andWhere(Budget::tableName() . ".EPSIDRefL in($eps_entity)");

        /* $this->andWhere(['or',
          "$processEntityTable.LastActionIDRef in (474,864,475,476) and $budgetTable.EPSIDRefL in($eps_entity)",
          "$processEntityTable.LastActionIDRef not in (474,864,475,476)",
          ]); */
        // ادمین کل و مجمع بتوانند برنامه بودجه های تمامی شرکت ها رو ببنینند در 
        // Mode All

        if (in_array($type, ['All']) && ( ($obsId == 606) || ($obsId == 830))) {
            return $this;
        }
        //قدم دوم حذف ای پی اس های غیر مرتبط
        if (($type == 'All' || ($type == 'BudgetWorkflowReport')) && (($uid == 1 || $uid == 2 || $uid == 25) && ($obsId == 606))) {
            return $this;
        } else {
            $this->andWhere(Budget::tableName() . ".EPSIDRefL in($epsList)");
        }

        if ($type == 'All') {
            $this->andWhere(['or',
                ProcessEntity::tableName() . ".ProcessEntityID in (select ProcessEntityIDRef from " . ActionEntity::tableName() . " where OBSCategory3IDRef in ($obsAllChildsList))",
                ProcessEntity::tableName() . ".LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                ["or",
                    "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('budget') . ")",
                    "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('budget') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                    . "and (
                            $actionEntityTable.FromActionIDRef is null 
                            or
                            $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                            select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                            ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                            ))>0",
                ]
            ]);

//            $this->andWhere(["or",
//                "$processEntityTable.LastActionIDRef not in (". $this->showObsChildsActionList('budget').")",
//                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (". $this->showObsChildsActionList('budget') .") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
//                    . "and (
//                        $actionEntityTable.FromActionIDRef is null 
//                        or
//                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
//                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
//                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
//                        ))>0",
//            ]);
        } else if ($type == 'ToDo') {
            $this->andWhere(ProcessEntity::tableName() . ".status=1");
            $this->andWhere(ProcessEntity::tableName() . ".LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))");

            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('budget') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('budget') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        } else if ($type == 'Finished') {
            $this->andWhere([ProcessEntity::tableName() . '.status' => 0]);
            $this->andWhere(ProcessEntity::tableName() . ".ProcessEntityID in (select ProcessEntityIDRef from " . ActionEntity::tableName() . " where OBSCategory3IDRef in ($obsAllChildsList))");
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                ProcessEntity::tableName() . ".status=1 and " . ProcessEntity::tableName() . ".ProcessEntityID in (select ProcessEntityIDRef from " . ActionEntity::tableName() . " where OBSCategory3IDRef in ($obsAllChildsList))",
                ProcessEntity::tableName() . ".LastActionIDRef in (select " . \app\modules\OBS\models\ObsProcessAction::tableName() . ".ActionIDRef from " . \app\modules\OBS\models\ObsProcessAction::tableName() . " where " . \app\modules\OBS\models\ObsProcessAction::tableName() . ".OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        } else if ($type == 'BudgetWorkflowReport') {
            
        } else if ($type == 'Graph') {
            
        }

        return $this;
    }

    public function warranty($type, $user_id = -1, $obs_id = -1) {
        $this->innerJoin(\app\modules\warranty\models\Warranty::tableName(), \app\modules\warranty\models\Warranty::tableName() . '.warrantyId=' . ProcessEntity::tableName() . '.warrantyIdRef');

        if ($type == 'All') {
            
        } else if ($type == 'ToDo') {
            
        } else if ($type == 'Finished') {
            
        } else if ($type == 'NotFinished') {
            
        } else if ($type == 'Graph') {
            
        } else {
            
        }
        return $this;
    }

    public function capital($type, $user_id = -1, $obs_id = -1) {
//        $uid = Yii::$app->session->get('UserID');
//        $obsId = Yii::$app->session->get('ObsPosition');
//        $companyId = Yii::$app->session->get('CompanyID');

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $capitalBalanceTable = \app\modules\capital\models\CapitalBalance::tableName();
        $tblObs = \app\modules\OBS\models\Obsnew::tableName();

        $capitalBalanceAmountTable = \app\modules\capital\models\CapitalBalanceAmount::tableName();
        $capitalBudget = \app\modules\budget\models\Budget::tableName();

        $workflowActionTable = Action::tableName();

        $this->innerJoin($capitalBalanceTable, "$capitalBalanceTable.capitalBalanceId=$processEntityTable.capitalBalanceIdRef");

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        $this->innerJoin($tblObs, "$tblObs.obsId=$capitalBalanceTable.OBSCategory3IDRef");

        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');
        if ($epsList == '') {
            $epsList = '0';
        }

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

//        $isAdmin = \app\access\AccessControl::isAccess(['obs', 'check' => 'isAdmin', 'errorException' => false]);
//        $isCandoAdmin = \app\access\AccessControl::isAccess(['obs', 'check' => 'isCandoAdmin', 'errorException' => false]);
        //قدم دوم حذف ای پی اس های غیر مرتبط
        if (($type == 'All') && $isCandoAdmin) {
            return $this;
        } else {
//            $this->andWhere(\app\modules\cashRequest\models\CashRequest::tableName() . '.CashRequestID in (select CashRequestIDRef from ' . \app\modules\cashRequest\models\CashRequestAmount::tableName() . ' where EPSIDRef in(' . $epsList . '))');
            $this->andWhere("(select count(DISTINCT($capitalBudget.EPSIDRefL)) from $capitalBalanceAmountTable "
                    . "inner join $capitalBudget on $capitalBudget.BudgetID=$capitalBalanceAmountTable.budgetIdRef "
                    . "where( $capitalBalanceAmountTable.isDeleted is null or $capitalBalanceAmountTable.isDeleted <> 1) "
                    . "and ($capitalBalanceAmountTable.capitalBalanceIdRef=$capitalBalanceTable.capitalBalanceId) and $capitalBudget.EPSIDRefL in ($epsList))>0");
        }

        if (!$isCandoAdmin) {
            $this->andWhere(["$tblObs.companyIdRef" => $companyId]);
        }

        if ($type == 'All') {
            if (!$isAdmin) {
                $this->andWhere(['or',
                    "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                    ['and',
                        "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                        "($processEntityTable.LastActionIDRef<>537) or ($processEntityTable.LastActionIDRef=537 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))",
                        "($processEntityTable.LastActionIDRef<>538) or ($processEntityTable.LastActionIDRef=538 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))",
                        "($processEntityTable.LastActionIDRef<>855) or ($processEntityTable.LastActionIDRef=855 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))",
                ]]);

                $this->andWhere(["or",
                    "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('capital') . ")",
                    "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('capital') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                    . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
                ]);

                $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
            }
        } else if ($type == 'ToDo') {
            $this->andWhere("$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1) and $processEntityTable.status=1)");
            $this->andWhere("($processEntityTable.LastActionIDRef<>537) or ($processEntityTable.LastActionIDRef=537 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))");
            $this->andWhere("($processEntityTable.LastActionIDRef<>538) or ($processEntityTable.LastActionIDRef=538 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))");
            $this->andWhere("($processEntityTable.LastActionIDRef<>855) or ($processEntityTable.LastActionIDRef=855 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))");

            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('capital') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('capital') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        } else if ($type == 'Finished') {
            $this->andWhere([ProcessEntity::tableName() . '.status' => 0]);
            $this->andWhere("$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))");
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                "$processEntityTable.status=1 and $processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                ['and',
                    "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                    "($processEntityTable.LastActionIDRef<>537) or ($processEntityTable.LastActionIDRef=537 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($processEntityTable.LastActionIDRef<>538) or ($processEntityTable.LastActionIDRef=538 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($processEntityTable.LastActionIDRef<>855) or ($processEntityTable.LastActionIDRef=855 and ($capitalBalanceTable.OBSCategory3IDRef in ($obsAllChildsList)))",
            ]]);
        } else if ($type == 'Graph') {
            
        } else {
            
        }

        return $this;
    }

    public function contractOld($type, $user_id = -1, $obs_id = -1) {
        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $workflowContractTable = \app\modules\workflowContract\models\WorkflowContract::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $companyTable = \app\modules\OBS\models\Company::tableName();

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');

        $epsArray = \app\components\CandoHelper::UserEps($uid, $obsId, 'array');
        $processActionArray = \app\modules\OBS\models\ObsProcessAction::find()->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $this->innerJoin($workflowContractTable, $workflowContractTable . '.WorkflowContractID=' . ProcessEntity::tableName() . '.WorkflowContractIDRef');

//        $isAdmin = \app\access\AccessControl::isAccess(['obs', 'check' => 'isAdmin', 'errorException' => false]);
//        $isCandoAdmin = \app\access\AccessControl::isAccess(['obs', 'check' => 'isCandoAdmin', 'errorException' => false]);        

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($isCandoAdmin) {
            if ($type == 'All') {
                return $this;
            }
        } else if ($isAdmin && $type == 'All') {
            $this->andWhere(['or',
                "$workflowContractTable.SignerPersonIDRef=$companyId",
                ["and",
                    "$workflowContractTable.SubContractorPersonIDRef=$companyId",
                    ["not in", "$workflowContractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                ],
            ]);
            return $this;
        } else {
            $this->andWhere(['or',
                "$processEntityTable.LastActionIDRef in (797,491,867)",
                "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                ["and",
                    "$workflowContractTable.SubContractorPersonIDRef=$companyId",
                    ["not in", "$workflowContractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                ],
            ]);
        }

        /* if ($type == 'All' && $isAdmin) {
          return $this;
          } */

        $this->andWhere(['or',
            "$processEntityTable.LastActionIDRef in (491,797,867)", //796 => 491
            ['or',
                [$workflowContractTable . '.EPS1IDRef' => $epsArray],
                [$workflowContractTable . '.EPS2IDRef' => $epsArray],
                [$workflowContractTable . '.EPS3IDRef' => $epsArray],
                ['and',
                    ["$workflowContractTable.EPS1IDRef" => null],
                    ["$workflowContractTable.EPS2IDRef" => null],
                    ["$workflowContractTable.EPS3IDRef" => null]
                ]
            ]
        ]);

        $siteObsArray = \app\modules\OBS\models\Obsnew::getObsSiteArray();

        if ($type == 'All') {
//          old Code
            /* if (in_array($obsId, $siteObsArray)) {
              $this->andWhere(["and",
              ['or',
              "$processEntityTable.LastActionIDRef in (797,491,867)",
              "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
              ],
              ['or',
              "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
              "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
              ]
              ]);

              } else {
              $this->andWhere(['or',
              "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
              "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
              ]);
              } */
//            New Code
            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(["and",
                    ['or',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                    ],
                ]);
                $this->andWhere(["in", 'LastActionIDRef', $processActionArray])->andWhere(['status' => 1]);
            } else {
                $this->andWhere(["in", 'LastActionIDRef', $processActionArray])->andWhere(['status' => 1]);
            }
            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(['or',
                    "$processEntityTable.LastActionIDRef in (797,491,488)", // For Process 23
                    "$processEntityTable.LastActionIDRef in (528,867)", // For Process 24
                    "$processEntityTable.LastActionIDRef not in (797,491,488,528,867) and ($workflowContractTable.ContractCategoryIDRef=12)",
                ]);
            } else {
                $this->andWhere(['or',
                    "$processEntityTable.LastActionIDRef in (797,491,488)", // For Process 23
                    "$processEntityTable.LastActionIDRef in (528,867)", // For Process 24
                    "$processEntityTable.LastActionIDRef not in (797,491,488,528,867) and ($workflowContractTable.ContractCategoryIDRef<>12)",
                ]);
            }

            $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        } else if ($type == 'ToDo') {
            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(["and",
                    ['or',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                    ],
                ]);
                $this->andWhere(["in", 'LastActionIDRef', $processActionArray])->andWhere(['status' => 1]);
            } else {
                $this->andWhere(["in", 'LastActionIDRef', $processActionArray])->andWhere(['status' => 1]);
            }
            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(['or',
                    "$processEntityTable.LastActionIDRef in (797,491,488)", // For Process 23
                    "$processEntityTable.LastActionIDRef in (528,867)", // For Process 24
                    "$processEntityTable.LastActionIDRef not in (797,491,488,528,867) and ($workflowContractTable.ContractCategoryIDRef=12)",
                ]);
            } else {
                $this->andWhere(['or',
                    "$processEntityTable.LastActionIDRef in (797,491,488)", // For Process 23
                    "$processEntityTable.LastActionIDRef in (528,867)", // For Process 24
                    "$processEntityTable.LastActionIDRef not in (797,491,488,528,867) and ($workflowContractTable.ContractCategoryIDRef<>12)",
                ]);
            }
        } else if ($type == 'Finished') {

            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(["and",
                    ['or',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                    ],
                    ['or',
                        "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                        "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                    ]
                ]);
                $this->andWhere(['status' => 0]);
            } else {

                $this->andWhere(['or',
                    "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                    "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                ]);
                $this->andWhere(['status' => 0]);
            }
        } else if ($type == 'NotFinished') {

            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(["and",
                    ['or',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                    ],
                    ['or',
                        "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                        "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                    ]
                ]);
                $this->andWhere(['status' => 1]);
            } else {
                $this->andWhere(['or',
                    "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                    "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                ]);
                $this->andWhere(['status' => 1]);
            }
        } else if ($type == 'Graph') {
            
        } else {
            
        }
        return $this;
    }

    public function contract($type, $user_id = -1, $obs_id = -1) {
        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $workflowContractTable = \app\modules\workflowContract\models\WorkflowContract::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');

        $epsArray = \app\components\CandoHelper::UserEps($uid, $obsId, 'array');
        $processActionArray = \app\modules\OBS\models\ObsProcessAction::find()->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $this->innerJoin($workflowContractTable, $workflowContractTable . '.WorkflowContractID=' . ProcessEntity::tableName() . '.WorkflowContractIDRef');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($isCandoAdmin) {
            if ($type == 'All') {
                return $this;
            }
        } else if ($isAdmin && $type == 'All') {
            $this->andWhere(['or',
                "$workflowContractTable.SignerPersonIDRef=$companyId",
                ["and",
                    "$workflowContractTable.SubContractorPersonIDRef=$companyId",
                    ["not in", "$workflowContractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                ],
            ]);
            return $this;
        } else {
            $this->andWhere(['or',
                // کد قدیمی // حقوقی قراردادهایی که کارفرمای بیرونی دارند رو نمیبینه 
                /* ['and',
                  "$processEntityTable.LastActionIDRef in (491,797,867)",
                  ['in', "$workflowContractTable.SignerPersonIDRef", $companyChildsArray]
                  ], */
                ['and',
                    "$processEntityTable.LastActionIDRef in (797,491,867)",
                    ['or',
                        ['in', "$workflowContractTable.SignerPersonIDRef", $companyChildsArray],
                        ['and',
                            ["not in", "$workflowContractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                            ['in', "$workflowContractTable.SubContractorPersonIDRef", $companyChildsArray],
                        ]
                    ]
                ],
                ['and',
                    "$processEntityTable.LastActionIDRef not in (797,491,867)",
                    "$workflowContractTable.SignerPersonIDRef=$companyId"
                ],
                ["and",
                    "$workflowContractTable.SubContractorPersonIDRef=$companyId",
                    ["not in", "$workflowContractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                ]
            ]);
        }

        $this->andWhere(['or',
            // کد قدیمی // حقوقی قراردادهایی که کارفرمای بیرونی دارند رو نمیبینه
            /* ['and',
              "$processEntityTable.LastActionIDRef in (491,797,867)", //796 => 491
              ['in', "$workflowContractTable.SignerPersonIDRef", $companyChildsArray]
              ], */
            ['and',
                "$processEntityTable.LastActionIDRef in (797,491,867)",
                ['or',
                    ['in', "$workflowContractTable.SignerPersonIDRef", $companyChildsArray],
                    ['and',
                        ["not in", "$workflowContractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                        ['in', "$workflowContractTable.SubContractorPersonIDRef", $companyChildsArray],
                    ]
                ]
            ],
            ['or',
                [$workflowContractTable . '.EPS1IDRef' => $epsArray],
                [$workflowContractTable . '.EPS2IDRef' => $epsArray],
                [$workflowContractTable . '.EPS3IDRef' => $epsArray],
                ['and',
                    ["$workflowContractTable.EPS1IDRef" => null],
                    ["$workflowContractTable.EPS2IDRef" => null],
                    ["$workflowContractTable.EPS3IDRef" => null]
                ]
            ]
        ]);

        $siteObsArray = \app\modules\OBS\models\Obsnew::getObsSiteArray();

        if ($type == 'All' || $type == 'ToDo') {
            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(['or',
                    ['and',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        ['in', "$workflowContractTable.SignerPersonIDRef", $companyChildsArray]
                    ],
                    ['and',
                        "$processEntityTable.LastActionIDRef not in (797,491,867)",
                        "$workflowContractTable.SignerPersonIDRef=$companyId"
                    ],
                ]);
                $contractCategoryCondition = "$workflowContractTable.ContractCategoryIDRef=12";
            } else {
                $contractCategoryCondition = "($workflowContractTable.ContractCategoryIDRef<>12 or $processEntityTable.LastActionIDRef in (976,977,1049))"; // مدیر حقوقی ستاد قراردادهای گارگاهی را باید تایید کند
            }
            $this->andWhere(['or',
                "$processEntityTable.LastActionIDRef in (797,491,488)", // For Process 23
                "$processEntityTable.LastActionIDRef in (528,867)", // For Process 24
                "$processEntityTable.LastActionIDRef not in (797,491,488,528,867) and ($contractCategoryCondition)",
            ]);
            if ($type == 'All') {
                $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
            } else {
                $this->andWhere(["in", 'LastActionIDRef', $processActionArray])->andWhere(['status' => 1]);
            }
        } else if ($type == 'Finished') {
            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(["and",
                    ['or',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                    ],
                    ['or',
                        "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                        "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                    ]
                ]);
                $this->andWhere(['status' => 0]);
            } else {

                $this->andWhere(['or',
                    "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                    "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                ]);
                $this->andWhere(['status' => 0]);
            }
        } else if ($type == 'NotFinished') {

            if (in_array($obsId, $siteObsArray)) {
                $this->andWhere(["and",
                    ['or',
                        "$processEntityTable.LastActionIDRef in (797,491,867)",
                        "$processEntityTable.LastActionIDRef not in (797,491,867) and $workflowContractTable.SignerPersonIDRef=$companyId",
                    ],
                    ['or',
                        "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                        "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                    ]
                ]);
                $this->andWhere(['status' => 1]);
            } else {
                $this->andWhere(['or',
                    "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                    "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                ]);
                $this->andWhere(['status' => 1]);
            }
        } else if ($type == 'Graph') {
            $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        } else {
            
        }
        return $this;
    }

    public function invoice($type, $user_id = -1, $obs_id = -1) {

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
//        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');
//        $userObsEntityTable = \app\modules\account\models\UserObsEntity::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $actionTable = \app\modules\workflow\models\Action::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();

        $workflowActionTable = Action::tableName();

        $contractQuery = \app\modules\contract\models\Contract::find()
                ->contractAccess('InvoiceList', $uid, $obsId);
        if (\app\access\AccessControl::isAccess(['formAction', 'aid' => 151, 'errorException' => false])) {
            if (\app\modules\account\models\UserOBSFormAction::find()
                            ->andWhere("UserIDRef=$uid and OBSCategory3IDRef=$obsId and FormActionIDRef=151 and COALESCE(isDeleted, 0)<>1")
                            ->exists()) {
                $mainContractQuery = \app\modules\contract\models\Contract::find()
                        ->maincontract('view', $companyId);
                $contractQuery->union($mainContractQuery, true);
            }
        }

        $this->innerJoin($invoiceTable, ProcessEntity::tableName() . ".InvoiceIDRef=$invoiceTable.InvoiceID");
        $this->innerJoin(["contractQuery" => $contractQuery], "contractQuery.ContractID=$invoiceTable.ContractID");

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($type == 'All' && $isCandoAdmin) {
            return $this;
        }

        /* $this->andWhere(["or",
          ["and",
          ["in", ProcessEntity::tableName() . ".ProcessIDRef", [5, 6, 63, 64]],
          ["contractQuery.SignerPersonIDRef" => $companyId],
          ],
          ["and",
          ["<>", ProcessEntity::tableName() . ".ProcessIDRef", 5],
          ["<>", ProcessEntity::tableName() . ".ProcessIDRef", 6],
          ["<>", ProcessEntity::tableName() . ".ProcessIDRef", 63],
          ["<>", ProcessEntity::tableName() . ".ProcessIDRef", 64],
          ],

          //                ["and",
          //                    "$processEntityTable.LastActionIDRef not in (". $this->showObsChildsActionList('invoice').")",
          //                    "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (". $this->showObsChildsActionList('invoice') .") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList))>0",
          //                ],
          //                ["and",
          //                    "$processEntityTable.LastActionIDRef<>125",
          //                    "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef=125 and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList))>0",
          //                ],
          ]); */

        if ($type == 'All' || $type == 'ToDo') {
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('invoice') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('invoice') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        }

        if ($type == 'All') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        } else if ($type == 'ToDo') {
            $this->joinWith(['lastAction']);
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef<>125",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef=125 and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList))>0",
            ]);

            $this->andWhere([
                'or',
                "(contractQuery.SubContractorPersonIDRef=$companyId) and ($actionTable.CharacterTypeIDRef=1)",
                "(contractQuery.SignerPersonIDRef=$companyId) and ($actionTable.CharacterTypeIDRef=2)",
            ]);

            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            return $this->andWhere(['LastActionIDRef' => $processActionArray])->andWhere(['status' => 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 1]);
        } else if ($type == 'Graph') {
            
        }

        return $this;
    }

    public function tender($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $workflowActionTable = Action::tableName();
        $tenderTable = \app\modules\tender\models\Tender::tableName();

        $this->innerJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef");
        $this->innerJoin($tenderTable, "$processEntityTable.TenderIDRef=$tenderTable.TenderID");

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($type == 'All' && $isCandoAdmin) {
            return $this;
        }

        if (!$isCandoAdmin) {
            $this->andWhere(["$tenderTable.companyIDRef" => $companyId]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'CashRequestWorkflowReport', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($type == 'All') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('tender') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('tender') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        } else if ($type == 'ToDo') {
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('tender') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('tender') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);

            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(['LastActionIDRef' => $processActionArray])->andWhere(['status' => 1]);
            $this->andWhere(['<>', 'isEndAction', 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 1]);
        } else if ($type == 'Graph') {
            
        }
        return $this;
    }

    public function automation($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $automationTable = \app\modules\automation\models\Automation::tableName();
        $workflowActionTable = Action::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $projectTable = \app\modules\wbs\models\Epsnew::tableName();

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        $this->innerJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
                ->innerJoin($automationTable, "$processEntityTable.AutomationIDRef=$automationTable.AutomationID")
                ->leftJoin($humanResourceTable, "$humanResourceTable.Id=$automationTable.HumanResourceIdRef")
                ->leftJoin($obsTable, "$obsTable.obsId=$automationTable.Position")
                ->leftJoin($projectTable, "$projectTable.EPSID=$obsTable.ProjectIDRef")
                ->leftJoin($positionTypeTable, "$positionTypeTable.PositionTypeID=$obsTable.positionTypeIdRef");

        if ($isCandoAdmin) {
            return $this;

        }

        if ($type == 'All' || $type == 'NotFinished' || $type == 'Graph') {
            // درحالی که درخواست مورد نظر جهت ایجاد به دبیرخانه و ای تی نرسیده و کاربران از آن ها پیگیری میکنند دسترسی مشاهده داده شد
            if (\app\components\Security::formActionAccess(417)) {
                return $this->andWhere(['in', "$automationTable.CompanyIDRef", $companyChildsArray]);
            }
        }

        if (!$isCandoAdmin) {
            $this->andWhere(['or',
                ['and', // هر شرکت فقط درخواست های شرکت خودش را می بیند
                    "$processEntityTable.LastActionIDRef in (964,965,966,978,979,980,985,986,987,988,1185,1186)",
                    ['=', "$automationTable.CompanyIDRef", $companyId],
                ],
                ['and', // معاونت ها همه شرکت های زیر مجموعه را می بینند
                    "$processEntityTable.LastActionIDRef not in (964,965,966,978,979,980,985,986,987,988,1185,1186)",
                    ['in', "$automationTable.CompanyIDRef", $companyChildsArray],
                ]
            ]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($type == 'All' || $type == 'Finished' || $type == 'NotFinished' || $type == 'Graph') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
                "$obsId"
            ]);
        }
        if ($type == 'ToDo') {
            if ($companyId == 248) {
                $this->andWhere(["or",
                    "$processEntityTable.LastActionIDRef not in (964,966,985,988,1185,1186)",
                    "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (964,966,985,988,1185,1186) and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                    . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
                ]);
            }
            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(['LastActionIDRef' => $processActionArray])
                    ->andWhere(["$processEntityTable.status" => 1])
                    ->andWhere(['<>', 'isEndAction', 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(["$processEntityTable.status" => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(["$processEntityTable.status" => 1]);
        } else if ($type == 'Graph') {
            $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        }

        return $this;
    }

    public function network($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $networkTable = \app\modules\automation\models\Network::tableName();
        $workflowActionTable = Action::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        $this->innerJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
                ->innerJoin($networkTable, "$processEntityTable.NetworkIDRef=$networkTable.NetworkID")
                ->leftJoin($humanResourceTable, "$humanResourceTable.Id=$networkTable.HumanResourceIdRef")
                ->leftJoin($obsTable, "$obsTable.obsId=$networkTable.Position")
                ->leftJoin($positionTypeTable, "$positionTypeTable.PositionTypeID=$obsTable.positionTypeIdRef");

        if ($isCandoAdmin) {
            return $this;
        }

        if ($type == 'All' || $type == 'NotFinished' || $type == 'Graph') {
            // درحالی که درخواست مورد نظر جهت ایجاد به دبیرخانه و ای تی نرسیده و کاربران از آن ها پیگیری میکنند دسترسی مشاهده داده شد
            if (\app\components\Security::formActionAccess(417)) {
                return $this->andWhere(['in', "$networkTable.CompanyIDRef", $companyChildsArray]);
            }
        }

        if (!$isCandoAdmin) {
            $this->andWhere(['or',
                ['and', // هر شرکت فقط درخواست های شرکت خودش را می بیند
                    "$processEntityTable.LastActionIDRef in (996,997,998)",
                    ['=', "$networkTable.CompanyIDRef", $companyId],
                ],
                ['and', // معاونت ها همه شرکت های زیر مجموعه را می بینند
                    "$processEntityTable.LastActionIDRef not in (996,997,998)",
                    ['in', "$networkTable.CompanyIDRef", $companyChildsArray],
                ]
            ]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($type == 'All' || $type == 'Finished' || $type == 'NotFinished' || $type == 'Graph') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        }
        if ($type == 'All' || $type == 'ToDo') {
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('network') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('network') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        }
        if ($type == 'ToDo') {
            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(['LastActionIDRef' => $processActionArray])
                    ->andWhere(["$processEntityTable.status" => 1])
                    ->andWhere(['<>', 'isEndAction', 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(["$processEntityTable.status" => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(["$processEntityTable.status" => 1]);
        } else if ($type == 'Graph') {
            $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        }

        return $this;
    }

    public function employeeSettlement($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processTable = Process::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $employeeSettlementTable = \app\modules\automation\models\EmployeeSettlement::tableName();
        $workflowActionTable = Action::tableName();

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        $this->innerJoin($processTable, "$processTable.ProcessID=$processEntityTable.ProcessIDRef")
                ->innerJoin("$actionTable", "$actionTable.ProcessIDRef=$processTable.ProcessID")
                ->leftJoin($actionEntityTable, "$actionEntityTable.ProcessEntityIDRef=$processEntityTable.ProcessEntityID AND $actionEntityTable.ActionIDRef=$actionTable.ActionID")
                ->innerJoin($employeeSettlementTable, "$processEntityTable.ObjectIDRef=$employeeSettlementTable.EmployeeSettlementID")
                ->andWhere(["$processTable.ProccessTypeIDRef" => Process::EMPLOYEESETTLEMENT_PROCESS_TYPE]);

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($isCandoAdmin) { // && $type == 'All'
            return $this;
        }

        if ($type == 'All' || $type == 'Finished' || $type == 'NotFinished' || $type == 'Graph') {
            if ($isAdmin) {
                return $this->andWhere(["$employeeSettlementTable.CreatedCompanyIDRef" => $companyId]);
            }
            /*$this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);*/
        }
        if ($type == 'ToDo') {
            // not confirmed
            $this->andWhere(['is', "$actionEntityTable.ActionIDRef", new \yii\db\Expression('null')]);

            //access to action
            $processActionArray = \app\modules\OBS\models\ObsProcessAction::find()->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(["$processEntityTable.status" => 1])
                    ->andWhere(['<>', 'isEndAction', 1]);

            //each company access to yourself request
            $this->andWhere(['or',
                /* ['and',
                  ['not in', "$actionTable.ActionID", [1011, 1012]],
                  ['=', "$employeeSettlementTable.CreatedCompanyIDRef", $companyId],
                  ],
                  ['and',
                  "$actionTable.ActionID in (1011)",
                  ['=', "$employeeSettlementTable.ManagementOBSIDRef", $obsId],
                  ],
                  ['and',
                  "$actionTable.ActionID in (1012)",
                  ['=', "$employeeSettlementTable.AssistanceOBSIDRef", $obsId],
                  ], */
                ['and',
                    ['in', "$actionTable.ActionID", [1071, 1072, 1073]], // بستن اتوماسیون و کندو شرکت ها توسط هلدینگ
                    ['in', "$actionTable.ActionID", $processActionArray],
                ],
                ['and',
                    ['not in', "$actionTable.ActionID", [1011, 1012, 1052, 1053]], // تایید واحدهای مرتبط
                    ['=', "$employeeSettlementTable.CreatedCompanyIDRef", $companyId],
                    ['in', "$actionTable.ActionID", $processActionArray],
                ],
                ['and',
                    "$obsId in (3164, 3807)",
                    ['in', "$actionTable.ActionID", [1011, 1012]], // در تسویه شرکت از هلدینگ تایید مدیر بالادست توسط منابع انسانی هلدینگ صورت میگیرد
                    "$employeeSettlementTable.CreatedCompanyIDRef<>$employeeSettlementTable.CompanyIDRef",
                ],
                ['and', // تایید مدیر مربوطه و مدیر بالادست
                    ['or',
                        ['and',
                            ['in', "$actionTable.ActionID", [1011, 1052]],
                            ['=', "$employeeSettlementTable.ManagementOBSIDRef", $obsId],
                        ],
                        ['and',
                            ['in', "$actionTable.ActionID", [1012, 1053]],
                            ['=', "$employeeSettlementTable.AssistanceOBSIDRef", $obsId],
                        ],
                    ],
                    "$employeeSettlementTable.CreatedCompanyIDRef=$employeeSettlementTable.CompanyIDRef",
                ]
            ]);

            // pre requisite action has done befor
            $doneActions = (new \yii\db\Query())
                    ->select("action_entity.ActionIDRef")
                    ->from("$actionEntityTable as action_entity")
                    ->where("action_entity.ProcessEntityIDRef = $processEntityTable.ProcessEntityID")
//                    ->andWhere("action.ActionID in ($actionTable.requiredAction)")
                    ->andWhere("FIND_IN_SET(action_entity.ActionIDRef, $actionTable.requiredAction)");
            $preRequisiteActions = (new \yii\db\Query())
                    ->select(new \yii\db\Expression('1'))
                    ->from("$actionTable as action")
                    ->where("action.ProcessIDRef = $processTable.ProcessID")
                    ->andWhere("FIND_IN_SET(action.ActionID, $actionTable.requiredAction)")
                    ->andWhere(["NOT IN", "action.ActionID", $doneActions]);
            $this->andWhere(['NOT EXISTS', $preRequisiteActions]);

            /* $preRequisiteActions = (new \yii\db\Query())
              ->select("GROUP_CONCAT(action_entity.ActionIDRef)")
              ->from("$actionEntityTable as action_entity")
              ->where("action_entity.ProcessEntityIDRef = $processEntityTable.ProcessEntityID")
              ->andWhere("action_entity.ActionIDRef in ($actionTable.requiredAction)")
              ->orderBy('action_entity.ActionIDRef');
              $this->andWhere(['=', "$actionTable.requiredAction", $preRequisiteActions]); */
        } else {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
//            $this->andWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        }
        if ($type == 'Finished') {
            $this->andWhere(["$processEntityTable.status" => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(["$processEntityTable.status" => 1]);
        }

        return $this;
    }

    public function employeeMembership($type, $user_id = -1, $obs_id = -1){
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $employeeMembershipTable = \app\modules\automation\models\EmployeeMembership::tableName();
        $workflowActionTable = Action::tableName();
        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $processTable = Process::tableName();

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);


        $this->innerJoin($processTable, "$processTable.ProcessID=$processEntityTable.ProcessIDRef")
            ->innerJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
            ->innerJoin($employeeMembershipTable, "$processEntityTable.ObjectIDRef=$employeeMembershipTable.EmployeeMembershipID")
            ->leftJoin($humanResourceTable, "$humanResourceTable.Id=$employeeMembershipTable.HumanResourceIDRef")
            ->leftJoin($obsTable, "$obsTable.obsId=$employeeMembershipTable.Position")
            ->leftJoin($positionTypeTable, "$positionTypeTable.PositionTypeID=$obsTable.positionTypeIdRef")
            ->andWhere(["$processTable.ProccessTypeIDRef" => Process::EMPLOYEE_MEMBERSHIP_PROCESS_TYPE]);


        if ($isCandoAdmin) {
            return $this;
        }

        if (!$isCandoAdmin) {
            $this->andWhere(['or',
                ['and', // هر شرکت فقط درخواست های شرکت خودش را می بیند
                    "$processEntityTable.LastActionIDRef in (1074,1075,1078)",
                    ['=', "$employeeMembershipTable.CompanyIDRef", $companyId],
                ],
                ['and', // معاونت ها همه شرکت های زیر مجموعه را می بینند
                    "$processEntityTable.LastActionIDRef not in (1074,1075,1078)",
                    ['in', "$employeeMembershipTable.CompanyIDRef", $companyChildsArray],
                ]
            ]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($type == 'All' || $type == 'Finished' || $type == 'NotFinished' || $type == 'Graph') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        }

        if ($type == 'ToDo') {
            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(['LastActionIDRef' => $processActionArray])
                ->andWhere(["$processEntityTable.status" => 1])
                ->andWhere(['<>', 'isEndAction', 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(["$processEntityTable.status" => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(["$processEntityTable.status" => 1]);
        } else if ($type == 'Graph') {
            $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        }

        return $this;
    }


    public function foreignMission($type, $user_id = -1, $obs_id = -1){
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $foreignMissionTable = \app\modules\automation\models\ForeignMission::tableName();

        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $positionTypeTable = \app\modules\automation\models\PositionType::tableName();
        $humanResourceTable = \app\modules\automation\models\HumanResource::tableName();
        $processTable = Process::tableName();
        $countryTable = PersonCountry::tableName();
        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);


        $this->innerJoin($processTable, "$processTable.ProcessID=$processEntityTable.ProcessIDRef")
            ->innerJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef")
            ->innerJoin($foreignMissionTable, "$processEntityTable.ObjectIDRef=$foreignMissionTable.MissionID")
//            ->leftJoin($humanResourceTable, "$humanResourceTable.Id=$foreignMissionTable.HumanResourceIDRef")
//            ->leftJoin("$humanResourceTable as humanResource", "humanResource.Id=$foreignMissionTable.HumanResourceIDRef")
//            ->leftJoin("$humanResourceTable as headGroup", "headGroup.Id=$foreignMissionTable.HeadGroupIDRef")
            ->leftJoin("$countryTable as country", "country.countryID=$foreignMissionTable.CountryIDRef")
//            ->leftJoin($obsTable, "$obsTable.obsId=$foreignMissionTable.Position")
//            ->leftJoin($positionTypeTable, "$positionTypeTable.PositionTypeID=$obsTable.positionTypeIdRef")
            ->andWhere(["$processTable.ProccessTypeIDRef" => Process::FOREIGN_MISSION_PROCESS_TYPE]);


        if ($isCandoAdmin) {
            return $this;
        }

        if ($type == 'All' || $type == 'NotFinished' || $type == 'Graph') {
            if (\app\components\Security::formActionAccess(441)) {
                return $this->andWhere(['in', "$foreignMissionTable.CompanyIDRef", $companyChildsArray]);
            }
        }

        if (!$isCandoAdmin) {
            $this->andWhere(['or',
                ['and', // هر شرکت فقط درخواست های شرکت خودش را می بیند
                    "$processEntityTable.LastActionIDRef in (1082,1083)",
                    ['=', "$foreignMissionTable.CompanyIDRef", $companyId],
                ],
                ['and', // معاونت ها همه شرکت های زیر مجموعه را می بینند
                    "$processEntityTable.LastActionIDRef not in (1082,1083)",
                    ['in', "$foreignMissionTable.CompanyIDRef", $companyChildsArray],
                ]
            ]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($type == 'All' || $type == 'Finished' || $type == 'NotFinished' || $type == 'Graph') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        }

        if ($type == 'ToDo') {
            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(['LastActionIDRef' => $processActionArray])
                ->andWhere(["$processEntityTable.status" => 1])
                ->andWhere(['<>', 'isEndAction', 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(["$processEntityTable.status" => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(["$processEntityTable.status" => 1]);
        } else if ($type == 'Graph') {
            $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
        }

        return $this;
    }
    
    public function stampDocument($type, $user_id = -1, $obs_id = -1)
    {
        if ($user_id != -1 && $obs_id != -1) {
            $obsId = $obs_id;
        } else {
            $obsId = Yii::$app->session->get('ObsPosition');
        }
        $currentObs = Obsnew::findOne($obsId);
        if (!$currentObs) {
            throw new \yii\web\BadRequestHttpException('OBS معتبر نیست.');
        }
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);
        $companyId = (int) $currentObs->companyIdRef;
        $categoryId = (int) $currentObs->categoryIdRef;
        $projectId = (int) $currentObs->ProjectIDRef;
        $siteId = (int) $currentObs->SiteIDRef;
        $processEntityTable = ProcessEntity::tableName();
        $processTable = Process::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = ActionEntity::tableName();
        $stampTable = StampDocument::tableName();
        $obsTable = Obsnew::tableName();
        $proccessType = Process::STAMP_DOCUMENT_PROCESS_TYPE;
        $stampObsAlias = 'stamp_obs';
        $DirectorRole = $currentObs->isDirector ? 1: 0 ;

        $this->select("$processEntityTable.*")
            ->distinct()
            ->innerJoin($processTable, "$processTable.ProcessID = $processEntityTable.ProcessIDRef")
            ->innerJoin($actionTable, "$actionTable.ActionID = $processEntityTable.LastActionIDRef")
            ->innerJoin($stampTable,"$stampTable.StampId = $processEntityTable.ObjectIDRef  AND $processTable.ProccessTypeIDRef = $proccessType")
            ->innerJoin("$obsTable $stampObsAlias", "$stampObsAlias.obsId = $stampTable.ObsIDRef")
            ->andWhere(["$processEntityTable.isDeleted" => 0])
            ->andWhere(["$stampTable.isDeleted" => 0]);
        if ($isCandoAdmin) {
            return $this;
        }
        // =========================================================
        // Access Condition
        // منطق سلسله‌مراتبی مشترک:
        // کاربر ستادی:
        // همه رکوردهای همان شرکت را می‌بیند.
        //
        // کاربر پروژه‌ای:
        // رکوردهای همان پروژه و کارگاه‌های همان پروژه را می‌بیند.
        //
        // کاربر کارگاهی:
        // فقط رکوردهای همان کارگاه را می‌بیند.
        //
        // این شرط برای:
        // 1) تب All در صورت داشتن 1172
        // 2) اکشن 1177
        // 3) اکشن 1178 وقتی از 1177 آمده
        // استفاده می‌شود.
        // =========================================================
        $accessCondition = [
            'and',
            ["$stampObsAlias.companyIdRef" => $companyId],
            '0=1',
        ];

        if ($categoryId === 2) {
            // ستاد: همه رکوردهای شرکت خودش
            $accessCondition = [
                'and',
                ["$stampObsAlias.companyIdRef" => $companyId],
            ];
        } elseif ($categoryId === 1 && $projectId > 0 && $siteId === 0) {
            // پروژه: خود پروژه + کارگاه‌های همان پروژه
            $accessCondition = [
                'and',
                ["$stampObsAlias.companyIdRef" => $companyId],
                ["$stampObsAlias.categoryIdRef" => 1],
                ["$stampObsAlias.ProjectIDRef" => $projectId],
            ];
        } elseif ($categoryId === 1 && $projectId > 0 && $siteId > 0) {
            // کارگاه: فقط همان کارگاه
            $accessCondition = [
                'and',
                ["$stampObsAlias.companyIdRef" => $companyId],
                ["$stampObsAlias.categoryIdRef" => 1],
                ["$stampObsAlias.ProjectIDRef" => $projectId],
                ["$stampObsAlias.SiteIDRef" => $siteId],
            ];
        }

        // =========================================================
        // دسترسی 1172 در تب All
        // اگر کاربر 1172 داشته باشد، فقط طبق Access Condition می‌بیند
        // اگر کاربر هلدینگ بود شرط 1172 رو چک نکنه
        // =========================================================
$hasAction1172 = ObsProcessAction::find()
    ->where(['OBSCategory3IDRef' => $obsId])
    ->andWhere(['ActionIDRef' => 1172])
    ->andWhere("COALESCE(isDeleted, 0) <> 1")
    ->exists();

$historyCondition = [
    'and',
    ["$stampTable.isDeleted" => 0],
    ["$processEntityTable.isDeleted" => 0],
    [
        'EXISTS',
        ActionEntity::find()
            ->select('ActionIDRef')
            ->where("ProcessEntityIDRef = $processEntityTable.ProcessEntityID")
            ->andWhere(['OBSCategory3IDRef' => $obsId])
            ->andWhere("COALESCE(isDeleted, 0) <> 1"),
    ],
];

if ($type === 'All' && $hasAction1172) {

    if ($companyId === 248) {
        $this->andWhere([
            'or',
            $accessCondition,
            $historyCondition,
        ]);
    } else {
        $this->andWhere($accessCondition);
    }

    return $this;
}

        // =========================================================
        // شرط کلی کارتابل
        // کاربر باید به اکشن جاری رکورد دسترسی داشته باشد
        // =========================================================
        $this->andWhere([
                'EXISTS',
                ObsProcessAction::find()
                    ->select('ActionIDRef')
                    ->where(['OBSCategory3IDRef' => $obsId])
                    ->andWhere("ActionIDRef = $processEntityTable.LastActionIDRef")
                    ->andWhere("COALESCE(isDeleted, 0) <> 1")
            ])
            ->andWhere(["$processEntityTable.status" => 1])
            ->andWhere(['<>', "$actionTable.isEndAction", 1]);

        $subQueryPreviousAction = "(SELECT ae.FromActionIDRef
            FROM $actionEntityTable ae
            WHERE ae.ProcessEntityIDRef = $processEntityTable.ProcessEntityID
              AND ae.ActionIDRef = $processEntityTable.LastActionIDRef
            ORDER BY ae.ID DESC
            LIMIT 1)";

        $publicConditions = [
            'and',
            ["$stampObsAlias.companyIdRef" => $companyId],
            [
                'or',
                // ستاد با ستاد
                ['and',
                    ["$stampObsAlias.categoryIdRef" => 2],
                    ["$stampObsAlias.categoryIdRef" => $categoryId],
                ],
                // ساختارهای پروژه/کارگاه
                ['and',
                    ["$stampObsAlias.categoryIdRef" => 1],
                    ['or',
                        // کارگاه
                        ['and',
                            ['>', "$stampObsAlias.SiteIDRef", 0],
                            ["$stampObsAlias.SiteIDRef" => $siteId],
                        ],
                        // پروژه
                        ['and',
                            ["$stampObsAlias.SiteIDRef" => null],
                            ['>', "$stampObsAlias.ProjectIDRef", 0],
                            ["$stampObsAlias.ProjectIDRef" => $projectId],
                        ],
                    ],
                ],
            ],
        ];
        $this->andWhere([
            'or',
            "$processEntityTable.LastActionIDRef IN (1179, 1181)",
            ['and',
                "$processEntityTable.LastActionIDRef = 1172",
                "(SELECT $actionEntityTable.OBSCategory3IDRef
                  FROM $actionEntityTable
                  WHERE $actionEntityTable.ProcessEntityIDRef = $processEntityTable.ProcessEntityID
                    AND $actionEntityTable.ActionIDRef = $processEntityTable.LastActionIDRef
                  ORDER BY ID DESC
                  LIMIT 1) = $obsId",
            ],
            ['and',
                "$processEntityTable.LastActionIDRef = 1178",
                [
                    'or',
                    ['and',
                        ["$stampTable.OrganizationalMaker" => 2],
                        $accessCondition,
                    ],
                    ['and',
                        ["$stampTable.OrganizationalMaker" => 1],
                        "$subQueryPreviousAction = 1177",
                        $accessCondition,
                    ],
                    ['and',
                        ["$stampTable.OrganizationalMaker" => 1],
                        "$subQueryPreviousAction = 1181",
                         $companyId === 248 ? '1=1' : '0=1',
                    ],
                ],
            ],
            ['and',
                "$processEntityTable.LastActionIDRef IN (1175, 1176)",
                [
                    'or',
                    ['and',
                        "$companyId = 248",
                        ["$stampTable.OrganizationalMaker" => 1],
                    ],
                    ['and',
                        ["$stampTable.OrganizationalMaker" => 2],
                        ["$stampObsAlias.companyIdRef" => $companyId],
                    ],
                ],
            ],
            ['and',
                "$processEntityTable.LastActionIDRef = 1174",
                $publicConditions,
            ],
            // 1177 با Access Condition
            ['and',
                "$processEntityTable.LastActionIDRef = 1177",
                $accessCondition,
            ],
            // 1173 بدون تغییر
// 1173
            ['and',
                "$processEntityTable.LastActionIDRef = 1173",
                [
                    'or',
                    // OrganizationalMaker = 1
                    ['and',
                        ["$stampTable.OrganizationalMaker" => 1],
                        ['<>', "$stampObsAlias.companyIdRef", 248],
                        ["$stampObsAlias.companyIdRef" => $companyId],
                        $DirectorRole == 1 ? '1=1' : '0=1',
                    ],

                    // OrganizationalMaker = 2
                    ['and',
                        ["$stampTable.OrganizationalMaker" => 2],
                        ['<>', "$stampObsAlias.companyIdRef", 248],
                        $publicConditions,
                    ],
                    // شرکت 248:
                    ['and',
                        ["$stampObsAlias.companyIdRef" => 248],
                        ["$stampObsAlias.relatedObsIdRef" => $obsId],
                    ],
                ],
            ],
        ]);
        if ($type === 'All' || $type === 'Graph') {
            $this->orWhere([
                'and',
                ["$stampTable.isDeleted" => 0],
                ["$processEntityTable.isDeleted" => 0],
                [
                    'EXISTS',
                    ActionEntity::find()
                        ->select('ActionIDRef')
                        ->where("ProcessEntityIDRef = $processEntityTable.ProcessEntityID")
                        ->andWhere(['OBSCategory3IDRef' => $obsId]),
                ],
            ]);
        }

        return $this;
    }
    /**
 * اسکوپ کارتابل امحا نشان سازمانی (ProcessType = 20 / ProcessID = 87)
 */
    public function refundStamp($type, $user_id = -1, $obs_id = -1)
    {
        if ($user_id != -1 && $obs_id != -1) {
            $obsId = $obs_id;
        } else {
            $obsId = Yii::$app->session->get('ObsPosition');
        }
        $currentObs = Obsnew::findOne($obsId);
        if (!$currentObs) {
            throw new \yii\web\BadRequestHttpException('OBS معتبر نیست.');
        }
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);
        $companyId    = (int) $currentObs->companyIdRef;
        $categoryId   = (int) $currentObs->categoryIdRef;
        $projectId    = (int) $currentObs->ProjectIDRef;
        $siteId       = (int) $currentObs->SiteIDRef;
        $DirectorRole = $currentObs->isDirector ? 1 : 0;
        $processEntityTable = ProcessEntity::tableName();
        $processTable       = Process::tableName();
        $actionTable        = Action::tableName();
        $actionEntityTable  = ActionEntity::tableName();
        $stampTable         = StampDocument::tableName();
        $obsTable           = Obsnew::tableName();
        $proccessType  = Process::REFUND_STAMP_PROCESS_TYPE; // باید 20 باشد
        $stampObsAlias = 'stamp_obs';
        $this->select("$processEntityTable.*")
            ->distinct()
            ->innerJoin($processTable, "$processTable.ProcessID = $processEntityTable.ProcessIDRef")
            ->innerJoin($actionTable, "$actionTable.ActionID = $processEntityTable.LastActionIDRef")
            ->innerJoin($stampTable, "$stampTable.StampId = $processEntityTable.ObjectIDRef AND $processTable.ProccessTypeIDRef = $proccessType")
            ->innerJoin("$obsTable $stampObsAlias", "$stampObsAlias.obsId = $stampTable.ObsIDRef")
            ->andWhere(["$processEntityTable.isDeleted" => 0])
            ->andWhere(["$stampTable.isDeleted" => 0]);
        if ($isCandoAdmin) {
            return $this;
        }
        // =========================================================
        // Access Condition (سلسله‌مراتبی)
        // ستاد → همه شرکت خودش
        // پروژه → پروژه + کارگاه‌های همان پروژه
        // کارگاه → فقط همان کارگاه
        // =========================================================
        $accessCondition = ['and', ["$stampObsAlias.companyIdRef" => $companyId], '0=1'];
        if ($categoryId === 2) {
            $accessCondition = [
                'and',
                ["$stampObsAlias.companyIdRef" => $companyId],
            ];
        } elseif ($categoryId === 1 && $projectId > 0 && $siteId === 0) {
            $accessCondition = [
                'and',
                ["$stampObsAlias.companyIdRef" => $companyId],
                ["$stampObsAlias.categoryIdRef" => 1],
                ["$stampObsAlias.ProjectIDRef" => $projectId],
            ];
        } elseif ($categoryId === 1 && $projectId > 0 && $siteId > 0) {
            $accessCondition = [
                'and',
                ["$stampObsAlias.companyIdRef" => $companyId],
                ["$stampObsAlias.categoryIdRef" => 1],
                ["$stampObsAlias.ProjectIDRef" => $projectId],
                ["$stampObsAlias.SiteIDRef" => $siteId],
            ];
        }
        $historyCondition = [
            'and',
            ["$stampTable.isDeleted" => 0],
            ["$processEntityTable.isDeleted" => 0],
            [
                'EXISTS',
                ActionEntity::find()
                    ->select('ActionIDRef')
                    ->where("ProcessEntityIDRef = $processEntityTable.ProcessEntityID")
                    ->andWhere(['OBSCategory3IDRef' => $obsId])
                    ->andWhere("COALESCE(isDeleted, 0) <> 1"),
            ],
        ];
        $hasAction1195 = ObsProcessAction::find()
            ->where(['OBSCategory3IDRef' => $obsId])
            ->andWhere(['ActionIDRef' => 1195])
            ->andWhere("COALESCE(isDeleted, 0) <> 1")
            ->exists();
        if ($type === 'All' && $hasAction1195) {
            if ($companyId === 248) {
                $this->andWhere(['or', $accessCondition, $historyCondition]);
            } else {
                $this->andWhere($accessCondition);
            }
            return $this;
        }
        $this->andWhere([
                'EXISTS',
                ObsProcessAction::find()
                    ->select('ActionIDRef')
                    ->where(['OBSCategory3IDRef' => $obsId])
                    ->andWhere("ActionIDRef = $processEntityTable.LastActionIDRef")
                    ->andWhere("COALESCE(isDeleted, 0) <> 1")
            ])
            ->andWhere(["$processEntityTable.status" => 1])
            ->andWhere(['<>', "$actionTable.isEndAction", 1]);
        $this->andWhere([
            'or',
            ['and',
                "$processEntityTable.LastActionIDRef = 1195",
                $historyCondition,
            ],
            ['and',
                "$processEntityTable.LastActionIDRef = 1196",
                $accessCondition,
            ],
            ['and',
                "$processEntityTable.LastActionIDRef = 1197",
                [
                    'or',
                    ['and',
                        ["$stampObsAlias.companyIdRef" => 248],
                        $companyId === 248 ? '1=1' : '0=1',
                    ],
                    ['and',
                        ['<>', "$stampObsAlias.companyIdRef", 248],
                        ["$stampObsAlias.companyIdRef" => $companyId],
                        $DirectorRole == 1 ? '1=1' : '0=1',
                    ],
                ],
            ],
            ['and',
                "$processEntityTable.LastActionIDRef = 1198",
                $companyId === 248 ? '1=1' : '0=1',
            ],
            ['and',
                "$processEntityTable.LastActionIDRef = 1199",
                [
                    'or',
                    ['and',
                        ["$stampTable.LicenseIssueType" => 1],
                        $companyId === 248 ? '1=1' : '0=1',
                    ],
                    ['and',
                        ["$stampTable.LicenseIssueType" => 2],
                        $accessCondition,
                    ],
                ],
            ],
            ['and',
            "$processEntityTable.LastActionIDRef = 1200",
            $accessCondition,
        ],
        ]);
        if ($type === 'All' || $type === 'Graph') {
            $this->orWhere([
                'and',
                ["$stampTable.isDeleted" => 0],
                ["$processEntityTable.isDeleted" => 0],
                [
                    'EXISTS',
                    ActionEntity::find()
                        ->select('ActionIDRef')
                        ->where("ProcessEntityIDRef = $processEntityTable.ProcessEntityID")
                        ->andWhere(['OBSCategory3IDRef' => $obsId]),
                ],
            ]);
        }
        return $this;
    }
    public function contractClosingDocument($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $companyChildsArray = \app\components\CandoHelper::companyChilds(Yii::$app->session->get('CompanyID'), 'array');

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionTable = Action::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $contractClosingDocumentTable = \app\modules\contract\models\ContractDocument::tableName();
        $contractTable = \app\modules\contract\models\Contract::tableName();
        $contractEpsTable = \app\modules\contract\models\ContractEps::tableName();

        $this->innerJoin($actionTable, "$actionTable.ActionID=$processEntityTable.LastActionIDRef");
        $this->innerJoin($contractClosingDocumentTable, "$processEntityTable.ContractClosingDocumentIDRef=$contractClosingDocumentTable.ContractDocumentID");
        $this->innerJoin($contractTable, "$contractClosingDocumentTable.ContractIDRef=$contractTable.ContractID");

        $this->andWhere("$contractClosingDocumentTable.isDeleted is null or $contractClosingDocumentTable.isDeleted<>1");

        $epsArray = \app\components\CandoHelper::UserEps($uid, $obsId, 'array');
        $epsList = empty($epsArray) ? "-1" : implode(',', $epsArray);

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $processActionArray = \app\modules\OBS\models\ObsProcessAction::find()->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'CashRequestWorkflowReport', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        if ($isCandoAdmin && $type == 'All') {
            return $this;
        } else if ($isAdmin && $type == 'All') {
            $this->andWhere(['or',
                "$contractTable.SignerPersonIDRef=$companyId",
                ["and",
                    "$contractTable.SubContractorPersonIDRef=$companyId",
                    ["not in", "$contractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                ],
            ]);
            return $this;
        } else {
            $this->andWhere(['or',
                ['and', // حقوقی هلدینگ همه شرکت های زیر مجموعه را می بیند
                    "$processEntityTable.LastActionIDRef in (959,960,961,962)",
                    ['or',
                        ['in', "$contractTable.SignerPersonIDRef", $companyChildsArray],
                        ['and',
                            ["not in", "$contractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                            ['in', "$contractTable.SubContractorPersonIDRef", $companyChildsArray],
                        ]
                    ]
                ],
                ['and',
                    "$processEntityTable.LastActionIDRef not in (959,960,961,961)",
                    ['or', // هر شرکت قراردادهای خودش را می بیند
                        "$contractTable.SignerPersonIDRef=$companyId",
                        ['and',
                            ["not in", "$contractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
                            ['in', "$contractTable.SubContractorPersonIDRef", $companyChildsArray],
                        ]
                    ],
                    ['or', // باید دسترسی پروژه  قرارداد را داشته باشد
                        "(select count(*) from $contractEpsTable where ContractIDRef=$contractTable.ContractID and COALESCE(isDeleted,0)<>1) = 0",
                        "(select count(*) from $contractEpsTable where ContractIDRef=$contractTable.ContractID and EPSIDRef in ($epsList) and COALESCE(isDeleted,0)<>1) > 0",
                    ]
                ],
            ]);
        }

        /* $this->andWhere(['or',
          ['and',
          "$processEntityTable.LastActionIDRef in (959,960,961,961)",
          ['or',
          ['in', "$contractTable.SignerPersonIDRef", $companyChildsArray],
          ['and',
          ["not in", "$contractTable.SignerPersonIDRef", (\app\modules\OBS\models\Company::find()->asArray()->addSelect(["companyIdRef"]))],
          ['in', "$contractTable.SubContractorPersonIDRef", $companyChildsArray],
          ]
          ]
          ],
          ['or',
          "(select count(*) from $contractEpsTable where ContractIDRef=$contractTable.ContractID and COALESCE(isDeleted,0)<>1) = 0",
          "(select count(*) from $contractEpsTable where ContractIDRef=$contractTable.ContractID and EPSIDRef in ($epsList) and COALESCE(isDeleted,0)<>1) > 0",
          //                ['=', ContractEps::find()->select('count(*)')->where(["ContractIDRef" => new \yii\db\Expression("$contractTable.ContractID")])->andWhere("COALESCE(isDeleted,0)<>1")->createCommand()->rawSql, 0],
          //                ['>', ContractEps::find()->select('count(*)')->where(["ContractIDRef" => new \yii\db\Expression("$contractTable.ContractID")])->andWhere(['EPSIDRef' => $epsArray])->andWhere("COALESCE(isDeleted,0)<>1")->createCommand()->rawSql, 0]
          ]
          ]); */

        if ($type == 'All' || $type == 'Graph' || $type == 'ToDo') {
            if ($type == 'All' || $type == 'Graph') {
                $this->orWhere("$processEntityTable.ProcessEntityID in (select DISTINCT $actionEntityTable.ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef=$obsId)");
            } else {
                $this->andWhere(["in", 'LastActionIDRef', $processActionArray])->andWhere(["$processEntityTable.status" => 1]);
            }
        } else if ($type == 'Finished' || $type == 'NotFinished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            if ($type == 'Finished') {
                $this->andWhere(["$processEntityTable.status" => 0]);
            } else if ($type == 'NotFinished') {
                $this->andWhere(["$processEntityTable.status" => 1]);
            }
        }

        return $this;
    }

    public function permission($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');
        $userObsEntityTable = \app\modules\account\models\UserObsEntity::tableName();
        $invoiceTable = \app\modules\invoice\models\InvoiceEdit::tableName();
        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $permissionTable = \app\modules\permission\models\Permission::tableName();
        $workflowActionTable = Action::tableName();

        $contractQuery = \app\modules\contract\models\Contract::find()
                ->contractAccess('InvoiceList', $uid, $obsId);

        $this->innerJoin($permissionTable, "$permissionTable.id=$processEntityTable.permissionId");
        $this->innerJoin(["contractQuery" => $contractQuery], "contractQuery.ContractID=$permissionTable.contractId");

        // دسترسی به ادمین
        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($type == 'All' && $isCandoAdmin) {
            return $this;
        } else {
            if ($type != 'Graph') {
                $this->andWhere("contractQuery.SignerPersonIDRef=" . $companyId);
            }
        }

        if ($type == 'All') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('permission') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('permission') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        } else if ($type == 'ToDo') {
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('permission') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('permission') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);

            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            return $this->andWhere(['LastActionIDRef' => $processActionArray])->andWhere(['status' => 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 1]);
        } else if ($type == 'Graph') {
            
        } else {
            
        }

        return $this;
    }

    public function sourcing($type, $user_id = -1, $obs_id = -1) {
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();
        $personTable = \app\modules\OBS\models\Person::tableName();
        $workflowActionTable = Action::tableName();

        // $contractQuery = \app\modules\contract\models\Contract::find()
        //         ->contractAccess('InvoiceList',$uid,$obsId);
//        $this->innerJoin($personTable, "$personTable.PersonID=$processEntityTable.PersonID");
        // $this->innerJoin(["contractQuery" => $contractQuery], "contractQuery.ContractID=$permissionTable.contractId");
        // دسترسی به ادمین
        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($type == 'All' && $isCandoAdmin) {
            return $this;
        }
        if ($type == 'All') {
            if ($isAdmin) {
                return $this;
            }
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('sourcing') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('sourcing') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);
        } else if ($type == 'ToDo') {
            $this->andWhere(["or",
                "$processEntityTable.LastActionIDRef not in (" . $this->showObsChildsActionList('sourcing') . ")",
                "(select count(*) from $actionEntityTable where ProcessEntityIDRef=$processEntityTable.ProcessEntityID and $actionEntityTable.ActionIDRef in (" . $this->showObsChildsActionList('sourcing') . ") and $actionEntityTable.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $actionEntityTable.FromActionIDRef is null 
                        or
                        $actionEntityTable.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$processEntityTable.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$processEntityTable.ProcessIDRef)
                        ))>0",
            ]);

            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->where(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            return $this->andWhere(['LastActionIDRef' => $processActionArray])->andWhere(['status' => 1]);
        } else if ($type == 'Finished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                "$processEntityTable.LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
            $this->andWhere(['status' => 1]);
        } else if ($type == 'Graph') {
            
        } else {
            
        }

        return $this;
    }

    public function debitmemo($type, $user_id = -1, $obs_id = -1) {
        //ToDo farzam
//        $uid = Yii::$app->session->get('UserID');
//        $obsId = Yii::$app->session->get('ObsPosition');
//        $companyId = Yii::$app->session->get('CompanyID');

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $userObsEntityTable = \app\modules\account\models\UserObsEntity::tableName();
        $debitmemoTable = \app\modules\debitmemo\models\Debitmemo::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $actionEntityTable = \app\modules\workflow\models\ActionEntity::tableName();

        $invoiceAmountTable = \app\modules\invoice\models\InvoiceAmountEdit::tableName();
        $debitmemoAmountTable = \app\modules\debitmemo\models\DebitmemoAmount::tableName();
        $costCodeTable = \app\modules\wbs\models\Costcodenew::tableName();

        $contractQuery = \app\modules\contract\models\Contract::find()->contractAccess('ContractList', $uid, $obsId);

        $this->innerJoin($debitmemoTable, "$processEntityTable.debitmemoIdRef=$debitmemoTable.id");
        $this->innerJoin(["contractQuery" => $contractQuery], "contractQuery.ContractID=$debitmemoTable.contractIdRef");
//        $this->innerJoin($userObsEntityTable, "$userObsEntityTable.entityId=$debitmemoTable.contractIdRef");
//        $this->andWhere("$userObsEntityTable.userIdRef=$uid and $userObsEntityTable.obsIdRef=$obsId and $userObsEntityTable.accessType='ContractList' and $userObsEntityTable.entityType='contract'");

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($type == 'All' && $isCandoAdmin) {
            return $this;
        }

        if (!$isCandoAdmin) {
//            $this->andWhere(["or",
//                "contractQuery.SignerPersonIDRef=" . $companyId,
//                "(select count(*) from $debitmemoAmountTable as tblDebitmemoAmount
//                    inner join $invoiceAmountTable as tblInvoiceAmount on tblInvoiceAmount.id=tblDebitmemoAmount.invoiceAmountIdRef
//                    inner join $costCodeTable on left(tblInvoiceAmount.CostCode,6)=$costCodeTable.NodeID
//                    where tblDebitmemoAmount.debitmemoIdRef=$processEntityTable.debitmemoIdRef and $costCodeTable.access like '%comv" . $companyId . "%' 
//                group by tblDebitmemoAmount.debitmemoIdRef,ownerCompanyIdRef)>0",
//            ]);
            $this->andWhere(["or",
                "contractQuery.SignerPersonIDRef=" . $companyId,
                "$debitmemoTable.ownerCompanyIdRef=" . $companyId,
            ]);
        }


        if ($type == 'All') {
            if ($isAdmin) {
                return $this;
            }
            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->andWhere(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            $this->andWhere(["or",
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))",
                ['LastActionIDRef' => $processActionArray]
            ]);
        } else if ($type == 'ToDo') {

            $processActionArray = \app\components\ActiveModel::find('\app\modules\OBS\models\ObsProcessAction')->select(['ActionIDRef'])->andWhere(['OBSCategory3IDRef' => $obsId])->andWhere("isDeleted is null or isDeleted <>1")->asArray();
            return $this->andWhere(['LastActionIDRef' => $processActionArray])->andWhere(['status' => 1]);
        } else if ($type == 'Finished') {
            $this->andWhere("$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))");
            return $this->andWhere(['status' => 0]);
        } else if ($type == 'NotFinished') {
            $this->andWhere("$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $actionEntityTable where OBSCategory3IDRef in ($obsAllChildsList))");
            return $this->andWhere(['status' => 1]);
        } else if ($type == 'Graph') {
            
        } else {
            
        }
        return $this;
    }

    public function cashRequest($type, $user_id = -1, $obs_id = -1) {
        $tblProcessEntity = ProcessEntity::tableName();
        $tblActionEntity = ActionEntity::tableName();
        $tblObsProcessAction = \app\modules\OBS\models\ObsProcessAction::tableName();
        $tblObs = \app\modules\OBS\models\Obsnew::tableName();
        $tblCashRequest = \app\modules\cashRequest\models\CashRequest::tableName();
        $workflowActionTable = Action::tableName();

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');
        if ($epsList == '') {
            $epsList = '0';
        }


        $this->innerJoin($tblCashRequest, $tblCashRequest . '.CashRequestID=' . $tblProcessEntity . '.CashRequestIDRef')
                ->innerJoin($tblObs, "$tblObs.obsId=$tblCashRequest.OBSCategory3IDRef");

//        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if (!$isCandoAdmin) {
            $this->andWhere(["$tblObs.companyIdRef" => $companyId]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'CashRequestWorkflowReport', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        //قدم دوم حذف ای پی اس های غیر مرتبط
        if (($type == 'All') && $isCandoAdmin) {
            return $this;
        } else {
            $this->andWhere(\app\modules\cashRequest\models\CashRequest::tableName() . '.CashRequestID in (select CashRequestIDRef from ' . \app\modules\cashRequest\models\CashRequestAmount::tableName() . ' where EPSIDRef in(' . $epsList . '))');
        }

        if ($type == 'All') {

            if (\app\access\AccessControl::isAccess(['obs', 'check' => 'isAdmin', 'errorException' => false])) {
                return $this;
            }

            $this->andWhere("$tblProcessEntity.LastActionIDRef in (select $tblObsProcessAction.ActionIDRef from $tblObsProcessAction where $tblObsProcessAction.OBSCategory3IDRef=$obsId and ($tblObsProcessAction.isDeleted is null or $tblObsProcessAction.isDeleted<>1))");

            $this->andWhere(["or",
                "$tblProcessEntity.LastActionIDRef not in (" . $this->showObsChildsActionList('cashRequest') . ")",
                "(select count(*) from $tblActionEntity where ProcessEntityIDRef=$tblProcessEntity.ProcessEntityID and $tblActionEntity.ActionIDRef in (" . $this->showObsChildsActionList('cashRequest') . ") and $tblActionEntity.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $tblActionEntity.FromActionIDRef is null 
                        or
                        $tblActionEntity.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$tblProcessEntity.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$tblProcessEntity.ProcessIDRef)
                        ))>0",
            ]);

            $this->orWhere("$tblProcessEntity.ProcessEntityID in (select DISTINCT $tblActionEntity.ProcessEntityIDRef from $tblActionEntity where OBSCategory3IDRef=$obsId)");
        } else if ($type == 'ToDo') {
            $this->andWhere("$tblProcessEntity.status=1");
            $this->andWhere("$tblProcessEntity.LastActionIDRef in (select $tblObsProcessAction.ActionIDRef from $tblObsProcessAction where $tblObsProcessAction.OBSCategory3IDRef=$obsId and ($tblObsProcessAction.isDeleted is null or $tblObsProcessAction.isDeleted<>1))");

            $this->andWhere(["or",
                "$tblProcessEntity.LastActionIDRef not in (" . $this->showObsChildsActionList('cashRequest') . ")",
                "(select count(*) from $tblActionEntity where ProcessEntityIDRef=$tblProcessEntity.ProcessEntityID and $tblActionEntity.ActionIDRef in (" . $this->showObsChildsActionList('cashRequest') . ") and $tblActionEntity.OBSCategory3IDRef in ($obsAllChildsList)"
                . "and (
                        $tblActionEntity.FromActionIDRef is null 
                        or
                        $tblActionEntity.FromActionIDRef in (select ActionID from $workflowActionTable Where ActionOrder<=(
                        select ActionOrder from $workflowActionTable Where ActionID=$tblProcessEntity.LastActionIDRef
                        ) and $workflowActionTable.ProcessIDRef=$tblProcessEntity.ProcessIDRef)
                        ))>0",
            ]);
        } else if ($type == 'Finished') {
            $this->andWhere(['status' => 0]);
            $this->andWhere("$tblProcessEntity.ProcessEntityID in (select ProcessEntityIDRef from $tblActionEntity where OBSCategory3IDRef in ($obsAllChildsList))");
            return $this;
        } else if ($type == 'NotFinished') {
            $this->andWhere(['status' => 1]);
            $this->andWhere(['or',
                "$tblProcessEntity.ProcessEntityID in (select ProcessEntityIDRef from $tblActionEntity where OBSCategory3IDRef in ($obsAllChildsList))",
                ['and',
                    "$tblProcessEntity.LastActionIDRef in (select $tblObsProcessAction.ActionIDRef from $tblObsProcessAction where $tblObsProcessAction.OBSCategory3IDRef=$obsId and ($tblObsProcessAction.isDeleted is null or $tblObsProcessAction.isDeleted<>1))",
                    "($tblProcessEntity.LastActionIDRef<>377) or ($tblProcessEntity.LastActionIDRef=377 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($tblProcessEntity.LastActionIDRef<>378) or ($tblProcessEntity.LastActionIDRef=378 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($tblProcessEntity.LastActionIDRef<>431) or ($tblProcessEntity.LastActionIDRef=431 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($tblProcessEntity.LastActionIDRef<>432) or ($tblProcessEntity.LastActionIDRef=432 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($tblProcessEntity.LastActionIDRef<>854) or ($tblProcessEntity.LastActionIDRef=854 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($tblProcessEntity.LastActionIDRef<>404) or ($tblProcessEntity.LastActionIDRef=404 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                    "($tblProcessEntity.LastActionIDRef<>405) or ($tblProcessEntity.LastActionIDRef=405 and ($tblCashRequest.OBSCategory3IDRef in ($obsAllChildsList)))",
                ]
            ]);
        } else if ($type == 'CashRequestWorkflowReport') {
            
        } else if ($type == 'Graph') {
            
        } else {
            
        }

        return $this;
    }

    public function claim($type, $user_id = -1, $obs_id = -1) {
        $claimTable = \app\modules\claim\models\Claim::tableName();
        $processEntityTable = \app\modules\workflow\models\ProcessEntity::tableName();
        $tblActionEntity = ActionEntity::tableName();
        $tblObsProcessAction = \app\modules\OBS\models\ObsProcessAction::tableName();
        $tblClaim = \app\modules\claim\models\Claim::tableName();

//        $uid = Yii::$app->session->get('UserID');
//        $obsId = Yii::$app->session->get('ObsPosition');
//        $companyId = Yii::$app->session->get('CompanyID');
        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');
        if ($epsList == '') {
            $epsList = '0';
        }

        $contractQuery = \app\modules\contract\models\Contract::find()->contractAccess('ContractList', $uid, $obsId);

        $this->innerJoin($tblClaim, "$tblClaim.id=$processEntityTable.claimIdRef");
        $this->innerJoin(["contractQuery" => $contractQuery], "contractQuery.ContractID=$claimTable.contractIdRef");

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if ($type == 'All' && $isCandoAdmin) {
            return $this;
        }

        if ($type == 'All') {
            if ($isAdmin) {
                $this->andWhere("contractQuery.SignerPersonIDRef=" . $companyId);
                return $this;
            }

            //  مهندس گفتن شرط اعمال نشود
            /* if (!($isCandoAdmin || ($obsId == 830))) {
              $this->andWhere(['or',
              "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from " . $tblActionEntity . " where OBSCategory3IDRef in ($obsAllChildsList))",
              ['and',
              "$processEntityTable.LastActionIDRef in (select $tblObsProcessAction.ActionIDRef from $tblObsProcessAction where $tblObsProcessAction.OBSCategory3IDRef=$obsId and ($tblObsProcessAction.isDeleted is null or $tblObsProcessAction.isDeleted<>1))",
              ]
              ]);
              } */
        } else if ($type == 'ToDo') {
            $this->andWhere("$processEntityTable.status=1");
            $this->andWhere("$processEntityTable.LastActionIDRef in (select $tblObsProcessAction.ActionIDRef from $tblObsProcessAction where $tblObsProcessAction.OBSCategory3IDRef=$obsId and ($tblObsProcessAction.isDeleted is null or $tblObsProcessAction.isDeleted<>1))");
        } else if ($type == 'Finished') {
            $this->andWhere(["$processEntityTable.status" => 0]);
            $this->andWhere("$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $tblActionEntity where OBSCategory3IDRef in ($obsAllChildsList))");
            return $this;
        } else if ($type == 'NotFinished') {
            $this->andWhere(["$processEntityTable.status" => 1]);
            $this->andWhere(['or',
                "$processEntityTable.ProcessEntityID in (select ProcessEntityIDRef from $tblActionEntity where OBSCategory3IDRef in ($obsAllChildsList))",
                ['and',
                    "$processEntityTable.LastActionIDRef in (select $tblObsProcessAction.ActionIDRef from $tblObsProcessAction where $tblObsProcessAction.OBSCategory3IDRef=$obsId and ($tblObsProcessAction.isDeleted is null or $tblObsProcessAction.isDeleted<>1))",
                ]
            ]);
        } else if ($type == 'Graph') {
            
        } else {
            
        }

        return $this;
    }

    public function actualProgress($type, $user_id = -1, $obs_id = -1) {

        $budgetTable = Budget::tableName();
        $planTable = \app\modules\budget\models\Plan::tableName();
        $actualProgress = \app\modules\budget\models\ActualProgress::tableName();
        $processEntityTable = ProcessEntity::tableName();

        $ObsProcessActionTable = \app\modules\OBS\models\ObsProcessAction::tableName();

//        $uid = Yii::$app->session->get('UserID');
//        $obsId = Yii::$app->session->get('ObsPosition');
//        $companyId = Yii::$app->session->get('CompanyID');

        if ($user_id <> -1 && $obs_id <> -1) {
            $uid = $user_id;
            $obsId = $obs_id;
            $obsModel = \app\modules\OBS\models\Obsnew::find()->andWhere(["obsId" => $obs_id])->one();
            if (!isset($obsModel)) {
                throw new \yii\web\BadRequestHttpException("Obs Id is not valid!!");
            }
            $companyId = $obsModel->companyIdRef;
        } else {
            $uid = Yii::$app->session->get('UserID');
            $obsId = Yii::$app->session->get('ObsPosition');
            $companyId = Yii::$app->session->get('CompanyID');
        }

        $isAdmin = \app\access\AccessControl::isAdmin($obsId);
        $obsAllChildsList = \app\components\CandoHelper::obsChilds($companyId, $obsId, 'string');
        $epsList = \app\components\CandoHelper::UserEps($uid, $obsId, 'string');

        $entityType = 9; // Actual Progress => 9
        $eps_entity = \app\components\CandoHelper::UserEpsEntity($uid, $obsId, $entityType, 'view', 'array');

        if ($epsList == '') {
            $epsList = '0';
        }

        $obsTable = \app\modules\OBS\models\Obsnew::tableName();
        $this->innerJoin($actualProgress, "$actualProgress.id=$processEntityTable.actualProgressAnnualBudgetIdRef")
                ->innerJoin($planTable, "$actualProgress.planIdRef=$planTable.PlanID")
                ->innerJoin(Budget::tableName(), "$budgetTable.PlanIDRef=$planTable.PlanID")
                ->innerJoin($obsTable, "$obsTable.obsId=" . Budget::tableName() . ".OBSCategory3IDRef");

        $isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);

        if (!$isCandoAdmin) {
            // @@ باید از کامنت برداشته شود
            $this->andWhere(["$obsTable.companyIdRef" => $companyId]);
        }

        //قدم اول حذف نوع های غیر مرتبط
        if (!in_array($type, ['All', 'ToDo', 'Finished', 'NotFinished', 'BudgetWorkflowReport', 'Graph'])) {
            return $this->andWhere('1=0');
        }

        // ادمین شرکت ها در
        //All
        //همه را ببینند  
        if ($type == 'All' && $isAdmin) {
            return $this;
        }

        $this->andWhere(["$budgetTable.EPSIDRefL" => $eps_entity]);

        //قدم دوم حذف ای پی اس های غیر مرتبط
        if (($type == 'All' || ($type == 'BudgetWorkflowReport')) && (($uid == 1 || $uid == 2 || $uid == 25) && ($obsId == 606))) {
            return $this;
        } else {
            // @@ باید از کامنت برداشته شود
            $this->andWhere(Budget::tableName() . ".EPSIDRefL in($epsList)");
        }


        if ($type == 'All') {
            $this->andWhere(['or',
                ProcessEntity::tableName() . ".ProcessEntityID in (select ProcessEntityIDRef from " . ActionEntity::tableName() . " where OBSCategory3IDRef in ($obsAllChildsList))",
                ProcessEntity::tableName() . ".LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        } else if ($type == 'ToDo') {
            $this->andWhere(ProcessEntity::tableName() . ".status=1");
            $this->andWhere(ProcessEntity::tableName() . ".LastActionIDRef in (select $ObsProcessActionTable.ActionIDRef from $ObsProcessActionTable where $ObsProcessActionTable.OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))");
        } else if ($type == 'Finished') {
            $this->andWhere([ProcessEntity::tableName() . '.status' => 0]);
            $this->andWhere(ProcessEntity::tableName() . ".ProcessEntityID in (select ProcessEntityIDRef from " . ActionEntity::tableName() . " where OBSCategory3IDRef in ($obsAllChildsList))");
        } else if ($type == 'NotFinished') {
            $this->andWhere(['or',
                ProcessEntity::tableName() . ".status=1 and " . ProcessEntity::tableName() . ".ProcessEntityID in (select ProcessEntityIDRef from " . ActionEntity::tableName() . " where OBSCategory3IDRef in ($obsAllChildsList))",
                ProcessEntity::tableName() . ".LastActionIDRef in (select " . \app\modules\OBS\models\ObsProcessAction::tableName() . ".ActionIDRef from " . \app\modules\OBS\models\ObsProcessAction::tableName() . " where " . \app\modules\OBS\models\ObsProcessAction::tableName() . ".OBSCategory3IDRef=$obsId and ($ObsProcessActionTable.isDeleted is null or $ObsProcessActionTable.isDeleted<>1))",
            ]);
        } else if ($type == 'BudgetWorkflowReport') {
            
        } else if ($type == 'Graph') {
            
        } else {
            
        }




        return $this;
    }

    public function reportItem($itemId) {
        return $this->innerJoin(\app\modules\reportWorkflow\models\ReportItemAction::tableName(), \app\modules\reportWorkflow\models\ReportItemAction::tableName() . '.actionIdRef=' . ProcessEntity::tableName() . '.LastActionIDRef')
                        ->andWhere(["in", 'itemIdRef', $itemId])
        ;
    }

    /**
     * 
     * @param type $processType
     * @param type $returnType
     * 
     * processType Value List ['invoice','contract','budget','capital']
     * 
     * returnType Value list ['array','string']
     * returnType default value :"string"
     * @return returnType
     */
    private function showObsChildsActionList($processType, $returnType = 'string') {
        $actionParamTable = ActionParam::tableName();
        $data = (new \yii\db\Query())->from($actionParamTable)
                ->andWhere("paramTitle='showObsChilds' and value1='$processType' and value2=1")
                ->select([
                    "result" => "GROUP_CONCAT(actionIdRef)"
                ])
                ->groupBy("paramTitle")
                ->one();
        if ($returnType == 'string') {
            if (empty($data['result'])) {
                return '0';
            } else {
                return $data['result'];
            }
        } else {
            return $data['result'];
        }

//select paramTitle,GROUP_CONCAT(actionIdRef) from workflow_table_action_param 
//where paramTitle='showObsChilds' and value1='invoice' and value2=1 
//group by paramTitle;
    }

}
