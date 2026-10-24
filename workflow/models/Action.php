<?php

namespace app\modules\workflow\models;

use Yii;

/**
 * This is the model class for table "Workflow.tblAction".
 *
 * @property integer $ActionID
 * @property string $ActionTitle1
 * @property string $ActionTitle2
 * @property integer $ProcessIDRef
 * @property integer $ActionOrder
 * @property integer $isStartAction
 * @property integer $isEndAction
 * @property integer $SignOrder
 * @property integer $bestDurationInMinute
 * @property integer $calenderIdRef
 * @property integer $companyIdList
 * @property integer $CharacterTypeIDRef
 * @property integer $isDeleted
 */
class Action extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'workflow_table_action';
    }

    public static function primaryKey() {
        return ['ActionID'];
    }

    public static function getDb() {
        return \app\components\Utility::centralDb();
    }

    public function rules() {
        return [
            [['ActionID'], 'required'],
            [['ActionID', 'ProcessIDRef', 'ActionOrder', 'isStartAction', 'isEndAction', 'SignOrder', 'bestDurationInMinute', 'calenderIdRef', 'CharacterTypeIDRef', 'isDeleted'], 'integer'],
            [['ActionTitle1', 'ActionTitle2'], 'string'],
            [['ActionTitle1', 'ActionTitle2'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'ActionID' => 'Action ID',
            'ActionTitle1' => 'Action Title1',
            'ActionTitle2' => 'Action Title2',
            'ProcessIDRef' => 'Process Idref',
            'ActionOrder' => 'Action Order',
            'isStartAction' => 'Is Start Action',
            'isEndAction' => 'Is End Action',
            'SignOrder' => 'Sign Order',
            'bestDurationInMinute' => 'Best Duration In Minute',
            'calenderIdRef' => 'Calender Id Ref',
            'companyIdList' => 'Companies',
            'CharacterTypeIDRef' => 'CharacterType',
            'isDeleted' => Yii::t('contract' ,  'Is Deleted'),
        ];
    }
   
    /**
     * @inheritdoc
     * @return ActionQuery the active query used by this AR class.
     */
    public static function find() {
        return new ActionQuery(get_called_class());
    }

    public function getProcess() {
        return $this->hasOne(\app\modules\workflow\models\Process::className(), ['ProcessID' => 'ProcessIDRef']);
    }

    public function getObs() {
        return $this->hasMany(\app\modules\OBS\models\ObsProcessAction::className(), ['ActionIDRef' => 'ActionID']);
    }

    public function getProcessCodeAction(): \yii\db\ActiveQuery
    {
        return $this->hasMany(\app\modules\document\models\ProcessCodeAction::className(),['ActionIDRef' => 'ActionID']);
    }

    public function getReportItemAction() {
        return $this->hasOne(\app\modules\reportWorkflow\models\ReportItemAction::className(), ['actionIdRef' => 'ActionID']);
    }
    
    public function getCharacterType() {
        return $this->hasOne(\app\modules\workflow\models\ActionCharacterType::className(), ['CharacterTypeID' => 'CharacterTypeIDRef']);
    }
    
    public function getAmountStatus() {
        $model = \app\modules\workflow\models\ActionParam::find()
                ->andWhere(["actionIdref" => $this->ActionID])
                ->andWhere(["paramTitle" => 'AmountStatus'])
                ->addSelect("value1")
                ->asArray()
                ->one();
        return isset($model)?$model["value1"]:null;
    }
}
