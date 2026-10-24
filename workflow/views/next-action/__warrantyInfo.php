<?php
echo\kartik\detail\DetailView::widget([
    'model'=>$peModel,
    'attributes'=>[
        [
            'value'=>Html::encode($peModel->warranty->contract->ContractNo),
            'label'=>'ContractNo',
        ],
        [
            'value'=>Html::encode($peModel->warranty->subject->warrantySubjectTitle),
            'label'=>'Warranty Subject',
        ],
        [
            'value'=>Html::encode($peModel->warranty->type->warrantyTypeTitle),
            'label'=>'Warranty Type',
        ],
        
    ]
]);
 