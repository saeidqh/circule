<?php

use yii\widgets\Pjax;
?>

<div class="row mb5">
    <div class="col-md-12 text-right">
        <?php
        echo \yii\helpers\Html::a('<i class="fa-pencil"></i> ' . Yii::t('tender', 'New Action Order Measure'), '#', [
            'data-url' => Yii::$app->urlManager->createAbsoluteUrl(["workflow/action-order-measure/create", 'aoid' => $aoid]),
            'onclick' => 'showActionOrderMeasureModal(this)',
            'class' => 'btn btn-sm btn-success'
        ]);
        ?>
    </div>
</div>

<div class="row process-actions grid-font " style="overflow-y: hidden">
    <?php
    echo \kartik\grid\GridView::widget([
        'id' => 'action-order-measure-grid',
        'dataProvider' => $actionOrderMeasureProvider,
//        'filterModel' => $actionOrderMeasureModelSearch,
        'columns' => [
            [
                'attribute' => 'ActionOrderMeasureID',
                'label' => 'Action Order Measure ID',
                'options' => [
                    'width' => '180px'
                ],
            ],
            [
                'attribute' => 'measureTitle',
                'value' => function ($model) {
                    return $model->measure->MeasureTitle;
                },
//                'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
//                'filterWidgetOptions' => [
//                    'data' => $measureArray,
//                    'options' => ['placeholder' => 'انتخاب شرکت'],
//                    'pluginOptions' => [
//                        'allowClear' => true,
//                        'width' => '200px'
//                    ]
//                ],
            ],
            [
                'class' => kartik\grid\ActionColumn::className(),
                'template' => '{delete}',
                'options' => [
                    'width' => '40px'
                ],
                'buttons' => [
                    'delete' => function ($key, $model, $index)use ($pid, $aoid) {
                        return kartik\helpers\Html::a('<i class="fa-trash s12"></i>', '#', [
                            "id" => "delete-action-order-measure-" . $model->ActionOrderMeasureID,
                            'data-pjax' => '0',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top',
                            'data-original-title' => 'Edit',
                            'onclick' => 'confirmBox("Are you sure to delete this item?!", "Delete!", "btnDeleteClick(' . $model->ActionOrderMeasureID . ')");',
                            'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/action-order-measure/delete', 'aom' => $model->ActionOrderMeasureID]),
                        ]);
                    },
                ],
            ]
        ]
    ]);
    ?>
</div>

<script>
    var reloadUrl = '<?= Yii::$app->urlManager->createAbsoluteUrl(['workflow/action-order/measure-list', 'pid' => $pid, 'aoid' => $aoid]) ?>';
    function showActionOrderMeasureModal(obj) {
        ajaxProgressLoader('show', '.modal-content');
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function (data) {
            $('#action-order-measure-form-pjax').html(data);
            $('#action-order-measure-form-modal').modal('show');
            ajaxProgressLoader('hide', '.modal-content');
        }).error(function () {
            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
            return false;
        });
    }

    function callbackActionOrderMeasure(data) {
        if (data.result == 'true') {
            $('#action-order-measure-form-modal').modal('hide');
            swal({
                title: 'successfully',
                type: 'success',
                text: data.message,
                timer: 1000,
                showConfirmButton: false,
            });
            $.pjax.reload({
                container: '#action-order-measure-pjax',
                url: reloadUrl,
                timeout: false,
                push: false,
                replace: false,
            });
        } else {
            swal({
                title: 'Error',
                type: 'error',
                text: data.message,
                timer: 1000,
                showConfirmButton: false
            });
        }

    }

    function btnDeleteClick(id) {
        ajaxProgressLoader('show', '.popup-box');
        var url = $("#delete-action-order-measure-" + id).attr('data-url');
        $.ajax({
            url: url,
        }).done(function (data) {
            if (data.result == true) {
                swal({
                    title: 'successfully',
                    type: 'success',
                    text: data.message,
                    timer: 1000,
                    showConfirmButton: false,
                });
                $.pjax.reload({
                    container: '#action-order-measure-pjax',
                    url: reloadUrl,
                    timeout: false,
                    push: false,
                    replace: false,
                });
            } else {
                swal({
                    title: 'Error',
                    type: 'error',
                    text: data.message,
                    timer: 1000,
                    showConfirmButton: false
                });
            }
        }).error(function () {
            showMsgBoxInBottomRight('Error on connecting to server. <br />Plase login again and try later!', 'error');

            ajaxProgressLoader('hide', '.popup-box');
            return false;
        });
        return false;
    }
</script>