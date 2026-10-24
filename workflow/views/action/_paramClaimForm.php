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
    <div class='col-md-1'>
        <?= $form->field($actionParamClaimModel, 'claimUpdate',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Update') ?>
    </div>
    <div class='col-md-3'>
        <?= $form->field($actionParamClaimModel, 'claimDocumentUpdate',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Update Document') ?>
        <?= $form->field($actionParamClaimModel, 'claimDocumentDelete',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Delete Document') ?>
        <?= $form->field($actionParamClaimModel, 'claimDocumentCreate',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Create Document') ?>
    </div>
    <div class='col-md-3'>
        <?= $form->field($actionParamClaimModel, 'claimAmountStep1Update',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Requested By Subcontractor') ?>
        <?= $form->field($actionParamClaimModel, 'claimAmountStep2Update',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Site') ?>
        <?= $form->field($actionParamClaimModel, 'claimAmountStep5Update',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Pars Group') ?>
    </div>
    <div class='col-md-5'>
        <?= $form->field($actionParamClaimModel, 'claimAmountStep4Update',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Holding') ?>
        <?= $form->field($actionParamClaimModel, 'claimAmountStep3Update',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Company') ?>
        <?= $form->field($actionParamClaimModel, 'amountStatus',['horizontalCssClasses' =>['label'=>'col-md-5','wrapper'=>'col-md-7']])
            ->dropDownList($actionParamClaimModel->AMOUNT_STATUS_TYPES,
                ['prompt' =>(($actionParamClaimModel->amountStatus)?null:'...') ,]
            )->label('Amount Status') ?>
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