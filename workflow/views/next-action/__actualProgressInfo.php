<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<?php 
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
        <div class="row mb5" >
            <div class="col-md-2 text-right" >
                Actual Progress ID
            </div>
            <div class="col-md-3" >
                <?=
                Html::textInput('ContractNo', $peModel->actualProgress->id, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>
            <div class="col-md-2 text-right" >
                PlanTitle
            </div>
            <div class="col-md-5" >

                <?=
                Html::textInput('PlanTitle', $peModel->actualProgress->plan->PlanTitle, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>


        </div>
        <div class="row mb5" >
            <div class="col-md-2 text-right" >
                Actual Description
            </div>
            <div class="col-md-10" >
                <?= Html::textarea('description', $peModel->actualProgress->description, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    