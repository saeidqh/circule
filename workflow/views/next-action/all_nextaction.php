<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<?php
if (!empty($processEntityModel->InvoiceIDRef)) {
    echo $this->render('__invoiceInfo', array(
        'peModel' => $processEntityModel,
    ));
} elseif (!empty($processEntityModel->CashRequestIDRef)) {
    echo $this->render('__cashRequestInfo', array(
        'peModel' => $processEntityModel,
    ));
} elseif (!empty($processEntityModel->WorkflowContractIDRef)) {
    echo $this->render('__contractInfo', [
        'peModel' => $processEntityModel,
    ]);
} elseif (!empty($processEntityModel->warrantyIdRef)) {

    echo $this->render('__warrantyInfo', [
        'peModel' => $processEntityModel,
    ]);
} elseif (!empty($processEntityModels[0]->BudgetIDRef)) {

    echo $this->render('__allGridbudget', [
        'dataProvider' => $dataProvider,
        'processEntityModels' => $processEntityModels,
    ]);
} elseif (!empty($processEntityModel->capitalBalanceIdRef)) {

    echo $this->render('__capitalBalanceInfo', [
        'peModel' => $processEntityModel,
    ]);
} elseif (!empty($processEntityModel->claimIdRef)) {
    echo $this->render('__claimInfo', [
        'peModel' => $processEntityModel,
    ]);
} elseif (!empty($processEntityModel->debitmemoIdRef)) {

    echo $this->render('__debitmemoInfo', [
        'peModel' => $processEntityModel,
    ]);
}
?>
<div class="row" >
    <div class="col-md-12 col-lg-12">
        <?php
        $message = Yii::$app->session->getAllFlashes();
        if (isset($message['danger'])) {
            for ($i = 0; $i < count($message['danger']); $i++) {
                echo kartik\widgets\Alert::widget([
                    'type' => kartik\widgets\Alert::TYPE_DANGER,
                    'icon' => 'glyphicon glyphicon-alert',
                    'body' => $message['danger'][$i],
                ]);
            }
        }
        ?>
        <?php
        $form = ActiveForm::begin([
                    'id' => 'next-action-form',
                    'enableClientValidation' => false,
                    'action' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/all-sign', 'peids' => $peids]),
                    'method' => 'post',
                    'options' => [
                        'class' => 'form-horizontal',
                        'data-pjax' => 1
                    ],
                    'fieldConfig' => [
                        'template' => '<div class="col-lg-3 col-md-3 text-left">{label}</div><div class="col-lg-9 col-md-9">{input}{error}</div>',
                    ],
        ]);
        ?>


        <?=
        $form->field($nextactionForm, 'actionAutoMessage', [
            'options' => [
                'class' => ''
            ]
        ])->textarea(['rows' => '3', 'style' => 'direction:rtl;text-align:right;']);
        ?>


        <div class="field-actionentity-actionmessage">
            <?=
            $form->field($nextactionForm, 'fromActionId', [
                'options' => [
                    'class' => ''
                ]
            ])->dropDownList([$currentActionModel->ActionID => $currentActionModel->ActionTitle1], array(
                'readonly' => 'readonly',
                'class' => 'form-control input-sm',
            ))->label('Current Step');
            ?>
        </div>

        <?=
        $form->field($nextactionForm, 'actionId', [
            'options' => [
                'class' => ''
            ]
        ])->dropDownList($dropDownDataActions, array(
            'class' => 'form-control input-sm',
            'onchange' => 'updateform(this)',
            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/all-next-action', 'peids' => $peids]),
        ))->label('Next Action');
        ?>

<?php if (!empty($dropDownDataAutoMessage)): ?>
    <?=
    $form->field($nextactionForm, 'ActionAutoMessage')->dropDownList($dropDownDataAutoMessage, [
        'class' => 'form-control input-sm',
    ]);
    ?>
                        <?php endif; ?>
        <div class="row">
            <div class="col-lg-3 col-md-3 text-left" style="padding-left: 30px;"><label class="control-label" for="actionentity-actionidref">Action Date</label></div>
            <div class="col-lg-9 col-md-9" style="padding-left: 7px;">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <?=
                        $form->field($nextactionForm, 'actionDateHijri', [
                            'template' => '<div class="col-lg-12 col-md-12">{input}</div>{error}',
                        ])->textInput([
                            'class' => 'form-control input-sm datepicker-fa',
                            'readonly' => $nextactionForm->validate(['actionDateHijri'], false) ? true : false
                        ])->label('');
                        ?>
                    </div>
                    <div class="col-lg-6 col-md-6">
        <?=
        $form->field($nextactionForm, 'actionDate', [
            'template' => '<div class="col-lg-12 col-md-12">{input}</div>{error}',
        ])->textInput([
            'class' => 'form-control input-sm  pull-right',
            'readonly' => 'readonly',
        ])->label('');
        ?>
                    </div>
                </div>
            </div>
        </div><br> 
                    <?=
                    $form->field($nextactionForm, 'letterNumber')->textInput([
                        'class' => 'form-control input-sm',
                    ]);
                    ?>
        <hr>
        <div class="row">
            <div id="box-create-letter" style="display: <?= isset($message['danger'])?'none':'block'?>">
                <div class="col-md-2 pull-left">
                    <?=
                    Html::a('<span class="fa-envelope"></span> Create Letter', $url = Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/generate-letter', 'peid' => Yii::$app->request->get('peid')]), [
                        'onclick' => "showModalCreateLetter(this); return false;",
                        'class' => 'btn btn-info pull-left ml10 btn-sm',
                        'id' => 'btn-create-letter'
                            ]
                    );
                    ?>
                    <?=
                    Html::a('<span class="fa-envelope"></span> Create Excel', '#', [
                        'export_url' => $url,
                        'style' => 'display:none',
                        'id' => 'btn-create-excel',
                        'onClick' => 'exportExcelFile(this);',
                        'class' => 'btn btn-info pull-left ml10 btn-sm col-md-12',
                        'data-pjax' => '0',
                        'target' => '_blank'
                    ]);
                    ?>
                </div>
                <div class="col-md-6 pull-left p0 m0">
                    <?=
                    Html::dropDownList('LetterID', '', $letterTitlesMap, array(
                        'class' => ' form-control input-sm',
                        'direction' => 'rtl',
                        'id' => 'drop-letter',
                    ));
                    ?>
                    <?php
                    //دکمه های ایجاد نامه ممکن است که یک ویو ویا یک فایل اکسل یا  فایل ورد 
                    //
                        $js = "
                            $('#drop-letter').change(function(){
                                var value = $(this).find(':selected').val();
                                if(value == 12 || value == 2 || value == 14  || value == 15)
                                {
                                    $('#btn-create-letter').hide();
                                    $('#btn-create-excel').show();
                                }
                                else{
                                    $('#btn-create-letter').show();
                                    $('#btn-create-excel').hide();
                                }
                            });
                        ";
                    $this->registerJs($js, \yii\web\View::POS_READY);
                    ?>
                </div>
            </div>
            <div class="col-md-4 pull-right">
                <a class="btn btn-warning pull-right ml10 btn-sm" data-pjax="0" data-dismiss="modal"><i class="br-cancel"></i> Cancel</a>
<?php 
if (!isset($message['danger'])) {
    echo Html::submitButton('<i class="fa-ok"></i> Sign and Close', [
    'class' => 'btn btn-sm btn-success pull-right',
//                            "onclick" => "sign(this)",
//                            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/all-sign', 'peids' => $peids, 'faId' => $nextactionForm->fromActionId, 'aId' => $nextactionForm->actionId])
]);
}
?>
            </div>
        </div>
<?= $form->field($nextactionForm, 'processEntityIDs')->hiddenInput()->label(''); ?>
<?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$js = "
    $(document).ready(function(){
        renderCreateLetter();
        renderLetterNumber();   
    });
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
        var ActionIDRef = '" . $nextactionForm->actionId . "';
        var letterTitle  = '" . (empty($letterTitlesMap) ? '1' : '0') . "';
        if(ActionIDRef == '' || letterTitle == '1'){
            $('#box-create-letter').hide();
        }
    }
    
    function renderLetterNumber(){
        var ActionIDRef = '" . $nextactionForm->actionId . "';
        var LetterNumberValidate  = '" . ($nextactionForm->validate(['letterNumber'], true) ? '1' : '0') . "';
        var letterId = '" . $nextactionForm->letterNumber . "';
        if(ActionIDRef == '' || (LetterNumberValidate != '0' && letterId == '')){
            $('.field-nextactionform-letternumber').hide();
        }
    }
    
   function exportExcelFile(selector) {
        var url=$(selector).attr('export_url');                                 
        $(selector).attr('href',url+'&lid='+$('#drop-letter').val());    
    }    
   
";
$this->registerJs($js, \yii\web\View::POS_READY);

$js = "
    function sign(obj){
        
        ajaxProgressLoader('show', '.modal-content');
//        ajaxProgressLoader('show', '#claim-list-pjax');
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
//            ajaxProgressLoader('hide', '#check-modal');
//            ajaxProgressLoader('hide', '#claim-list-pjax');
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('claim%2Fclaim%2Fview') != -1 || r.indexOf('claim/claim/view') != -1 ){
                    showMsgBoxInBottomRight('Claim successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['claim/claim/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#claim-modal').modal('hide');
                $.pjax.reload({
                    container:'#claim-list-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl([
            'workflow/workflow/claim', 'type' => 'ToDo',
        ]) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide', '#check-modal');
            ajaxProgressLoader('hide', '#claim-list-pjax');
        });
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_READY);
