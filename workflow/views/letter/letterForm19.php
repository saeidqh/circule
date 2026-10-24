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



<div class="row">
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox01']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox01',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Commercial Invoice');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox01')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number01')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox02']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox02',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Inspection Certificate (IC)');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox02')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number02')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox03']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox03',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Inspection Release Note (IRN)');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox03')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number03')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox04']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox04',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Bill of lading (B/L)');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox04')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number04')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox05']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox05',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Packing list (P/L)');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox05')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number05')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox06']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox06',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Certificate of Origin (COO)');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox06')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number06')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox07']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox07',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Shipping Acknowledgment');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox07')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number07')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox08']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox08',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('APG/PBG formal letter of financial department');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox08')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number08')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
    <div class="col-lg-12 col-md-12" style="margin-bottom: -29px;">
        <?php if ($options['checkBox09']) { ?>
            <div class="col-lg-6 col-md-6" style="margin-top: 25px;direction: rtl;text-align: left;">
                <?=
                    $form->field($model, 'checkBox09',[
                        'labelOptions' => ["style"=>"direction: initial;"],
                    ])->checkbox([
                        'value' => '1',
                        'uncheckValue' => '0',
                    ])->label('Others (CI with VAT 1 set Original (1 page))');
                ?>
            </div>
            <div class="col-lg-4 col-md-4">
                <?=
                    $form->field($model, 'textBox09')->textarea([
                        'class' => 'form-control input-sm',
                        'style' => 'margin-left: -44px;width: 203px;',
                    ]);
                ?>
            </div>
            <div class="col-lg-1 col-md-1"  style="margin-top: 32px;direction: rtl;text-align: left;font-weight: bold;">
                <?= ":Page"; ?>
            </div>
            <div class="col-lg-1 col-md-1">
                <?=
                    $form->field($model, 'number09')->textInput([
                        'class' => 'form-control input-sm',
                        'type' => 'number',
                        'options' => ["style"=>"width: 54px;margin-left: -25px;"],
                        'style' => "width: 54px;margin-left: -25px;height: 48px;",
                    ]);
                ?>
            </div>
        <?php } ?>
    </div>
    
 
</div>
<hr class="field-row-split" />


<?= Html::a('<i class="br-cancel"></i> Cancel', '#', ['class' => 'btn btn-sm btn-warning pull-right ml10', 'data-dismiss' => 'modal']) ?>
<?=
Html::submitButton('<span class="fa-envelope"></span> Create Letter', [
    'class' => 'btn btn-sm btn-success btn-create-letter pull-right ml10',
    'id' => 'btn-create-word-letter',
])
?>
<?=
Html::a('<span class="im-file-excel"></span> Create Income Excel', Yii::$app->urlManager->createAbsoluteUrl(['workflow/letter/generate-letter', 'peid' => Yii::$app->request->get('peid'), 'lid' => 12]), [
    'class' => 'btn btn-sm btn-primary pull-right',
    'id' => 'btn-download-excel',
    'style' => 'display:none;',
    'target' => '_blank'
]);
?>            
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

    $('#btn-create-word-letter').click(function () {
        var income = $('#letterform-income');
        if (!income.is(':checked')) {
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
