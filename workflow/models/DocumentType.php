<?php

namespace app\modules\workflow\models;

use Yii;

    /**
    * This is the model class for table "workflow_table_document_type".
    * @property string id
    * @property string title
    * @property string processTypeIdRef
    * @property string OBSCategory3CompanyIDRef
     */

class DocumentType extends \yii\db\ActiveRecord {
	
    /**
     * @inheritdoc
     */
    
    public static function tableName() {
        return 'workflow_table_document_type';
    }

    public static function primaryKey() {
        return ['id'];
    }

    public function rules() {
        return [
            [['title'], 'string'],
            [['processTypeIdRef', 'OBSCategory3CompanyIDRef'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'id',
            'title' => 'title',
            'processTypeIdRef' => 'processTypeIdRef',
            'OBSCategory3CompanyIDRef' => 'OBSCategory3CompanyIDRef',
        ];
    }

    public function getProcessType() {
        return $this->hasOne(\app\modules\workflow\models\ProcessType::className(), ['proccessTypeID' => 'processTypeIdRef']);
    }
}
