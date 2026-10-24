<?php

use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style >

    .arrow-slow-flasher{
        color:#F7B0B0 !important;
        -webkit-animation: arrowSlowFlasherEffect 2s infinite;
        -moz-animation:    arrowSlowFlasherEffect 2s infinite;
        -ms-animation:     arrowSlowFlasherEffect 2s infinite;
    }

    @-webkit-keyframes arrowSlowFlasherEffect {
        50%  { color:#FF2E2E; }
    }
    @-moz-keyframes arrowSlowFlasherEffect {
        50%  { color:#FF2E2E; }
    }
    @-ms-keyframes arrowSlowFlasherEffect {
        50%  { color:#FF2E2E; }
    }

</style>
<?php
$form = ActiveForm::begin([
            'id' => 'action-order-form',
            'layout' => 'horizontal',
            'options' => [
                'data-pjax' => '1',
                'enctype' => 'multipart/form-data'
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
        <?= $form->field($actionOrderModel, 'AutoMessage1')->textInput() ?>
        <?= $form->field($actionOrderModel, 'AutoMessage2')->textInput() ?>
        <?= $form->field($actionOrderModel, 'AutoMessage3')->textInput() ?>

        <?=
        $form->field($actionOrderModel, 'letterIdRef1')->widget(kartik\widgets\Select2::className(), [
            'data' => $arrLetter,
            'options' => ['prompt' => '--Select Letter1 --'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Letter1')
        ?>
        <?=
        $form->field($actionOrderModel, 'letterIdRef2')->widget(kartik\widgets\Select2::className(), [
            'data' => $arrLetter,
            'options' => ['prompt' => '--Select Letter2 --'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Letter2')
        ?>
        <?=
        $form->field($actionOrderModel, 'letterIdRef3')->widget(kartik\widgets\Select2::className(), [
            'data' => $arrLetter,
            'options' => ['prompt' => '--Select Letter3 --'],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ])->label('Letter3')
        ?>
        <?=
        $form->field($actionOrderModel, 'documentModelIdRef')->widget(kartik\widgets\Select2::className(), [
            'data' => $arrDocumentModel,
            'options' => ($actionOrderModel->documentModelIdRef) ? ['prompt' => '--Select Person --'] : []
        ])
        ?>
    </div>
    <div class="col-md-5" >
        <?= $form->field($actionOrderModel, 'isDefaultNextAction')->checkbox() ?>
        <?= $form->field($actionOrderModel, 'RequiredMessage')->checkbox() ?>
        <?= $form->field($actionOrderModel, 'requiredLetterNo')->checkbox() ?>
        <?= $form->field($actionOrderModel, 'acceptManualDate')->checkbox() ?>
    </div>

</div>

<hr class="p0 m0 mb5" style="border-top: 1px solid #C7C7C7"/>
<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        <?=
        Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Update', [
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