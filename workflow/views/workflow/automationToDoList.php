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
/* Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function () {
  Yii::$app->view->registerAssetBundle(app\modules\tender\assets\InvoiceGridViewAsset::className());
  }); */
$this->title = "WORKFLOW :: Automation todo";

$this->params['breadcrumbs'] = [
    'label' => 'workflow'
];

$visible = ($automationType == 'automation') ? true : false;
$pid = $automationType == 'automation' ? 72 : ($automationType == 'secret' ? 73 : 86);
$createLabel = $pid == 72 ? 'New Automation' : ($pid == 73 ? 'New Access Level' : 'Retake Automation');
$createUrl = $pid == 86 ? Yii::$app->urlManager->createAbsoluteUrl(['automation/retake-automation/create']) : Yii::$app->urlManager->createAbsoluteUrl(['automation/automation/create', 'pid' => $pid]);
$canViewAccessLevel = \app\access\AccessControl::can("automation/access-level/list");
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => IM_USER,
]);
?>
<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php if ($canViewAccessLevel && $pid == 73) {
                echo Html::a("<i class='fa-list-ul'></i> &nbsp" . \Yii::t("automation", "Dashboard"), Yii::$app->urlManager->createAbsoluteUrl(['automation/access-level/list']), [
                        'class' => 'btn btn-sm btn-mini btn-gray',
                        'data-pjax' => '0',
                    ]
                );
            }
            $btns = $automationSearchModel->AUTOMATION_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?php echo ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?>  btn-sm tip mr5"
                   data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>"
                   href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/automation", 'type' => $btns[$i]['value'], 'automation' => $automationType]); ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btns[$i]['value']); ?>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            echo Html::a("<i class='fa-plus'></i> &nbsp" . \Yii::t("automation", $createLabel), $createUrl, [
//                'onclick' => "showProcessSelectModal(this); return false;",
                    'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                    'data-pjax' => '0',
                ]
            );
            ?>
        </div>

    </div>
    <div class="row">
        <?php Pjax::begin(['id' => 'automation-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'grid-workflow-automation',
            'filterModel' => $automationSearchModel,
            'dataProvider' => $automationDataProvider,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $id = $model['AutomationIDRef'];
                $class = $index % 2 ? 'odd' : 'even';
                return array('index' => $index, 'class' => $class . ' dblclick', 'id' => 'row-' . $id);
            },
            'floatHeader' => true,
            'columns' => [
                [
                    'attribute' => 'AutomationIDRef',
                    'label' => Yii::t('automation', 'AutomationID'),
                    'options' => [
                        'width' => '30px',
                    ],
                ],
                [
                    'attribute' => 'CompanyIDRef',
                    'label' => Yii::t('automation', 'Company'),
                    'value' => function ($model) {
                        return empty($model->automation->company) ? '' : $model->automation->company->companyName;
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $companyList,
                        'options' => ['placeholder' => 'انتخاب شرکت'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'width' => $visible ? '200px' : 'auto'
                        ]
                    ],
                ],
                [
                    'attribute' => 'Project',
                    'value' => function ($model) {
                        $project = isset($model->automation, $model->automation->position, $model->automation->position->project) ? $model->automation->position->project : null;
                        return $project ? $project->EPSTitle : '';
                    },
                    'label' => Yii::t('obs', 'setad/project'),
                    'options' => [
                        'width' => $visible ? '200px' : 'auto',
                    ],
                    'visible' => $visible
                ],
                [
                    'attribute' => 'ParentObs',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return Utility::generateTextShorterSpan(yii\helpers\Html::encode($model->automation->assistanceTitle), 30, 30);
                    },
                    'label' => Yii::t('automation', 'Parent Obs'),
                    'options' => [
                        'width' => '200px',
                    ],
                    'visible' => $visible
                ],
                [
                    'attribute' => 'ObsId',
                    'value' => function ($model) {
                        $position = $model->automation->position;
                        return isset($position) ? $position->obsId : '';
                    },
                    'label' => Yii::t('automation', 'ObsId'),
                    'options' => [
                        'width' => '70px',
                    ],
                    'visible' => $visible
                ],            
                [
                    'attribute' => 'Position',
                    'value' => function ($model) {
                        $position = $model->automation->position;
                        return isset($position) ? $position->fullTitle : '';
                    },
                    'label' => Yii::t('automation', 'Position'),
                    'options' => [
                        'width' => '200px',
                    ],
                    'visible' => $visible
                ],
                [
                    'attribute' => 'PositionStatus',
                    'label' => 'وضعیت',
                    'visible' => $pid != 86,
                    'value' => function ($model) {
                        $position = $model->automation->position;
                        return (isset($position) && isset($position->status)) ? app\modules\OBS\models\Obsnew::getStatus($position->status) : '';
                    },
                    'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => app\modules\OBS\models\Obsnew::getStatusList(),
                        'options' => ['placeholder' => 'وضعیت'],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ]
                    ],
                ],
                [
                    'attribute' => 'Position Count',
                    'value' => function ($model) {
                        $position = $model->automation->position;
                        return isset($position) ? ($position->totalPositionCount) : null;
                    },
                    'label' => Yii::t('automation', 'Position Count'),
                    'options' => [
                        'width' => '70px',
                    ],
                    'visible' => $visible
                ],
                [
                    'attribute' => 'parentFirstLevelPositionCount',
                    'value' => function ($model) {
                        $position = $model->automation->position;
                        return isset($position) ? ($position->parentFirstLevelPositionCount) : null;
                    },
                    'label' => Yii::t('automation', 'FirstLevelPositionCount'),
                    'options' => [
                        'width' => '70px',
                    ],
                    'visible' => $visible
                ],
                [
                    'attribute' => 'FullName',
                    'value' => function ($model) {
                        $humanResource = $model->automation->humanResource;
                        return isset($humanResource) ? ($humanResource->FirstName . ' ' . $humanResource->LastName) : '';
                    },
                    'label' => Yii::t('automation', 'FullName'),
                    'options' => [
                        'width' => $visible ? '150px' : 'auto',
                    ],
                ],
                [
                    'attribute' => 'NationalCode',
                    'value' => function ($model) {
                        $humanResource = $model->automation->humanResource;
                        return isset($humanResource) ? $humanResource->NationalCode : '';
                    },
                    'label' => Yii::t('automation', 'NationalCode'),
                    'options' => [
                        'width' => $visible ? '100px' : 'auto',
                    ],
                ],
                [
                    'attribute' => 'RequestDate',
                    'label' => Yii::t('workflow', 'Request Date'),
                    'value' => function ($model) {
                        return substr($model->automation->RequestDateHijri, 0, 10);
                    },
                    'options' => [
                        'width' => '100px',
                    ],
                ],
                [
                    'attribute' => 'ExpireRemain',
                    'label' => Yii::t('automation', 'Expire Remain (Days)'),
                    'value' => function ($model) {
                        return app\modules\automation\models\Automation::DaysToExpire($model->automation->RequestDate);
                    },
                    'options' => [
                        'width' => '100px',
                    ],
                ],
                [
                    'attribute' => 'lastActionTitle',
                    'label' => Yii::t('workflow', 'Current Action'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return isset($model->lastAction) ? Utility::generateTextShorterSpan($model->lastAction->ActionTitle2, 30, 30) : '';
                    },
                    'options' => [
                        'width' => $visible ? '100px' : 'auto',
                    ],
                ],
                [
                    'attribute' => 'LastActionDateHijri',
                    'label' => Yii::t('workflow', 'Last Action Date'),
                    'value' => 'LastActionDateHijri',
                    'options' => [
                        'width' => $visible ? '100px' : 'auto',
                    ],
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
                    'template' => '{nextaction}{diagram}{detail}{createform}',
                    'options' => [
                        'width' => '100px;',
                    ],
                    'buttons' => [
                        'nextaction' => function ($url, $model, $key) {
                            return (Yii::$app->request->get('type') == 'ToDo') ?
                                Html::a('<i class="im-arrow-right2 rotate-360 s16"></i>', Url::to(['/workflow/next-action/next-action', 'type' => 'automation', 'peid' => $model->ProcessEntityID]), [
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
                            $url = $model->ProcessIDRef == 86 ? '/automation/retake-automation/view' : '/automation/automation/view';
                            return Html::a('<i class="im-user s16"></i>', Url::to([$url, 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Invocie Detail',
                                'class' => 'btn btn-xs tip',
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#AE85BE;',
                                'target' => '_blank',
                                'data-toggle' => 'tooltip'
                            ]);
                        },
                        'createform' => function ($url, $model, $key) {
                            return Html::a('<i class="glyphicon glyphicon-equalizer s16"></i>', Url::to(['/automation/automation/create-form', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'Create Form',
                                'class' => "btn btn-xs tip",
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#46ADFF;',
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
    'id' => 'automation-modal',
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
    
    $('#automation-pjax').on('pjax:complete', function () {
            dubleclick();
    });
        function showModal(obj){
            ajaxProgressLoader('show', '#automation-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                $('#automation-modal').modal('show');
                ajaxProgressLoader('hide', '#automation-pjax');
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


