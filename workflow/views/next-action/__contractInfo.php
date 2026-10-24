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
                Html::textInput('ContractNo', $peModel->contract->ContractNo, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>
            <div class="col-md-3 text-right" >
                SubContractor
            </div>
            <div class="col-md-3" >

                <?php
                $subContractorPersonTitle='';
                if(app\components\Utility::isJson($peModel->contract->SubContractorPersonTitle)){
                    $val=  json_decode($peModel->contract->SubContractorPersonTitle);
                    $subContractorPersonTitle=  $val->SubContractorPersonTitle;
                }else{
                    $subContractorPersonTitle=  $peModel->contract->SubContractorPersonTitle;
                }
                ?>
                <?=
                Html::textInput('SubContractor', $subContractorPersonTitle, array(
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
                <?= Html::textarea('ContractSubject', $peModel->contract->ContractSubject, array(
                    'class' => 'form-control input-sm',
                    'readonly' => true,
                ))
                ?>
            </div>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    