<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\CandoHelper;
use app\assets\DatepickerAsset;
use app\assets\JdateTimeAsset;
use app\modules\document\models\StampDocument; 
use app\components\jDateTime;

// ثبت Assetها
DatepickerAsset::register($this);
JdateTimeAsset::register($this);

/* @var $this yii\web\View */
/* @var $stampModel app\modules\automation\models\StampDocument */
/* @var $processEntityModel app\modules\workflow\models\ProcessEntity */
/* @var $actionOrderMeasures array */
/* @var $internalScope array */
/* @var $externalScope array */

if (!isset($stampModel) || !$stampModel) {
    echo '<div class="alert alert-danger">اطلاعات سند مهر یافت نشد.</div>';
    return;
}

// تبدیل تاریخ
$jdate = new jDateTime(true, true, 'Asia/Tehran');
$requestDate = $stampModel->RequestDate ? $jdate->date("Y/m/d", strtotime($stampModel->RequestDate), false) : '---';

$stampModel = $processEntityModel->stampDocument;
?>

<?php
// فرم fix-data - برای ارسال داده به اکشن sign
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

<!-- فیلدهای مخفی برای ارسال داده‌های ضروری -->
<?= Html::hiddenInput('peid', $processEntityModel->ProcessEntityID) ?>
<?= Html::hiddenInput('type', 'stamp-document') ?>
<?= Html::hiddenInput('stamp_id', $stampModel->StampId) ?>

<!-- نمایش اطلاعات سند مهر -->
<div class="grid-box grid-box-silver" style="direction: rtl;text-align: right; margin-top: 15px;">
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
            <div class="col-lg-4 col-md-4">
                 <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($stampModel->company->companyName) ?></i>
            </div>
        </div>

        <?php if (!empty($internalScope) || !empty($externalScope)): ?>
        <div class="row mb5">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <strong><?= Yii::t('document', 'Scopes') ?>:</strong>
                <?php if (!empty($internalScope)): ?>
                    <i class="badge badge-info">داخلی: <?= Html::encode(implode(', ', $internalScope)) ?></i>
                <?php endif; ?>
                <?php if (!empty($externalScope)): ?>
                    <i class="badge badge-info">خارجی: <?= Html::encode(implode(', ', $externalScope)) ?></i>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<?php
// نمایش اقدامات مرتبط
if (isset($actionOrderMeasures) && !empty($actionOrderMeasures)) {
    echo $this->render('_actionOrdreMeasure', [
        'processEntityModel' => $processEntityModel,
        'actionOrderMeasures' => $actionOrderMeasures
    ]);
}
?>

<!-- دکمه‌های اقدام -->
<div class="pull-right" style="padding: 15px 5px;">
    <?=
    Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
        'class' => 'btn btn-sm btn-success',
        "onclick" => "sign(this)",
        'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
    ]);
    ?>

    <?=
    Html::a('<i class="en-back"></i> برگشت', '#', [
        'class' => 'btn btn-sm btn-primary',
        'data-dismiss' => 'modal'
    ])
    ?>

    <?=
    Html::a('<i class="br-cancel"></i> انصراف', '#', [
        'class' => 'btn btn-sm btn-warning',
        'data-dismiss' => 'modal'
    ]);
    ?>
</div>

<?php ActiveForm::end(); ?>

<?php
$js = "
    function sign(obj){
        if(typeof ajaxProgressLoader !== 'undefined'){
            ajaxProgressLoader('show', '.modal-content');
        }
        
        var data = $('#fix-data').serialize();
        
        $.ajax({
            type: 'POST',
            url: $(obj).attr('ajax-url'),
            dataType: 'json',
            data: data,
        }).done(function(data){
            if(typeof ajaxProgressLoader !== 'undefined'){
                ajaxProgressLoader('hide', '.modal-content');
            }
            
            if(data.result != true){
                if(typeof showMsgBoxInBottomRight !== 'undefined'){
                    showMsgBoxInBottomRight(data.message, 'error');
                } else {
                    alert(data.message || 'خطا در انجام عملیات');
                }
            }
            else if(data.result == true){
                // ✅ بستن مودال با پیدا کردن نزدیکترین مودال
                var modal = $(obj).closest('.modal');
                if(modal.length > 0){
                    modal.modal('hide');
                } else {
                    $('#stamp-document-modal').modal('hide');
                }
                
                // ✅ رفرش لیست ToDo
                $.pjax.reload({
                    container: '#stamp-document-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/stamp-document', 'type' => 'ToDo']) . "',
                    push: false,
                    replace: false,
                });
                
                if(typeof showMsgBoxInBottomRight !== 'undefined'){
                    showMsgBoxInBottomRight('عملیات با موفقیت انجام شد', 'success');
                }
            }
        }).error(function(){
            if(typeof ajaxProgressLoader !== 'undefined'){
                ajaxProgressLoader('hide', '.modal-content');
            }
            if(typeof showMsgBoxInBottomRight !== 'undefined'){
                showMsgBoxInBottomRight('خطا در ارتباط با سرور', 'error');
            }
        });
        
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_READY);
?>