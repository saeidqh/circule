<?php

use yii\helpers\Html;

?>
    <div class="grid-box grid-box-silver ">
        <div class="grid-box-body">

            <div class="row mb5">
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <?= Yii::t('contract', 'Contract No') ?>  &nbsp; <i class="badge badge-purple"><?= Html::encode($contractDocumentModel->contract->ContractNo) ?></i>
                </div>

                <div class="col-lg-6 col-md-6 col-xs-12">
                    <?= Yii::t('contract', 'Subject') ?>  &nbsp; <i class="badge badge-brown"><?= Html::encode($contractDocumentModel->contract->ContractSubject) ?></i>
                </div>
            </div>
            
            <div class="row mb5">
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <?= Yii::t('contract', 'Sub Contractor') ?> 
                    <i class="badge badge-pink">
                        <?= Html::encode($contractDocumentModel->contract->subcontractorsList) ?>
                    </i>
                </div>
                <div class="col-lg-6 col-md-6 col-xs-12">
                    <?= Yii::t('contract', 'Signer Company') ?>  &nbsp; <i class="badge badge-info"><?= Html::encode($contractDocumentModel->contract->signer->PersonTitle) ?></i>
                </div>
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
                if(r.indexOf('contract%2Fdocument%2Fview') != -1 || r.indexOf('contract/document/view') != -1 ){
                    showMsgBoxInBottomRight('document successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['contract/document/view', 'peid' => Yii::$app->request->get('peid')]) . "';
                    return;
                }
                $('#contract-closin-document-modal').modal('hide');
                $.pjax.reload({
                    container:'#contract-closin-document-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/contract-closing-document', 'type' => 'ToDo']) . "',
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
