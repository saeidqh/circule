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
$this->title = "WORKFLOW :: Tender todo";

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
            $btns = $tenderSearchModel->TENDER_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5"
                   data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>"
                   href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/tender", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $url = Yii::$app->urlManager->createAbsoluteUrl(['tender/tender/create']);
            ?>
            <button type=button class="btn btn-sm btn-success" id="btn-create-process-entirt" onClick="createProcessEntity(this)" data-ajax-url="<?= $url ?>" ><i class="fa-plus"></i> <?php echo Yii::t('tender', 'New Tender'); ?></button>
            <?php
//            echo
//            Html::a('<i class="fa-plus"></i> &nbsp;'.Yii::t('tender', 'New Tender'), Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/process-select','type'=>'tender']), [
////                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['contract/widget/select-contract', 'callfrom' => 'newInvoiceWorkflow']),
//                'class' => 'btn btn-rank-alert btn-sm btn-primary',
//                'data-pjax' => '0',
//                'onclick' => 'btnRankClick(this); return false;',
//            ]);
            ?>
        </div>

    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'tender-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-tender',
            'filterModel' => $tenderSearchModel,
            'dataProvider' => $tenderDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['TenderIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id);
            },

            'floatHeader' => true,
            'columns' => [
                /* [
                  'class' => 'yii\grid\CheckboxColumn',
                  'checkboxOptions' => [
                  'hidden' => (Yii::$app->request->get('type') == 'ToDo') ? false : true,
                  ],
                  'headerOptions' => [
                  'hidden' => (Yii::$app->request->get('type') == 'ToDo') ? false : true,
                  ],
                  'contentOptions' => [
                  'hidden' => (Yii::$app->request->get('type') == 'ToDo') ? false : true,
                  ],
                  'filterOptions' => [
                  'hidden' => (Yii::$app->request->get('type') == 'ToDo') ? false : true,
                  ],
                  ], */
                [
                    'attribute' => 'TenderIDRef',
                    'label' => Yii::t('tender', 'TenderID'),
                    'options' => [
                        'width' => '30px',
                    ],
                ],
                [
                    'attribute' => 'companyIDRef',
                    'label' => Yii::t('tender', 'Company'),
                    'value' => function ($model) {
                        return empty($model->tender->company) ? '' : $model->tender->company->companyName;
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $companyList,
                        'options' => ['placeholder' => 'انتخاب شرکت'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '200px'
                        ]
                    ],
                ],
                [
                    'attribute' => 'subject',
                    'value' => function ($model) {
                        return $model->tender->subject;
                    },
                    'label' => Yii::t('tender', 'Subject'),
                    'options' => [
                        'width' => '300px',
                    ],
                ],
                [
                    'attribute' => 'tenderType',
                    'label' => Yii::t('tender', 'Tender Type'),
                    'value' => function ($model) {
                        return empty($model->tender->tenderType) ? '' : $model->tender->getTenderType($model->tender->tenderType);
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'options' => ['placeholder' => 'انتخاب نوع مناقصه'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ],
                    'filter' => $tenderModel->getTenderTypeList(),
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
                            Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'tender', 'peid' => $model->ProcessEntityID]), [
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
                            return Html::a('<i class="im-bullhorn s16"></i>', Url::to(['/tender/tender/view', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Invocie Detail',
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
    'id' => 'tender-modal',
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
    
    $('#tender-pjax').on('pjax:complete', function () {
            dubleclick();
    });
        function showModal(obj){
            ajaxProgressLoader('show', '#tender-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#tender-modal').modal('show');
                ajaxProgressLoader('hide', '#tender-pjax');
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
        var hasRank = '<?= $hasRank ?>';
        var message = '<?= Yii::t('tender', 'Not set Company Rank for this date!') ?>';
        if (!hasRank) {
            showMsgBoxInBottomRight(message, "error");
            return false;
        } else {
            var pid = 69;
            var ajaxUrl = $(obj).attr('data-ajax-url');
            window.location.href = ajaxUrl + "&pid=" + pid;
        }
    }
</script>


