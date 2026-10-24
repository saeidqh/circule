<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Utility;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use app\modules\workflow\assets\InvoiceAsset;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\modules\permission\assets\PermissionGridViewAsset;
use omnilight\assets\SweetAlertAsset;

SweetAlertAsset::register($this);

PermissionGridViewAsset::register($this);

DatepickerAsset::register($this);
JdateTimeAsset::register($this);
InvoiceAsset::register($this);


$this->title = "WORKFLOW :: Permission todo";

$this->params['breadcrumbs'] = [
    'lable' => 'workflow'
];
$securityDelete = \app\access\AccessControl::can('permission/permission/delete');
?>

<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>
<div class="row">
    <?php GridViewBox::begin(['id' => 'permission-pjax']); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $permissionSearchModel->PERMISSION_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/permission", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <a href="#modalContractSelector" data-toggle="modal" role="button" class="btn btn-sm pull-left btn-mini btn-999999" ><i class="fa-plus"></i> &nbsp;New permission</a>
        </div>

    </div> 
    <div class="row">
        <?php Pjax::begin(['id' => 'permission-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-permission',
            'filterModel' => $permissionSearchModel,
            'dataProvider' => $permissionDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['InvoiceIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id);
            },
                    'floatHeader' => true,
                    'columns' => [
                        [
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
                        ],
                        [
                            'attribute' => 'permissionId',
                            'label' => Yii::t('workflow', 'ID'), // '',
                            'options' => [
                                'width' => '30px',
                            ],
                        ],
                        [
                            'attribute' => 'contractSubject',
                            'label' => Yii::t('permission', 'Contract Subject'), //'Contract Subject',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->permission->contract->ContractSubject, 35, 60);
                            },
                        ],
                        [
                            'attribute' => 'subContractorTitle',
                            'format' => 'raw',
                            'label' => Yii::t('permission', 'Subcontractor'), //'SubContractor',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->permission->contract->subContractor->PersonTitle, 30, 70);
                            },
                            'options' => ['width' => '130px'],
                        ],
                        [
                            'attribute' => 'contractNo',
                            'format' => 'raw',
                            'label' => Yii::t('permission', 'Contract No'), //'contractNo',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->permission->contract->ContractNo, 30, 70);
                            },
                            'options' => ['width' => '130px'],
                        ],
                        [
                            'attribute' => 'Title',
                            'label' => Yii::t('permission', 'Title'),
                            'format' => 'raw',
//                    'value' => 'permission.Title',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->permission->title, 35, 17);
                            },
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'cutOffDateHijri',
                            'label' => Yii::t('permission', 'Cut Off Date'), //'Cut Off Date',
                            'value' => 'permission.cutOffDateHijri',
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'typeId',
                            'label' => Yii::t('permission', 'Permission Type'), //'Type',
                            'value' => 'permission.type.title',
                            'options' => ['width' => '70px'],
                        ],
                        [
                            'attribute' => 'lastActionTitle',
                            'label' => Yii::t('workflow', 'Last Action'), //'Last Action',
                            'format' => 'raw',
                            'value' => function($model) {
                                return isset($model->lastAction) ? Utility::generateTextShorterSpan($model->lastAction->ActionTitle2, 30, 60) : '';
                            },
                            'options' => ['width' => '110px'],
                        ],
                        [
                            'attribute' => 'approved',
                            'label' => Yii::t('permission', 'Amount Approved'), //'Amount Approved',
                            'format' => 'raw',
                            'filter' => false,
                            'value' => function($model) {
                                $color = '#4ac77c';
                                $back = '#C1F0D3';
                                $cur2 = isset($model->permission->currency2->CurrencyID) ? $model->permission->currency2->CurrencyTitle : null;
                                return '<table class="inner-table" style="background:' . $back . '">'
                                        . '<tr><td width=\'180\' style="color:' . $color . ';">'
                                        . '<b>' . Utility::moneyFormat($model->permission->getApproved1(), ($model->permission->currency1->CurrencyID != 1) ? 2 : null) . '</b>'
                                        . '<td>'
                                        . $model->permission->currency1->CurrencyTitle
                                        . '</td>'
                                        . '<tr class="top-border"><td style="color:' . $color . ';">'
                                        . '<b>' . Utility::moneyFormatByCurrency($model->permission->getApproved2(), isset($model->permission->currency2->CurrencyID) ? $model->permission->currency2->CurrencyID : null) . '</b>'
                                        . '<td>'
                                        . $cur2
                                        . '</td>'
                                        . '</table>';
                            },
                            'options' => [
                                'width' => '110px'
                            ]
                        ],
                        [
                            'attribute' => 'LastActionDateHijri',
                            'label' => Yii::t('workflow', 'Last Action Date'), //'Last Action Date',
                            'value' => 'LastActionDateHijri',
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'LastActionMessage',
                            'label' => Yii::t('workflow', 'Last Action Message'), //'Last Action Message',
                            'format' => 'raw',
                            'value' => function($data) {
                                return Utility::generateTextShorterSpan($data['LastActionMessage'], 30, 40);
                            },
                            'options' => ['width' => '120px'],
                        ],
                        [
                            'class' => yii\grid\ActionColumn::className(),
                            'header' => 'Actions',
                            'template' => '{nextaction}{diagram}{detail}{delete}{createform}',
                            'options' => [
                                'width' => '100px;',
                            ],
                            'buttons' => [
                                'nextaction' => function($url, $model, $key) {
                                    return (Yii::$app->request->get('type') == 'ToDo') ?
                                            Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'permission', 'peid' => $model->ProcessEntityID]), [
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
                                    return Html::a('<i class="im-key s16"></i>', Url::to(['/permission/permission/view', 'peid' => $model['ProcessEntityID']]), [
                                                'data-original-title' => 'Permission Detail',
                                                'class' => 'btn btn-xs tip',
                                                'data-pjax' => '0',
                                                'style' => 'padding:1px 2px;color:#1fd21c;',
                                                'target' => '_blank',
                                                'data-toggle' => 'tooltip'
                                    ]);
                                },
                                        'delete' => function ($url, $model, $key)use($type, $securityDelete) {
                                    if ($type == 'All' && $securityDelete) {
                                        return Html::a('<i class="fa-trash" style="font-size:18px"></i>', "#", [
                                                    'delete-url' => Yii::$app->urlManager->createAbsoluteUrl(['/permission/permission/delete-confirm', "peid" => $model->ProcessEntityID]),
                                                    'onclick' => "showDeletePermissionModal(this);",
                                                    "data-pjax" => "0",
                                                    'data-original-title' => "Delete Workflow Permission",
                                                    'data-toggle' => 'tooltip'
                                        ]);
                                    } else {
                                        return '';
                                    }
                                },
                                        'createform' => function ($url, $model, $key) {
                                    if (Yii::$app->session->get('CompanyID') == 258 || Yii::$app->session->get('ObsPosition') == 606) {
                                        return Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', "#", [
                                                    'url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/create-form', "peid" => $model->ProcessEntityID, 'lid' => 21]),
                                                    'data-original-title' => 'Create From',
                                                    'data-toggle' => 'tooltip',
                                                    'class' => 'btn btn-xs tip',
                                                    'style' => 'padding:1px 2px;color:#AE85BE;',
                                                    'data-pjax' => 0,
                                                    'onclick' => 'return showCreateForm(this);',
                                        ]);
                                    } else {
                                        return '';
                                    }
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
                    'callFrom' => 'newPermission',
                    'callBackFunction' => 'createNewPermission("{value}")',
                ]);
                ?>
                <?php \app\components\PopupBox::begin([]); ?>
                <?php \app\components\PopupBox::end([]); ?>
                <?php
                Modal::begin(['id' => "delete-workflow-modal", 'header' => "Delete Workflow Contract"]);
                Modal::end();
                ?>

                <script >
                    function showDeletePermissionModal(obj) {
                        $.ajax({
                            url: $(obj).attr('delete-url'),
                        }).done(function (data) {
                            $('#delete-workflow-modal .modal-body').html(data);
                            $('#delete-workflow-modal').modal('show');
                        });
                        return false;
                    }

                    function createNewPermission(cid) {
                        var uri = "<?= Yii::$app->urlManager->createAbsoluteUrl(["permission/permission/create"]); ?>";
                        uri = uri + '&cid=' + cid;
                        document.location.href = uri;
                    }

                    $('#permission-pjax').on('pjax:send', function () {
                        ajaxProgressLoader('show', '#permission-pjax');
                    });

                    $('#permission-pjax').on('pjax:complete', function () {
                        ajaxProgressLoader('hide', '#permission-pjax');
                    });

                    function showModalCreateForm(obj) {
                        var url = $(obj).attr('ajax_url');
                        var pwin = window.open(url, "_blank", "width=700,height=660,scrollbars=yes,resizable=no");
                        if (window.focus)
                            pwin.focus();
                        return false;
                    }
                    function showCreateForm(obj) {
                        var url = $(obj).attr("url");
                        var newWindows = window.open(url, "_blank", "width=700,height=660,scrollbars=yes,resizable=no");
                        if (window.focus) {
                            newWindows.focus();
                            return true;
                        }
                    }

                    function allmodalCallbackPjax(data) {
                        if (data.result) {
                            $.pjax.reload({
                                container: '#permission-pjax',
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
                            $('#invoice-all-modal').modal('hide');
                        }
                        else {
                            swal('Error', data.message, 'error');
                            setTimeout(function () {
                                $('#invoice-all-modal').modal('hide');
                            }, 100);
                        }
                    }

                    $('#grid-workflow-permission input[type=checkbox]').change(function (e) {
                        if ($(this).is(':checked')) {
                            $('#cricle-next-action').show();
                        }
                        else {
                            $('#cricle-next-action').hide();
                        }
                    });

                </script>
                <?php
                Modal::begin([
                    'id' => 'permission-modal',
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

            $('#permission-pjax').on('pjax:complete', function () {
                    dubleclick();
            });
    

        function showModal(obj){
            ajaxProgressLoader('show', '#permission-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#permission-modal').modal('show');
                ajaxProgressLoader('hide', '#permission-pjax');
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
                ?>
  <!--ارسال چندتایی به مرحله بعد از روی صورت وضعیت نوشته شده--> 
                <!----  All Next Action  ---->
                <?php
                Modal::begin([
                    'id' => 'invoice-all-modal',
                    'size' => Modal::SIZE_LARGE,
                ]);
                Pjax::begin([
                    'id' => 'pjax-all-modal',
                ]);
                $js = "
        function showAllModal(obj){
            ajaxProgressLoader('show', '#permission-pjax');
            
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
                $('#invoice-all-modal').modal('show');
                ajaxProgressLoader('hide', '#permission-pjax');
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
                    <?=
                    Html::a('<i class="im-arrow-up-right3 rotate-360" style="font-size:48px"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/all-next-action']), [
                        'class' => 'pull-right mb5 animated bounceInLeft all-next-action',
                        'onclick' => 'showAllModal(this); return false;',
                        'style' => 'color:#337ab7;',
                        'data-pjax' => 0,
                        "data-toggle" => "tooltip",
                        "data-original-title" => "Go to selected item next action"
                    ]);
                    ?>
                </div>
<script>
    function allmodalCallbackPjax(data) {
        if (data.result) {
            $.pjax.reload({
                container: '#permission-pjax',
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
            $('#invoice-all-modal').modal('hide');
        }
        else {
            swal('Error', data.message, 'error');
            setTimeout(function () {
                $('#invoice-all-modal').modal('hide');
            }, 100);
        }
    }

    $('#grid-workflow-permission input[type=checkbox]').change(function (e) {
        if ($(this).is(':checked')) {
            $('#cricle-next-action').show();
        }
        else {
            $('#cricle-next-action').hide();
        }
    });
</script>