<?php
use yii\helpers\Html;

if ($processEntityModel->sourcing->personType == 'True Person') {
        echo\kartik\detail\DetailView::widget([    
        'model' => $processEntityModel,    
        'attributes' => [
            [
                'value' => Html::encode($processEntityModel->sourcing->PersonID),
                'label' => 'Person ID',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->PersonTitle),
                'label' => 'Person Title',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->personType),
                'label' => 'person Type',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->nationalCode),
                'label' => 'National Code',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->address),	
                'label' => 'Address',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->email),
                'label' => 'Email',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->postalCode),
                'label' => 'Postal Code',
            ],
            [
                'value' => Html::encode($processEntityModel->sourcing->mobile),
                'label' => 'Mobile',
            ],
            
        ]

        ]);
}
 else if ($processEntityModel->sourcing->personType == 'Legal Person') {
    echo\kartik\detail\DetailView::widget([    
    'model' => $processEntityModel, 
    'attributes' => [
        [
            'value' => Html::encode($processEntityModel->sourcing->PersonID),
            'label' => 'Person ID',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->personType),
            'label' => 'person Type',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->PersonTitle),
            'label' => 'Person Title',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->companyNationalCode),
            'label' => 'Company National Code',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->registrationNumber),	
            'label' => 'Registration Number',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->economicCode),
            'label' => 'Economic ID',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->registrationDate),
            'label' => 'Registration Date',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->directorManager),
            'label' => 'Director Manager',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->contactPerson),
            'label' => 'Contact Person',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->boardChief),
            'label' => 'Board Chief',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->boardMember),
            'label' => 'Board Member',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->companyPhone),
            'label' => 'Company Phone',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->registrationPlace),
            'label' => 'Registration Place',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->email),
            'label' => 'Email',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->companyFax),
            'label' => 'Company Fax',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->postalCode),
            'label' => 'Postal Code',
        ],
        [
            'value' => Html::encode($processEntityModel->sourcing->companyAddress),
            'label' => 'Company Address',									
        ],
    ]
    ]);
}
?>

<div class="pull-right">
    <?= Html::a('<i class="fa-ok"></i> Sign and Close', '#', [
            'class' => 'btn btn-sm btn-success', 
            "onclick" => "sign(this)", 
            'ajax-url' => Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/sign', 'type' => Yii::$app->request->get('type'), 'peid' => Yii::$app->request->get('peid'), 'mode' => 'sign'])
            ]); ?>
   
    <?= Html::a('<i class="en-back"></i> back', 
            Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action','peid' => Yii::$app->request->get('peid'), 'clearValue' => false]),
            [
                'class' => 'btn btn-sm btn-primary'
            ]) ?>&nbsp;
        
    <?= Html::a("<i class=\"br-cancel\"></i> Cancel", '#', 
        [
            'class' => 'btn btn-sm btn-warning',
            'data-dismiss' => 'modal'
        ]); ?>
</div>
<?php
$js = "
    function sign(obj){
        var r = window.location.search;
        if(r.indexOf('obs%2Fperson%2Fview') != -1 || r.indexOf('obs/person/view') != -1 ){
//            window.location = '".Yii::$app->urlManager->createAbsoluteUrl(['obs/person/view', 'pid' => Yii::$app->request->get('pid')])."';
//            return;
            ajaxProgressLoader('show', '.modal-content');
            $.ajax({
                url:$(obj).attr('ajax-url'),
            }).done(function(data){
                var res = data.result;
                if(res != true){
                    showMsgBoxInBottomRight(data.message,'error');
                }
                else if(res == true){
                    var r = window.location.search;
                    if(r.indexOf('obs%2Fperson%2Fview') != -1 || r.indexOf('obs/person/view') != -1 ){
                        showMsgBoxInBottomRight('Person successfully went to the next step', 'success')
                        window.location = r;    
                        return;
                    }
                    $('#sourcing-nextaction-modal').modal('hide');
                    //$('a.next-action').css({'display': 'none'});
                    showMsgBoxInBottomRight('successful sign and close!','success');
                    ajaxProgressLoader('hide', '.modal-content');
                }
            }).error(function(){
                showMsgBoxInBottomRight('Error on loading data!','error');
                ajaxProgressLoader('hide',parentBox);
            });    
        } else {       
        ajaxProgressLoader('show', '.modal-content');
        $.ajax({
            url:$(obj).attr('ajax-url'),
        }).done(function(data){
           var res = data.result;
            if(res != true){
                showMsgBoxInBottomRight(data.message,'error');
            }
            else if(res == true){
                $('#sourcing-modal').modal('hide');
                $.pjax.reload({
                    container:'#sourcing-pjax',
                    timeout: false,
                    url: '" . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/sourcing', 'type' => 'ToDo']) . "',
                    push:false,
                    replace:false,
                });
                 showMsgBoxInBottomRight('successful sign and close!','success');
                 ajaxProgressLoader('hide', '.modal-content');
            }
        }).error(function(){
            showMsgBoxInBottomRight('Error on loading data!','error');
            ajaxProgressLoader('hide',parentBox);
        });
        }
        return false;
    };
";
$this->registerJs($js, \yii\web\View::POS_READY);