<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Utility;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use app\modules\tender\assets\TenderAsset;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use omnilight\assets\SweetAlertAsset;

const IM_BULLHORN = 'im-bullhorn';
DatepickerAsset::register($this);
SweetAlertAsset::register($this);
JdateTimeAsset::register($this);
TenderAsset::register($this);
/* Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function () {
  Yii::$app->view->registerAssetBundle(app\modules\tender\assets\InvoiceGridViewAsset::className());
  }); */
$this->title = "WORKFLOW :: Contract Closing Document todo";

$this->params['breadcrumbs'] = [
    'label' => 'workflow'
];
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => IM_BULLHORN,
]);
?>
<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $contractClosingDocumentSearchModel->TENDER_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5"
                   data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>"
                   href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/contract-closing-document", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>
                <?php
            }
            ?>
        </div>

    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'contract-closin-document-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-contract-closin-document',
            'filterModel' => $contractClosingDocumentSearchModel,
            'dataProvider' => $contractClosingDocumentDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['ContractClosingDocumentIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id);
            },

            'floatHeader' => true,
            'columns' => [
                [
                    'attribute' => 'ContractClosingDocumentIDRef',
                    'label' => Yii::t('workflow', 'ID'),
                    'options' => [
                        'width' => '30px',
                    ],
                ],
                [
                    'attribute' => 'ContractIDRef',
                    'value' => 'contractClosingDocument.ContractIDRef',
                    'options' => [
                        'width' => '100px'
                    ]
                ],
                [
                    'attribute' => 'ContractNo',
                    'value' => 'contractClosingDocument.contract.ContractNo',
                ],
                [
                    'attribute' => 'signerTitle',
                    'value' => 'contractClosingDocument.contract.signer.PersonTitle',
                    'label' => Yii::t('contract', 'Client'),
                ],
                [
                    'attribute' => 'subContractorTitle',
                    'value' => function($model) {
                        return $model->contractClosingDocument->contract->subcontractorsList;
                    },
                    'label' => Yii::t('contract', 'Subcontractor'),
                ],
                [
                    'attribute' => 'ContractDocumentTypeIDRef',
                    'label' => Yii::t('contract', 'Contract Document Type Title'),
                    'value' => function ($model) {
                        return $model->contractClosingDocument->type->ContractDocumentTypeTitle;
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $documentTypeList,
                        'options' => ['placeholder' => 'نوع سند'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '200px'
                        ]
                    ],
                ],
                [
                    'attribute' => 'lastActionTitle',
                    'label' => Yii::t('workflow', 'Last Action'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return isset($model->lastAction) ? Utility::generateTextShorterSpan($model->lastAction->ActionTitle2, 30, 60) : '';
                    },
                ],
                [
                    'attribute' => 'LastActionDateHijri',
                    'label' => Yii::t('workflow', 'Last Action Date'),
                    'value' => 'LastActionDateHijri',
                ],
                [
                    'attribute' => 'LastActionMessage',
                    'label' => Yii::t('workflow', 'LastActionMessage'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return Utility::generateTextShorterSpan($data['LastActionMessage'], 30, 40);
                    },
                ],
                [
                    'class' => yii\grid\ActionColumn::className(),
                    'header' => Yii::t('workflow', 'Actions'),
                    'template' => '{nextaction}{diagram}{detail}',
                    'options' => [
                        'width' => '100px;',
                    ],
                    'buttons' => [
                        'nextaction' => function ($url, $model, $key) {
                            return (Yii::$app->request->get('type') == 'ToDo') ?
                            Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'contractClosingDocument', 'peid' => $model->ProcessEntityID]), [
                                'onclick' => "showModal(this); return false;",
                                'data-pjax' => "0",
                                'data-original-title' => 'Next Action',
                                'data-toggle' => 'tooltip',
                                'class' => 'nextAction'
                            ]) : '';
                        },
                        'diagram' => function ($url, $model, $key) {
                            return Html::a('<i class="en-flow-branch s16"></i>', Url::to(['/workflow/workflow/graph', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Action Diagram',
                                'class' => "btn btn-xs tip",
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#46ADFF;',
                                'target' => '_blank',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'detail' => function ($url, $model, $key) {
                            return Html::a('<i class="st-files s16"></i>', Url::to(['/contract/document/view', 'peid' => $model->ProcessEntityID, 'cid' => 1]), [
                                'data-original-title' => 'Document Detail',
                                'class' => 'btn btn-xs tip',
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#AE85BE;',
                                'target' => '_blank',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                    ]
                ]
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
    <?php GridViewBox::end() ?>
</div>

<?php \app\components\PopupBox::begin([]); ?>
<?php \app\components\PopupBox::end([]); ?>
<?php
Modal::begin([
    'id' => 'contract-closin-document-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> Next action</h4>',
    'options' => [
        'class' => 'fade modal in animated pulse'
    ]
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
    
    $('#contract-closin-document-pjax').on('pjax:complete', function () {
            dubleclick();
    });
        function showModal(obj){
            ajaxProgressLoader('show', '#contract-closin-document-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#contract-closin-document-modal').modal('show');
                ajaxProgressLoader('hide', '#contract-closin-document-pjax');
            });
            return false;
        }
        $('#pjax-modal').on('pjax:send', function(){
            ajaxProgressLoader('show', '.modal-content');
        });
        
        $('#pjax-modal').on('pjax:complete', function(){
            ajaxProgressLoader('hide', '.modal-content');
        });
    ";
$this->registerJs($js, yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>

<?php
Modal::begin([
    'id' => 'process-select-modal',
    'header' => '<h4 class="modal-title">Select Process</h4>'
]);
Pjax::begin(['id' => 'process-select-pjax']);
$js = "
    function showProcessSelectModal(url){
        $.ajax({
            url: url
        })
        .done(function(data){
            $('#process-select-modal').modal('show');
            $('#process-select-pjax').html(data);
        });
        return false;
    }
    ";
$this->registerJs($js, yii\web\View::POS_END);

Pjax::end();
Modal::end();
?>

<script type="text/javascript" >
    function createProcessEntity(obj) {
            var pid = 69;
            var ajaxUrl = $(obj).attr('data-ajax-url');
            window.location.href = ajaxUrl + "&pid=" + pid;
    }
</script>


