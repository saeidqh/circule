<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\components\Utility;

?>
<?php $form = ActiveForm::begin([
        'id'=>'fix-data',
        'enableClientValidation'=>false,
        'enableAjaxValidation'=>false,
        'method'=>'post',
//        'clientOptions'=>array(
//                'validateOnSubmit'=>false,
//        ),
        'options'=>array(
                'class'=>'form-horizontal ',

        ),

]); ?>
<div class="row" >
    <div class="col-md-12">
        <div class="row mb5" >
            <div class="col-md-3 text-right" >
                <?= Html::label(Yii::t('workflow','Cash Request ID')); ?>
                
            </div>
            <div class="col-md-3" >
                <?= Html::textInput('CashRequestID',$peModel->CashRequestIDRef, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            <div class="col-md-3 text-right" >
                <?= Html::label(Yii::t('workflow','Request Date')); ?>
                
            </div>
            <div class="col-md-3" >
                <?= Html::textInput('RequestDateHijri',  Utility::persianDateTimeFormat($peModel->cashrequest->RequestDateHijri) , array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                )) ?>
            </div>
            
            
        </div>
        <div class="row mb5" >
            <div class="col-md-3 text-right" >
                <?= Html::label(Yii::t('workflow','Description')); ?>
                
            </div>
            <div class="col-md-9" >
         
                <?= Html::textArea('Description',$peModel->cashrequest->Description, array(
                        'class'=>'form-control input-sm',
                        'readonly'=>true,
                        'style'=>'min-height:80px; direction:rtl',
                )) ?>
            </div>

        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    