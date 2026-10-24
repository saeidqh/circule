<?php

use yii\helpers\Html;

if ($processEntityModel->budget->ContractIDRef > 0) {
    app\components\GridViewBox::begin();
    ?>
    <div class="row">

        <div class="col-md-6 col-md-6">
            <?php
            echo yii\widgets\DetailView::widget([
                'model' => $processEntityModel,
                'attributes' => [
                    [
                        'label' => 'BudgetID',
                        'value' => Html::encode($processEntityModel->budget->BudgetID),
                        'options' => ['style' => "margin-bottom: 0px"]
                    ],
                    [
                        'label' => 'Contract Subject',
                        'value' => Html::encode($processEntityModel->budget->contract->ContractSubject),
                        'options' => ['style' => "margin-bottom: 0px"]
                    ],
                    [
                        'label' => 'subContractor',
                        'value' => Html::encode($processEntityModel->budget->contract->subContractor->PersonTitle),
                        'options' => ['style' => "margin-bottom: 0px"]
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6 col-md-6">
            <?php
            echo yii\widgets\DetailView::widget([
                'model' => $processEntityModel,
                'attributes' => [
                    [
                        'label' => 'ContarctNo',
                        'value' => Html::encode($processEntityModel->budget->contract->ContractNo),
                        'options' => [
                            'style' => "margin-bottom: 0px"
                        ]
                    ],
                    [
                        'label' => 'EPS List',
                        'value' => Html::encode($processEntityModel->budget->contract->subProjectList),
                        'options' => [
                            'style' => "margin-bottom: 0px"
                        ]
                    ],
                    [
                        'label' => 'Contract DateHijri',
                        'value' => Html::encode($processEntityModel->budget->contract->ContractDateHijri),
                        'options' => [
                            'style' => "margin-bottom: 0px"
                        ]
                    ],
                ],
            ]);
            ?>
        </div>

    </div>
    <?php app\components\GridViewBox::end();
    ?>


    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
        <tr>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Price</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-price1', $processEntityModel->budget->Price1L, ['id' => 'budget-price1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Approved</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-approved1', $processEntityModel->budget->ac1, ['id' => 'budget-approved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Reserved</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-reserved1', $processEntityModel->budget->reserved1, ['id' => 'budget-reserved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Free</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-free1', $processEntityModel->budget->free1, ['id' => 'budget-free1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
        </tr>
        <?php if (isset($processEntityModel->budget->currency2)) { ?>
            <tr>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-price2', $processEntityModel->budget->Price2L, ['id' => 'budget-price2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-approved2', $processEntityModel->budget->ac2, ['id' => 'budget-approved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-reserved2', $processEntityModel->budget->reserved2, ['id' => 'budget-reserved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-free2', $processEntityModel->budget->free2, ['id' => 'budget-free2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
            </tr>
            <?php
        }
        if ($processEntityModel->budget->contract->hasAdjustment > 0) {
            ?>
            <tr>
                <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Adjustment Price</td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-price1', $processEntityModel->budget->AdjustmentPrice1L, ['id' => 'budget-adjustment-price1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
                <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Adjustment Approved</td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-approved1', $processEntityModel->budget->adjustmentAc1, ['id' => 'budget-adjustment-approved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
                <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Reserved</td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-reserved1', $processEntityModel->budget->adjustmentReserved1, ['id' => 'budget-adjustment-reserved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
                <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Free</td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-free1', $processEntityModel->budget->adjustmentFree1, ['id' => 'budget-adjustment-free1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            </tr>
            <?php if (isset($processEntityModel->budget->currency2)) { ?>
                <tr>
                    <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-price2', $processEntityModel->budget->AdjustmentPrice2L, ['id' => 'budget-adjustment-price2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                    <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-approved2', $processEntityModel->budget->adjustmentAc2, ['id' => 'budget-adjustment-approved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                    <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-reserved2', $processEntityModel->budget->adjustmentReserved2, ['id' => 'budget-adjustment-reserved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                    <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-adjustment-free2', $processEntityModel->budget->adjustmentFree2, ['id' => 'budget-adjustment-free2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                </tr>
                <?php
            }
        }
        ?> 
        <tr>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>" colspan="2"></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Income</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-income1', $processEntityModel->budget->income1, ['id' => 'budget-income1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Reserved</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-income-reserved1', $processEntityModel->budget->incomeReserved1, ['id' => 'budget-income-reserved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>" colspan="2"></td>
        </tr>
        <?php if (isset($processEntityModel->budget->currency2)) { ?>
            <tr>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-income2', $processEntityModel->budget->income2, ['id' => 'budget-income2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-income-reserved2', $processEntityModel->budget->incomeReserved2, ['id' => 'budget-income-reserved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
} elseif ($processEntityModel->budget->NewContractIDRef > 0) {
    app\components\GridViewBox::begin();
    ?>
    <div class="row">

        <div class="col-md-6 col-md-6">
            <?php
            echo yii\widgets\DetailView::widget([
                'model' => $processEntityModel,
                'attributes' => [
                    [
                        'label' => 'BudgetID',
                        'value' => Html::encode($processEntityModel->budget->BudgetID),
                        'options' => ['style' => "margin-bottom: 0px"]
                    ],
                    [
                        'label' => 'Contract Subject',
                        'value' => Html::encode($processEntityModel->budget->ncontract->NewContractSubject),
                        'options' => ['style' => "margin-bottom: 0px"]
                    ],
                    [
                        'label' => 'subContractor',
                        'value' => isset($processEntityModel->budget->ncontract->subContractor->PersonTitle)? Html::encode($processEntityModel->budget->ncontract->subContractor->PersonTitle) : '',
                        'options' => ['style' => "margin-bottom: 0px"]
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6 col-md-6">
            <?php
            echo yii\widgets\DetailView::widget([
                'model' => $processEntityModel,
                'attributes' => [
                    [
                        'label' => 'ContarctNo',
                        'value' => Html::encode($processEntityModel->budget->ncontract->NewContractNo),
                        'options' => [
                            'style' => "margin-bottom: 0px"
                        ]
                    ],
                    [
                        'label' => 'EPS List',
                        'value' => Html::encode($processEntityModel->budget->ncontract->eps->EPSTitle),
                        'options' => [
                            'style' => "margin-bottom: 0px"
                        ]
                    ],
                    [
                        'label' => 'Contract DateHijri',
                        'value' => Html::encode($processEntityModel->budget->ncontract->ContractDateHijri),
                        'options' => [
                            'style' => "margin-bottom: 0px"
                        ]
                    ],
                ],
            ]);
            ?>
        </div>

    </div>
    <?php app\components\GridViewBox::end();
    ?>
    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
        <tr>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Price</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-price1', $processEntityModel->budget->Price1L, ['id' => 'budget-price1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Reserved</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-reserved1', $processEntityModel->budget->reserved1, ['id' => 'budget-reserved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Free</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-free1', $processEntityModel->budget->free1, ['id' => 'budget-free1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
        </tr>
        <?php if (isset($processEntityModel->budget->currency2)) { ?>
            <tr>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-price2', $processEntityModel->budget->Price2L, ['id' => 'budget-price2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-reserved2', $processEntityModel->budget->reserved2, ['id' => 'budget-reserved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-free2', $processEntityModel->budget->free2, ['id' => 'budget-free2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
            </tr>
            <?php
        }
        ?> 
        <tr>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>" colspan="2">Income</td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>">Reserved</td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-income-reserved1', $processEntityModel->budget->incomeReserved1, ['id' => 'budget-income-reserved1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>"></td>
            <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>"></td>
        </tr>
        <?php if (isset($processEntityModel->budget->currency2)) { ?>
            <tr>
                <td><div class="input-group"><?= \kartik\helpers\Html::textInput('budget-income-reserved2', $processEntityModel->budget->incomeReserved2, ['id' => 'budget-income-reserved2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
            </tr>
            <?php
        }
        ?>
    </table>

    <?php
} elseif ($processEntityModel->budget->PlanIDRef > 0) {
    app\components\GridViewBox::begin();
    ?>
    <div class="row">

        <div class="col-lg-6">
            <?php
            echo \kartik\detail\DetailView::widget([
                'id' => 'plan-detail-view',
                'model' => $processEntityModel->budget->plan,
                'attributes' => [
                    [
                        'attribute' => 'PlanID',
                        'lable' => 'Plan Id',
                        'labelColOptions' => ['style' => 'width: 35%;text-align: left; vertical-align: middle;'],
                    ],
                    [
                        'attribute' => 'StartDate',
                        'labelColOptions' => ['style' => 'width: 40%;text-align: left; vertical-align: middle;'],
                        'label' => 'Start Date',
                    ],
                    [
                        'attribute' => 'StartDateHijri',
                        'label' => 'Start Date Hijri',
                    ],
                    [
                        'attribute' => 'PlanTitle',
                        'label' => 'Plan Title',
                        'lebel' => 'Amount',
                        'format' => 'raw',
                        'value' => '<div class="col-lg-8 money-input">' . \app\components\Utility::moneyFormatByCurrency($processEntityModel->budget->plan->PlanAmount, $processEntityModel->budget->plan->currency->CurrencyTitle) . '</div><div class="col-lg-4">' . Html::encode($processEntityModel->budget->plan->currency->CurrencyTitle) . '</div>'
                    ,
                    ]
                ],
            ]);
            ?>
        </div>
        <div class="col-lg-6">
            <?php
            echo \kartik\detail\DetailView::widget([
                'id' => 'plan-detail-view',
                'model' => $processEntityModel->budget->plan,
                'attributes' => [
                    [
                        'attribute' => 'PlanTitle',
                    ],
                    [
                        'attribute' => 'PlanType',
                        'lable' => 'Plan Type',
                    ],
                    [
                        'attribute' => 'PlanStatus',
                        'label' => 'Plan Status',
                    ],
                    [
                        'attribute' => 'costType',
                        'label' => 'cost Type',
                        'value' => Html::encode($processEntityModel->budget->plan->costTypeArray()[$processEntityModel->budget->plan->costType])
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
<div class="row">
    <div class="col-lg-12">
            <?php
            echo \kartik\detail\DetailView::widget([
                'id' => 'plan-detail-view',
                'model' => $processEntityModel->budget->plan,
                'attributes' => [
                    [
                        'attribute' => 'Description',
                        'lable' => 'Description',
                    ]
                ],
            ]);
            ?>
        </div>
</div>

    <?php
    app\components\GridViewBox::end();
    app\components\GridViewBox::begin(); ?>
<div class="row">
    <table class="kv-grid-table table table-bordered table-striped kv-table-wrap">
    <tr>
        <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>"><b>Total Price</b></td>

        <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>"><b>Annual Budget</b></td>
        <td rowspan="<?= isset($processEntityModel->budget->currency2) ? 2 : 1; ?>"><b>Remain Amount</b></td>
    </tr>
    <tr>
        <td><div class="input-group"><?= \kartik\helpers\Html::textInput('node-committed1', $processEntityModel->budget->Committed1, ['id' => 'node-committed1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
        <td><div class="input-group"><?= \kartik\helpers\Html::textInput('node-totalbudjet1', $processEntityModel->budget->totalBudget1, ['id' => 'node-totalBudget1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
        <td><div class="input-group"><?= \kartik\helpers\Html::textInput('node-remain1', $processEntityModel->budget->remain1, ['id' => 'node-remain1', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency1->CurrencyTitle) ?></span></div></td>
    </tr>
    <?php if (isset($budgetNodeModel->budget->currency2)) { ?>
        <tr>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('node-committed2', $processEntityModel->budget->Committed2, ['id' => 'node-committed2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('node-totalbudjet2', $processEntityModel->budget->totalBudget2, ['id' => 'node-totalBudget2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
            <td><div class="input-group"><?= \kartik\helpers\Html::textInput('node-remain2', $processEntityModel->budget->remain2, ['id' => 'node-remain2', 'class' => 'form-control money-input', 'disabled' => 'disabled']) . '<span class="input-group-addon">' . Html::encode($processEntityModel->budget->currency2->CurrencyTitle) ?></span></div></td>
        </tr>
    <?php } ?>
</table>
</div>
    <?php app\components\GridViewBox::end(); 
   
}
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
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
                ajaxProgressLoader('hide', '.modal-content');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('budget%2Fbudget%2Fview-budget') != -1 || r.indexOf('budget/budget/view-budget') != -1 ){
                    showMsgBoxInBottomRight('budget successfully went to the next step', 'success')
                    window.location = '" . Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/view-budget', 'peid' => Yii::$app->request->get('peid')]) . "';
                        return;
                }
                $('#budget-modal').modal('hide');
                $.pjax.reload({
                    container:'#budget-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/budget', 'type' => 'ToDo']) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
            }
            ajaxProgressLoader('hide', '.modal-content');
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide', '#check-modal');
        });
        return false;
    };
   
";
$this->registerJs($js, \yii\web\View::POS_READY);