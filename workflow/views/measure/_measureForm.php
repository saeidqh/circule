<?php

use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style >

</style>
<?php
$form = ActiveForm::begin([
            'id' => 'measure-form',
            'layout' => 'horizontal',
            'options' => [
                'data-pjax' => '1',
            ]
        ]);
?>

<div class='row' >
    <div class="col-md-10" >
        <?=
        $form->field($measureModel, 'MeasureTitle')
        ?>
    </div>

</div>

<hr class="p0 m0 mb5" style="border-top: 1px solid #C7C7C7"/>
<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        
        <?= Html::submitButton($measureModel->isNewRecord ? '<i class="fa-plus"></i> Create' : '<i class="fa-edit"></i> Update', ['class' => $measureModel->isNewRecord ? 'btn btn-sm btn-success pull-right ml10' : 'btn btn-sm btn-primary pull-right ml10']) ?>
        <?=
        Html::a('<i class="im-close"></i>&nbsp;Cancel', '#', [
            'class' => 'btn btn-danger btn-sm pull-right',
            'data-dismiss' => 'modal'
        ])
        ?>
    </div>
</div>
<?php ActiveForm::end(); ?>