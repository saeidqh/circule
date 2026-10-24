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
                Person ID
            </div>
            <div class="col-md-2" >
                <?= Html::textInput('PersonID',$peModel->sourcing->PersonID, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            <div class="col-md-2 text-left">
                Person Title
            </div>
            <div class="col-md-5" >
            <?= Html::textInput('PersonID',$peModel->sourcing->PersonTitle, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
              </div>
            
            
        </div>

        <div class="row mb5" >
             <div class="col-md-3 text-left" style="padding-left:60px;">
             person Type
            </div>
            <div class="col-md-2" >
             
                <?= Html::textInput('Title', $peModel->sourcing->personType, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            <div class="col-md-2 text-left" >
                Insert Date
            </div>
            <div class="col-md-3" >
             
                 <?= Html::textInput('CutOffDateHijri',$peModel->sourcing->insertDateHijri, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
                
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    

