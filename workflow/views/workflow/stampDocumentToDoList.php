<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\Utility;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use omnilight\assets\SweetAlertAsset;
use kartik\grid\GridView;
use kartik\select2\Select2;

use app\modules\workflow\models\search\ProcessEntityStampDocumentSearch;

const IM_USER = 'im-user';

DatepickerAsset::register($this);
SweetAlertAsset::register($this);
JdateTimeAsset::register($this);

$this->title = "فرآیند نشان سازمانی";
$this->params['breadcrumbs'] = [
    'label' => 'گردش کار',
    'label' => 'اسناد نشان سازمانی',
];
$canCreateStampDocument = \app\access\AccessControl::isAccess([
    'processAction',
    'aid' => 1172,
    'errorException' => false,
]);
$canStampExport = \app\access\AccessControl::can("document/export/stamp");
$canVerifiedStamp = \app\access\AccessControl::can("document/stamp-document/verified", []);
$isCandoAdmin = \app\components\Security::isCandoAdmin();
$userCompanyId = yii::$app->session->get('CompanyID');
?>

<?= PageHeader::widget([
    'title' => $this->title,
    'icon' => IM_USER,
]); ?>

<style>
    #print-modal label { width: 100%; }
    .inner-table { width: 100%; }
    .inner-table td { padding: 2px; border: none; }
    .table td { vertical-align: middle !important; }
    .table th { white-space: nowrap; }
    .kv-grid-table td { 
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .kv-grid-table td:hover {
        white-space: normal;
        word-wrap: break-word;
    }
    .btn-xs { padding: 2px 4px !important; font-size: 12px !important; }
</style>

<div class="row">
    <?php GridViewBox::begin(); ?>
    <div class="row">
        <div class="col-md-4 pull-right text-right mb5">
            <?php
                         
            // ✅ دکمه جدید برای رفتن به مدیریت نشان

echo Html::a(
    '<i class="fa fa-arrow-left" style="margin-left:5px;"></i> ' .Yii::t("document", 'مدیریت نشان سازمانی'),
    Url::to(['/document/stamp-document/list', 'type' => 'ToDo']),
    [
        'class' => 'btn btn-sm btn-warning pull-left ml3',
'style' => '
    background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
    border: none;
    border-radius: 30px;
    padding: 6px 22px;
    font-weight: bold;
    color: #2d2d2d;
    box-shadow: 0 4px 20px rgba(255, 210, 0, 0.5);
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
',
'onmouseover' => 'this.style.boxShadow="0 6px 35px rgba(255, 210, 0, 0.8)"; this.style.transform="scale(1.06)";',
'onmouseout' => 'this.style.boxShadow="0 4px 20px rgba(255, 210, 0, 0.5)"; this.style.transform="scale(1)";',
        'data-toggle' => 'tooltip',
        'data-placement' => 'top',
        'title' => Yii::t("document", 'مدیریت نشان سازمانی ')
    ]
);
            $btns = $stampDocumentSearchModel->STAMP_DOCUMENT_TODO_REPORT_TYPE;
            foreach ($btns as $btn) {
                ?>
                <a class="btn <?php echo ($type == $btn['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5"
                   data-toggle="tooltip" data-placement="top" title="<?php echo $btn['value']; ?>"
                   href="<?= Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/stamp-document", 'type' => $btn['value']]); ?>">
                    <i class="<?php echo $btn['icon']; ?> s16"></i> <?php echo Yii::t('workflow', $btn['name']); ?>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="col-md-6 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <?php   
            if ($canCreateStampDocument) {
                echo Html::button(
                    "<i class='fa-plus'></i> " . Yii::t("document", 'Request Stamp Document'),
                    [
                        'class' => 'btn btn-sm btn-success',
                        'data-url' => Url::to(['/document/stamp-document/create-workflow']),
                        'onclick' => 'showCreateStampModal(this)',
                        'style' => 'margin-left:5px;'
                    ]
                );
            }

            ?>
        </div>
    </div>
<?php
//echo '<pre>';
//print_r($stampDocumentSearchModel->getStatusList());
//echo '</pre>';
//die;
?>
    <div class="row">
        <?php Pjax::begin(['id' => 'stamp-document-pjax']); ?>
 <?=
        
        GridView::widget([
            'id' => 'grid-workflow-stamp-document',
            'filterModel' => $stampDocumentSearchModel,
            'dataProvider' => $stampDocumentDataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
    $id = $model->stampDocument ? $model->stampDocument->StampId : $model['ObjectIDRef'];
    $class = $index % 2 ? 'odd' : 'even';

    return [
        'index' => $index,
        'class' => $class . ' stamp-view-row',
        'id' => 'row-' . $id,
        'data-url' => Url::to([
            '/document/stamp-document/view',
            'sid' => $id,
            'readonly' => 1
        ]),
        'style' => 'cursor:pointer;',
        'title' => 'برای مشاهده جزئیات کلیک کنید',
    ];
},  
            'floatHeader' => true,
            'containerOptions' => ['style' => 'overflow-x: auto;'],
            'responsive' => true,
            'hover' => true,
            'striped' => true,
            'bordered' => true,
            'columns' => [
                // ستون 1: شناسه سند
               [
                    'attribute' => 'StampId',
                    'label' => Yii::t('document', 'ID'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->StampId : null;
                    },
                    'width' => '60px',
                ],
             //   [
//    'attribute' => 'StampId',
//    'label' => Yii::t('document', 'ID'),
//    'value' => function ($model) {
//        // دیباگ
//        if (!$model->stampDocument) {
//            return 'NULL';
//        }
//        return $model->stampDocument->StampId;
//    },
//    'width' => '60px',
//],
                // ستون 2: لوگو
[
    'format' => 'raw',
    'label' => Yii::t("document", 'Logo'),
    'width' => '60px',
    'value' => function ($model) {
        if (!$model->stampDocument) {
            return '';
        }

        $stampDocument = $model->stampDocument;

        $imageUrl = !empty($stampDocument->FinalStampDocument)
            ? $stampDocument->getFinalStampDocumentUrl()
            : Yii::$app->urlManager->createAbsoluteUrl([
                'document/stamp-document/logo-image',
                'id' => $stampDocument->StampId
            ]);

        return Html::img($imageUrl, [
            'style' => 'border-radius:5px',
            'width' => '35px',
            'height' => '30px'
        ]);
    },
],
                // ستون 3: عنوان سند
                [
                    'attribute' => 'StampTitle',
                    'label' => Yii::t("document", 'Title'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->StampTitle : null;
                    },
                    'width' => '180px',
                ],
                // ستون 4: شرکت
//        [
//            'attribute' => 'Company',
//            'label' => Yii::t('document', 'Company'),
//            'value' => function ($model) {
//                return empty($model->obs->company) ? '' : $model->obs->company->companyName;
//            },
//            'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
//            'filterWidgetOptions' => [
//                'data' => $companyList,
//                'options' => ['placeholder' => 'انتخاب شرکت'],
//                'pluginOptions' => [
//                    'allowClear' => true,
//                    'width' => '200px'
//                ]
//            ],
//        ],
//        
[
    'attribute' => 'CompanyTitle',
    'label' => Yii::t('document', 'Company'),
   'value' => function ($model) {
        if (!$model->stampDocument) return '';
        return isset($model->stampDocument->obs->company) ? $model->stampDocument->obs->company->PersonTitle : '';
    },
    'width' => '150px',
    'filter' => $personList,  // ✅ لیست شرکت‌ها (PersonID => PersonTitle)
    'filterType' => GridView::FILTER_SELECT2,
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
    'attribute' => 'ParentObs',
    'format' => 'raw',
    'value' => function ($model) {
        $title = '';

        if (!empty($model->stampDocument)) {
            $title = $model->stampDocument->assistanceTitle;
        }

        return \app\components\Utility::generateTextShorterSpan(
            \yii\helpers\Html::encode($title),
            30,
            30
        );
    },
    'label' => Yii::t('automation', 'Parent Obs'),
    'options' => [
        'width' => '200px',
    ],
],
         //        ستون 5: موقعیت سازمانی
                [
                    'attribute' => 'PositionTitle',
                    'label' => Yii::t("document", 'Position'),
                    'value' => function ($model) {
                        if (!$model->stampDocument) return '';
                        return !empty($model->stampDocument->ObsIDRef) ? $model->stampDocument->obs->fullTitle : null;
                    },
                    'width' => '150px',
                ],
                // ستون 6: سازنده نشان
                [
                    'attribute' => 'OrganizationalMaker',
                    'label' => Yii::t('document', 'گردش'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->getOrganizationalMakerListLabel() : '---';
                    },
                    'width' => '90px',
                    'filter' => $stampDocumentSearchModel::getOrganizationalMakerList(),
                ],
                            // ستون 12: صاحب امضا/نام
//[
//    'attribute' => 'NationalCode',
//    'label' => 'صاحب امضا/نام',
//    'format' => 'raw',
//    'value' => function ($model) {
//        if (!$model->stampDocument) return '';
//        $stampDocument = $model->stampDocument;
//        if (empty($stampDocument->HumanResourceIDRef)) return '';
//        $humanResource = $stampDocument->humanResource;
//        if (!$humanResource) return '';
//        return '<table class="inner-table">'
//            . '<tr><td width="150px">' . Html::encode($humanResource->fullName) . '<tr>'
//            . '<td><td>' . Html::encode($humanResource->NationalCode) . '</td>'
//            . '</table>';
//    },
//    'width' => '180px',
//],
// ستون 13: بهره بردار(Last Holder)
//[
//    'attribute' => 'LastFlowIDRef',
//    'label' => Yii::t('document', 'Last Holder'),
//    'format' => 'raw',
//    'value' => function ($model) {
//        if (!$model->stampDocument) return '';
//        $stampDocument = $model->stampDocument;
//        if (!$stampDocument->lastFlow) return '';
//        $lastFlow = $stampDocument->lastFlow;
//        if (!$lastFlow->human) return '';
//        $human = $lastFlow->human;
//        return '<table class="inner-table">'
//            . '<td><td width="150px">' . Html::encode($human->fullName) . '</td>'
//            . '<tr><td>' . Html::encode($human->NationalCode) . '</td>'
//            . '</table>';
//    },
//    'width' => '180px',
//],
// ستون 14: موقعیت سازمانی والد (Parent Obs)
//[
//    'attribute' => 'ParentObs',
//    'label' => Yii::t('automation', 'Parent Obs'),
//    'format' => 'raw',
//    'value' => function ($model) {
//        if (!$model->stampDocument) return '';
//        return Utility::generateTextShorterSpan(Html::encode($model->stampDocument->assistanceTitle), 30, 30);
//    },
//    'width' => '150px',
//],
                // ستون 7: نوع سند
                [
                    'attribute' => 'StampTypeIDRef',
                    'label' => Yii::t("document", 'نوع مدرک'),
                    'value' => function ($model) {
                        return $model->stampDocument && $model->stampDocument->stampType ? $model->stampDocument->stampType->TypeName : null;
                    },
                    'width' => '100px',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => \yii\helpers\ArrayHelper::map(\app\modules\document\models\StampTypeDocument::find()->all(), 'StampTypeId', 'TypeName'),
                        'options' => ['placeholder' => 'نوع'],
                        'pluginOptions' => ['allowClear' => true, 'width' => '100px']
                    ],
                ],
                // ستون 8: وضعیت سند
                [
                    'attribute' => 'Status',
                    'label' => Yii::t("document", 'Status'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->getStatusLabal() : '---';
                    },
                    'width' => '90px',
                    'filter' => $stampDocumentSearchModel::getStatusList(),
                ],
                // ستون 9: مرحله جاری
//                [
//                    'attribute' => 'lastActionTitle',
//                    'label' => Yii::t('workflow', 'Step'),
//                    'value' => function ($model) {
//                        return isset($model->lastAction) ? Utility::generateTextShorterSpan($model->lastAction->ActionTitle2, 20, 35) : '---';
//                    },
//                    'width' => '110px',
//                ],
          
                // ستون 10: آخرین اقدام کننده
                [
                    'attribute' => 'LastUserName',
                    'label' => Yii::t('workflow', 'Last User'),
                    'value' => function ($model) {
                        return isset($model->lastUser) ? $model->lastUser->fullName : '---';
                    },
                    'width' => '120px',
                ],
                // ستون 11: تاریخ ایجاد

                            // ستون 9: مرحله جاری
[
    'attribute' => 'lastActionTitle',
    'label' => Yii::t('workflow', 'مرحله جاری'),
    'value' => function ($model) {
        return isset($model->lastAction) ? $model->lastAction->ActionTitle2 : '---';
    },
    'width' => '150px',
],    
                      [
                    'attribute' => 'LastActionDateHijri',
                    'label' => Yii::t('workflow', 'Last Action Date'),
                    'value' => 'LastActionDateHijri',
                ],
                // ستون 12: عملیات
                [
                    'class' => kartik\grid\ActionColumn::className(),
                    'header' => Yii::t('workflow', 'Actions'),
                    'template' => '{nextaction}{summary}{verified}{delete}',
                    'width' => '100px',
                    'buttons' => [
                        'nextaction' => function ($url, $model, $key) {
                            if (Yii::$app->request->get('type') == 'ToDo' && isset($model->Status) && $model->Status == 1) {
                                return Html::a('<i class="im-arrow-right2 rotate-360" style="font-size:16px;"></i>', 
                                    Url::to(['/workflow/next-action/next-action', 'type' => 'stamp-document', 'peid' => $model->ProcessEntityID]), [
                                    'onclick' => "showModal(this); return false;",
                                    'data-pjax' => "0",
                                    'data-original-title' => 'اقدام بعدی',
                                    'data-toggle' => 'tooltip',
                                    'class' => 'btn btn-xs btn-success',
                                    'style' => 'margin:0 2px;'
                                ]);
                            }
                            return '';
                        },
                        'summary' => function ($url, $model, $key) {
                            return Html::a('<i class="glyphicon glyphicon-list-alt s16"></i>', 
                                Url::to(['/workflow/workflow/stamp-summary', 'peid' => $model->ProcessEntityID]), [
                                'data-original-title' => 'خلاصه اقدامات',
                                'class' => 'btn btn-xs tip',
                                'data-pjax' => '0',
                                'style' => 'padding:1px 2px;color:#FF9800;',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'showStampSummaryModal(this); return false;',
                            ]);
                        },
'delete' => function ($url, $model, $key) {
    // فقط بررسی دسترسی (اگر دسترسی نداشت، دکمه نمایش داده نشود)
    if (!\app\access\AccessControl::can('document/stamp-document/delete')) {
        return '';
    }
    
    // ✅ دکمه برای همه رکوردها نمایش داده می‌شود (حتی تایید شده و رد شده)
    return Html::a(
        '<i class="fa-trash s16"></i>',
        '#',
        [
            'class' => 'btn btn-xs btn-danger tip',
            'data-toggle' => 'tooltip',
            'data-placement' => 'top',
            'data-original-title' => 'حذف سند',
            'onclick' => "
                if (confirm('آیا از حذف این سند و تمام اطلاعات مرتبط مطمئن هستید؟')) {
                    ajaxProgressLoader('show', '#stamp-grid-pjax');
                    $.ajax({
                        url: '" . Url::to(['/document/stamp-document/delete', 'id' => $model->StampIDRef]) . "',
                        type: 'POST',
                        dataType: 'json',
                        success: function(data) {
                            if (data.result) {
                                swal('موفق', data.message, 'success');
                                reloadForm();
                            } else {
                                swal('خطا', data.message, 'error');
                            }
                            ajaxProgressLoader('hide', '#stamp-grid-pjax');
                        },
                        error: function() {
                            ajaxProgressLoader('hide', '#stamp-grid-pjax');
                            showMsgBoxInBottomRight('خطا در ارتباط با سرور', 'error');
                        }
                    });
                }
                return false;
            "
        ]
    );
},
//                        'detail' => function ($url, $model, $key) {
//                            return Html::a('<i class="ec-search" style="font-size:16px;"></i>', 
//                                Url::to(['/automation/stamp-document/view2', 'peid' => $model->ProcessEntityID]), [
//                                'data-original-title' => 'جزئیات',
//                                'class' => 'btn btn-xs btn-info',
//                                'data-pjax' => '0',
//                                'style' => 'margin:0 2px;',
//                                'target' => '_blank',
//                                'data-toggle' => 'tooltip'
//                            ]);
//                        },
//                        'verified' => function ($key, $model, $index) use ($canVerifiedStamp, $isCandoAdmin, $userCompanyId) {
//                            if (!$model->stampDocument) return '';
//                            if ($isCandoAdmin || ($canVerifiedStamp && $model->stampDocument->Status != 'destroyed' && ($userCompanyId == 248 || (int)$model->stampDocument->OrganizationalMaker === 2))) {
//                                $iconClass = $model->stampDocument->Status == 'approved' ? 'im-checkbox-checked' : 'im-checkbox-unchecked';
//                                return Html::a('<i class="' . $iconClass . '" style="font-size:16px;"></i>', '#', [
//                                    'data-pjax' => '0',
//                                    'title' => 'تایید',
//                                    'data-toggle' => 'tooltip',
//                                    'class' => 'btn btn-xs btn-warning',
//                                    'data-url' => Yii::$app->urlManager->createAbsoluteUrl(["document/stamp-document/verified", 'Id' => $model->stampDocument->StampId]),
//                                    'onclick' => 'confirmBox("Are you sure?!", "Yes!", "changeVerified(' . $model->stampDocument->StampId . ')");',
//                                    'style' => 'margin:0 2px;'
//                                ]);
//                            }
//                            return '';
//                        },
                    ]
                ]
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
    <?php GridViewBox::end(); ?>
</div>

<?php \app\components\PopupBox::begin([]); ?>
<?php \app\components\PopupBox::end([]); ?>

<!-- مودال اقدام بعدی -->
<?php
Modal::begin([
    'id' => 'stamp-document-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> اقدام بعدی</h4>',
    'options' => ['class' => 'fade modal in animated pulse']
]);
Pjax::begin(['id' => 'pjax-modal']);
$js = "
    dubleclick();
    function dubleclick(){
        $('.dblclick').attr('data-toggle', 'tooltip');
        $('.dblclick').attr('data-placement', 'left');
        $('.dblclick').attr('data-original-title', 'برای انتخاب یک آیتم و اقدام بعدی، دوبار کلیک کنید');
        $('.dblclick').dblclick(function(){
            $(this).addClass('success');
            $(this).find('.nextAction').trigger('click');
        });
    }
    
    $('#stamp-document-pjax').on('pjax:complete', function () {
        dubleclick();
    });
    
    function showModal(obj){
        ajaxProgressLoader('show', '#stamp-document-pjax');
        var url = $(obj).attr('href');
        $.ajax({
            url: url
        })
        .done(function(data){
            $('#pjax-modal').html(data);
            $('#stamp-document-modal').modal('show');
            ajaxProgressLoader('hide', '#stamp-document-pjax');
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

<!-- مودال انتخاب فرایند -->
<?php
Modal::begin([
    'id' => 'process-select-modal',
    'header' => '<h4 class="modal-title">انتخاب فرایند</h4>'
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

<!-- مودال ایجاد سند مهر جدید -->
<?php
Modal::begin([
    'id' => 'stamp-create-modal',
    'header' => '<h4 class="modal-title"><i class="fa-plus"></i> ' . Yii::t('document', 'Create New Stamp Document') . '</h4>',
    'size' => Modal::SIZE_LARGE,
    'options' => ['class' => 'fade modal in animated pulse']
]);
Pjax::begin(['id' => 'stamp-create-pjax']);
Pjax::end();
Modal::end();
?>

<!-- مودال ویرایش سند مهر -->
<?php
Modal::begin([
    'id' => 'stamp-modal',
    'header' => Yii::t('document', 'create or edit Stamp'),
    'size' => Modal::SIZE_LARGE,
]);
Pjax::begin(['id' => 'stamp-form-pjax']);
Pjax::end();
Modal::end();
?>

<script>
    function showStampSummaryModal(obj){
    ajaxProgressLoader('show', '#stamp-grid-pjax');
    var url = $(obj).attr('href');
    $.ajax({
        url: url,
        type: 'GET',
        success: function(data){
            if($('#stamp-summary-modal').length === 0){
                $('body').append('<div class="modal fade" id="stamp-summary-modal" tabindex="-1" role="dialog"><div class="modal-dialog modal-lg" role="document"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">خلاصه اقدامات</h4></div><div class="modal-body"></div></div></div></div>');
            }
            $('#stamp-summary-modal .modal-body').html(data);
            $('#stamp-summary-modal').modal('show');
            ajaxProgressLoader('hide', '#stamp-grid-pjax');
        },
        error: function(){
            ajaxProgressLoader('hide', '#stamp-grid-pjax');
            showMsgBoxInBottomRight('خطا در بارگذاری اطلاعات!', 'error');
        }
    });
    return false;
}
    function showCreateStampModal(obj){
        ajaxProgressLoader('show', '.modal-content');
        var url = $(obj).data('url');
        $.ajax({
            url: url,
            type: 'GET'
        })
        .done(function(data){
            $('#stamp-create-pjax').html(data);
            $('#stamp-create-modal').modal('show');
            ajaxProgressLoader('hide', '.modal-content');
        })
        .fail(function(jqXHR, textStatus, errorThrown){
            console.error("Error loading create form:", textStatus, errorThrown);
            showMsgBoxInBottomRight(Yii.t('app', 'Error loading form.'), 'error');
            ajaxProgressLoader('hide', '.modal-content');
        });
        return false;
    }

    function showStampModal(obj) {
        ajaxProgressLoader('show', '.modal-content');
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function (data) {
            $('#stamp-form-pjax').html(data);
            $('#stamp-modal').modal('show');
            ajaxProgressLoader('hide', '.modal-content');
        }).error(function () {
            showMsgBoxInBottomRight('Stamp in not Unknown. you cannot Edit it', 'error');
            return false;
        });
    }

    function changeVerified(Id) {
        ajaxProgressLoader('show', '.obs-list .row');
        var url = '<?= Yii::$app->urlManager->createAbsoluteUrl(['document/stamp-document/verified']) ?>' + "&Id=" + Id;
        $.ajax({
            url: url,
            dataType: "json"
        }).done(function (data) {
            if (data.result == 1) {
                swal('success', data.message, 'success');
                reloadForm();
            } else {
                swal('Error', data.message, 'error');
            }
        }).error(function () {
            showMsgBoxInBottomRight('Error on connecting to server. <br />Plase login again and try later!', 'error');
            return false;
        });
        ajaxProgressLoader('hide', '.obs-list .row');
        return false;
    }

    function reloadForm() {
        $.pjax.reload({
            container: '#stamp-document-pjax',
            timeout: false,
            push: false,
            replace: false,
        });
    }
    $(document).on('click', '.stamp-view-row', function(e) {
    if ($(e.target).closest('a, button, input, select, textarea, .select2-container').length) {
        return true;
    }

    var url = $(this).data('url');

    if (url) {
        window.open(url, '_blank');
    }
});
</script>   