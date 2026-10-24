<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblActionParam".
 *
 * @property integer $paramId
 * @property string $paramTitle
 * @property string $value1
 * @property string $value2
 * @property string $value3
 * @property string $value4
 * @property string $description
 * @property integer $actionIdRef
 */

 /**
 * @see ActionParam
 * 
 * 
 * @author <mail:mortezakml@gmail.com & Tel:09366263397>
 */

class ActionParam extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_param';
        } else {
            return 'WorkFlow.tblActionParam';
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
            [['actionIdRef'], 'required'],
            [['actionIdRef'], 'integer'],
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
            'actionIdRef' => 'Action Id Ref',
        ];
    }

    /**
     * @inheritdoc
     * @return ActionParamQuery the active query used by this AR class.
     */
    public static function find() {
        return new ActionParamQuery(get_called_class());
    }

}
