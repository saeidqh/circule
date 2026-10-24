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
    'id' => 'param-automation-form',
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
                <div class="panel-heading">
                    <h5 class="panel-title"><a id=contract-cols class=accordion-toggle data-toggle=collapse
                                               data-parent=#accordion1 href=#collapse2>
                            <i class='im-arrow-right2'></i>&nbsp;Network
                        </a></h5>
                    <div class='col-md-4 pull-right mr25'>
                        <div class='row'>
                            <div class='col-md-4 col-md-offset-2'>
                                <?= $form->field($actionParamNetworkModel, 'NetworkModelVisible', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'NetworkModelEditable', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id=collapse2 class="panel-collapse collapse">
                    <div class=panel-body>
                        <div class='row'>

                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'FirstName', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'LastName', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'Father', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'NationalCode', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'Mobile', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'EmploymentType', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'Position', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'NetworkType', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'KosarNetwork', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'SecureNetwork', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'KosarUsername', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'SecureUsername', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'InternalUsername', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'InternetUsername', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'OrganizationEmailUsername', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'File', ['horizontalCssClasses' => ['label' => 'col-md-1', 'wrapper' => 'col-md-11 col-sm-offset-1']])->checkbox() ?>
                            </div>
                        </div>
                        <div class='row'>
                            <div class='col-md-3'>
                                <?= $form->field($actionParamNetworkModel, 'showObsChilds',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()->label('Access Only Obs Childs') ?>
                            </div>
                        </div>
                    </div>
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
