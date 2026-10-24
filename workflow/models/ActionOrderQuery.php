<?php

namespace app\modules\workflow\models;

use Yii;
use app\modules\workflowContract\models\WorkflowContract;
use app\modules\cashRequest\models\CashRequestAmount;
use app\modules\contract\models\Contract;
use app\modules\budget\models\AnnualBudget;

class ActionOrderQuery extends \yii\db\ActiveQuery {

    public function all($db = null) {
        return parent::all($db);
    }

    public function one($db = null) {
        return parent::one($db);
    }

    public function process($pid) {
        return $this->andWhere("ActionIDRef in (select ActionID from " . Action::tableName() . " where ProcessIDRef=:pid)")
                        ->addParams([':pid' => $pid]);
    }

    public function nextAction($peid) {
        $processEntityModel = ProcessEntity::find()
                ->where(['ProcessEntityID' => $peid])
                ->one();

        $this->joinWith(['nextAction'])
                ->andWhere(['ActionIDRef' => $processEntityModel->LastActionIDRef]);

        if (!empty($processEntityModel->TenderIDRef)) {
            $this->tender($processEntityModel);
        } else if (!empty($processEntityModel->CashRequestIDRef)) {
            $this->cashRequest($processEntityModel);
        } else if (!empty($processEntityModel->WorkflowContractIDRef)) {
            $this->workflowContract($processEntityModel);
        } else if (!empty($processEntityModel->BudgetIDRef)) {
            $this->budget($processEntityModel);
        } else if (!empty($processEntityModel->capitalBalanceIdRef)) {
            $this->capital($processEntityModel);
        } else if (!empty($processEntityModel->InvoiceIDRef)) {
            $this->invoice($processEntityModel);
        } else if (!empty($processEntityModel->ContractClosingDocumentIDRef)) {
            $this->contractClosingDocument($processEntityModel);
        } else if (!empty($processEntityModel->AutomationIDRef)) {
            $this->automation($processEntityModel);
        } else if (!empty($processEntityModel->NetworkIDRef)) {
            $this->network($processEntityModel);
        } else if ($processEntityModel->process->ProccessTypeIDRef == Process::EMPLOYEE_MEMBERSHIP_PROCESS_TYPE) {
            $this->employeeMembership($processEntityModel);
        } else if ($processEntityModel->process->ProccessTypeIDRef == Process::STAMP_DOCUMENT_PROCESS_TYPE) {
            $this->stampDocument($processEntityModel);
        } else if ($processEntityModel->process->ProccessTypeIDRef == Process::REFUND_STAMP_PROCESS_TYPE) {
            $this->stampRefund($processEntityModel);
}

        return $this;
    }

    public function tender($processEntityModel) {
        $tenderModel = $processEntityModel->tender;
        $requestDate = \app\components\Utility::convertTimestampToJalali($tenderModel->requestDate);
        $year = explode('/', $requestDate)[0];
        $annualQuorum = \app\modules\management\models\AnnualQuorum::find()->where(['year' => $year])->one();

        if ($processEntityModel->LastActionIDRef == 912) {
            if ($tenderModel->getTotalRialEquivalent() > 10 * $annualQuorum->quorum) { // معاملات بزرگ
                if ($tenderModel->tenderType == 3 || $tenderModel->tenderType == 4) { // استعلام بها
                    $this->andWhere(['not in', 'ActionID', [937, 938, 913]]);
                } else { // عدم الزام
                    $this->andWhere(['not in', 'ActionID', [937, 938, 951]]);
                }
            } else { // معاملات کوچک و متوسط
                if (empty($tenderModel->tenderCondition)) { // شرایط الزام به عقد قرارداد ندارد
                    $this->andWhere(['not in', 'ActionID', [913, 951, 938]]);
                } else { // // شرایط الزام به عقد قرارداد دارد
                    $this->andWhere(['not in', 'ActionID', [913, 951, 937]]);
                }
            }
        } else if ($processEntityModel->LastActionIDRef == 941) {
            if ($tenderModel->tenderType == 6) { // استعلام بها
                $this->andWhere(['<>', 'ActionID', 942]);
            } else { // عدم الزام
                $this->andWhere(['<>', 'ActionID', 943]);
            }
        } else if ($processEntityModel->LastActionIDRef == 917) {
            if ($tenderModel->tenderType == 4) { // ترک تشریفات
                $this->andWhere(['not in', 'ActionID', [918, 919]]);
            } else if ($tenderModel->tenderType == 5) { // عدم الزام
                $this->andWhere(['not in', 'ActionID', [919, 920]]);
            } else { // مناقصه
                $this->andWhere(['not in', 'ActionID', [918, 920]]);
            }
        } else if ($processEntityModel->LastActionIDRef == 921) {
            if ($tenderModel->tenderType == 3) { // مناقصه محدود
                $this->andWhere(['<>', 'ActionID', 923]);
            } else { // مناقصه عمومی
                $this->andWhere(['<>', 'ActionID', 922]);
            }
        } else if ($processEntityModel->LastActionIDRef == 926) {
            if ($tenderModel->tenderType == 3) { // مناقصه محدود
                if ($tenderModel->AuthorityLevel == 2) { // سطح هلدینگ
                    $this->andWhere(['not in', 'ActionID', [927, 928, 930]]);
                } else { // شرکت/موسسه
                    $this->andWhere(['not in', 'ActionID', [927, 928, 931]]);
                }
            } else { // مناقصه عمومی
                if ($tenderModel->AuthorityLevel == 2) { // سطح هلدینگ
                    if ($tenderModel->tenderType == 1) { // تک مرحله ای
                        $this->andWhere(['=', 'ActionID', 931]);
                    } else if ($tenderModel->tenderType == 2) { // دو مرحله ای
                        $this->andWhere(['=', 'ActionID', 928]);
                    }
                } else { // شرکت/موسسه
                    if ($tenderModel->tenderType == 1) { // تک مرحله ای
                        $this->andWhere(['=', 'ActionID', 930]);
                    } else if ($tenderModel->tenderType == 2) { // دو مرحله ای
                        $this->andWhere(['=', 'ActionID', 927]);
                    }
                }
            }
        } else if ($processEntityModel->LastActionIDRef == 920) {
            if ($tenderModel->AuthorityLevel == 1) { // شرکت/موسسه 
                $this->andWhere(['not in', 'ActionID', [933, 934]]);
            } else if ($tenderModel->AuthorityLevel == 2) { // سطح هلدینگ
                $this->andWhere(['not in', 'ActionID', [934, 944]]);
            } else { // سطح قرب مرکز
                $this->andWhere(['not in', 'ActionID', [933, 944]]);
            }
        } else if ($processEntityModel->LastActionIDRef == 934) {
            if ($tenderModel->AuthorityLevel == 4) { // سطح مدیرعامل
                $this->andWhere(['not in', 'ActionID', [935]]);
            } else { // سطح معاون 
                $this->andWhere(['not in', 'ActionID', [936]]);
            }
        }
    }

    public function cashRequest($processEntityModel) {
        if ($processEntityModel->LastActionIDRef == 405) {
            //درخواست از نوع حساب مشترک با پیمانکار بود
            $existAmount_1 = CashRequestAmount::find()
                            ->where([
                                'CashRequestTypeIDRef' => 3,
                                'CashRequestIDRef' => $processEntityModel->CashRequestIDRef
                            ])->exists();

            //ارز خارجی داشت
            $existAmount_2 = CashRequestAmount::find()
                    ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef])
                    ->andWhere(['<>', 'RequestedCurrencyIDRef', 1])
                    ->exists();

            //درخواست در مورد یک قرارداد بود
            $existAmount_3 = CashRequestAmount::find()
                    ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef, 'CashRequestRecipientTypeIDRef' => 1])
                    ->exists();

            //مسیر تهران حذف شود و مسیر کارگاه باقی بماند
            if ($existAmount_1) {
                $this->andWhere(['<>', 'ActionID', 430]);
            } //مسیر کارگاه حذف شود و مسیر تهران باقی بماند
            else if ($existAmount_2) {
                $this->andWhere(['<>', 'ActionID', 442]);
            } //قراردادهای غیرسایتی
            else if ($existAmount_3) {
                $existCon = Contract::find()->joinWith(['cashRequestAmount'])
                        ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef])
                        ->andWhere(['<>', 'ContractCategory', 12])
                        ->exists();
                if ($existCon) {
                    $this->andWhere(['<>', 'ActionID', 442]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 430]);
                }
            } else {
                $this->andWhere(['<>', 'ActionID', 430]);
            }
        } else if ($processEntityModel->LastActionIDRef == 378) {
            //درخواست از نوع حساب مشترک با پیمانکار بود
            $existAmount_4 = CashRequestAmount::find()
                    ->where([
                        'CashRequestIDRef' => $processEntityModel->CashRequestIDRef,
                        'CashRequestTypeIDRef' => 3
                    ])
                    ->exists();

            //ارز خارجی داشت
            $existAmount_5 = CashRequestAmount::find()
                    ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef])
                    ->andWhere(['<>', 'RequestedCurrencyIDRef', 1])
                    ->exists();

            //درخواست در مورد یک قرارداد بود
            $existAmount_3 = CashRequestAmount::find()
                    ->where(['CashRequestIDRef' => $processEntityModel->CashRequestIDRef, 'CashRequestRecipientTypeIDRef' => 1])
                    ->exists();

            //مسیر تهران حذف شود و مسیر کارگاه باقی بماند
            if ($existAmount_4) {
                $this->andWhere(['<>', 'ActionID', 403]);
            } //مسیر جهرم حذف شود و مسیر تهران باقی بماند
            else if ($existAmount_5) {
                $this->andWhere(['<>', 'ActionID', 507]);
            } //مسیر جهرم حذف شود و مسیر تهران باقی بماند
            else if ($existAmount_3) {
                $existContract = Contract::find()->innerJoin(CashRequestAmount::tableName(), 'RecipientContractIDRef=ContractID')
                        ->where(['cashrequestIDRef' => $processEntityModel->CashRequestIDRef])
                        ->andWhere(['<>', Contract::tableName() . '.ContractCategory', 12])
                        ->exists();
                if ($existContract) {
                    $this->andWhere(['<>', 'ActionID', 507]);
                }
            } else {
                //مسیر تهران حذف شود و مسیر جهرم باقی بماند
                $this->andWhere(['<>', 'ActionID', 403]);
            }
        }
    }

    public function workflowContract($processEntityModel) {
        $wfModel = WorkflowContract::find()
                ->where(['WorkflowContractID' => $processEntityModel->WorkflowContractIDRef])
                ->one();
        // اگر قرارداد در کارگاه ثبت گردد ابتدا به حقوقی ستاد شرکت مربوطه ارجاع می شود
        if ($processEntityModel->LastActionIDRef == 796) {
//            $lastActionEntityModel = $processEntityModel->lastActionEntity;
            $contractPart = $processEntityModel->contract->contractPart;
            if ($contractPart == 3) {   //اگر قرارداد virtual باشد
                $this->andWhere(['not in', 'ActionID', [485, 797, 976]]);
            } else {
                $this->andWhere(['<>', 'ActionID', 1049]);
                $obsId = \Yii::$app->session->get('ObsPosition');
                $obsModel = \app\modules\OBS\models\Obsnew::find()->where(['obsId' => $obsId])->one();
                if ($wfModel->RentalContract == 1) {
                    $this->andWhere(['<>', 'ActionID', 797]);
                    if ($wfModel->ContractCategoryIDRef == 11) {
                        $this->andWhere(['<>', 'ActionID', 976]);
                    } else {
                        $this->andWhere(['<>', 'ActionID', 485]);
                    }
                } else {
                    $this->andWhere(['<>', 'ActionID', 485]);
                    if ($wfModel->ContractCategoryIDRef == 11) {
                        $this->andWhere(['<>', 'ActionID', 976]);
                    } else {
                        $this->andWhere(['<>', 'ActionID', 797]);
                    }
                }
            }
        } else if ($processEntityModel->LastActionIDRef == 976) {
            if ($wfModel->RentalContract == 1) {
                $this->andWhere(['<>', 'ActionID', 797]);
            } else {
                $this->andWhere(['<>', 'ActionID', 485]);
            }
        } else if ($processEntityModel->LastActionIDRef == 797) {
            $actionEntityModel = ActionEntity::find()->where([
                        'ProcessEntityIDRef' => $processEntityModel->ProcessEntityID,
                        'ActionIDRef' => 797
                    ])
                    ->andWhere(['in', 'FromActionIDRef', [796, 976]])
                    ->orderBy('ID desc')
                    ->one();

            if ($actionEntityModel->FromActionIDRef == 796) {
                $this->andWhere(['<>', 'ActionID', 976]);
            } else {
                $this->andWhere(['<>', 'ActionID', 796]);
            }
        } else if ($processEntityModel->LastActionIDRef == 485) {
            /* if (empty($wfModel->SubContractorPersonIDRef)) {
              $actionEntityCount = ActionEntity::find()->where([
              'ProcessEntityIDRef' => $processEntityModel->ProcessEntityID,
              'ActionIDRef' => 491
              ])
              ->count();
              //اگر قبلا رفته بود
              if ($actionEntityCount > 0) {
              $this->andWhere(['<>', 'ActionID', 491]);
              } else {
              $this->andWhere(['<>', 'ActionID', 486]);
              }
              } else {
              $this->andWhere(['<>', 'ActionID', 491]);
              } */
            $subContractorPersonModel = \app\modules\OBS\models\Person::find()
                    ->where(['PersonID' => $wfModel->SubContractorPersonIDRef])
                    ->one();
            if ($subContractorPersonModel->verified) {
                $this->andWhere(['<>', 'ActionID', 491]);
            } else {
                $this->andWhere(['<>', 'ActionID', 486]);
            }
            $contractPart = $processEntityModel->contract->contractPart;
            if ($contractPart == 3) {   //اگر قرارداد virtual باشد
                $this->andWhere(['<>', 'ActionID', 797]);
            } else {
                $this->andWhere(['<>', 'ActionID', 1049]);
                if ($wfModel->RentalContract == 1) {
                    $this->andWhere(['<>', 'ActionID', 797]);
                }
            }
        } else if ($processEntityModel->LastActionIDRef == 596) {
            if (empty($wfModel->SubContractorPersonIDRef)) {
                $actionEntityCount = ActionEntity::find()->where([
                            'ProcessEntityIDRef' => $processEntityModel->ProcessEntityID,
                            'ActionIDRef' => 602
                        ])
                        ->count();
                //اگر قبلا رفته بود
                if ($actionEntityCount > 0) {
                    $this->andWhere(['<>', 'ActionID', 602]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 597]);
                }
            } else {
                $this->andWhere(['<>', 'ActionID', 602]);
            }
        } else if ($processEntityModel->LastActionIDRef == 525) {
            $obsId = \Yii::$app->session->get('ObsPosition');
            $obsModel = \app\modules\OBS\models\Obsnew::find()->where(['obsId' => $obsId])->one();
            $workflowContractModel = WorkflowContract::find()->where(['ContractIDRef' => $wfModel->ContractIDRef, 'RevisionTypeIDRef' => 1])->one();
            if ((isset($workflowContractModel) && $workflowContractModel->RentalContract == 1) || $wfModel->ContractChangePermission == 1) {
                $this->andWhere(['<>', 'ActionID', 867]);
                if ($obsModel->categoryIdRef == 2) {
                    $this->andWhere(['<>', 'ActionID', 977]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 526]);
                }
            } else {
                $this->andWhere(['<>', 'ActionID', 526]);
                if ($obsModel->categoryIdRef == 2) {
                    $this->andWhere(['<>', 'ActionID', 977]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 867]);
                }
            }
        } else if ($processEntityModel->LastActionIDRef == 977) {
            $workflowContractModel = WorkflowContract::find()->where(['ContractIDRef' => $wfModel->ContractIDRef, 'RevisionTypeIDRef' => 1])->one();
            if ((isset($workflowContractModel) && $workflowContractModel->RentalContract == 1) || $wfModel->ContractChangePermission == 1) {
                $this->andWhere(['<>', 'ActionID', 867]);
            } else {
                $this->andWhere(['<>', 'ActionID', 526]);
            }
        } else if ($processEntityModel->LastActionIDRef == 867) {
            $actionEntityModel = ActionEntity::find()->where([
                        'ProcessEntityIDRef' => $processEntityModel->ProcessEntityID,
                        'ActionIDRef' => 867
                    ])
                    ->andWhere(['in', 'FromActionIDRef', [525, 977]])
                    ->orderBy('ID desc')
                    ->one();

            if ($actionEntityModel->FromActionIDRef == 525) {
                $this->andWhere(['<>', 'ActionID', 977]);
            } else {
                $this->andWhere(['<>', 'ActionID', 525]);
            }
        }
    }

    public function budget($processEntityModel) {
        if ($processEntityModel->LastActionIDRef == 475) {
            $budgetId = $processEntityModel->BudgetIDRef;
            //قرارداد سایت بود
            $existsContract = Contract::find()
                    ->select([Contract::tableName() . ".ContractCategory"])
                    ->joinWith(['budget', 'category'])
                    ->where(['budgetId' => $budgetId, Contract::tableName() . '.ContractCategory' => 12])
                    ->exists();

            //ثبت کننده بودجه از سایت بود
            $obsAllChilds = \app\components\CandoHelper::obsChilds(240, 623, 'array');
            $existsAnnual = AnnualBudget::find()
                    ->select(['OBSCategory3CompanyIDRef'])
                    ->where([
                        'AnnualBudgetID' => $processEntityModel->BudgetIDRef,
                        'OBSCategory3CompanyIDRef' => $obsAllChilds,
                    ])
                    ->exists();

            //@@[qryLastContractRevision] 
            if ($existsContract) {
                $this->andWhere(['<>', 'ActionID', 479]);
            } //مسیر تهران حذف شود و مسیر سایت باقی بماند
            else if ($existsAnnual) {
                $this->andWhere(['<>', 'ActionID', 479]);
            } else {
                //مسیر سایت حذف شود و مسیر تهران باقی بماند
                $this->andWhere(['<>', 'ActionID', 478]);
            }
        }
    }

    public function capital($processEntityModel) {
        if ($processEntityModel->LastActionIDRef == 538) {

            $siteObsArray = \app\modules\OBS\models\Obsnew::getObsSiteArray();

            $obid = \app\modules\capital\models\CapitalBalance::find()
                    ->select(['OBSCategory3IDRef'])
                    ->where(['capitalBalanceId' => $processEntityModel->capitalBalanceIdRef])
                    ->one();

            if (in_array($obid->OBSCategory3IDRef, $siteObsArray)) {
                $this->andWhere(['<>', 'ActionID', 540]);
            } else {
                $this->andWhere(['<>', 'ActionID', 573]);
            }
        }
    }

    public function invoice($processEntityModel) {
        if ($processEntityModel->LastActionIDRef == 696) {
            $contractCategoryId = $processEntityModel->invoice->contract->ContractCategory;
            if ($contractCategoryId == 12) {//قرارداد سایت بود
                $this->andWhere(['<>', 'ActionID', 697]);
            } else {
                $this->andWhere(['<>', 'ActionID', 698]);
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [769, 773])) {
            $contractPart = $processEntityModel->invoice->contract->contractPart;
            if ($contractPart == 3) {   //اگر قرارداد virtual باشد
                $this->andWhere(['<>', 'ActionID', 770]);
                $this->andWhere(['<>', 'ActionID', 772]);
            } else {
                $this->andWhere(['<>', 'ActionID', 773]);
                $this->andWhere(['<>', 'ActionID', 769]);
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [782, 786])) {
            $contractPart = $processEntityModel->invoice->contract->contractPart;
            if ($contractPart == 3) {   //اگر قرارداد virtual باشد
                $this->andWhere(['<>', 'ActionID', 783]);
                $this->andWhere(['<>', 'ActionID', 785]);
            } else {
                $this->andWhere(['<>', 'ActionID', 786]);
                $this->andWhere(['<>', 'ActionID', 782]);
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [890])) {
            $contractPart = $processEntityModel->invoice->contract->contractPart;
            if ($contractPart == 3) {   //اگر قرارداد virtual باشد
                $this->andWhere(['<>', 'ActionID', 891]);
            } else {
                $this->andWhere(['<>', 'ActionID', 892]);
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [789])) {
            $this->andWhere(['<>', 'ActionID', 788]);
        } /* else if (in_array($processEntityModel->LastActionIDRef, [775])) {
          $this->andWhere(['<>', 'ActionID', 776]);
          } */
    }

    public function contractClosingDocument($processEntityModel) {
        if ($processEntityModel->LastActionIDRef == 957) {
            $contractDocumentTypeId = $processEntityModel->contractClosingDocument->ContractDocumentTypeIDRef;
            if (in_array($contractDocumentTypeId, [4, 22])) {//معاونت برنامه ریزی و کنترل پروژه هلدینگ
                $this->andWhere(['in', 'ActionID', [958, 959]]);
            }
            if (in_array($contractDocumentTypeId, [8, 14, 15, 16, 20, 23])) {//معاونت مالی و سرمایه گذاری هلدینگ
                $this->andWhere(['in', 'ActionID', [958, 960]]);
            } else if (in_array($contractDocumentTypeId, [11])) {// معاونت حقوقی وقراردادها هلدینگ
                $this->andWhere(['in', 'ActionID', [958, 961]]);
            } else if (in_array($contractDocumentTypeId, [1, 2, 3, 5, 6, 9, 10, 12, 18, 19, 21, 24, 25])) {// معاونت فنی ومهندسی هلدینگ
                $this->andWhere(['in', 'ActionID', [958, 962]]);
            }
        }
    }

    public function automation($processEntityModel) {
        $automationModel = $processEntityModel->automation;
        $positionModel = $automationModel->position;
        if ($processEntityModel->ProcessIDRef == 72) {
            if ($processEntityModel->LastActionIDRef == 966) {
                $disableOldPosition = explode(',', $automationModel->DisableOldPosition);
                $requestDate = \app\modules\workflow\models\ActionEntity::find()->where(["ProcessEntityIDRef" => $processEntityModel->ProcessEntityID])->andWhere(['FromActionIDRef' => null])->one()->ActionDateHijri;
                $humanObs = \app\modules\automation\models\HumanResourceObs::find()->where(['HumanIdRef' => $automationModel->HumanResourceIdRef, 'State' => 'Employment'])->andWhere(["<>", "ObsIdRef", $automationModel->Position])->andWhere(['<', 'LastUpdate', $requestDate])->andWhere("COALESCE(isDeleted,0)<>1");
                if (count($disableOldPosition > 0)) {
                    $humanObs->andWhere(['not in', 'ObsIdRef', $disableOldPosition]);
                }
                if ($positionModel->verified != 1 || $positionModel->status == 'not-approval' || $positionModel->totalPositionCount < 1 || ($positionModel->notApprovalPositionCount >= 1 && $positionModel->status != 'holding-approval') || $humanObs->exists()) {
                    $this->andWhere(['not in', 'ActionID', [968, 978]]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 967]);
                }
                $this->automationHumanResource($automationModel);
            } else if ($processEntityModel->LastActionIDRef == 967) {
                $this->automationHumanResource($automationModel);
            } else if (in_array($processEntityModel->LastActionIDRef, [968, 978])) {
                $this->automationAutomationType($automationModel);
            } else if ($processEntityModel->LastActionIDRef == 972) {
                if (strpos($automationModel->AutomationType, 'MA') !== false) {// اتوماسیون مجمع داشته باشد
                    $this->andWhere(['<>', 'ActionID', 974]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 973]);
                }
            }
        } else if ($processEntityModel->ProcessIDRef == 73) {
            $hsseNotConfirmed = ActionEntity::find()->where(['ProcessEntityIDRef' => $processEntityModel->ProcessEntityID])->andWhere(['FromActionIDRef' => 987])->andWhere(['ActionIDRef' => 988])->andWhere(['OBSCategory3IDRef' => 606]);
            if (in_array($processEntityModel->LastActionIDRef, [985])) {
                if ($automationModel->CompanyIDRef == 248) {
                    $this->andWhere(['not in', 'ActionID', [986]]);
                } else {
                    $this->andWhere(['not in', 'ActionID', [988]]);
                }
            } else if (in_array($processEntityModel->LastActionIDRef, [988])) {
                $secretNotConfirmed = ActionEntity::find()->where(['ProcessEntityIDRef' => $processEntityModel->ProcessEntityID])->andWhere(['FromActionIDRef' => 986, 'ActionIDRef' => 987])->andWhere(['OBSCategory3IDRef' => 606]);
                $human = $automationModel->humanResource;
                if ($automationModel->CompanyIDRef == 248 || $human->EmploymentType == 'official' || $secretNotConfirmed->exists()) {
                    $this->andWhere(['in', 'ActionID', [989, 995]]);
                } else if ($positionModel->categoryIdRef == 2 || $hsseNotConfirmed->exists()) {
                    $this->andWhere(['in', 'ActionID', [991, 995]]);
                } else {
                    $this->andWhere(['not in', 'ActionID', [989, 991]]);
                    $this->automationAutomationType($automationModel);
                }
                /* if ($automationModel->CompanyIDRef == 248) {
                  $this->andWhere(['<>', 'ActionID', 990]);
                  } else {
                  $this->andWhere(['<>', 'ActionID', 989]);
                  } */
            } else if (in_array($processEntityModel->LastActionIDRef, [990])) {
                if ($automationModel->CompanyIDRef == 248 || $positionModel->categoryIdRef == 2 || $hsseNotConfirmed->exists()) {
                    $this->andWhere(['not in', 'ActionID', [992, 993]]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 991]);
                    $this->automationAutomationType($automationModel);
                }
            } else if (in_array($processEntityModel->LastActionIDRef, [991])) {
                $this->automationAutomationType($automationModel);
            } else if ($processEntityModel->LastActionIDRef == 992) {
                if (strpos($automationModel->AutomationType, 'MA') !== false) {// اتوماسیون مجمع داشته باشد
                    $this->andWhere(['<>', 'ActionID', 994]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 993]);
                }
            }
        } else if ($processEntityModel->ProcessIDRef == 86) {
            if ($processEntityModel->LastActionIDRef == 1186) {
                if (strpos($automationModel->AutomationType, 'GH') !== false) {// اتوماسیون قرارگاه داشته باشد
                    $this->andWhere(['<>', 'ActionID', 1188]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 1187]);
                }
            } else if ($processEntityModel->LastActionIDRef == 1187) {
                if (strpos($automationModel->AutomationType, 'MA') !== false) {// اتوماسیون مجمع داشته باشد
                    $this->andWhere(['not in', 'ActionID', [1189, 1190, 1193]]);
                } else if ($automationModel->IsSettlement == 0) {
                    $this->andWhere(['not in', 'ActionID', [1188, 1189, 1190]]);
                } else if ($automationModel->CompanyIDRef == 248) {
                    $this->andWhere(['not in', 'ActionID', [1188, 1189, 1193]]);
                } else {
                    $this->andWhere(['not in', 'ActionID', [1188, 1190, 1193]]);
                }
            } else if ($processEntityModel->LastActionIDRef == 1188) {
                if ($automationModel->IsSettlement == 0) {
                    $this->andWhere(['not in', 'ActionID', [1189, 1190]]);
                } else if ($automationModel->CompanyIDRef == 248) {
                    $this->andWhere(['not in', 'ActionID', [1189, 1193]]);
                } else {
                    $this->andWhere(['not in', 'ActionID', [1190, 1193]]);
                }
            }
        }
    }

    public function automationSequerityLevel($processEntityModel, $automationModel) {
        if (strpos($automationModel->SequerityLevel, 'secret') !== false) { // اتوماسیون محرمانه یا خیلی محرمانه باشد
            $this->andWhere(['not in', 'ActionID', [972, 973, 984]]);
        } else {
            $this->andWhere(['not in', 'ActionID', [969, 970, 983]]);
        }
        $this->automationPositionType($automationModel);
    }

    public function automationPositionType($automationModel) {
        if ($automationModel->positionType->IsManagerial == 1) {// پست مدیریتی باشد
            $this->andWhere(['not in', 'ActionID', [968, 972, 973]]);
        } else {
            $this->andWhere(['not in', 'ActionID', [984, 981]]);
        }
        $this->automationAutomationType($automationModel);
    }

    public function automationAutomationType($automationModel) {
        if (strpos($automationModel->AutomationType, 'GH') !== false) {// اتوماسیون قرارگاه داشته باشد
            $this->andWhere(['not in', 'ActionID', [973, 993]]);
        } else {
            $this->andWhere(['not in', 'ActionID', [972, 992]]);
        }
    }

    public function automationHumanResource($automationModel) {
        if ($automationModel->humanResource->Verified == 1) {
            $this->andWhere(['<>', 'ActionID', 968]);
        } else {
            $this->andWhere(['<>', 'ActionID', 978]);
        }
    }

    public function network($processEntityModel) {
        $networkModel = $processEntityModel->network;
        if (in_array($processEntityModel->LastActionIDRef, [999])) {
            if ($networkModel->CompanyIDRef == 248) {
                $this->andWhere(['<>', 'ActionID', 997]);
            } else {
                $this->andWhere(['<>', 'ActionID', 1000]);
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [997])) {
            $obsId = Yii::$app->session->get('ObsPosition');
            $human = $networkModel->humanResource;
            if ($human->EmploymentType == 'official' || $obsId == 606) {
                $this->andWhere(['not in', 'ActionID', [1005, 1007]]);
            } else {
                $this->andWhere(['<>', 'ActionID', 1000]);
                if (strpos($networkModel->NetworkType, 'cando') !== false) {
                    $this->andWhere(['<>', 'ActionID', 1007]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 1005]);
                }
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [1001])) {
            if (strpos($networkModel->NetworkType, 'cando') !== false) {
                $this->andWhere(['not in', 'ActionID', [1002, 1007]]);
            } else {
                $this->andWhere(['<>', 'ActionID', 1005]);
                if ($networkModel->CompanyIDRef == 248) {
                    $this->andWhere(['<>', 'ActionID', 1007]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 1002]);
                }
            }
        } else if (in_array($processEntityModel->LastActionIDRef, [1005])) {
            $networkTypeArray = explode(',', $networkModel->NetworkType);
            if (count($networkTypeArray) > 1) {
                $this->andWhere(['<>', 'ActionID', 1006]);
                if ($networkModel->CompanyIDRef == 248) {
                    $this->andWhere(['<>', 'ActionID', 1007]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 1002]);
                }
            } else {
                $this->andWhere(['not in', 'ActionID', [1002, 1007]]);
            }
        }
    }

    public function employeeMembership($processEntityModel) {

        $employeeMembershipModel = $processEntityModel->employeeMembership;
        if ($processEntityModel->ProcessIDRef == 77) {
            if ($processEntityModel->LastActionIDRef == 1075) {
                if ($employeeMembershipModel->CompanyIDRef != 248 && $employeeMembershipModel->IncludeSalaryIn == 1) {
                    $this->andWhere(['<>', 'ActionID', 1079]);
                } else {
                    $this->andWhere(['<>', 'ActionID', 1077]);
                }

                $this->employeeMembershipHumanResourceType($employeeMembershipModel);
            }
        }
    }

    public function employeeMembershipHumanResourceType($employeeMembershipModel) { // سرباز باشد یا قراردادی/رسمی
        if ($employeeMembershipModel->humanResource->EmploymentType == 'soldier') {
            $this->andWhere(['not in', 'ActionID', [1077, 1078, 1079]]);
        } else {
            $this->andWhere(['<>', 'ActionID', 1076]);
        }
    }

    public function stampDocument($processEntityModel) {
        $stampModel = $processEntityModel->getStampDocument()->one();
        if (!$stampModel) {
            return $this;
        }

        Yii::info("ActionOrderQuery - ProcessEntityID: " . $processEntityModel->ProcessEntityID, 'stamp_workflow');
        Yii::info("ActionOrderQuery - LastActionIDRef: " . $processEntityModel->LastActionIDRef, 'stamp_workflow');
        Yii::info("ActionOrderQuery - OrganizationalMaker: " . ($stampModel->OrganizationalMaker ?? 2), 'stamp_workflow');

        $isPerson = ($stampModel->StampTypeIDRef == 4);
        $currentAction = $processEntityModel->LastActionIDRef;
        $organizationalMaker = $stampModel->OrganizationalMaker ?? 2;

        // ========== بررسی مدیریتی بودن پست سازمانی نشان ==========
        $stampObs = $stampModel->obs;
        $isManagerial = false;
        if ($stampObs && $stampObs->positionTypeIdRef) {
            $positionType = \app\modules\automation\models\PositionType::find()
                    ->where(['PositionTypeID' => $stampObs->positionTypeIdRef])
                    ->select(['IsManagerial'])
                    ->scalar();
            $isManagerial = ($positionType == 1);
        }

        // ========== فیلتر مسیرها بر اساس سناریو و مرحله ==========
        // سناریوی «وجود دارد» (OrganizationalMaker = 1)
        if ($organizationalMaker == 1) {

            if ($currentAction == 1172) {
                // درخواست کننده → تاییدکننده اول
                $this->andWhere(['NextActionIDRef' => [1173, 1183]]);
            } elseif ($currentAction == 1173) {
                // تاییدکننده اول → تاییدکننده دوم
                $this->andWhere(['NextActionIDRef' => [1179, 1172, 1183]]);
            
            } elseif ($currentAction == 1179) {
                // ========== پیدا کردن مرحله قبلی (FromActionIDRef) ==========
                $previousAction = ActionEntity::find()
                        ->select(['FromActionIDRef'])
                        ->where([
                            'ProcessEntityIDRef' => $processEntityModel->ProcessEntityID,
                            'ActionIDRef' => 1179
                        ])
                        ->orderBy(['ID' => SORT_DESC])
                        ->one();

                $fromAction = $previousAction ? $previousAction->FromActionIDRef : null;

                // اگر از 1179 آمده باشد
                if ($fromAction == 1173) {
                    if ($isPerson) {
                        // اشخاص: فقط منابع انسانی (1175) و رد (1183)
                        $this->andWhere(['NextActionIDRef' => [1175, 1183]]);
                    } else {
                        // غیر اشخاص: رده (1177) - رده بالادستی (1181) - برگشت (1172) - رد (1183)
                        $this->andWhere(['NextActionIDRef' => [1177, 1181, 1172, 1183]]);
                    }
                }
                // اگر از 1175 یا 1176 آمده باشد
                elseif ($fromAction == 1175 || $fromAction == 1176) {
                    // رده (1177) - رده بالادستی (1181) - رد (1183)
                    $this->andWhere(['NextActionIDRef' => [1177, 1181, 1183]]);
                }
                // اگر مرحله قبلی مشخص نیست (پیش‌فرض)
                else {
                    if ($isPerson) {
                        $this->andWhere(['NextActionIDRef' => [1175, 1183]]);
                    } else {
                        $this->andWhere(['NextActionIDRef' => [1177, 1181, 1172, 1183]]);
                    }
                }
                // ========== پایان 1179 ==========
            } elseif ($currentAction == 1175) {
                // منابع انسانی
                if ($isManagerial) {
                    // مدیریتی → بازرسی
                    $this->andWhere(['NextActionIDRef' => [1176, 1183]]);
                } else {
                    // غیرمدیریتی → برگشت به تایید سیستمی اول
                    $this->andWhere(['NextActionIDRef' => [1179, 1183]]);
                }
            } elseif ($currentAction == 1176) {
                // بازرسی → برگشت به تایید سیستمی اول
                $this->andWhere(['NextActionIDRef' => [1179, 1183]]);
            } elseif ($currentAction == 1177) {
                // ساخت رده → تحویل رده
                $this->andWhere(['NextActionIDRef' => [1178, 1183]]);
            } elseif ($currentAction == 1178) {
                // تحویل رده → تایید نهایی
                $this->andWhere(['NextActionIDRef' => [1184, 1183]]);
            } elseif ($currentAction == 1181) {
                // ساخت رده بالادستی → تحویل رده
                $this->andWhere(['NextActionIDRef' => [1178, 1183]]);
            }
        }
        // سناریوی «وجود ندارد» (OrganizationalMaker = 2)
        else {

            if ($currentAction == 1172) {
                $this->andWhere(['NextActionIDRef' => [1173, 1183]]);
            } elseif ($currentAction == 1173) {
                $this->andWhere(['NextActionIDRef' => [1174, 1172, 1183]]);
            } elseif ($currentAction == 1174) {
                if ($isPerson) {
                    // اشخاص → 1175 (منابع انسانی)
                    $this->andWhere(['NextActionIDRef' => [1175, 1172, 1183]]);
                } else {
                    // غیر اشخاص → 1177 (ساخت رده)
                    $this->andWhere(['NextActionIDRef' => [1177, 1172, 1183]]);
                }
            } elseif ($currentAction == 1175) {
                if ($isManagerial) {
                    // مدیریتی → بازرسی
                    $this->andWhere(['NextActionIDRef' => [1176, 1183]]);
                } else {
                    // غیرمدیریتی → ساخت رده
                    $this->andWhere(['NextActionIDRef' => [1177, 1183]]);
                }
            } elseif ($currentAction == 1176) {
                // بازرسی → ساخت رده
                $this->andWhere(['NextActionIDRef' => [1177, 1183]]);
            } elseif ($currentAction == 1177) {
                // ساخت رده → تحویل رده
                $this->andWhere(['NextActionIDRef' => [1178, 1183]]);
            } elseif ($currentAction == 1178) {
                // تحویل رده → تایید نهایی
                $this->andWhere(['NextActionIDRef' => [1184, 1183]]);
            }
        }

        return $this;
    }
    /**
 * فیلتر مسیرهای مجاز برای فرایند امحا نشان سازمانی (ProcessType = 20)
 * مسیرهای تعریف‌شده در action_order:
 * 1195 → 1196
 * 1196 → 1197 | 1202
 * 1197 → 1198 | 1200 | 1202
 * 1198 → 1199 | 1202
 * 1199 → 1201 | 1202
 * 1200 → 1199 | 1202
 */
    public function stampRefund($processEntityModel)
    {
        $stampModel = $processEntityModel->getStampDocument()->one();
        if (!$stampModel) {
            return $this;
        }

        $currentAction = (int) $processEntityModel->LastActionIDRef;
        $licenseIssueType = (int) ($stampModel->LicenseIssueType ?? 0);

        // مسیرهای پایه از action_order (همه مسیرهای تعریف‌شده)
        // فقط در صورت نیاز فیلتر اضافه می‌کنیم

        switch ($currentAction) {

            case 1195: // ثبت درخواست → فقط بررسی مدیر
                $this->andWhere(['NextActionIDRef' => [1196]]);
                break;

            case 1196: // بررسی مدیر → بررسی مدیرعامل / رد
                $this->andWhere(['NextActionIDRef' => [1197, 1202]]);
                break;

            case 1197: // بررسی مدیرعامل / معاونت برنامه‌ریزی
                if ($licenseIssueType === 1) {
                    // نوع صدور مجوز 1: فقط اقدامات تأمینی + رد درخواست
                    $this->andWhere(['NextActionIDRef' => [1198, 1202]]);
                } elseif ($licenseIssueType === 2) {
                    // نوع صدور مجوز 2: فقط برنامه‌ریزی رده + رد درخواست
                    $this->andWhere(['NextActionIDRef' => [1200, 1202]]);
                } else {
                    // نوع نامعتبر
                    $this->andWhere(['NextActionIDRef' => [1202]]);
                    // یا اگر اصلاً چیزی نمایش ندهد:
                    // $this->andWhere('0=1');
                }
                break;

            case 1198: // بررسی اقدامات تامینی هلدینگ → امحا یا رد
                $this->andWhere(['NextActionIDRef' => [1199, 1202]]);
                break;

            case 1199: // امحا نشان سازمانی → تایید نهایی یا رد
                $this->andWhere(['NextActionIDRef' => [1201, 1202]]);
                break;

            case 1200: // بررسی برنامه‌ریزی رده → امحا یا رد
                $this->andWhere(['NextActionIDRef' => [1199, 1202]]);
                break;

            case 1201: // تایید و ثبت نهایی (پایان)
            case 1202: // رد درخواست (پایان)
                // مرحله پایانی – معمولاً مسیر بعدی ندارد
                $this->andWhere('0=1');
                break;

            default:
                // اگر مرحله ناشناخته بود، هیچ مسیری نشان نده
                $this->andWhere('0=1');
                break;
        }

        return $this;
    }

}
