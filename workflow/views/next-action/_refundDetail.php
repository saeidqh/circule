<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use app\modules\document\models\StampDocument;
use app\components\jDateTime;

DatepickerAsset::register($this);
JdateTimeAsset::register($this);

/* @var $this yii\web\View */
/* @var $stampModel app\modules\document\models\StampDocument */
/* @var $processEntityModel app\modules\workflow\models\ProcessEntity */
/* @var $actionOrderMeasures array */

if (!isset($stampModel) || !$stampModel) {
    // تلاش برای پیدا کردن از processEntity
    if (isset($processEntityModel)) {
        $stampId = !empty($processEntityModel->ObjectIDRef)
            ? $processEntityModel->ObjectIDRef
            : $processEntityModel->StampIDRef;
        $stampModel = StampDocument::find()
            ->where(['StampId' => $stampId, 'isDeleted' => 0])
            ->one();
    }
}

if (!$stampModel) {
    echo '<div class="alert alert-danger">اطلاعات سند نشان یافت نشد.</div>';
    return;
}

$jdate = new jDateTime(true, true, 'Asia/Tehran');
$requestDate = $stampModel->RequestDate
    ? $jdate->date("Y/m/d", strtotime($stampModel->RequestDate), false)
    : '---';
$destroyDate = $stampModel->DestroyDate ?: '---';
?>

<?php
$form = ActiveForm::begin([
    'id' => 'fix-data',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'method' => 'post',
    'options' => [
        'class' => 'form-horizontal',
        'data-pjax' => false,
    ],
]);
?>

<?= Html::hiddenInput('peid', $processEntityModel->ProcessEntityID) ?>
<?= Html::hiddenInput('type', 'refund-stamp') ?>
<?= Html::hiddenInput('stamp_id', $stampModel->StampId) ?>

<div class="grid-box grid-box-silver" style="direction: rtl; text-align: right; margin-top: 15px;">
    <div class="grid-box-body">
        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <strong><?= Yii::t('document', 'Stamp ID') ?>:</strong>
                <i class="badge badge-brown"><?= Html::encode($stampModel->StampId) ?></i>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
                <strong><?= Yii::t('document', 'Stamp Title') ?>:</strong>
                <i class="badge badge-brown"><?= Html::encode($stampModel->StampTitle) ?></i>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
                <strong><?= Yii::t('document', 'Stamp Purpose') ?>:</strong>
                <i class="badge badge-brown"><?= Html::encode($stampModel->StampPurpose) ?></i>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
                <strong><?= Yii::t('document', 'Request Date') ?>:</strong>
                <i class="badge badge-brown"><?= Html::encode($requestDate) ?></i>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
                <strong><?= Yii::t('document', 'Company') ?>:</strong>
                <i class="badge badge-brown">
                    <?= isset($stampModel->company) ? Html::encode($stampModel->company->companyName) : '---' ?>
                </i>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
                <strong><?= Yii::t('document', 'تاریخ امحا') ?>:</strong>
                <i class="badge badge-brown"><?= Html::encode($destroyDate) ?></i>
            </div>
            <div class="col-lg-12 col-md-12 col-xs-12">
                <strong><?= Yii::t('document', 'توضیحات امحا') ?>:</strong>
                <i class="badge badge-brown"><?= Html::encode($stampModel->DescriptionDestroy ?: '---') ?></i>
            </div>
        </div>
    </div>
</div>

<?php
if (isset($actionOrderMeasures) && !empty($actionOrderMeasures)) {
    echo $this->render('_actionOrdreMeasure', [
        'processEntityModel' => $processEntityModel,
        'actionOrderMeasures' => $actionOrderMeasures
    ]);
}
?>

<div class="pull-right" style="padding: 15px 5px;">
    <?= Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
        'class' => 'btn btn-sm btn-success',
        'onclick' => 'signRefund(this)',
        'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl([
            'workflow/next-action/sign',
            'type' => 'refund-stamp',
            'peid' => Yii::$app->request->get('peid'),
            'mode' => 'sign'
        ])
    ]); ?>

    <?= Html::a('<i class="en-back"></i> برگشت', '#', [
        'class' => 'btn btn-sm btn-primary',
        'data-dismiss' => 'modal'
    ]); ?>

    <?= Html::a('<i class="br-cancel"></i> انصراف', '#', [
        'class' => 'btn btn-sm btn-warning',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$js = "
function signRefund(obj) {
    if (typeof ajaxProgressLoader !== 'undefined') {
        ajaxProgressLoader('show', '.modal-content');
    }

    var data = $('#fix-data').serialize();

    $.ajax({
        type: 'POST',
        url: $(obj).attr('ajax-url'),
        dataType: 'json',
        data: data
    }).done(function (data) {
        if (typeof ajaxProgressLoader !== 'undefined') {
            ajaxProgressLoader('hide', '.modal-content');
        }

        if (data.result != true) {
            if (typeof showMsgBoxInBottomRight !== 'undefined') {
                showMsgBoxInBottomRight(data.message || 'خطا در انجام عملیات', 'error');
            } else {
                alert(data.message || 'خطا در انجام عملیات');
            }
            return;
        }

        var modal = $(obj).closest('.modal');

        if (modal.length > 0) {
            modal.modal('hide');
        }

        setTimeout(function () {
            if (typeof reloadRefundForm === 'function') {
                reloadRefundForm();
            } else {
                console.error('تابع reloadRefundForm در صفحه اصلی پیدا نشد.');
            }
        }, 300);

        if (typeof showMsgBoxInBottomRight !== 'undefined') {
            showMsgBoxInBottomRight(
                data.message || 'عملیات با موفقیت انجام شد',
                'success'
            );
        }
    }).fail(function () {
        if (typeof ajaxProgressLoader !== 'undefined') {
            ajaxProgressLoader('hide', '.modal-content');
        }

        if (typeof showMsgBoxInBottomRight !== 'undefined') {
            showMsgBoxInBottomRight('خطا در ارتباط با سرور', 'error');
        }
    });

    return false;
}
";
$this->registerJs($js, \yii\web\View::POS_READY);
?>