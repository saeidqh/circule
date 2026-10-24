<?php

use yii\helpers\Html; 

\yii\widgets\Pjax::begin();
?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel panel-teal">
            <div class="panel-heading" style="padding-top: 5px">
                <h4 class="panel-title"> Debit Memo Info</h4>
                <?php
                    echo yii\bootstrap\Html::a('Edit',Yii::$app->urlManager->createAbsoluteUrl(["debitmemo/debitmemo/update", 'peid' => $processEntityModel->ProcessEntityID,]), [
                        'class' => 'btn btn-sm btn-primary pull-right',
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?= \kartik\detail\DetailView::widget([
                    'model'=>$processEntityModel,
                    'attributes'=>[
                        [
                            'label'=>'Contract No.',
                            'value'=>Html::encode($processEntityModel->debitmemo->contract->ContractNo),
                        ],
                        [
                            'label'=>'Contract Subject',
                            'value'=>Html::encode($processEntityModel->debitmemo->contract->ContractSubject),
                        ],
                        [
                            'label'=>'Debit Memo ID',
                            'value'=>Html::encode($processEntityModel->debitmemo->id),
                        ],
                        [
                            'label'=>'Debit Memo No.',
                            'value'=>Html::encode($processEntityModel->debitmemo->debitmemoNo),
                        ],
                        [
                            'label'=>'Description',
                            'value'=>Html::encode($processEntityModel->debitmemo->description),
                        ],
                        [
                            'label' => 'createdAt',
                            'value' => Html::encode($processEntityModel->debitmemo->debitmemoDateHijri)
                        ]
                    ],
                ]);
                ?>
            </div>
        </div>
    
    </div>
</div>
<?php  \yii\widgets\Pjax::end(); ?>
