<?php

use app\modules\workflow\assets\WorkflowListAsset;
use app\components\PageHeader;
use yii\widgets\Pjax;
use kartik\helpers\Html;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);
$this->title = "Process Actions List";

$canProcessUpdate = \app\access\AccessControl::can('workflow/process/update',
        ['pid' => $processModel->ProcessID]);

WorkflowListAsset::register($this);
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>

<style>
    .grid-font .summary, 
    .grid-font td, 
    .grid-font th {
        font-size: 11px;
    }
    .action-order .kv-grid-wrapper,
    .action-list .kv-grid-wrapper{
        height:430px;
    }
    .action-order .pagination, 
    .action-list .pagination{
        margin:0; 
    }
    .action-list table thead,
    .action-list .summary {
        background-color: #F7EA8F;
    }
    .action-order table thead,
    .action-order .summary {
        background-color: #EAA0A0;
    }
    .grid-box .grid-box-body {
        padding: 15px 0 5px 0; 
    }
    .green-box{
        padding: 5px;
        border-radius: 4px;
        box-shadow: none;
        border:solid 1px #C5A4CA;
        overflow: hidden;
        position: relative;
        background:rgb(247, 233, 249);
        color: black;
    }
    #process-action-main-pjax{overflow: hidden;}
</style>
<?php if (count($arrProcessChilds) > 0) { ?>
    <div class='row' >
        <div class='col-md-12'>
            <div class="row green-box mb5 mr0">
                <div class='col-md-3'>
                    <b>Subprocess List </b>
                </div>
                <div class='col-md-6'>
                    <?=
                    kartik\widgets\Select2::widget([
                        'name' => 'subProcess',
                        'data' => $arrProcessChilds,
                        'options' => [
                            'id' => 'sub-process',
                            'prompt' => 'Select a Process...',
                            'onchange' => 'return loadSubprocessActions("sub")'
                        ]
                    ]);
                    ?>
                </div>
                <div class='col-md-3'>
                    <?=
                    Html::a('<i class="fa-backward"></i>&nbsp;&nbsp;Back To Main Process', '#', [
                        'onclick' => 'return loadSubprocessActions("main");',
                        'class' => 'btn btn-primary col-md-12',
                        'style' => 'display:none',
                        'id' => 'btn-return-back-to-main-process',
                        'data-pid' => $processModel->ProcessID,
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="row process-actions grid-font " style="overflow-y: hidden">
    <?php
    Pjax::begin([
        'id' => 'process-action-main-pjax'
    ])
    ?>

    <div class='col-md-8 '>
        <div class='row action-list ' style='border-radius:4px; ;border:1px solid #C7C7C7;'>
            <div class="col-md-12" ><span style=''>
                    <i class="im-arrow-right2 s18"></i>&nbsp;
                    <b>Process Action List</b></span>
            </div>
            <?php
            echo \kartik\grid\GridView::widget([
                'id' => 'process-action-grid',
                'dataProvider' => $actionDataProvider,
                'filterModel' => $actionSearchModel,
                'layout' => "{items}\n{pager}\n{summary}",
                'pjax' => true,
                'floatHeader' => true,
                'floatOverflowContainer' => true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'process-action-grid-pjax',
                    ],
                ],
                'columns' => [
                    [
                        'attribute' => 'ActionID',
                        'options' => [
                            'width' => '30px'
                        ],
                    ],
                    'ActionTitle1',
                    'ActionTitle2',
                    [
                        'label' => 'Type',
                        'format' => 'raw',
                        'value' => function($model) {
                            $value = '<span class="badge badge-primary mr10 mb10" style="width:46px" ><i class="fa-arrow-right s12"></i></span>';
                            if ($model->isStartAction) {
                                $value = '<span class="badge badge-success mr10 mb10" style="width:46px" >Start</span>';
                            } else if ($model->isEndAction) {
                                $value = '<span class="badge badge-danger mr10 mb10" style="width:46px" >End</span>';
                            }
                            return $value;
                        },
                        'options' => [
                            'width' => '40px'
                        ],
                    ],
                    [
                        'attribute' => 'ActionOrder',
                        'options' => [
                            'width' => '40px'
                        ],
                    ],
                    [
                        'class' => kartik\grid\ActionColumn::className(),
                        'template' => '{sms}&nbsp;&nbsp;{edit}&nbsp;&nbsp;{param}',
                        'options' => [
                            'width' => '40px'
                        ],
                        'buttons' => [
                            'sms' => function($key, $model, $index) {
                                return kartik\helpers\Html::a('<i class="br-envelope s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['sms/sms/settings', 'aid' => $model->ActionID, 'pid' => $model->ProcessIDRef]), [
                                            'data-pjax' => '0',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-original-title' => 'SMS Settings',
                                            'target' => "_blank"
                                ]);
                            },
                            'edit' => function($key, $model, $index)use($processModel, $canProcessUpdate) {
                                if ($canProcessUpdate) {
                                    return kartik\helpers\Html::a('<i class="fa-pencil s16"></i>', '#', [
                                                'data-pjax' => '0',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'data-original-title' => 'Edit',
                                                'onclick' => 'return btnEditProcessActionClick(this)',
                                                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action/update', 'pid' => $processModel->ProcessID, 'aid' => $model->ActionID]),
                                    ]);
                                }
                            },
                            'param' => function($key, $model, $index)use($processModel, $canProcessUpdate,$paramsList) {
                                
                                if ($canProcessUpdate) {
                                    return kartik\helpers\Html::a('<i class="fa-check s16"></i>', '#', [
                                                'data-pjax' => '0',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'data-original-title' => 'param',
                                                'onclick' => 'return btnEditProcessActionClick(this)',
                                                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action/' . $paramsList[$model->process->ProccessTypeIDRef], 'pid' => $processModel->ProcessID, 'aid' => $model->ActionID]),
                                    ]);
                                }
                            },
                        ],
                    ]
                ]
            ]);
            ?>

        </div>
    </div>
    <div class='col-md-4'>
        <div class='r ow action-order mr0' style='border-radius:4px; ;border:1px solid #C7C7C7;'>

            <div class='row' >
                <div class="col-md-12" >
                    <span ><i class="en-flow-branch s20" style="transform: rotate(90deg);"></i>&nbsp;
                        <b>Action Order List</b>
                    </span>
                </div>
            </div>
            <div class='row' >
                <div class="col-md-12" >
                    <?php
                    echo \kartik\grid\GridView::widget([
                        'id' => 'action-order-grid',
                        'dataProvider' => $actionOrderDataProvider,
                        'filterModel' => $actionOrderSearchModel,
                        'layout' => "{items}\n{pager}\n{summary}",
                        'pjax' => true,
                        'floatHeader' => true,
                        'floatOverflowContainer' => true,
                        'pjaxSettings' => [
                            'options' => [
                                'id' => 'action-order-grid-pjax',
                            ],
                        ],
                        'columns' => [
                            [
                                'attribute' => 'ActionIDRef',
                                'label' => 'Action ID',
                            ],
                            [
                                'attribute' => 'NextActionIDRef',
                                'label' => 'Next Action ID',
                            ],
                            [
                                'class' => kartik\grid\ActionColumn::className(),
                                'template' => '{edit}&nbsp;{measure}&nbsp;{param}&nbsp;{attachment}',
                                'options' => [
                                    'width' => '40px'
                                ],
                                'buttons' => [
                                    'edit' => function($key, $model, $index)use($processModel, $canProcessUpdate) {
                                        if ($canProcessUpdate) {
                                            return kartik\helpers\Html::a('<i class="fa-pencil s16"></i>', '#', [
                                                        'data-pjax' => '0',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'top',
                                                        'data-original-title' => 'Edit',
                                                        'onclick' => 'return btnEditActionOrderClick(this)',
                                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action-order/update', 'pid'=>$processModel->ProcessID,'aoid' => $model->ID]),
                                            ]);
                                        }
                                    },
                                    'measure' => function($key, $model, $index)use($processModel, $canProcessUpdate) {
                                        if ($canProcessUpdate) {
                                            return kartik\helpers\Html::a('<i class="br-list s16"></i>', '#', [
                                                        'data-pjax' => '0',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'top',
                                                        'data-original-title' => 'Edit Measure',
                                                        'onclick' => 'return btnShowActionOrderMeasureClick(this)',
                                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action-order/measure-list', 'pid'=>$processModel->ProcessID,'aoid' => $model->ID]),
                                            ]);
                                        }
                                    },        
                                    'param' => function($key, $model, $index)use($processModel, $canProcessUpdate,$paramsList) {
                                        if ($canProcessUpdate) {
                                            return kartik\helpers\Html::a('<i class="fa-check s16"></i>', '#', [
                                                'data-pjax' => '0',
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'top',
                                                'data-original-title' => 'param',
                                                'onclick' => 'return btnEditActionOrderParamClick(this)',
                                                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action-order/' . $paramsList[$model->action->process->ProccessTypeIDRef], 'pid' => $processModel->ProcessID, 'aoid' => $model->ID]),
                                            ]);
                                        }
                                    },        
                                    'attachment' => function($key, $model, $index)use($processModel, $canProcessUpdate) {
                                        if ($canProcessUpdate && $processModel->ProcessID == 69) {
                                            return kartik\helpers\Html::a('<i class="im-attachment s16"></i>', '#', [
                                                        'data-pjax' => '0',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'top',
                                                        'data-original-title' => 'Show Document Required Type',
                                                        'onclick' => 'return btnShowDocumentRequiredTypeClick(this)',
                                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action-order/document-required-type', 'pid'=>$processModel->ProcessID, 'aoid' => $model->ID]),
                                            ]);
                                        }
                                    },        
                                ],
                            ]
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>

    </div>
    <?php Pjax::end() ?>
</div>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'process-action-modal',
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'header' => '<h5 class="m0 p0" style="font-weight:bold" ><i class="im-arrow-right2 s18"></i>&nbsp;Edit Process Action</h5>',
])
?>
<?php
Pjax::begin([
    'id' => 'process-action-pjax'
])
?>
<?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'action-order-modal',
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'header' => '<h5 class="m0 p0" style="font-weight:bold" ><i class="en-flow-branch s20" style="transform: rotate(90deg);"></i>&nbsp;Edit Action Order</h5>',
])
?>
<?php
Pjax::begin([
    'id' => 'action-order-pjax'
])
?>
<?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'action-order-measure-modal',
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'header' => '<h5 class="m0 p0" style="font-weight:bold" ><i class="en-flow-branch s20" style="transform: rotate(90deg);"></i>&nbsp;Action Order Check Box List</h5>',
])
?>
<?php
Pjax::begin([
    'id' => 'action-order-measure-pjax'
])
?>
<?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'action-order-measure-form-modal',
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'header' => '<h5 class="m0 p0" style="font-weight:bold" ><i class="en-flow-branch s20" style="transform: rotate(90deg);"></i>&nbsp;Edit Check Box List</h5>',
])
?>
<?php
Pjax::begin([
    'id' => 'action-order-measure-form-pjax'
])
?>
<?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'action-order-param-modal',
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'header' => '<h5 class="m0 p0" style="font-weight:bold" ><i class="en-flow-branch s20" style="transform: rotate(90deg);"></i>&nbsp;Edit Action Order Param</h5>',
])
?>
<?php
Pjax::begin([
    'id' => 'action-order-param-pjax'
])
?>
<?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>

<?php
yii\bootstrap\Modal::begin([
    'id' => 'action-order-document-required-type-modal',
    'size' => yii\bootstrap\Modal::SIZE_LARGE,
    'header' => '<h5 class="m0 p0" style="font-weight:bold" >Document Required Type</h5>',
])
?>
<?php
Pjax::begin([
    'id' => 'action-order-document-required-type-pjax'
])
?>
<?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>


<?php
        yii\bootstrap\Modal::begin([
            'id' => 'document-required-type-modal',
            'size' => yii\bootstrap\Modal::SIZE_LARGE,
            'header' => '<h5 class="m0 p0" style="font-weight:bold" >Document Required Type</h5>',
        ])
        ?>
        <?php
        Pjax::begin([
            'id' => 'document-required-type-form-pjax'
        ])
        ?>
        <?php Pjax::end() ?>
        <?php yii\bootstrap\Modal::end() ?>

<script>
    $(document).ready(function () {
        var showDocumentRequiredTypeUrl;
        $("#process-action-pjax").on("pjax:send", function (dt, res, ev) {
            ajaxProgressLoader('show', '#process-action-modal .modal-body');
        });
        $("#process-action-pjax").on("pjax:complete", function (dt) {

            var res = dt.currentTarget.innerHTML;
            var p = res.split('*');
            if (p[0] === 'true') {
                showMsgBoxInBottomRight(p[1], 'success');
                $('#process-action-modal').modal('hide');
                $.pjax.reload({
                    container: "#process-action-grid-pjax",
                    timeout: false,
                    push: false,
                    replace: false,
                });
            } else if (p[0] === 'false') {
                showMsgBoxInBottomRight(p[1], 'error');
            }
            ajaxProgressLoader('hide', '#process-action-modal .modal-body');
        });
        $("#process-action-main-pjax").on("pjax:send", function (dt, res, ev) {
            ajaxProgressLoader('show', '#process-action-main-pjax');
        });
        $("#process-action-main-pjax").on("pjax:complete", function (dt, res, ev) {
            ajaxProgressLoader('hide', '#process-action-main-pjax');
        });
    });
    function callbackActionOrder(data) {
        if (data.result == 'true') {
            $('#action-order-modal').modal('hide');
            swal({
                title: 'successfully',
                type: 'success',
                text: data.message,
                timer: 1000,
                showConfirmButton: false,
            });
        } else {
            swal('save error', data.message, 'error');
        }

    }
    function btnEditProcessActionClick(obj) {
        obj = $(obj);
        var url = obj.attr('data-url');
        $.pjax.reload({
            container: "#process-action-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#process-action-modal').modal('show');
        return false;
    }
    function btnEditActionOrderClick(obj) {
        obj = $(obj);
        var url = obj.attr('data-url');
        $.pjax.reload({
            container: "#action-order-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#action-order-modal').modal('show');
        return false;
    }
    function btnEditActionOrderParamClick(obj) {
        obj = $(obj);
        var url = obj.attr('data-url');
        $.pjax.reload({
            container: "#action-order-param-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#action-order-param-modal').modal('show');
        return false;
    }
    function loadSubprocessActions(type) {

        var subProcessId = $('#sub-process').val();
        if (type == 'main') {
            subProcessId = $('#btn-return-back-to-main-process').attr('data-pid');
            $('#sub-process').val('');
            $('#sub-process').trigger('change');
        }
        var url = '<?= Yii::$app->urlManager->createAbsoluteUrl(['workflow/process/subprocess-action-list', 'pid' => '']) ?>' + subProcessId;
        console.log(subProcessId);
        $.pjax.reload({
            container: "#process-action-main-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#btn-return-back-to-main-process').css('display', '');
        return false;
    }
    function btnShowActionOrderMeasureClick(obj) {
        obj = $(obj);
        var url = obj.attr('data-url');
        $.pjax.reload({
            container: "#action-order-measure-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#action-order-measure-modal').modal('show');
        return false;
    }

    function btnShowDocumentRequiredTypeClick(obj) {
        obj = $(obj);
        var url = obj.attr('data-url');
        showDocumentRequiredTypeUrl = url;
        $.pjax.reload({
            container: "#action-order-document-required-type-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#action-order-document-required-type-modal').modal('show');
        return false;
    }
    function btnEditDocumentRequiredTypeClick(obj) {
        obj = $(obj);
        var url = obj.attr('data-url');
        $.pjax.reload({
            container: "#document-required-type-form-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#document-required-type-modal').modal('show');
        return false;
    }
    function showDocumentRequiredTypeModal(obj) {
        ajaxProgressLoader('show', '.modal-content');
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function (data) {
            $('#document-required-type-form-pjax').html(data);
            $('#document-required-type-modal').modal('show');
            ajaxProgressLoader('hide', '.modal-content');
        }).error(function () {
            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
            return false;
        });
    }
    function callbackDocumentRequiredTypePjax(data) {
        if (data.result == true) {
            $('#document-required-type-modal').modal('hide');
            swal({
                title: 'successfully',
                type: 'success',
                text: data.message,
                timer: 1000,
                showConfirmButton: false,
            });

            $.pjax.reload({
                    container: "#action-order-document-required-type-pjax",
                    url: showDocumentRequiredTypeUrl,
                    timeout: false,
                    push: false,
                    replace: false,
                }); 
        } else {
            swal('save error', data.message, 'error');
        }

    }
</script>