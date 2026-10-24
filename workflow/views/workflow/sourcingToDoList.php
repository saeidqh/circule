<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\helpers\Html;
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
$this->title = "WORKFLOW :: Sourcing todo";

$this->params['breadcrumbs'] = [
    'lable' => 'workflow'
];
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>
<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $sourcingSearchModel->SOURCING_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/sourcing", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">        
            <a class="btn btn-sm btn-primary" data-pjax="0" target="_blank" style="border-radius: 3px;margin: 0px 3px 0px 3px;" href="<?= Yii::$app->urlManager->createAbsoluteUrl('obs/person/create') ?>" ><i class="fa-plus" ></i> &nbsp;<?= Yii::t('obs','New Person') ?></a>       
        </div>

    </div> 
    <div class="row">
        <?php Pjax::begin(['id' => 'sourcing-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-sourcing',
            'filterModel' => $sourcingSearchModel,
            'dataProvider' => $sourcingDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['PersonID'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id);
            },
                    'floatHeader' => true,
                    'columns' => [
                         [
                             'class' => 'yii\grid\CheckboxColumn',
                             'checkboxOptions' => [
                                 'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                                 'onchange'=>'return showAllNextActionButton(this);',
                             ],
                             'headerOptions' => [
                                 'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
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
                            'attribute' => 'countryIDRef',
//                            'value' => 'isoCode',
                            'value' => function($model) {
                                return isset($model->sourcing->country) ? $model->sourcing->country->isoCode : '';
                            },
                            'options' => ['width' => 40],
                            
                             'format' => 'raw',
                        //     'value' => function($model) {
                        //        return \yii\bootstrap\Html::img(Yii::$app->urlManager->createAbsoluteUrl(['obs/person/flag-image', 'countryID' => $model['countryIDRef']]), ['width' => '30px;', 'height' => '20px']);
                        //    },
                        ],
        
                        [
                            'attribute' => 'LOGO',
                            'format' => 'raw',
                            'label' => \Yii::t("obs", 'LOGO') ,
                            'options' => ['width' => 60],
                            'value' => function($model) {
                                return \yii\bootstrap\Html::img(Yii::$app->urlManager->createAbsoluteUrl(['obs/person/logo-image', 'PersonID' => $model->PersonID]), ['width' => '50px;', 'height' => '30px']);
                            },
                        ],
                        [
                            'attribute' => 'PersonID',
                            'value' => 'PersonID',
                            'options' => ['width' => 60],
                        ],
                        [
                            'attribute' => 'PersonTitle',
//                            'value' => 'PersonTitle',
                            'options' => ['width' => 300],
                            'value' => function($model) {
                                return $model->sourcing->PersonTitle;
                            },
                        ],                        
                        [
                            'attribute' => 'personType',
                            'format' => 'raw',
                            // 'filter' => Html::activeDropDownList(Person, 'personType', ['L'=>"Legal Person",'T'=>"True Person"], ['class' => 'form-control','style'=>'font-size:10px', 'prompt' => '']),
        //                    'value' => 'personType',
                            'value' => function($model) {
                                return ($model->sourcing->personType == "Legal Person") ? '<span class="badge  badge-purple" style="font-size:8px">Legal Person</span>' : '<span class="badge badge-brown" style="font-size:8px">True Person</span>';
                            },
                            'options' => ['width' => 70],
                        ],
                        [
                            'attribute' => 'registrationNumber',
//                            'value' => 'registrationNumber',
                            'options' => ['width' => 120],
                            'value' => function($model) {
                                return $model->sourcing->registrationNumber;
                            },
                        ],
                        [
                            'attribute' => 'economicCode',
//                            'value' => 'economicCode',
                            'options' => ['width' => 120],
                            'value' => function($model) {
                                return $model->sourcing->economicCode;
                            },
                        ],
                        [
                            'attribute' => 'LastActionDateHijri',
                            'options' => ['width' => 60],
                        ],
                        [
                            'attribute' => 'actionTitle',
                            'value' => function($model) {
                                return $model->lastAction->ActionTitle1;
                            },
                            'options' => ['width' => 60],
                        ],
                        [
                            'attribute' => 'nationalCode',
                            'format' => 'raw',                    
                            'value' => function($model) {
                                return ($model->sourcing->personType == "Legal Person") ? $model->sourcing->companyNationalCode : $model->sourcing->nationalCode;
                            },
                            'options' => ['width' => 120],
                        ],

                        [
                            'class' => yii\grid\ActionColumn::className(),
                            'header' => 'Actions',
                            'template' => '{nextaction}{diagram}{detail}{remove}',
                            'options' => [
                                'width' => '100px;',
                            ],
                            'buttons' => [
                                'nextaction' => function($url, $model, $key) {
                                    return (Yii::$app->request->get('type') == 'ToDo') ?
                                        Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['/workflow/next-action/next-action', 'type' => 'sourcing', 'peid' => $model->ProcessEntityID]), [
                                            'onclick' => "showModal(this); return false;",
                                            'data-pjax' => "0",
                                            'data-original-title' => 'Next Action',
                                            'data-toggle' => 'tooltip',
                                            'class' => 'nextAction'
                                        ]) : '';
                                },
                                'diagram' => function($url, $model, $key) {
                                    return Html::a('<i class="en-flow-branch s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['/workflow/workflow/graph', 'peid' => $model->ProcessEntityID]), [
                                                'data-original-title' => 'Action Diagram',
                                                'class' => "btn btn-xs tip",
                                                'data-pjax' => '0',
                                                'style' => 'padding:1px 2px;color:#46ADFF;',
                                                'target' => '_blank',
                                                'data-toggle' => 'tooltip'
                                    ]);
                                },                                                  
           
                                'detail' => function($url, $data, $key)  {                                    
                                    return Html::a('<i class="en-vcard s20"></i>', Yii::$app->urlManager->createAbsoluteUrl(['obs/person/view' , 'pid' => $data['PersonID']]), [
                                                'data-original-title' => 'Person Detail',
                                                'class' => 'btn btn-xs tip',
                                                'data-pjax' => '0',
                                                'style' => 'padding:1px 2px;color:#AE85BE;',
                                                'target' => '_blank',
                                                'data-toggle' => 'tooltip'
                                    ]);                                    
                                },

                                'remove' => function($url, $data, $key)  {
                                    return \yii\helpers\Html::a('<i class="fa-trash s16"></i>', '#', [
                                            'id' => 'delete-person-' . $data->PersonID,
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-original-title' => 'Remove Person',
                                            'onclick' => 'confirmBox("Are you sure to delete this item?!", "Delete!", "deletePerson(' . $data->PersonID . ')");',
                                            'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['obs/person/delete' , 'pid' => $data['PersonID']])
                                        ]
                                    );
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
                <script >

                    $('#sourcing-pjax').on('pjax:send', function () {
                        ajaxProgressLoader('show', '#sourcing-pjax');
                    });

                    $('#sourcing-pjax').on('pjax:complete', function () {
                        ajaxProgressLoader('hide', '#sourcing-pjax');
                    });

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
                            showPopupBox(data, 'sourcing Log', '', false);
                        }).error(function () {
                            showMsgBoxInBottomRight('Error in loading data', 'error');
                            return false;
                        });

                        return false;
                    }
                    function deletePerson(id) {
                        var url = $('#delete-person-' + id).attr('data-url');
                        $.ajax({
                            url: url,
                        }).done(function (data) {
                            if (data.result == 1) {
                                $.pjax.reload({
                                    container: '#sourcing-pjax',
                                    timeout: false,
                                    push: false,
                                    replace: false,
                                });
                                setTimeout("showMsgBoxInBottomRight('" + data.message + "', 'success')", 300);
                            } else {
                                setTimeout("showMsgBoxInBottomRight('" + data.message + "', 'error')", 300);
                            }
                        }).error(function () {
                            showMsgBoxInBottomRight("Error on connecting to server.", "error");
                            ajaxProgressLoader('hide', '.popup-box');
                            return false;
                        });
                        return false;
                    }
                </script>
                <?php
                Modal::begin([
                    'id' => 'sourcing-modal',
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
    
    $('#sourcing-pjax').on('pjax:complete', function () {
            dubleclick();
    });
    

        function showModal(obj){
            ajaxProgressLoader('show', '#sourcing-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                 $('#sourcing-modal').modal('show');
                 ajaxProgressLoader('hide', '#sourcing-pjax');
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
    
    
";
                $this->registerJs($js, yii\web\View::POS_END);
                Modal::end();
                ?>
<!----  All Next Action  ---->
<?php
Modal::begin([
    'id' => 'sourcing-all-modal',
    'size' => Modal::SIZE_LARGE,
]);
Pjax::begin([
    'id' => 'pjax-all-modal',
]);
$js = "
        function showAllModal(obj){
            ajaxProgressLoader('show', '#sourcing-pjax');
            
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
                $('#sourcing-all-modal').modal('show');
                ajaxProgressLoader('hide', '#sourcing-pjax');
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
                container:'#sourcing-pjax',
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
            $('#sourcing-all-modal').modal('hide');
        }
        else{
            swal('Error', data.message, 'error');
            setTimeout(function(){
                $('#sourcing-all-modal').modal('hide');
            }, 100);
        }
    }
    
    function showAllNextActionButton(){
        var checked = $('#grid-workflow-sourcing input[type=checkbox]:checked');
//        if(checked.length === 0 || (checked.length === 1 && checked.attr('name') === "selection_all")){
        if(checked.length < 2){
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

