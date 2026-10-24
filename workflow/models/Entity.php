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
 * @property 'entityName' => $this->string(50),
 * @property 'tableName' => $this->string(50),
*/

 /**
 * @see Entity
 * 
 * 
 * @author <mail:mortezakml@gmail.com & Tel:09366263397>
 */
class Entity extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'workflow_table_entity';
    }

    public static function primaryKey() {
        return ["id"];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['id'], 'integer'],
            [['entityName', 'tableName'], 'string'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('workflow', 'id'),
            'entityName' => Yii::t('workflow', 'Entity Name'),
            'tableName' => Yii::t('workflow', 'Table Name'),
        ];
    }

//    public static function find() {
//        return new EntityQuery(get_called_class());
//    }

}
