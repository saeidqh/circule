<?php

use app\modules\workflow\assets\WorkflowListAsset;
use app\components\PageHeader;
use app\components\DashboardPanel;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\components\PopupBox;

$this->title = "WORKFLOW :: Workflow List";

WorkflowListAsset::register($this);

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
<style >
    .process-detail:hover{
        text-decoration: none;
    }
    .process-detail:active{
        text-decoration: none;
    }
</style>
<div class="col-md-7 pull-left text-left" style="" >
    <div class="btn-group">

        <?php
        echo '<a type="button " class="btn btn-default psa-btn-default-active" href="' . Yii::$app->urlManager->createAbsoluteUrl('workflow/workflow/workflowlist') . '" >Workflow List</a>';
        echo '<a type="button " class="btn btn-default" href="' . Yii::$app->urlManager->createAbsoluteUrl('reportWorkflow/management/report-list') . '" >Report Manager</a>';
        echo '<a type="button " class="btn btn-default" href="' . Yii::$app->urlManager->createAbsoluteUrl('workflow/measure/list') . '" >Measure List</a>';
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 col-lg-12 col-sm-12" >
        <?php
        DashboardPanel::begin([
            'title' => 'Process List',
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
        <div class='row'>
            <?php Pjax::begin(['enablePushState' => false, 'linkSelector' => '']); ?>
            <?=
            \kartik\grid\GridView::widget([
                'id' => 'po-report-invoice-type',
                'dataProvider' => $processDataProvider,
                'filterModel' => $processModel,
                'tableOptions' => ['class' => 'table table-striped table-hover non-responsive psa-grid table-bordered'],
                'floatHeader' => true,
                'columns' => array(
                    'ProcessID',
                    'ProcessTitle',
                    [
                        'attribute' => 'ProccessTypeTitle',
                        'label' => 'Process Type',
                        'value' => 'type.proccessTypeTitle'
                    ],
                    [
                        'attribute' => 'CompanyTitle',
                        'label' => 'Company',
                        'value' => 'company.PersonTitle'
                    ],
                    [
                        'header' => 'Action',
                        'format' => 'raw',
                        'value' => function($data) {
                    return '<a class="link-primary" style="color:purple;"  data-toggle="tooltip" data-placement="top" data-original-title="Process Actions" href="' . Yii::$app->urlManager->createAbsoluteUrl(['workflow/process/action-list', 'pid' => $data['ProcessID']]) . '"><i class="fa-play s12"></i></a>';
                },
                    ],
                    [
                        'header' => 'Graph',
                        'format' => 'raw',
                        'value' => function($data) {
                    return '<a class="process-detail"  data-toggle="tooltip" data-placement="top" data-original-title="Process Detail"   ProcessTitle="' . $data['ProcessTitle'] . '" href="' . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/process-detail', 'pid' => $data['ProcessID']]) . '" target="_blank">'
                                .'<i class="fa-file s16" style="color:#717171;" ></i>'
                                .'<i class="en-flow-branch s16" style="transform: rotate(90deg); display: block; margin-top: -11px; margin-left: -10px; color: #FFBD05;text-shadow: 0px 0 3px black;"></i>'
                            .'</a>';
                },
                    ],
                ),
            ]);
            ?>
<?php Pjax::end(); ?>
        </div>
<?php DashboardPanel::end(); ?>
    </div>
</div>
<?php PopupBox::begin() ?>
<?php PopupBox::end() ?> 	
