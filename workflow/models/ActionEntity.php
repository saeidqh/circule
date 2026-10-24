<?php

namespace app\modules\workflow\models;



use Yii;
use app\modules\workflow\models\ActionOrder;

/**
 * This is the model class for table "WorkFlow.tblActionEntity".
 * @author <mail:mortezakml@gmail.com & Tel:09366263397>
 *
 * @property integer $ID
 * @property integer $ProcessEntityIDRef
 * @property integer $FromActionIDRef
 * @property integer $ActionIDRef
 * @property string $ActionDate
 * @property string $ActionDateHijri
 * @property integer $UserIDRef
 * @property integer $OBSCategory3IDRef
 * @property string $ComputerName
 * @property string $ipAddress
 * @property string $ActionMessage
 * @property string $ActionAutoMessage
 * @property string $LetterNumber
 * @property integer $toActionIdRef
 * @property string $toActionDate
 * @property string $toActionDateHijri
 * @property integer $durationInMinute
 * @property integer $hasChange
 */
class ActionEntity extends \yii\db\ActiveRecord {

    public static function tableName() {
        if (Yii::$app->params['dmbs'] == 'mysql') {
            return 'workflow_table_action_entity';
        } else {
            return 'WorkFlow.tblActionEntity';
        }
    }

    public static function primaryKey() {
        return ["ID"];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['ID', 'ProcessEntityIDRef', 'FromActionIDRef', 'ActionIDRef', 'UserIDRef', 'OBSCategory3IDRef', 'toActionIdRef', 'durationInMinute', 'hasChange'], 'integer'],
            [['ActionDate', 'ActionDateHijri', 'ComputerName', 'ipAddress', 'ActionMessage', 'ActionAutoMessage', 'LetterNumber', 'toActionDateHijri'], 'string'],
            [['toActionDate'], 'safe'],
            ['ActionIDRef', 'required'],
            ['LetterNumber', 'LetterNumberValidation', 'skipOnEmpty' => false, 'on' => 'nextaction'],
            ['ActionMessage', 'ActionMessageValidation', 'skipOnEmpty' => false, 'on' => 'nextaction'],
            ['ActionDateHijri', 'ActionDateHijriValidation', 'skipOnEmpty' => false, 'on' => 'nextaction'],
        ];
    }

    public function attributeLabels() {
        return [
            'ID' => Yii::t('workflow','ID'),
            'ProcessEntityIDRef' => Yii::t('workflow','ProcessEntityIDRef'),
            'FromActionIDRef' => Yii::t('workflow','Current Action'),
            'ActionIDRef' => Yii::t('workflow','Next Step'),
            'ActionDate' => Yii::t('workflow','ActionDate'),
            'ActionDateHijri' => Yii::t('workflow','Action Date'),
            'UserIDRef' => Yii::t('workflow','UserIDRef'),
            'OBSCategory3IDRef' => Yii::t('workflow','OBSCategory3IDRef'),
            'ComputerName' => Yii::t('workflow','ComputerName'),
            'ActionMessage' => Yii::t('workflow','Message'),
            'ActionAutoMessage' => Yii::t('workflow','Auto Message'),
            'LetterNumber' => Yii::t('workflow','Letter No'),
            'durationInMinute' => Yii::t('workflow','durationInMinute'),
            'toActionIdRef' => Yii::t('workflow','toActionIdRef'),
            'ipAddress' => Yii::t('workflow','ipAddress'),
            'toActionDate' => Yii::t('workflow','toActionDate'),
            'toActionDateHijri' => Yii::t('workflow','toActionDateHijri'),
        ];
    }

    public function getNextStepDopdownData($CurrentActionIDRef, $ProcessEntityID) {

        $uid = Yii::$app->getSession()->get('UserID');
        $obp = Yii::$app->getSession()->get('ObsPosition');

        $sql = "select * from [dbo].[get_user_next_action_list_in_workflow]($uid,$obp,$ProcessEntityID,$CurrentActionIDRef)";
        $rawData = Yii::$app->db->createCommand($sql)->queryAll();
        return $rawData;
    }

    public function LetterNumberValidation($attribute) {
        $actionOrderModel = ActionOrder::find()
                ->where(['ActionIDRef' => $this->FromActionIDRef, 'NextActionIDRef' => $this->ActionIDRef])
                ->one();
        if (!empty($actionOrderModel)) {
            if ($actionOrderModel->requiredLetterNo == 1 && empty($this->LetterNumber)) {
                $this->addError($attribute, 'field is required!');
            }
        }
    }

    public function ActionMessageValidation($attribute) {
        $actionOrderModel = ActionOrder::find()
                ->where(['ActionIDRef' => $this->FromActionIDRef, 'NextActionIDRef' => $this->ActionIDRef])
                ->one();
        if (!empty($actionOrderModel)) {
            if ($actionOrderModel->RequiredMessage == 1 && empty($this->ActionMessage)) {
                $this->addError($attribute, 'field is required!');
            }
        }
    }

    public function ActionDateHijriValidation($attribute) {

        $actionOrderModel = ActionOrder::find()
                ->where(['ActionIDRef' => $this->FromActionIDRef, 'NextActionIDRef' => $this->ActionIDRef])
                ->one();
        if (!empty($actionOrderModel)) {
            if ($actionOrderModel->acceptManualDate == 1 && empty($this->ActionDateHijri)) {
                $this->addError($attribute, 'field is required!');
            }
        }
    }

    public function setStoreValues() {
        $array = [];
        foreach ($this->attributes() as $attribute) {
            $array[$attribute] = $this->$attribute;
        }
        if (!empty($this->ProcessEntityIDRef) && !empty($this->FromActionIDRef)) {
            Yii::$app->session->set($uniqe = 'EntityID-' . $this->ProcessEntityIDRef, $array);
            $array = [
                'ipAddress' => Yii::$app->request->UserIP,
            ];
            $data = array_merge($array, Yii::$app->session->get($uniqe));
            Yii::$app->session->set($uniqe, $data);
        } else {
            return false;
        }
    }

    /**
     * remove attributes value off model  
     */
    public function clearStoreValues() {
        if (!empty($this->ProcessEntityIDRef)) {
            Yii::$app->session->remove('EntityID-' . $this->ProcessEntityIDRef);
        }
    }

    /**
     * if empty key session variable $key , that time set session width key in $key
     * @param type $key
     * @param type $value
     * @return type
     */
    public function fetchStoreValue($key, $defaultValue = null) {
        if (Yii::$app->request->isPost && !empty($this->$key)) {
            return $this->$key;
        }
        $session = Yii::$app->session['EntityID-' . $this->ProcessEntityIDRef];
        if (isset($session[$key]) && !empty($defaultValue = $session[$key])) {
            return $defaultValue;
        }

        if (!empty($defaultValue)) {
            return $defaultValue;
        } else {
            return $this->$key;
        }
    }

    /**
     * set field Entitys(invoice,contract,budget,capital,warranty) width $processEntityModel
     * @param type $peModel
     */
    public function setFeildIdStoreValue($peModel) {
        $uniqe = 'EntityID-' . $peModel->ProcessEntityID;

        if (isset($peModel->InvoiceIDRef) && !empty($peModel->InvoiceIDRef)) {
            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'InvoiecId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['InvoiecId' => $peModel->InvoiceIDRef]);
        } elseif (isset($peModel->CashRequestIDRef) && !empty($peModel->CashRequestIDRef)) {

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'CashRequestId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['CashRequestId' => $peModel->CashRequestIDRef]);
        } elseif (isset($peModel->WorkflowContractIDRef) && !empty($peModel->WorkflowContractIDRef)) {

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'ContractId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['ContractId' => $peModel->WorkflowContractIDRef]);
        } elseif (isset($peModel->warrantyIdRef) && !empty($peModel->warrantyIdRef)) {

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['fieldId' => 'WarrantyId']);

            Yii::$app->session[$uniqe] = array_merge(Yii::$app->session[$uniqe], ['WarrantyId' => $peModel->warrantyIdRef]);
        }
    }

    public static function duration($id) {
        //ToDo farzam-joudi
        //بر اساس اس کیو ال تکمیل شود
        //[controller].[actionEntityCalculateDuration]
        return 0;
    }

    public static function find() {
        return new ActionEntityQuery(get_called_class());
    }

    public function getProcessEntity() {
        return $this->hasOne(\app\modules\workflow\models\ProcessEntity::className(), ['ProcessEntityID' => 'ProcessEntityIDRef']);
    }

    public function getFromAction() {
        return $this->hasOne(\app\modules\workflow\models\Action::className(), ['ActionID' => 'FromActionIDRef']);
    }

    public function getAction() {
        return $this->hasOne(\app\modules\workflow\models\Action::className(), ['ActionID' => 'ActionIDRef']);
    }

    public function getToAction() {
        return $this->hasOne(\app\modules\workflow\models\Action::className(), ['ActionID' => 'toActionIDRef']);
    }

    public function getUser() {
        return $this->hasOne(\app\modules\account\models\User::className(), ['UserID' => 'UserIDRef']);
    }

    public function getObs() {
        return $this->hasOne(\app\modules\OBS\models\Obsnew::className(), ['obsId' => 'OBSCategory3IDRef']);
    }

}
