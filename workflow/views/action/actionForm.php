<?php
use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style >
    .prc-act-head{background-color:#F7EA8F;}
    .prc-act-head input{background-color:#FFFEE4 !important;}
    
</style>
<?php app\components\GridViewBox::begin(['cssClass' => '']); ?>
    <?php app\components\GridViewBox::begin(['cssClass' => 'prc-act-head']); ?>
        <div class='row mb10' >
            <div class="col-md-2">
                <b>Process</b>
            </div>
            <div class="col-md-10">
                <?= Html::textInput('processTitle', $actionModel->process->ProcessTitle,[
                        'id'=>'process-title',
                        'class'=>'form-control input-sm',
                        'disabled'=>true,
                ]) ?>
            </div>
        </div>
        <div class='row' >
            <div class="col-md-2">
                <b>Action ID</b>
            </div>
            <div class="col-md-3">
                <?= Html::textInput('actionId', $actionModel->ActionID,[
                        'id'=>'action-id',
                        'class'=>'form-control input-sm',
                        'disabled'=>true,
                ]) ?>
            </div>
            <div class="col-md-2">
                <b>Action Order</b>
            </div>
            <div class="col-md-3">
                <?= Html::textInput('actionOrder', $actionModel->ActionOrder,[
                        'id'=>'action-order',
                        'class'=>'form-control input-sm',
                        'disabled'=>true,
                ]) ?>
            </div>
            <div class="col-md-1">
                <b>Type</b>
            </div>
            <div class="col-md-1  pl0 text-right">
                <?php 
                    $value = '<span class="badge badge-primary  mt5 pull-right" style="width:46px" ><i class="fa-arrow-right s12"></i></span>';
                    if($actionModel->isStartAction ){
                        $value='<span class="badge badge-success mt5 pull-right" style="width:46px" >Start</span>';
                    }else if($actionModel->isEndAction ){
                        $value='<span class="badge badge-danger mt5 pull-right" style="width:46px" >End</span>';
                    }
                    echo $value;
               ?>
            </div>
        </div>
    <?php app\components\GridViewBox::end(); ?>
    <?php
    $form = ActiveForm::begin([
        'id' => 'process-action-form',
        'layout' => 'horizontal',
        'options' => [
            'data-pjax' => '1',
            'enctype' => 'multipart/form-data'
        ]
    ]);?>
        <div class='row' >


            <?= $form->field($actionModel, 'ActionTitle1')->textInput() ?>
            
            <?= $form->field($actionModel, 'ActionTitle2')->textInput() ?>
            <?= $form->field($actionModel, 'companyIdList')->widget(kartik\widgets\Select2::className(), [
                'data' => $companyArray,
                'options' => [
                    'placeholder' => '--Select Company--',
                    'multiple' => true,
                    'tabindex' => '110'
                ],
            ])->label('Companys');
            ?>
            <?= $form->field($actionModel, 'bestDurationInMinute')->textInput() ?>
            <?= $form->field($actionModel, 'calenderIdRef')->widget(kartik\widgets\Select2::className(),[
                'data' => $arrCalender,
                'options' => ($actionModel->calenderIdRef) ?['prompt' => '--Select Person --']: []
            ])->label('Calender')?>
            <?= $form->field($actionModel, 'ActionOrder')->textInput(["type"=>'number']) ?>
            <?= $form->field($actionModel, 'CharacterTypeIDRef')->widget(kartik\widgets\Select2::className(),[
                'data' => $characterTypeArray,
                'options' => [
                    'placeholder' => '--Select CharacterType--',
                ],
//                'options' => ($actionModel->CharacterTypeIDRef) ?['prompt' => '--Select CharacterType --']: []
            ])->label('Character Type')?>
        </div>

        <hr style="border-top: 1px solid #C7C7C7"/>
        <div class='row'>
            <div class='col-md-6 pull-right mb5'>
                <?= Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Update', [
                    'class'=>'btn btn-success btn-sm pull-right ml10'
                ]) ?>
                <?= Html::a('<i class="im-close"></i>&nbsp;Cancel','#', [
                    'class'=>'btn btn-danger btn-sm pull-right',
                    'data-dismiss'=>'modal'
                ]) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
<?php app\components\GridViewBox::end(); ?>