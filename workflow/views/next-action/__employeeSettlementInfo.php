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
$employeeSettlementModel = $peModel->employeeSettlement;
$employeeSettlementModel->loadHumanResource();
$position = $employeeSettlementModel->position;

$stampDocuments = \app\modules\automation\models\HumanResource::stampDocumentQuery($employeeSettlementModel->HumanResourceIdRef)->all();

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
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->FullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->getEmploymentType($employeeSettlementModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->NationalCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'PersonnelCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->PersonnelCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->company->companyName) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Assistance OBS') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode((isset($employeeSettlementModel->assistance->positionType) ? $employeeSettlementModel->assistance->positionType->Title . ' ' : '') . $employeeSettlementModel->assistance->obsTitle) ?></i>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Organization Position') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode((isset($position->positionType) ? $position->positionType->Title . ' ' : '') . $position->obsTitle) ?></i>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    

<div>

<?php if (count($stampDocuments) > 0 ) { ?>
<div style="text-align: right; margin-top: 30px;"><strong>نشان های سازمانی دراختیار فرد(نشان های سازمانی زیر می بایست تعیین تکلیف و توسط برنامه ریزی و سیستم های رده  در سامانه کندو ثبت گردد)</strong></div>
<table class="table table-striped table-bordered" style="width: 100%; margin-top: 10px;">
    <tr>
        <th>شناسه کندو</th>
        <th>صادرکننده مجوز</th>
        <th>عنوان نشان سازمانی</th>
    </tr>
    <?php foreach ($stampDocuments as $stampDocument) { ?>
    <tr>
        <td><?= $stampDocument->StampId ?></td>
        <td><?= $stampDocument->OrganizationalMaker ?></td>
        <td><?= $stampDocument->StampTitle ?></td>
    </tr>
    <?php } ?>
</table>
<?php } ?>
</div>

