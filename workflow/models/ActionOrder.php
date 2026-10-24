<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "WorkFlow.tblActionOrder".
 *
 * @property integer $ID
 * @property integer $ActionIDRef
 * @property integer $NextActionIDRef
 * @property integer $isDefaultNextAction
 * @property string $AutoMessage1
 * @property string $AutoMessage2
 * @property string $AutoMessage3
 * @property integer $RequiredMessage
 * @property integer $requiredLetterNo
 * @property integer $acceptManualDate
 * @property integer $letterIdRef1
 * @property integer $letterIdRef2
 * @property integer $letterIdRef3
 * @property integer $documentModelIdRef
 * @property integer $subProcessIdRef1
 * @property integer $subProcessIdRef2
 * @property integer $subProcessIdRef3
 */
class ActionOrder extends \yii\db\ActiveRecord {

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_order';
        } else {
            return 'WorkFlow.tblActionOrder';
        }
    }

    public static function primaryKey() {
        return ['ID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['ActionIDRef', 'NextActionIDRef'], 'required'],
            [['ActionIDRef', 'NextActionIDRef', 'isDefaultNextAction', 'RequiredMessage', 'requiredLetterNo', 'acceptManualDate', 'letterIdRef1', 'letterIdRef2', 'letterIdRef3', 'documentModelIdRef', 'subProcessIdRef1', 'subProcessIdRef2', 'subProcessIdRef3'], 'integer'],
            [['AutoMessage1', 'AutoMessage2', 'AutoMessage3'], 'string'],
            [['letterIdRef1', 'letterIdRef2', 'letterIdRef3'], 'validateLetterId']
        ];
    }

    public function validateLetterId($attribute) {
        if ($this->letterIdRef1 == $this->letterIdRef2 && !empty($this->letterIdRef1)) {
            $this->addError('letterIdRef2', 'Duplicate letter');
        } elseif ($this->letterIdRef1 == $this->letterIdRef3 && !empty($this->letterIdRef1)) {
            $this->addError('letterIdRef3', 'Duplicate letter');
        } elseif ($this->letterIdRef2 == $this->letterIdRef3 && !empty($this->letterIdRef2)) {
            $this->addError('letterIdRef3', 'Duplicate letter');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ID' => 'ID',
            'ActionIDRef' => 'Action Idref',
            'NextActionIDRef' => 'Next Action Idref',
            'isDefaultNextAction' => 'Is Default Next Action',
            'AutoMessage1' => 'Auto Message1',
            'AutoMessage2' => 'Auto Message2',
            'AutoMessage3' => 'Auto Message3',
            'RequiredMessage' => 'Required Message',
            'requiredLetterNo' => 'Required Letter No',
            'acceptManualDate' => 'Accept Manual Date',
            'letterIdRef1' => 'Letter Id Ref1',
            'letterIdRef2' => 'Letter Id Ref2',
            'letterIdRef3' => 'Letter Id Ref3',
            'documentModelIdRef' => 'Document Model Id Ref',
            'subProcessIdRef1' => 'Sub Process Id Ref1',
            'subProcessIdRef2' => 'Sub Process Id Ref2',
            'subProcessIdRef3' => 'Sub Process Id Ref3',
        ];
    }

    public static function find() {
        return new ActionOrderQuery(get_called_class());
    }

    public function getAction() {
        return $this->hasOne(\app\modules\workflow\models\Action::className(), ['ActionID' => 'ActionIDRef']);
    }

    public function getNextAction() {
        return $this->hasOne(\app\modules\workflow\models\Action::className(), ['ActionID' => 'NextActionIDRef']);
    }

}
