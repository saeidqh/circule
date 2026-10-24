<?php

use app\components\RegisterRequird;
use app\components\PageHeader;
use app\components\GridViewBox;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use app\components\Utility;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

DatepickerAsset::register($this);
JdateTimeAsset::register($this);
Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function() {
    Yii::$app->view->registerAssetBundle(app\modules\debitmemo\assets\DebitmemoGridViewAsset::className());
});

$this->title = "WORKFLOW :: Debit Memo todo";

$this->params['breadcrumbs'] = [
    'lable' => 'workflow'
];
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
    'help' => $help,
]);
?>
<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $debitmemoSearchModel->DEBIT_MEMO_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/debitmemo", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <a href="#modalContractSelector" data-toggle="modal" role="button" class="btn btn-sm pull-left btn-mini btn-999999" ><i class="fa-plus"></i> &nbsp;New Debit Memo</a>
        </div>

    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'debitmemo-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-debitmemo',
            'filterModel' => $debitmemoSearchModel,
            'dataProvider' => $debitmemoDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['InvoiceIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class.' dblclick', 'id' => 'row-' . $id);
            },
            'floatHeader' => true,
            'columns' => [
                        [
                            'attribute' => 'contractNo',
                            'label' => 'contractNo',
                            'value' => 'debitmemo.contract.ContractNo',
                            'options' => ['width' => '100px'],
                        ],
                        [
                            'attribute' => 'contractSubject',
                            'label' => 'Contract Subject',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->debitmemo->contract->ContractSubject, 35, 60);
                            },
                        ],
                        [
                            'attribute' => 'subContractorTitle',
                            'format' => 'raw',
                            'label' => 'SubContractor',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->debitmemo->contract->subContractor->PersonTitle, 30, 70);
                            },
                            'options' => ['width' => '100px'],
                        ],
                        [
                            'attribute' => 'debitmemoNo',
                            'label' => 'Debit Memo NO.',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->debitmemo->debitmemoNo, 35, 17);
                            },
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'description',
                            'format' => 'raw',
                            'value' => function($model) {
                                return '<div style="width:114px;">' .
                                        Utility::generateTextShorterSpan($model->debitmemo->description, 25, 60) . '<div>';
                            }
                        ],
                        [
                            'attribute' => 'receiver',
//                            'filter' => \yii\helpers\Html::activeDropDownList($debitmemoSearchModel, 'receiver', ['55' => 'شرکت پتروسینا آریا (PSA)', '56' => 'شرکت پتروپایدار ایرانیان (PPI)'], ['class' => 'form-control', 'prompt' => 'Select ...', 'style' => 'height:28px; font-size: 10px; padding: 3px;',]),
//                            'filter' => \yii\helpers\Html::activeDropDownList($debitmemoSearchModel, 'receiver', ['240' => 'شرکت پتروسینا آریا (PSA)', '241' => 'شرکت پتروپایدار ایرانیان (PPI)'], ['class' => 'form-control', 'prompt' => 'Select ...', 'style' => 'height:28px; font-size: 10px; padding: 3px;',]),
//                            'value' => 'debitmemo.receiver'
                            'value' => function($model) {
                                return isset($model->debitmemo) ? $model->debitmemo->ownerCompany->PersonTitle : '';
                            }
                        ],
                        [
                            'attribute' => 'debitmemoDateHijri',
                            'label' => 'Debit Memo Date',
                            'value' => 'debitmemo.debitmemoDateHijri',
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'lastUserName',
//                            'value' => 'lastUser.UserName',
                            'value' => 'lastUser.fullName',
                        ],
                        [
                            'attribute' => 'lastActionTitle',
                            'label' => 'Last Action',
                            'format' => 'raw',
                            'value' => function($model) {
                                return isset($model->lastAction) ? Utility::generateTextShorterSpan($model->lastAction->ActionTitle2, 30, 60) : '';
                            },
                            'options' => ['width' => '110px'],
                        ],
                        [
                            'attribute' => 'LastActionDateHijri',
                            'label' => 'Last Action Date',
                            'value' => 'LastActionDateHijri',
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'typeId',
                            'label' => 'Type',
                            'filter' => \yii\helpers\Html::activeDropDownList($debitmemoSearchModel, 'typeId', \app\modules\debitmemo\models\Debitmemo::getTypeArray(), ['class' => 'form-control', 'prompt' => 'Select ...', 'style' => 'height:28px; font-size: 10px; padding: 3px;',]),
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan(\app\modules\debitmemo\models\Debitmemo::getTypeArray()[$model->debitmemo->typeId], 35, 17);
                            },
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'LastActionMessage',
                            'label' => 'Last Action Message',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Utility::generateTextShorterSpan($data['LastActionMessage'], 30, 40);
                            },
                            'options' => ['width' => '120px'],
                        ],
                        [
                            'class' => yii\grid\ActionColumn::className(),
                            'header' => 'Actions',
                            'template' => '{nextaction}{diagram}{detail}',
                            'options' => [
                                'width' => '100px;',
                            ],
                            'buttons' => [
                                'nextaction' => function($url, $model, $key) {
                                    return (Yii::$app->request->get('type') == 'ToDo') ?
                                            Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'invoice', 'peid' => $model->ProcessEntityID]), [
                                                'onclick' => "showModal(this); return false;",
                                                'data-pjax' => "0",
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
                                    return Html::a('<i class="fa-money s16"></i>', Url::to(['/debitmemo/debitmemo/view', 'peid' => $model->ProcessEntityID]), [
                                                'data-original-title' => 'Debit Memo Detail',
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
                <?=
                app\components\ModalContractSelector::widget([
                    'modalId' => 'modalContractSelector',
                    'callFrom' => 'newDebitmemo',
                    'callBackFunction' => 'createNewDebitmemo("{value}")',
                ]);
                ?>

                <?php   Modal::begin(['id'=>'delete-debitmemo-modal']); 
                Modal::end();?>

                <script>

                    function createNewDebitmemo(cid) {
                        var url = "<?= Yii::$app->urlManager->createAbsoluteUrl(["debitmemo/debitmemo/invoice-select", "cid" => '{cid}']); ?>";
                        url = url.replace('%7Bcid%7D', cid + '');
                        $.ajax({
                            url: url,
                        }).done(function (data) {
                            $('#invoice-select-pjax').html(data);
                            $('#invoice-select-modal').modal('show');
                        }).error(function () {
                            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
                            return false;
                        });
                    }
                    $('#debitmemo-pjax').on('pjax:send', function () {
                        ajaxProgressLoader('show', '#debitmemo-pjax');
                    });

                    $('#debitmemo-pjax').on('pjax:complete', function () {
                        ajaxProgressLoader('hide', '#debitmemo-pjax');
                    });

                </script>
                <?php
                Modal::begin([
                    'id' => 'debitmemo-modal',
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
                    $('#debitmemo-pjax').on('pjax:complete', function () {
                            dubleclick();
                    });
    

        function showModal(obj){
            ajaxProgressLoader('show', '#debitmemo-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#debitmemo-modal').modal('show');
                ajaxProgressLoader('hide', '#debitmemo-pjax');
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


                Modal::begin([
                    'id' => 'letter-modal',
                    'header' => '<h4 class="modal-title"><span class="fa-envelope"></span> Create letter</h4>',
                ]);
                echo '<div id="leter-content"></div>';
                $js = "
     //show CreateLetter modal
    function showModalCreateLetter(obj){
        var url = $(obj).attr('href');
        $.ajax({
            url:url,
            data:{lid:$('#drop-letter').val()}
        })
        .done(function(data){
            $('#leter-content').html(data);
        });
    }
    
";
                $this->registerJs($js, yii\web\View::POS_END);
                Modal::end();



                \yii\bootstrap\Modal::begin([
                    'id' => 'invoice-select-modal',
                    'size' => Modal::SIZE_LARGE,
                    'header' => '<h4 class="modal-title">Select Invoice</h4>',

                ]);
                Pjax::begin([
                    'id' => 'invoice-select-pjax',
                ]);
                Pjax::end();
                \yii\bootstrap\Modal::end();
                ?>
<style>
    .dblclick{
        cursor: pointer;
    }
</style>