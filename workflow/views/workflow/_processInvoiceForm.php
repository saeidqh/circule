<?php

use app\components\GridViewBox;
use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;


?>  
<style>
    .tbl-row-doted .buttom-yellow-dotted-border{
        border-bottom: dotted 1px yellow;
        padding: 4px; 
    }
    .user-action-box table  thead{
        background-color: #FFAF9D !important;
    }
    .user-action-box .summary,
    .user-action-box td,
    .user-action-box th{
        font-size: 11px;
    }
    
    .input-value-picker a {
        position: absolute;
        right: 17px;
        top: 2px;
    }
</style>

<div class='row' >
    
<?php
$form = ActiveForm::begin([
    'id' => 'obs-action-form',
    'layout' => 'horizontal',
    'options' => [
        'data-pjax' => '1',
        'enctype' => 'multipart/form-data'
    ]
]);?>
    <div class="col-md-12 pt10  mt10" style='' >
        <div class='row' >
                <div class='col-md-12' >
                    <div class="form-group  ">
                        <!-- Hiden field for contract picker -->
                        
                        <label class="control-label col-sm-3" for="contract-subject">Invoice Process</label>
                        <div class="col-sm-9">
                            <?= Html::textInput('contractSubject',$processModel->ProcessTitle,[
                                'id'=>'process-title',
                                'class'=>'form-control input-sm',
                                'disabled'=>true,
                            ])?>
                        </div>
                    </div>
                    
                </div>
                <div class='col-md-12 input-value-picker' >
                    <div class="form-group field-contractinvoicetypeprocess-contractidref required required">
                        <!-- Hiden field for contract picker -->
                        <div class="col-sm-4" style="display:none">
                            <?= $form->field($contractInvoiceTypeProcessModel, 'ContractIDRef')->hiddenInput();?>
                        </div>
                        <label class="control-label col-sm-3" for="contractinvoicetypeprocess-contractidref">Contract No</label>
                        <div class="col-sm-9">
                            <?=  kartik\widgets\Select2::widget([
                                    'name'=>'contract-title',
                                    'id'=>'contract-no',
                                    'data' => yii\helpers\ArrayHelper::map($subcontractorArray, 'ContractID', 'ContractDesc'),
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'escapeMarkup' => new yii\web\JsExpression('function(markup){return markup;}'),
                                        'templateResult' => new yii\web\JsExpression('formatRepo'),
                                        'templateSelection'  => new yii\web\JsExpression('formatRepoSelection')
                                    ],
                                    'options' => [
                                        'placeholder' => 'Select a Contract ...',
                                        'tabindex' => '103'
                                    ],
                            ]) ?>
                         
<!--                            
                            <?php 
//                            echo Html::textInput('contract-title', '', [
//                                'id'=>'contract-no',
//                                'class'=>'form-control',
//                                'disabled'=>true,
//                                'style'=>'background-color:#FFEFC2',
//                                'label'=>'lslk',
//                            ]) 
                            ?>
                            <?php
//                            echo Html::a('<i class="en-ellipsis"></i>','#',[
//                                'class'=>'btn btn-success pull-right ml5 btn-sm',
//                                'id'=>'btn-save-action',
//                                'onclick'=>'return btnSelectContractClick(this);',
//                            ])
                            ?>
                            -->
                            
                            <div class="help-block help-block-error "></div>
                        </div>
                    </div>
                   
                    
                </div>
                <div class='col-md-12' >
                    <div class="form-group  ">
                        <!-- Hiden field for contract picker -->
                        
                        <label class="control-label col-sm-3" for="contract-subject">Contract Subject</label>
                        <div class="col-sm-9">
                            <?= Html::textInput('contractSubject','',[
                                'id'=>'contract-subject',
                                'class'=>'form-control input-sm',
                                'disabled'=>true,
                            ])?>
                        </div>
                    </div>
                    
                </div>
                <div class='col-md-12' >
                    <?= $form->field($contractInvoiceTypeProcessModel, 'InvoiceTypeIDRef',['horizontalCssClasses' =>['label'=>'col-md-3','wrapper'=>'col-md-9']])->dropDownList($arrInvoiceTypes,
                            ['prompt' =>(($contractInvoiceTypeProcessModel->InvoiceTypeIDRef)?null:'...') ,])->label('Invoice Type') ?>
                </div>
                
        </div>
    </div>
    <div class="col-md-12 pt10 text-right" >
        <div class='row' >
            <?= Html::a('Cancel', '#',[
                'class'=>'btn btn-danger pull-right ml5',
                'data-dismiss'=>'modal',
            ]) ?>
            <?= Html::submitButton('Save',[
                'class'=>'btn btn-success pull-right ml5 ',
                'id'=>'btn-save-action',
            ]) ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<?php 
    $js = "
            function formatRepo(repo){
                if(repo.loading)
                {
                    return repo.text;
                }
                var str = repo.text;
                str = str.split('|');
                var markup = '<div class=\"container-fluid\"><div><span class=\"cando-handshake s16\"></span></div><div class=\"row\">'+

                    '<div class=\"col-lg-12 col-md-12\" >'+
                        '<div class=\"col-sm-12\">ContractNo : '+str[0]+'</div>'+
                        '<div class=\"col-sm-12\">Subcontractor : <span style=\"color:#fba113;font-family:tahoma; font-size:12px\" >'+str[1]+'</span></div>'+
                        '<div class=\"col-sm-12\">Subject : <span style=\"color:#fba113;font-family:tahoma; font-size:12px\">'+str[2]+'</span></div>'+
                    '</div>'+
                '</div></div>';
                return '<div style=\"owerflow:hidden\">'+markup+'</div>';
            }
            function formatRepoSelection(repo){
                var str = repo.text;
                str = str.split('|');
                $('#contractinvoicetypeprocess-contractidref').val(repo.id);
                $('#contract-subject').val(str[1]);
                return str[0];
                

            }
    ";
    $this->registerJs($js, \yii\web\View::POS_HEAD);
 ?>