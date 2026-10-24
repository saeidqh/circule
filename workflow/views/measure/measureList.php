<?php

use app\components\PageHeader;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use app\components\DashboardPanel;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);

$this->title = "WORKFLOW :: Measure List";

$this->params['breadcrumbs'] = [
    'lable' => 'workflow'
];
//
?>

<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>

<div class="col-md-7 pull-left text-left" style="" >
    <div class="btn-group">
        <?php
        echo '<a type="button " class="btn btn-default" href="' . Yii::$app->urlManager->createAbsoluteUrl('workflow/workflow/workflowlist') . '" >Workflow List</a>';
        echo '<a type="button " class="btn btn-default" href="' . Yii::$app->urlManager->createAbsoluteUrl('reportWorkflow/management/report-list') . '" >Report Manager</a>';
        echo '<a type="button " class="btn btn-default psa-btn-default-active" href="' . Yii::$app->urlManager->createAbsoluteUrl('workflow/measure/list') . '" >Measure List</a>';
        ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">

        <?php
        DashboardPanel::begin([
            'title' => 'Measure List',
            'icon' => 'en-circles',
            'ajax_url' => '',
            'header' => '',
            'footer' => '',
            'refresf_btn' => false,
            'close_btn' => false,
            'toggle_btn' => false,
            'template' => 'panel-default plain',
            'headClass' => 'white-bg',
            'headBg' => 'panel-head-blue',
        ]);
        ?>
        <div class="row" style='margin-top: -40px'>
            <div class='col-md-6 pull-right text-right' >
                <?=
                \kartik\helpers\Html::a('<i class="fa-plus "></i>&nbsp;New Measure', '#', [
                    'onclick' => ' return btnShowMeasureModal(this);',
                    'class' => 'btn btn-primary btn-sm',
                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/measure/create']),
                ]);
                ?>
            </div>
        </div>
        <?= Html::label('Measure List'); ?>
        <?=
        GridView::widget([
            'id' => 'measure-grid',
            'pjax' => true,
            'pjaxSettings' => [
                'options' => [
                    'id' => 'measure-grid-pjax',
                    'clientOptions' => [
                        'container' => '#measure-pjax'
                    ]
                ]
            ],
            'dataProvider' => $dataProvider,
            'filterModel' => $measureSearchModel,
            'columns' => [
                [
                    'attribute' => 'MeasureID',
                    'label' => 'Measure ID',
                    'options' => [
                        'width' => '100px'
                    ],
                ],
                'MeasureTitle',
                [
                    'class' => kartik\grid\ActionColumn::className(),
                    'template' => '{edit}',
                    'buttons' => [
                        'edit' => function ($url, $model, $key) {
                            return Html::a('<i class="fa-pencil s16"></i>', '#',
                                    [
                                        'data-pjax' => 0,
                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/measure/update', 'mid' => $model->MeasureID]),
                                        'onclick' => 'return btnShowMeasureModal(this);',
                                        'data-toggle' => "tooltip",
                                        'data-placement' => "top",
                                        'data-original-title' => "Edit",
                                    ]
                            );
                        },
                    ]
                ],
            ],
        ]);
        ?>
        <?php DashboardPanel::end(); ?>
    </div>
</div>
<?php
Modal::begin([
    'header' => '<h5 class="p0 m0">New/Edit Measure</h5>',
    'id' => 'measure-modal',
    'size' => Modal::SIZE_DEFAULT,
]);
?>

<?php
Pjax::begin([
    'id' => 'measure-pjax',
]);
?>
<?php Pjax::end(); ?>

<?php Modal::end(); ?>

<?php
$this->registerCss("
input[type=text]{height: 30px;} 
.table > thead > tr > td{padding:4px;} 
.table {font-family: tahoma; font-size:11px; width:100%; } 

.table input[type='text']{font-family: tahoma; font-size:12px; width:100%;} 
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
    background:#d1edff;
    height:35px;
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

<script>
    $(document).ready(function () {
        $('#measure-pjax').on("pjax:complete", function (dt, res, ev) {
            ajaxProgressLoader('hide', '#measure-modal .modal-body');
        });
    });

    function btnShowMeasureModal(obj) {
        ajaxProgressLoader('show', '#measure-modal .modal-body');
        $('#measure-modal').modal('show');
        var url = $(obj).attr('data-url');
        $.pjax.reload({
            container: "#measure-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false
        });
        return false;
    }

    function reloadList() {
        $.pjax.reload({
            container: "#measure-grid-pjax",
            timeout: false,
            push: false,
            replace: false,
        });

    }
    function measureCallbackPjax(data) {
        if (data.result) {
            swal({
                title: 'Success',
                type: 'success',
                text: data.message,
                timer: 1000,
                showConfirmButton: false
            });
            $('#measure-modal').modal('hide');
            reloadList();

        } else {
            swal('Error', data.message, 'error');
        }
    }


</script>
