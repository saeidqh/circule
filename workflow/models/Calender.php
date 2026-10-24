<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "calender.tblCalender".
 *
 * @property integer $calenderId
 * @property string $calenderTitle
 * @property integer $OBSCategory3CompanyIDRef
 */
class Calender extends \yii\db\ActiveRecord {

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'cando_table_calender';
        } else {
            return 'calender.tblCalender';
        }
    }

    public static function primaryKey() {
        return ['calenderId'];
    }

    public function rules() {
        return [
            [['calenderTitle'], 'string'],
            [['OBSCategory3CompanyIDRef'], 'required'],
            [['OBSCategory3CompanyIDRef'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'calenderId' => 'Calender ID',
            'calenderTitle' => 'Calender Title',
            'OBSCategory3CompanyIDRef' => 'Obscategory3 Company Idref',
        ];
    }

    public static function find() {
        return new CalenderQuery(get_called_class());
    }

}
