<?php
use app\modules\workflow\assets\WorkflowListAsset;
use app\components\PageHeader;
use yii\widgets\Pjax;
use kartik\helpers\Html;

$this->title = "WORKFLOW :: Process Detail";


WorkflowListAsset::register($this);
?>
<?= PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);?>

<style>
    .process-detail-box .nav-tabs>li a {
        background: #FFA500;
        font-weight:bold;
        color:white;
    }
    .nav-tabs>li.active>a,
    .nav-tabs>li.active>a:focus, 
    .nav-tabs>li.active>a:hover {
        color: #4E4E4E;
        background: #FFFCFC;
    }
    .grid-font .summary, 
    .grid-font td, 
    .grid-font th {
        font-size: 11px;
    }
    .kv-grid-wrapper{
        height:380px;
    }
    #process-invoice-grid table thead,
    #process-invoice-grid .summary {
        background-color: #B2B977;
    }
    #process-invoice-grid .pagination{
        margin: 0;
    }
    #process-invoice-grid .floatThead-wrapper{
        border-bottom: solid 1px #B2B977;
    }
    
    .process-summary{
        border:solid 1px #C9CACC; 
        background-color: #FFF9DA;
        border-radius: 4px;
        margin-top:-40px;
        height:34px;
        /*width: 734px;*/ 
        overflow: hidden;
    }
</style>

<div class="row process-detail-box">
    <ul id=myTab class="nav nav-tabs tabdrop">
        <li class="active" ><a href="#graph-tab" data-toggle=tab>Graph</a></li>
        <?php if($processModel->ProccessTypeIDRef == 1){ ?>
            <li><a class='animated bounceInRight' href="#pro-inv-tab" data-toggle=tab>Process Invoices</a></li>
        <?php } ?>
    </ul>
    <div id=myTabContent class=tab-content style="border-bottom-right-radius: 5px;border-bottom-left-radius: 5px">
        
        <div class='col-md-9 pull-right process-summary'>
            <h4 class='p5 m0 mb20' >
                <b>ID:</b> 
                <span class='badge badge-purple'><?= Html::encode($processModel->ProcessID) ?> </span>
                
                <b>Title:</b>
                <span class='badge badge-primary'><?= Html::encode($processModel->ProcessTitle) ?> </span>
                
                <b>Type:</b>
                <span class='badge badge-teal'><?= Html::encode($processModel->type->proccessTypeTitle) ?> </span>
                
                <b>OBS:</b>
                <span class='badge badge-pink'><?= Html::encode($processModel->OBSCategory3CompanyIDRef) ?> </span>
            </h4>
        </div>
        <div class="tab-pane fade active in" id=graph-tab>
          <?php 
              echo $this->render('../ajax/_graph', array(
                  'pid' => $pid,
              ));
          ?>
        </div>
        <?php if($processModel->ProccessTypeIDRef == 1){ ?>
            <div class="tab-pane fade" id="pro-inv-tab" >
                <div class='row pr5 mb5' style='margin-top:-10px'>
                    <!--process 34 is removed-->
                    <?php if($processModel->ProcessID != 34){ ?>
                         <?= Html::a('<i class="im-plus"></i>&nbsp;&nbsp;Add Contract','#',[
                        'class'=>'btn btn-sm btn-primary pull-right',
                        'onclick'=>'return btnAddInvoiceClick(this)',
                        'data-url'=> Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/create-process-invoice','pid'=>$pid]),
                    ]) ?>
                    <?php } ?> 
                   
                    <?= Html::a('<i class="im-close"></i>&nbsp;&nbsp;Delete','#',[
                        'class'=>'btn btn-sm btn-warning pull-right mr5',
                        'onclick'=>'return btnDeleteInvoiceClick(this);',
                        'data-url'=> Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/delete-process-invoice', 'pid' => $processModel->ProcessID]),
                    ]) ?>

                </div>
                <div class='row grid-font'  id='process-invoice-box' >
                    <?php
                        echo \kartik\grid\GridView::widget([
                            'id' => 'process-invoice-grid',
                            'dataProvider' => $contractInvoiceTypeDataProvider,
                            'filterModel' => $contractInvoiceTypeProcessModelSearch,
                            'layout'=>"{items}\n{pager}\n{summary}",
                            'pjax' => true,
                            'floatHeader' => true,
                            'floatOverflowContainer' => true,
                            'pjaxSettings' => [
                                'options' => [
                                    'id' => 'process-invoice-grid-pjax',
                                ],
                            ],
                            'columns' => [
                                [
                                    'class' => '\kartik\grid\CheckboxColumn',
                                    'options' => [
                                        'width' => '10px'
                                    ],
                                ],
                                [
                                    'attribute' => 'contractNo',
                                    'value' => 'contract.ContractNo',
                                ],
                                [
                                    'attribute' => 'contractSubject',
                                    'value' => 'contract.ContractSubject',
                                ],
                                [
                                    'attribute' => 'invoiceType',
                                    'value' => 'invoiceTypes.InvoiceType',
                                ],

                            ],
                        ]);
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
        
</div>


<?php yii\bootstrap\Modal::begin([
    'id'=>'process-invoice-modal',
    'size' => yii\bootstrap\Modal::SIZE_DEFAULT,
    'header'=>'<h5 class="m0 p0" style="font-weight:bold" >Select Contract Invoice Process</h5>',
    'options'=>[
        'tabindex'=>false,
    ]
]) ?>
    <?php Pjax::begin([
        'id'=>'process-invoice-pjax'
    ]) ?>
    <?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>

<?php yii\bootstrap\Modal::begin([
    'id'=>'contract-picker-modal',
    'size' => yii\bootstrap\Modal::SIZE_DEFAULT,
    'header'=>'<h5 class="m0 p0" style="font-weight:bold" >Contract List</h5>',
]) ?>
    <?php Pjax::begin([
        'id'=>'contract-picker-pjax'
    ]) ?>
    <?php Pjax::end() ?>
<?php yii\bootstrap\Modal::end() ?>



<script>
    $(document).ready(function(){
        
        $('#process-invoice-pjax').on("pjax:send", function(dt,res,ev) {
            ajaxProgressLoader('show', '#process-invoice-modal .modal-body');
        });
        $('#process-invoice-pjax').on("pjax:complete", function(dt,res,ev) {
            
            var res = dt.currentTarget.innerHTML;
            var p =res.split('*');
            if(p[0] === 'true'){
                showMsgBoxInBottomRight(p[1],'success');
                $('#process-invoice-modal').modal('hide');
                $.pjax.reload({
                    container: "#process-invoice-grid-pjax",
                    timeout: false,
                    push: false,
                    replace: false,
                });
                
                
            }else if(p[0] === 'false') {
                showMsgBoxInBottomRight(p[1],'error');
            }
            ajaxProgressLoader('hide', '#process-invoice-modal .modal-body');
        });
        
        $("#contract-picker-pjax").on("pjax:send", function(dt,res,ev) {
            ajaxProgressLoader('show', '#contract-picker-modal .modal-body');
        });
        $("#contract-picker-pjax").on("pjax:complete", function(dt) {
            ajaxProgressLoader('hide', '#contract-picker-modal .modal-body');
        });
    });
    
    function btnAddInvoiceClick(obj){
        obj = $(obj);
        var url = obj.attr('data-url');
        $.pjax.reload({
            container: "#process-invoice-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        $('#process-invoice-modal').modal('show');
        return false;
    }
    function btnDeleteInvoiceClick(obj){
        var items = $('#process-invoice-grid').yiiGridView('getSelectedRows');
        if(!(items.length>0)){
            showMsgBoxInBottomRight('Please select the Invoice Type(s) and try again.','warning');
                return;
        }
        obj = $(obj);
        var url = obj.attr('data-url');
        var strKeys = '';
        for(var i=0; i<items.length; i++){
            strKeys+=((strKeys!='')?',':'')+items[i];
        }
        $.pjax.reload({
            container: "#process-invoice-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
            data:{
                keys:strKeys,
            }
        });
        return false;
    }
    
    function btnSelectContractClick(obj){
        var url =  '<?= Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/invoice-process-contract-picker','type'=>'ContractID']) ?>';
        $.pjax.reload({
            container: "#contract-picker-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        
        $('#contract-picker-modal').modal('show');
        return false;
    }
    function  addSelectedContractToValueField(obj){
        obj = $(obj);
        var attribs = obj.attr('data-attrib')+'';
        if(attribs.indexOf('###') == -1){
            showMsgBoxInBottomRight('Please select a item and try again.','warning');
            return;
        }
        attribs = attribs.split('###');
        
        $('#contractinvoicetypeprocess-contractidref').val(attribs[0]);
        $('#contract-no').val(attribs[1]);
        $('#contract-subject').val(attribs[2]);
        $('#contractinvoicetypeprocess-contractidref').trigger('change');
        $('#contract-picker-modal').modal('hide');
        return false;
    }
</script>