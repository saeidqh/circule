<?php
use app\components\Utility;
use yii\helpers\Html;

?>
<style> 
#panel, #flip {
    padding: 5px;
    text-align: center;
    background-color: #e5eecc;
    border: solid 1px #c3c3c3;
}

#panel {
    padding: 50px;
    display: none;
}
</style>

<div class="panel panel-purple">
    <div class="panel-heading"><h3 class="panel-title">Budget</h3></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6">
                <?php
                echo kartik\detail\DetailView::widget([
                    'id' => 'budget-detail',
                    'model' => $processEntityModel->actualProgress->plan->budget,
                    'attributes' => [
                        [
                            'label'=>'Annual Budget',
                            'format' => 'raw',
                            'displayOnly'=>true,
                            'value' => Html::encode($processEntityModel->actualProgress->plan->budget->annual->AnnualBudgetTitle),
                        ],
                    ],
                ]);
                ?>
            </div>
            <div class="col-lg-2">
                <?php
                echo kartik\detail\DetailView::widget([
                    'id' => 'budget-detail',
                    'model' => $processEntityModel->actualProgress->plan->budget,
                    'attributes' => [
                        [
                            'attribute'=>'BudgetID',
                            'label'=>'BudgetID',
                            'format' => 'raw',
                            'displayOnly'=>true,
                        ],
                    ],
                ]);
                ?>
            </div>
            <div class="col-lg-4">
                <?php
                echo kartik\detail\DetailView::widget([
                    'id' => 'budget-detail',
                    'model' => $processEntityModel->actualProgress->plan->budget,
                    'attributes' => [
                        [
                            'label'=>'EPS',
                            'format' => 'raw',
                            'displayOnly'=>true,
                            'value' => $processEntityModel->actualProgress->plan->budget->lastEps->EPSTitle,
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
        
        <div class="row">
            
            <?php
echo \kartik\grid\GridView::widget([
    'id' => 'budget-node-grid',
    'dataProvider' => $budgetNodeDataProvider,
    'layout' => "{items}\n",
    'options' => [
        "style" => "margin-top: -15px;"
    ],
    "showFooter" => FALSE,
    'columns' => [
        [
            'label' => 'CostCode',
            'attribute' => 'NodeFullCode',
            'format' => 'raw',
            'options' => ['width' => '8.61%'],
            'value' => function($model) {
                $lastLevel = '';
                if (isset($model->costCode->level8)) {
                    $lastLevel = $model->costCode->level8;
                } elseif (isset($model->costCode->level7)) {
                    $lastLevel = $model->costCode->level7;
                } elseif (isset($model->costCode->level6)) {
                    $lastLevel = $model->costCode->level6;
                } elseif (isset($model->costCode->level5)) {
                    $lastLevel = $model->costCode->level5;
                } elseif (isset($model->costCode->level4)) {
                    $lastLevel = $model->costCode->level4;
                } elseif (isset($model->costCode->level3)) {
                    $lastLevel = $model->costCode->level3;
                } elseif (isset($model->costCode->level2)) {
                    $lastLevel = $model->costCode->level2;
                } elseif (isset($model->costCode->level1)) {
                    $lastLevel = $model->costCode->level1;
                }
                return app\components\Utility::generateCostCodeLink($model->costCode->NodeID, $model->costCode->NodeFullCode) . '<br>' . $lastLevel;
            },
        ],
        [
            'label' => '',
            'format' => 'raw',
            'options' => ['width' => '1.61%'],
            'value' => function($model) {
        if (isset($model->budget->currency1)) {
            $cur1 = $model->budget->currency1->CurrencyTitle;
        } else {
            $cur1 = '';
        }
        if (isset($model->budget->currency2)) {
            $cur2 = $model->budget->currency2->CurrencyTitle;
        } else {
            $cur2 = '';
        }
        return '<table class="inner-table">'
                . '<tr><td class="label-silver">' . $cur1 . '</td></tr>'
                . '<tr><td class="label-silver">' . $cur2 . '</td></tr>'
                . '</table>';
    },
        ],
        [
            'label' => 'Month 01',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month01Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month01Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 02',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month02Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month02Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 03',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month03Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month03Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
                
            },
        ],
        [
            'label' => 'Month 04',
            'format' => 'raw',
            'value' => function($model) {
                 if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month04Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month04Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';               

            },
        ],
        [
            'label' => 'Month 05',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month05Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month05Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 06',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month06Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month06Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
                
            },
        ],
        [
            'label' => 'Month 07',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month07Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month07Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';

            },
        ],
        [
            'label' => 'Month 08',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month08Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month08Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 09',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month09Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month09Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 10',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month10Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month10Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 11',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month11Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month11Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Month 12',
            'format' => 'raw',
            'value' => function($model) {
                if($model->totalCurrency1==0){
                    $percent=0;
                }  else {
                    $percent=($model->Month12Currency1 * 100) / $model->totalCurrency1;
                }
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . app\components\Utility::moneyFormat($model->Month12Currency1) . '</td></tr>'
                        . '<tr><td class="label-silver">' . $percent . '%</td></tr>'
                        . '</table>';
            },
        ],
        [
            'label' => 'Total',
            'format' => 'raw',
            'options' => ['width' => "60px"],
            'value' => function($model) {
                if (isset($model->budget->currency1)) {
                    $value1 = app\components\Utility::moneyFormat($model->totalCurrency1);
                } else {
                    $value1 = '';
                }
                
                return '<table class="inner-table">'
                        . '<tr><td class="label-silver">' . $value1 . '</td></tr>'
                        . '</table>';
            },
        ],
    ]
]);
?>


        </div>

    </div>
</div>

<table class="kv-grid-table table table-bordered table-striped kv-table-wrap" id="table-budget-node-view">
    <!--Actual Progress-->
    <tr>
        <td colspan="16" rowspan="1" style="background-color: rgb(115, 197, 216);">Actual Progress</td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td>Month 01</td>
        <td>Month 02</td>
        <td>Month 03</td>
        <td>Month 04</td>
        <td>Month 05</td>
        <td>Month 06</td>
        <td>Month 07</td>
        <td>Month 08</td>
        <td>Month 09</td>
        <td>Month 10</td>
        <td>Month 11</td>
        <td>Month 12</td>
        <td>Total</td>
    </tr>
    <tr>
        <td style="width: 40px;" rowspan="1">Actual Progress</td>
        <td style="width: 20px;" rowspan="1"><?= $processEntityModel->actualProgress->plan->currency->CurrencyTitle ?></td>
        <td style="width: 42px;">Rate</td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(1, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(2, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(3, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(4, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(5, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(6, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(7, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(8, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(9, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(10, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(11, 1)) ?></td>
        <td><?= Utility::moneyFormat($processEntityModel->actualProgress->rate(12, 1)) ?></td>
        <td style="width: 70px;"><?= Utility::moneyFormat($processEntityModel->actualProgress->totalRate(1)) ?></td>
    </tr>
    <tr>
        <td colspan="1" style="width: 70px;">Description</td>
        <td colspan="15" style="width: 70px;"><?= $processEntityModel->actualProgress->description ?></td>

        
    </tr>
    </table>

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
<script> 
$(document).ready(function(){
    
    $("#flip").click(function(){
        if($('#panel').css('display')=="block"){
            $('div#flip span').removeClass('glyphicon glyphicon-arrow-up');
            $('div#flip span').addClass('glyphicon glyphicon-arrow-down');
        }else{
            $('div#flip span').removeClass('glyphicon glyphicon-arrow-down');
            $('div#flip span').addClass('glyphicon glyphicon-arrow-up');
        }
        
        $("#panel").stop().slideToggle("slow");
    });

});
</script>
<?php
                        $js = "
    function sign(obj){
        
        ajaxProgressLoader('show', '.modal-content');
        ajaxProgressLoader('show', '#actual-progress-pjax');
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
            ajaxProgressLoader('hide', '.modal-content');
            ajaxProgressLoader('hide', '#check-modal');
            ajaxProgressLoader('hide', '#actual-progress-pjax');
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('budget%2Factual-progress%2Fview') != -1 || r.indexOf('budget/actual-progress/view') != -1 ){
                    showMsgBoxInBottomRight('Actual Progress successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['budget/actual-progress/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#budget-modal').modal('hide');
                $.pjax.reload({
                    container:'#actual-progress-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl([
                                    'workflow/workflow/actual-progress', 'type' => 'ToDo',
                                ]) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide', '#check-modal');
            ajaxProgressLoader('hide', '#actual-progress-pjax');
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
                        