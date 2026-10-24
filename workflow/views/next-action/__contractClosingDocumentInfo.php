<?php 
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<?php $form = ActiveForm::begin([
        'id'=>'fix-data',
        'enableClientValidation'=> false,
        'enableAjaxValidation'=> false,
        'method'=>'post',
        'options'=>array(
            'class'=>'form-horizontal ',

        ),

]); ?>

<div class="row" >
    <div class="col-md-12">
        <div class="row mb5" >
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('contract', 'Contract No') ?>  &nbsp; <i class="badge badge-purple"><?= Html::encode($peModel->contractClosingDocument->contract->ContractNo) ?></i>
            </div>

            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('contract', 'Subject') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($peModel->contractClosingDocument->contract->ContractSubject) ?></i>
            </div>
        </div>
        
        <div class="row mb5" >
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('contract', 'Sub Contractor') ?> 
                <i class="badge badge-pink">
                    <?= Html::encode($peModel->contractClosingDocument->contract->subcontractorsList) ?>
                </i>
            </div>
            
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('contract', 'Signer Company') ?>  &nbsp; <i class="badge badge-info"><?= Html::encode($peModel->contractClosingDocument->contract->signer->PersonTitle) ?></i>
            </div>    
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>    

