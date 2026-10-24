<?php
use app\modules\workflow\assets\WorkflowListAsset;
use app\components\PageHeader;
use yii\widgets\Pjax;
use kartik\helpers\Html;
?>
    <div class='col-md-8 '>
        <div class='row action-list ' style='border-radius:4px; ;border:1px solid #C7C7C7;'>
                <div class="col-md-12" ><span style=''>
                        <i class="im-arrow-right2 s18"></i>&nbsp;
                        <b>Process Action List</b></span>
                </div>
                <?php
                    echo \kartik\grid\GridView::widget([
                        'id' => 'process-action-grid',
                        'dataProvider' => $actionDataProvider,
                        'filterModel' => $actionSearchModel,
                        'layout'=>"{items}\n{pager}\n{summary}",
                        'pjax' => true,
                        'floatHeader' => true,
                        'floatOverflowContainer' => true,
                        'pjaxSettings' => [
                            'options' => [
                                'id' => 'process-action-grid-pjax',
                            ],
                        ],
                        'columns' => [
                            [
                                'attribute' => 'ActionID',
                                'options' => [
                                    'width' => '30px'
                                ],
                            ],        
                            'ActionTitle1',  
                            'ActionTitle2',
                            [
                                'label' => 'Type',
                                'format' => 'raw',
                                'value'=>function($model){
                                    $value = '<span class="badge badge-primary mr10 mb10" style="width:46px" ><i class="fa-arrow-right s12"></i></span>';
                                    if($model->isStartAction ){
                                        $value='<span class="badge badge-success mr10 mb10" style="width:46px" >Start</span>';
                                    }else if($model->isEndAction ){
                                        $value='<span class="badge badge-danger mr10 mb10" style="width:46px" >End</span>';
                                    }
                                    return $value;
                                },
                                'options' => [
                                    'width' => '40px'
                                ],
                            ],
                            [
                                'attribute'=>'ActionOrder',
                                'options' => [
                                    'width' => '40px'
                                ],
                            ],
                            [
                                'class' => kartik\grid\ActionColumn::className(),
                                'template' => '{edit}&nbsp;&nbsp;{param}',
                                'options' => [
                                    'width' => '40px'
                                ],
                                'buttons' => [
                                    'edit' => function($key, $model, $index)use($processModel) { 
                                        return kartik\helpers\Html::a('<i class="fa-pencil s16"></i>', '#', [
                                            'data-pjax' => '0',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-original-title' => 'Edit',
                                            'onclick' => 'return btnEditProcessActionClick(this)',
                                            'data-url'=> Yii::$app->urlManager->createAbsoluteUrl(['workflow/action/update','aid'=>$model->ActionID,'pid' => $processModel->ProcessID]),
                                        ]);
                                    },
                                    'param' => function($key, $model, $index) {
                                        $controllersList =[
                                            1=>'param-invoice',
                                            2=>'param-cash-request',
                                            3=>'param-contract',
                                            4=>'param-budget',
                                            5=>'param-capital',
                                            6=>'param-warranty',
                                        ];
                                        return kartik\helpers\Html::a('<i class="fa-check s16"></i>', '#', [
                                            'data-pjax' => '0',
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'top',
                                            'data-original-title' => 'param',
                                            'onclick' => 'return btnEditProcessActionClick(this)',
                                            'data-url'=> Yii::$app->urlManager->createAbsoluteUrl(['workflow/process/'.$controllersList[$model->process->ProccessTypeIDRef],'aid'=>$model->ActionID]),
                                        ]);
                                    },
                                ],

                            ]
                                        
                        ]
                    ]);
                ?>

        </div>
    </div>
    <div class='col-md-4'>
        <div class='r ow action-order mr0' style='border-radius:4px; ;border:1px solid #C7C7C7;'>
            
            <div class='row' >
                <div class="col-md-12" >
                    <span ><i class="en-flow-branch s20" style="transform: rotate(90deg);"></i>&nbsp;
                        <b>Action Order List</b>
                    </span>
                </div>
            </div>
            <div class='row' >
                <div class="col-md-12" >
                
                    <?php
                        echo \kartik\grid\GridView::widget([
                            'id' => 'action-order-grid',
                            'dataProvider' => $actionOrderDataProvider,
                            'filterModel' => $actionOrderSearchModel,
                            'layout'=>"{items}\n{pager}\n{summary}",
                            'pjax' => true,
                            'floatHeader' => true,
                            'floatOverflowContainer' => true,
                            'pjaxSettings' => [
                                'options' => [
                                    'id' => 'action-order-grid-pjax',
                                ],
                            ],
                            'columns' => [
                                [
                                    'attribute'=>'ActionIDRef',
                                    'label'=>'Action ID',
                                ],  
                                [
                                    'attribute'=>'NextActionIDRef',
                                    'label'=>'Next Action ID',
                                ],  
                                [
                                    'class' => kartik\grid\ActionColumn::className(),
                                    'template' => '{edit}',
                                    'options' => [
                                        'width' => '40px'
                                    ],
                                    'buttons' => [
                                        'edit' => function($key, $model, $index) { 
                                            return kartik\helpers\Html::a('<i class="fa-pencil s16"></i>', '#', [
                                                        'data-pjax' => '0',
                                                        'data-toggle' => 'tooltip',
                                                        'data-placement' => 'top',
                                                        'data-original-title' => 'Edit',
                                                        'onclick' => 'return btnEditActionOrderClick(this)',
                                                        'data-url'=> Yii::$app->urlManager->createAbsoluteUrl(['workflow/process/edit-action-order','aoid'=>$model->ID]),
                                                    ]);
                                        },
                                    ],

                                ]


                            ],
                        ]);
                    ?>
                </div>
            </div>
        </div>

    </div>
