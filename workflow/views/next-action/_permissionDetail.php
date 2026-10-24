<?php

use yii\grid\GridView;
use app\components\Utility;
use yii\helpers\Html;

$decimalPointNumber1 = (strtoupper($permissionModel->currency1Id) == '1') ? 0 : 2;
$decimalPointNumber2 = (strtoupper($permissionModel->currency2Id) == '1') ? 0 : 2;

$contractCurrency1Title = Html::encode($permissionModel->contract->lastRider->currency1->CurrencyTitle);
$contractCurrency2Title = isset($permissionModel->contract->lastRider->currency2) ? Html::encode($permissionModel->contract->lastRider->currency1->CurrencyTitle) : '';
?>

<div class="row kamali" style="margin-right: -15px;margin-left: -15px;margin-top: 5px;">

    <div class="bs-example bs-example-tabs" style="text-align: left;">
        <ul id="myTab" class="nav nav-tabs" role="tablist"> 
            <li class="active"><a href="#Permissions" role="tab" data-toggle="tab">
                    <i class="fa-dollar"></i> <?= 'Contract Detail' ?></a>
            </li>
        </ul>
        <div id="myTabContent" class="tab-content">

            <div class="tab-pane fade active in" id="Permissions" style="padding:10px 0;">

                <div>
                    <div class="col-lg-12 col-md-12" style="padding:0px;">
                        <div class="panel panel-default" style="border: 1px solid #ccc;padding: 0px;margin-bottom: 0px;">
                            <div class="panel-heading">
                                <p class="m0 ml5">Contract Info</p>
                            </div>
                            <div class="panel-body" style="padding:5px 0px 0px 0px;border: 0px !important;">

                                <div class="col-md-6">
                                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                                        <tbody>
                                            <tr><th width='40%'>ContractNo:</th><td width='10'></td><td><?= Html::encode($permissionModel->contract->ContractNo); ?></td></tr>  
                                            <tr><th width='40%'>Contract Subject:</th><td width='10'></td><td><?= Html::encode($permissionModel->contract->ContractSubject); ?></td></tr>  

                                            <tr><th>Price:</th><td></td><td>
                                                    <table class="inner-table"><tbody>
                                                            <tr><td width="200"><?= Utility::moneyFormat($permissionModel->contract->lastRider->Price1, $decimalPointNumber1); ?></td><td class="label-silver"><?= $contractCurrency1Title; ?></td></tr>
                                                            <tr class="top-border"><td><?php
                                                                    if (isset($permissionModel->contract->lastRider->Price2)) {
                                                                        echo Utility::moneyFormat($permissionModel->contract->lastRider->Price2, $decimalPointNumber2);
                                                                    } else {
                                                                        echo '';
                                                                    };
                                                                    ?></td><td class="label-silver"><?= $contractCurrency2Title; ?></td></tr>
                                                        </tbody></table>
                                                </td></tr>
                                        </tbody>    
                                    </table>

                                </div>
                                <div class="col-md-6">
                                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                                        <tbody>
                                            <tr><th width='40%'>Sub Contractor:</th><td width='10'></td><td><?= !empty($permissionModel->contract->subContractor) ? Html::encode($permissionModel->contract->subContractor->PersonTitle) : ''; ?></td></tr>  
                                            <tr><th width='40%'>Contract Date:</th><td width='10'></td><td><?= Html::encode($permissionModel->contract->ContractDateHijri); ?></td></tr>  
                                            <tr><th width='40%'>Finish Date:</th><td width='10'></td><td><?= Html::encode($permissionModel->contract->lastRider->FinishDateHijri); ?></td></tr>  

                                        </tbody>    
                                    </table>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!--                    <div class="col-lg-4 col-md-4" style="padding:0px 0px 0px 5px;">
                                            <div class="panel panel-default" style="border: 1px solid #ccc;">
                                                <div class="panel-heading">
                                                    <p class="m0">Permission Documents</p> 
                                                </div>
                                                <div class="panel-body" style="padding: 0px;border: 0px !important;">
                    <?php
//                                echo GridView::widget([
//                                    'id' => 'tbl-obs-grid1',
//                                    'options' => [
//                                        'style' => 'margin-bottom:0px;'
//                                    ],
//                                    'dataProvider' => $documentRequireModelDataProvider,
//                                    'columns' => [
//                                        'PermissionDocumentTypeTitle',
//                                        'RequireType',
//                                    ],
//                                ]);
                    ?>
                                                </div>
                                            </div>
                                        </div>-->

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
                                                        'format' => 'raw',
                                                        'attribute' => 'currency',
                                                        'value' => function($model)use($contractCurrency1Title,$contractCurrency2Title) {
                                                            return '<table class="inner-table">'
                                                                    . '<tr><td width=\'100\'>'
                                                                    . $contractCurrency1Title
                                                                    . '<tr class="top-border"><td >'
                                                                    . $contractCurrency2Title
                                                                    . '</table>';
                                                        },
                                                        'options' => [
                                                            'width' => '20px'
                                                        ]
                                                    ],
                                                    [
                                                        'format' => 'raw',
                                                        'attribute' => 'amountRequested1',
                                                        'label' => Yii::t('permission', 'Requested'),
                                                        'value' => function($data)use($contractCurrency1Title,$contractCurrency2Title) {
                                                            return '<table class="inner-table">'
                                                                    . '<tr><td width=\'200\'>'
                                                                    . Utility::moneyFormat($data["amountRequested1"], ($contractCurrency1Title != 'IRR') ? 2 : null)
                                                                    . '<tr class="top-border"><td >'
                                                                    . Utility::moneyFormat($data["amountRequested2"], ($contractCurrency1Title != 1) ? 2 : null)
                                                                    . '</table>';
                                                        }
                                                    ],
                                                    [
                                                        'format' => 'raw',
                                                        'attribute' => 'AmountApproved1',
                                                        'label' => Yii::t('permission', 'Approved'),
                                                        'value' => function($data) {
                                                            return '<table class="inner-table">'
                                                                    . '<tr><td width=\'200\'>'
                                                                    . Utility::moneyFormat($data->AmountApproved1, ($data->CurrencyTitle1 != 'IRR') ? 2 : null)
                                                                    . '<tr class="top-border"><td >'
                                                                    . Utility::moneyFormat($data->AmountApproved2, (isset($data->CurrencyTitle1) && $data->CurrencyTitle1 != 'IRR') ? 2 : null)
                                                                    . '</table>';
                                                        }
                                                    ],
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
                                                        <p class="m0">permission Info</p>
                                                    </div>
                                                    <div class="panel-body" style="border:0px !important;padding: 15px 15px 0px 15px;">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view'>
                                                                    <tbody>
                                                                        <tr><th>permission Type:</th><td></td><td>
                                                                                <table class="inner-table"><tbody>
                                                                                        <tr><td ><?= $permissionModel->type->title; ?></td></tr>

                                                                                    </tbody></table>
                                                                            </td></tr>
                                                                        <tr><th>permission title:</th><td></td><td>
                                                                                <table class="inner-table"><tbody>
                                                                                        <tr><td ><?= $permissionModel->title; ?></td></tr>
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
                                                                                        <tr><td><?= Yii::$app->utility->date($permissionModel->cutOffDate); ?></td></tr>

                                                                                    </tbody></table>
                                                                            </td></tr>
                                                                        <tr><th>Date Shamsi:</th><td></td><td>
                                                                                <table class="inner-table"><tbody>
                                                                                        <tr><td><?= Yii::$app->utility->date($permissionModel->cutOffDateHijri); ?></td></tr>
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
                                                                                        <tr><td><?= Utility::moneyFormat($permissionModel->requested1, $decimalPointNumber1); ?></td><td class="label-silver">&nbsp;<?= $contractCurrency1Title ?></td></tr>
                                                                                        <tr class="top-border"><td><?= !empty($permissionModel->Currency2Title) ? Utility::moneyFormat($permissionModel->requested2, $decimalPointNumber2) : ''; ?></td><td class="label-silver">&nbsp;<?= $contractCurrency2Title ?></td></tr>
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
                                                                                        <tr><td><?= Utility::moneyFormat($permissionModel->approved1, $decimalPointNumber1); ?></td><td class="label-silver">&nbsp;<?= $contractCurrency1Title ?></td></tr>
                                                                                        <tr class="top-border"><td><?= !empty($permissionModel->Currency2Title) ? Utility::moneyFormat($permissionModel->approved2, $decimalPointNumber2) : ''; ?></td><td class="label-silver">&nbsp;<?= $contractCurrency2Title ?></td></tr>
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
                if(r.indexOf('permission%2Fpermission%2Fview') != -1 || r.indexOf('permission/permission/view') != -1 ){
                    showMsgBoxInBottomRight('Permission successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['permission/permission/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#permission-modal').modal('hide');
                $.pjax.reload({
                    container:'#permission-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl([
                                                    'workflow/workflow/permission', 'type' => 'ToDo',
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
                                                    