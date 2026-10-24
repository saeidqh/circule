<?php

use yii\helpers\Html;

$position = $networkModel->position;
$networkModel->loadHumanResource();
?>
<div class="grid-box grid-box-silver">
    <div class="grid-box-body">
        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'FullName') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->fullName) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Father') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->Father) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Employment Type') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->getEmploymentType($networkModel->EmploymentType)) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'NationalCode') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->NationalCode) ?></i>
            </div>

            <div class="col-lg-4 col-md-4 col-xs-12">
                <?= Yii::t('automation', 'Mobile') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->Mobile) ?></i>
            </div>

            <div class="col-lg-4 col-md-4">
                <?= Yii::t('automation', 'Company') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($networkModel->company->companyName) ?></i>
            </div>
        </div>

        <div class="row mb5">
            <div class="col-lg-12 col-md-12 col-xs-12">
                <?= Yii::t('automation', 'Position2') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode(isset($position) ? $position->fullTitle : '') ?></i>
            </div>
        </div>
        
        <div class="row mb5">
            <div class="col-lg-12 col-md-12 col-xs-12" style="margin-top: 10px; display: flex;">
                <div>
                    <?= Yii::t('automation', 'Network Type') ?>  &nbsp;
                </div>
                <?= Html::checkboxList('NetworkType', explode(',', $networkModel->NetworkType), $networkModel->networkTypeList, [
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
                if(r.indexOf('automation%2Fnetwork%2Fview') != -1 || r.indexOf('automation/network/view') != -1 ){
                    showMsgBoxInBottomRight('automation successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['automation/network/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#network-modal').modal('hide');
                $.pjax.reload({
                    container:'#network-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/network', 'type' => 'ToDo']) . "',
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
