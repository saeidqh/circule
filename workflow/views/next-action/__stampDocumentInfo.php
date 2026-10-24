<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\jDateTime;
use app\modules\document\models\StampDocument;

/* @var $this yii\web\View */
/* @var $peModel app\modules\workflow\models\ProcessEntity */

// پیدا کردن مدل StampDocument بر اساس StampIDRef
$stampDocumentModel = null;
if ($peModel && $peModel->StampIDRef) {
    $stampDocumentModel = StampDocument::find()
        ->where(['StampId' => $peModel->StampIDRef])
        ->one();
}

// اگر مدل وجود نداشت، پیام خطا نشان بده
if (!$stampDocumentModel) {
    echo '<div class="alert alert-warning">اطلاعات سند مهر یافت نشد.</div>';
    return;
}

// ایجاد شیء jDateTime برای تبدیل تاریخ
$jdate = new jDateTime(true, true, 'Asia/Tehran');
// ✅ استفاده از RequestDate به جای created_at
$requestDate = '---';
if ($stampDocumentModel->RequestDate) {
    $requestDate = $jdate->date("Y/m/d", strtotime($stampDocumentModel->RequestDate), false);
}

$stampModel = $peModel->stampDocument;
?>

<style>
    input[type="checkbox"] {
        pointer-events: none;
    }
    label:not(:last-child) {
        margin-right: 10px;
    }
</style>

<?php
$form = ActiveForm::begin([
    'id' => 'stamp-document-info-form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'method' => 'get',
    'options' => array(
        'class' => 'form-horizontal ',
    ),
]);
?>

<div class="row" >
    <div class="col-md-12">
        <div class="row mb5">

        </div>

        <div class="row mb5">
                        <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('document', 'Stamp ID') ?>  
                &nbsp; <i class="badge badge-brown"><?= Html::encode($stampDocumentModel->StampId) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('document', 'Stamp Title') ?>  
                &nbsp; <i class="badge badge-brown"><?= Html::encode($stampDocumentModel->StampTitle) ?></i>
            </div>
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('document', 'Stamp Purpose') ?>  
                &nbsp; <i class="badge badge-brown"><?= Html::encode($stampDocumentModel->StampPurpose) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('document', 'Request Date') ?>  
                &nbsp; <i class="badge badge-brown"><?= Html::encode($requestDate) ?></i>
            </div>
<div class="col-lg-4 col-md-4 col-xs-12">
    <?= Yii::t('document', 'Company') ?>  
    &nbsp; <i class="badge badge-brown">
        <?= isset($stampDocumentModel->company) ? Html::encode($stampDocumentModel->company->companyName) : '---' ?>
    </i>
</div>
        </div>
        
        <div class="row mb5">

        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>