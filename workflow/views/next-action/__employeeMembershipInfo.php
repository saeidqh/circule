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
$employeeMembershipModel = $peModel->employeeMembership;
$employeeMembershipModel->loadHumanResource();
$position = $employeeMembershipModel->position;
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
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeMembershipModel->FullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeMembershipModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeMembershipModel->getEmploymentType($employeeMembershipModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeMembershipModel->NationalCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'PersonnelCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeMembershipModel->PersonnelCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeMembershipModel->company->companyName) ?></i>
            </div>
        </div>


    </div>
</div>
<?php ActiveForm::end(); ?>    

