<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblContractInvoiceTypeProcess".
 *
 * @property integer $ID
 * @property integer $ContractIDRef
 * @property integer $InvoiceTypeIDRef
 * @property integer $ProcessIDRef
 * @property integer $hasChange
 * @property integer $isDeleted
 */
class ContractInvoiceTypeProcess extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_contract_invoice_type_process';
        } else {
            return 'WorkFlow.tblContractInvoiceTypeProcess';
        }
    }

    public static function primaryKey() {
        return ['ID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ContractIDRef', 'InvoiceTypeIDRef', 'ProcessIDRef'], 'required'],
            [['ContractIDRef', 'InvoiceTypeIDRef', 'ProcessIDRef', 'hasChange', 'isDeleted'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ID' => 'ID',
            'ContractIDRef' => 'Contract Idref',
            'InvoiceTypeIDRef' => 'Invoice Type Idref',
            'ProcessIDRef' => 'Process Idref',
        ];
    }

    public function getInvoiceTypes() {
        return $this->hasOne(\app\modules\invoice\models\InvoiceTypes::className(), ['InvoiceTypeID' => 'InvoiceTypeIDRef']);
    }

    public function getContract() {
        return $this->hasOne(\app\modules\contract\models\Contract::className(), ['ContractID' => 'ContractIDRef']);
    }

    public function getProcess() {
        return $this->hasOne(\app\modules\workflow\models\Process::className(), ['ProcessID' => 'ProcessIDRef']);
    }

}
