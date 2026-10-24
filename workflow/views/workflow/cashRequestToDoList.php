<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\components\Utility;
use yii\bootstrap\Modal;
use kartik\grid\GridView as KartikGridView;

$this->title = Yii::t('workflow','WORKFLOW :: Cash request todo list');

\app\modules\workflow\assets\ContractAsset::register($this);
Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function() {
    Yii::$app->view->registerAssetBundle(app\modules\cashRequest\assets\GridViewAsset::className());
});

$cashRequestDeleteUrl = 'cashRequest/cashrequest/delete';
$securityDelete = \app\access\AccessControl::can($cashRequestDeleteUrl);
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>
<div class="row">
    <?php GridViewBox::begin() ?>

    <div class="row">
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            if ($hasSign) {
                echo Html::a("<i class='fa-plus'></i> &nbsp;".Yii::t('workflow','New cash request'), Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/process-select', 'type' => 'cashRequest']), [
                    'onclick' => "showModalNewCashRequest(this); return false;",
                    'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                    'data-pjax' => '0',
                        ]
                );
            } else {
                echo "<a id='btn-sign-alert'  class='btn btn-sm pull-left btn-mini btn-999999' ><i class='fa-plus'></i> &nbsp; ".Yii::t('workflow','New cash request')." </a>";
            }
            ?>
        </div>


        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $cashRequestSearchModel->CASH_REQUEST_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/cashrequest", 'type' => $btns[$i]['value']]) ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>

    </div>


    <div class="row">
        <?php Pjax::begin(['id' => 'cashrequest-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'po-report-invoice-type',
            'dataProvider' => $cashRequestDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['CashRequestIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return ['index' => $index, 'class' => $class.' dblclick', 'id' => 'row-' . $id];
            },
            'filterModel' => $cashRequestSearchModel,
            'floatHeader' => true,
            'columns' => [
                //'ExportID',
                [
                    'attribute' => 'exportId',
                    'value' => function($model) {
                        return 'Py-000-' . Html::encode($model->CashRequestIDRef);
                    },
                ],
                [
                    'attribute' => 'RequestDateHijri',
                    'value' => 'cashrequest.RequestDateHijri',
                ],
                [
                    'attribute' => 'Description',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Utility::generateTextShorterSpan($model->cashrequest->Description, 30);
                    },
                    'options' => [
                        'width' => '250px',
                    ]
                ],
                            [
                    'attribute' => 'createBy',
                    'label'=>'درخواست کننده',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Utility::generateTextShorterSpan($model->cashrequest->user->fullName, 35,60);
                    },  
                            ],
                [
                    'attribute' => 'LastActionDateHijri',
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
                [
                    'attribute' => 'lastUserName',
//                    'value' => 'lastUser.UserName',
                    'value' => 'lastUser.fullName',
                ],
                [
                    'attribute' => 'lastActionTitle',
                    'value' => 'lastAction.ActionTitle2',
                ],
                [
                    'attribute' => 'LastActionAutoMessage',
                    'format' => 'raw',
                    'value' => function($data) {
                        return Utility::generateTextShorterSpan($data['LastActionAutoMessage'], 30);
                    },
                    'options' => [
                        'width' => '150px',
                    ]
                ],
                [
                    'label' => Yii::t('workflow','Actions'),
                    'format' => 'raw',
                    'value' => function($data)use($type, $securityDelete) {
                        $res = ''
                                . (($type == 'ToDo') ? Html::a(
                                        '<i class="im-arrow-right2 rotate-360 s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'type' => 'cashRequest', 'peid' => $data['ProcessEntityID'], 'sort' => Yii::$app->request->get('sort'), 'page' => Yii::$app->request->get('page'),]), [
                                    'onclick' => "showModal(this); return false;",
                                    'class' => 'nextAction btn btn-xs tip',
                                    'data-original-title' => Yii::t('workflow','Next Action'),
                                    'style' => 'color:#AACF72;padding:1px 2px;',
                                    'data-pjax' => "0",
                                    'data-peid' => $data['ProcessEntityID'],
                                    'data-rowid' => $data['CashRequestIDRef'],
                                    "data-toggle" => 'tooltip'
                                        ]
                                ) : '')
                                . '<a title="" data-original-title="'. Yii::t('workflow','Action Diagram').'" data-toggle="tooltip" class="btn btn-xs tip " data-pjax="0" style="padding:1px 2px;color:#646464;" href="' . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/graph', 'peid' => $data['ProcessEntityID']]) . '" target="_blank"><i class="en-flow-branch s16"></i></a>'
                                . '<a title="" data-original-title="'. Yii::t('workflow','Cash Request Detail').'" data-toggle="tooltip" class="btn btn-xs tip " data-pjax="0" style="padding:1px 2px;color:#AE85BE;" href="' . Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/view', 'peid' => $data['ProcessEntityID']]) . '" target="_blank" onclick="return hideNextActionBox(' . $data["ProcessEntityID"] . ');" ><i class="im-coin" style="font-size:18px"></i></a>'
                                . '<a title="" data-original-title="'. Yii::t('workflow','View Log').'" data-toggle="tooltip" class="btn btn-xs tip load-log-popup" style="padding:1px 2px;color:#DA7575;" '
                                . 'ajax_url="' . Yii::$app->urlManager->createAbsoluteUrl(["workflow/ajax/cashrequestlog", "id" => $data['CashRequestIDRef']]) . '" href="#" target="_blank"><i class="en-text2 s16"></i></a>';
                        if ($securityDelete) {
                            $res .= \yii\helpers\Html::a('<i class="fa-trash s16"></i>', '#', [
//                                        'id' => 'delete-cash-request-' . $data['ProcessEntityID'],
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top',
                                        'data-original-title' => 'Delete Cash request',
                                        'onclick' => 'showCashRequestModalDelete(this)',
                                        'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/delete-confirm', 'peid' => $data['ProcessEntityID']]),
                                            ]
                            );
                        }
                            $res .= Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', "#", [
                                    'url' => Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/createform',"peid"=>$data['ProcessEntityID']]),
                                    'data-original-title' => 'Create From',
                                    'data-toggle' => 'tooltip',
                                    'class' => 'btn btn-xs tip',
                                    'style' => 'padding:1px 2px;color:#AE85BE;',
                                    'data-pjax' => 0,
                                    'onclick' => 'return showCreateForm(this);',
                                ]);
                        return $res;
                        ;
                    },
                    'options' => [
                        'width' => 100,
                    ],
                ]
            ],
                            'panel' => [
    'type' => KartikGridView::TYPE_PRIMARY,
    'heading' => 'لیست درخواست وجه',
],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
    <?php GridViewBox::end() ?> 
</div>
<?php
Modal::begin([
    'id' => 'cashrequest-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> Next action</h4>'
]);
Pjax::begin(['id' => 'pjax-modal']);
$js = "
        function showModal(obj){
            ajaxProgressLoader('show', '#cashrequest-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#cashrequest-modal').modal('show');
                ajaxProgressLoader('hide', '#cashrequest-pjax');
            });
           
            return false;
        }
        
    ";
$this->registerJs($js, yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>


<?php
Modal::begin([
    'id' => 'newcashrequest-modal',
    'header' => '<h4 class="modal-title"><i class="im-coin rotate-360"></i> New CashRequest</h4>'
]);
Pjax::begin(['id' => 'newcashrequest-pjax-modal']);
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
    $('#cashrequest-pjax').on('pjax:complete', function () {
            dubleclick();
    });
        
    function showModalNewCashRequest(obj){
        var url = $(obj).attr('href');
        $.ajax({
            url: url
        })
        .done(function(data){
            $('#newcashrequest-modal').modal('show');
            $('#newcashrequest-pjax-modal').html(data);
        });
        return false;
    }
    ";
$this->registerJs($js, yii\web\View::POS_END);

Pjax::end();
Modal::end();



Modal::begin([
    'id' => 'cash-request-delete-modal'
]);
echo '<div id="cash-request-delete-content"></div>';
Modal::end();
?>

<script>

    function showCashRequestModalDelete(obj)
    {
        $.ajax({
            url: $(obj).attr('data-url'),
        }).done(function (data) {
            $('#cash-request-delete-content').html(data);
            $('#cash-request-delete-modal').modal('show');
        });

        return false;
    }

    function showCreateForm(obj){
        var url=$(obj).attr("url");
        var newWindows=window.open(url, "_blank", "width=700,height=660,scrollbars=yes,resizable=no");
        if(window.focus){
            newWindows.focus();
            
        return true;
        
        }
            
    }
</script>

<?php
$css = ".table {
            font-size: 12px !important;
        }
        ";
$this->registerCss($css);