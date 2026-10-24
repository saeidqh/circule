<?php
use kartik\helpers\Html;
use yii\bootstrap\ActiveForm;
?>
<style >
    

</style>
<?php
    $form = ActiveForm::begin([
    'id' => 'param-invoice-form',
    'layout' => 'horizontal',
    'enableClientValidation' => true,
    'options' => [
        'data-pjax' => '1',
        'enctype' => 'multipart/form-data'
    ]
]);?>
<div class='row p5 mb5' style='background-color: #FAEBFF;' >
    <div class='col-md-12' >

        <div class='row ' >
            <div class='col-md-12' >
                <div class='row' >
                    <div class='col-md-2' >
                        <div class='row '>
                            <div style='background:#C966FB;border-top-left-radius: 5px;border-bottom-left-radius: 5px;height:100px;text-align: center;padding-top: 20px;'>
                                <span style='font-size:40px ;color:white;padding-top:50px;' >
                                    <?= $actionModel->ActionID ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class='col-md-10' >
                        <div class='row pt10' style='border:solid 1px #C966FB;height:100px;'>
                            <div class='col-md-2 pt5' >Title1:</div>
                            <div class='col-md-10 mb10' >
                                <?= Html::textInput('actionTitle1', $actionModel->ActionTitle1,[
                                        'id'=>'action-title-1',
                                        'class'=>'form-control input-sm',
                                        'disabled'=>true,
                                ]) ?>
                            </div>
                            <div class='col-md-2 pt5' >Title2:</div>
                            <div class='col-md-10' >
                                <?= Html::textInput('actionTitle2', $actionModel->ActionTitle2,[
                                        'id'=>'action-title-2',
                                        'class'=>'form-control input-sm',
                                        'disabled'=>true,
                                ]) ?>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <div class='clear' ></div>
</div>
<div class='row'>
    <div class='col-md-4'>

        <?= $form->field($actionParamPermissionModel, 'permission_amountStatus',['horizontalCssClasses' =>['label'=>'col-md-5','wrapper'=>'col-md-7']])
            ->dropDownList($actionParamPermissionModel->AMOUNT_STATUS_TYPES,
                ['prompt' =>(($actionParamPermissionModel->AMOUNT_STATUS_TYPES)?null:'...') ,]
            )->label('Amount Status') ?>
        <?php 
            echo $form->field($actionParamPermissionModel, 'permission_edit',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
                ->label('permission Edit') 
        ?>
        
        
    </div>
    <div class='col-md-4'>
        <?php 
            echo $form->field($actionParamPermissionModel, 'permission_insertDocument',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
                ->label('permission Insert Document') 
        ?>
        <?php 
            echo $form->field($actionParamPermissionModel, 'permission_insertAmount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
                ->label('permission Insert Amount') ;
                    
        ?>
        <?= $form->field($actionParamPermissionModel, 'showObsChilds',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
            ->label('Access Only Obs Childs') ?>
    </div>
    <div class='col-md-4 border-b'>
        <?php 
            \app\components\GridViewBox::begin();
        ?>
        <?php 
            echo $form->field($actionParamPermissionModel, 'permission_updateAmount',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
                ->label('permission Update Amount') 
        ?>
         <?php 
            \app\components\GridViewBox::begin(
                ["htmlOptions"=>["style"=>"margin: -3px 1px;"]
            ]);
        ?>
        <?php 
            echo $form->field($actionParamPermissionModel, 'permission_canEditRequest',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
                ->label('permission Can Edit Request ') 
        ?>
        <?php 
            echo $form->field($actionParamPermissionModel, 'permission_canEditApprove',['horizontalCssClasses' =>['label'=>'col-md-1','wrapper'=>'col-md-11 col-sm-offset-1']])->checkbox()
                ->label('permission Can Edit Approve') 
        ?>
        <?php 
            \app\components\GridViewBox::end();
        ?>
        <?php 
            \app\components\GridViewBox::end();
        ?>
    </div>
    

</div>


<hr class="p0 m0 mb5" style="border-top: 1px solid #C7C7C7"/>
<div class='row'>
    <div class='col-md-6 pull-right mb5'>
        <?= Html::submitButton('<i class="im-checkmark2"></i>&nbsp;Save', [
            'class'=>'btn btn-success btn-sm pull-right ml10'
        ]) ?>
        <?= Html::a('<i class="im-close"></i>&nbsp;Cancel','#', [
            'class'=>'btn btn-danger btn-sm pull-right',
            'data-dismiss'=>'modal'
        ]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>