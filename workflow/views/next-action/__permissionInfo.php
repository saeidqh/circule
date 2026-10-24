<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
        'id'=>'fix-data',
        'enableClientValidation'=> false,
        'enableAjaxValidation'=> false,
        'method'=>'post',
        'options'=>array(
            'class'=>'form-horizontal ',

        ),

]); ?>

<div class="row" >
    <div class="col-md-12">
        <div class="row mb5" >
            <div class="col-md-3 text-left" style="padding-left:60px;">
                ContractNo
            </div>
            <div class="col-md-3" >
                <?= Html::textInput('ContractNo',$peModel->permission->contract->ContractNo, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            <div class="col-md-2 text-left">
                SubContractor
            </div>
            <div class="col-md-4" >
                <?= Html::textInput('SubContractor', $peModel->permission->contract->subContractor->PersonTitle, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
              </div>
            
            
        </div>
        <div class="row mb5" >
            <div class="col-md-3 text-left" style="padding-left:60px;">
                Contract Subject
            </div>
            <div class="col-md-9" >
         
                <?= Html::textarea('ContractSubject', $peModel->permission->contract->ContractSubject, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
      
        </div>
        <div class="row mb5" >
             <div class="col-md-3 text-left" style="padding-left:60px;">
                Permission Title
            </div>
            <div class="col-md-3" >
             
                <?= Html::textInput('Title', $peModel->permission->title, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            <div class="col-md-2 text-left" >
                CutOffDate
            </div>
            <div class="col-md-4" >
             
                 <?= Html::textInput('CutOffDateHijri',$peModel->permission->cutOffDateHijri, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
                
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    

