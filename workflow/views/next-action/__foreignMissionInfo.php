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
$foreignMissionModel = $peModel->foreignMission;

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
                <?= Yii::t('automation', 'StartDate') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($foreignMissionModel->StartDate) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'EndDate') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($foreignMissionModel->EndDate) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Country') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($foreignMissionModel->country->persianName) ?></i>
            </div>
        </div>

        <div class="row mb5">


            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($foreignMissionModel->company->companyName) ?></i>
            </div>
        </div>


    </div>
</div>
<?php ActiveForm::end(); ?>    

