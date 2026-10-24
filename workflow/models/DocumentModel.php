<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblDocumentModel".
 *
 * @property integer $modelId
 * @property string $modelTitle
 * @property integer $processTypeIdRef
 * @property integer $OBSCategory3CompanyIDRef
 */
class DocumentModel extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_document_model';
        } else {
            return 'WorkFlow.tblDocumentModel';
        }
    }

    public static function primaryKey() {
        return ['modelId'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['modelTitle'], 'string'],
            [['processTypeIdRef', 'OBSCategory3CompanyIDRef'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'modelId' => 'Model ID',
            'modelTitle' => 'Model Title',
            'processTypeIdRef' => 'Process Type Id Ref',
            'OBSCategory3CompanyIDRef' => 'Obscategory3 Company Idref',
        ];
    }

    /**
     * @inheritdoc
     * @return DocumentModelQuery the active query used by this AR class.
     */
    public static function find() {
        return new DocumentModelQuery(get_called_class());
    }

}
