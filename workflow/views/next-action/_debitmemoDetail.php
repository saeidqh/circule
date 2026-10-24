<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Utility;

?>

<div class="row">
    <div class="col-lg-12 col-md-12" style="padding:0px;">

        <div class="panel panel-default" style="border: 1px solid #ccc;padding: 0px;margin-bottom: 0px;">
            <div class="panel-body" style="padding:5px 0px 0px 0px;border: 0px !important;">

                <div class="col-md-6">
                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                        <tbody>
                            <tr><th width='40%'>Contract NO:</th><td width='10'></td><td><?= Html::encode($processEntityModel->debitmemo->contract->ContractNo); ?></td></tr>  
                            <tr><th width='40%'>Debitmemo ID:</th><td width='10'></td><td><?= Html::encode($processEntityModel->debitmemo->id); ?></td></tr>  
                            <tr><th width='40%'>Description:</th><td width='10'></td><td><?= Html::encode($processEntityModel->debitmemo->description); ?></td></tr>  
                        </tbody>    
                    </table>

                </div>
                <div class="col-md-6">
                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                        <tbody>
                            <tr><th width='40%'>Contract Subject:</th><td width='10'></td><td><?= Html::encode($processEntityModel->debitmemo->contract->ContractSubject); ?></td></tr>  
                            <tr><th width='40%'>Debitmemo NO:</th><td width='10'></td><td><?= Html::encode($processEntityModel->debitmemo->debitmemoNo); ?></td></tr>  
                            <tr><th width='40%'>Cut Off Date:</th><td width='10'></td><td><?= Html::encode($processEntityModel->debitmemo->debitmemoDateHijri); ?></td></tr>  
                        </tbody>      
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>

<ul class="nav nav-tabs">
    <li class="nav active"><a href="#debit-amount-grid" data-toggle="tab"><span class="fa-dollar s16"></span> Debitmemo Amounts</a></li>
    <li class="nav"><a href="#debit-invoice" data-toggle="tab"><span class="im-info s16"></span> Invoice Info</a></li>
</ul>
<div class="tab-content">
<div class="tab-pane fade" id="debit-amount-grid">
        <?php
        echo kartik\grid\GridView::widget([
            'id' => 'debit-amount-grid',
            'dataProvider' => $debitmemoAmountDataProvider,
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'id' => 'debit-amount-grid-pjax',
                ],
            ],
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'value' => 'id',
                ],
                [
                    'attribute' => 'invoiceNo',
                    'label' => 'Invoice No',
                    'value' => 'invoiceNo',
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'nodeFullCode',
                    'label' => 'CostCode',
                    'value' => function($model) {
                        return Utility::generateCostCodeLink($model->nodeId, $model->nodeFullCode);
                    },
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'currency',
                    'label' => 'Currency',
                    'value' => function($model) {
                        $res = '<table class="inner-table">';
                        $res .= '<tr><td width=\'200\'>';
                        $res .= Html::encode($model->currency1Title);
                        $res .= '</td></tr>';
                        if (isset($model->currency2Title)) {
                            $res .= '<tr class="top-border"><td >';
                            $res .= Html::encode($model->currency2Title);
                            $res .= '</td></tr>';
                        }
                        $res .= '</table>';
                        return $res;
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'invoiceApproved',
                    'label' => 'Invoice Approved',
                    'value' => function($model) {
                        $res = '<table class="inner-table">';
                        $res .= '<tr><td width=\'200\'>';
                        $res .= Utility::moneyFormat($model->AmountApproved1, ($model->currency1IdRef != 1) ? 2 : null);
                        $res .= '</td></tr>';
                        if (isset($model->currency2)) {
                            $res .= '<tr class="top-border"><td >';
                            $res .= Utility::moneyFormat($model->AmountApproved2, (isset($model->currency2) && $model->currency2IdRef != 1) ? 2 : null);
                            $res .= '</td></tr>';
                        }
                        $res .= '</table>';
                        return $res;
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'requested',
                    'label' => 'Debitmemo Requested',
                    'value' => function($data) {
                        $res = '<table class="inner-table">';
                        $res .= '<tr><td width=\'200\'>';
                        $res .= Utility::moneyFormat($data["requested1"], ($data->currency1IdRef != 1) ? 2 : null);
                        $res .= '</td></tr>';
                        if (isset($data->currency2)) {
                            $res .= '<tr class="top-border"><td >';
                            $res .= Utility::moneyFormat($data["requested2"], (isset($data->currency2) && $data->currency2IdRef != 1) ? 2 : null);
                            $res .= '</td></tr>';
                        }
                        $res .= '</table>';
                        return $res;
                    }
                ],
                [
                    'format' => 'raw',
                    'attribute' => 'approved',
                    'label' => 'Debitmemo Approved',
                    'value' => function($data) {
                        $res = '<table class="inner-table">';
                        $res .= '<tr><td width=\'200\'>';
                        $res .= Utility::moneyFormat($data["approved1"], ($data->currency1IdRef != 1) ? 2 : null);
                        $res .= '</td></tr>';
                        if (isset($data->currency2)) {
                            $res .= '<tr class="top-border"><td >';
                            $res .= Utility::moneyFormat($data["approved2"], (isset($data->currency2) && $data->currency2IdRef != 1) ? 2 : null);
                            $res .= '</td></tr>';
                        }
                        $res .= '</table>';
                        return $res;
                    }
                ],
            ],
        ]);

        ?>
    </div>
    <div class="tab-pane fade in active" id="debit-invoice">

        <?=
        GridView::widget([
            'id' => 'debitmemo-invoice-grid',
            'dataProvider' => $invoiceDataProvider,
            'columns' => [
                [
                    'label' => 'Title',
                    'value' => 'Title',
                ],
                [
                    'label' => 'invoiceType',
                    'value' => 'type.InvoiceType',
                    'options' => [
                        'width' => '130px',
                    ]
                ],
                [
                    'label' => 'CutOffDateHijri',
                    'label' => 'CutOffDate',
                    'format' => 'raw',
                    'value' => function($data) {
                        return '<table class="inner-table">'
                                . '<tr><td width=\'200\'>'
                                . $data["CutOffDateHijri"]
                                . '<tr class="top-border"><td >'
                                . Date("Y-m-d", strtotime($data["CutOffDate"]))
                                . '</table>';
                    },
                ],
                [
                    'label' => 'Currency',
                    'label' => 'Currency',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        return '<table class="inner-table">'
                                . '<tr><td width=\'100\'>'
                                . (($data["Currency1Title"] != null) ? $data["Currency1Title"] : '&nbsp;')
                                . '<tr class="top-border"><td >'
                                . (($data["Currency2Title"] != null) ? $data["Currency2Title"] : '&nbsp;')
                                . '</table>';
                    }
                ],
                [
                    'label' => 'Requested',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        $color = 'black';
                        if ($data->AmountStatus == 0) {
                            $color = '#f68484';
                        } else if ($data->AmountStatus == 1) {
                            $color = '#259BE9';
                        } else if ($data->AmountStatus == 5) {
                            $color = '#4ac77c';
                        }
                        $back = 'black';
                        if ($data->AmountStatus == 0) {
                            $back = '#F7C5C5';
                        } else if ($data->AmountStatus == 1) {
                            $back = '#B9DBF1';
                        } else if ($data->AmountStatus == 5) {
                            $back = '#C1F0D3';
                        }
                        return '<table class="inner-table" style="background:' . $back . '">'
                                . '<tr><td width=\'180\' style="color:' . $color . ';">'
                                . '<b>' . Utility::moneyFormat($data["Requested1"]) . '</b>'
                                . '<tr class="top-border"><td style="color:' . $color . ';">'
                                . '<b>' . Utility::moneyFormat($data["Requested2"]) . '</b>'
                                . '</table>';
                    }
                ],
                [
                    'label' => 'approved',
                    'label' => 'Approved',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        $color = 'black';
                        if ($data->AmountStatus == 0) {
                            $color = '#f68484';
                        } else if ($data->AmountStatus == 1) {
                            $color = '#259BE9';
                        } else if ($data->AmountStatus == 5) {
                            $color = '#4ac77c';
                        }
                        $back = 'black';
                        if ($data->AmountStatus == 0) {
                            $back = '#F7C5C5';
                        } else if ($data->AmountStatus == 1) {
                            $back = '#B9DBF1';
                        } else if ($data->AmountStatus == 5) {
                            $back = '#C1F0D3';
                        }
                        return '<table class="inner-table" style="background:' . $back . '">'
                                . '<tr>'
                                . '<td width=\'180\' style="color:' . $color . ';">'
                                . '<b>' . Utility::moneyFormat($data["Approved1"], ($data->currency1->CurrencyID != 1) ? 2 : null) . '</b>'
                                . '</td>'
                                . '<td>'
                                . $data["Currency1Title"]
                                . '</td>'
                                . '</tr>'
                                . '<tr class="top-border">'
                                . '<td style="background:' . $back . '">'
                                . '<b style="color:' . $color . '">' . Utility::moneyFormat($data["Approved2"]) . '</b>'
                                . '</td>'
                                . '<td>'
                                . $data["Currency2Title"]
                                . '</td>'
                                . '</tr>'
                                . '</table>';
                    }
                ],
                [
                    'label' => 'Income',
                    'label' => 'Income',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        return '<table class="inner-table">'
                                . '<tr><td width=\'180\'>'
                                . Utility::moneyFormat($data["Income1"], ($data->currency1->CurrencyID != 1) ? 2 : null)
                                . '<tr class="top-border"><td >'
                                . Utility::moneyFormat($data["Income2"], ($data->currency1->CurrencyID != 1) ? 2 : null)
                                . '</table>';
                    }
                ],
                [
                    'label' => 'Penalty',
                    'label' => 'Penalty',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        return '<table class="inner-table">'
                                . '<tr><td width=\'180\'>'
                                . Utility::moneyFormat($data["Penalty1"])
                                . '<tr class="top-border"><td >'
                                . Utility::moneyFormat($data["Penalty2"])
                                . '</table>';
                    }
                ],
                [
                    'label' => 'LastActionTitle',
                    'label' => 'Last Action',
                    'format' => 'raw',
                    'value' => function($model) {
                        $text = isset($model->processEntity->lastAction->ActionTitle2) ? $model->processEntity->lastAction->ActionTitle2 : '';
                        return Utility::generateTextShorterSpan($text, 30, 70);
                    },
                    'filter' => false,
                    'options' => [
                        'width' => '150px'
                    ]
                ],
                [
                    'label' => 'Description',
                    'format' => 'raw',
                    'filter' => false,
                    'value' => function($data) {
                        return Utility::generateTextShorterSpan($data['Description'], 30, 100);
                    },
                    'options' => [
                        'width' => '150px',
                    ]
                ],
            ]
        ]);
        ?>
    </div>
    
    <div class="panel-body" style="padding:0px;">
            <div class="total-content">

                <table class="table table-striped table-hover non-responsive table-bordered kv-grid-table kv-table-wrap" style="margin-bottom:5px"><thead>

                <tr><th data-col-seq="0">Currency</th>
                    <th data-col-seq="3">Invoice Approved</th>
                    <th data-col-seq="3">Invoice Cumulative</th>
                    <th data-col-seq="2">Debit Memo Approved</th>
                    <th data-col-seq="1">Debit Memo Cumulative</th></tr>

                </thead>
                <tbody>
                <tr data-key="0">
                    <td data-col-seq="0"><?= $processEntityModel->debitmemo->contract->lastRider->currency1->CurrencyTitle ?></td>
                    <td data-col-seq="4"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->invoiceApproved1, $processEntityModel->debitmemo->contract->lastRider->currency1->CurrencyTitle) ?></td>
                    <td data-col-seq="3"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->LastInvoiceCumulativeApproved1(6), $processEntityModel->debitmemo->contract->lastRider->currency1->CurrencyTitle) ?></td>
                    <td data-col-seq="2"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->approved1, $processEntityModel->debitmemo->contract->lastRider->currency1->CurrencyTitle) ?></td>
                    <td data-col-seq="1"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->cumulativeApproved1(), $processEntityModel->debitmemo->contract->lastRider->currency1->CurrencyTitle) ?></td>
                </tr>
                <?php if(isset($processEntityModel->debitmemo->contract->lastRider->currency2->CurrencyTitle)){ ?>
                    <tr data-key="0">
                    <td data-col-seq="0"><?= $processEntityModel->debitmemo->contract->lastRider->currency2->CurrencyTitle ?></td>
                    <td data-col-seq="4"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->invoiceApproved2, $processEntityModel->debitmemo->contract->lastRider->currency2->CurrencyTitle) ?></td>
                    <td data-col-seq="3"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->LastInvoiceCumulativeApproved2(6), $processEntityModel->debitmemo->contract->lastRider->currency2->CurrencyTitle) ?></td>
                    <td data-col-seq="2"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->approved2, $processEntityModel->debitmemo->contract->lastRider->currency2->CurrencyTitle) ?></td>
                    <td data-col-seq="1"><?= Utility::moneyFormatByCurrency($processEntityModel->debitmemo->cumulativeApproved2(), $processEntityModel->debitmemo->contract->lastRider->currency2->CurrencyTitle) ?></td>
                </tr>
                <?php } ?>
                </tbody></table>
                
            </div>
        </div>
</div>
<!--<br /><h4><span class="label label-info">Total :</span></h4>-->

<div class="pull-right">
    <?=
    Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
        'class' => 'btn btn-sm btn-success',
        "onclick" => "sign(this)",
        'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
    ]);
    ?>

    <?=
    Html::a('<i class="en-back"></i> back', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'peid' => Yii::$app->request->get('peid'), 'clearValue' => false]), [
        'class' => 'btn btn-sm btn-primary'
    ])
    ?>&nbsp;

    <?=
    Html::a("<i class=\"br-cancel\"></i> Cancel", '#', [
        'class' => 'btn btn-sm btn-warning',
        'data-dismiss' => 'modal'
    ]);
    ?>
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
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('debitmemo%2Fdebitmemo%2Fview') != -1 || r.indexOf('debitmemo/debitmemo/view') != -1 ){
                    showMsgBoxInBottomRight('Debitmemo successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['debitmemo/debitmemo/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#debitmemo-modal').modal('hide');
                $.pjax.reload({
                    container:'#debitmemo-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/debitmemo', 'type' => 'ToDo']) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
                 ajaxProgressLoader('hide', '.modal-content');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide',parentBox);
        });
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_READY);
