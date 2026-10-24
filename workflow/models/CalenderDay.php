<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "calender.tblCalenderDay".
 *
 * @property integer $calenderIdRef
 * @property string $pDate
 * @property integer $workTimeInMinute
 * @property string $description1
 * @property string $description2
 * @property string $description3
 */
class CalenderDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'cando_table_calender_day';
        } else {
            return 'calender.tblCalenderDay';
        }
    }
	
    public static function primaryKey() {
        return ['id'];
    }

  
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['calenderIdRef', 'pDate'], 'required'],
            [['calenderIdRef', 'workTimeInMinute'], 'integer'],
            [['pDate', 'description1', 'description2', 'description3'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'calenderIdRef' => 'Calender Id Ref',
            'pDate' => 'P Date',
            'workTimeInMinute' => 'Work Time In Minute',
            'description1' => 'Description1',
            'description2' => 'Description2',
            'description3' => 'Description3',
        ];
    }

    /**
     * @inheritdoc
     * @return CalenderDayQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CalenderDayQuery(get_called_class());
    }
}
