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
            <div class="col-md-3 text-right" >
                ContractNo
            </div>
            <div class="col-md-3" >
                <?=
                Html::textInput('ContractNo', $peModel->claim->contract->ContractNo, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>
            <div class="col-md-3 text-right" >
                SubContractor
            </div>
            <div class="col-md-3" >

                <?=
                Html::textInput('SubContractor', $peModel->claim->contract->subContractor->PersonTitle, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>


        </div>
        <div class="row mb5" >
            <div class="col-md-3 text-right" >
                Contract Subject
            </div>
            <div class="col-md-9" >
                <?= Html::textarea('ContractSubject', $peModel->claim->contract->ContractSubject, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>

        </div>
        <div class="row mb5">
            <div class="col-md-3 text-right">
                Claim No
            </div>
            <div class="col-md-3">
                <?=  Html::textInput('claimNo',$peModel->claim->claimNo,[
                    'class'=>'form-control input-ms',
                    'readonly'=>true,
                ]);
                ?>
            </div>
            <div class="col-md-3 text-right">
                Claim subject
            </div>
            <div class="col-md-3">
                <?=  Html::textInput('subject',$peModel->claim->subject,[
                    'class'=>'form-control input-ms',
                    'readonly'=>true,
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    