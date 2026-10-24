<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$actionData = Yii::$app->session['EntityID-' . $processEntityModel->ProcessEntityID];
$actionOrderModel = \app\modules\workflow\models\ActionOrder::find()->where(['ActionIDRef' => $processEntityModel->LastActionIDRef, 'NextActionIDRef' => $actionData['ActionIDRef']])->one();
$actionOrderMeasures = \app\modules\workflow\models\ActionOrderMeasure::find()->where(['ActionOrderIDRef' => $actionOrderModel->ID])->andWhere(['isDeleted' => null])->all();

?>

<?php $form = ActiveForm::begin([
    'id' => 'fix-data',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'method' => 'post',
    'options' => array(
        'class' => 'form-horizontal ',

    ),
]); ?>
<?php if(!empty($actionOrderMeasures)) { ?>
<div><span class="badge badge-primary mt10" style=""><i class="fa-check s16">&nbsp;</i><?= Yii::t('workflow', 'Check List') ?></span></div>
    <div class="grid-box grid-box-silver ">
        <div class="grid-box-body">
        <?php
            $index = 1;
            foreach ($actionOrderMeasures as $actionOrderMeasure) { ?>
            <div class="mb5" style="display: flex; align-items: center;">
                <div style="display: flex; margin-right: 20px; width: 320px;">
                    <div style="width: 20px"><?= $index.')'?></div>
                    <div class="text-center"><?= $actionOrderMeasure->measure->MeasureTitle ?></div>
                </div>
                <div class="text-right text-success" style="margin-right: 5px;">
                    <?= Yii::t('workflow', 'Approved') ?>
                </div>
                <div style="margin-right: 20px;">
                    <?= Html::radio('value'.$actionOrderMeasure->ActionOrderMeasureID, false, ['value' => 1]) ?>
                </div>
                <div class="text-right text-danger" style="margin-right: 5px;">
                    <?= Yii::t('workflow', 'Not Approved') ?>
                </div>
                <div style="margin-right: 40px;">
                    <?= Html::radio('value'.$actionOrderMeasure->ActionOrderMeasureID, false, ['value' => 2]) ?>
                </div>
                <div style="display: flex; align-items: center; flex-grow: 1;">
                    <span style="margin-right: 5px;">
                        <?= Yii::t('workflow', 'Descripton') ?>
                    </span>
                    <?= Html::textarea('description'.$actionOrderMeasure->ActionOrderMeasureID, '', ['class' => 'form-control input-sm text-right']) ?>
                </div>
            </div>
         <?php $index++; } ?>
        </div>
    </div>
<?php } ?>

<?php ActiveForm::end(); ?>
