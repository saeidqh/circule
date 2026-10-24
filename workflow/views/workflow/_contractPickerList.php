<?php

use yii\helpers\Html;

?>
<style>
    .fix-grid-header .kv-thead-float{
        top:70px !important;
        left:5px !important;
    }
</style>
<div class="row" style="height:400px; overflow-y:scroll; " >
    
    <div class="fix-grid-header font-grid" >
        <?php
        echo \kartik\grid\GridView::widget([
            'id' => 'contract-list-grid',
            'dataProvider' => $modelDataProvider,
            'filterModel' => $modelSearch,
            'floatHeader' => true,
            'layout'=>"{items}\n{pager}\n{summary}",
            'pjaxSettings' => [
                'options' => [
                    'id' => 'contract-list-grid-pjax',
                ],
            ],
            'columns' => [
                [
                    'class' => '\kartik\grid\RadioColumn',
                    'showClear' => true,
                   
                ],
                [
                    'attribute' => 'ContractNo',
                    'format'=>'raw',
                    'value'=>function($model){
                        $contractSubject  = Html::encode($model->ContractSubject);
                        $contractSubject  = str_replace("'", '-', $contractSubject);
                        $contractSubject  = str_replace('"', '-', $contractSubject);
                        $contractSubject  = str_replace('#', '-', $contractSubject);
                        $contractSubject  = str_replace('\n', '-', $contractSubject);
                        $contractSubject  = str_replace('\r', '-', $contractSubject);
                        return '<span id="contract-item-'.$model->ContractID.'" data-attrib="'.$model->ContractID.'###'.$model->ContractNo.'###'.$contractSubject.'">'.$model->ContractNo.'</span>';
                    },
                    'label' => 'Contract No',
                ],
                [
                    'attribute' => 'ContractSubject',
                    'label' => 'Contract Subject',
                ],
                [
                    'attribute' => 'subContractorTitle',
                    'value' => 'cf_subcontractorTitle',
                    'label' => 'Sub Contractor',

                ],
            ]
        ]);
        ?>
    </div>
</div>
<div class='row'>
    <div class='col-md-12' >
        <hr class='p0 m0 mb10' />
        <?= Html::button('Cancel', [
            'class'=>'btn btn-danger btn-sm pull-right ml5',
            'style'=>'margin-right:5px;',
            'data-dismiss'=>'modal',

        ]) ?>
        <?= Html::button('Select Contract', [
            'id'=>'select-contract',
            'class'=>'btn btn-success btn-sm pull-right',
            'onclick'=>'return addSelectedContractToValueField(this)',
            'data-key'=>'null',
        ]) ?>
    </div>

</div>
<?php
    // کد نمایش پیغام های کاربر
    if(Yii::$app->request->isAjax)
        echo app\components\Utility::showUserMessages(Yii::$app->session->getAllFlashes());
?>

<script>
    $(document).ready(function(){
        var $grid = $('#contract-list-grid');
        $grid.on('grid.radiochecked', function(ev, key, val) {
            var attrib =$('#contract-item-'+key).attr('data-attrib');
            $('#select-contract').attr('data-attrib',attrib);
        });
    });
    
</script>

