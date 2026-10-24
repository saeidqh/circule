<?php

namespace app\modules\workflow\models;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Yii;
use app\modules\workflow\models\ActionOrder;

/**
 * This is the model class for table "WorkFlow.tblActionEntity".
 *
 * @property 'id' => $this->integer(),
 * @property 'fieldName' => $this->string(50),
 * @property 'dataType' => $this->string(10),
 * @property 'entityIdRef' => $this->integer(),
 * @property 'log' => $this->boolean(),
 */
class EntityField extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'workflow_table_entity_field';
    }

    public static function primaryKey() {
        return ["id"];
    }
    
    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['id', 'log','entityIdRef'], 'integer'],
            [['fieldName', 'dataType'], 'string'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('workflow', 'id'),
            'fieldName' => Yii::t('workflow', 'Field Name'),
            'dataType' => Yii::t('workflow', 'Data Type'),
            'entityIdRef' => Yii::t('workflow', 'Entity Id'),
            'log' => Yii::t('workflow', 'log'),
        ];
    }

//    public static function find() {
//        return new EntityQuery(get_called_class());
//    }

}
