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
<!--Evidence 4 -->
<?php if ($options['Evidence4']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence4Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence4Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence4No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence4No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence4Chek')->checkbox(); ?>
        </div>
    </div>
<?php } ?>
<!--Evidence 5 -->
<?php if ($options['Evidence5']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence5Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence5Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence5No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence5No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence5Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 6 -->
<?php if ($options['Evidence6']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence6Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence6Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence6No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence6No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4" style="padding-top:30px;"
        <?= $form->field($model, 'Evidence6Chek')->checkbox(); ?>
    </div>
    </div> 
<?php } ?>
<!--Evidence 7 -->
<?php if ($options['Evidence7']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence7Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence7Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence7No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence7No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence7Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 8 -->
<?php if ($options['Evidence8']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence8Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence8Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence8No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence8No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence8Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 9 -->
<?php if ($options['Evidence9']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence9Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence9Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence9No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence9No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence9Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 10 -->
<?php if ($options['Evidence10']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence10Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence10Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence10No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence10No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence10Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>

<!--Evidence 11 -->
<?php if ($options['Evidence11']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence11Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence11Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence11No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence11No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence11Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>

<!--Evidence 12 -->
<?php if ($options['Evidence12']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence12Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence12Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence12No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence12No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence12Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 13 --> 
<?php if ($options['Evidence13']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence13Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence13Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence13No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence13No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence13Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 14 --> 
<?php if ($options['Evidence14']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence14Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence14Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence14No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence14No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence14Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence 15 --> 
<?php if ($options['Evidence15']) { ?>
    <div class="row">
        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence15Date')->textInput([
                'class' => 'form-control input-sm datepicker-fa',
                'placeholder' => $modelLabels['Evidence15Date'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?=
            $form->field($model, 'Evidence15No')->textInput([
                'class' => 'form-control input-sm',
                'placeholder' => $modelLabels['Evidence15No'],
            ]);
            ?>
        </div>

        <div class="col-lg-4 col-md-4">
            <?= $form->field($model, 'Evidence15Chek')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--Evidence Part  End--> 

<?php if ($options['Evidence1'] || $options['Evidence2'] || $options['Evidence3'] || $options['Evidence4'] || $options['Evidence5'] || $options['Evidence6'] || $options['Evidence7'] || $options['Evidence8'] || $options['Evidence9'] || $options['Evidence10'] || $options['Evidence11'] || $options['Evidence12'] || $options['Evidence13'] || $options['Evidence14'] || $options['Evidence15']) { ?>
    <hr class="field-row-split" />
<?php } ?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox01']) { ?>
        <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox01')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox01')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox02']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox02')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox02')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox03']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox03')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox03')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox04']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox04')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox04')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox05']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox05')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox05')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox06']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox06')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox06')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox07']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox07')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox07')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox08']) { ?>
            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox08')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>

            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox08')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox09']) { ?>

            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox09')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox09')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
    <div class="col-lg-12 col-md-12">
        <?php if ($options['checkBox10']) { ?>

            <div class="col-lg-4 col-md-4" style="margin-top: 17px;">
                <?=
                $form->field($model, 'checkBox10')->checkbox([
                    'value' => '1',
                    'uncheckValue' => '0',
                ]);
                ?>
            </div>
            <div class="col-lg-8 col-md-8">
                <?=
                $form->field($model, 'textBox10')->textInput([
                    'class' => 'form-control input-sm',
                ]);
                ?>
            </div>
        <?php } ?>
    </div>
</div>
<hr class="field-row-split" />
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
<!--CC1 Part Start -->
<?php if ($options['cc1']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?= $form->field($model, 'CcModirAmelalc')->textInput(); ?>
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
<?php } ?>
<!--CC2 Part Start -->
<?php if ($options['cc2']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcRaeisKargah')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
        </div>

        <div class="col-lg-3 col-md-3">
            <?=
            $form->field($model, 'CcCheckRaeisKargah')->checkbox([
                'value' => '1',
                'uncheckValue' => '0',
            ]);
            ?>
        </div>
    </div> 
<?php } ?>
<!--CC3 Part Start -->
<?php if ($options['cc3']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirHoghooghi')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
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
<?php } ?>
<!--CC4 Part Start -->
<?php if ($options['cc4']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirPeymankar')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
        </div>

        <div class="col-lg-3 col-md-3">
            <?=
            $form->field($model, 'CcCheckModirPeymankar')->checkbox([
                'value' => '1',
                'uncheckValue' => '0'
            ]);
            ?>
        </div>
    </div> 
<?php } ?>
<!--CC5 Part Start -->
<?php if ($options['cc5']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirBarnameRizi')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
        </div>

        <div class="col-lg-9 col-md-9">
            <?= $form->field($model, 'CcCheckModirBarnameRizi')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--CC6 Part Start -->
<?php if ($options['cc6']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirMohandesi')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
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
<?php } ?>


<!--CC7 Part Start -->
<?php if ($options['cc7']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?= $form->field($model, 'CcModirAmelalcPPI')->textInput(); ?>
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
<?php } ?>
<!--CC8 Part Start -->
<?php if ($options['cc8']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcRaeisKargahPPI')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
        </div>

        <div class="col-lg-3 col-md-3">
            <?=
            $form->field($model, 'CcCheckRaeisKargah')->checkbox([
                'value' => '1',
                'uncheckValue' => '0',
            ]);
            ?>
        </div>
    </div> 
<?php } ?>
<!--CC9 Part Start -->
<?php if ($options['cc9']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirHoghooghiPPI')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
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
<?php } ?>
<!--CC10 Part Start -->
<?php if ($options['cc10']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirPeymankarPPI')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
        </div>

        <div class="col-lg-3 col-md-3">
            <?=
            $form->field($model, 'CcCheckModirPeymankar')->checkbox([
                'value' => '1',
                'uncheckValue' => '0'
            ]);
            ?>
        </div>
    </div> 
<?php } ?>
<!--CC11 Part Start -->
<?php if ($options['cc11']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirBarnameRiziPPI')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
        </div>

        <div class="col-lg-9 col-md-9">
            <?= $form->field($model, 'CcCheckModirBarnameRizi')->checkbox(); ?>
        </div>
    </div> 
<?php } ?>
<!--CC12 Part Start -->
<?php if ($options['cc12']) { ?>
    <div class="row">
        <div class="col-lg-9 col-md-9">
            <?=
            $form->field($model, 'CcModirMohandesiPPI')->textInput([
                'class' => 'form-control input-sm',
            ]);
            ?>
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
<?php } ?>

<!--CC Part End -->


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
