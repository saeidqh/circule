<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblActionOrderMeasureValue".
 *
 * @property integer $ID
 * @property integer $ActionOrderMeasureIDRef
 * @property integer $ActionEntityIDRef
 * @property integer $value
 * @property string $description
 */
class ActionOrderMeasureValue extends \yii\db\ActiveRecord {

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_order_measure_value';
        } else {
            return 'WorkFlow.tblActionOrderMeasureValue';
        }
    }

    public static function primaryKey() {
        return ['ID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['ActionOrderMeasureIDRef', 'ActionEntityIDRef', 'value'], 'required'],
            [['ActionOrderMeasureIDRef', 'ActionEntityIDRef'], 'integer'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ActionOrderMeasureID' => 'ID',
            'ActionIDRef' => 'Action IDRef',
            'MeasureIDRef' => 'Measure IDRef',
        ];
    }

    public static function find() {
        return new ActionOrderQuery(get_called_class());
    }
    
    public function getVlueTypeList() {
        return [
            '1' => 'موافقم',
            '2' => 'موافق نیستم'
        ];
    }

    public function getValueType($id) {
        switch ($id) {
            case '1':
                return 'موافقم';
            case '2':
                return 'موافق نیستم';
            default:
                return 'Invalid Receipt Type';
        }
    }

    public function getActionOrderMeasure() {
        return $this->hasOne(\app\modules\workflow\models\ActionOrderMeasure::className(), ['ActionOrderMeasureID' => 'ActionOrderMeasureIDRef']);
    }

}
