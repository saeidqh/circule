<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblActionOrderParam".
 *
 * @property integer $paramId
 * @property string $paramTitle
 * @property string $value1
 * @property string $value2
 * @property string $value3
 * @property string $value4
 * @property string $description
 * @property integer $actionOrderIdRef
 */

 /**
 * @see ActionOrderParam
 * 
 */

class ActionOrderParam extends \yii\db\ActiveRecord { 

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_order_param';
        } else {
            return 'WorkFlow.tblActionOrderParam';
        }
    }

    public static function primaryKey() {
        return ['paramId'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['paramTitle', 'value1', 'value2', 'value3', 'value4', 'description'], 'string'],
            [['actionOrderIdRef'], 'required'],
            [['actionOrderIdRef'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'paramId' => 'Param ID',
            'paramTitle' => 'Param Title',
            'value1' => 'Value1',
            'value2' => 'Value2',
            'value3' => 'Value3',
            'value4' => 'Value4',
            'description' => 'Description',
            'actionOrderIdRef' => 'Action Order Id Ref',
        ];
    }
    
    public function sendLog($id, $type) {
        $actionOrderParamTable = \app\modules\workflow\models\ActionOrderParam::tableName();
        $activityManagement = new \app\components\ActivityManagement();
        $actionType = $type;
        $subType = $type . "ActionOrderParam";
        $subTypeDescription["TableName"] = $actionOrderParamTable;
        $subTypeDescription["RecordPrimaryKey"] = $id;
        $activityManagement->sendActitvityLog($actionType, $subType, $subTypeDescription);
    }

}
