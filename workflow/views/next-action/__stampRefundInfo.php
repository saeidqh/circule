<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\components\jDateTime;
use app\modules\document\models\StampDocument;

/* @var $this yii\web\View */
/* @var $peModel app\modules\workflow\models\ProcessEntity */

$stampDocumentModel = null;
if ($peModel) {
    $stampId = !empty($peModel->ObjectIDRef) ? $peModel->ObjectIDRef : $peModel->StampIDRef;
    if ($stampId) {
        $stampDocumentModel = StampDocument::find()
            ->where(['StampId' => $stampId, 'isDeleted' => 0])
            ->one();
    }
}

if (!$stampDocumentModel) {
    echo '<div class="alert alert-warning">اطلاعات سند نشان یافت نشد.</div>';
    return;
}

$jdate = new jDateTime(true, true, 'Asia/Tehran');

$requestDate = '---';
if ($stampDocumentModel->RequestDate) {
    $requestDate = $jdate->date("Y/m/d", strtotime($stampDocumentModel->RequestDate), false);
}

$destroyDate = '---';
if ($stampDocumentModel->DestroyDate) {
    $destroyDate = $stampDocumentModel->DestroyDate; // معمولاً شمسی ذخیره شده
}
?>

<style>
    input[type="checkbox"] { pointer-events: none; }
    label:not(:last-child) { margin-right: 10px; }
</style>

<?php
$form = ActiveForm::begin([
    'id' => 'stamp-refund-info-form',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'method' => 'get',
    'options' => ['class' => 'form-horizontal'],
]);
?>

<div class="row">
    <div class="col-md-12">
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
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('document', 'تاریخ امحا') ?>
                &nbsp; <i class="badge badge-brown"><?= Html::encode($destroyDate) ?></i>
            </div>
            <div class="col-lg-8 col-md-8 col-xs-12">
                <?= Yii::t('document', 'توضیحات امحا') ?>
                &nbsp; <i class="badge badge-brown"><?= Html::encode($stampDocumentModel->DescriptionDestroy ?: '---') ?></i>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>