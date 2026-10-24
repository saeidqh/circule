<?php

use yii\grid\GridView;
use app\components\Utility;
use yii\helpers\Html;

$decimalPointNumber1 = (strtoupper($invoiceModel->Currency1Title) == 'IRR') ? 0 : 2;
$decimalPointNumber2 = (strtoupper($invoiceModel->Currency2Title) == 'IRR') ? 0 : 2;

$contractCurrency1Title = $invoiceModel->contract->lastRider->currency1->CurrencyTitle;
$contractCurrency2Title = isset($invoiceModel->contract->lastRider->currency2) ? $invoiceModel->contract->lastRider->currency1->CurrencyTitle : '';
?>

<div class="row kamali" style="margin-right: -15px;margin-left: -15px;margin-top: 5px;">

    <div class="bs-example bs-example-tabs" style="text-align: left;">
        <ul id="myTab" class="nav nav-tabs" role="tablist"> 
            <li class="active"><a href="#Invoices" role="tab" data-toggle="tab">
                    <i class="fa-dollar"></i> <?= 'Invoices Detail' ?></a>
            </li>
            <li class=""><a href="#createform" role="tab" data-toggle="tab" style="<?= (in_array($actionEntityFormData["ProcessID"],[57,58]))?'':'display: none;'; ?>">
                    <i class="im-contract"></i> <?= 'Create Form' ?></a>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content">

            <div class="tab-pane fade active in" id="Invoices" style="padding:10px 0;">

                <div>
                    <div class="col-lg-8 col-md-8" style="padding:0px;">
                        <div class="panel panel-default" style="border: 1px solid #ccc;padding: 0px;margin-bottom: 0px;">
                            <div class="panel-heading">
                                <p class="m0 ml5">Create Form</p>
                            </div>
                            <div class="panel-body" style="padding:5px 0px 0px 0px;border: 0px !important;">

                                <div class="col-md-6">
                                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                                        <tbody>
                                            <tr><th width='40%'>ContractNo:</th><td width='10'></td><td><?= Html::encode($invoiceModel->contract->ContractNo); ?></td></tr>  
                                            <tr><th width='40%'>Contract Subject:</th><td width='10'></td><td><?= Html::encode($invoiceModel->contract->ContractSubject); ?></td></tr>  

                                            <tr><th>Price:</th><td></td><td>
                                                    <table class="inner-table"><tbody>
                                                            <tr><td width="200"><?= Utility::moneyFormat($invoiceModel->contract->lastRider->Price1, $decimalPointNumber1); ?></td><td class="label-silver"><?= Html::encode($contractCurrency1Title); ?></td></tr>
                                                            <tr class="top-border"><td><?php if (isset($invoiceModel->contract->lastRider->Price2)) {
    echo Utility::moneyFormat($invoiceModel->contract->lastRider->Price2, $decimalPointNumber2);
} else {
    echo '';
}; ?></td><td class="label-silver"><?= $contractCurrency2Title; ?></td></tr>
                                                        </tbody></table>
                                                </td></tr>
                                        </tbody>    
                                    </table>

                                </div>
                                <div class="col-md-6">
                                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                                        <tbody>
                                            <tr><th width='40%'>Sub Contractor:</th><td width='10'></td><td><?= !empty($invoiceModel->contract->subContractor) ? Html::encode($invoiceModel->contract->subContractor->PersonTitle) : ''; ?></td></tr>  
                                            <tr><th width='40%'>Contract Date:</th><td width='10'></td><td><?= Html::encode($invoiceModel->contract->ContractDateHijri); ?></td></tr>  
                                            <tr><th width='40%'>Finish Date:</th><td width='10'></td><td><?= Html::encode($invoiceModel->contract->lastRider->FinishDateHijri); ?></td></tr>  

                                        </tbody>    
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4 col-md-4" style="padding:0px 0px 0px 5px;">
                        <div class="panel panel-default" style="border: 1px solid #ccc;">
                            <div class="panel-heading">
                                <p class="m0">Invoice Documents</p> 
                            </div>
                            <div class="panel-body" style="padding: 0px;border: 0px !important;">
                                <?=
                                GridView::widget([
                                    'id' => 'tbl-obs-grid1',
                                    'options' => [
                                        'style' => 'margin-bottom:0px;'
                                    ],
                                    'dataProvider' => $documentRequireModelDataProvider,
                                    'columns' => [
                                        'InvoiceDocumentTypeTitle',
                                        'RequireType',
                                    ],
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div>
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="panel panel-default" style="border: 1px solid #ccc;margin-bottom: 5px;">
                            <div class="panel-heading">
                                <span class="m0 ml5">Cumulative Amounts<span>
                                        </div>
                                        <div class="panel-body" style="border: 0px !important; padding: 2px;">

                                            <?php
                                            echo GridView::widget([
                                                'dataProvider' => $amountDataProvider,
                                                'columns' => [
                                                    [
                                                        'attribute' => 'Currency',
                                                        'options' => [
                                                            'width' => '50px;'
                                                        ],
                                                        'format' => 'raw',
                                                        'value' => function($model) {
                                                    return Html::encode($model['CurrencyTitle']);
                                                },
                                                    ],
                                                    [
                                                        'label' => 'Requested',
                                                        'value' => function($model)use($invoiceModel, $decimalPointNumber1) {
                                                            if ($invoiceModel->currency1->CurrencyTitle == $model['CurrencyTitle']) {
                                                                return Utility::moneyFormat($invoiceModel->cumulativeRequested1(6), $decimalPointNumber1);
                                                            } else if (isset($invoiceModel->currency2) && $invoiceModel->currency2->CurrencyTitle == $model['CurrencyTitle']) {
                                                                return Utility::moneyFormat($invoiceModel->cumulativeRequested2(6), $decimalPointNumber1);
                                                            }
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Approved',
                                                        'value' => function($model) use($invoiceModel) {
                                                            $decimalPointNumber = (strtoupper($model['CurrencyTitle']) == 'IRR') ? 0 : 2;
                                                            if ($invoiceModel->InvoiceType == 17) {
                                                                return Utility::moneyFormat($model['LC'], $decimalPointNumber);
                                                            } else if ($invoiceModel->InvoiceType == 18) {
                                                                return Utility::moneyFormat($model['Logistic'], $decimalPointNumber);
                                                            } else if ($invoiceModel->InvoiceType == 19) {
                                                                return Utility::moneyFormat($model['Insurance'], $decimalPointNumber);
                                                            } else if ($invoiceModel->InvoiceType == 20) {
                                                                return Utility::moneyFormat($model['ServiceFee'], $decimalPointNumber);
                                                            } else if ($invoiceModel->InvoiceType == 21) {
                                                                return Utility::moneyFormat($model['Advanced'], $decimalPointNumber);
                                                            } else {
                                                                return Utility::moneyFormat($model['Approved'], $decimalPointNumber);
                                                            }
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Adjustment',
                                                        'value' => function($model)use($invoiceModel, $decimalPointNumber1) {
                                                            if ($invoiceModel->currency1->CurrencyTitle == $model['CurrencyTitle']) {
                                                                return Utility::moneyFormat($invoiceModel->cumulativeAdjustment1(6), $decimalPointNumber1);
                                                            } else if (isset($invoiceModel->currency2) && $invoiceModel->currency2->CurrencyTitle == $model['CurrencyTitle']) {
                                                                return Utility::moneyFormat($invoiceModel->cumulativeAdjustment2(6), $decimalPointNumber1);
                                                            } else {
                                                                return Utility::moneyFormat(0, $decimalPointNumber1);
                                                            }
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Income',
                                                        'value' => function($model)use($invoiceModel, $decimalPointNumber1) {
                                                            return Utility::moneyFormat($model['Income'], $decimalPointNumber1);
                                                        }
                                                    ],
                                                    [
                                                        'label' => 'Penalty',
                                                        'value' => function($model)use($invoiceModel, $decimalPointNumber1) {
                                                            return Utility::moneyFormat($model['Penalty'], $decimalPointNumber1);
                                                        }
                                                    ]
                                                ]
                                            ]);
                                            ?>

                                        </div>
                                        </div>
                                        </div>
                    
                    
                                        </div>

                <div>
                    <div class="col-md-12" style="padding: 0px;">
                        <div class="panel panel-default" style="border: 1px solid #ccc;margin-bottom:0px;">
            <div class="panel-heading">
                <p class="m0">Invoice Info</p>
            </div>
            <div class="panel-body" style="border:0px !important;padding: 15px 15px 0px 15px;">
                <div class="row">
                    <div class="col-md-3">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view'>
                            <tbody>
                                <tr><th>Invoice Type:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td ><?= Html::encode($invoiceModel->type->InvoiceType); ?></td></tr>

                                            </tbody></table>
                                    </td></tr>
                                <tr><th>Invoice No:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td ><?= Html::encode($invoiceModel->Title); ?></td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tbody>
                                <tr><th>Cut Off Date:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td><?= Yii::$app->utility->date($invoiceModel->CutOffDate); ?></td></tr>

                                            </tbody></table>
                                    </td></tr>
                                <tr><th>Date Shamsi:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td><?= Yii::$app->utility->date($invoiceModel->CutOffDateHijri); ?></td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>  

                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><th><?= 'Requested1' ?>:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td><?= Utility::moneyFormat($invoiceModel->requested1, $decimalPointNumber1); ?></td><td class="label-silver">&nbsp;<?= Html::encode($invoiceModel->Currency1Title); ?></td></tr>
                                                <tr class="top-border"><td><?= !empty($invoiceModel->Currency2Title) ? Utility::moneyFormat($invoiceModel->requested2, $decimalPointNumber2) : ''; ?></td><td class="label-silver">&nbsp;<?= Html::encode($invoiceModel->Currency2Title); ?></td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-3">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><th><?= 'Approved1' ?>:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td><?= Utility::moneyFormat($invoiceModel->approved1, $decimalPointNumber1); ?></td><td class="label-silver">&nbsp;<?= Html::encode($invoiceModel->Currency1Title); ?></td></tr>
                                                <tr class="top-border"><td><?= !empty($invoiceModel->Currency2Title) ? Utility::moneyFormat($invoiceModel->approved2, $decimalPointNumber2) : ''; ?></td><td class="label-silver">&nbsp;<?= Html::encode($invoiceModel->Currency2Title); ?></td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                </div>
                <div class="row" style="border: 1px solid #C7C7C7 !important;border-radius:5px;">
                    <div class="col-md-2">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><th><?= 'SE.1'; ?>:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td ><?= ''; ?></td><td class="label-silver">&nbsp;USD</td></tr>
                                                <tr class="top-border"><td><?= ''; ?></td><td class="label-silver">&nbsp;USD</td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-2">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td ><?= ''; ?></td><td class="label-silver">&nbsp;IRR</td></tr>
                                                <tr class="top-border"><td><?= ''; ?></td><td class="label-silver">&nbsp;IRR</td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-2">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><th><?= 'SE.2'; ?>:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                            <tr><td><?= Utility::moneyFormat($invoiceModel->approved1UsdF2, 2); ?></td><td class="label-silver">&nbsp;USD</td></tr>
                                            <tr class="top-border"><td><?= Utility::moneyFormat($invoiceModel->approved2UsdF2, 2); ?></td><td class="label-silver">&nbsp;USD</td></tr>
                                        </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-2">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><td></td><td>
                                        <table class="inner-table"><tbody>
                                            <tr><td><?= Utility::moneyFormat($invoiceModel->approved1IrrF2, 0); ?></td><td class="label-silver">&nbsp;IRR</td></tr>
                                            <tr class="top-border"><td><?= Utility::moneyFormat($invoiceModel->approved2IrrF2, 0); ?></td><td class="label-silver">&nbsp;IRR</td></tr>
                                        </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-2">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><th><?= '(SE.3)'; ?>:</th><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td><?= Utility::moneyFormat($invoiceModel->approved1UsdF3, 2); ?></td><td class="label-silver">&nbsp;USD</td></tr>
                                                <tr class="top-border"><td><?= Utility::moneyFormat($invoiceModel->approved2UsdF3, 2); ?></td><td class="label-silver">&nbsp;USD</td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                    <div class="col-md-2">
                        <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                            <tbody>
                                <tr><td></td><td>
                                        <table class="inner-table"><tbody>
                                                <tr><td><?= Utility::moneyFormat($invoiceModel->approved1IrrF3, 0); ?></td><td class="label-silver">&nbsp;IRR</td></tr>
                                                <tr class="top-border"><td><?= Utility::moneyFormat($invoiceModel->approved2IrrF3, 0); ?></td><td class="label-silver">&nbsp;IRR</td></tr>
                                            </tbody></table>
                                    </td></tr>
                            </tbody>    
                        </table>
                    </div>
                </div>

                <div class="row" style="padding-top:5px;">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                                <tbody>
                                    <tr><td></td><td>
                                            <table class="inner-table"><tbody>
                                                   <tr><td><?= 'Auto Message'; ?></td><td width='10'></td><td><?= $actionEntityFormData["AutoMessage"]; ?></td></tr>
                                                    <tr class="top-border"><td><?= 'User Message'; ?></td><td width='10'></td><td><?= $actionEntityFormData["Message"]; ?></td></tr>
                                                    <tr>
                                                        <td>Action Date</td>
                                                        <td width="20"></td>
                                                        <td><?= $actionEntityFormData["ActionDate"]; ?></td>
                                                    </tr>
                                                </tbody></table>
                                        </td></tr>
                                </tbody>   
                            </table>
                        </div>
                        <div class="col-md-6">

                                <div class="box-nextaction box-nextaction-anim box-green pull-left">
                                    <?= $actionEntityFormData["CurrentActionTitle"]; ?>
                                </div>
                                <div class="pull-left" style="padding: 7px 0px 0px 10px;">
                                    <i class="im-arrow-right s24"></i>
                                </div>

                                <div class="pull-left"> 
                                    <div class="box-nextaction box-gold">
                                       <?= $actionEntityFormData["NextActionTitle"]; ?>
                                    </div>
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
                    </div>
                    </div>

                                        </div>

                                        <div class="tab-pane fade" id="createform">
                                            <div>
                                                <div id="box" style="max-height:450px; overflow-y: scroll; margin-left: 60px;">
                                                <iframe 
                                                    src="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["invoice/invoice/createform", 'peid'=>$actionEntityFormData["ProcessEntityID"],'type'=>'na']); ?>" 
                                                    width="100%"
                                                    height="1000px"
                                                    scrolling="no"
                                                    style="border: 0px;"
                                                    name="frame-cashrequest"
                                                ></iframe>

                                                </div>
                                                <div class="pull-left">
                                                   <?= Html::a("<i class=\"im-print\"></i> Print", '#', 
                                                    [
                                                        'class' => 'btn btn-sm btn-danger',
                                                        'onclick' => 'print(); return false;'
                                                    ]); ?> 
                                                </div>
                                                
                                            </div>
                                        </div>

                                        </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-lg-12 col-md-12">
                                                <div class="pull-right p5">
<?=
Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
    'class' => 'btn btn-sm btn-success',
    "onclick" => "sign(this)",
    'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
]);
?>

<?=
Html::a('<i class="en-back"></i> back', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action',
            'type' => Yii::$app->request->get('type'),
            'peid' => Yii::$app->request->get('peid'),
            'mode' => 'check', 'clearValue' => false]), ['class' => 'btn btn-sm btn-primary'])
?>&nbsp;
<?=
Html::a("<i class=\"br-cancel\"></i> Cancel", '#', [
    'class' => 'btn btn-sm btn-warning',
    'data-dismiss' => 'modal'
]);
?>
                                                </div>
                                            </div>
                                        </div>

                                        </div>


                                                    <?php
                                                    $js = "
    function sign(obj){
        ajaxProgressLoader('show', '.modal-content');
        
       
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message, 'error');
                ajaxProgressLoader('hide', '.modal-content');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('invoice%2Finvoice%2Fview') != -1 || r.indexOf('invoice/invoice/view') != -1 ){
                    showMsgBoxInBottomRight('Invoice successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['invoice/invoice/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                else if(r.indexOf('invoice%2Fmaincontractinvoice%2Fview') != -1 || r.indexOf('invoice/maincontractinvoice/view') != -1 ){
                    showMsgBoxInBottomRight('Invoice successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['invoice/maincontractinvoice/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#invoice-modal').modal('hide');
                $.pjax.reload({
                    container:'#invoice-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl([
                                                                'workflow/workflow/invoice', 'type' => 'ToDo',
                                                            ]) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
            }
            ajaxProgressLoader('hide', '.modal-content');
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide', '#check-modal');
        });
        return false;
    };
";
                                                    $this->registerJs($js, \yii\web\View::POS_READY);
                                                    $css = "
    .box-nextaction{
        text-align:center;
        width:165px;
        padding:5px;
        margin-bottom:15px;
        margin-left:5px;
        height:40px;
        overflow: hidden;
        color:white;
        
    }
    .box-green{
    
        background:#3ccc3c;
    }
    .box-gold{
        background:#ecb61e;
    }
    
    .box-nextaction-anim{
        background-color: #34ea28;
        -webkit-animation: activeOwghatEffect 2s infinite;
        -moz-animation: activeOwghatEffect 2s infinite;
        -ms-animation: activeOwghatEffect 2s infinite;
    }
    .modal-body {
        position: relative;
        padding: 0px;
    }

";
                                                    $this->registerCss($css);

$js = "
    function print(){
       window.frames['frame-cashrequest'].focus();
       window.frames['frame-cashrequest'].print();
       return  false;
    }
    
";
$this->registerJs($js, \yii\web\View::POS_END);
?>
                                                    