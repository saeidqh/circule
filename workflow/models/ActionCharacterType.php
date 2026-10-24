<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "Workflow.tblActionCharacterType".
 *
 * @property integer $CharacterTypeID
 * @property string $CharacterTypeTitle
 */
class ActionCharacterType extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_character_type';
        } else {
            return 'tblActionCharacterType';
        }
    }

    public static function primaryKey() {
        return ['CharacterTypeID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['CharacterTypeTitle'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'CharacterTypeID' => 'Character Type ID',
            'CharacterTypeTitle' => 'Character Type Title',
        ];
    }

}
