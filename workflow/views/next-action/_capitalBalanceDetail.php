<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>
<style>
    .capital-check-page .kv-thead-float{
        top: 116px !important;
        left: 21px !important;
    }
</style>
<div class="row" >
    <div class="col-lg-12 col-md-12">
        <div class="form-group">
            <div class="row">
                <div class="col-lg-3 col-md-3 mt5" style="padding-right: 0px;">
                    <label for="capital-list-user-name">Capital List User Name :</label>
                </div>
                <div class="col-lg-9 col-md-9" style="padding-left: 0px;">
                    <?=
//                    Html::textInput('', $capitalBalaceModel->user->UserName, [
                    Html::textInput('', $capitalBalaceModel->user->fullName, [
                        'class' => 'form-control',
                        'id' => 'capital-list-user-name',
                        'readonly' => 'readonly',
                    ])
                    ?>
                </div>
            </div>
        </div>
        <div class='capital-check-page' style="max-height:350px; overflow-y: scroll;overflow-x: hidden; border:solid 1px  #CCCCCC; margin-bottom: 10px; position: relative;display:block" >
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'capital-amount-grid',
            'dataProvider' => $capitalAmountDataProvider,
            'hover' => true,
            'showFooter' => true,
            'floatHeader' => true,
            'columns' => [
                [
                    'label' => 'Budget Row',
                    'attribute' => 'budgetRow',
                    'value' => 'fullId',
                ],
                [
                    'label' => 'Budget Title',
                    'attribute' => 'budgetTitle',
                    'value' => 'PlanTitle',
                ],
                [
                    'attribute' => 'remainBudgetYear',
                    'filter' => false,
                    'value' =>  function($data) {
//                        return Yii::$app->utility->moneyFormat($data['AC1_95'] - $data['financialAmount']);
                        return Yii::$app->utility->moneyFormat($data['PlanAmount'] - $data['AC1_95']);
                    },
                    'contentOptions' =>  function($data) {
//                        return ['style' => 'color:' .(($data['AC1_95'] - $data['financialAmount']) > 0 ? '#53ec53' : 'red')];
                        return ['style' => 'background-color:' .(($data['PlanAmount'] - $data['AC1_95']) > 0 ? '#53ec53' : 'red' . '; width: 80px')];
                    },
                ],
                [
                    'attribute' => 'remainBudgetMonth',
                    'filter' => false,
                    'value' =>  function($data) {
                        return Yii::$app->utility->moneyFormat($data['TotalBudgetUpToRequestMonth'] - $data['AC1_95_up_to_current_month']);
                    },
                    'contentOptions' =>  function($data) {
                        return ['style' => 'background-color:' .(($data['TotalBudgetUpToRequestMonth'] - $data['AC1_95_up_to_current_month']) > 0 ? '#53ec53' : 'red' . '; width: 80px')];
                    },
                ],    
                [
                    'label' => 'Description',
                    'attribute' => 'balanceAmountDescription',
                    'value' => 'balanceAmountDescription',
                ],
                [
                    'header' => Html::a('factorNo', Url::to(['/workflow/next-action/check-next-action-capital-balance', 'peid' => $processEntityModel->ProcessEntityID, 'sort' => 'factorNo'])),
                    'label' => 'factorNo',
                    'attribute' => 'factorNo',
                    'value' => 'factorNo',
                ],
                [
                    'label' => 'factorDate',
                    'attribute' => 'factorDate',
                    'value' => 'factorDate',
                ],
                [
                    'label' => 'costAccount',
                    'attribute' => 'costAccount',
                    'value' => 'costAccount',
                ],
                [
                    'label' => 'Amount',
                    'attribute' => 'balanceAmount',
                    'format' => 'raw',
                    'options' => [
                        'width' => '150px;'
                    ],
                    'value' => function($model) {
                          return Yii::$app->utility->generateMoneyFieldContent(
                                        Yii::$app->utility->moneyFormat($model['financialAmount']), null, $model['CurrencyTitle'], null, 150, true);  
//                        return Yii::$app->utility->generateMoneyFieldContent(
//                                        Yii::$app->utility->moneyFormat($model->financialAmount), null, $model->currency->CurrencyTitle, null, 150, true);
                    },
                    'footer' => Yii::$app->utility->moneyFormat($sumCapitalAmount)
                ],
            ],
        ]);
        ?>
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
        <div class="pull-right">
            <?=
            Html::a('<i class="im-file-excel"></i> Download Excel', Yii::$app->urlManager->createAbsoluteUrl(['capital/capital/capital-amount-export', 'peid' => Yii::$app->request->get('peid')]), [
                'class' => 'btn btn-sm btn-info',
                'target' => '_blank',
                'data-pjax' => 0,
            ]);
            ?>
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
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('capital%2Fcapital%2Fview') != -1 || r.indexOf('capital/capital/view') != -1 ){
                    showMsgBoxInBottomRight('Capital successfully went to the next step', 'success')
                    window.location = '".Yii::$app->urlManager->createAbsoluteUrl(['capital/capital/view', 'peid' => Yii::$app->request->get('peid')])."';
                    return;
                }
                $('#capital-modal').modal('hide');
                $.pjax.reload({
                    container:'#capital-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/capital', 'type' => 'ToDo']) . "',
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

";
$this->registerCss($css);
