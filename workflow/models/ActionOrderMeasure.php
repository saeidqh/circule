<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblActionOrderMeasure".
 *
 * @property integer $ActionOrderMeasureID
 * @property integer $ActionOrderIDRef
 * @property integer $MeasureIDRef
 * @property integer $isDeleted
 */
class ActionOrderMeasure extends \yii\db\ActiveRecord {

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_order_measure';
        } else {
            return 'WorkFlow.tblActionOrderMeasure';
        }
    }

    public static function primaryKey() {
        return ['ActionOrderMeasureID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['ActionOrderIDRef', 'MeasureIDRef'], 'required'],
            [['ActionOrderIDRef', 'MeasureIDRef', 'isDeleted'], 'integer'],
//            [['MeasureIDRef'], 'unique', 'targetAttribute' => ['ActionOrderIDRef', 'MeasureIDRef'], 'message' => 'Duplicate Measure!'],
            [['MeasureIDRef'], function ($attribute) {
                $personModel = ActionOrderMeasure::find()
                    ->where(["MeasureIDRef" => $this->MeasureIDRef, "ActionOrderIDRef" => $this->ActionOrderIDRef])
                    ->andWhere("COALESCE(isDeleted,0)<>1");
                if ($this->isNewRecord) {
                    $count = $personModel->count();
                } else {
                    $count = $personModel->andWhere(['!=', 'ActionOrderMeasureID', $this->ActionOrderMeasureID])->count();
                }
                if ($count > 0) {
                    $this->addError('MeasureIDRef', 'Duplicate Measure!!');
                }
            }]
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

    public function getActionOrder() {
        return $this->hasOne(\app\modules\workflow\models\ActionOrder::className(), ['ID' => 'ActionOrderIDRef']);
    }

    public function getMeasure() {
        return $this->hasOne(\app\modules\workflow\models\Measure::className(), ['MeasureID' => 'MeasureIDRef']);
    }

}
