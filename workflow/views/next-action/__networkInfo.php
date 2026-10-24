<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<style>
    input[type="checkbox"] {
        pointer-events: none;
    }
    label:not(:last-child) {
        margin-right: 5px;
    }
</style>

<?php
$networkModel = $peModel->network;
$position = $networkModel->position;
$networkModel->loadHumanResource();
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
    <div class="col-md-12" style="padding: 0">
        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->fullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->getEmploymentType($networkModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->NationalCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Mobile') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->Mobile) ?></i>
            </div>

            <div class="col-lg-4 col-md-4">
                 <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->company->companyName) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <?= Yii::t('automation', 'Position2') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->fullTitle : '') ?></i>
            </div>
        </div>
        
        <div class="row mb5">
            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t('automation', 'Network Type') ?>  &nbsp;
                </div>
                <?= Html::checkboxList('NetworkType', explode(',', $networkModel->NetworkType), $networkModel->networkTypeList, [
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
        
    </div>
</div>
<?php ActiveForm::end(); ?>    

