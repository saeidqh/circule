<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<style>
    input[type="checkbox"] {
        pointer-events: none;
    }
    label:not(:last-child) {
        margin-right: 10px;
    }
</style>

<?php
$automationModel = $peModel->automation;
$position = $automationModel->position;
$automationModel->loadHumanResource();
$title = $peModel->ProcessIDRef == 72 ? 'New Automation' : ($peModel->ProcessIDRef == 73 ? 'New Access Level' : 'Person Retake Automation');
$requestDate = substr($automationModel->RequestDateHijri, 0, 10);
$humanObs = \app\modules\automation\models\HumanResourceObs::find()->with('obs')->where(['HumanIdRef' => $automationModel->HumanResourceIdRef, 'State' => 'Employment'])->andWhere(['<', 'LastUpdate', $requestDate])->andWhere("COALESCE(isDeleted,0)<>1")->all();
$oldPositionArray = [];
foreach ($humanObs as $ho) {
    if($peModel->ProcessIDRef == 86 || $ho->ObsIdRef != $automationModel->Position) {
        $oldPositionArray[$ho->ObsIdRef] = $ho->ObsIdRef . ' - ' . $ho->obs->fullTitle . ' ' . $ho->obs->company->PersonTitle;
    }
}
$oldPositionArray['other'] = 'و غیرفعال سازی سایر پست های قبلی این شخص';

$form = ActiveForm::begin([
            'id' => 'fix-data',
            'enableClientValidation' => false,
            'enableAjaxValidation' => false,
            'method' => 'post',
            'options' => array(
                'class' => 'form-horizontal ',
            ),
        ]);
?>

<div class="row" >
    <div class="col-md-12">
        <div class="row mb5">
            <div class="col-md-5">نوع درخواست</div>

            <div class="col-md-7 badge badge-brown">
                <i class=""><?= Yii::t('automation', $title) ?></i>
            </div>
        </div>
        
        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->fullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->getEmploymentType($automationModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->NationalCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Mobile') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->Mobile) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->company->companyName) ?></i>
            </div>
        </div>

        <?php if (!in_array($peModel->LastActionIDRef, [992, 993]) && $peModel->ProcessIDRef != 86) { ?>
        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Assistance') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->assistanceTitle) ?></i>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Position') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->fullTitle : '') ?></i>
            </div>
        </div>
        <?php } if ($peModel->ProcessIDRef == 72) { ?>
        <div class="row mb5">
            <div class="col-lg-3 col-md-3 col-xs-12">
                <?= Yii::t('automation', 'Position Count') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->totalPositionCount : null) ?></i>
            </div>
            <div class="col-lg-3 col-md-3 col-xs-12">
                <?= Yii::t('automation', 'FirstLevelPositionCount') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->parentFirstLevelPositionCount : null) ?></i>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Assistance Full Name') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->Assistance) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Old Full Name') ?>  &nbsp; <i class="badge badge-brown"><?= isset($automationModel->oldHumanResource) ? Html::encode($automationModel->oldHumanResource->fullName) : null; ?></i>
            </div>
            <?php if ($automationModel->HasAppointment == 0 || $automationModel->HasAppointment == 1) { ?>
            <div class="col-lg-5 col-md-5" style="margin-top: 10px; display: flex;">
            <div>
                <?= Yii::t('automation', 'Has Appointment') ?>  &nbsp;
            </div>
                <?= Html::radioList('HasAppointment', $automationModel->HasAppointment, $automationModel->hasAppointmentList, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::radio($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]); ?>
            </div>
            <div class="col-lg-3 col-md-3 col-xs-12">
                <?= Yii::t('automation', 'Management Position Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->ManagementPositionType) ?></i>
            </div>
            <?php } ?>
        </div>
        <?php }?>
        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t('automation', 'Automation Type') ?>  &nbsp;
                </div>
                <?= Html::checkboxList('AutomationType', explode(',', $automationModel->AutomationType), $automationModel->automationTypeList, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]) ?>
            </div>
            <?php if ($peModel->ProcessIDRef != 86) { ?>
            <div class="col-lg-6 col-md-6 col-xs-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t('automation', 'Sequerity Level') ?>  &nbsp;
                </div>
                <?= Html::checkboxList('SequerityLevel', explode(',', $automationModel->SequerityLevel), $automationModel->sequerityLevelList, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]); ?>
            </div>
            <?php }?>
        </div>
        <?php if ($peModel->ProcessIDRef == 72 || $peModel->ProcessIDRef == 86) { ?>
            <div class="row mb5">
                <div class="col-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t("automation", 'Old Automation Status') . '( ' . Yii::t("automation", 'Disable Old Position') . ' )' ?>  &nbsp;
                </div>
                <?= Html::checkboxList('DisableOldPosition', explode(',', $automationModel->DisableOldPosition), $oldPositionArray, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]) ?>
                </div>
            </div>
        <?php } if ($peModel->ProcessIDRef == 86) { ?>
            <div class="row mb5">
                <div class="col-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t("automation", 'Is Settlement') ?>  &nbsp;
                </div>
                <?= Html::radioList('IsSettlement', explode(',', $automationModel->IsSettlement), [0 => 'خیر', 1 => 'بله'], [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]) ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php ActiveForm::end(); ?>    

