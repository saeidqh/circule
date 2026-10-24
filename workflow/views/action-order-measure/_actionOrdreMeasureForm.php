<?php

use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style >

</style>
<?php
$form = ActiveForm::begin([
            'id' => 'action-order-measure-form',
            'layout' => 'horizontal',
            'options' => [
                'data-pjax' => '1',
            ]
        ]);
?>
<div class='row p5 mb5' style='background-color: #F7CBCB;' >
    <div class='col-md-12' >

        <div class='row ' >
            <div class='col-md-5' >
                <div class='row' >
                    <div class='col-md-3' >
                        <div class='row '>
                            <div style='background:#C966FB;border-top-left-radius: 5px;border-bottom-left-radius: 5px;height:100px;text-align: center;padding-top: 20px;'>
                                <span style='font-size:40px ;color:white;padding-top:50px;' >
                                    <?= Html::encode($actionOrderModel->ActionIDRef) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-9' >
                        <div class='row pt10' style='border:solid 1px #C966FB;height:100px;'>
                            <div class='col-md-2 pt5' >Title1:</div>
                            <div class='col-md-10 mb10' >
                                <?=
                                Html::textInput('actionTitle1', $actionOrderModel->action->ActionTitle1, [
                                    'id' => 'action-title-1',
                                    'class' => 'form-control input-sm',
                                    'disabled' => true,
                                ])
                                ?>
                            </div>
                            <div class='col-md-2 pt5' >Title2:</div>
                            <div class='col-md-10' >
                                <?=
                                Html::textInput('actionTitle2', $actionOrderModel->action->ActionTitle2, [
                                    'id' => 'action-title-2',
                                    'class' => 'form-control input-sm',
                                    'disabled' => true,
                                ])
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class='col-md-2' >
                <span class="">
                    <i class="en-arrow-right5 arrow-slow-flasher " style='font-size:100px' ></i>
                </span>
            </div>
            <div class='col-md-5' >
                <div class='row' >
                    <div class='col-md-3' >
                        <div class='row '>
                            <div style='background:#C966FB;border-top-left-radius: 5px;border-bottom-left-radius: 5px;height:100px;text-align: center;padding-top: 20px;'>
                                <span style='font-size:40px ;color:white;padding-top:50px;' > 
                                    <?= Html::encode($actionOrderModel->NextActionIDRef) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-9' >
                        <div class='row pt10' style='border:solid 1px #C966FB;height:100px;'>
                            <div class='col-md-2 pt5' >Title1:</div>
                            <div class='col-md-10 mb10' >
                                <?=
                                Html::textInput('actionTitle1', $actionOrderModel->nextAction->ActionTitle1, [
                                    'id' => 'action-title-1',
                                    'class' => 'form-control input-sm',
                                    'disabled' => true,
                                ])
                                ?>
                            </div>
                            <div class='col-md-2 pt5' >Title2:</div>
                            <div class='col-md-10' >
                                <?=
                                Html::textInput('actionTitle2', $actionOrderModel->nextAction->ActionTitle2, [
                                    'id' => 'action-title-2',
                                    'class' => 'form-control input-sm',
                                    'disabled' => true,
                                ])
                                ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class='clear' ></div>
</div>


<div class='row' >
    <div class="col-md-7" >
        <?=
        $form->field($actionOrderMeasureModel, 'MeasureIDRef')->widget(kartik\widgets\Select2::className(), [
            'data' => $measureArray,
            'options' => ['prompt' => '--Select Measure --'],
        ])->label('Measure')
        ?>
    </div>

</div>

<hr class="p0 m0 mb5" style="border-top: 1px solid #C7C7C7"/>
<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        <?=
        Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Create', [
            'class' => 'btn btn-success btn-sm pull-right ml10'
        ])
        ?>
        <?=
        Html::a('<i class="im-close"></i>&nbsp;Cancel', '#', [
            'class' => 'btn btn-danger btn-sm pull-right',
            'data-dismiss' => 'modal'
        ])
        ?>
    </div>
</div>
<?php ActiveForm::end(); ?>