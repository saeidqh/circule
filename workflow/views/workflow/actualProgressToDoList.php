<?php

use app\components\PageHeader;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\assets\JdateTimeAsset;
use app\assets\DatepickerAsset;
use yii\bootstrap\Modal;

omnilight\assets\SweetAlertAsset::register($this);
$accessInsert = app\access\AccessControl::can('budget/actual-progress/insert');


app\modules\workflow\assets\BudgetAsset::register($this);
DatepickerAsset::register($this);
JdateTimeAsset::register($this);

$this->title = Yii::t('workflow', 'WORKFLOW :: Actual Progress Annual Budget todo list') ;

$this->params['breadcrumbs'] = [
    'leble' => 'WORKFLOW',
];
?>

<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>
<div class="row">
    <?php app\components\GridViewBox::begin() ?>
    <div class="row">

        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            if ($accessInsert) {
                echo Html::a("<i class='fa-plus'></i> &nbsp;". Yii::t('workflow', 'Run Actual Progress'), "#", [
                    'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                    'id' => 'btn-new-budget',
                    'onclick' => "showActualProgressModal(this); return  false;",
                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['budget/actual-progress/select-month']),
                ]);
            }
            ?>
        </div>

        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
<?php
$btns = $actualProgressSearchModel->ACTUAL_PROGRESS_ANNUAL_BUDGET_TODO_REPORT_TYPE;
for ($i = 0; $i < count($btns); $i++) {
    ?>
                <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-pjax="0" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/actual-progress", 'type' => $btns[$i]['value']]) ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                    <?php
                }
                ?>
            <?=
            yii\bootstrap\Html::a('<span class="im-file-excel"></span> ' . Yii::t('budget', 'Export'), '#', [
                'class' => 'btn btn-success',
                'style' => 'padding: 4px 12px;',
                'target' => '_blank',
                'export-url' => Yii::$app->urlManager->createAbsoluteUrl(["budget/export/actual-progress-export", 'type' => Yii::$app->request->get('type')]),
                'onClick' => 'exportExcelFile(this);',
                'data-toggle' => 'tooltip',
                'data-placement' => Yii::t('workflow', 'data-placement'),
                'data-pjax' => 0,
                'data-original-title' => 'برنامه و بودجه عملیاتی سالیانه',
            ]);
            ?>
        </div>

    </div>
    <div class="row">
<?php Pjax::begin(['id' => 'actual-progress-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-actual-progress-todo',
            'dataProvider' => $actualProgressDataProvider,
            'filterModel' => $actualProgressSearchModel,
            'floatHeader' => true,
            'columns' => [
                [
                    'attribute' => 'actualProgressid',
                    'label' => Yii::t('workflow', 'ID'),
                    'value' => 'actualProgressid',
                    'options' => [
                        'width' => '20px',
                    ],
                ],
                [
                    'attribute' => 'BudgetID',
                    'label' => Yii::t('workflow', 'Budget'),
                    'value' => function($model) {
                        return $model['Year'] . "-" . $model['BudgetID'];
                    },
                    'options' => [
                        'width' => '80px',
                    ],
                ],
                [
                    'attribute' => 'planType',
                    'label' => Yii::t('workflow', 'Plan Type'),
                    'value' => 'planType'
                ],
                [
                    'attribute' => 'obsTitle_475',
                    'label' => Yii::t('workflow', 'Departement'),
                    'value' => 'obsTitle_475',
                ],
                [
                    'attribute' => 'epsTitle',
                    'label' => Yii::t('workflow', 'EPS'),
                    'value' => 'epsTitle',
                ],
                [
                    'attribute' => 'PlanTitle',
                    'label' => Yii::t('workflow', 'Plan Title'),
                    'value' => 'PlanTitle'
                ],
                [
                    'attribute' => 'LastUserName',
                    'label' => Yii::t('workflow', 'Last User'),
                    'value' => 'LastUserName',
                ],
                [
                    'attribute' => 'lastActionTitle',
                    'label' => Yii::t('workflow', 'Last Action Title'),
                    'value' => 'lastActionTitle',
                ],
                [
                    'attribute' => 'LastActionDateHijri',
                    'label' => Yii::t('workflow', 'Last Action Date'),
                    'value' => 'LastActionDateHijri',
                ],
                [
                    'attribute' => 'fromMonth',
                    'filter' => $arrayMouth,
                    'label' => Yii::t('workflow', 'From'),
                    'format' => 'raw',
                    'value' => function($model)use($arrayMouth) {
                        return '<table class="inner-table" style="background-color: #74f9a9;    width: -webkit-fill-available;height: 45px">'
                                . '<tr><td class="label-silver">' . $arrayMouth[$model['fromMonth']] . '</td></tr>'
                                . '</table>';
                        return $arrayMouth[$model['fromMonth']];
                    },
                    'options' => [
                        'width' => '30px',
                    ],
                ],
                [
                    'attribute' => 'toMonth',
                    'filter' => $arrayMouth,
                    'label' => Yii::t('workflow', 'To'),
                    'format' => 'raw',
                    'value' => function($model)use($arrayMouth) {
                        return '<table class="inner-table" style="background-color: #74f9a9;    width: -webkit-fill-available;height: 45px">'
                                . '<tr><td class="label-silver">' . $arrayMouth[$model['toMonth']] . '</td></tr>'
                                . '</table>';
                        return $arrayMouth[$model['toMonth']];
                    },
                    'options' => [
                        'width' => '30px',
                    ],
                ],
                [
                    'header' => Yii::t('workflow', 'Actions'),
                    'class' => yii\grid\ActionColumn::className(),
                    'template' => '{nextaction}{diagram}{detail}{delete}',
                    'options' => [
                        'width' => '80px',
                    ],
                    'buttons' => [
                        'nextaction' => function($url, $model, $key) {
                            return (Yii::$app->request->get('type') == 'ToDo') ? Html::a(
                                            '<i class="im-arrow-right2 rotate-360 s16"></i>', "#", [
                                        'onclick' => "showModal(this); return  false;",
                                        'class' => 'nextAction btn btn-xs tip',
                                        'data-original-title' => "Next Action",
                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'type' => 'actualProgress', 'peid' => $model['ProcessEntityID']]),
                                        'style' => 'color:#AACF72;padding:1px 2px;',
                                        'data-pjax' => "0",
                                        'data-peid' => $model['ProcessEntityID'],
                                        'data-rowid' => $model['actualProgressAnnualBudgetIdRef'],
                                            ]
                                    ) : '';
                        },
                                'diagram' => function($url, $model, $key) {
                            return Html::a('<i class="en-flow-branch s16"></i>', Url::to(['/workflow/workflow/graph', 'peid' => $model['ProcessEntityID']]), [
                                        'data-original-title' => 'Action Diagram',
                                        'class' => 'btn btn-xs tip',
                                        'data-pjax' => '0',
                                        'style' => 'padding:1px 2px;color:#646464;',
                                        'target' => '_blank',
                            ]);
                        },
                                'detail' => function($url, $model, $key) {
                            return Html::a('<i class="im-bold" style="font-size:18px"></i>', Url::to(['/budget/actual-progress/view', 'peid' => $model['ProcessEntityID']]), [
                                        'data-original-title' => 'Actual Progress Detail',
                                        'class' => 'btn btn-xs tip',
                                        'data-pjax' => '0',
                                        'style' => 'padding:1px 2px;color:#AE85BE;',
                                        'target' => '_blank',
                                        'onclick' => 'return hideNextActionBox(' . $model["ProcessEntityID"] . ');',
                            ]);
                        },
                                'delete' => function($url, $model, $key) {
                            return (Yii::$app->request->get('type') == 'All') ? Html::a(
                                            '<i class="fa-trash" style="font-size:18px"></i>', "#", [
                                        'data-original-title' => 'Actual Progress Delete',
                                        'class' => 'btn btn-xs tip',
                                        'data-pjax' => '0',
                                        'style' => 'color:#466FA3;padding:1px;',
                                        'delete-url' => Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/delete-confirm', 'peid' => $model['ProcessEntityID']]),
                                        'onclick' => 'showDeleteActualProgressModal(this)',
                                    ]) : '';
                        },
                            ]
                        ],
                    ],
                ]);
                ?> 
                <?php Pjax::end(); ?>
            </div>

        <?php app\components\GridViewBox::end() ?>
        </div>

        <?php ?>
        <script>
            function showDeleteBudgetModal(obj) {
                $.ajax({
                    url: $(obj).attr('delete-url'),
                }).done(function (data) {
                    $('#delete-budget-modal .modal-body').html(data);
                    $('#delete-budget-modal').modal('show');
                });
                return false;
            }

            function insertActualProgress(obj) {
                ajaxProgressLoader('show', '#grid-actual-progress-todo');
                var url = $(obj).attr('data-url');
                var month = $("#selected-month").val();
                url = url + "&month=" + month;

                $.ajax({
                    url: url,
                }).done(function (data) {
                    $.pjax.reload({
                        container: '#actual-progress-pjax',
                        timeout: false,
                        push: false,
                        replace: false,
                    });
                    swal({
                        title: 'successfully',
                        type: 'success',
                        text: "successfully Insert Actual Progress.",
                        timer: 1000,
                        showConfirmButton: false,
                    });
                    ajaxProgressLoader('hide', '#grid-actual-progress-todo');
                    $('#actual-progress-modal').modal('hide');
                }).error(function () {
                    swal('Error', "Error Saving Actual Progress", 'error');
                    ajaxProgressLoader('hide', '#grid-actual-progress-todo');
                    showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
                    return false;
                });
            }
            function exportExcelFile(selector) {
                var filter = $('#grid-actual-progress-todo-filters input').serialize();
                var exportUrl = $(selector).attr('export-url') + '&' + filter;
                $(selector).attr('href', exportUrl);
            }

        </script>
        <?php
        Modal::begin([
            'id' => 'budget-modal',
            'size' => Modal::SIZE_LARGE,
        ]);
        Pjax::begin([
            'id' => 'pjax-modal',
        ]);
        $js = "
        function showModal(obj){
            var url = $(obj).attr('data-url');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                 $('#budget-modal').modal('show');
            });

            return false;
        }
        
    ";
        $this->registerjs($js, \yii\web\View::POS_END);
        Pjax::end();
        Modal::end();


        Modal::begin([
            'id' => 'actual-progress-modal',
            'size' => Modal::SIZE_SMALL,
            'header' => Yii::t('workflow', "Select Actual Progress Month"),
        ]);
        Pjax::begin([
            'id' => 'pjax-actual-progress-modal',
        ]);
        $js = "
        function showActualProgressModal(obj){
            var url = $(obj).attr('data-url');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-actual-progress-modal').html(data);
                $('#actual-progress-modal').modal('show');
            });

            return false;
        }
        
    ";
        $this->registerjs($js, \yii\web\View::POS_END);
        Pjax::end();
        Modal::end();
        