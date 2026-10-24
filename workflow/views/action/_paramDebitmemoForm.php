<?php
use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<?php
    $form = ActiveForm::begin([
    'id' => 'param-invoice-form',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'options' => [
        'data-pjax' => '1',
        'enctype' => 'multipart/form-data'
    ]
]);?>
<div class='row p5 mb5' style='background-color: #FAEBFF;' >
    <div class='col-md-12' >

        <div class='row ' >
            <div class='col-md-12' >
                <div class='row' >
                    <div class='col-md-2' >
                        <div class='row '>
                            <div style='background:#C966FB;border-top-left-radius: 5px;border-bottom-left-radius: 5px;height:100px;text-align: center;padding-top: 20px;'>
                                <span style='font-size:40px ;color:white;padding-top:50px;' >
                                    <?= $actionModel->ActionID ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-10' >
                        <div class='row pt10' style='border:solid 1px #C966FB;height:100px;'>
                            <div class='col-md-2 pt5' >Title1:</div>
                            <div class='col-md-10 mb10' >
                                <?= Html::textInput('actionTitle1', $actionModel->ActionTitle1,[
                                        'id'=>'action-title-1',
                                        'class'=>'form-control input-sm',
                                        'disabled'=>true,
                                ]) ?>
                            </div>
                            <div class='col-md-2 pt5' >Title2:</div>
                            <div class='col-md-10' >
                                <?= Html::textInput('actionTitle2', $actionModel->ActionTitle2,[
                                        'id'=>'action-title-2',
                                        'class'=>'form-control input-sm',
                                        'disabled'=>true,
                                ]) ?>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div class='clear' ></div>
</div>
<div class='row'>

    <div class='col-md-6'>
        <?= $form->field($actionParamDebitmemoModel, 'debitmemo_financialDocNumber_show',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
        <?= $form->field($actionParamDebitmemoModel, 'debitmemo_financialDocNumber_required',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
        <?= $form->field($actionParamDebitmemoModel, 'debitmemo_financialDocDateHijri_show',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
        <?= $form->field($actionParamDebitmemoModel, 'debitmemo_financialDocDateHijri_required',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
    </div>
    <div class='col-md-6'>
        <?php 
        // echo $form->field($actionParamDebitmemoModel, 'debitmemo_canEditRequest',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
        <?php 
        // echo $form->field($actionParamDebitmemoModel, 'debitmemo_canEditApprove',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
        <?php 
        // echo $form->field($actionParamDebitmemoModel, 'debitmemo_insertDocument',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
        ?>
        <?php
    // انشالله در یک موقعیت دیگر انجام گردد زیرا زمان برای اعمال و بررسی آن محیا نمی باشد
//        echo $form->field($actionParamDebitmemoModel, 'debitmemo_amountStatus',['horizontalCssClasses' =>['label'=>'col-md-5','wrapper'=>'col-md-7']])
//            ->dropDownList($actionParamDebitmemoModel->AMOUNT_STATUS_TYPES,
//                ['prompt' =>(($actionParamDebitmemoModel->debitmemo_amountStatus)?null:'...') ,]
//            )->label('Amount Status') 
                ?>
    </div>

    

</div>


<hr class="p0 m0 mb5" style="border-top: 1px solid #C7C7C7"/>
<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        <?= Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Save', [
            'class'=>'btn btn-success btn-sm pull-right ml10'
        ]) ?>
        <?= Html::a('<i class="im-close"></i>&nbsp;Cancel','#', [
            'class'=>'btn btn-danger btn-sm pull-right',
            'data-dismiss'=>'modal'
        ]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>