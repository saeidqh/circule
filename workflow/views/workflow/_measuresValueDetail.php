<?php
use app\components\PageHeader;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\bootstrap\Modal;

?>

<div class="bs-example bs-example-tabs">

    <?php
    Pjax::begin([
        'id' => 'measures-value-detail-pjax'
    ]);

    ?>

    <?= GridView::widget([
        'id' => 'tbl-tender-grid3',
//        'filterModel' => $actionOrderMeasureValueSearch,
        'dataProvider' => $actionOrderMeasureValueProvider,
        'columns' => [
            [
                'attribute' => 'measureTitle',
                'value' => function($model){
                    return $model->actionOrderMeasure->measure->MeasureTitle;
                },
//                'options' => ['width' => '50px'],
            ],
            [
                'attribute' => 'value',
                'value' => function($model){
                    return $model->getValueType($model->value);
                },
            ],
            [
                'attribute' => 'description',
//                'options' => ['width' => '120px'],
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
