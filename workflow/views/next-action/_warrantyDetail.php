<?php

use yii\helpers\Html;

echo\kartik\detail\DetailView::widget([
    'model' => $processEntityModel,
    'attributes' => [
        [
            'value' => Html::encode($processEntityModel->warranty->contract->ContractNo),
            'label' => 'ContractNo',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->subject->warrantySubjectTitle),
            'label' => 'Warranty Subject',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->type->warrantyTypeTitle),
            'label' => 'Warranty Type',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->warrantyAmount) . Html::encode($processEntityModel->warranty->currency->CurrencyTitle),
            'label' => 'Amount',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->bankName) . ' -- ' . Html::encode($processEntityModel->warranty->bankBrunch),
            'label' => 'bankName',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->bankAddress),
            'label' => 'bankAddress',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->bankPhone),
            'label' => 'bankPhone',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->recieveDateHijri),
            'label' => 'Recieve Date',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->issueDateHijri),
            'label' => 'Issue Date',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->expireDateHijri),
            'label' => 'Expire Date',
        ],
        [
            'value' => Html::encode($processEntityModel->warranty->description),
            'label' => 'Description',
        ],
    ]
]);
?>
<div class="pull-right">
    <?= Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
            'class' => 'btn btn-sm btn-success', 
            "onclick" => "sign(this)", 
            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
            ]); ?>
   
    <?= Html::a('<i class="en-back"></i> back', 
            Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action','peid' => Yii::$app->request->get('peid'), 'clearValue' => false]),
            [
                'class' => 'btn btn-sm btn-primary'
            ]) ?>&nbsp;
        
    <?= Html::a("<i class=\"br-cancel\"></i> Cancel", '#', 
        [
            'class' => 'btn btn-sm btn-warning',
            'data-dismiss' => 'modal'
        ]); ?>
</div>
<?php
$js = "
    function sign(obj){
        var r = window.location.search;
        if(r.indexOf('warranty%2Fwarranty%2Fview') != -1 || r.indexOf('warranty/warranty/view') != -1 ){
            window.location = '".Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/warranty', 'type' => 'ToDo'])."';
        }
         ajaxProgressLoader('show', '.modal-content');
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
           var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                $('#warranty-modal').modal('hide');
                $.pjax.reload({
                    container:'#contract-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/warranty', 'type' => 'ToDo']) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide',parentBox);
        });
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_READY);
