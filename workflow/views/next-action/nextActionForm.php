<?php
/* @var $this yii\web\View */
/* @var $actionEntityModel app\modules\workflow\models\ActionEntity */
/* @var $processEntityModel app\modules\workflow\models\ProcessEntity */

use app\components\GridViewBox;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\assets\JdateTimeAsset;
use app\assets\DatepickerAsset;
use app\modules\workflow\models\Process;
JdateTimeAsset::register($this);
DatepickerAsset::register($this);
?>
<div class="row" >
    <div class="col-md-12 col-lg-12">
            <?php GridViewBox::begin([
                'htmlOptions' => array(
                    'style' => 'background:rgb(251, 249, 255);margin-bottom:5px;',
                ),
            ]);?>
                
            <?php 
            if (!empty($processEntityModel->InvoiceIDRef)) {
                echo $this->render('__invoiceInfo', array(
                    'peModel' => $processEntityModel,
                ));
            } elseif (!empty($processEntityModel->CashRequestIDRef)) {
                echo $this->render('__cashRequestInfo', array(
                    'peModel' => $processEntityModel,
                ));
            }elseif(!empty($processEntityModel->WorkflowContractIDRef)){
                echo $this->render('__contractInfo', [
                    'peModel' => $processEntityModel,
                ]);
            } elseif (!empty($processEntityModel->warrantyIdRef)) {
                
                echo $this->render('__warrantyInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }
            elseif (!empty($processEntityModel->BudgetIDRef)) {
                
                echo $this->render('__budgetInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->capitalBalanceIdRef)) {
                
                echo $this->render('__capitalBalanceInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->claimIdRef)) {
                echo $this->render('__claimInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->actualProgressAnnualBudgetIdRef)) {
                echo $this->render('__actualProgressInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->permissionId)) {
                echo $this->render('__permissionInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->PersonID)) {
                echo $this->render('__sourcingInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->debitmemoIdRef)) {
                echo $this->render('__debitmemoInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->TenderIDRef)) {
                echo $this->render('__tenderInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->ContractClosingDocumentIDRef)) {
                echo $this->render('__contractClosingDocumentInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->AutomationIDRef)) {
                echo $this->render('__automationInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }elseif (!empty($processEntityModel->NetworkIDRef)) {
                echo $this->render('__networkInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }else if ($processEntityModel->process->ProccessTypeIDRef == Process::EMPLOYEESETTLEMENT_PROCESS_TYPE) {
                echo $this->render('__employeeSettlementInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }else if ($processEntityModel->process->ProccessTypeIDRef == Process::EMPLOYEE_MEMBERSHIP_PROCESS_TYPE) {
                echo $this->render('__employeeMembershipInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }else if ($processEntityModel->process->ProccessTypeIDRef == Process::FOREIGN_MISSION_PROCESS_TYPE) {
                echo $this->render('__foreignMissionInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }else if ($processEntityModel->process->ProccessTypeIDRef == Process::STAMP_DOCUMENT_PROCESS_TYPE) {
                echo $this->render('__stampDocumentInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }else if ($processEntityModel->process->ProccessTypeIDRef == Process::REFUND_STAMP_PROCESS_TYPE) {
                echo $this->render('__stampRefundInfo', [
                    'peModel' => $processEntityModel,
                ]);
            }
            ?>
            <?php GridViewBox::end(); ?>
            <?php 
            $message = Yii::$app->session->getAllFlashes();
            if(isset($message['danger'])){
                //====Add Link=====
                $isStampDocument = ($processEntityModel->process->ProccessTypeIDRef == Process::STAMP_DOCUMENT_PROCESS_TYPE);
            $hasBeneficiaryError = false; 
            foreach($message['danger'] as $msg){
                if(strpos($msg, 'بهره‌بردار') !== false){
                    $hasBeneficiaryError = true;
                    break;
                }
            }
            $linkAdded = false;
            //=====end=======
                for($i=0; $i<count($message['danger']); $i++){
            //====Add loop======
                        $body = $message['danger'][$i];
                    
                    if($isStampDocument && $hasBeneficiaryError && strpos($body, 'بهره‌بردار') !== false && !$linkAdded){
                        $stampId = $processEntityModel->StampIDRef;
                        $url = \yii\helpers\Url::to(['/document/stamp-flow-document/create', 'sid' => $stampId]);
                        $body .= '<br><br>';
                        $body .= '<a href="#" onclick="showBeneficiaryModalFromAlert(\'' . $url . '\', ' . $stampId . '); return false;" ';
                        $body .= 'style="color:#ff9800; text-decoration:underline; cursor:pointer; font-weight:bold;">';
                        $body .= '🔍 برای ثبت بهره‌بردار جدید کلیک کنید</a>';
                        $linkAdded = true;
                    }
            //======end loop==========
                    echo kartik\widgets\Alert::widget([
                        'type' => kartik\widgets\Alert::TYPE_DANGER,
                        'icon' => 'glyphicon glyphicon-alert',
                        'body' => $body,
                    ]);
                }
            }
            ?>
            <?php $form = ActiveForm::begin([
                'id' => 'next-action-form',
                'enableClientValidation' => false,
                'action' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'peid' => Yii::$app->request->get('peid'), 'sort' => Yii::$app->request->get('sort'), 'page' => Yii::$app->request->get('page'),]),
                'method' => 'post',
                'options' => [
                    'class' => 'form-horizontal',
                    'data-pjax' => '1'
                ],
                'fieldConfig' => [
                    'template' => '<div class="col-lg-3 col-md-3 text-left">{label}</div><div class="col-lg-9 col-md-9">{input}{error}</div>',
                ],
            ]);?>
                  
                
                    <?= $form->field($actionEntityModel, 'ActionMessage', [
                        'options' => [
                            'class' => ''
                        ]
                    ])->textarea(['rows' => '3', 'style' => 'direction:rtl;text-align:right;', 'value' => $actionEntityModel->fetchStoreValue('ActionMessage')]); ?>
                    
                    
                    <div class="field-actionentity-actionmessage">
                        <div class="col-lg-3 col-md-3 text-left">
                            <label class="control-label" for="ActionTitle1"><?= Yii::t('workflow','Current Step') ?></label>
                        </div>
                        <div class="col-lg-9 col-md-9" style="margin-bottom: 5px;">
                            <?= Html::textInput('ActionTitle1', $currentActionModel->ActionTitle1 . "", array(
                                    'readonly' => true,
                                    'class' => 'form-control input-sm',
                                    'id' => 'ActionTitle1',
                                ));?>
                            <?= Html::hiddenInput("ActionTitle1", $currentActionModel->ActionTitle1); ?>
                        </div>
                    </div>

                    <?= $form->field($actionEntityModel, 'ActionIDRef', [
                        'options' => [
                            'class' => ''
                        ]
                    ])->dropDownList($dropDownDataActions, array(
                        'class' => 'form-control input-sm',
                        'value' => $actionEntityModel->fetchStoreValue('ActionIDRef'),
                        'onchange' => 'updateform(this)',
                        'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action','peid' => Yii::$app->request->get('peid')]),
                    )); ?>
        
                    <?php if(!empty($dropDownDataAutoMessage)): ?>
                    <?php
                    $actionEntityModel->ActionAutoMessage = $actionEntityModel->fetchStoreValue('ActionAutoMessage');
                    ?>
                    <?= $form->field($actionEntityModel, 'ActionAutoMessage')->dropDownList($dropDownDataAutoMessage, [
                        'class' => 'form-control input-sm',
                    ]); ?>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-lg-3 col-md-3 text-left" style="padding-left: 30px;"><label class="control-label" for="actionentity-actionidref"><?= Yii::t('workflow','Action Date') ?></label></div>
                        <div class="col-lg-9 col-md-9" style="padding-left: 7px;">
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                <?= $form->field($actionEntityModel, 'ActionDateHijri', [
                                     'template' => '<div class="col-lg-12 col-md-12">{input}</div>{error}',
                                ])->textInput([
                                    'class' => 'form-control input-sm datepicker-fa',
                                    'disabled' => $actionEntityModel->validate(['ActionDateHijri'], false) ? true:false
,                                    'value' => $actionEntityModel->fetchStoreValue('ActionDateHijri')
                                ])->label(''); ?>
                                </div>
                                <div class="col-lg-6 col-md-6">
                                <?= $form->field($actionEntityModel, 'ActionDate', [
                                     'template' => '<div class="col-lg-12 col-md-12">{input}</div>{error}',
                                ])->textInput([
                                    'class' => 'form-control input-sm  pull-right',
                                    'readonly' => 'readonly',
                                    'value' => $actionEntityModel->fetchStoreValue('ActionDate')
                                ])->label(''); ?>
                                </div>
                            </div>
                        </div>
                    </div><br> 
                    <?= $form->field($actionEntityModel, 'LetterNumber')->textInput([
                        'class' => 'form-control input-sm',
                        'value' => $actionEntityModel->fetchStoreValue('LetterNumber'),
                   ]); ?>
                <hr>
                <div class="row">
                    <div id="box-create-letter">
                    <div class="col-md-2 pull-left">
                        <?= Html::a('<span class="fa-envelope"></span> Create Letter', 
                                $url = Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/generate-letter', 'peid' => Yii::$app->request->get('peid')]), 
                            [
                                'onclick' => "showModalCreateLetter(this); return false;",
                                'class' => 'btn btn-info pull-left ml10 btn-sm',
                                'id' => 'btn-create-letter'
                            ]
                        );?>
                        <?= Html::a('<span class="fa-envelope"></span> Create Excel','#', 
                            [
                                'export_url'=>$url,
                                'style' => 'display:none',
                                'id' => 'btn-create-excel',
                                'onClick' => 'exportExcelFile(this);',
                                'class' => 'btn btn-info pull-left ml10 btn-sm col-md-12',
                                'data-pjax' => '0',
                                'target' =>'_blank'
                            ]); ?>
                        <?= Html::a('<span class="fa-print"></span> Create Form','#', 
                            [
                                'export_url'=>Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/create-form', 'peid' => Yii::$app->request->get('peid')]),
                                'style' => 'display:none',
                                'id' => 'btn-create-form',
                                'onClick' => 'exportCrateForm(this);',
                                'class' => 'btn btn-info pull-left ml10 btn-sm col-md-12',
                                'data-pjax' => '0',
//                                'target' =>'_blank'
                            ]); ?>
                        
                    </div>
                    <div class="col-md-6 pull-left p0 m0">
                        <?= Html::dropDownList('LetterID', '', $letterTitlesMap, array(
                            'class' => ' form-control input-sm',
                            'direction' => 'rtl',
                            'id' => 'drop-letter',
                        ));?>
                        <?php
                        //دکمه های ایجاد نامه ممکن است که یک ویو ویا یک فایل اکسل یا  فایل ورد 
                        //
                        $js = "
                            $('#drop-letter').change(function(){
                                changeExportStatus();
                            });
                        ";
                        $this->registerJs($js, \yii\web\View::POS_READY);
                        ?>
                    </div>
                    </div>
                    <div class="col-md-4 pull-right">
                        <a class="btn btn-warning pull-right ml10 btn-sm" data-pjax="0" data-dismiss="modal"><i class="br-cancel"></i> Cancel</a>
                        <?=  Html::submitButton('<i class="im-checkmark-circle"></i> check', 
                            [
                                'class' => 'btn btn-sm btn-primary pull-right',
                            ]);
                        ?>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>
    </div>
</div>
<script>
function showBeneficiaryModalFromAlert(url, stampId) {
    if (typeof ajaxProgressLoader !== 'undefined') {
        ajaxProgressLoader('show', '.modal-content');
    }

    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'html',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {

            if ($('#beneficiary-modal').length === 0) {
                $('body').append(
                    '<div class="modal fade" id="beneficiary-modal" tabindex="-1" role="dialog">' +
                        '<div class="modal-dialog modal-lg" role="document">' +
                            '<div class="modal-content">' +
                                '<div class="modal-header">' +
                                    '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                                    '<h4 class="modal-title">ثبت بهره‌بردار جدید</h4>' +
                                '</div>' +
                                '<div class="modal-body" id="beneficiary-modal-body"></div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            }

            $('#beneficiary-modal-body').html(data);
            $('#beneficiary-modal').modal('show');

            if (typeof ajaxProgressLoader !== 'undefined') {
                ajaxProgressLoader('hide', '.modal-content');
            }
        },
        error: function() {
            if (typeof ajaxProgressLoader !== 'undefined') {
                ajaxProgressLoader('hide', '.modal-content');
            }

            if (typeof showMsgBoxInBottomRight !== 'undefined') {
                showMsgBoxInBottomRight('خطا در بارگذاری فرم بهره‌بردار!', 'error');
            } else {
                alert('خطا در بارگذاری فرم بهره‌بردار!');
            }
        }
    });
}

$(document).off('submit.beneficiary', '#beneficiary-modal form')
    .on('submit.beneficiary', '#beneficiary-modal form', function(e) {

        e.preventDefault();

        var form = $(this);
        var actionUrl = form.attr('action');

        if (!actionUrl) {
            actionUrl = window.location.href;
        }

        if (typeof ajaxProgressLoader !== 'undefined') {
            ajaxProgressLoader('show', '#beneficiary-modal .modal-content');
        }

        var formData = new FormData(this);

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'html',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {

                $('#beneficiary-modal-body').html(response);

                if (typeof ajaxProgressLoader !== 'undefined') {
                    ajaxProgressLoader('hide', '#beneficiary-modal .modal-content');
                }
            },
            error: function(xhr) {
                if (typeof ajaxProgressLoader !== 'undefined') {
                    ajaxProgressLoader('hide', '#beneficiary-modal .modal-content');
                }

                if (typeof showMsgBoxInBottomRight !== 'undefined') {
                    showMsgBoxInBottomRight('خطا در ثبت بهره‌بردار!', 'error');
                } else {
                    alert('خطا در ثبت بهره‌بردار!');
                }
            }
        });

        return false;
    });

function flowCallbackPjax(data) {
    if (data.result) {
        $('#beneficiary-modal').modal('hide');

        if (typeof showMsgBoxInBottomRight !== 'undefined') {
            showMsgBoxInBottomRight(data.message, 'success');
        } else {
            alert(data.message);
        }

        location.reload();

    } else {
        if (typeof showMsgBoxInBottomRight !== 'undefined') {
            showMsgBoxInBottomRight(data.message, 'error');
        } else {
            alert(data.message);
        }
    }
}

function beneficiaryCallbackPjax(data) {
    flowCallbackPjax(data);
}
</script>
<?php
$js = "
    $(document).ready(function(){
        renderCreateLetter();
        renderLetterNumber();
        changeExportStatus();
    });
    
    function changeExportStatus(){
        var value = $('#drop-letter').find(':selected').val();
        if(value == 12 || value == 2 || value == 14  || value == 15)
        {
            $('#btn-create-excel').show();
            $('#btn-create-letter').hide();
            $('#btn-create-form').hide();
        }
        else{
            $('#btn-create-letter').show();
            $('#btn-create-excel').hide();
            $('#btn-create-form').hide();
        }
//                               Create From   
        if(value == 19 || value == 21)
        {
            $('#btn-create-form').show();
            $('#btn-create-letter').hide();
            $('#btn-create-excel').hide();
        }
    }
    

    function updateform(obj){
         ajaxProgressLoader('show','.ajax-loading-next-action');
        var ajaxUrl = $(obj).attr('ajax-url');
        var data= $('#next-action-form').serialize();
        data += '&dropDown=true';
        var key= $(obj).attr('peid');
        key = '#tr-child-row-'+key
        $.ajax({
            url:ajaxUrl, 
            type:'POST',
            data: data,
        }).done(function(data){
            $('#pjax-modal').html(data);    
            ajaxProgressLoader('hide','.ajax-loading-next-action');
        }).error(function(){
            showMsgBoxInBottomRight('error!','erroe');
            ajaxProgressLoader('hide','.ajax-loading-next-action');
        });
    }
    
    function renderCreateLetter(){
        var ActionIDRef = '".$actionEntityModel->fetchStoreValue('ActionIDRef', $actionEntityModel->ActionIDRef)."';
        var letterTitle  = '".(empty($letterTitlesMap) ? '1':'0')."';
        if(ActionIDRef == '' || letterTitle == '1'){
            $('#box-create-letter').hide();
        }
    }
    
    function renderLetterNumber(){
        var ActionIDRef = '".$actionEntityModel->fetchStoreValue('ActionIDRef', $actionEntityModel->ActionIDRef)."';
        var LetterNumberValidate  = '".($actionEntityModel->validate(['LetterNumber'], true) ? '1':'0')."';
        var letterId = '".$actionEntityModel->LetterNumber."';
        if(ActionIDRef == '' || (LetterNumberValidate != '0' && letterId == '')){
            $('.field-actionentity-letternumber').hide();
        }
    }
    
    function exportExcelFile(selector) {
        var url=$(selector).attr('export_url');                                 
        $(selector).attr('href',url+'&lid='+$('#drop-letter').val());    
    }    

    function exportCrateForm(selector) {
        var url=$(selector).attr('export_url');         
        window.open(url+'&lid='+$('#drop-letter').val(),'newwindow','width=700,height=660,scrollbars=yes,resizable=no');
    }    
   
";

$this->registerJs($js, \yii\web\View::POS_READY);

