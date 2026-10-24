<?php

namespace app\modules\workflow\models;

use Yii;

class ProcessQuery extends \yii\db\ActiveQuery {

    /*public function newProcessEntity($type) {
        //برگرداننده لیست پروسس ها در زمان ثبت پروسس انتیتی جدید
        if ($type == 'cashRequest') {
            $this->andWhere(['ProccessTypeIDRef' => 2])->andWhere(['not in', 'ProcessID', [14,16, 17, 18]]);
        } elseif ($type == 'capital') {
            $this->andWhere('ProccessTypeIDRef=5')
                    ->andWhere('parentProcessIdRef is null or parentProcessIdRef=0');
        } else {
            $this->andWhere('1=0');
        }
        return $this;
    }*/

    public function newProcessEntity($type) {
        //برگرداننده لیست پروسس ها در زمان ثبت پروسس انتیتی جدید
        if ($type == 'cashRequest') {
            if (\Yii::$app->session->get('CompanyID') == 240) {
                $this->andWhere(['ProccessTypeIDRef' => 2])->andWhere(['not in', 'ProcessID', [14]]);
            } else {
                $this->andWhere(['ProccessTypeIDRef' => 2])->andWhere(['not in', 'ProcessID', [14, 19]]);
            }
        } elseif ($type == 'capital') {
            $this->andWhere('ProccessTypeIDRef=5')
                    ->andWhere('parentProcessIdRef is null or parentProcessIdRef=0');
        } elseif ($type == 'tender') {
            $this->andWhere('ProccessTypeIDRef=12')
                    ->andWhere('parentProcessIdRef is null or parentProcessIdRef=0');
        } elseif ($type == 'automation') {
            $this->andWhere('ProccessTypeIDRef=14')
                    ->andWhere('parentProcessIdRef is null or parentProcessIdRef=0');
        } elseif ($type == 'employee-settlement') {
            $this->andWhere('ProccessTypeIDRef=16')
                    ->andWhere('parentProcessIdRef is null or parentProcessIdRef=0');
        } else {
            $this->andWhere('1=0');
        }
        return $this;
    }

    public function processList() {

        $processTable = \app\modules\workflow\models\Process::tableName();
        $this->andWhere("$processTable.parentProcessIdRef is null");
        if (!\app\access\AccessControl::isAccess(['obs', 'check' => 'isCandoAdmin', 'errorException' => false])) {
            $this->andWhere("$processTable.OBSCategory3CompanyIDRef is null or OBSCategory3CompanyIDRef=" . Yii::$app->session->get('CompanyID'));
        }
        return $this;
    }

    //لیست فرایند های قابل ویرایش
    public function processUpdate() {
        $processTable = \app\modules\workflow\models\Process::tableName();
        $this->andWhere("$processTable.parentProcessIdRef is null");
        if (!\app\access\AccessControl::isAccess(['obs', 'check' => 'isCandoAdmin', 'errorException' => false])) {
            $this->andWhere("OBSCategory3CompanyIDRef=" . Yii::$app->session->get('CompanyID'));
        }
        return $this;
    }

    public function budget() {
        //برگرداننده فرایند برنامه و بودجه مربوط به هر شرکت
        $companyId = Yii::$app->session->get('CompanyID');
        $this->andWhere('ProcessID=22');
        return $this;
    }

    public function debitmemo2($signerId, $mainOwnerId) {
//        ورک فلوی شماره ی 60-61 جمع شد 
//        و دیگر مورد استفاده قرار نمی گیرد

        //برگرداننده فرایند اعلام بدهی
        if ($mainOwnerId == 240) {
            $this->andWhere('ProcessID=55');
        } elseif ($mainOwnerId == 241) {
            $this->andWhere('ProcessID=56');
//        } elseif ($signerId == 139 && $mainOwnerId == 240) {
//            $this->andWhere('ProcessID=60');
//        } elseif ($signerId == 139 && $mainOwnerId == 241) {
//            $this->andWhere('ProcessID=61');
        } else {
            $this->andWhere('1=0');
        }
        return $this;
    }
    
    public function debitmemo() {
        $this->andWhere('ProcessID=55');
        return $this;
    }

    public function workflowContract() {
        //برگرداننده فرایندهای صورت وضعیت برای تخصیص در فرایند قرارداد ها
        $companyId = Yii::$app->session->get('CompanyID');
        $this->andWhere(['ProccessTypeIDRef' => '1']);
//        process 34 is removed
        $this->andWhere(["<>", "ProcessID", 34]);
        if (\app\components\Security::isCandoAdmin()) {
            return $this;
        } else {
            return $this->andWhere(['or',
                ['OBSCategory3CompanyIDRef' => $companyId],
                ['is','OBSCategory3CompanyIDRef', NULL]
            ]);
        }
    }
    

    public function all($db = null) {
        return parent::all($db);
    }

    public function one($db = null) {
        return parent::one($db);
    }

}
