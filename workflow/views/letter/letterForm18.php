<?php

use app\assets\DatepickerAsset;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\GridViewBox;

DatepickerAsset::register($this);
$modelLabels = $model->attributeLabels();
?>
<?php
$form = ActiveForm::begin([
    'id' => 'create-letter-form',
    'action' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/generate-letter', 'lid' => $lid, 'peid' => $peid]),
    'options' => [
        'class' => 'form-horizontal',
    ],
]);
?>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <?= $form->field($model, 'LetterTitle')->textInput(['class' => 'form-control', 'placeholder' => 'title...']) ?> 
    </div>
</div>
<hr class="field-row-split" />
<div class="row">
    <div class='col-md-6'>
        <?=
        $form->field($model, 'LetterDate')->textInput([
            'class' => 'form-control input-small datepicker-fa',
            'placeholder' => $modelLabels['LetterDate'],
        ]);
        ?>
    </div>
    <div class='col-md-6'>
        <?= $form->field($model, 'LetterNumber')->textInput(['placeholder' => 'number...']) ?> 
    </div>
</div>
<hr class="field-row-split" />
<?php if ($options['Description']) { ?>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => '7', 'placeholder' => 'description...']) ?> 
        </div>
    </div>
    <hr class="field-row-split" />                   
<?php } ?>
<!--Evidence Part  Start-->
<!--Evidence 1 -->
<?php if ($options['Evidence1']) { ?>
<div class="row">
    <div class="col-lg-4 col-md-4">
        <?=
        $form->field($model, 'Evidence1Date')->textInput([
            'class' => 'form-control input-sm datepicker-fa',
            'placeholder' => $modelLabels['Evidence1Date'],
        ]);
        ?>
    </div>

    <div class="col-lg-4 col-md-4">
        <?=
        $form->field($model, 'Evidence1No')->textInput([
            'class' => 'form-control input-sm',
            'placeholder' => $modelLabels['Evidence1No'],
        ]);
        ?>
    </div>

    <div class="col-lg-4 col-md-4">
        <?= $form->field($model, 'Evidence1Chek')->checkbox(); ?>
    </div>
</div> 
<?php } ?>
<!--Evidence 2 -->
<?php if ($options['Evidence2']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence2Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence2Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence2No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence2No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence2Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 3 -->
<?php if ($options['Evidence3']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence3Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence3Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence3No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence3No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence3Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>

    <!--Evidence Part  End--> 

    <?php if ($options['Evidence1'] || $options['Evidence2'] || $options['Evidence3'] ) { ?>
        <hr class="field-row-split" />
    <?php } ?>

    <div class="row">
        <div class="col-lg-1 col-md-1">
        </div>
        <?php if ($options['Penalty']) { ?>
            <div  class="col-lg-5 col-md-5">
                <?=
                $form->field($model, 'Penalty')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                    ($invoiceModel->cumulativePenalty1(6) <> 0)? : 'disabled' => 'disabled'
                ]);
                ?>
            </div>
        <?php } ?>
        <?php if ($options['Income']) { ?>
            <div  class="col-lg-6 ol-md-6">
                <?=
                $form->field($model, 'Income')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                    ($invoiceModel->cumulativeIncome1(6) <> 0)? : 'disabled' => 'disabled',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <?php if ($options['Penalty'] || $options['Income']) { ?>
        <hr class="field-row-split" />
    <?php } ?>



    <!--CC Part Start -->
        <div class="row">
            <div class="col-lg-9 col-md-9">
                <?=
                $form->field($model, 'CcMoavenMarbote')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>

            <div class="col-lg-3 col-md-3">
                <?=
                $form->field($model, 'CcCheckMoavenMarbote')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
        </div>

        
        <div class="row">
            <div class="col-lg-9 col-md-9">
                <?= $form->field($model, 'CcModirAmelNSR')->textInput(); ?>
            </div>

            <div class="col-lg-3 col-md-3">
                <?=
                $form->field($model, 'CcCheckModirAmelalc')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
        </div> 
        <div class="row">
            <div class="col-lg-9 col-md-9">
                <?= $form->field($model, 'CcModirHoghooghiNSR')->textInput(); ?>
            </div>

            <div class="col-lg-3 col-md-3">
                <?=
                $form->field($model, 'CcCheckModirHoghooghi')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
        </div> 
        <div class="row">
            <div class="col-lg-9 col-md-9">
                <?= $form->field($model, 'CcModieMohandesiNSR')->textInput(); ?>
            </div>

            <div class="col-lg-3 col-md-3">
                <?=
                $form->field($model, 'CcCheckModirMohandesi')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
        </div> 

    <!--CC Part End -->
            
            
<?= Html::a('<i class="br-cancel"></i> Cancel', '#', ['class' => 'btn btn-sm btn-warning pull-right ml10', 'data-dismiss' => 'modal']) ?>
<?= Html::submitButton('<span class="fa-envelope"></span> Create Letter', [
    'class' => 'btn btn-sm btn-success btn-create-letter pull-right ml10',
    'id' => 'btn-create-word-letter',

])?>
<?= Html::a('<span class="im-file-excel"></span> Create Income Excel', 
    Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/generate-letter', 'peid' => Yii::$app->request->get('peid'), 'lid' => 12]), [
        'class' => 'btn btn-sm btn-primary pull-right',
        'id' => 'btn-download-excel',
        'style' => 'display:none;',
        'target' => '_blank'    
]);?>            
<?php ActiveForm::end(); ?>                     

<script type="text/javascript">
    $(".datepicker-fa").each(function () {
        $(this).datepicker({dateFormat: "yy/mm/dd"});
    });

    $('#letter-modal').modal('show');
    $('#letterform-income').click(function () {
        if ($(this).is(':checked')) {
            $('#btn-download-excel').show();
        }
        else {
            $('#btn-download-excel').hide();
        }
    });
    
    $('#btn-create-word-letter').click(function(){
        var income = $('#letterform-income');
        if(!income.is(':checked')){
            $('#letter-modal').modal('hide');
        }
        $('#create-letter-form').submit();
        return false;
    });
</script>


<style type="text/css" >
    .field-row-split{
        /*        box-shadow: 0 -3px 5px rgba(86, 114, 130, 0.7);*/
        margin: 4px 0;
        border-color: #75B9E6;
    }
</style>
