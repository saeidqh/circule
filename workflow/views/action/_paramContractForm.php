<?php

use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<style>
    .panel-default > .panel-heading {
        color: #000 !important;
        background-color: #DAE6C6;
        border-color: #ddd;
        height: 33px;
        overflow: hidden;
    }

    .panel-group .panel.panel-default .panel-body {
        border-color: #DAE6C6 !important;
    }

    .panel-default > .panel-heading a {
        color: black !important
    }

    .param-contract-form-box .help-block {
        display: none;
    }

    .param-contract-form-box .checkbox {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
    }
</style>

<?php
$form = ActiveForm::begin([
    'id' => 'param-invoice-form',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'options' => [
        'data-pjax' => '1',
        'enctype' => 'multipart/form-data'
    ]
]); ?>

<div class='row p5 mb5' style='background-color: #FAEBFF;margin-top: -10px'>
    <div class='col-md-12'>

        <div class='row '>
            <div class='col-md-12'>
                <div class='row'>
                    <div class='col-md-2'>
                        <div class='row '>
                            <div style='background:#C966FB;border-top-left-radius: 5px;border-bottom-left-radius: 5px;height:83px;text-align: center;padding-top: 5px;'>
                                <span style='font-size:40px ;color:white;padding-top:50px;'>
                                    <?= $actionModel->ActionID ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-10'>
                        <div class='row pt5' style='border:solid 1px #C966FB;height:83px;'>
                            <div class='col-md-2 pt5'>Title1:</div>
                            <div class='col-md-10 mb10'>
                                <?= Html::textInput('actionTitle1', $actionModel->ActionTitle1, [
                                    'id' => 'action-title-1',
                                    'class' => 'form-control input-sm',
                                    'disabled' => true,
                                ]) ?>
                            </div>
                            <div class='col-md-2 pt5'>Title2:</div>
                            <div class='col-md-10'>
                                <?= Html::textInput('actionTitle2', $actionModel->ActionTitle2, [
                                    'id' => 'action-title-2',
                                    'class' => 'form-control input-sm',
                                    'disabled' => true,
                                ]) ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class='clear'></div>
</div>
<div class='row param-contract-form-box' style="height:420px; overflow-y: scroll; overflow-x: hidden;">
    <div class='col-md-12 pl0 pr5'>
        <div class="panel-group accordion" id=accordion1>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse1>
                            <i class='im-arrow-right2'></i>&nbsp;Pre Contract
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'preContractVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'preContractEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse1 class="panel-collapse collapse in">
                    <div class=panel-body>Param not found.</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h5 class="panel-title"><a id=contract-cols class=accordion-toggle data-toggle=collapse
                                               data-parent=#accordion1 href=#collapse2>
                            <i class='im-arrow-right2'></i>&nbsp;Contract
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'contractVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'contractEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse2 class="panel-collapse collapse">
                    <div class=panel-body>
                        <div class='row'>

                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'contractNo', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'contractSubject', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'startDate', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'subcontractor', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'category', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'type', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                        <div class='row'>

                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'pms', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'plan', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'project', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'eps', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'executionType', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-2'>
                                <?= $form->field($actionParamContractModel, 'requiredContractNo', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>

                            <div class='col-md-2'>
                            </div>
                            <div class='col-md-2'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse3>
                            <i class='im-arrow-right2'></i>&nbsp;Rider
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'riderVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'riderEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse3 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse4>
                            <i class='im-arrow-right2'></i>&nbsp;Budget
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'budgetVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'budgetEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse4 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse5>
                            <i class='im-arrow-right2'></i>&nbsp;Invoice Process
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'invoiceProcessVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'invoiceProcessEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse5 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse6>
                            <i class='im-arrow-right2'></i>&nbsp;Pms & Plan
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'pmsPlanVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'pmsPlanEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse6 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse7>
                            <i class='im-arrow-right2'></i>&nbsp;Financial Code
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'financialCodeVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'financialCodeEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse7 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse8>
                            <i class='im-arrow-right2'></i>&nbsp;Waranties
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'warantiesVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'warantiesEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse8 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class=panel-heading>
                    <h5 class=panel-title><a class=accordion-toggle data-toggle=collapse data-parent=#accordion1
                                             href=#collapse8>
                            <i class='im-arrow-right2'></i>&nbsp;ClosingGrade
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamContractModel, 'closingGradeVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamContractModel, 'closingGradeEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse8 class="panel-collapse collapse">
                    <div class=panel-body>Param not found</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        <?= Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Save', [
            'class' => 'btn btn-success btn-sm pull-right ml10'
        ]) ?>
        <?= Html::a('<i class="im-close"></i>&nbsp;Cancel', '#', [
            'class' => 'btn btn-danger btn-sm pull-right',
            'data-dismiss' => 'modal'
        ]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script>
    var t = setTimeout(function () {
        $('#contract-cols').click();
    }, 250);
</script>
