<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblActionOrder".
 *
 * @property integer $MeasureID
 * @property string $MeasureTitle
 */
class Measure extends \yii\db\ActiveRecord {

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_measure';
        } else {
            return 'WorkFlow.tblMeasure';
        }
    }

    public static function primaryKey() {
        return ['MeasureID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['MeasureID', 'MeasureTitle'], 'required'],
            [['MeasureID'], 'integer'],
            [['MeasureTitle'], 'string'],
        ];
    }
    
    public function scenarios() {
        $scnario = parent::scenarios();
        $scnario['create'] = ['MeasureID', 'MeasureTitle'];
        $scnario['update'] = ['MeasureID', 'MeasureTitle'];

        return $scnario;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'MeasureID' => Yii::t('workflow', 'ID'),
            'MeasureTitle' => Yii::t('workflow', 'Measure Title'),
        ];
    }

}
