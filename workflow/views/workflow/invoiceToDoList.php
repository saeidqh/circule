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
use omnilight\assets\SweetAlertAsset;

DatepickerAsset::register($this);
SweetAlertAsset::register($this);
JdateTimeAsset::register($this);
InvoiceAsset::register($this);
Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function() {
    Yii::$app->view->registerAssetBundle(app\modules\invoice\assets\InvoiceGridViewAsset::className());
});
$this->title = "WORKFLOW :: Invoice todo";

$this->params['breadcrumbs'] = [
    'lable' => 'workflow'
];
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);

$canInvoiceDelete = \app\access\AccessControl::can('invoice/invoice/delete');
?>
<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $invoiceSearchModel->INVOICE_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/invoice", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?=
    Html::a('<i class="fa-plus"></i> &nbsp;New invoice', '#', [
	'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['contract/widget/select-contract','callfrom'=>'newInvoiceWorkflow']),
	'class' => 'btn btn-sm pull-left btn-mini btn-999999',
	'data-toggle' => 'tooltip',
	'data-original-title' => 'New Invoice',
	'onclick' => 'return btnSelectContractClick(this);',
    ]);
          
    ?>
        <?=
        Html::a('<i class="im-file-excel"></i> &nbspInvoice Duration',
                Yii::$app->urlManager->createAbsoluteUrl(['workflow/export/export-invoice-duration']), [
            'target' => '_blank',
            'class' => 'btn btn-sm btn-success pull-right mr5 btn-export-budget',
//            'onclick' => 'return btnExportClick(this)'
        ])
        ?>
          <?=
             Html::a('<i class="fa-plus"></i> &nbsp;Import invoice', Yii::$app->urlManager->createAbsoluteUrl(['invoice/invoice/import-invoice']), [
	'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['invoice/invoice/import-invoice']),
	'class' => 'btn btn-sm pull-left btn-mini btn-999999',
	'data-toggle' => 'tooltip',
	'data-original-title' => 'New Invoice',
        'style' => 'display: none',
//	'onclick' => 'return btnSelectContractClick(this);', 
]);
?>
       <?=
             Html::a('<i class="fa-plus"></i> &nbsp;user sign', Yii::$app->urlManager->createAbsoluteUrl(['invoice/invoice/usersignature', 'uid' => 601, 'oid' => 3088]), [
	'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['invoice/invoice/usersignature', 'uid' => 601, 'oid' => 3088]),
	'class' => 'btn btn-sm pull-left btn-mini btn-999999',
        'style' => 'display: none',
]);
?>
        </div>

    </div> 
    <div class="row">
        <?php Pjax::begin(['id' => 'invoice-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-invoice',
            'filterModel' => $invoiceSearchModel,
            'dataProvider' => $invoiceDataProvider,
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
                                'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
//                                'onclick'=>'return showAllNextActionButton(this)',
                                'onchange'=>'return showAllNextActionButton(this);',
                            ],
                            'headerOptions' => [
                                'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
//                                'onclick'=>'return showAllNextActionButton(this)',
                                'onchange'=>'return showAllNextActionButton(this);',
                            ],
                            'contentOptions' => [
                                'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                            ],
                            'filterOptions' => [
                                'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                            ],
                        ],
                        [
                            'attribute' => 'InvoiceIDRef',
                            'label' => 'ID',
                            'options' => [
                                'width' => '30px',
                            ],
                        ],
                        [
                            'attribute' => 'contractNo',
                            'label' => 'Contract No',
//                    'value' => 'invoice.contract.ContractNo',
                            'format' => 'raw',
                            'value' => function($model) use ($mainContractUpdateList) {
                                $isMain = in_array($model->invoice->contract->ContractID, $mainContractUpdateList);
                                $html = '<table class="w100 inner-table"><tr><td>';
                                $html .= Html::encode($model->invoice->contract->ContractNo);
                                $html .= '</td><td>';
                                $html .= '<span class="label label-' . ($isMain ? "purple" : 'success') . ' pull-right" style="width:24px;cursor:default" title="' . ($isMain ? "Main Contract" : 'Sub Contract') . '" >';
                                $html .= ($isMain ? "M" : 'S');
                                $html .= '</span>';
                                $html .= '</td><tr></table>';

                                return $html;
                            },
                            'options' => [
                                'width' => '120px',
                            ],
                        ],
                        [
                            'attribute' => 'contractSubject',
                            'label' => 'Contract Subject',
                            'format' => 'raw',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->invoice->contract->ContractSubject, 35, 60);
                            },
                        ],
                        [
                            'attribute' => 'subContractorTitle',
                            'format' => 'raw',
                            'label' => 'SubContractor',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->invoice->contract->subContractor->PersonTitle, 30, 70);
                            },
                            'options' => ['width' => '130px'],
                        ],
                        [
                            'attribute' => 'Title',
                            'label' => 'NO',
                            'format' => 'raw',
//                    'value' => 'invoice.Title',
                            'value' => function($model) {
                                return Utility::generateTextShorterSpan($model->invoice->Title, 35, 17);
                            },
                            'options' => ['width' => '50px'],
                        ],
//                        [
//                            'attribute' => 'CutOffDateHijri',
//                            'label' => 'Cut Off Date',
//                            'value' => 'invoice.CutOffDateHijri',
//                            'options' => ['width' => '50px'],
//                        ],
                           [
                            'attribute' =>'InsertDateHijri',
                            'label' =>'Insert Date Hijri',
                            'value' =>'invoice.InsertDateHijri',
                            'options' => ['width' => '50px'],
                        ],
                        [
                            'attribute' => 'InvoiceType',
                            'label' => 'Type',
                            'value' => 'invoice.type.InvoiceType',
                            'options' => ['width' => '50px'],
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
                            'attribute' => 'approved',
                            'label' => 'Amount Approved',
                            'format' => 'raw',
                            'filter' => false,
                            'value' => function($model) {
                                $color = '#4ac77c';
                                $back = '#C1F0D3';
                                $cur2 = isset($model->invoice->currency2->CurrencyID) ? $model->invoice->currency2->CurrencyTitle : null;
                                return '<table class="inner-table" style="background:' . $back . '">'
                                        . '<tr><td width=\'180\' style="color:' . $color . ';">'
                                        . '<b>' . Utility::moneyFormat($model->invoice->getApproved1(), ($model->invoice->currency1->CurrencyID != 1) ? 2 : null) . '</b>'
                                        . '<td>'
                                        . $model->invoice->currency1->CurrencyTitle
                                        . '</td>'
                                        . '<tr class="top-border"><td style="color:' . $color . ';">'
                                        . '<b>' . Utility::moneyFormatByCurrency($model->invoice->getApproved2(), isset($model->invoice->currency2->CurrencyID) ? $model->invoice->currency2->CurrencyID : null) . '</b>'
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
                            'label' => 'Last Action Date',
                            'value' => 'LastActionDateHijri',
                            'options' => ['width' => '50px'],
                        ],
                                    [
    'label' => 'Duration',
    'format' => 'raw',
    'value' => function($model) {
        if (empty($model->LastActionDateHijri)) {
            return '-';
        }

        $jDate = new \app\components\jDateTime(true, true, 'Asia/Tehran');
        $currentDateHijri = $jDate->date("Y/m/d-hi", false, false);

        $start = $model->LastActionDateHijri;

        if (strlen($start) < 10) {
            return '-';
        }

        $yer1 = intval(substr($start, 0, 4));
        $month1 = intval(substr($start, 5, 2));
        $day1 = intval(substr($start, 8, 2));

        $yer2 = intval(substr($currentDateHijri, 0, 4));
        $month2 = intval(substr($currentDateHijri, 5, 2));
        $day2 = intval(substr($currentDateHijri, 8, 2));

        $timeBetween = $jDate->timeBetweenDate($yer1, $month1, $day1, $yer2, $month2, $day2);

        $duration = '';

        if ($timeBetween[0] > 0) {
            $duration .= $timeBetween[0] . ' month ';
        }

        if ($timeBetween[1] > 0) {
            $duration .= $timeBetween[1] . ' day ';
        }

        if (strlen($start) >= 15) {
            $firstTimeString = substr($start, -4);
            $firstTimeString = substr($firstTimeString, 0, 2) . ':' . substr($firstTimeString, 2);
            $firstTime = new \DateTime($firstTimeString);

            $currentTimeString = $jDate->date("H:i:s", false, false);
            $currentTime = new \DateTime($currentTimeString);

            $interval = $firstTime->diff($currentTime);
            $hour = ($interval->h >= 10) ? $interval->h : '0' . $interval->h;
            $minute = ($interval->i >= 10) ? $interval->i : '0' . $interval->i;

            $duration .= $hour . ':' . $minute;
        } else {
            $duration .= '00:00';
        }

        return trim($duration);
    },
    'options' => ['width' => '90px'],
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
                            'template' => '{nextaction}{diagram}{detail}{delete}{log}{createform}',
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
                                        'detail' => function($url, $model, $key) use ($mainContractUpdateList) {
                                    return Html::a('<i class="fa-dollar s16"></i>', Url::to(['/invoice/' . ((in_array($model->invoice->contract->ContractID, $mainContractUpdateList)) ? 'maincontractinvoice' : 'invoice') . '/view', 'peid' => $model['ProcessEntityID']]), [
                                                'data-original-title' => 'Invocie Detail',
                                                'class' => 'btn btn-xs tip',
                                                'data-pjax' => '0',
                                                'style' => 'padding:1px 2px;color:#AE85BE;',
                                                'target' => '_blank',
                                                'data-toggle' => 'tooltip'
                                    ]);
                                },
                                        'delete' => function($url, $model, $key) use ($canInvoiceDelete) {
//                                    $canInvoiceDelete = \app\access\AccessControl::can('invoice/invoice/delete',["invid" => $model->InvoiceIDRef]);
                                    if ($canInvoiceDelete) {
                                        return \yii\helpers\Html::a('<i class="fa-trash s16"></i>', '#', [
                                            'id' => 'delete-invoice-' . $model->InvoiceIDRef,
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-original-title' => 'Delete Invoice',
                                            'onclick' => 'showModalDeleteInvoice(this); return false;',
                                            'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['invoice/invoice/delete-confirm', 'invid' => $model->InvoiceIDRef]),
                                            ]
                                        );
                                    }
                                },
                                        'log' => function($url, $model, $key) {
                                    return Html::a('<i class="en-text2 s16"></i>', '#', [
                                                'data-toggle' => 'tooltip',
                                                'data-original-title' => "View Log",
                                                'data-pjax' => "0",
                                                'class' => "btn btn-xs tip load-log-popup",
                                                'onclick' => "return LoadLogPopup(this)",
                                                'style' => "padding:1px 2px;color:#DA7575;",
                                                'ajax_url' => Url::toRoute(["/invoice/invoice/log", "invid" => $model->InvoiceIDRef])
                                    ]);
                                },
                                'createform' => function($url, $model, $key) {
                                    if (in_array($model->invoice->contract->SignerPersonIDRef, [387,240])){
                                        return ($model->ProcessIDRef == 57 || $model->ProcessIDRef == 58 || $model->ProcessIDRef == 67 || $model->ProcessIDRef == 25) ?
                                                Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', "#", [
                                                    'onclick' => "return showModalCreateForm(this); ",
                                                    'data-pjax' => "0",
                                                    'target' => "_blank",
                                                    'data-original-title' => 'Create Form',
                                                    'ajax_url' => Yii::$app->urlManager->createAbsoluteUrl(["invoice/invoice/createform", "peid" => $model->ProcessEntityID, "type" => "na"]),
                                                    'data-toggle' => 'tooltip',
                                                ]) : '';
                                    } elseif ($model->invoice->contract->SignerPersonIDRef == 258) {
                                        return ($model->ProcessIDRef == 5 || $model->ProcessIDRef == 6) ?
                                                Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', "#", [
                                                    'onclick' => "return showModalCreateForm(this); ",
                                                    'data-pjax' => "0",
                                                    'target' => "_blank",
                                                    'data-original-title' => 'Create Form',
                                                    'ajax_url' => Yii::$app->urlManager->createAbsoluteUrl(["workflow/letter/create-form", "peid" => $model->ProcessEntityID, "lid" => "19", "type" => "na"]),
                                                    'data-toggle' => 'tooltip',
                                                ]) : '';
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

                <?php \app\components\PopupBox::begin([]); ?>
                <?php \app\components\PopupBox::end([]); ?>

                <?php 
                Modal::begin([
                    'id' => 'invoice-delete-modal'
                ]);
                    echo '<div id="invoice-delete-content"></div>';
                Modal::end();
                ?>    
                <script >

                    function createNewInvoice(cid) {
                        var mainContractUpdateList = <?= \yii\helpers\Json::encode($mainContractUpdateList) ?>;
                        var controllerName = 'invoice';
                        if (mainContractUpdateList.indexOf('100072') != -1)
                            controllerName = 'maincontractinvoice';
                        var uri = "<?= Yii::$app->urlManager->createAbsoluteUrl(["invoice/{controller-name}/create", "cid" => '{value}']); ?>";

                        uri = uri.replace('%7Bvalue%7D', cid + '');
                        uri = uri.replace('%7Bcontroller-name%7D', controllerName + '');
                        console.log(uri);
                        document.location.href = uri;
                    }
                    $('#invoice-pjax').on('pjax:send', function () {
                        ajaxProgressLoader('show', '#invoice-pjax');
                    });

                    $('#invoice-pjax').on('pjax:complete', function () {
                        ajaxProgressLoader('hide', '#invoice-pjax');
                    });
                    
                    function showModalDeleteInvoice(obj) {
                        $.ajax({
                            url: $(obj).attr('data-url'),
                        }).done(function(data){
                            $('#invoice-delete-content').html(data);
                            $('#invoice-delete-modal').modal('show');
                        });
                        return false;
                    }

                    function showModalCreateForm(obj) {
                        var url = $(obj).attr('ajax_url');
                        var pwin = window.open(url, "_blank", "width=700,height=660,scrollbars=yes,resizable=no");
                        if (window.focus)
                            pwin.focus();
                        return false;
                    }

                    function LoadLogPopup(obj) {
                        url = $(obj).attr("ajax_url"),
                                $.ajax({
                                    url: url,
                                }).done(function (data) {
                            showPopupBox(data, 'Invoice Log', '', false);
                        }).error(function () {
                            showMsgBoxInBottomRight('Error in loading data', 'error');
                            return false;
                        });

                        return false;
                    }
                </script>
                <?php
                Modal::begin([
                    'id' => 'invoice-modal',
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
    
    $('#invoice-pjax').on('pjax:complete', function () {
            dubleclick();
    });
    

        function showModal(obj){
            ajaxProgressLoader('show', '#invoice-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                 $('#invoice-modal').modal('show');
                 ajaxProgressLoader('hide', '#invoice-pjax');
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
            ajaxProgressLoader('show', '#invoice-pjax');
            
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
                ajaxProgressLoader('hide', '#invoice-pjax');
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
                container:'#invoice-pjax',
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
        else{
            swal('Error', data.message, 'error');
            setTimeout(function(){
                $('#invoice-all-modal').modal('hide');
            }, 100);
        }
    }
        
    /*$('#grid-workflow-invoice input[type=checkbox]').change(function(e){
        if($(this).is(':checked')){
            $('#cricle-next-action').show();
        }
        else{
            $('#cricle-next-action').hide();
        }
    });*/
    
    function showAllNextActionButton(obj){
        var checked = $('#grid-workflow-invoice input[type=checkbox]:checked');
        if(checked.length === 0 || (checked.length === 1 && checked.attr('name') === "selection_all")){
        $('#cricle-next-action').hide();
        }
        else{
            $('#cricle-next-action').show();
        }
    }
</script>



<?php
yii\bootstrap\Modal::begin([
    'header' => '<h5 class="p0 m0">Select Contarct</h5>',
    'options' => [
        'id' => 'modal-select-contract',
        'tabindex' => false,
    ],
    'size' => yii\bootstrap\Modal::SIZE_LARGE
])
?>
<?php Pjax::begin(['id' => 'select-contract-pjax']); ?>  
<?php Pjax::end(); ?>
<?php yii\bootstrap\Modal::end() ?>

<script>
    function btnSelectContractClick(obj) {
        ajaxProgressLoader('show', '#modal-select-contract .modal-body');
        var url = $(obj).attr('data-url');
        $('#modal-select-contract').modal('show');
        $.pjax.reload({
            container: "#select-contract-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        ajaxProgressLoader('hide', '#modal-select-contract .modal-body');
        return false;
    }
    function addSelectedContractReturnContractId(callfrom) {

        var item = $('#tbl-contract-selector-grid input[type="radio"]:checked').attr('data-id');
        if (item == 'null' || item ==undefined) {
            showMsgBoxInBottomRight('Please select a Sub Contractor and try again.', 'warning');
            return;
        }

        $('#modal-select-contract').modal('hide');
        ajaxProgressLoader('show', '.grid-box.grid-box-silver');
        ajaxProgressLoader('show', '#tbl-contract-selector-grid');
        var url="<?= Yii::$app->urlManager->createAbsoluteUrl(["invoice/invoice/create"]) ?>" ;
        window.location.href=url+"&cid="+item;
    }
</script>
