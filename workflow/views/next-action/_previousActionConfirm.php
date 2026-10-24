<?php

use app\components\Utility;
use yii\widgets\DetailView;
use yii\helpers\Html;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);
$previousActionUrl = ['workflow/next-action/previous-action', "peid" => $processEntityModel->ProcessEntityID];
?>
<?php

$attributes = [
    [
        'label' => 'ProcessEntity ID',
        'attribute' => 'ProcessEntityID',
    ],
    [
        'label' => 'ProcessTitle',
        'value' => $processEntityModel->process->ProcessTitle,
    ],
    [
        'label' => 'Last Action',
        'value' => $processEntityModel->lastAction->ActionTitle2,
    ]
];

if ($processEntityModel->InvoiceIDRef > 0) {
    array_push($attributes, [
        'label' => 'Invoice ID',
        'value' => $processEntityModel->InvoiceIDRef,
    ]);
    array_push($attributes, [
        'label' => 'Invoice No.',
        'value' => $processEntityModel->invoice->Title,
    ]);
} elseif ($processEntityModel->CashRequestIDRef > 0) {
    array_push($attributes, [
        'label' => 'CashRequestID',
        'value' => $processEntityModel->CashRequestIDRef,
    ]);
    array_push($attributes, [
        'label' => 'CashRequest Description',
        'value' => $processEntityModel->cashrequest->Description,
    ]);
} elseif ($processEntityModel->WorkflowContractIDRef > 0) {
    
} elseif ($processEntityModel->BudgetIDRef > 0) {
    
} elseif ($processEntityModel->capitalBalanceIdRef > 0) {
    
} elseif ($processEntityModel->warrantyIdRef > 0) {
    
} elseif ($processEntityModel->debitmemoIdRef > 0) {
    
}


echo DetailView::widget([
    'id' => '',
    'model' => $processEntityModel,
    'attributes' => $attributes,
]);
echo Html::a('<span class="br-cancel"></span> Cancel', '#', [
    'class' => 'btn btn-sm btn-warning mr10',
    'data-dismiss' => 'modal'
]);
echo Html::a('<span class="im-arrow-left2"></span> Bach to Previous', '#', [
    'class' => 'btn btn-sm btn-danger',
    'onclick' => 'backToPreviousAction()',
]);
$js = "
    function backToPreviousAction(obj)
    {
        swal({
            title:'Are you sure?',
            text:'Are you sure to go back?!',
            type:'warning',
            showCancelButton:true,
            colseOnConfrim:false,
            showLoaderOnConfrim:true,
        },
        function(){
            $.ajax({
                url:'" . Yii::$app->urlManager->createAbsoluteUrl($previousActionUrl) . "',
            })
            .done(function(data){
                if(data.result){
                    var dt = data.data;
                    swal('Go Back', data.message, 'success');
                    location.reload();
                }
                else{
                    swal('Go Back', data.message, 'error');
                }
            });
        });
    }
    ";
$this->registerJs($js, yii\web\View::POS_END);
?>