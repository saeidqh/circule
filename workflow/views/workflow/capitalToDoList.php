<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\components\Utility;
use yii\bootstrap\Modal;
use omnilight\assets\SweetAlertAsset;
SweetAlertAsset::register($this);

$this->title = Yii::t('workflow', "WORKFLOW :: Capital todo list");

Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function() {
    Yii::$app->view->registerAssetBundle(app\modules\capital\assets\GridViewAsset::className());
});

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
            echo Html::a(Yii::t('workflow', "{icon} New Capital", ['icon' => "<i class='fa-plus'></i>"]), Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/process-select','type'=>'capital']), [
                'onclick' => "showProcessSelectModal(this); return false;",
                'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                'data-pjax' => '0',
                    ]
            );
            ?>
        </div>


        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
<?php
$btns = $capitalSearchModel->CAPITAL_TODO_REPORT_TYPE;
for ($i = 0; $i < count($btns); $i++) {
    ?>
                <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/capital", 'type' => $btns[$i]['value']]) ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>

    </div>


    <div class="row">
<?php Pjax::begin(['id' => 'capital-pjax']); ?>
<?=
\kartik\grid\GridView::widget([
    'id' => 'po-report-invoice-type',
    'dataProvider' => $capitalDataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $id = $model['capitalBalanceIdRef'];
        $class = $index % 2 ? 'odd' : 'even';
        return ['index' => $index, 'class' => $class.' dblclick', 'id' => 'row-' . $id];
    },
    'filterModel' => $capitalSearchModel,
    'floatHeader' => true,
    'columns' => [
        [
            'attribute' => 'capitalId',
            'label' => Yii::t('workflow', 'ID'),
            'options' => [
                'style' => 'width:3em',
            ],
            'value' => function($model) {
                return Html::encode($model->capitalBalanceIdRef);
            },
        ],
        [
            'attribute' => 'creatorUserName',
            'label' => Yii::t('workflow', 'UserName'),
            'options' => [
                'style' => 'width:5em',
            ],
            'value' => function($model) {
//                return $model->capital->user->UserName;
                return Html::encode($model->capital->user->fullName);
            },
        ],
        [
            'attribute' => 'creatorObs',
            'options' => [
                'style' => 'width:12em',
            ],
            'label' => Yii::t('workflow', 'Obs'),
            'value' => function($model) {
        return Html::encode($model->capital->obs->obsTitle);
    },
        ],
        [
            'attribute' => 'balanceDescription',
            'label' => Yii::t('workflow', 'Balance Description'),
            'format' => 'raw',
            'value' => function($model) {
        return Utility::generateTextShorterSpan($model->capital->balanceDescription, 30);
    },
            'options' => [
                'width' => '380px',
            ]
        ],
        [
            'attribute' => 'LastActionDateHijri',
            'options' => [
                'style' => 'width:10em',
            ],
            'value' => 'LastActionDateHijri',
        ],
        [
            'attribute' => 'lastActionTitle',
            'options' => [
                'style' => 'width:10em',
            ],
            'value' => 'lastAction.ActionTitle2',
        ],
        [
            'attribute' => 'balanceAmounts',
            'label' => Yii::t('workflow', 'balanceAmounts'),
            'options' => [
                'style' => 'width:10em',
            ],
            'value' => function($model) {
                return Utility::moneyFormat($model->balanceAmounts).' IRR';
            },
        ],
        [
            'attribute' => 'lastUserName',
//            'value' => 'lastUser.UserName',
            'value' => 'lastUser.fullName',
            'label' => 'Last User',
            'options' => [
                'width' => '30px',
            ]
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
            'header' => Yii::t('app', 'actions'),
            'options' => [
                'style' => 'width:6em',
            ],
            'class' => \yii\grid\ActionColumn::className(),
            'template' => '{nextaction} {diagram} {detail} {createForm} {delete}',
            'buttons' => [
                'nextaction' => function($key, $model, $index) use($type){
                    return (($type == 'ToDo') ? Html::a(
                                '<i class="im-arrow-right2 rotate-360 s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'type' => 'capital', 'peid' => $model['ProcessEntityID'], 'sort' => Yii::$app->request->get('sort'), 'page' => Yii::$app->request->get('page'),]), [
                            'onclick' => "showModal(this); return false;",
                            'class' => 'nextAction btn btn-xs tip',
                            'data-original-title' => "Next Action",
                            'style' => 'color:#AACF72;padding:1px 2px;',
                            'data-pjax' => "0",
                            'data-peid' => $model['ProcessEntityID'],
                            'data-rowid' => $model['capitalBalanceIdRef'],
                            "data-toggle" => 'tooltip'
                                ]
                        ) : '');
                },
                'diagram' => function($key, $model, $index){
                    return Html::a('<i class="en-flow-branch s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/graph', 'peid' => $model['ProcessEntityID']]), [
                        'target' => '_blank',
                        'data-original-title' => 'Action Diagram ',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-xs tip',
                        'style' => 'padding:1px 2px;color:#646464;',
                        'data-pjax' => 0
                    ]);
                },
                'detail' => function($key, $model, $index){
                    return Html::a('<i class="im-dashboard" style="font-size:18px"></i>', Yii::$app->urlManager->createAbsoluteUrl(['capital/capital/view', 'peid' => $model['ProcessEntityID']]), [
                        'target' => '_blank',
                        'data-original-title' => 'Capital Balance Detail',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-xs tip',
                        'style' => 'padding:1px 2px;color:#AE85BE;',
                        'data-pjax' => 0,
                        'onclick' => "return hideNextActionBox(".$model["ProcessEntityID"].");"
                    ]);
                },
                'createForm' => function($key, $model, $index){
                    return Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', "#", [
                        'url' => Yii::$app->urlManager->createAbsoluteUrl(['capital/capital/create-form',"bid"=>$model['capitalBalanceIdRef']]),
                        'data-original-title' => 'Create From',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-xs tip',
                        'style' => 'padding:1px 2px;color:#AE85BE;',
                        'data-pjax' => 0,
                        'onclick' => 'return showCreateForm(this);',
                    ]);
                },
                'delete' => function($key, $model, $index){
                    $candelete = app\access\AccessControl::can('capital/capital/delete', ['peid' => $model['ProcessEntityID']]); 
                    return ($candelete) ?  Html::a('<span class="glyphicon glyphicon-trash"></span>', Yii::$app->urlManager->createAbsoluteUrl(['capital/capital/delete', 'peid' => $model['ProcessEntityID']]), [
                        'data-original-title' => 'delete',
                        'data-toggle' => 'tooltip',
                        'class' => 'btn btn-xs tip',
                        'style' => 'padding:1px 2px;color:#AE85BE;',
                        'data-pjax' => 0,
                        'onclick' => "deleteCapital(this); return false;"
                    ]): null;
                }
            ]
        ],
        
    ],
]);
?>
        <?php Pjax::end(); ?>
    </div>
        <?php GridViewBox::end() ?> 
</div>
    <?php
    Modal::begin([
        'id' => 'capital-modal',
        'size' => Modal::SIZE_LARGE,
        'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> Next action</h4>'
    ]);
    Pjax::begin(['id' => 'pjax-modal']);
    $js = "
        dubleclick();
        function dubleclick(){
            $('.dblclick').attr('data-toggle', 'tooltip').stop();
            $('.dblclick').attr('data-placement', 'left').stop();
            $('.dblclick').attr('data-original-title', 'Please double-click to select an item and next action');

            $('.dblclick').dblclick(function(){
                $(this).addClass('success');
                var radio = $(this).find('td').find('input:radio')[0];
                $(this).find('.nextAction').trigger('click');
            });
        }
        $('#capital-pjax').on('pjax:complete', function () {
            dubleclick();
        });

        function showModal(obj){
            ajaxProgressLoader('show', '#capital-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#capital-modal').modal('show');
                ajaxProgressLoader('hide', '#capital-pjax');
            });
           
            return false;
        }
        
        function deleteCapital(obj){
            var url = $(obj).attr('href');
            swal({
                title:'Are you sure?',
                text:'Are you sure delete item?!',
                type:'warning',
                showCancelButton:true,
                colseOnConfrim:false,
                showLoaderOnConfrim:true,
            },
            function(){
                $.ajax({
                    url: url
                })
                .done(function(data){
                    if(data.result){
                        swal('delete', data.message, 'success');
                        $.pjax.reload({
                            container: '#capital-pjax',
                            timeout: false,
                            push: false,
                            replace: false,
                        });
                    }
                    else{
                        swal('delete', data.message, 'error');
                    }
                });
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
    'id' => 'process-select-modal',
    'header' => '<h4 class="modal-title">Select Process</h4>'
]);
Pjax::begin(['id' => 'process-select-pjax']);
$js = "
    function showProcessSelectModal(obj){
        var url = $(obj).attr('href');
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
<script>
    function showCreateForm(obj){
        var url=$(obj).attr("url");
        var newWindows=window.open(url, "_blank", "width=700,height=660,scrollbars=yes,resizable=no");
        if(window.focus){
            newWindows.focus();
            
        return true;
        
        }
            
    }
</script>
<style>
    .dblclick{
        cursor: pointer;
    }
</style>