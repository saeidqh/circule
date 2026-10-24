<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
        'id'=>'fix-data',
        'enableClientValidation'=> false,
        'enableAjaxValidation'=> false,
        'method'=>'post',
//        'clientOptions'=>[
//             'validateOnSubmit'=>false,
//        ],
        'options'=>array(
            'class'=>'form-horizontal ',

        ),

]); ?>

<div class="row" >
    <div class="col-md-12">
        <div class="row mb5" >
            <div class="col-md-3 text-left" style="padding-left:60px;">
                Company
            </div>
            <div class="col-md-3" >
                <?= Html::textInput('companyIDRef',$peModel->tender->company->companyName, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            <div class="col-md-2 text-left">
                Tender Type
            </div>
            <div class="col-md-4" >
                <?= Html::textInput('tenderType', $peModel->tender->getTenderType($peModel->tender->tenderType), array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
              </div>
            
            
        </div>
        <div class="row mb5" >
            <div class="col-md-3 text-left" style="padding-left:60px;">
                Subject
            </div>
            <div class="col-md-9" >
                <?= Html::textInput('Subject',$peModel->tender->subject, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
      
        </div>
        <div class="row mb5" >
            <div class="col-md-3 text-left" >
                Estimate Start Date
            </div>
            <div class="col-md-3" >
                 <?= Html::textInput('estimateStartDate',  app\components\Utility::convertTimestampToJalali($peModel->tender->estimateStartDate), array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
                
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    

