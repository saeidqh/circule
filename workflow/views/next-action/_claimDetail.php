<?php

use yii\grid\GridView;
use app\components\Utility;
use yii\helpers\Html;

$decimalPointNumber1 = (strtoupper($contractModel->lastRider->currency1->CurrencyTitle) == 'IRR') ? 0 : 2;
$decimalPointNumber2 = (strtoupper(isset($contractModel->lastRider->currency2->CurrencyTitle) ? Html::encode($contractModel->lastRider->currency2->CurrencyTitle) : 0) == 'IRR') ? 0 : 2;

$contractCurrency1Title = $claimModel->contract->lastRider->currency1->CurrencyTitle;
$contractCurrency2Title = isset($claimModel->contract->lastRider->currency2->CurrencyTitle) ? Html::encode($claimModel->contract->lastRider->currency2->CurrencyTitle) : NULL;
?>

<div class="row">
    <div class="col-lg-12 col-md-12" style="padding:0px;">

        <div class="panel panel-default" style="border: 1px solid #ccc;padding: 0px;margin-bottom: 0px;">
            <div class="panel-heading">
                <p class="m0 ml5">Claim Info</p>
            </div>
            <div class="panel-body" style="padding:5px 0px 0px 0px;border: 0px !important;">

                <div class="col-md-6">
                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                        <tbody>
                            <tr><th width='40%'>Claim NO:</th><td width='10'></td><td><?= Html::encode($claimModel->claimNo); ?></td></tr>  
                            <tr><th width='40%'>claim Type:</th><td width='10'></td><td><?= Html::encode($claimModel->type->title); ?></td></tr>  
                            <tr><th width='40%'>Claim Subject:</th><td width='10'></td><td><?= Html::encode($claimModel->subject); ?></td></tr>  

                            <tr><th>Site:</th><td></td><td>
                                    <table class="inner-table"><tbody>
                                            <tr><td width="200"><?= Utility::moneyFormat($claimModel->cumulativeStep1Amount1(), $decimalPointNumber1); ?></td><td class="label-silver"><?= Html::encode($contractCurrency1Title); ?></td></tr>
                                            <?php
                                            if (isset($contractCurrency2Title)) {
                                                echo '<tr class="top-border"><td>' . Utility::moneyFormat($claimModel->cumulativeStep1Amount2(), $decimalPointNumber2) . '</td><td class="label-silver">' . Html::encode($contractCurrency2Title) . '</td></tr>';
                                            } else {
                                                echo '';
                                            };
                                            ?>

                                        </tbody></table>
                                </td></tr>

                        </tbody>    
                    </table>

                </div>
                <div class="col-md-6">
                    <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                        <tbody>
                            <tr><th>Site:</th><td></td><td>
                                    <table class="inner-table"><tbody>
                                            <tr><td width="200"><?= Utility::moneyFormat($claimModel->cumulativeStep2Amount1(), $decimalPointNumber1); ?></td><td class="label-silver"><?= Html::encode($contractCurrency1Title); ?></td></tr>
                                            <?php
                                            if (isset($contractCurrency2Title)) {
                                                echo '<tr class="top-border"><td>' . Utility::moneyFormat($claimModel->cumulativeStep2Amount2(), $decimalPointNumber2) . '</td><td class="label-silver">' . Html::encode($contractCurrency2Title) . '</td></tr>';
                                            } else {
                                                echo '';
                                            };
                                            ?>

                                        </tbody></table>
                                </td></tr>
                            <tr><th>Company:</th><td></td><td>
                                    <table class="inner-table"><tbody>
                                            <tr><td width="200"><?= Utility::moneyFormat($claimModel->cumulativeStep3Amount1(), $decimalPointNumber1); ?></td><td class="label-silver"><?= Html::encode($contractCurrency1Title); ?></td></tr>
                                            <?php
                                            if (isset($contractCurrency2Title)) {
                                                echo '<tr class="top-border"><td>' . Utility::moneyFormat($claimModel->cumulativeStep3Amount2(), $decimalPointNumber2) . '</td><td class="label-silver">' . Html::encode($contractCurrency2Title) . '</td></tr>';
                                            } else {
                                                echo '';
                                            };
                                            ?>

                                        </tbody></table>
                                </td></tr>
                            <tr><th>Holding:</th><td></td><td>
                                    <table class="inner-table"><tbody>
                                            <tr><td width="200"><?= Utility::moneyFormat($claimModel->cumulativeStep4Amount1(), $decimalPointNumber1); ?></td><td class="label-silver"><?= Html::encode($contractCurrency1Title); ?></td></tr>
                                            <tr class="top-border"><td><?php
                                                    if (isset($contractCurrency2Title)) {
                                                        echo Utility::moneyFormat($claimModel->cumulativeStep4Amount2(), $decimalPointNumber2);
                                                    } else {
                                                        echo '';
                                                    };
                                                    ?>
                                        </tbody></table>
                                </td></tr>
                            <tr><th>Pars Group:</th><td></td><td>
                                    <table class="inner-table"><tbody>
                                            <tr><td width="200"><?= Utility::moneyFormat($claimModel->cumulativeStep5Amount1(), $decimalPointNumber1); ?></td><td class="label-silver"><?= Html::encode($contractCurrency1Title); ?></td></tr>
                                            <tr class="top-border"><td><?php
                                                    if (isset($contractCurrency2Title)) {
                                                        echo Utility::moneyFormat($claimModel->cumulativeStep5Amount2(), $decimalPointNumber2);
                                                    } else {
                                                        echo '';
                                                    };
                                                    ?>
                                        </tbody></table>
                                </td></tr>

                        </tbody>    
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-12" style="padding: 0px;">
        <div class="panel panel-default" style="border: 1px solid #ccc;margin-bottom: 5px;">
            <div class="panel-heading">
                <span class="m0 ml5">Claim Amount List<span>
                        </div>
                        <div class="pre-scrollable" style="max-height: 230px;">

                            <div class="panel-body" style="border: 0px !important; padding: 2px;">

                                <?php
                                echo GridView::widget([
                                    'dataProvider' => $claimAmountSearchDataProvider,
                                    'layout' => '{items}',
                                    'columns' => [
                                        [
                                            'attribute' => 'costcode',
                                            'label' => 'Cost Code',
                                            'value' => 'costcode',
                                            'options' => [
                                                'width' => '80 px',
                                            ],
                                        ],
                                        [
                                            'attribute' => 'nodeText',
                                            'label' => 'Title',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                return Html::encode($model->nodeText);
                                            },
                                            'options' => [
                                                'width' => '120 px',
                                            ],
                                        ],
                                        [
                                            'label' => 'Requested By Subcontractor',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                return Utility::generateMoneyFieldContent(
                                                                Utility::moneyFormat($model->step1Amount1), Utility::moneyFormat($model->step1Amount1), $model['currency1Title'], $model['currency2Title'], 150, true);
                                            }
                                        ],
                                        [
                                            'label' => 'Site',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $result = '<table class="inner-table"><tr><td width=\'200\'>' . Utility::moneyFormat($model->step2Amount1);
                                                if (isset($contractCurrency2Title)) {
                                                    $result.='<tr class="top-border"><td >'. Utility::moneyFormat($model->step2Amount2);
                                                }
                                                $result.="</table>";
                                                return $result;
                                            }
                                        ],
                                        [
                                            'label' => 'Company',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $result = '<table class="inner-table"><tr><td width=\'200\'>' . Utility::moneyFormat($model->step3Amount1);
                                                if (isset($contractCurrency2Title)) {
                                                    $result.='<tr class="top-border"><td >'. Utility::moneyFormat($model->step3Amount2);
                                                }
                                                $result.="</table>";
                                                return $result;
                                            }
                                        ],
                                        [
                                            'label' => 'Holding',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $result = '<table class="inner-table"><tr><td width=\'200\'>' . Utility::moneyFormat($model->step4Amount1);
                                                if (isset($contractCurrency2Title)) {
                                                    $result.='<tr class="top-border"><td >'. Utility::moneyFormat($model->step4Amount2);
                                                }
                                                $result.="</table>";
                                                return $result;
                                            }
                                        ],
                                        [
                                            'label' => 'Pars Group',
                                            'format' => 'raw',
                                            'value' => function($model) {
                                                $result = '<table class="inner-table"><tr><td width=\'200\'>' . Utility::moneyFormat($model->step5Amount1);
                                                if (isset($contractCurrency2Title)) {
                                                    $result.='<tr class="top-border"><td >'. Utility::moneyFormat($model->step5Amount2);
                                                }
                                                $result.="</table>";
                                                return $result;
                                            }
                                        ],
                                    ]
                                ]);
                                ?>

                            </div>

                        </div>

                        </div>




                        <div class="panel panel-default" style="border: 1px solid #ccc;padding: 0px;margin-bottom: 0px;">
                            <div class="panel-heading">

                                <div class="row" style="padding-top:5px;">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <table class='table table-striped psa-grid psa-grid-text-left psa-detail-view' >
                                                <tbody>
                                                    <tr><td></td><td>
                                                            <table class="inner-table"><tbody>
                                                                    <tr><td><?= 'Auto Message'; ?></td><td width='10'></td><td><?= Html::encode($actionEntityFormData["AutoMessage"]); ?></td></tr>
                                                                    <tr class="top-border"><td><?= 'User Message'; ?></td><td width='10'></td><td><?= Html::encode($actionEntityFormData["Message"]); ?></td></tr>
                                                                    <tr>
                                                                        <td>Action Date</td>
                                                                        <td width="20"></td>
                                                                        <td><?= Html::encode($actionEntityFormData["ActionDate"]); ?></td>
                                                                    </tr>
                                                                </tbody></table>
                                                        </td></tr>
                                                </tbody>   
                                            </table>
                                        </div>
                                        <div class="col-md-6">

                                            <div class="box-nextaction box-nextaction-anim box-green pull-left">
                                                <?= Html::encode($actionEntityFormData["CurrentActionTitle"]); ?>
                                            </div>
                                            <div class="pull-left" style="padding: 7px 0px 0px 10px;">
                                                <i class="im-arrow-right s24"></i>
                                            </div>

                                            <div class="pull-left"> 
                                                <div class="box-nextaction box-gold">
                                                    <?= Html::encode($actionEntityFormData["NextActionTitle"]); ?>
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
                        <?php
                        $js = "
    function sign(obj){
        
        ajaxProgressLoader('show', '.modal-content');
        ajaxProgressLoader('show', '#claim-list-pjax');
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
            ajaxProgressLoader('hide', '#check-modal');
            ajaxProgressLoader('hide', '#claim-list-pjax');
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
                        