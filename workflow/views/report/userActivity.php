<?php

use yii\widgets\Pjax;
use kartik\grid\GridView;
use app\components\GridViewBox;
use app\components\PageHeader;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use app\assets\HighChartAsset;

DatepickerAsset::register($this);
JdateTimeAsset::register($this);
HighChartAsset::register($this);

$this->title = "Users Action/Process Activities Log";
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>
<?php GridViewBox::begin() ?>

<style>
    #user-activity-grid table thead{
        background-color:#97d3c5;
    }
    #total-user-activity-grid table thead{
        background-color:#8cb2d2;
    }
    .table{font-size:11px !important }
    .table input{font-size:11px !important;height:25px;padding:2px 3px; }
    .total-background{background: #71d398}
</style>
<?php Pjax::begin(['id' => 'user-activity-pjax']); ?>
<!--< ?= yii\helpers\Html::beginForm(); ?>-->
<div class="row ">
    <div class='col-md-6'><h5>List of Users activities Count</h5></div>
    <div class='col-md-2 mb10'>
        <?= yii\helpers\Html::label('Mode') ?>
        <?=
        yii\helpers\Html::activeDropDownList($userAcivitySearchModel, 'viewMode', [
            'perAction' => 'Per Action',
            'perProcess' => 'Per Process',
                ], [
            'class' => 'form-control form-control-sm input-sm',
            'id' => 'view-mode',
            'style' => 'background-color:#fff2db',
            'onchange' => 'ddlViewModeChane(this)',
        ])
        ?>
    </div>
    <div class='col-md-2 mb10'>
        <?= yii\helpers\Html::label('From') ?>
        <?=
        yii\helpers\Html::activeTextInput($userAcivitySearchModel, 'fromDate', [
            'class' => 'form-control input-sm datepicker-fa',
            'placeholder' => 'example 1395/02/02',
            'style' => 'background-color:#fff2db',
            'onchange' => 'reloadGridView()',
            'keypress' => 'return inpKeyDown()',
        ])
        ?>
    </div>
    <div class='col-md-2 mb10'>
        <?= yii\helpers\Html::label('To') ?>
        <?=
        yii\helpers\Html::activeTextInput($userAcivitySearchModel, 'toDate', [
            'class' => 'form-control input-sm datepicker-fa',
            'placeholder' => 'example 1395/02/02',
            'style' => 'background-color:#fff2db',
            'onchange' => 'reloadGridView()',
            'keypress' => 'return inpKeyDown()',
        ])
        ?>
    </div>
</div>



<div class="row" style="overflow:hidden">
    <?=
    GridView::widget([
        'id' => 'user-activity-grid',
        'dataProvider' => $userAcivityDataProvider,
        'filterModel' => $userAcivitySearchModel,
        'columns' => [
            [
                'attribute' => 'uName',
                'label' => 'User Name'
            ],
            'obsTitle',
            [
                'attribute' => 'Action_CashRequest',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'CashRequest'
            ],
            [
                'attribute' => 'Action_claim',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Claim'
            ],
            [
                'attribute' => 'Action_invoice',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Invoice'
            ],
            [
                'attribute' => 'Action_Contract',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Contract'
            ],
            [
                'attribute' => 'Action_Budget',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Budget'
            ],
            [
                'attribute' => 'Action_capital',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Capital'
            ],
            [
                'attribute' => 'Action_debitmemo',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Debitmemo'
            ],
            [
                'attribute' => 'Action_warranty',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Warranty'
            ],
            [
                'attribute' => 'Action_actualProgress',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'ActualProgress'
            ],
            [
                'attribute' => 'Action_tender',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Tender'
            ],
            [
                'attribute' => 'Action_person',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action'],
                'label' => 'Person'
            ],
            [
                'attribute' => 'total_action',
                'headerOptions' => ['class' => 'show-on-per-action'],
                'filterOptions' => ['class' => 'show-on-per-action'],
                'contentOptions' => ['class' => 'show-on-per-action total-background'],
                'label' => 'TotalAction'
            ],
            [
                'attribute' => 'Process_CashRequest',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'CashRequest'
            ],
            [
                'attribute' => 'Process_claim',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Claim'
            ],
            [
                'attribute' => 'Process_Invoice',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Invoice'
            ],
            [
                'attribute' => 'Process_Contract',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Contract'
            ],
            [
                'attribute' => 'Process_Budget',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Budget'
            ],
            [
                'attribute' => 'Process_capital',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Capital'
            ],
            [
                'attribute' => 'Process_debitmemo',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Debitmemo'
            ],
            [
                'attribute' => 'Process_warranty',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Warranty'
            ],
            [
                'attribute' => 'Process_actualProgress',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'ActualProgress'
            ],
            [
                'attribute' => 'Process_Tender',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Tender'
            ],
            [
                'attribute' => 'Process_Person',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process'],
                'label' => 'Person'
            ],
            [
                'attribute' => 'total_Process',
                'headerOptions' => ['class' => 'show-on-per-process'],
                'filterOptions' => ['class' => 'show-on-per-process'],
                'contentOptions' => ['class' => 'show-on-per-process total-background'],
                'label' => 'TotalProcess'
            ],
            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{graph}',
                'options' => [
                    'width' => '20',
                ],
                'buttons' => [
                    'graph' => function($url, $model, $key) {
                        return yii\helpers\Html::a('<i class="fa-bar-chart s16"></i>', "#", [
                                    'data-pjax' => '0',
                                    'title' => 'Show Graph',
                                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/report/user-activity-graph', 'uid' => $model['UserID'],'obsId' => $model['obsId']]),
                                    'onclick' => "return btnGraphClick(this)",
                        ]);
                    },
                        ]
                    ],
                ],
            ]);
            ?>
        </div>

<div class="bs-example bs-example-tabs">
    <ul id="myTab" class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#total-tab" role="tab" data-toggle="tab"><i class="fa-info s16"></i> Total Activities Count</a></li>
        <li class="" style="color:red"><a href="#graph-tab" role="tab" data-toggle="tab"><i class="fa-bar-chart s16"></i> Total Activities Graph</a></li>
            
    </ul>
    <div id="myTabContent" class="tab-content">
        <div class="tab-pane fade active in" id="total-tab">
            <div class="row total-grid" style="overflow:hidden">

             <?=
             GridView::widget([
                 'id' => 'total-user-activity-grid',
                 'dataProvider' => $totalUserAcivityDataProvider,
                 'filterModel' => null,
                 'columns' => [
 [
                         'attribute' => 'Total_Action_CashRequest',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'CashRequest'
                     ],
                     [
                         'attribute' => 'Total_Action_claim',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Claim'
                     ],
                     [
                         'attribute' => 'Total_Action_invoice',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Invoice'
                     ],
                     [
                         'attribute' => 'Total_Action_Contract',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Contract'
                     ],
                     [
                         'attribute' => 'Total_Action_Budget',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Budget'
                     ],
                     [
                         'attribute' => 'Total_Action_capital',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Capital'
                     ],
                     [
                         'attribute' => 'Total_Action_debitmemo',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Debitmemo'
                     ],
                     [
                         'attribute' => 'Total_Action_warranty',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Warranty'
                     ],
                     [
                         'attribute' => 'Total_Action_actualProgress',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'ActualProgress'
                     ],
                     [
                         'attribute' => 'Total_Action_Person',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Person'
                     ],
                     [
                         'attribute' => 'Total_Action_Tender',
                         'headerOptions' => ['class' => 'show-on-per-action'],
                         'filterOptions' => ['class' => 'show-on-per-action'],
                         'contentOptions' => ['class' => 'show-on-per-action'],
                         'label' => 'Tender'
                     ],
 //                    [
 //                        'attribute' => 'total_action',
 //                        'headerOptions' => ['class' => 'show-on-per-action'],
 //                        'filterOptions' => ['class' => 'show-on-per-action'],
 //                        'contentOptions' => ['class' => 'show-on-per-action total-background'],
 //                        'label' => 'TotalAction'
 //                    ],        

                     [
                         'attribute' => 'Total_Process_CashRequest',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'CashRequest'
                     ],                    
                     [
                         'attribute' => 'Total_Process_claim',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Claim'
                     ],
                     [
                         'attribute' => 'Total_Process_Invoice',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Invoice'
                     ],
                     [
                         'attribute' => 'Total_Process_Contract',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Contract'
                     ],
                     [
                         'attribute' => 'Total_Process_Budget',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Budget'
                     ],
                     [
                         'attribute' => 'Total_Process_capital',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Capital'
                     ],
                     [
                         'attribute' => 'Total_Process_debitmemo',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Debitmemo'
                     ],
                     [
                         'attribute' => 'Total_Process_warranty',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'Warranty'
                     ],
                     [
                         'attribute' => 'Total_Process_actualProgress',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'ActualProgress'
                     ],
                     [
                         'attribute' => 'Total_Process_Tender',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'ActualProgress'
                     ],
                     [
                         'attribute' => 'Total_Process_Person',
                         'headerOptions' => ['class' => 'show-on-per-process'],
                         'filterOptions' => ['class' => 'show-on-per-process'],
                         'contentOptions' => ['class' => 'show-on-per-process'],
                         'label' => 'ActualProgress'
                     ],

                 ],
             ]);
            ?>
            </div>
        </div>
        <div class="tab-pane fade" id="graph-tab">
            <?= 
            $this->render ('_totaluserActivityChart',[
                'graphData'=>$graphData,
            ]);
            ?>
            
            
        </div>
    </div>
</div>





        <!--< ? = yii\helpers\Html::endForm(); ?>-->
        <?php Pjax::end(); ?>
        <?php GridViewBox::end() ?> 
<?php
yii\bootstrap\Modal::begin([
    'header' => '<h5 class="p0 m0">User Activity Graph</h5>',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
    'options' => [
        'id' => 'modal-user-activity-graph',
        'tabindex' => false,
    ]
])
?>
    <?php Pjax::begin(['id' => 'user-activity-graph-pjax']); ?>  
    <?php Pjax::end(); ?>
<?php yii\bootstrap\Modal::end() ?>
<script>
    $('#user-activity-pjax').on("pjax:send", function (dt, res, ev) {
        ajaxProgressLoader('show', '.grid-box-body');
    });
    $('#user-activity-pjax').on("pjax:complete", function (dt, res, ev) {
        ajaxProgressLoader('hide', '.grid-box-body');
    });

    function ddlViewModeChane(obj) {
        var viewMode = $(obj).val();
        if (viewMode == 'perAction') {
            $('.show-on-per-action').css('display', '');
            $('.show-on-per-process').css('display', 'none');
        } else {
            $('.show-on-per-action').css('display', 'none');
            $('.show-on-per-process').css('display', '');
        }
    }
    $(document).ready(function () {
        ddlViewModeChane($('#view-mode'));
    });
    $(document).ajaxComplete(function () {
        ddlViewModeChane($('#view-mode'));

        $('.datepicker-fa').each(function () {
            $(this).datepicker({dateFormat: 'yy/mm/dd'});
        });
    });
    function reloadGridView() {

        $.pjax.reload({container: "#user-activity-pjax",
            data: $('#user-activity-pjax input,#user-activity-pjax select').serialize(),
            timeout: false,
            push: false,
            replace: false,
        });

    }
    function inpKeyDown(e) {
        if (e.which == 13) {
            reloadGridView();
            return false;
        }
        return true;
    }
    function btnGraphClick(obj){
        $('#modal-user-activity-graph .modal-header h5').text('User Activity Graph (From '+$('#useractivitysearch-fromdate').val()+' To '+$('#useractivitysearch-todate').val()+')');
        var url = $(obj).attr('data-url');
        $('#modal-user-activity-graph').modal('show');
        $.pjax.reload({container: "#user-activity-graph-pjax",
            url: url,
            data: $('#user-activity-pjax input,#user-activity-pjax select').serialize(),
            timeout: false,
            push: false,
            replace: false,
        });
        return false;
        
    }
</script>