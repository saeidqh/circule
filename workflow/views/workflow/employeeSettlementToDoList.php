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

const IM_USER = 'im-user';
DatepickerAsset::register($this);
SweetAlertAsset::register($this);
JdateTimeAsset::register($this);
TenderAsset::register($this);

$this->title = "WORKFLOW :: Employee Settlement todo";

$this->params['breadcrumbs'] = [
    'label' => 'workflow'
];

$canUpdate = app\access\AccessControl::can('automation/employee-settlement/create');
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => IM_USER,
]);
?>
<style>
    #print-modal label {
        width: 100%;
    }
</style>
<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $employeeSettlementSearchModel->EMPLOYEESETTLEMENT_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5"
                   data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>"
                   href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/employee-settlement", 'type' => $btns[$i]['value']]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            if ($canUpdate) {
//            echo Html::a("<i class='fa-plus'></i> &nbsp" . \Yii::t("automation", 'New Employee Settlement'), Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/process-select','type'=>'employee-settlement']), [
                echo Html::a("<i class='fa-plus'></i> &nbsp" . \Yii::t("automation", 'New Employee Settlement'), Yii::$app->urlManager->createAbsoluteUrl(['automation/employee-settlement/create']), [
//                'onclick' => "showProcessSelectModal(this); return false;",
                    'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                    'data-pjax' => '0',
                        ]
                );
            }
            ?>
        </div>

    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'employee-settlement-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-employee-settlement',
            'filterModel' => $employeeSettlementSearchModel,
            'dataProvider' => $employeeSettlementDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['AutomationIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id);
            },
            'floatHeader' => true,
            'columns' => [
                [
                    'attribute' => 'ObjectIDRef',
                    'label' => Yii::t('automation', 'EmployeeSettlementID'),
                    'options' => [
                        'width' => '70px',
                    ],
                ],
                [
                    'attribute' => 'CreatedCompanyIDRef',
                    'label' => Yii::t('automation', 'Created Company'),
                    'value' => function ($model) {
                        return $model->employeeSettlement->createdCompany->shortTitle;
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $companyList,
                        'options' => ['placeholder' => 'مرجع صدور'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '100px'
                        ]
                    ],
                ],
                [
                    'attribute' => 'CompanyIDRef',
                    'label' => Yii::t('automation', 'Company'),
                    'value' => function ($model) {
                        return empty($model->employeeSettlement->company) ? '' : $model->employeeSettlement->company->companyName;
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $personList,
                        'options' => ['placeholder' => 'انتخاب شرکت'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => '200px'
                        ]
                    ],
                ],
                [
                    'attribute' => 'Assistance',
                    'value' => function ($model) {
                        // $assistance = $model->employeeSettlement->assistance;
                        // return (isset($assistance->positionType) ? $assistance->positionType->Title . ' ' : '') . $assistance->obsTitle;
                       return $model->employeeSettlement->assistance->fullTitle;
                    },
                    'label' => Yii::t('automation', 'Assistance OBS'),
                ],
                [
                    'attribute' => 'Position',
                    'value' => function ($model) {
                        return $model->employeeSettlement->position->fullTitle;
                    },
                    'label' => Yii::t('automation', 'Organization Position'),
                ],
                [
                    'attribute' => 'FullName',
                    'value' => function ($model) {
                        return $model->employeeSettlement->humanResource->fullName;
                    },
                    'label' => Yii::t('automation', 'FullName'),
                ],
                [
                    'attribute' => 'NationalCode',
                    'value' => function ($model) {
                        return $model->employeeSettlement->humanResource->NationalCode;
                    },
                    'label' => Yii::t('automation', 'NationalCode'),
                ],
                [
                    'attribute' => 'Mobile',
                    'value' => function ($model) {
                        return $model->employeeSettlement->humanResource->Mobile;
                    },
                    'label' => Yii::t('automation', 'Mobile'),
                ],
                [
                    'attribute' => 'IssueDate',
                    'value' => function ($model) {
                        return $model->employeeSettlement->IssueDate;
                    },
                    'label' => Yii::t('automation', 'Issue Date'),
                ],
                [
                    'attribute' => 'lastActionTitle',
                    'label' => Yii::t('workflow', 'Last Action'),
//                    'value' => 'lastActionTitle',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Utility::generateTextShorterSpan(yii\helpers\Html::encode($model->lastActionTitle), 30, 30);
                    },
                    'options' => [
                        'width' => '150px',
                    ],
                ],
                /* [
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
                  ], */
                [
                    'class' => yii\grid\ActionColumn::className(),
                    'header' => Yii::t('workflow', 'Actions'),
                    'template' => '{nextaction}{diagram}{detail}{createform}{delete}',
                    'options' => [
                        'width' => '100px;',
                    ],
                    'buttons' => [
                        'nextaction' => function ($url, $model, $key) {
                            return (Yii::$app->request->get('type') == 'ToDo') ?
                            Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'employee-settlement', 'peid' => $model->ProcessEntityID]), [
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
                            return Html::a('<i class="im-user s16"></i>', Url::to(['/automation/employee-settlement/view', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Invocie Detail',
                                'class' => 'btn btn-xs tip',
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#AE85BE;',
                                'target' => '_blank',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'createform' => function ($url, $model, $key) {
                            return Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', '#', [
                                'onclick' => "showPrintModal(this); return false;",
                                'data-original-title' => 'Create Form',
                                'class' => "btn btn-xs tip",
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#46ADFF;',
//                                'target' => '_blank',
                                'data-toggle' => 'tooltip',
                                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['/automation/employee-settlement/create-form', 'peid' => $model->ProcessEntityID])
                            ]);
                        },
                                
                        'delete' => function ($key, $model, $index) {
                            $canProcessDelete = app\access\AccessControl::can('automation/employee-settlement/delete', ['peid' => $model->ProcessEntityID]);
                            if ($canProcessDelete) {
                                return Html::a('<i class="fa-trash s16"></i>', '#', [
                                    'class' => 'delete-process-' . $model->ProcessEntityID,
                                    'data-toggle' => 'tooltip',
                                    'data-placement' => 'top',
                                    'data-original-title' => 'Remove Process item',
                                    'onclick' => 'confirmBox("Are you sure to delete this item?!", "Delete!", "deleteProcessEntity(\'' . $model->ProcessEntityID . '\')");',
                                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['automation/employee-settlement/delete', 'peid' => $model->ProcessEntityID])
                                            ]
                                        ) . '&nbsp;';
                            }
                        },    
                                
                                
                           
//                          'delete'=> function ($url, $model, $key) {
//                            return Html::a('<i class="fa-trash s16"></i>', Url::to(['/automation/employee-settlement/delete', 'peid' => $model->ProcessEntityID]), [
//                                'data-pjax' => '0',
//                                'data-original-title' => 'Delete',
//                                //'data-confirm'=>"آیا مطمئن هستید که می خواهید این فرم را حذف کنید؟",
//                                'class' => 'btn btn-xs tip',
//                                'onclick' => 'confirmBox("Are you sure to delete this item?!", "Delete!", "deleteProcessEntity(\'' . $model->ProcessEntityID . '\')");',
//                                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/employee-settlement&type=ToDo', 'ProcessEntityID' => $model->ProcessEntityID]),
//                                'data-pjax' => '0',
//                                'style' => 'padding:1px 2px;color:#AE85BE;',
//                                'data-methode'=>'post',
//                                     ]);
//                        },
                                
                                
                                
//                                'delete' => function ($key, $model, $index)  {
//                                     
//                                    return Html::a('<i class="fa-trash s16"></i>', '#', [
//                                    'class' => 'delete-processEntity-' . $model->ProcessEntityID,
//                                    'data-toggle' => 'tooltip',
//                                    'data-placement' => 'top',
//                                    'data-original-title' => 'Remove ProcessEntity item',
//                                    'onclick' => 'confirmBox("Are you sure to delete this item?!", "Delete!", "deleteProcessEntity(\'' . $model->ProcessEntityID . '\')");',
//                                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['/automation/employee-settlement/delete', 'ProcessEntityID' => $model->ProcessEntityID])
//                                        ]
//                                ) . '&nbsp;';
//                    
//                },    
//                                
                                
                                
                                
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
    'id' => 'employee-settlement-modal',
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
    
    $('#employee-settlement-pjax').on('pjax:complete', function () {
            dubleclick();
    });
        function showModal(obj){
            ajaxProgressLoader('show', '#employee-settlement-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#employee-settlement-modal').modal('show');
                ajaxProgressLoader('hide', '#employee-settlement-pjax');
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

$printType = [1 => 'نمایش و پرینت فرم تسویه با نوع یک', 2 => 'نمایش و پرینت فرم تسویه با نوع دو'];
Modal::begin([
    'id' => 'print-modal',
    'header' => '<h4 class="modal-title">Select Print Type</h4>'
]);
echo Html::radioList('PrintType', 2, $printType);
?>

<button type=button class="btn btn-sm btn-success" id="btn-create-process-entirt" onClick="printForm(this)"><i class="fa-plus"></i> Create</button>
<button type=button class="btn btn-sm btn-warning" data-dismiss=modal><i class="br-cancel"></i> Close</button>

<?php
Modal::end();
?>

<script>
    var printFormUrl = null;
    function showPrintModal(obj) {
        printFormUrl = $(obj).attr('data-url');
        $('#print-modal').modal('show');
        return false;
    }

    function printForm() {
        var type = null;
        $('input:radio[name=PrintType]:checked').each(function () {
            type = $(this).val();
        });
        var url = printFormUrl + '&type=' + type;
        window.open(url, '_blank');
    }
    
    function deleteProcessEntity(ProcessEntityID) {
        var id = $('.delete-process-' + ProcessEntityID).attr('data-id');
        var url = $('.delete-process-' + ProcessEntityID).attr('data-url');
        $.ajax({
            url: url,
        }).done(function (data) {
            if (data.result == 1) {
                $.pjax.reload({
                    container: '#grid-workflow-employee-settlement',
                    timeout: false,
                    push: false,
                    replace: false,
                });
                setTimeout(function () {
                    showMsgBoxInBottomRight(data.message, 'success');
                }, 1000)


            }
            return false;
        })
    }
    
    
    
    
    
    
</script>
