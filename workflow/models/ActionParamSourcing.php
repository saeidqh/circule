<?php

namespace app\modules\workflow\models;

use Yii;

class ActionParamSourcing extends \yii\base\Model
{

    public $ACCESS_TYPES = [
        1 => 'Hide',
        2 => 'Show Only',
        3 => 'Edit',
    ];
    public $SOURCING_STATUS_TYPES = [
        5 => 'Approved',
        1 => 'Checking',
        0 => 'Held Or Reject',
    ];
    // public $sourcing_insertPerson;
    // public $sourcing_canEditApprove;
    // public $showObsChilds;
    public $verify;
    public $showObsChilds;

    public function rules()
    {
        return [
            [['verify', 'showObsChilds'], 'integer'],
            [['verify'], 'required'],
        ];
    }

    public function find($actionId)
    {
        //@TODO: hasChange and isDelete 
        $actionParamModel = ActionParam::find()->where(['actionIdRef' => $actionId])
            ->asArray()->all();
        foreach ($actionParamModel as $key => $value) {
            if ($value['paramTitle'] == 'verify') {
                $this->verify = $value['value1'];

                // }else if ($value['paramTitle']== 'sourcing_canEditApprove'){
                //     $this->sourcing_canEditApprove =$value['value1'];
                // }else if ($value['paramTitle']== 'sourcing_insertPerson'){
                //     $this->sourcing_insertPerson =$value['value1'];
            } else if ($value['paramTitle'] == 'showObsChilds') {
                $this->showObsChilds = $value['value2'];
            }
        }
    }

    public function save($actionId)
    {
        $rows = [];
        $rows[count($rows)] = [
            'paramTitle' => 'verify',
            'value1' => $this->verify,
            'description' => 'sourcing',
            'actionIdRef' => $actionId,
        ];
        // $rows[count($rows)] = [
        //     'paramTitle'=>'sourcing_insertPerson',
        //     'value1'=> $this->sourcing_insertPerson,
        //     'description'=> 'sourcing', 
        //     'actionIdRef'=> $actionId, 
        // ];
        // $rows[count($rows)] = [
        //     'paramTitle'=>'sourcing_canEditApprove',
        //     'value1'=> $this->sourcing_canEditApprove,
        //     'description'=> 'sourcing', 
        //     'actionIdRef'=> $actionId, 
        // ];
         $rows[count($rows)] = [
             'paramTitle'=> 'showObsChilds',
             'value1'=>'sourcing',
             'value2'=> $this->showObsChilds,
             'description'=> 'به جهت فراهم کردن امکان دسترسی به اکشن ها که فقط مدیر همان واحد بتواند کارهای ارسالی واحد خود را ببیند',
             'actionIdRef'=> $actionId,
         ];
//$transaction = Yii::$app->dbTehran->beginTransaction();
        $actionParamCreateIds = "";
        $actionParamUpdateIds = "";
        foreach ($rows as $row) {
            $recordExists = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                ->andWhere(['paramTitle' => $row['paramTitle']])
                ->exists();

            if ($recordExists) {

                $actionParamModel = ActionParam::find()->where(['actionIdRef' => $row['actionIdRef']])
                    ->andWhere(['paramTitle' => $row['paramTitle']])
                    ->one();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;

                if ($actionParamModel->save()) {
//                     $transaction->commit();
                    $actionParamUpdateIds .= $actionParamModel->paramId . ", ";
                } else {
//                    $transaction->rollback();
                    return false;
                }
            } else { // create new record
                $actionParamModel = new ActionParam();
                $actionParamModel->attributes = $row;
                $actionParamModel->hasChange = 1;

                if ($actionParamModel->save()) {
//                     $transaction->commit();
                    $actionParamCreateIds .= $actionParamModel->paramId . ", ";
                } else {
//                    $transaction->rollback();
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
