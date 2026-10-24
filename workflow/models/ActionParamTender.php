<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamTender extends \yii\base\Model {

    public $ACCESS_TYPES = [
        1 => 'Hide',
        2 => 'Show Only',
        3 => 'Edit',
    ];
    public $AMOUNT_STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    public $tenderCallingDocument;
    public $tenderInfo;
    public $tenderWbs;
    public $tenderDocument;
    public $tenderActivities;
    public $tenderContractStrategy;
    public $tenderFinancing;
    public $tenderBidders;
    public $tenderIndexPrice;
    public $tenderCommission;
    public $amountStatus;
    public $showObsChilds;
    /*public $initialEstimateDocuments; //اسناد براورد اولیه
    public $tenderDocuments; //اسناد مناقصه
    public $serviceAndSpecification;  //شرح خدمات و مشخصات
    public $outsourcingStrategy; //استراتژی برون سپاری
    public $justificationPlan; //طرح توجیهی
    public $leavingFormalitiesReport; //گزارش توجیهی ترک تشریفات
    public $limitedTenderReport; //گزارش توجیهی برگزاری مناقصه محدود
    public $tenderNoRequirementReason; //ارائه دلایل عدم الزام به برگزاری مناقصه
    public $financingAndAllocationcredi; //مدارک مربوط به تامین مالی و اعتبار تخصیص
    public $governmentapproval; //مصوبه دولت یا تصمیم نامه هیات دولت
    public $publicTenderAnnouncement; //گهی مناقصه عمومی منتشر شده
    public $applicantSuggestions; //پیشنهادات یگانه متقاضی
    public $restrictionResonExpert; //گزارش کارشناسی مبتنی بر ارائه دلایل انحصار
    public $judicialExporsOpinion; //نظریه هیات کارشناسان رسمی دادگستری
    public $connoisseurExporsOpinion; //نظریه هیات سه نفره کارشناسان خبره
    */
    
    public function rules() {
        return [
            [['tenderCallingDocument', 'tenderInfo', 'tenderWbs', 'tenderDocument', 'tenderActivities', 'tenderContractStrategy', 'tenderFinancing', 'tenderBidders', 'tenderIndexPrice', 'tenderCommission', 'amountStatus', 'showObsChilds'], 'integer'],
            [['tenderCallingDocument', 'tenderInfo', 'tenderWbs', 'tenderDocument', 'tenderActivities', 'tenderContractStrategy', 'tenderFinancing', 'tenderBidders', 'tenderIndexPrice', 'tenderCommission', 'amountStatus'], 'required'],
        ];
    }

    public function find($actionId) {
        //@TODO: hasChange and isDelete 
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])
                        ->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'tenderAccessId') {
                $val = str_split($value['value1']);
                $this->tenderCallingDocument = $val[9];
                $this->tenderInfo = $val[8];
                $this->tenderWbs = $val[7];
                $this->tenderDocument = $val[6];
                $this->tenderActivities = $val[5];
                $this->tenderContractStrategy = $val[4];
                $this->tenderFinancing = $val[3];
                $this->tenderBidders = $val[2];
                $this->tenderIndexPrice = $val[1];
                $this->tenderCommission = $val[0];
            } else if ($value['paramTitle'] == 'AmountStatus') {
                $this->amountStatus = $value['value1'];
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            }
        }
    }

    public function save($actionId) {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'tenderAccessId',
            'value1' => $this->tenderCommission . $this->tenderIndexPrice . $this->tenderBidders . $this->tenderFinancing . $this->tenderContractStrategy . $this->tenderActivities . $this->tenderDocument . $this->tenderWbs . $this->tenderInfo . $this->tenderCallingDocument,
            'value2' => null,
            'description' => 'tender',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'AmountStatus',
            'value1' => $this->amountStatus,
            'value2' => null,
            'description' => 'tender',
            'actionIdRef' => $actionId,
        ];
        $rows[count($rows)] = [
            'paramTitle' => 'showObsChilds',
            'value1' => 'tender',
            'value2' => $this->showObsChilds,
            'description' => 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند',
            'actionIdRef' => $actionId,
        ];
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        //$transaction = Yii::$app->dbTehran->beginTransaction();
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->andWhere(($row['paramTitle'] != 'tenderAccessId' && $row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                    ->exists();

            if ($recordExists) { // update exists record
                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                        ->andWhere(['paramTitle' => $row['paramTitle']])
                        ->andWhere(($row['paramTitle'] != 'tenderAccessId' && $row['paramTitle'] != 'AmountStatus') ? ['value1' => $row['value1']] : '1=1')
                        ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    //$transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId . ", ";
                } else {
                    //$transaction->rollback();
                    return false;
                }
            } else { // create new record
                $actionParamModel = new ActionParam();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;
                if ($actionParamModel->save()) {
                    //$transaction->commit();
                    $actionParamCreateIds .= $actionParamModel->paramId . ", ";
                } else {
                    //$transaction->rollback();
                    return false;
                }
            }
        }
        $activityManagement = new \app\components\ActivityManagement();
        $activityManagement->sendActionParamCreateActitvityLog($actionParamCreateIds);

        $activityManagement1 = new \app\components\ActivityManagement();
        $activityManagement1->sendActionParamUpdateActitvityLog($actionParamUpdateIds);

        return true;
    }

}
