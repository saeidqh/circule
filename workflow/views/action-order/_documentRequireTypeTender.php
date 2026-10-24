<?php

use yii\widgets\Pjax;

$tenderModel = new \app\modules\tender\models\Tender();
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

<div class="row mb5">
    <div class="col-md-12 text-right">
        <?php
        echo \yii\helpers\Html::a('<i class="fa-pencil"></i> ' . Yii::t('tender', 'New Document Required Type'), '#', [
            'data-url' => Yii::$app->urlManager->createAbsoluteUrl(["tender/document-require-type/create", 'aoid' => $aoid]),
            'onclick' => 'showDocumentRequiredTypeModal(this)',
            'class' => 'btn btn-sm btn-success'
        ]);
        ?>
    </div>
</div>

<div class="row process-actions grid-font " style="overflow-y: hidden">
    <?php
    Pjax::begin([
        'id' => 'document-required-type-pjax'
    ])
    ?>

    <?php
    echo \kartik\grid\GridView::widget([
        'id' => 'document-required-type-grid',
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
            [
                'attribute' => 'tenderType',
                'value' => function($model) use($tenderModel) {
                    return $tenderModel->getTenderType($model->tenderType);
                }
            ],
            [
                'attribute' => 'noRequirementType',
                'value' => function($model) use($tenderModel) {
                    return $tenderModel->getNoRequirementType($model->noRequirementType);
                },
            ],
            [
                'attribute' => 'TenderDocumentTypeIDRef',
                'value' => function($model) {
                    return $model->documentType->TenderDocumentTypeTitle;
                },
            ],
            [
                'attribute' => 'RequireType',
                'value' => function($model) {
                    return $model->getRequireType($model->RequireType);
                }
            ],
            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{edit}',
                'options' => [
                    'width' => '40px'
                ],
                'buttons' => [
                    'edit' => function($key, $model, $index)use($pid, $aoid) {
                        return kartik\helpers\Html::a('<i class="fa-pencil s16"></i>', '#', [
                                    'data-pjax' => '0',
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'data-original-title' => 'Edit',
                                    'onclick' => 'return btnEditDocumentRequiredTypeClick(this)',
                                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['tender/document-require-type/update', 'drt' => $model->ID]),
                        ]);
                    },
                        ],
                    ]
                ]
            ]);
            ?>
            <?php Pjax::end() ?>
</div>

<script>
    $(document).ready(function () {
        /*$("#process-action-pjax").on("pjax:send", function (dt, res, ev) {
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
         });*/
    });

</script>