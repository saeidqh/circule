<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "calender.tblDay".
 *
 * @property integer $ID
 * @property string $cDate
 * @property integer $pDate
 */
class Day extends \yii\db\ActiveRecord {

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'cando_table_day';
        } else {
            return 'calender.tblDay';
        }
    }

    public static function primaryKey() {
        return ['ID'];
    }

    public function rules() {
        return [
            [['pDate'], 'string'],
            [['ID','cDate','pDate'], 'required'],
            [['ID'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'ID' => 'ID',
            'cDate' => 'cDate',
            'pDate' => 'pDate',
        ];
    }

    
}
