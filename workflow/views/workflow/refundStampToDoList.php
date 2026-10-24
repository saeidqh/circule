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

const IM_USER = 'im-user';

DatepickerAsset::register($this);
SweetAlertAsset::register($this);
JdateTimeAsset::register($this);

$this->title = "فرآیند امحا نشان سازمانی";
$this->params['breadcrumbs'] = [
    'label' => 'گردش کار',
    'label' => 'امحا نشان سازمانی',
];

$canCreateRefund = \app\access\AccessControl::isAccess([
    'processAction',
    'aid' => 1195,
    'errorException' => false,
]);
$userCompanyId = Yii::$app->session->get('CompanyID');
$isCandoAdmin  = \app\components\Security::isCandoAdmin();
?>

<?= PageHeader::widget([
    'title' => $this->title,
    'icon'  => IM_USER,
]); ?>

<style>
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
    #refund-workflow-guide-modal .modal-dialog {
    width: 90%;
    max-width: 1100px;
}
#refund-workflow-guide-modal .modal-body {
    max-height: 80vh;
    overflow-y: auto;
    text-align: center;
}
</style>

<div class="row">
    <?php GridViewBox::begin(); ?>

    <div class="row">
        <!-- سمت راست: دکمه برگشت + تب‌ها -->
        <div class="col-md-5 pull-right text-right mb5">
            <?= Html::a(
                '<i class="fa fa-arrow-left" style="margin-left:5px;"></i> ' . Yii::t("document", 'مدیریت نشان سازمانی'),
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
                    ',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'top',
                    'title' => Yii::t("document", 'مدیریت نشان سازمانی')
                ]
            ); ?>

            <?php
            $btns = $refundSearchModel->REFUND_STAMP_TODO_REPORT_TYPE;
            foreach ($btns as $btn) {
                $activeClass = ($type == $btn['value']) ? 'btn-active' : 'btn-gray';
                echo Html::a(
                    '<i class="' . $btn['icon'] . ' s16"></i> ' . Yii::t('workflow', $btn['name']),
                    Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/refund-stamp', 'type' => $btn['value']]),
                    [
                        'class' => "btn {$activeClass} btn-sm tip mr5",
                        'data-toggle' => 'tooltip',
                        'data-placement' => 'top',
                        'title' => $btn['value'],
                    ]
                );
            }
            ?>
        </div>

        <!-- سمت چپ: دکمه درخواست امحا -->
 <!-- سمت چپ: دکمه راهنما + درخواست امحا -->
<div class="col-md-5 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">

    <!-- دکمه راهنمای گردش کار -->
    <?= Html::button(
        '<i class="fa fa-sitemap"></i> ' . Yii::t('workflow', 'راهنمای گردش کار'),
        [
            'class' => 'btn btn-sm btn-info',
            'style' => 'margin-left:5px;',
            'data-toggle' => 'modal',
            'data-target' => '#refund-workflow-guide-modal',
            'title' => 'راهنمای گردش کار امحا نشان سازمانی',
        ]
    ); ?>

    <?php if ($canCreateRefund) { ?>
        <?= Html::button(
            "<i class='fa-plus'></i> " . Yii::t("document", 'درخواست امحاء نشان سازمانی'),
            [
                'class' => 'btn btn-sm btn-success',
                'data-url' => Url::to(['/document/stamp-document/create-refund']),
                'onclick' => 'showCreateRefundModal(this)',
                'style' => 'margin-left:5px;'
            ]
        ); ?>
    <?php } ?>
</div>
    </div>

    <div class="row">
        <?php Pjax::begin(['id' => 'refund-stamp-pjax']); ?>

        <?= GridView::widget([
            'id' => 'grid-refund-stamp-document',
            'filterModel' => $refundSearchModel,
            'dataProvider' => $refundDataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
    $peid = $model->ProcessEntityID; // شناسه فرایند
    $class = $index % 2 ? 'odd' : 'even';
    return [
        'index' => $index,
        'class' => $class . ' refund-view-row',
        'id' => 'row-' . $peid,
        'data-url' => Url::to([
            '/document/stamp-document/view-refund',
            'peid' => $peid, // ✅ ارسال peid به جای sid
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
                // شناسه
                [
                    'attribute' => 'StampId',
                    'label' => Yii::t('document', 'ID'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->StampId : null;
                    },
                    'width' => '60px',
                ],
                // لوگو
                [
                    'format' => 'raw',
                    'label' => Yii::t("document", 'Logo'),
                    'width' => '60px',
                    'value' => function ($model) {
                        if (!$model->stampDocument) return '';
                        $stamp = $model->stampDocument;
                        $imageUrl = !empty($stamp->FinalStampDocument)
                            ? $stamp->getFinalStampDocumentUrl()
                            : Yii::$app->urlManager->createAbsoluteUrl([
                                'document/stamp-document/logo-image',
                                'id' => $stamp->StampId
                            ]);
                        return Html::img($imageUrl, [
                            'style' => 'border-radius:5px',
                            'width' => '35px',
                            'height' => '30px'
                        ]);
                    },
                ],
                // عنوان
                [
                    'attribute' => 'StampTitle',
                    'label' => Yii::t("document", 'Title'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->StampTitle : null;
                    },
                    'width' => '180px',
                ],
                // شرکت
                [
                    'attribute' => 'CompanyTitle',
                    'label' => Yii::t('document', 'Company'),
                    'value' => function ($model) {
                        if (!$model->stampDocument) return '';
                        return isset($model->stampDocument->obs->company)
                            ? $model->stampDocument->obs->company->PersonTitle
                            : '';
                    },
                    'width' => '150px',
                    'filter' => $personList,
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => $personList,
                        'options' => ['placeholder' => 'انتخاب شرکت'],
                        'pluginOptions' => ['allowClear' => true, 'width' => '200px']
                    ],
                ],
                // Parent Obs
                [
                    'attribute' => 'ParentObs',
                    'format' => 'raw',
                    'label' => Yii::t('automation', 'Parent Obs'),
                    'value' => function ($model) {
                        $title = $model->stampDocument ? $model->stampDocument->assistanceTitle : '';
                        return Utility::generateTextShorterSpan(Html::encode($title), 30, 30);
                    },
                    'width' => '180px',
                ],
                // موقعیت سازمانی
                [
                    'attribute' => 'PositionTitle',
                    'label' => Yii::t("document", 'Position'),
                    'value' => function ($model) {
                        if (!$model->stampDocument || empty($model->stampDocument->ObsIDRef)) return '';
                        return $model->stampDocument->obs->fullTitle ?? '';
                    },
                    'width' => '150px',
                ],
                // گردش (OrganizationalMaker)
                [
                    'attribute' => 'OrganizationalMaker',
                    'label' => Yii::t('document', 'گردش'),
                    'value' => function ($model) {
                        return $model->stampDocument
                            ? $model->stampDocument->getOrganizationalMakerListLabel()
                            : '---';
                    },
                    'width' => '90px',
                    'filter' => $refundSearchModel::getOrganizationalMakerList(),
                ],
                // نوع مدرک
                [
                    'attribute' => 'StampTypeIDRef',
                    'label' => Yii::t("document", 'نوع مدرک'),
                    'value' => function ($model) {
                        return $model->stampDocument && $model->stampDocument->stampType
                            ? $model->stampDocument->stampType->TypeName
                            : null;
                    },
                    'width' => '100px',
                    'filterType' => GridView::FILTER_SELECT2,
                    'filterWidgetOptions' => [
                        'data' => \yii\helpers\ArrayHelper::map(
                            \app\modules\document\models\StampTypeDocument::find()->all(),
                            'StampTypeId',
                            'TypeName'
                        ),
                        'options' => ['placeholder' => 'نوع'],
                        'pluginOptions' => ['allowClear' => true, 'width' => '100px']
                    ],
                ],
                // وضعیت
                [
                    'attribute' => 'Status',
                    'label' => Yii::t("document", 'Status'),
                    'value' => function ($model) {
                        return $model->stampDocument
                            ? $model->stampDocument->getStatusLabal()
                            : '---';
                    },
                    'width' => '90px',
                    'filter' => $refundSearchModel::getStatusList(),
                ],
                // تاریخ امحا
                [
                    'attribute' => 'DestroyDate',
                    'label' => Yii::t('document', 'تاریخ امحا'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->DestroyDate : null;
                    },
                    'width' => '100px',
                ],
                // توضیحات امحا
                [
                    'attribute' => 'DescriptionDestroy',
                    'label' => Yii::t('document', 'توضیحات امحا'),
                    'value' => function ($model) {
                        return $model->stampDocument ? $model->stampDocument->DescriptionDestroy : null;
                    },
                    'width' => '150px',
                ],
                // مرحله جاری
                [
                    'attribute' => 'lastActionTitle',
                    'label' => Yii::t('workflow', 'مرحله جاری'),
                    'value' => function ($model) {
                        return isset($model->lastAction) ? $model->lastAction->ActionTitle2 : '---';
                    },
                    'width' => '150px',
                ],
                // آخرین اقدام‌کننده
                [
                    'attribute' => 'LastUserName',
                    'label' => Yii::t('workflow', 'Last User'),
                    'value' => function ($model) {
                        return isset($model->lastUser) ? $model->lastUser->fullName : '---';
                    },
                    'width' => '120px',
                ],
                // تاریخ آخرین اقدام
                [
                    'attribute' => 'LastActionDateHijri',
                    'label' => Yii::t('workflow', 'Last Action Date'),
                    'value' => 'LastActionDateHijri',
                    'width' => '110px',
                ],
                // عملیات
                [
                    'class' => \kartik\grid\ActionColumn::className(),
                    'header' => Yii::t('workflow', 'Actions'),
                    'template' => '{nextaction}{summary}',
                    'width' => '100px',
                    'buttons' => [
                        'nextaction' => function ($url, $model, $key) {
                            if (Yii::$app->request->get('type') == 'ToDo' && isset($model->Status) && $model->Status == 1) {
                                return Html::a(
                                    '<i class="im-arrow-right2 rotate-360" style="font-size:16px;"></i>',
                                    Url::to(['/workflow/next-action/next-action', 'peid' => $model->ProcessEntityID]),
                                    [
                                        'onclick' => "showNextActionModal(this); return false;",
                                        'data-pjax' => "0",
                                        'data-original-title' => 'اقدام بعدی',
                                        'data-toggle' => 'tooltip',
                                        'class' => 'btn btn-xs btn-success',
                                        'style' => 'margin:0 2px;'
                                    ]
                                );
                            }
                            return '';
                        },
  'summary' => function ($url, $model, $key) {
    return Html::a(
        '<i class="glyphicon glyphicon-list-alt s16"></i>',
        Url::to(['/workflow/workflow/refund-stamp-summary', 'peid' => $model->ProcessEntityID]),
        [
            'data-original-title' => 'خلاصه اقدامات',
            'class' => 'btn btn-xs tip',
            'data-pjax' => '0',
            'style' => 'padding:1px 2px;color:#FF9800;',
            'data-toggle' => 'tooltip',
            'onclick' => 'showRefundSummaryModal(this); return false;',
        ]
    );
},
                    ]
                ]
            ],
        ]); ?>

        <?php Pjax::end(); ?>
    </div>
    <?php GridViewBox::end(); ?>
</div>

<?php \app\components\PopupBox::begin([]); ?>
<?php \app\components\PopupBox::end([]); ?>

<!-- مودال اقدام بعدی -->
<?php
Modal::begin([
    'id' => 'refund-next-action-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> اقدام بعدی</h4>',
 //        'options' => ['class' => 'fade modal in animated pulse']
]);
Pjax::begin(['id' => 'refund-next-action-pjax']);
Pjax::end();
Modal::end();
?>

<!-- مودال درخواست امحا (انتخاب نشان) -->
<?php
Modal::begin([
    'id' => 'refund-create-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="fa-plus"></i> درخواست امحا</h4>',
    'options' => ['class' => 'fade modal in animated pulse']
]);
Pjax::begin(['id' => 'refund-create-pjax']);
Pjax::end();
Modal::end();
?>
<!-- مودال راهنمای گردش کار -->
<?php
Modal::begin([
    'id' => 'refund-workflow-guide-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="fa fa-sitemap"></i> راهنمای گردش کار امحا نشان سازمانی</h4>',
    'options' => ['class' => 'fade modal in animated pulse'],
]);
?>
<div class="text-center" style="padding:10px;">
    <?= Html::img(
        Yii::getAlias('@web/images/workflow/refund-stamp-guide.png'),
        [
            'alt' => 'راهنمای گردش کار امحا نشان سازمانی',
            'style' => 'max-width:100%; height:auto; border-radius:8px; box-shadow:0 2px 12px rgba(0,0,0,.15);',
        ]
    ); ?>
</div>
<?php Modal::end(); ?>

<script>
function showCreateRefundModal(obj) {
    ajaxProgressLoader('show', '.modal-content');
    var url = $(obj).data('url') || $(obj).attr('data-url');
    $.ajax({
        url: url,
        type: 'GET'
    }).done(function (data) {
        $('#refund-create-pjax').html(data);
        $('#refund-create-modal').modal('show');
        ajaxProgressLoader('hide', '.modal-content');
    }).fail(function () {
        showMsgBoxInBottomRight('خطا در بارگذاری فرم', 'error');
        ajaxProgressLoader('hide', '.modal-content');
    });
    return false;
}

function showNextActionModal(obj) {
    ajaxProgressLoader('show', '#refund-stamp-pjax');
    var url = $(obj).attr('href');
    $.ajax({
        url: url
    }).done(function (data) {
        $('#refund-next-action-pjax').html(data);
        $('#refund-next-action-modal').modal('show');
        ajaxProgressLoader('hide', '#refund-stamp-pjax');
    }).fail(function () {
        ajaxProgressLoader('hide', '#refund-stamp-pjax');
        showMsgBoxInBottomRight('خطا در بارگذاری', 'error');
    });
    return false;
}

function showRefundSummaryModal(obj) {
    ajaxProgressLoader('show', '#refund-stamp-pjax');
    var url = $(obj).attr('href');
    $.ajax({
        url: url,
        type: 'GET',
        success: function (data) {
            if ($('#refund-summary-modal').length === 0) {
                $('body').append(
                    '<div class="modal fade" id="refund-summary-modal" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog modal-lg" role="document">' +
                    '<div class="modal-content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                    '<h4 class="modal-title">خلاصه اقدامات</h4>' +
                    '</div>' +
                    '<div class="modal-body"></div>' +
                    '</div></div></div>'
                );
            }
            $('#refund-summary-modal .modal-body').html(data);
            $('#refund-summary-modal').modal('show');
            ajaxProgressLoader('hide', '#refund-stamp-pjax');
        },
        error: function () {
            ajaxProgressLoader('hide', '#refund-stamp-pjax');
            showMsgBoxInBottomRight('خطا در بارگذاری اطلاعات!', 'error');
        }
    });
    return false;
}

window.reloadRefundForm = function () {
    console.log('شروع رفرش گرید امحاء');

    $.pjax.reload({
        container: '#refund-stamp-pjax',
        timeout: false,
        push: false,
        replace: false
    });
};
$(document).on('pjax:beforeSend', '#refund-stamp-pjax', function (event, xhr, options) {
    console.log('PJAX ارسال شد:', options.url);
});

$(document).on('pjax:success', '#refund-stamp-pjax', function () {
    console.log('PJAX با موفقیت رفرش شد.');
});

$(document).on('pjax:error', '#refund-stamp-pjax', function (event, xhr) {
    console.error('خطا در PJAX گرید امحاء:', xhr.status, xhr.responseText);
});

// کلیک روی ردیف برای باز کردن جزئیات
$(document).on('click', '.refund-view-row', function (e) {
    if ($(e.target).closest('a, button, input, select, textarea, .select2-container').length) {
        return true;
    }
    var url = $(this).data('url');
    if (url) {
        window.open(url, '_blank');
    }
});
</script>