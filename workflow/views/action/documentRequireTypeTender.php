<?php

use app\modules\workflow\assets\WorkflowListAsset;
use app\components\PageHeader;
use yii\widgets\Pjax;
use kartik\helpers\Html;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);
$this->title = "Process Action Required Document Type";
//$processModel->ProcessID
$canProcessUpdate = \app\access\AccessControl::can('workflow/process/update', ['pid' => 1]);

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

<div class="row process-actions grid-font " style="overflow-y: hidden">
    <?php
    Pjax::begin([
        'id' => 'action-order-document-required-type-pjax'
    ])
    ?>

    <?php
    echo \kartik\grid\GridView::widget([
        'id' => 'action-order-document-required-type-grid',
        'dataProvider' => $tenderDocumentRequireDocumentTypeProvider,
        'filterModel' => $tenderDocumentRequireDocumentTypeSearchModel,
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
            'tenderType',
            'noRequirementType',
            [
                'attribute' => 'TenderDocumentTypeIDRef',
                'options' => [
                    'width' => '40px'
                ],
            ],
            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{edit}',
                'options' => [
                    'width' => '40px'
                ],
                'buttons' => [
                    'edit' => function($key, $model, $index)use($canProcessUpdate) {
                        if ($canProcessUpdate) {
                            return kartik\helpers\Html::a('<i class="fa-pencil s16"></i>', '#', [
                                        'data-pjax' => '0',
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'data-original-title' => 'Edit',
                                        'onclick' => 'return btnEditProcessActionClick(this)',
                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action/update', 'pid' => 1, 'aid' => 2]),
                            ]);
                        }
                    },
                        ],
                    ]
                ]
            ]);
            ?>
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



        <script>
            $(document).ready(function () {
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
</script>