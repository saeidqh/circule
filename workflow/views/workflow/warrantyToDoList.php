<?php

use app\modules\workflow\assets\WarrantyAsset;
use app\components\PageHeader;
use app\components\GridViewBox;
use yii\helpers\Html;
use yii\grid\GridView;
use app\components\Utility;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\assets\JdateTimeAsset;
use app\assets\DatepickerAsset;
use yii\bootstrap\Modal;
DatepickerAsset::register($this);
JdateTimeAsset::register($this);
WarrantyAsset::register($this);
Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function(){
    Yii::$app->view->registerAssetBundle(app\modules\warranty\assets\GridViewAsset::className());
});
$this->title = "WORKFLOW :: Warranty todo list (Under Construction)";

$this->params['breadcrumbs'] = [
    'leble' => 'WORKFLOW',
];
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>


    <div class="row">
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?= Html::a("<i class='fa-plus'></i> &nbsp;New Warranty", "#modalContractSelector", [
                'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                'id' => 'btn-new-warranty',
                'data-toggle' => 'modal',
                'data-pjax' => '0',
            ]);?>
        </div>


        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $model->WARRANTY_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-pjax="0" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/warranty", 'type' => $btns[$i]['value']]) ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>
    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'warranty-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-warranty-todo',
            'tableOptions' => ['class' => 'table table-striped table-hover non-responsive psa-grid table-bordered'],
            'dataProvider' => $warrantyDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $class = $index % 2 ? 'odd' : 'even';
                return ['index' => $index, 'class' => $class.' dblclick', 'id' => 'row-' . $model->warrantyIdRef];
            },
            'filterModel' => $model,
            'floatHeader' => true,
            'columns' => [
                'warranty.warrantyId',
                [
                    'attribute' => 'contractNo',
                    'value' => 'warranty.contract.ContractNo',
                ],
                [
                    'attribute' => 'warrantySubject',
                    'value' => 'warranty.subject.warrantySubjectTitle',
                ],
                [
                    'attribute' => 'warrantyType',
                    'value' => 'warranty.type.warrantyTypeTitle',
                ],
                'warranty.warrantyAmount',
                [
                    'attribute' => 'LastActionDateHijri',
                    'label' => 'Last Action Date',
                    'value' => 'LastActionDateHijri',
                ],
                [
                    'attribute' => 'LastActionMessage',
                    'format' => 'raw',
                    'value' => function($data) {
                        return Utility::generateTextShorterSpan($data['LastActionMessage'], 30);
                    },
                    'options' => [
                        'width' => '150px',
                    ]
                ],
//                'lastUser.UserName',
                'lastUser.fullName',            
                'lastAction.ActionTitle1',
                [
                    'header'=> 'actions',
                    'class' => yii\grid\ActionColumn::className(),
                    'template' => '{nextaction}{diagram}{detail}',
                    'options' => [
                        'width' => '80px',
                    ],
                    'buttons' => [
                        'nextaction' => function($url, $model, $key){
                            return (Yii::$app->request->get('type') == 'ToDo') ? Html::a(
                                        '<i class="im-arrow-right2 s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'type' => 'warranty', 'peid' => $model['ProcessEntityID']]), [
                                    'onclick' => "showModal(this); return false;",
                                    'class' => 'nextAction',
                                    'data-original-title' => "Next Action",
                                    'style' => 'color:#AACF72;padding:1px 2px;',
                                    'data-pjax' => "0",
                                        ]
                                ) : '';
                        },
                        'diagram' => function($url, $model, $key){
                            return Html::a('<i class="en-flow-branch s16"></i>',
                                Url::to(['/workflow/workflow/graph', 'peid' => $model['ProcessEntityID']]),
                                [
                                    'data-original-title' => 'Action Diagram',
                                    'class' => 'btn btn-xs tip',
                                    'data-pjax' => '0',
                                    'style' => 'padding:1px 2px;color:#646464;',
                                    'target' => '_blank',
                                ]);
                        },
                        'detail' => function($url, $model, $key){
                            return Html::a('<i class="im-stack" style="font-size:18px"></i>',
                                Url::to(['/warranty/warranty/view', 'peid' => $model['ProcessEntityID']]),
                                [
                                    'data-original-title' => 'Warranty Detail',
                                    'class' => 'btn btn-xs tip',
                                    'data-pjax' => '0',
                                    'style' => 'padding:1px 2px;color:#AE85BE;',
                                    'target' => '_blank',
                                    'onclick' => 'return hideNextActionBox(' . $model["ProcessEntityID"] . ');',
                                ]);
                        },
                    ]
                ],
            ],
        ]);
        ?> 
        <?php Pjax::end(); ?>
    </div>


<?= app\components\ModalContractSelector::widget([
    'modalId' => 'modalContractSelector',
    'callFrom' => 'newWarranty',
    'callBackFunction' => 'createNewWarranty("{value}")',
]);?>


<script >
    function createNewWarranty(cid) {
        var uri = "<?= Yii::$app->urlManager->createAbsoluteUrl(['warranty/warranty/new-warranty', 'cid' => '{value}']) ?>";
        uri = uri.replace('%7Bvalue%7D', cid + '');
        document.location.href = uri;
    }
</script>


<?php Modal::begin([
    'id' => 'warranty-modal',
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
        $('#warranty-pjax').on('pjax:complete', function () {
                dubleclick();
        });
                    

        function showModal(obj){
            ajaxProgressLoader('show', '#warranty-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#warranty-modal').modal('show');
                ajaxProgressLoader('hide', '#warranty-pjax');
            });
           
            return false;
        }
    ";
    $this->registerJs($js, yii\web\View::POS_END);
Pjax::end();
Modal::end();?>