<?php

$employeeSettlementModel->loadHumanResource();
$position = $employeeSettlementModel->position;

use yii\helpers\Html;
?>
    <div class="grid-box grid-box-silver ">
        <div class="grid-box-body">

            <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->FullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->getEmploymentType($employeeSettlementModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->NationalCode) ?></i>
            </div>
            
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'PersonnelCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->PersonnelCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($employeeSettlementModel->company->companyName) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Assistance OBS') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode((isset($employeeSettlementModel->assistance->positionType) ? $employeeSettlementModel->assistance->positionType->Title . ' ' : '') . $employeeSettlementModel->assistance->obsTitle) ?></i>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Organization Position') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode((isset($position->positionType) ? $position->positionType->Title . ' ' : '') . $position->obsTitle) ?></i>
            </div>
        </div>
        </div>
    </div>

    <div class="pull-right">
        <?=
        Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
            'class' => 'btn btn-sm btn-success',
            "onclick" => "sign(this)",
            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
        ]);
        ?>

        <?=
        Html::a('<i class="en-back"></i> back', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'peid' => Yii::$app->request->get('peid'), 'clearValue' => false]), [
            'class' => 'btn btn-sm btn-primary'
        ])
        ?>&nbsp;

        <?=
        Html::a("<i class=\"br-cancel\"></i> Cancel", '#', [
            'class' => 'btn btn-sm btn-warning',
            'data-dismiss' => 'modal'
        ]);
        ?>
    </div>


<?php
$js = "
    function sign(obj){
        ajaxProgressLoader('show', '.modal-content');
        data = $('#fix-data').serialize();
        $.ajax({
            type: 'POST',
            url: $(obj).attr('ajax-url'),
            dataType: 'json',
            data: data,
        }).done(function(data){
           var res = data.result;
            ajaxProgressLoader('hide', '.modal-content');
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('automation%employee-settlement%2Fview') != -1 || r.indexOf('automation/employee-settlement/view') != -1 ){
                    showMsgBoxInBottomRight('automation successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['automation/employee-settlement/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#employee-settlement-modal').modal('hide');
                $.pjax.reload({
                    container:'#employee-settlement-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/employee-settlement', 'type' => 'ToDo']) . "',
                    push:false,
                    replace:false,
                });
                showMsgBoxInBottomRight('successful sign and close!','success');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide',parentBox);
        });
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_READY);
