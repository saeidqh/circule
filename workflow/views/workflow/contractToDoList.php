<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\workflow\assets\ContractAsset;
use yii\bootstrap\Modal;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);
ContractAsset::register($this);
Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function() {
    Yii::$app->view->registerAssetBundle(app\modules\workflowContract\assets\GridViewAsset::className());
});
$this->params['breadcrumbs'] = [
    'lable' => 'WORKFLOW'
];
$workflowContractDeleteUrl = 'workflowContract/workflowcontract/delete';
$securityDelete = \app\access\AccessControl::can($workflowContractDeleteUrl);
?>

<?=
PageHeader::widget([
    'title' => \Yii::t("contract", 'WORKFLOW :: Contract todo list'),
    'icon' => 'fa-tasks',
]);
?>
<div class="row">
    <?php GridViewBox::begin() ?>

    <div class="row">
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            echo Html::a("<i class='fa-plus'></i> &nbsp".\Yii::t("contract", 'New Contract'), Yii::$app->urlManager->createAbsoluteUrl('workflowContract/workflowcontract/new-contract'), [
                'class' => 'btn btn-sm pull-left btn-mini btn-999999 mr5',
                'id' => 'btn-new-cashrequest',
                'data-pjax' => '0',
            ]);

            echo Html::a("<i class='fa-plus'></i> &nbsp;".\Yii::t("contract", 'New Rider'), "#modalContractSelector", [
                'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                'id' => 'btn-new-cashrequest',
                'data-toggle' => 'modal',
                'data-pjax' => '0',
            ]);
            ?>

        </div>


        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $model->WORKFLOW_CONTRACT_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/contract", 'type' => $btns[$i]['value']]) ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>
                <?php
            }
            ?>
        </div>

    </div>

    <div class="row">
        <?php Pjax::begin(['id' => 'contract-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-contract',
            'dataProvider' => $contractDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model->contract->WorkflowContractID;
                $class = $index % 2 ? 'odd' : 'even';
                return ['index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id];
            },
            'pager' =>[
                'firstPageLabel' => 'First',
                'lastPageLabel' => 'Last',
            ],
            'filterModel' => $model,
            'floatHeader' => true,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => [
                        'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                    ],
                    'headerOptions' => [
                        'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                        'onchange'=>'return showAllNextActionButton(this);',
                    ],
                    'contentOptions' => [
                        'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                        'onchange'=>'return showAllNextActionButton(this);',
                    ],
                    'filterOptions' => [
                        'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                    ],
                ],
                [
                    'attribute' => 'WorkflowContractID',
                    'value' => 'contract.WorkflowContractID',
                    // 'label' => 'ID',
                    'options' => [
                        'width' => '50px'
                    ]
                ],
                [
                    'attribute' => 'ContractIDRef',
                    'value' => 'contract.ContractIDRef',
                    // 'label' => 'Contract ID',
                    'options' => [
                        'width' => '50px'
                    ]
                ],
                [
                    'attribute' => 'personTitle',
                    'value' => 'contract.signer.PersonTitle',
                    // 'label' => 'Client',
                ],
                [
                    'attribute' => 'ContractNo',
                    'value' => 'contract.ContractNo',
                    // 'label' => 'ContractNo',
                ],
                [
                    'attribute' => 'ContractSubject',
                    'value' => 'contract.ContractSubject',
                    // 'label' => 'Contract Subject',
                ],
                [
                    'attribute' => 'SubContractorPersonTitle',
                    // 'label' => 'Sub Contractor',
                    'value' => function($model) {
                        /*if (app\components\Utility::isJson($model->contract->SubContractorPersonTitle)) {
                            $val = json_decode($model->contract->SubContractorPersonTitle);
                            return $val->SubContractorPersonTitle;
                        } else {
                            return $model->contract->SubContractorPersonTitle;
                        }*/
                        return $model->contract->subcontractorsList;
                    }
                ],
                [
                    'attribute' => 'ActionTitle2',
                    'value' => 'lastAction.ActionTitle2',
                    // 'label' => 'Last Action',
                ],
                [
                    'attribute' => 'LastActionDateHijri',
                    // 'label' => 'Last Action Date',
                ],
                [
                    'attribute' => 'UserName',
//                    'value' => 'lastUser.UserName',
                    'value' => 'lastUser.fullName',
                ],
                [
                    'attribute' => 'LastActionMessage',
                    'value' => 'LastActionMessage',
                    // 'label' => 'Last Message',
                ],
//                [
//                    'attribute' => 'processTitle',
//                    'value' => 'process.ProcessTitle',
//                    'label' => 'Process',
//                ],
                [
                    'attribute' => 'RevisionNumber',
                    'value' => 'contract.RevisionNumber',
                    // 'label' => 'Rev.No',
                    'options' => [
                        'width' => '50px'
                    ]
                ],
                [
                    'attribute' => 'ContractRevisionType',
                    'value' => 'contract.revisionType.ContractRevisionType',
                    // 'label' => 'Revision Type',
                ],
                [
                    'class' => yii\grid\ActionColumn::className(),
                    'template' => '{nextaction}{diagram}{detail}{delete}',
                    'options' => [
                        'width' => '80px;',
                    ],
                    'buttons' => [
                        'nextaction' => function($url, $model, $key) {
                            return (Yii::$app->request->get('type') == 'ToDo') ?
                                Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'contract', 'peid' => $model->ProcessEntityID]), [
                                    'onclick' => "showModal(this); return false;",
                                    'data-pjax' => "0",
                                    'data-peid' => $model->ProcessEntityID,
                                    'data-rowid' => $model->contract->WorkflowContractID,
                                    'data-original-title' => 'Next Action',
                                    'data-toggle' => 'tooltip',
                                    'class' => 'nextAction'
                                ]) : '';
                        },
                        'diagram' => function($url, $model, $key) {
                            return Html::a('<i class="en-flow-branch s16"></i>', Url::to(['/workflow/workflow/graph', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Action Diagram',
                                'class' => "btn btn-xs tip",
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#46ADFF;',
                                'target' => '_blank',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'detail' => function($url, $model, $key) {
                            return Html::a('<i class="im-contract s16"></i>', Url::to(['/workflowContract/workflowcontract/view', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Contract Detail',
                                'class' => 'btn btn-xs tip',
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#AE85BE;',
                                'target' => '_blank',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'delete' => function ($url, $model, $key)use($type, $securityDelete) {
                            if ($type == 'All' && $securityDelete) {
                                return Html::a('<i class="fa-trash" style="font-size:18px"></i>', "#", [
                                    'delete-url' => Yii::$app->urlManager->createAbsoluteUrl(['/workflowContract/workflowcontract/delete-confirm', "peid" => $model->ProcessEntityID]),
                                    'onclick' => "showDeleteclaimModal(this);",
                                    "data-pjax" => "0",
                                    'data-original-title' => "Delete Workflow Contract",
                                    'data-toggle' => 'tooltip'
                                ]);
                            } else {
                                return '';
                            }
                        },
                    ]
                ],
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
    <?php GridViewBox::end() ?>
</div>

<?=
app\components\ModalContractSelector::widget([
    'modalId' => 'modalContractSelector',
    'callFrom' => 'nrw',
    'callBackFunction' => 'createNewContractRider("{value}")',
]);
?>

<?php
Modal::begin(['id' => "delete-workflow-modal", 'header' => "Delete Workflow Contract"]);
Modal::end();
?>
<script >
    function showDeleteclaimModal(obj) {
        $.ajax({
            url: $(obj).attr('delete-url'),
        }).done(function (data) {
            $('#delete-workflow-modal .modal-body').html(data);
            $('#delete-workflow-modal').modal('show');
        });
        return false;
    }

    function createNewContractRider(cid) {
        var uri = "<?= Yii::$app->urlManager->createAbsoluteUrl(['workflowContract/workflowcontract/new-rider', 'cid' => '{value}']) ?>";
        uri = uri.replace('%7Bvalue%7D', cid + '');
        document.location.href = uri;
    }
    $('#contract-pjax').on('pjax:send', function () {
        ajaxProgressLoader('show', '#contract-pjax');
    });

    $('#contract-pjax').on('pjax:complete', function () {
        ajaxProgressLoader('hide', '#contract-pjax');
    });

</script>

<?php
Modal::begin([
    'id' => 'contract-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> Next action</h4>'
]);
Pjax::begin(['id' => 'pjax-modal']);
$js = "
        dubleclick();
        function dubleclick(){
            $('.dblclick').attr('data-toggle', 'tooltip');
            $('.dblclick').attr('data-placement', 'left');
            $('.dblclick').attr('data-original-title', 'Please double-click to select an item and next action');

            $('.dblclick').dblclick(function(){
                $(this).addClass('success');
                var radio = $(this).find('td').find('input:radio')[0];
                $(this).find('.nextAction').trigger('click');
            });
        }
        $('#contract-pjax').on('pjax:complete', function () {
            dubleclick();
        });
    
        function showModal(obj){
            ajaxProgressLoader('show', '#contract-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#contract-modal').modal('show');
                ajaxProgressLoader('hide', '#contract-pjax');
            });
           
            return false;
        }
        $('#pjax-modal').on('pjax:send', function(){
            ajaxProgressLoader('show', '.modal-dialog');
        });

        $('#pjax-modal').on('pjax:complete', function(){
            ajaxProgressLoader('hide', '.modal-dialog');
        });
    ";
$this->registerJs($js, yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>

<!----  All Next Action  ---->
<?php
Modal::begin([
    'id' => 'contract-all-modal',
    'size' => Modal::SIZE_LARGE,
]);
Pjax::begin([
    'id' => 'pjax-all-modal',
]);
$js = "
        function showAllModal(obj){
            ajaxProgressLoader('show', '#contract-pjax');
            
            var href = $('.all-next-action').attr('href')+'&peids=';
            $('tbody tr td input[type=checkbox]').each(function(e, r){
                if($(this).is(':checked')){
                    var value = $(r).val();
                    href+=value+',';
                }
            });
            href = href.substr(0, href.length-1);
            
            var url = href;
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-all-modal').html(data);
                $('#contract-all-modal').modal('show');
                ajaxProgressLoader('hide', '#contract-pjax');
            });

            return false;
        }
    ";
$this->registerjs($js, \yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>

<style>
    .all-next-action {
        position: fixed;
        z-index: 99;
        right: 10px;
        bottom: 10px;
    }
    #cricle-next-action{
        display: none;
    }
    .dblclick{
        cursor: pointer;
    }
</style>

<div id="cricle-next-action">
    <?= Html::a('<i class="im-arrow-up-right3 rotate-360" style="font-size:48px"></i>',
        Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/all-next-action']),
        [
            'class' => 'pull-right mb5 animated bounceInLeft all-next-action',
            'onclick' => 'showAllModal(this); return false;',
            'style' => 'color:#337ab7;',
            'data-pjax' => 0,
            "data-toggle" => "tooltip",
            "data-original-title" => "Go to selected item next action"
        ]); ?>
</div>
<script>
    function allmodalCallbackPjax(data){
        if(data.result){
            $.pjax.reload({
                container:'#contract-pjax',
                timeout: false,
                push: false,
                replace: false,
            });
            swal({
                title: 'Success',
                type: 'success',
                text: data.message,
                timer: 1000,
                showConfirmButton: false,
            });
            $('#contract-all-modal').modal('hide');
        }
        else{
            swal('Error', data.message, 'error');
            setTimeout(function(){
                $('#invoice-all-modal').modal('hide');
            }, 100);
        }
    }

    function showAllNextActionButton(obj){
        var checked = $('#grid-workflow-contract input[type=checkbox]:checked');
        if(checked.length === 0 || (checked.length === 1 && checked.attr('name') === "selection_all")){
            $('#cricle-next-action').hide();
        }
        else{
            $('#cricle-next-action').show();
        }
    }
</script>   