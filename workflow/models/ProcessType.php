<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblProccessType".
 *
 * @property integer $proccessTypeID
 * @property string $proccessTypeTitle
 */
class ProcessType extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_process_type';
        } else {
            return 'WorkFlow.tblProccessType';
        }
    }

    public static function primaryKey() {
        return ['proccessTypeID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['proccessTypeTitle'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'proccessTypeID' => 'Proccess Type ID',
            'proccessTypeTitle' => 'Proccess Type',
        ];
    }

}
