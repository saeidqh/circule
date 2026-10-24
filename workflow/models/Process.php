<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblProcess".
 *
 * @property integer $ProcessID
 * @property string $ProcessTitle
 * @property integer $ProccessTypeIDRef
 * @property integer $OBSCategory3CompanyIDRef
 * @property integer $parentProcessIdRef
 */
class Process extends \yii\db\ActiveRecord {
    const EMPLOYEESETTLEMENT_PROCESS_TYPE = 16;
    const EMPLOYEE_MEMBERSHIP_PROCESS_TYPE = 17;
    const FOREIGN_MISSION_PROCESS_TYPE = 18;
    const STAMP_DOCUMENT_PROCESS_TYPE = 19;
    const REFUND_STAMP_PROCESS_TYPE = 20;

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_process';
        } else {
            return 'WorkFlow.tblProcess';
        }
    }

    public static function primaryKey() {
        return ['ProcessID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ProcessID', 'ProccessTypeIDRef'], 'required'],
            [['ProcessID', 'ProccessTypeIDRef', 'OBSCategory3CompanyIDRef', 'parentProcessIdRef', 'hasChange'], 'integer'],
            [['ProcessTitle',], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ProcessID' => 'Process ID',
            'ProcessTitle' => 'Process Title',
            'ProccessTypeIDRef' => 'Proccess Type Idref',
            'OBSCategory3CompanyIDRef' => 'Obscategory3 Company Idref',
            'parentProcessIdRef' => 'Parent Process Id Ref',
        ];
    }

    public static function getFirstAction($pid) {
        $actionModel = new Action();
        $firstActionData = $actionModel->find()->where(['ProcessIDRef' => $pid, 'isStartAction' => 1])->one();
        $firstAction = !($firstActionData) ? null : $firstActionData->ActionID;
        return $firstAction;
    }

    public static function find() {
        return new ProcessQuery(get_called_class());
    }

    public function getType() {
        return $this->hasOne(\app\modules\workflow\models\ProcessType::className(), ['proccessTypeID' => 'ProccessTypeIDRef']);
    }

    public function getCompany() {
        return $this->hasOne(\app\modules\OBS\models\Person::className(), ['PersonID' => 'OBSCategory3CompanyIDRef']);
    }

}
