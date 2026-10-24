<?php

use yii\helpers\Html;

?>
    <div class="grid-box grid-box-silver ">
        <div class="grid-box-body">

            <div class="row mb5">
                <div class="col-md-3 text-left" style="padding-left:60px;">
                    <?php echo Yii::t("tender", 'Company'); ?>
                </div>
                <div class="col-md-3">
                    <?= Html::textInput('companyIDRef', $tenderModel->company->companyName, array(
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                    )) ?>
                </div>
                <div class="col-md-2 text-left">
                    <?php echo Yii::t("tender", 'Tender Type'); ?>
                </div>
                <div class="col-md-4">
                    <?= Html::textInput('tenderType', $tenderModel->getTenderType($tenderModel->tenderType), array(
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                    )) ?>
                </div>


            </div>
            <div class="row mb5">
                <div class="col-md-3 text-left" style="padding-left:60px;">
                    <?php echo Yii::t("tender", 'Subject'); ?>
                </div>
                <div class="col-md-9">
                    <?= Html::textInput('Subject', $tenderModel->subject, array(
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                    )) ?>
                </div>

            </div>
            <div class="row mb5">
                <div class="col-md-3 " style="padding-left:60px;">
                    <?php echo Yii::t("tender", 'Estimate Start Date'); ?>
                </div>
                <div class="col-md-3">
                    <?= Html::textInput('estimateStartDate', app\components\Utility::convertTimestampToJalali($tenderModel->estimateStartDate), array(
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                    )) ?>

                </div>
                <div class="col-md-2 ">
                    <?php echo Yii::t("tender", 'Authority Level'); ?>
                </div>
                <div class="col-md-4">
                    <?= Html::textInput('authorityLevel', $tenderModel->getAuthorityLevel($tenderModel->AuthorityLevel), array(
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                    )) ?>

                </div>
            </div>
        </div>
    </div>

    <?php
        echo $this->render('_actionOrdreMeasure', [
            'processEntityModel' => $processEntityModel
        ]);
    ?>

    <div class="tabbable"> <!-- Only required for left/right tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#box-step01" data-toggle="tab"><i class="im-stack" style="color:#FF8282 !important"></i> <?php echo Yii::t("tender", 'Documents'); ?></a></li>
            <li><a href="#box-step02" data-toggle="tab"><i class="en-flow-cascade" style="color:black !important"></i>  <?php echo Yii::t("tender", 'WBS'); ?></a></li>
            <li><a href="#box-step03" data-toggle="tab"><i class="st-folder-check" style="color:#af91e1 !important"></i><?php echo Yii::t("tender", 'Activites');?></a></li>
            <li><a href="#box-step04" data-toggle="tab"><i class="cando-handshake" style="color:deeppink !important"></i> <?php echo Yii::t("tender", 'Contract Strategy'); ?></a></li>
            <li><a href="#box-step05" data-toggle="tab"><i class="en-docs" style="color:darkgreen !important"></i> <?php echo Yii::t("tender", 'Financing') ?> </a></li>
            <li><a href="#box-step06" data-toggle="tab"><i class="im-stack" style="color:darkgreen !important"></i> <?php echo Yii::t("tender", 'Calling Document') ?> </a></li>
            <li><a href="#box-step07" data-toggle="tab"><i class="im-book" style="color:indigo !important"></i> <?php echo Yii::t("tender", 'Bidders') ?> </a></li>
            <li><a href="#box-step08" data-toggle="tab"><i class="fa-dollar" style="color:yellow !important"></i> <?php echo Yii::t("tender", 'Index Price') ?> </a></li>
            <li><a href="#box-step09" data-toggle="tab"><i class="en-users" style="color:grey !important"></i> <?php echo Yii::t("tender", 'Commission') ?> </a></li>
        </ul>
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
                if(r.indexOf('tender%2Ftender%2Fview') != -1 || r.indexOf('tender/tender/view') != -1 ){
                    showMsgBoxInBottomRight('Tender successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['tender/tender/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#tender-modal').modal('hide');
                $.pjax.reload({
                    container:'#tender-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/tender', 'type' => 'ToDo']) . "',
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
