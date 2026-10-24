<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use app\modules\workflow\assets\GraphAsset;
use kartik\grid\GridView;

GraphAsset::register($this);
$this->title = "Workflow :: Graph view";

$this->params['breadcrumbs'] = [
    'lable' => 'workflow',
];
if ($mode == 'normal') {
    echo PageHeader::widget([
        'title' => $this->title,
        'icon' => 'en-flow-tree',
    ]);
}
?>
<style>
    tr.kv-page-summary td:not(:last-child) {
        display: none;
    }
    tr.kv-page-summary td:last-child {
        text-align: right;
    }
</style>
<div class="row" >
    <?php
    GridViewBox::begin([
        'cssClass' => 'panel-head-brown',
        'htmlOptions' => [
            'style' => 'background-position-y: -40px; height:65px;',
        ],
    ]);
    ?>

    <?php
    $headHtml = '';
    $align = 'text-align:left;';
    $c = 0;

    foreach ($gInfo as $k => $v) {
        if ($k != '') {
            $headHtml .= "<div class='" . (($c++ < 4) ? 'col-md-4' : 'col-md-8') . " col-sm-12' ><div style='height:30px'><b>$k:</b>"
                    . "<span style='color:rgb(41, 138, 146)'>&nbsp;$v</span></div></div>";
        } else {
            $align = 'text-align:right;';
            $headHtml .= "<span  style='color:rgb(41, 138, 146);' align='right' >&nbsp;$v</span>";
        }
    }
    ?>
    <div class="row font-tahoma" style="margin: -8px; <?php echo $align ?>">
        <?php
        echo $headHtml;
        ?>
    </div>    



    <?php GridViewBox::end() ?> 

</div>
<div class="row">
    <?=
    yii\helpers\Html::button('Show Action Detail', [
        'id' => 'button-workflow-graph',
        'class' => 'btn btn-sm btn-default pull-right button-workflow-graph',
    ]);
    ?>
</div>

<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-24 p0" id="graph-workflow-div">
        <?php
        GridViewBox::begin([
            'cssClass' => 'graph-box-main fixed-box panel-head-blue',
            'htmlOptions' => [
                'active-nodes' => $data,
                'style' => 'top:0px;bottom:0px;max-height:750px;0px;padding:0;right:0px;overflow:hidden;cursor: move;position:inherit;',
            ],
        ]);
        ?>

        <div class="row ">
            <div class="row font-tahoma font-bold text-muted h4" style="margin-bottom: -40px;margin-top: 0px; direction: rtl;">
                <center><strong><?php echo $processEntityModel->process->ProcessTitle ?></strong></center>
            </div> 

            <div class="graph-view" style="opacity:0;margin: -15px;">

                <?php
                $path = Yii::$app->getModule('workflow')->getBasePath() . '/bower/graph/ProccessDiagram' . $graphId . '.svg';
                require_once $path;
                ?>
            </div>
        </div>

        <?php GridViewBox::end() ?> 
    </div>
    <div class="col-md-5 col-lg-5 col-sm-8 animated bounceInRight p0" style='height:480px;padding:0px;animation-duration: 2s;display: none;'  id="table-action-detail">
        <?php
        GridViewBox::begin([
        ]);
        ?>
        <div class="row m5">
            <?php
            $previousActionAccess = \app\access\AccessControl::can('workflow/next-action/previous-action', ['peid' => $processEntityModel->ProcessEntityID]);
            if ($previousActionAccess) {
                echo \yii\bootstrap\Html::button('<i class="im-arrow-left2">&nbsp&nbspBack to previous action</i>', [
                    'class' => 'btn btn-sm btn-danger pull-left',
                    'onclick' => "previousActionConfirm(this)",
                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/previous-action-confirm', 'peid' => $processEntityModel->ProcessEntityID]),
                ]);
            }
            echo \yii\bootstrap\Html::a('<i class="im-file-excel">&nbsp&nbspExcel</i>', '#', [
                'class' => 'btn btn-sm btn-default pull-right',
                'target' => '_blank',
                'onclick' => "exportExcelActionEntity(this)",
                'export-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/export/action-entity', 'peid' => $processEntityModel->ProcessEntityID]),
            ]);
            ?>
        </div>
        <?php



        echo GridView::widget([
            'id' => 'action-entity-grid',
            'dataProvider' => $actionEntityProvider,
            'pjax' => true,
            'autoXlFormat' => true,
            'floatHeader' => FALSE,
            'bordered' => TRUE,
            'tableOptions' => ['class' => 'table table-striped table-hover non-responsive table table-bordered'],
            'rowOptions' => function ($model, $key, $index, $grid) {
        $class = $index % 2 ? 'odd' : 'even';
        return array('key' => $key, 'index' => $index, 'class' => $class);
    },
            'options' => [
                'style' => 'font-family:tahoma; font-size:11px;',
            ],
            'columns' => [
                    //begin +
                [
                    'class' => \kartik\grid\ExpandRowColumn::className(),
                    'attribute' => 'ID',
                    'value' => function($url, $model, $key) {
                        return \kartik\grid\GridView::ROW_COLLAPSED;
                    },
//                    'detailUrl' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/measure', 'peid'=>$processEntityModel->ProcessEntityID]),
//                    'detailUrl' => function($data) use($processEntityModel){
//                        return Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/measure', 'peid'=>$processEntityModel->ProcessEntityID, 'aeid'=> $data['ID']]);
//                    },
                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                    'expandOneOnly' => true,
                    'expandIcon' => '<span class="fa-plus"></span>',
                    'collapseIcon' => '<span class="fa-minus"></span>',
                    'detail' => function($data) use($processEntityModel){
                        $actionOrderMeasureValueSearch = new \app\modules\workflow\models\ActionOrderMeasureValueSearch();
                        $actionOrderMeasureValueSearch->ActionEntityIDRef = $data['ID'];
                        $actionOrderMeasureValueProvider = $actionOrderMeasureValueSearch->search(Yii::$app->request->queryParams);
                        return Yii::$app->controller->renderPartial('_measuresValueDetail',[
                                'actionOrderMeasureValueSearch' => $actionOrderMeasureValueSearch,
                                'actionOrderMeasureValueProvider' => $actionOrderMeasureValueProvider
                        ]);
                    }
                ],
                    //end +
                [
                    'attribute' => 'ActionTitle2',
                    'label' => 'ارجاع گیرنده',
                    'options' => [
                        'width' => '120px',
                    ],
                    'pageSummary' => false,
                ],
                [
                    'attribute' => 'UserName',
                    'label' => 'ارجاع دهنده',
                    'value' => 'UserName',
                    'options' => [
                        'width' => '80px',
                    ],
                    'pageSummary' => false,
                ],
                [
                    'attribute' => 'ActionDateHijri',
                    'label' => 'تاریخ ارجاع',
                    'options' => [
                        'width' => '85px',
                    ],
                    'pageSummary' => false,
                ],
                [
                    'attribute' => 'ActionAutoMessage',
                    'label' => 'پیام پیش فرض',
                    'options' => [
                        'width' => '85px',
                    ],
                    'pageSummary' => false,
                ],
                [
                    'attribute' => 'ActionMessage',
                    'label' => 'پیام',
                    'options' => [
                        'width' => '85px',
                    ],
                    'pageSummary' => false,
                ],
                [
                    'attribute' => 'Duration',
                    'label' => 'زمان',
                    'options' => [
                        'width' => '85px',
                    ],
                    'pageSummary' => true,
                    'pageSummaryFunc' => function() use($sumDuration) {
                        return Yii::$app->formatter->asHtml($sumDuration, null);
                    },
                    'pageSummaryOptions' => [
                        'colspan' => 6,
                    ]
                ],
            ],
            'responsive' => true,
            'hover' => true,
            'showPageSummary' => true,
        ]);
        ?>
        <?php GridViewBox::end() ?>



    </div>

</div>
<?php
yii\bootstrap\Modal::begin([
    'id' => 'previous-action-confirm-modal',
]);
yii\bootstrap\Modal::end();
?>


<script type="text/javascript">
    var balloonData = <?= $jsonActions; ?>;

    $(document).ready(function () {
        $('button#button-workflow-graph').click(function () {
            if ($('#table-action-detail').css('display') == 'none') {
                $('#table-action-detail').fadeIn('slow');
                $('#graph-workflow-div').removeClass('col-md-12 col-lg-12 col-sm-24')
                $('#graph-workflow-div').addClass('col-md-7 col-lg-7 col-sm-16')
                $('button#button-workflow-graph').text('Hide Action Detail');
            } else {
                $('#table-action-detail').fadeOut('slow');
                $('#graph-workflow-div').removeClass('col-md-7 col-lg-7 col-sm-16')
                $('#graph-workflow-div').addClass('col-md-12 col-lg-12 col-sm-24')
                $('button#button-workflow-graph').text('Show Action Detail');
            }
            loadgraph();
        });

    });

    function loadgraph() {
        inisiateGraph();
        var actNodes = $('.graph-box-main').attr('active-nodes');
        activeNodes(actNodes);
        $('.graph-box-main .graph-view').animate({
            opacity: '10'
        }, 2000);
    }

    function previousActionConfirm(obj) {
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
        }).done(function (data) {
            $('#previous-action-confirm-modal .modal-body').html(data);
            $('#previous-action-confirm-modal').modal('show');
        }).error(function () {
            showMsgBoxInBottomRight("Error on connecting to server.", "error");
            return false;
        });
        return false;
    }

    function exportExcelActionEntity(obj) {
        var exportUrl = $(obj).attr('export-url');
        $(obj).attr('href', exportUrl);
    }
</script>
<?php
$this->registerJs("
        $('button#button-workflow-graph').click();
        loadgraph();
    ", \yii\web\View::POS_READY);
?>


<?php
$this->registerCss("
input[type=text]{height: 30px;} 
.table > thead > tr > td{padding:4px;} 


.table {font-family: tahoma; font-size:11px; width:100%; } 

.table input[type='text']{font-family: tahoma; font-size:10px; width:100%;} 
.table tr th{text-align:center;}
.table tr td{padding:0 important;text-align: center;}
.table>tbody>tr>td, 
.table>tbody>tr>th, 
.table>tfoot>tr>td, 
.table>tfoot>tr>th, 
.table>thead>tr>td, 
.table>thead>tr>th {
    padding: 3px !important;
    vertical-align: middle;
}


.table .filters td{
    background: background:#f8e777;
}
.table td{
    max-height: 42px !important;
    
}
.table .table-responsive1
{
    overflow: visible !important;    
}
.table tbody>tr>td, 
.table tbody>tr>th, 
.table tfoot>tr>td, 
.table tfoot>tr>th, 
.table thead>tr>td, 
.table thead>tr>th {
    border-top: 1px solid #E7E7E7;
}
.table.table-bordered thead tr{
//    background:#CCFF33;
//    background:#79859b;
    height:35px;
    color: #FFFF00;
    background-color: #79859b;
    border-color: #f78db8
    

}


.table .td-indent-popup{
    min-width: 10px;
    background: #eee !important; 
    position:relative;
    border-color: #eee !important;
}
.table .td-indent-popup .indent-line{
    border-left: solid 1px white;
    position:absolute;
    top:0;
    bottom: 0;
    left:0px;
    right:0;
    
       
}

.table .td-indent{
    width:10px;
    background: #9543FD; 
}
");
?>
