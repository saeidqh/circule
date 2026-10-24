<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use kartik\detail\DetailView;
use app\components\Utility;
?>
<div class="row" style="text-align: left;height: 150px; overflow: scroll;border:1px solid #999">
    <div class="col-lg-12 cl-md-12">
        <div>
        <?= GridView::widget([
            'id'  => 'rider-document-grid2',
            'dataProvider' => $riderDocumentDataProvider,
            'columns' => [
                [
                    'class' => \kartik\grid\ActionColumn::className(),
                    'template' => "{download}",
                    'buttons' => [
                        'download' => function($url, $model, $key){
                            return Html::a('<i class="fa-arrow-down s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflowContract/workflowcontract/download-revision-document', 'peid' => Yii::$app->request->get('peid'), 'did' => $model->ID]), [
                                'data-pjax' => '0', 
                                'target' => '_blank',
                                "original-title" => "Next Action",
                                "data-toggle" => "tooltip"
                                ]);
                        }
                    ]
                ],
                [
                    'header' => 'File Name',
                    'attribute' => 'FileName',
                ],
                [
                    'header' => 'Description',
                    'value' => 'Description'
                ],
                [
                    'attribute' => 'Last Update Hijri',
                    'value' => 'lastUpdateHijri'
                ],
                [
                    'header' => 'User Name',
                    'attribute' => 'userIdRef1',
                    'value' => function($model){
//                        return Html::encode($model->user1->UserName);
                        return Html::encode($model->user1->fullName);
                    }
                ]
            ]
        ]); ?>
        </div>
    </div>
</div>
<?php
    echo $this->render('_actionOrdreMeasure', [
        'processEntityModel' => $processEntityModel
    ]);
?>
<!--<hr>-->
<div class="tabbable"> <!-- Only required for left/right tabs -->
    <ul class="nav nav-tabs">
        <li class="active"><a href="#box-step01" data-toggle="tab"><i class="im-contract"></i> Contract</a></li>
        <li><a href="#box-step02" data-toggle="tab"><i class="en-reply-all"></i> Riders</a></li>
        <li><a href="#box-step03" data-toggle="tab"><i class="im-bold"></i> Budget</a></li>
        <li><a href="#box-step04" data-toggle="tab"><i class="fa-dollar"></i> Invoice Process</a></li>
        <li><a href="#box-step05" data-toggle="tab"><i class="br-window"></i> Pms & Plan</a></li>
        <li><a href="#box-step06" data-toggle="tab"><i class="im-file"></i> Financial Code</a></li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="box-step01">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <?php 
                    if(app\components\Utility::isJson($processEntityModel->contract->SubContractorPersonTitle)){
                        $val=  json_decode($processEntityModel->contract->SubContractorPersonTitle);
                        $subContractorPersonTitle=  $val->SubContractorPersonTitle;
                    }else{
                        $subContractorPersonTitle= $processEntityModel->contract->SubContractorPersonTitle;
                    }
                    ?>
                    <?= DetailView::widget([
                        'id' => 'pro-detail',
                        'model' => $processEntityModel->contract,
                       
                        'attributes' => [
                            'ContractNo',
                            'ContractSubject',
                            [
                                'attribute' => 'SubContractorPersonTitle',
                                'label' => 'SubContractor',
                                'value' => Html::encode($subContractorPersonTitle),
                            ],
                            [
                                'value' => isset($processEntityModel->contract->signer) ? Html::encode($processEntityModel->contract->signer->PersonTitle):'',
                                'label' => 'Signer Company',
                                'captionOptions' => [
                                    'style' => 'text-align:left;'
                                ],
                            ],
                            [
                                'label' => 'Contract Date',
                                'label' => 'Start Date',
                                'value' => '<div class="form-horizontal">
                                                <div class="form-group">
                                                    <div class="col-lg-1 col-md-1"></div>
                                                    <div class="col-lg-5 col-md-5" style="padding:0px;">
                                                        '.Html::textInput('ContractDateHijri', explode(' ', $processEntityModel->contract->ContractDateHijri)[0], ['class' => 'form-control input-sm right-icon', 'disabled' => 'disabled']).'
                                                            <i class="fa-time s12 right-input-icon" style="right: 15px;"></i>
                                                    </div>
                                                    <div class="col-lg-1 col-md-1"></div>
                                                    <div class="col-lg-5 col-md-5" style="padding:0px;">
                                                        '.Html::textInput('ContractDate', explode(' ', $processEntityModel->contract->ContractDate)[0], ['class' => 'form-control input-sm right-icon', 'disabled' => 'disabled']).'
                                                            <i class="fa-time s12 right-input-icon" style="right: 15px;"></i>
                                                    </div>
                                                </div>
                                                </div>',
                                'format' => 'raw',
                            ],
                            [
                                'value' => (isset($processEntityModel->contract->eps1->EPSTitle) ? Html::encode($processEntityModel->contract->eps1->EPSTitle): '').(isset($processEntityModel->contract->eps2->EPSTitle) ? '-'.Html::encode($processEntityModel->contract->eps2->EPSTitle): '').(isset($processEntityModel->contract->eps3->EPSTitle) ? '-'.Html::encode($processEntityModel->contract->eps3->EPSTitle): ''),
                                'label' => 'EPS',
                            ],
                            [
                               
                                'value' => isset($processEntityModel->contract->contractCategory) ? Html::encode($processEntityModel->contract->contractCategory->ContractCategory):'',
                                'label' => 'Contract Category',
                            ],
                            [
                                'value' => isset($processEntityModel->contract->contractType) ? Html::encode($processEntityModel->contract->contractType->ContractTypeTitle):'',
                                'label' => 'Contract Type',
                            ],
//                            [
//                                'attribute'=>'assignmentType.assignmentTypeTitle',
//                                'value' => $processEntityModel->contract->assignmentType->assignmentTypeTitle,
//                                'label'=>'Assignment Type',
//                            ],
                            [
                                'attribute' => 'RequiredPMS',
                                'format' => 'raw',
                                'value' => ($processEntityModel->contract->RequiredPMS) ? '<i class="im-checkmark2" style="color:rgb(26, 189, 26)"></i>':'<i class="br-cancel" style="color:red"></i>',
                            ],
                            [
                                'attribute' => 'RequiredPlan',
                                'format' => 'raw',
                                'value' => ($processEntityModel->contract->RequiredPlan) ? '<i class="im-checkmark2" style="color:rgb(26, 189, 26)"></i>':'<i class="br-cancel" style="color:red"></i>',

                            ],
                        ],
                    ]);
                    ?>

                </div>
            </div>
        </div>

        <div class="tab-pane" id="box-step02" style="text-align:left;">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                <?= Html::a('<i class="en-history"></i> Show History','#', ['class' => 'btn btn-sm btn-info', 'onclick' => 'showhistory(this)']); ?>
                </div>
            </div>
            <div class="row" id="contract-rev-box" style="padding:0px;text-align:left;margin-bottom:5px;display: none;">
                <div class="col-lg-12 col-md-12">
                    <?= Html::label('Contract Revision') ?>
                    <?= GridView::widget([
                        'id' => 'grid-riderhistory-contract',
                        'dataProvider' => $contractRevisionDataProvider,
                        'pjax' => true,
                        'pjaxSettings' => [
                            'options' => [
                                'id' => 'rider-history-pjax',
                            ]
                        ],
                        'columns' => [
                            [
                                'class' => \kartik\grid\ExpandRowColumn::className(),
                                'value' => function($url, $model, $key) {
                                    return \kartik\grid\GridView::ROW_COLLAPSED;
                                },
                                'detailUrl' => Yii::$app->urlManager->createAbsoluteUrl(['contract/rider/view-only-rider']),
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'expandOneOnly' => true,
                                'expandIcon' => '<span class="fa-plus"></span>',
                                'collapseIcon' => '<span class="fa-minus"></span>',
                            ],
                            [
                                'attribute' => 'RevisionNumber',
                                'header' => 'Rev.No',
                            ],
                            [
                                'header' => 'Rev.Type',
                                'attribute' => 'RevisionType',
                                'value' => 'type.ContractRevisionType',
                            ],
                            [
                                'attribute' => 'RevisionDateHijri',
                                'header' => 'Issue Date',
                                'value' => 'RevisionDateHijri',
                            ],
                            [
                                'attribute' => 'FinishDateHijri',
                                'header' => 'Finish Date',
                            ],
                            [
                                'header' => 'Duration',
                                'attribute' => 'Duration',
                                'value' => function($model){
                                    return Html::encode($model->Duration).' '.(isset($model->durationUnit) ? Html::encode($model->durationUnit->DurationUnit):'');
                                },
                            ],
                            [
                                'attribute' => 'Price1',
                                'header' => 'Price',
                                'format' => 'raw',
                                'value' => function($model){
                                    return '<div class="row">'
                                                . '<div class="col-lg-12 col-md-12">'
                                                    . Yii::$app->utility->moneyFormat($model->Price1).' '.Html::encode($model->currency1->CurrencyTitle)
                                                . '</div>'
                                                . '<div class="col-lg-12 col-md-12">'
                                                    . ((isset($model->Price2) && isset($model->currency2)) ? Yii::$app->utility->moneyFormat($model->Price2).' '.Html::encode($model->currency2->CurrencyTitle): '')
                                                . '</div>'
                                            . '</div>';
                                }
                            ],
                            [
                                'attribute' => 'Description',
                                'header' => 'Description'
                            ]
                        ]
                    ]); ?>
                </div>
                <div class="col-lg-12 col-md-12">
                    <?= Html::label('Workflow Contract Revision') ?>
                    <?= GridView::widget([
                        'id' => 'grid-riderhistory-contract-Revision',
                        'dataProvider' => $workflowContractDataProvider,
                        'pjax' => true,
                        'pjaxSettings' => [
                            'options' => [
                                'id' => 'grid-riderhistory-contract-Revision-pjax'
                            ]
                        ],
                        'columns'  => [
                            [
                                'class' => \kartik\grid\ExpandRowColumn::className(),
                                'value' => function($url, $model, $key) {
                                    return \kartik\grid\GridView::ROW_COLLAPSED;
                                },
                                'detailUrl' => Yii::$app->urlManager->createAbsoluteUrl(['workflowContract/ajax/view-only-rider']),
                                'headerOptions' => ['class' => 'kartik-sheet-style'],
                                'expandOneOnly' => true,
                                'expandIcon' => '<span class="fa-plus"></span>',
                                'collapseIcon' => '<span class="fa-minus"></span>',
                            ],
                            [
                                'attribute' => 'RevisionNumber',
                                'label' => 'Rev.No',
                                'header' => 'Rev.No',
                            ],
                            [
                                'attribute' => 'contract.RevisionType',
                                'value' => 'revisionType.ContractRevisionType',
                                'header' => 'Rev.Type',
                            ],
                            [
                                'attribute' => 'RevisionDateHijri',
                                'label' => 'Issue Date',
                                'value' => 'RevisionDateHijri',
                                'header' => 'Issue Date',
                            ],
                            [
                                'attribute' => 'FinishDateHijri',
                                'header' => 'Finish Date',
                            ],
                            [
                                'attribute' => 'Duration',
                                'header' => 'Duration',
                                'value' => function($model){
                                    $durationUnit= isset($model->durationUnit) ? $model->durationUnit->DurationUnit : '' ;
                                    return $model->Duration.' '.$durationUnit;
                                },
                            ],
                                [
                                'attribute' => 'Price1',
                                'header' => 'Price',
                                'label' => 'Price',
                                'format' => 'raw',
                                'value' => function($model){
                                    $cur1=isset($model->currency1) ? Html::encode($model->currency1->CurrencyTitle) : '';
                                    $cur2=isset($model->currency2) ? Html::encode($model->currency2->CurrencyTitle) : '';
                                    return '<div class="row">'
                                                . '<div class="col-lg-12 col-md-12">'
                                                    . Yii::$app->utility->moneyFormat($model->Price1).' '.$cur1
                                                . '</div>'
                                                . '<div class="col-lg-12 col-md-12">'
                                                    . Yii::$app->utility->moneyFormat($model->Price2).' '.$cur2
                                                . '</div>'
                                            . '</div>';
                                },
                                'options' => [
                                    'width' => '55px;'
                                ]
                            ],
                            [
                                'attribute' => 'Description',
                                'header' => 'Description'
                            ],
                        ]
                    ]); ?>
                </div>
            </div>
            
            
            
            <?= DetailView::widget([
                'id'  => 'contract-detail',
                'model' => $processEntityModel->contract,
                'attributes' => [
                    'RevisionNumber',
                    [
                        'value' => isset($processEntityModel->contract->revisionType) ? Html::encode($processEntityModel->contract->revisionType->ContractRevisionType):'',
                        'label' => 'Revision Type',
                    ],
                    [
                        'label' => 'Issue Date',
                        'value' => (isset($processEntityModel->contract->RevisionDate) ? Html::encode($processEntityModel->contract->RevisionDate):'').(isset($processEntityModel->contract->RevisionDateHijri) ? ' '.Html::encode($processEntityModel->contract->RevisionDateHijri):'')
                    ],
                    [
                        'label' => 'Finsh Date',
                        'value'  => (isset($processEntityModel->contract->FinishDate) && isset($processEntityModel->contract->FinishDateHijri)) ? Yii::$app->utility->date($processEntityModel->contract->FinishDate).' '.Html::encode($processEntityModel->contract->FinishDateHijri):'',
//                        'value' => (isset($processEntityModel->contract->FinishDate) ? $model->FinishDate:''.(isset($processEntityModel->contract->FinishDateHijri) ? ' '.$processEntityModel->contract->FinishDateHijri:''))
                    ],
                    'Description',
                    [
                        'value' => Html::encode($processEntityModel->contract->Duration) . (isset($processEntityModel->contract->durationUnit->DurationUnit) ? ' '.Html::encode($processEntityModel->contract->durationUnit->DurationUnit) : ''),
                        'label' => 'Duration',
                    ],
                    [
                        'format' => 'raw',
                        'value' => '<div class="form-horizontal">
                                        <div class="form-group">
                                            <div class="col-lg-5 col-md-5" style="padding:0px;">
                                                '.((!empty($processEntityModel->contract->Price1) && isset($processEntityModel->contract->currency1->CurrencyTitle)) ? 
                                                        Html::textInput('', Yii::$app->utility->moneyFormat($processEntityModel->contract->Price1), ['class' => 'form-control input-sm right-icon', 'disabled' => 'disabled']).' <i class="right-input-icon" style="right: 15px;">'.Html::encode($processEntityModel->contract->currency1->CurrencyTitle).'</i>'
                                                        : '').'    
                                            </div>
                                            <div class="col-lg-12 col-md-12"></div>
                                            <div class="col-lg-5 col-md-5" style="padding:0px;">
                                                '.((!empty($processEntityModel->contract->Price2) && isset($processEntityModel->contract->currency2->CurrencyTitle)) ? 
                                                            Html::textInput('', Yii::$app->utility->moneyFormat($processEntityModel->contract->Price2), ['class' => 'form-control input-sm right-icon', 'disabled' => 'disabled']).'<i class="right-input-icon" style="right: 15px;">'.Html::encode($processEntityModel->contract->currency2->CurrencyTitle).'</i>'
                                                            : '').'  
                                            </div>
                                        </div>
                                        </div>',
                        'label' => 'Price',
                    ],
                    [
                        'value' => ($processEntityModel->contract->hasAdjustment) ? '<i class="im-checkmark2" style="color:rgb(26, 189, 26)"></i>':'<i class="br-cancel" style="color:red"></i>',
                        'label' => 'hasAdjustment',
                        'format' => 'raw',
                    ],
                    [
                        'visible' => ($processEntityModel->contract->hasAdjustment) ? true:false,
                        'label' => 'AdjustmentPrice',
                        'value' => (isset($processEntityModel->contract->AdjustmentPrice1) ? Yii::$app->utility->moneyFormat($processEntityModel->contract->AdjustmentPrice1): '').(isset($processEntityModel->contract->AdjustmentPrice2) ? '-'.Yii::$app->utility->moneyFormat($processEntityModel->contract->AdjustmentPrice2): '')
                    ],
                    [
                        'visible' => ($processEntityModel->contract->hasAdjustment) ? true:false,
                        'format' => 'raw',
                        'label' => 'Adjustment Start Date',
                        'value' => '<div class="row">'.(isset($processEntityModel->contract->AdjustmentStartDate) ? '<div class="col-lg-6 col-md-6">'.Html::textInput('', $processEntityModel->contract->AdjustmentStartDate, ['class' => 'form-control input-sm', 'disabled' => 'disabled']).'</div>': '').(isset($processEntityModel->contract->AdjustmentStartDateHijri) ? '<div class="col-lg-6 col-md-6">'.Html::textInput('', $processEntityModel->contract->AdjustmentStartDateHijri, ['class' => 'form-control input-sm', 'disabled' => 'disabled']).'</div>': '').'</div>'
                    ],
                    [
                        'visible' => ($processEntityModel->contract->hasAdjustment) ? true:false,
                        'format' => 'raw',
                        'label' => 'Adjustment Finish Date',
                        'value' => '<div class="row">'.(isset($processEntityModel->contract->AdjustmentFinishDate) ? '<div class="col-lg-6 col-md-6">'.Html::textInput('', $processEntityModel->contract->AdjustmentFinishDate, ['class' => 'form-control input-sm', 'disabled' => 'disabled']).'</div>': '').(isset($processEntityModel->contract->AdjustmentFinishDateHijri) ? '<div class="col-lg-6 col-md6">'.Html::textInput('', $processEntityModel->contract->AdjustmentFinishDateHijri, ['class' => 'form-control input-sm', 'disabled' => 'disabled']).'</div>': '').'</div>'
                    ],
                ],
            ]);
            ?>
        </div>
        
        <div class="tab-pane text-left" id="box-step03">
            <!--begin budget tab-->
            <?=
            kartik\grid\GridView::widget([
                'id' => 'contract-budget-grid',
                'options' => [
                    'style' => 'font-family:tahoma; font-size:11px;',
                ],
                'hover' => true,
                'pjax' => true,
                'pjaxSettings' => [
                    'options' => [
                        'id' => 'contract-budget-grid-pjax',
                    ]
                ],
                'dataProvider' => $contractNodeBudgetProvider,
                'columns' => [
                    [
                        'attribute' => 'nodeFullCode',
                        'label' => 'CostCode',
                        'format' => 'raw',
                        'value' => function($model) {
                            return isset($model->costcode) ? Utility::generateCostCodeLink($model->costcode->NodeID,$model->costcode->NodeFullCode) : '';
                        },
                        'options' => [
                            'width' => '120px',
                        ]
                    ],
                    [
                        'attribute' => 'ownerContractNo',
                        'label' => 'Owner',
                        'format' => 'raw',
                        'value' => function($model) {
                            if($model->AnotherContractIsOwner == 1){
                                return '<table class="inner-table" width="100%" ><tr><td width="50%"  class="right-border">'
                                    . Html::encode($model->owner->ContractNo)
                                    . '</td><td  width="50%">'
                                    . Html::encode($model->owner->subContractor->PersonTitle)
                                    . '</td></tr><tr class="top-border" ><td colspan="2" class="label-silver" >'
                                    . Html::encode($model->owner->ContractSubject)
                                    . '</td></tr></table>';
                            }
                            return '<table class="inner-table" width="100%" ><tr><td width="50%"  class="right-border">'
                                    . Html::encode($model->contract->ContractNo)
                                    . '</td><td  width="50%">'
                                    . Html::encode($model->contract->SubContractorPersonTitle)
                                    . '</td></tr><tr class="top-border" ><td colspan="2" class="label-silver" >'
                                    . Html::encode($model->contract->ContractSubject)
                                    . '</td></tr></table>';
                        }
                    ],
                    [
                        'header' => 'Owner Price',
                        'format' => 'raw',
                        'value' => function($model) {
                            $currncy1 = '';
                            $currncy2 = '';
                            if (!empty($model->AnotherContractIsOwner)) {
                                $currncy1 = $model->owner->lastRider->currency1->CurrencyTitle;
                                if (!empty($model->owner->lastRider->currency2)) {
                                    $currncy2 = $model->owner->lastRider->currency2->CurrencyTitle;
                                }
                            } else {
                                $currncy1 = $model->contract->currency1->CurrencyTitle;
                                if (!empty($model->contract->currency2)) {
                                    $currncy2 = $model->contract->currency2->CurrencyTitle;
                                }
                            }
                            return '
                                <table class="inner-table">
                                    <tbody>
                                        <tr>
                                            <td width="200">' . Yii::$app->utility->moneyFormat($model->OwnerPrice1) . '</td>
                                            <td class="label-silver">' . Html::encode($currncy1) . '</td>
                                        </tr>
                                        <tr class="top-border"><td>' . Yii::$app->utility->moneyFormat($model->OwnerPrice2) . '</td>
                                        <td class="label-silver">' . Html::encode($currncy2) . '</td>
                                        </tr>
                                    </tbody>
                                </table>';
                        },
                        'options' => [
                            'width' => '130',
                        ]
                    ],
                    array(
                        'attribute' => 'executerContractNo',
                        'label' => 'Executer',
                        'format' => 'raw',
                        'value' => function($model) {
                            if($model->AnotherContractIsExecuter == 1){
                               return '<table class="inner-table" width="100%" ><tr><td width="50%"  class="right-border">'
                                    . Html::encode($model->executer->ContractNo)
                                    . '</td><td  width="50%">'
                                    . Html::encode($model->executer->subContractor->PersonTitle)
                                    . '</td></tr><tr class="top-border" ><td colspan="2" class="label-silver" >'
                                    . Html::encode($model->executer->ContractSubject)
                                    . '</td></tr></table>';
                            }
                            return '<table class="inner-table" width="100%" ><tr><td width="50%"  class="right-border">'
                                    . Html::encode($model->contract->ContractNo)
                                    . '</td><td  width="50%">'
                                    . Html::encode($model->contract->SubContractorPersonTitle)
                                    . '</td></tr><tr class="top-border" ><td colspan="2" class="label-silver" >'
                                    . Html::encode($model->contract->ContractSubject)
                                    . '</td></tr></table>';
                        }
                    ),
                    array(
                        'header' => 'Executer Price',
                        'format' => 'raw',
                        'value' => function($model) {
                            $currncy1 = '';
                            $currncy2 = '';
                            if (!empty($model->AnotherContractIsExecuter)) {
                                $currncy1 = $model->executer->lastRider->currency1->CurrencyTitle;
                                if (!empty($model->executer->lastRider->currency2)) {
                                    $currncy2 = $model->executer->lastRider->currency2->CurrencyTitle;
                                }
                            } else {
                                $currncy1 = $model->contract->currency1->CurrencyTitle;
                                if (!empty($model->executer->lastRider->currency2)) {
                                    $currncy2 = $model->executer->lastRider->currency2->CurrencyTitle;
                                }
                            }
                            return '
                                <table class="inner-table">
                                    <tbody>
                                        <tr>
                                            <td width="200">' . Yii::$app->utility->moneyFormat($model->ExecuterPrice1) . '</td>
                                            <td class="label-silver">' . $currncy1 . '</td>
                                        </tr>
                                        <tr class="top-border"><td>' . Yii::$app->utility->moneyFormat($model->ExecuterPrice2) . '</td>
                                        <td class="label-silver">' . $currncy2 . '</td>
                                        </tr>
                                    </tbody>
                                </table>';
                        },
                        'options' => array(
                            'width' => '130',
                        )
                    ),
                ],
            ]);?>
            <!--end budget tab-->
        </div>
        
        <div class="tab-pane" id="box-step04">
            <?=
            DetailView::widget([
                'model' => $processEntityModel->contract,
                'attributes' => [
                    [
                        
                        'value' => isset($processEntityModel->contract->process1) ? Html::encode($processEntityModel->contract->process1->ProcessTitle) :'',
                        'label' => 'موقت',
                    ],
                    [
                        'value' => isset($processEntityModel->contract->process2) ? Html::encode($processEntityModel->contract->process2->ProcessTitle) :'',
                        'label' => 'ما قبل قطعی',
                    ],
                    [
                        'value' => isset($processEntityModel->contract->process3) ? Html::encode($processEntityModel->contract->process3->ProcessTitle):'',
                        'label' => 'قطعی',
                    ],
                    [
                        'value' => isset($processEntityModel->contract->process4) ? Html::encode($processEntityModel->contract->process4->ProcessTitle):'',
                        'label' => 'تعدیل',
                    ],
                    [
                        'value' => isset($processEntityModel->contract->process5) ? Html::encode($processEntityModel->contract->process5->ProcessTitle):'',
                        'label' => 'قطعی تعدیل',
                    ],
                    [
                        'value' => isset($processEntityModel->contract->process6) ? Html::encode($processEntityModel->contract->process6->ProcessTitle):'',
                        'label' => 'LC,Logistic,Insurance,Service fee,Advanced',
                    ],
                ],
            ]);
            ?>
        </div>
        
        <div class="tab-pane" id="box-step05">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            PMS
                        </div>
                        <div class="panel-body">
                            <?php
                            if(isset($contractPMSModel)){
                                echo DetailView::widget([
                                    'model' => $contractPMSModel,
                                    'attributes' => [
                                        [
                                            'attribute' => 'RevisionNumber',
                                            'label' => 'PMS Rev.No',
                                        ],
                                        [
                                            'attribute' => 'PMSDateHijri',
                                            'label' => 'PMS DateHijri',
                                        ],
                                        [
                                            'attribute' => 'PMSStatus',
                                            'label' => 'PMS Status',
                                        ],
                                        [
                                            'attribute' => 'ContractPMSExcelFileName',
                                            'label' => 'PMS Excel FileName',
                                        ],
                                        [
                                            'attribute' => 'ContractPMSPDFFileName',
                                            'label' => 'PMS PDF FileName',
                                        ],
                                        [
                                            'attribute' => 'ExpireDateHijri',
                                            'label' => 'PMS Expire DateHijri',
                                        ],
                                        [
                                            'attribute' => 'ExpireDateHijri',
                                            'label' => 'PMS Expire DateHijri',
                                        ],
                                    ],
                                ]);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            PLAN
                        </div>
                        <div class="panel-body">
                            
                            <?php
                            
                            if(isset($contractPlanModel)){
                                echo DetailView::widget([
                                    'model' => $contractPlanModel,
                                    'attributes' => [
                                        [
                                            'attribute' => 'RevisionTitle',
                                            'label' => 'Plan Rev.Title',
                                        ],
                                        [
                                            'attribute' => 'PlanDateHijri',
                                            'label' => 'Plan DateHijri',
                                        ],
                                        [
                                            'attribute' => 'PlanStatus',
                                            'label' => 'Plan Status',
                                        ],
                                        [
                                            'attribute' => 'ContractPlanExcelFileName',
                                            'label' => 'Plan Excel FileName',
                                        ],
                                        [
                                            'attribute' => 'ContractPlanPDFFileName',
                                            'label' => 'Plan PDF FileName',
                                        ],
                                        [
                                            'attribute' => 'ContractPlanPrimaveraFileName',
                                            'label' => 'Plan Primavera FileName',
                                        ],
                                        [
                                            'attribute' => 'ExpireDateHijri',
                                            'label' => 'Plan Expire DateHijri',
                                        ],
                                        [
                                            'attribute' => 'Description',
                                            'label' => 'Plan Description',
                                        ],
                                    ],
                                ]);
                                
                            }
                            ?>  

                        </div>
                    </div>
                </div>
            </div>
        </div>    

        <div class="tab-pane" id="box-step06">
            <?=
            DetailView::widget([
                'model' => $processEntityModel->contract,
                'attributes' => [
                    'FinancialContractCode',
                    'FinancialContractTitle',
                    'FinancialSubContractorCode',
                    'FinancialSubContractorTitle',
                ],
            ]);
            ?>
        </div>
        
    </div>
</div>
<div class="pull-right">
    <?= Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
            'class' => 'btn btn-sm btn-success', 
            "onclick" => "sign(this)", 
            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
            ]); ?>
   
    <?= Html::a('<i class="en-back"></i> back', 
        Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action',
            'type' => Yii::$app->request->get('type'),
            'peid' => Yii::$app->request->get('peid'),
            'mode' => 'check', 'clearValue' => false]),
        ['class' => 'btn btn-sm btn-primary']) ?>&nbsp;
     <?= Html::a("<i class=\"br-cancel\"></i> Cancel", '#', 
        [
            'class' => 'btn btn-sm btn-warning',
            'data-dismiss' => 'modal'
        ]); ?>
</div>
<?php
$js = "
    function sign(obj){
        data = $('#fix-data').serialize();
        ajaxProgressLoader('show', '.modal-content');
        $.ajax({
            type: 'POST',
            url:$(obj).attr('ajax-url'),
            dataType: 'json',
            data: data,
        }).done(function(data){
            var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                var r = window.location.search;
                if(r.indexOf('workflowContract%2Fworkflowcontract%2Fview') != -1 || r.indexOf('workflowContract/workflowcontract/view') != -1 ){
                    showMsgBoxInBottomRight('Contract successfully went to the next step', 'success')
                    window.location = '".Yii::$app->urlManager->createAbsoluteUrl(['workflowContract/workflowcontract/view', 'peid' => Yii::$app->request->get('peid')])."';
                    return;
                }
                $('#contract-modal').modal('hide');
                $.pjax.reload({
                    container:'#contract-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/contract', 'type' => 'ToDo']) . "',
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
    var i=0;
    function showhistory(obj){
        if(i==0){
            $('#contract-rev-box').stop().slideDown(500);
            $(obj).html('<i class=\"en-history\"></i> hide History');
            i = 1;
        }
        else if(i==1){
            $('#contract-rev-box').stop().slideUp(500);
            $(obj).html('<i class=\"en-history\"></i> show History');
            i = 0;
        }
        return false;
    }
    
    
";
$this->registerJs($js, \yii\web\View::POS_READY);
