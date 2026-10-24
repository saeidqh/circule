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
 * @property 'id' => $this->integer(),
 * @property 'entityIdRef' => $this->integer(),
 * @property 'parentEntityIdRef' => $this->integer(),
 * @property 'processIdRef' => $this->integer(),
 * @property 'parentKey' => $this->string(50),
 * @property 'forignKey' => $this->string(10),
*/

class EntityRelation extends \yii\db\ActiveRecord {

    public static function tableName() {
        return 'workflow_table_entity_relation';
    }

    public static function primaryKey() {
        return ["id"];
    }
    
    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['id', 'entityIdRef','parentEntityIdRef','processIdRef'], 'integer'],
            [['parentKey', 'forignKey'], 'string'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => Yii::t('workflow', 'id'),
            'entityIdRef' => Yii::t('workflow', 'EntityId'),
            'parentEntityIdRef' => Yii::t('workflow', 'Parent EntityId'),
            'processIdRef' => Yii::t('workflow', 'ProcessId'),
            'parentKey' => Yii::t('workflow', 'Parent Key'),
            'forignKey' => Yii::t('workflow', 'Forign Key'),
        ];
    }

    
}
