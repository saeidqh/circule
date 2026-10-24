<?php

use app\components\PageHeader;
use yii\helpers\Html;
use app\components\Utility;
use yii\widgets\Pjax;
use yii\helpers\Url;
use app\assets\JdateTimeAsset;
use app\assets\DatepickerAsset;
use yii\bootstrap\Modal;
use omnilight\assets\SweetAlertAsset;

app\modules\workflow\assets\BudgetAsset::register($this);
SweetAlertAsset::register($this);
DatepickerAsset::register($this);
JdateTimeAsset::register($this);
Yii::$app->view->on(\yii\web\View::EVENT_END_BODY, function() {
    Yii::$app->view->registerAssetBundle(app\modules\budget\assets\BudgetGridViewAsset::className());
});
$this->title = "WORKFLOW :: Budget todo list";

$this->params['breadcrumbs'] = [
    'leble' => 'WORKFLOW',
];
$obsId = Yii::$app->session->get('ObsPosition');
$isCandoAdmin = \app\access\AccessControl::isCandoAdmin($obsId);
?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>

<div class="row">
    <div class="col-md-6 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
        
        <?php
            echo Html::a("<i class='fa-plus'></i> &nbsp;".Yii::t('budget', 'New Budget'), "#", [
                'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                'id' => 'btn-new-budget',
                'onClick' => 'showNewBudgetSelectorModal(this)',
                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/new-budget-selector']),
            ]);
            echo Html::a(Yii::t('budget', 'Budget Status'), "#", [
                'class' => 'btn btn-sm pull-left btn-mini btn-999999 ml5',
                'id' => 'btn-budget-status',
                'onClick' => 'showBudgetStatusModal(this)',
                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['budget/report/general']),
            ]);
        ?>
        
        <button type="button" id="btnExportFile" class="btn btn-sm pull-left btn-mini btn-success ml5" data-toggle="dropdown"><i class="im-file-excel"></i>&nbsp;&nbsp;Excel <span class="caret"></span></button>
        <ul class="dropdown-menu text-left" role="menu" style="width: 100px; left: 224px !important; background-color: #E2E1D2;z-index: 10000;">
            <li>
                <?=
                yii\bootstrap\Html::a('<span class="im-file-excel"></span> '.Yii::t('budget', 'Budget Plan'), '#', [
                    'target' => '_blank',
                    'export-url' => Yii::$app->urlManager->createAbsoluteUrl(["budget/export/plan-export",'type'=>Yii::$app->request->get('type')]),
                    'onClick' => 'exportExcelFile(this);',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                    'data-pjax' => 0,
                    'data-original-title' => 'برنامه و بودجه عملیاتی سالیانه',
                ]);
                ?>
            </li>
            <li>
                <?=
                yii\bootstrap\Html::a('<span class="im-file-excel"></span> '.Yii::t('budget', 'Budget Plan2'), '#', [
                    'target' => '_blank',
//                    'export-url' => Yii::$app->urlManager->createAbsoluteUrl(["budget/export/plan-export-departement",'type'=>Yii::$app->request->get('type')]),
//                    'onClick' => 'showExportExcelDepartementModal(this);',
                    'id' => 'showExportExcelDepartementModal',
                    'data-toggle' => 'tooltip',
                    'data-placement' => 'left',
                ]);
                ?>
            </li>
        </ul>
        <?php
        echo Html::a("<i class='fa-plus'></i> &nbsp;Import Plan", Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/import-plan']), [
                'class' => 'btn btn-sm pull-left btn-mini btn-999999',
                'id' => 'btn-new-budget',
                'style' => 'display: none',
                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/import-plan']),
            ]);
        ?>
        <?php 
        if(app\access\AccessControl::can('wbs/eps/project-picker-list')){
            echo Html::a("<i class='fa-circle'></i>&nbsp;Income Budget", "#", [
                'class' => 'btn btn-sm pull-left btn-mini btn-info ml5',
                'id' => 'btn-budget-income',
                'onClick' => 'return btnIncomeBudgetClick(this)',
                'data-url' => Yii::$app->urlManager->createAbsoluteUrl(['wbs/eps/project-picker-list']),
            ]);
        }
        ?>
        
        

    </div>
    <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
        <?php
        $btns = $budgetModel->BUDGET_TODO_REPORT_TYPE;
        for ($i = 0; $i < count($btns); $i++) {
            ?>
            <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-pjax="0" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/budget", 'type' => $btns[$i]['value']]) ?>">
                <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                <?= Yii::t('workflow', $btns[$i]['value']); ?>
            </a>    
            <?php
        }
        ?>
    </div>

</div>
<div class="row">
    <?php Pjax::begin(['id' => 'budget-pjax']); ?>
    <?=
    \kartik\grid\GridView::widget([
        'id' => 'grid-budget-todo',
        'dataProvider' => $budgetDataProvider,
        'filterModel' => $budgetModel,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $class = $index % 2 ? 'odd' : 'even';
            return ['index' => $index, 'class' => $class.' dblclick'];
        },
        'floatHeader' => true,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => [
                    'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                ],
                'headerOptions' => [
                    'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                ],
                'contentOptions' => [
                    'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                ],
                'filterOptions' => [
                    'hidden'=>(Yii::$app->request->get('type') == 'ToDo') ?false: true,
                ],
            ],
            [
                'attribute' => 'fullId',
                'label' => Yii::t('budget','ID'),
                'value' => 'budget.fullId'
            ],
            [
                'attribute' => 'costType',
                'label' => Yii::t('budget','Cost Type'),
//                'filter' => Html::activeDropDownList($budgetModel, 'type', $budgetModel->BUDGET_TYPE, ['class' => 'form-control', 'prompt' => '...', 'style' => 'height:28px; font-size: 9px; padding: 3px;',]),
//                'filter' => Html::dropDownList('amount-status-' , ['Cost Type' => 'Cost Type'], $costTypeArray, ['class' => 'form-control']),
                'value' => function($model) {
                    return $model->budget->costType;
                },
                'options' => [
                    'width' => '85px',
                ],
            ],
            /*[
                'attribute' => 'type',
                'filter' => Html::activeDropDownList($budgetModel, 'type', $budgetModel->BUDGET_TYPE, ['class' => 'form-control', 'prompt' => '...', 'style' => 'height:28px; font-size: 9px; padding: 3px;',]),
                'label' => Yii::t('budget','Type'),
                'value' => 'budget.type',
                'options' => [
                    'width' => '85px',
                ],
            ],*/
            [
                'attribute' => 'title',
                'label' => Yii::t('budget','Title'),
                'value' => 'budget.title'
            ],
            /*[
                'attribute' => 'budgetUserName',
                'label' => 'UserName',
                'value' => 'budget.user.UserName',
            ],*/
            [
                'attribute' => 'last User',
                'label' => Yii::t('workflow','Last User'),
//                'value' => 'lastUser.UserName',
                'value' => 'lastUser.fullName',
                'options' => [
                    'width' => '100pxpx',
                ],
            ],
            [
                'attribute' => 'LastActionDateHijri',
                'label' => Yii::t('workflow','LastActionDate'),
                'value' => 'LastActionDateHijri',
            ],
            [
                'attribute' => 'lastActionTitle',
                'label' => Yii::t('workflow','Last Action'),
                'value' => 'lastAction.ActionTitle1',
            ],
            [
                'attribute' => 'epsTitle',
                'label' => Yii::t('budget','EPS'),
                'value' => 'budget.lastEps.EPSTitle',
            ],
            [
                'attribute' => 'obsTitle_475',
                'label' => Yii::t('budget','Departement'),
                'value' => 'budget.departement.obsTitle',
//                'value' => 'budget.lastEps.EPSTitle',
            ],
            [
//                'attribute' => 'budgetObs',
                'label' => Yii::t('budget','Budget Amount'),
                'format' =>'raw',
                'value' => function($model) {
                    return '<table class="inner-table">'
                        . '<tr><td width=\'200\'>'
                        . Utility::moneyFormatByCurrency($model->budget->totalBudget1 - $model->budget->income1Amount, $model->budget->Currency1IDRef)
                        . '</td><td>' . $model->budget->currency1->CurrencyTitle . '</td></tr>'
                        . '<tr><td width=\'200\'>'
                        . (isset($model->budget->Currency2IDRef)? Utility::moneyFormatByCurrency($model->budget->totalBudget2 - $model->budget->income2Amount, $model->budget->Currency2IDRef):"")
                        . '</td><td>' . (isset($model->budget->Currency2IDRef)? $model->budget->currency2->CurrencyTitle : "") . '</td></tr>'
                        . '</table>';
                }
            ],
            [
                'attribute' => 'SystemDescription',
                'label' => Yii::t('budget','SystemDescription'),
                'format' => 'raw',
                'value' => function($model) {
                    return Utility::generateTextShorterSpan($model->budget->SystemDescription, 30);
                },
//                'options' => [
//                    'width' => '250px',
//                ]
            ],
            [
                'header' => Yii::t('workflow','Actions'),
                'class' => yii\grid\ActionColumn::className(),
                'template' => '{nextaction}{diagram}{detail}{delete}',
                'options' => [
                    'width' => '80px',
                ],
                'buttons' => [
                    'nextaction' => function($url, $model, $key) {
                        return (Yii::$app->request->get('type') == 'ToDo') ? Html::a(
                                        '<i class="im-arrow-right2 rotate-360 s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'type' => 'budget', 'peid' => $model['ProcessEntityID']]), [
                                    'onclick' => "showModal(this); return  false;",
                                    'class' => 'nextAction btn btn-xs tip',
                                    'data-original-title' => "Next Action",
                                    'data-toggle' => 'tooltip',
                                    'style' => 'color:rgb(51, 122, 183);padding:1px 2px;',
                                    'data-pjax' => "0",
                                    'data-peid' => $model['ProcessEntityID'],
                                    'data-rowid' => $model['BudgetIDRef'],
                                        ]
                                ) : '';
                    },
                    'diagram' => function($url, $model, $key) {
                        return Html::a('<i class="en-flow-branch s16"></i>', Url::to(['/workflow/workflow/graph', 'peid' => $model['ProcessEntityID']]), [
                                    'data-original-title' => 'Action Diagram',
                                    'data-toggle' => 'tooltip',
                                    'class' => 'btn btn-xs',
                                    'data-pjax' => '0',
                                    'style' => 'padding:1px 2px;color:#646464;',
                                    'target' => '_blank',
                        ]);
                    },
                    'detail' => function($url, $model, $key) {
                        return Html::a('<i class="im-bold" style="font-size:18px"></i>', Url::to(['/budget/budget/view-budget', 'peid' => $model['ProcessEntityID']]), [
                                    'data-original-title' => 'Budget Detail',
                                    'data-toggle' => 'tooltip',
                                    'class' => 'btn btn-xs tip',
                                    'data-pjax' => '0',
                                    'style' => 'padding:1px 2px;color:#7b79e0;',
                                    'target' => '_blank',
                                    'onclick' => 'return hideNextActionBox(' . $model["ProcessEntityID"] . ');',
                        ]);
                    },
                    'delete' => function($key, $model, $index)use($isCandoAdmin){
                        $candelete = app\access\AccessControl::can('budget/budget/delete-confirm', ['peid' => $model['ProcessEntityID']]); 
                        if((Yii::$app->request->get('type') == 'All' && $isCandoAdmin) || (Yii::$app->request->get('type') == 'ToDo' && $candelete)){
                            return Html::a(
                                '<i class="fa-trash" style="font-size:18px"></i>',"#", [
                                    'class' => 'btn btn-xs tip',
                                    'data-original-title' =>"Budget Delete",
                                    'data-toggle' => 'tooltip',
                                    'data-pjax' => '0',
                                    'style' => 'color:#466FA3;padding:1px;',
                                    'delete-url' => Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/delete-confirm','peid' => $model['ProcessEntityID']]),
                                    'onclick' => 'showDeleteBudgetModal(this)',
                            ]);
                        }
                    }        
                ]
            ],
        ],
    ]);
    ?> 
    <?php Pjax::end(); ?>
</div>


<?php
\yii\bootstrap\Modal::begin([
    'id' => 'new-budget-selector-modal',
    'size' => 'modal-lg',
    'footer' => '<button id="btn-new-budget" class="btn btn-primary btn-sm btn-mini " onclick="newBudget()"  >'. Yii::t('budget', 'Select') .'</button><button class="btn btn-danger btn-sm btn-mini " data-dismiss=modal>'. Yii::t('budget', 'Close') .'</button>',
]);
echo '<div id="new-budget-selector-modal-content"></div>';
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'header' => '<h5 class="p0 m0">Plan</h5>',
    'id' => 'new-budget-selector-plan-form-modal',
    'size' => 'modal-lg',
    'options' => [ 
        'tabindex' => false,
    ],
]);
Pjax::begin([
    'id' => 'new-budget-selector-plan-form-pjax'
]);

Pjax::end();
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'header' => '<h5 class="p0 m0">Cost Breakdown Structure</h5>',
    'id' => 'new-budget-selector-cost_breakdown_structure-form-modal',
    'options' => [
        'tabindex' => false,
    ],
]);
Pjax::begin([
    'id' => 'new-budget-selector-cost_breakdown_structure-form-pjax'
]);
Pjax::end();
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'header' => '<h5 class="p0 m0">Plan Activity</h5>',
    'id' => 'new-budget-selector-plan-activity-form-modal',
    'options' => [
        'tabindex' => false,
    ],
]);

Pjax::begin([
    'id' => 'new-budget-selector-plan-activity-form-pjax'
]);
Pjax::end();

\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'id' => 'new-budget-selector-ncontract-form-modal',
    'options' => [
        'tabindex' => false,
    ],
    'footer' => '<button class="btn btn-danger btn-sm btn-mini " data-dismiss=modal>Close</button>',
]);
Pjax::begin([
    'id' => 'new-budget-selector-ncontract-form-pjax'
]);
Pjax::end();
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'id' => 'new-budget-selector-ncontract-budget-form-modal',
    'header' => '<h4>New Contract - Detail Price</h4>',
]);
Pjax::begin([
    'id' => 'new-budget-selector-ncontract-budget-form-pjax'
]);
Pjax::end();
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'id' => 'budget-status-modal',
    'header' => '<h4>Budget Status</h4>',
    'footer' => '<button class="btn btn-danger btn-sm btn-mini " data-dismiss=modal>Close</button>',
]);
Pjax::begin([
    'id' => 'budget-status-pjax'
]);
Pjax::end();
\yii\bootstrap\Modal::end();



\yii\bootstrap\Modal::begin([
    'id' => 'budget-status-detail-modal',
    'size' => 'modal-lg',
    'header' => '<h4>Budget Status Detail</h4>',
    'footer' => '<button class="btn btn-danger btn-sm btn-mini " data-dismiss=modal>Close</button>',
]);
Pjax::begin([
    'id' => 'budget-status-detail-pjax'
]);
Pjax::end();
\yii\bootstrap\Modal::end();

\yii\bootstrap\Modal::begin([
    'id' => 'project-modal',
    'size' => 'modal-lg',
    'header' => '<h4>Select Project</h4>',
    'footer' => '<button class="btn btn-danger btn-sm btn-mini " data-dismiss=modal>Close</button>'.
                '<button '.
                    'class="btn btn-success btn-sm btn-mini "'.
                    'id="btn-select-project"'.
                    'onclick="return btnIncomeSelectProject(this)"'.
                '>Select</button>',
]);
    Pjax::begin([
        'id' => 'project-pjax'
    ]);
    Pjax::end();
\yii\bootstrap\Modal::end();


$js = "
    //_ncontractBudgetGrid.php
    $('#new-budget-selector-ncontract-budget-form-pjax').on('pjax:complete', function(e, d) {
        if (d.responseText == '1') {
            $('#new-budget-selector-ncontract-budget-form-modal').modal('hide');
            $.pjax.reload({
                url: ncontract_update_url,
                container: '#new-budget-selector-ncontract-form-pjax',
                replace: false,
            });
            showMsgBoxInBottomRight('Budget saved successfully!', 'success');
        } else if (d.responseText == '0') {
            showMsgBoxInBottomRight('Error in saving budget!', 'Error');
        }
        ajaxProgressLoader('hide', '#new-budget-selector-ncontract-budget-form-pjax');
    });

    function showNcontractBudgetModal(obj) {
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
        }).done(function(data) {
            $('#new-budget-selector-ncontract-budget-form-pjax').html(data);
            $('#new-budget-selector-ncontract-budget-form-modal').modal('show');
        }).error(function() {
            showMsgBoxInBottomRight(\"Error on connecting to server.\", \"error\");
            return false;
        });


    }
    function deleteNcontractBudget(id) {
        var url = $('#delete-ncontract-budget-' + id).attr('data-url');
        $.ajax({
            url: url,
        }).done(function(data) {
            if (data.result == 1) {
                $.pjax.reload({
                    container: '#new-budget-selector-ncontract-form-pjax',
                    url: ncontract_update_url,
                    timeout: false,
                    push: false,
                    replace: false,
                });
                setTimeout(\"showMsgBoxInBottomRight('\" + data.message + \"', 'success')\", 300);
            } else {
                setTimeout(\"showMsgBoxInBottomRight('\" + data.message +\"', 'error')\", 300);
            }
        }).error(function() {
            showMsgBoxInBottomRight(\"Error on connecting to server.\", \"error\");
            return false;
        });
        return false;
    }
    
    //_ncontractBudgetForm.php
    $('#btn-ncontract-budget-submit').mousedown(function() {
        ajaxProgressLoader('show', '#new-budget-selector-ncontract-budget-form-pjax');
        $('#ncontract-budget-form').submit();
        ajaxProgressLoader('hide', '#new-budget-selector-ncontract-budget-form-pjax');
    });
    ";
$this->registerJs($js, \yii\web\View::POS_END);


\yii\bootstrap\Modal::begin([
    'id' => 'delete-budget-modal',
]);
\yii\bootstrap\Modal::end();


\yii\bootstrap\Modal::begin([
    'id' => 'export-budget-departement-modal',
    'header' => '<h4>Export Budget Departement</h4>',
    'footer' => '<button class="btn btn-danger btn-sm btn-mini " data-dismiss=modal>Close</button>'.
                '<button '.
                    'href="#"'.
                    'class="btn btn-success btn-sm btn-mini "'.
                    'id="btn-select-project"'.
                    'onclick="return exportExcelDepartementFile(this)"'.
                '>Export</button>',
]);
//var_dump($departementArray);
echo 'Departement';
echo kartik\widgets\Select2::widget([
    'name'=>'departementIds',
    'data' => $departementArray,
    'size' => \kartik\select2\Select2::SMALL,
    'options' => [
        'id' => 'departementIds',
        'prompt' => 'Select a departement...',
//        'multiple' => TRUE,
    ]
]);
\yii\bootstrap\Modal::end();

?>

<script>z
    $('#new-budget-selector-plan-form-pjax').on('pjax:complete', function (e, d) {
        if (d.responseText == '1') {
            $('#new-budget-selector-plan-form-modal').modal('hide');
            $.pjax.reload({
                url: '<?php echo Yii::$app->urlManager->createAbsoluteUrl(['budget/budget/new-budget-selector']); ?>',
                container: '#select-plan-grid-pjax',
                replace: false,
            });
            showMsgBoxInBottomRight('Plan saved successfully!', 'success');
        } else if (d.responseText == '0') {
            showMsgBoxInBottomRight('Error in saving plan!', 'Error');
        }
        ajaxProgressLoader('hide', '#new-budget-selector-plan-form-pjax');
    });
    
    function showDeleteBudgetModal(obj){
        $.ajax({
            url: $(obj).attr('delete-url'),
        }).done(function (data) {
            $('#delete-budget-modal .modal-body').html(data);
            $('#delete-budget-modal').modal('show');
        });
        return false;
    }

    function showBudgetStatusDetailModal(obj) {
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
        }).done(function (data) {
            $('#budget-status-detail-pjax').html(data);
            $('#budget-status-detail-modal').modal('show');
            return false;
        }).error(function () {
            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
            return false;
        });
    }
    function showBudgetStatusModal(obj) {
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
        }).done(function (data) {
            $('#budget-status-pjax').html(data);
            $('#budget-status-modal').modal('show');
            return false;
        }).error(function () {
            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
            return false;
        });
    }
    function showNewBudgetSelectorModal(obj) {
        var url = $(obj).attr('data-url');
        $.ajax({
            url: url,
        }).done(function (data) {
            if (typeof data == 'object') // JASON !!
            {
                showMsgBoxInBottomRight(data.message, 'error');
                return false;
            } else
            {
                $('#new-budget-selector-modal .modal-body').html(data);
                $('#new-budget-selector-modal').modal('show');
                return false;
            }
        }).error(function () {
            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
            return false;
        });
    }

    function newBudget() {
        var url = "<?= Yii::$app->urlManager->createAbsoluteUrl(["budget/budget/new-budget", "id" => '{id}', 'type' => '{type}']); ?>";
        url = url.replace('%7Bid%7D', $('#btn-new-budget').data('id') + '');
        url = url.replace('%7Btype%7D', $('#btn-new-budget').data('type') + '');
        $.ajax({
            url: url,
        }).done(function (data) {
            if (data.result == 1) {
                var url = "<?= Yii::$app->urlManager->createAbsoluteUrl(["budget/budget/view-budget", "peid" => '{peid}']); ?>";
                url = url.replace('%7Bpeid%7D', data.peid + '');
//                document.location.href = urwindow
//                window.open(url, '_blank');
                window.open(url, '_blank');
                return false;
            } else {
                setTimeout("showMsgBoxInBottomRight('" + data.message + "', 'error')", 300);
                return false;
            }
        }).error(function () {
            showMsgBoxInBottomRight('Limited Access or error in connecting to server', 'error');
            return false;
        });
    }

</script>
<?php
Modal::begin([
    'id' => 'budget-modal',
    'size' => Modal::SIZE_LARGE,
]);
Pjax::begin([
    'id' => 'pjax-modal',
]);
$js = "
        function showModal(obj){
            ajaxProgressLoader('show', '#budget-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                 $('#budget-modal').modal('show');
                 ajaxProgressLoader('hide', '#budget-pjax');
            });

            return false;
        }
    ";
$this->registerjs($js, \yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>
<?php
Modal::begin([
    'id' => 'budget-all-modal',
    'size' => Modal::SIZE_LARGE,
]);
Pjax::begin([
    'id' => 'pjax-all-modal',
]);
$js = "
        function showAllModal(obj){
            ajaxProgressLoader('show', '#budget-pjax');
            
            var href = $('.all-next-action').attr('href')+'&peids=';
            $('tbody tr td input[type=checkbox]').each(function(e, r){
                if($(this).is(':checked')){
                    var value = $(r).val();
                    href+=value+',';
                }
            });
            href = href.substr(0, href.length-1);
            
            var url = href;
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-all-modal').html(data);
                 $('#budget-all-modal').modal('show');
                 ajaxProgressLoader('hide', '#budget-pjax');
            });

            return false;
        }
        
        
    ";
$this->registerjs($js, \yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>
<script>
    function allmodalCallbackPjax(data){
            if(data.result){
                $.pjax.reload({
                    container:'#budget-pjax',
                    timeout: false,
                    push: false,
                    replace: false,
                });
                swal({
                    title: 'Success',
                    type: 'success',
                    text: data.message,
                    timer: 1000,
                    showConfirmButton: false,
                });
                $('#budget-all-modal').modal('hide');
            }
            else{
                swal('Error', data.message, 'error');
                setTimeout(function(){
                    $('#budget-all-modal').modal('hide');
                }, 100);
            }
        }
        
    function exportExcelFile(selector) {
        var filter = $('#grid-budget-todo-filters select').serialize() +  '&' + $('#grid-budget-todo-filters input').serialize();
        var exportUrl = $(selector).attr('export-url') + '&' + filter;
        $(selector).attr('href', exportUrl);
    }
    $('#showExportExcelDepartementModal').click(function (){
        $('#btnExportFile').trigger("click");
        $('#export-budget-departement-modal').modal('show');
        return false; 
    });
    
    function exportExcelDepartementFile() {
        var departementIds = $('#departementIds').val();
        var url = '<?= Yii::$app->urlManager->createAbsoluteUrl(['budget/export/plan-export-departement', 'type' => Yii::$app->request->get('type')]) ?>';
        var filter = $('#grid-budget-todo-filters select').serialize() +  '&' + $('#grid-budget-todo-filters input').serialize();
        var exportUrl = url + '&' + filter + '&did=' + departementIds;
        window.open(exportUrl, '_blank');
    }
    
    var ids=[];
    $('#grid-budget-todo input[type=checkbox]').change(function(e){
        if($(this).is(':checked')){
            $('#cricle-next-action').show();
        }
        else{
            $('#cricle-next-action').hide();
        }
    });
    dubleclick();
    function dubleclick(){
        $('.dblclick').attr('data-toggle', 'tooltip');
        $('.dblclick').attr('data-placement', 'left');
        $('.dblclick').attr('data-original-title', 'Please double-click to select an item and next action');

        $('.dblclick').dblclick(function(){
            $(this).addClass('success');
            var radio = $(this).find('td').find('input:radio')[0];
            $(this).find('.nextAction').trigger('click');
        });
    }
    $('#budget-pjax').on('pjax:complete', function () {
            dubleclick();
            $('#grid-budget-todo input[type=checkbox]').change(function(e){
                if($(this).is(':checked')){
                    $('#cricle-next-action').show();
                }
                else{
                    $('#cricle-next-action').hide();
                }
            });
    });
    
</script>
<style>
    .all-next-action {
        position: fixed;
        z-index: 99;
        right: 10px;
        bottom: 10px;
    }
    #cricle-next-action{
        display: none;
    }
    .dblclick{
        cursor: pointer;
    }
</style>
<div id="cricle-next-action">
    <?= Html::a('<i class="im-arrow-up-right3 rotate-360" style="font-size:48px"></i>', 
            Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/all-next-action']), 
        [
            'class' => 'pull-right mb5 animated bounceInLeft all-next-action',
            'onclick' => 'showAllModal(this); return false;',
            'style' => 'color:#337ab7;',
            'data-pjax' => 0,
            "data-toggle" => "tooltip",
            "data-original-title" => "Go to selected item next action"
        ]); ?>

</div>

<?php 
    // the script of imcome budget
?>

<script>
    $(document).ready(function(){
        $('#project-pjax').on('pjax:complete', function () {
            incomeBudgetFormDubleClickHandle();
            
            var grid = $('#eps-grid');
            grid.on('grid.radiochecked', function(ev,key,val){
                $('#btn-select-project').attr('selected-item',val)
            });
            
        });
    });
        
    function btnIncomeBudgetClick(obj){
        $('#project-modal').modal('show');
        var url = $(obj).attr('data-url');
        $.pjax.reload({
            container: "#project-pjax",
            url: url,
            timeout: false,
            push: false,
            replace: false,
        });
        return false;
    }
    function incomeBudgetFormDubleClickHandle(){
        $('#project-pjax table tr').dblclick(function(){
            var radio = $(this).find('td').find('input:radio')[0];
            $(radio).trigger('click');
            $('#btn-select-project').trigger('click')
        });
    }
    function btnIncomeSelectProject(obj){
        var url = '<?= Yii::$app->urlManager->createAbsoluteUrl('budget/income/view') ?>';
        var value =  $(obj).attr('selected-item');
        url += '&pid='+value;
        document.location.href = url;
    }
    
</script>