<?php
use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style >
    

</style>
<?php
    $form = ActiveForm::begin([
    'id' => 'param-invoice-form',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'options' => [
        'data-pjax' => '1',
        'enctype' => 'multipart/form-data'
    ]
]);?>
<div class='row p5 mb5' style='background-color: #FAEBFF;' >
    <div class='col-md-12' >

        <div class='row ' >
            <div class='col-md-12' >
                <div class='row' >
                    <div class='col-md-2' >
                        <div class='row '>
                            <div style='background:#C966FB;border-top-left-radius: 5px;border-bottom-left-radius: 5px;height:100px;text-align: center;padding-top: 20px;'>
                                <span style='font-size:40px ;color:white;padding-top:50px;' >
                                    <?= $actionModel->ActionID ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-10' >
                        <div class='row pt10' style='border:solid 1px #C966FB;height:100px;'>
                            <div class='col-md-2 pt5' >Title1:</div>
                            <div class='col-md-10 mb10' >
                                <?= Html::textInput('actionTitle1', $actionModel->ActionTitle1,[
                                        'id'=>'action-title-1',
                                        'class'=>'form-control input-sm',
                                        'disabled'=>true,
                                ]) ?>
                            </div>
                            <div class='col-md-2 pt5' >Title2:</div>
                            <div class='col-md-10' >
                                <?= Html::textInput('actionTitle2', $actionModel->ActionTitle2,[
                                        'id'=>'action-title-2',
                                        'class'=>'form-control input-sm',
                                        'disabled'=>true,
                                ]) ?>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div class='clear' ></div>
</div>
<div class='row'>
    
    <div class='col-md-3'>
        
        <?= $form->field($actionParamCashRequestModel, 'requestType',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'eps',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'recipientType',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'recipient',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'budgetRow',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'budgetTitle',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'paymentSubject',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        
    </div>
    <div class='col-md-3'>
        
        <?= $form->field($actionParamCashRequestModel, 'accountType',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'accountNumber',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'bank',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'name',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'requestAmount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'requestCurrency',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'manager1Amount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        
    </div>
    <div class='col-md-3'>
        
        <?= $form->field($actionParamCashRequestModel, 'planningAmount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'manager2Amount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'manager2Currency',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'clientAmount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'financialAmountInManager2Currency',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'financialAmount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'financialCurrency',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        
    </div>
    <div class='col-md-3'>
        <?= $form->field($actionParamCashRequestModel, 'chequeNumber',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'chequeDate',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'reciver',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'tempNo',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox() ?>
        <?= $form->field($actionParamCashRequestModel, 'showObsChilds',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()->label('Access Only Obs Childs') ?>
        <?= $form->field($actionParamCashRequestModel, 'amountStatus',['horizontalCssClasses' =>['label'=>'col-md-9','wrapper'=>'col-md-11 col-sm-offset-1']])->dropDownList($actionParamCashRequestModel->AMOUNT_STATUS_TYPES,['prompt' =>(($actionParamCashRequestModel->amountStatus)?null:'...') ,])->label('Amount Status') ?>
    </div>
</div>


<hr class="p0 m0 mb5" style="border-top: 1px solid #C7C7C7"/>
<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        <?= Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Save', [
            'class'=>'btn btn-success btn-sm pull-right ml10'
        ]) ?>
        <?= Html::a('<i class="im-close"></i>&nbsp;Cancel','#', [
            'class'=>'btn btn-danger btn-sm pull-right',
            'data-dismiss'=>'modal'
        ]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>