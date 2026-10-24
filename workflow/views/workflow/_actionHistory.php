<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<?php
echo GridView::widget([
    'id' => 'stamp-summary-grid',
    'dataProvider' => $actionEntityProvider,
    'pjax' => true,
    'bordered' => true,
    'striped' => true,
    'hover' => true,
    'tableOptions' => ['class' => 'table table-bordered'],
    'columns' => [
        [
            'attribute' => 'ActionTitle1',
            'label' => 'مرحله ',
            'width' => '150px',
        ],
        [
            'attribute' => 'UserName',
            'label' => ' نام و نام خانوادگی',
            'width' => '150px',
        ],
        [
            'attribute' => 'ActionDateHijri',
            'label' => 'تاریخ ارجاع',
            'width' => '120px',
        ],
        [
            'attribute' => 'ActionAutoMessage',
            'label' => 'پیام پیش فرض',
            'width' => '200px',
        ],
        [
            'attribute' => 'ActionMessage',
            'label' => 'پیام',
            'width' => '200px',
        ],
        [
            'attribute' => 'Duration',
            'label' => 'زمان',
            'width' => '100px',
        ],
        // ستون دسترسی‌ها
    
    ],
]);
?>