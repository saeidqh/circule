<?php
use yii\helpers\Html;

$position = $automationModel->position;
$automationModel->loadHumanResource();
$title = $processEntityModel->ProcessIDRef == 72 ? 'New Automation' : ($processEntityModel->ProcessIDRef == 73 ? 'New Access Level' : 'Person Retake Automation');
$automation = $processEntityModel->ProcessIDRef == 72 ? 'automation' : ($processEntityModel->ProcessIDRef == 73 ? 'secret' : 'retake');
$requestDate = substr($automationModel->RequestDateHijri, 0, 10);
$humanObs = \app\modules\automation\models\HumanResourceObs::find()->with('obs')->where(['HumanIdRef' => $automationModel->HumanResourceIdRef, 'State' => 'Employment'])->andWhere(['<', 'LastUpdate', $requestDate])->andWhere("COALESCE(isDeleted,0)<>1")->all();
$oldPositionArray = [];
foreach ($humanObs as $ho) {
    if($processEntityModel->ProcessIDRef == 86 || $ho->ObsIdRef != $automationModel->Position) {
        $oldPositionArray[$ho->ObsIdRef] = $ho->ObsIdRef . ' - ' . $ho->obs->fullTitle . ' ' . $ho->obs->company->PersonTitle;
    }
}
$oldPositionArray['other'] = 'و غیرفعال سازی سایر پست های قبلی این شخص';

?>
    <div class="grid-box grid-box-silver ">
        <div class="grid-box-body">
            <div class="row mb5">
            <div class="col-md-5">نوع درخواست</div>

            <div class="col-md-7 badge badge-brown">
                <i class=""><?= Yii::t('automation', $title) ?></i>
            </div>
            </div>
            
            <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->fullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->getEmploymentType($automationModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->NationalCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Mobile') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->Mobile) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->company->companyName) ?></i>
            </div>
        </div>

        <?php if (!in_array($processEntityModel->LastActionIDRef, [992, 993]) && $processEntityModel->ProcessIDRef != 86) { ?>
        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Assistance') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->assistanceTitle) ?></i>
            </div>
            
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Position') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->fullTitle : '') ?></i>
            </div>
        </div>
        
        <?php } if ($processEntityModel->ProcessIDRef == 72) { ?>
        <div class="row mb5">
            <div class="col-lg-3 col-md-3 col-xs-12">
                <?= Yii::t('automation', 'Position Count') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->totalPositionCount : null) ?></i>
            </div>
            <div class="col-lg-3 col-md-3 col-xs-12">
                <?= Yii::t('automation', 'FirstLevelPositionCount') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->parentFirstLevelPositionCount : null) ?></i>
            </div>
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Assistance Full Name') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($automationModel->Assistance) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12">
                <?= Yii::t('automation', 'Old Full Name') ?>  &nbsp; <i class="badge badge-brown"><?= isset($automationModel->oldHumanResource) ? Html::encode($automationModel->oldHumanResource->fullName) : null; ?></i>
            </div>
            <?php if ($automationModel->HasAppointment == 0 || $automationModel->HasAppointment == 1) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 10px; display: flex;">
                <div>
                <?= Yii::t('automation', 'Has Appointment') ?>  &nbsp;
            </div>
                <?= Html::radioList('HasAppointment', $automationModel->HasAppointment, $automationModel->hasAppointmentList, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::radio($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]); ?>
            </div>
            <?php } ?>
        </div>
        <?php } ?>
            
        <div class="row mb5">
            <div class="col-lg-6 col-md-6 col-xs-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t('automation', 'Automation Type') ?>  &nbsp;
                </div>
                <?= Html::checkboxList('AutomationType', explode(',', $automationModel->AutomationType), $automationModel->automationTypeList, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]) ?>
            </div>
            <?php if ($processEntityModel->ProcessIDRef != 86) { ?>
            <div class="col-lg-6 col-md-6 col-xs-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t('automation', 'Sequerity Level') ?>  &nbsp;
                </div>
                <?= Html::checkboxList('SequerityLevel', explode(',', $automationModel->SequerityLevel), $automationModel->sequerityLevelList, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]); ?>
            </div>
            <?php }?>
        </div>
        <?php if ($processEntityModel->ProcessIDRef == 72 || $processEntityModel->ProcessIDRef == 86) { ?>
            <div class="row mb5">
                <div class="col-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t("automation", 'Old Automation Status') . '( ' . Yii::t("automation", 'Disable Old Position') . ' )' ?>  &nbsp;
                </div>
                <?= Html::checkboxList('DisableOldPosition', explode(',', $automationModel->DisableOldPosition), $oldPositionArray, [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]) ?>
                </div>
            </div>
        <?php } if ($processEntityModel->ProcessIDRef == 86) { ?>
            <div class="row mb5">
                <div class="col-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t("automation", 'Is Settlement') ?>  &nbsp;
                </div>
                <?= Html::radioList('IsSettlement', explode(',', $automationModel->IsSettlement), [0 => 'خیر', 1 => 'بله'], [
                    'item' => function($index, $label, $name, $checked, $value) {
                        return Html::checkbox($name, $checked, [
                            'label' => $label,
                            'value' => $value,
                            'disabled' => true
                        ]);
                    },
                ]) ?>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>

    <?php
        echo $this->render('_actionOrdreMeasure', [
            'processEntityModel' => $processEntityModel
        ]);
    ?>

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
                if(r.indexOf('automation%2Fautomation%2Fview') != -1 || r.indexOf('automation/automation/view') != -1 ){
                    showMsgBoxInBottomRight('automation successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['automation/automation/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                } else if(r.indexOf('automation%2Fretake-automation%2Fview') != -1 || r.indexOf('automation/retake-automation/view') != -1 ){
                    showMsgBoxInBottomRight('automation successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['automation/retake-automation/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#automation-modal').modal('hide');
                $.pjax.reload({
                    container:'#automation-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/automation', 'type' => 'ToDo', 'automation' => $automation]) . "',
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
