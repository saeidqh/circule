<?php
use kartik\detail\DetailView;
use app\components\Utility;
?>
<div class="row text-right" style="overflow: scroll;overflow-x: hidden; height: 100px;">
    <div class="col-lg-12 col-md-12">
    <?= DetailView::widget([
        'model' => $peModel->capital,
        'attributes' => [
            [
                'attribute' => 'capitalBalanceId'
            ],
            [
                'attribute' => 'balanceDescription',
                'format' => 'raw',
                'value' => \yii\helpers\Html::encode($peModel->capital->balanceDescription),
            ]
        ]
    ]) ?>
    </div>
</div>

