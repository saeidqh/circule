<?php

use app\components\PageHeader;
use app\components\GridViewBox;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use app\components\Utility;
use yii\bootstrap\Modal;
use app\assets\JdateTimeAsset;
use app\assets\DatepickerAsset;

JdateTimeAsset::register($this);
DatepickerAsset::register($this);

$this->title = "WORKFLOW :: Claim todo list";

?>
<?=
PageHeader::widget([
    'title' => $this->title,
    'icon' => 'fa-tasks',
]);
?>
<style>
    .table thead tr{
        background: #FFB200 !important;
    }
</style>
<div class="row">
    <?php GridViewBox::begin() ?>

    <div class="row">
        <div class="col-md-4 col-sm-12 pull-left text-left" style="margin-top:-5px;margin-bottom: 5px;">
            <a href="#" onclick="newClaim();" data-toggle="modal" role="button" class="btn btn-sm pull-left btn-mini btn-999999" ><i class="fa-plus"></i> &nbsp;New Claim</a>
        </div>


        <div class="col-md-6 col-sm-12 pull-right text-right" style="margin-top:-5px;margin-bottom: 5px;">
            <?php
            $btns = $claimSearchModel->CLAIM_TODO_REPORT_TYPE;
            for ($i = 0; $i < count($btns); $i++) {
                ?>
                <a class="btn <?= ($type == $btns[$i]['value']) ? "btn-active" : "btn-gray"; ?> btn-sm tip mr5" data-toggle="tooltip" data-placement="top" title="<?php echo $btns[$i]['value']; ?>" href="<?php echo Yii::$app->urlManager->createAbsoluteUrl(["workflow/workflow/claim", 'type' => $btns[$i]['value']]) ?>">
                    <i class="<?php echo $btns[$i]['icon']; ?> s16"></i>
                    <?= Yii::t('workflow', $btns[$i]['value']); ?>
                </a>    
                <?php
            }
            ?>
        </div>

    </div>


    <div class="row">
        <?php Pjax::begin(['id' => 'claim-list-pjax']); ?>
        <?=
        \kartik\grid\GridView::widget([
            'id' => 'id-claim-list-grid',
            'dataProvider' => $claimDataProvider,
            'filterModel' => $claimSearchModel,
            'rowOptions' => function ($model, $key, $index, $grid) {
                $class = $index % 2 ? 'odd' : 'even';
                return ['index' => $index, 'class' => $class.' dblclick'];
            },
            'floatHeader' => true,
            'columns' => [
                [
                    'attribute' => 'id',
                    'label' => 'ID',
                    'value' => 'claim.id',
                    'options' => [
                        'width' => '35px',
                    ]
                ],
                [
                    'attribute' => 'contractNo',
                    'label' => 'Contract No',
                    'value' => 'claim.contract.ContractNo',
                    'options' => [
                        'width' => '120px',
                    ]
                ],
                [
                    'attribute' => 'contractSubject',
                    'label' => 'Contract Subject',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Utility::generateTextShorterSpan($model->claim->contract->ContractSubject, 30, 150);
                    },
                    'options' => [
                        'width' => '150px',
                    ]
                ],
                [
                    'attribute' => 'subContractor',
                    'label' => 'Sub Contractor',
                    'value' => 'claim.contract.subContractor.PersonTitle',
                    'options' => [
                        'width' => '80px',
                    ]
                ],
                [
                    'attribute' => 'RequestDateHijri',
                    'label' => 'Request Date',
                    'format'=>'raw',
                    'value' =>function($model){
                        $data="<table>
                            <tbody>
                                <tr><td>".Utility::date($model->claim->createdAt)."</td></tr>
                                <tr><td>".Utility::convertDate($model->claim->createdAt,true)."</td></tr>
                            </tbody>
                        </table>";
                        return $data;
                    },
                            'options'=>[
                                'width'=>'80px',
                            ],
                ],
                [
                    'attribute' => 'claimNo',
                    'label' => 'Package No',
                    'value' => 'claim.claimNo',
                ],
                [
                    'attribute' => 'subject',
                    'format' => 'raw',
                    'value' => function($model) {
                        return Utility::generateTextShorterSpan($model->claim->subject, 30);
                    },
                    'options' => [
                        'width' => '140px',
                    ]
                ],
                [
                    'attribute' => 'LastActionDateHijri',
                    'label' => 'Action Date',
                    'value' => 'claim.processEntity.LastActionDateHijri',
                    
                ],
                [
                    'attribute' => 'lastActionTitle',
                    'label' => 'Last Action',
                    'value' => 'claim.processEntity.lastAction.ActionTitle1',
                    'options' => [
                        'width' => '100px',
                    ]
                ],
                [
                    'attribute' => 'lastUser',
                    'label' => 'Last User',
//                    'value' => 'claim.processEntity.lastUser.UserName',
                    'value' => 'claim.processEntity.lastUser.fullName',
                ],
                [
                    'attribute' => 'LastActionAutoMessage',
                    'label' => 'Auto Message',
                    'value' => 'claim.processEntity.LastActionAutoMessage',
                ],
                [
                    'attribute' => 'LastActionMessage',
                    'label' => 'Last Message',
                    'format' => 'raw',
                    'value' => function($data) {
                        return Utility::generateTextShorterSpan($data['LastActionMessage'], 30);
                    },
                    'options' => [
                        'width' => '110px',
                    ]
                ],
                [
                    'header' => 'Actions',
                    'format' => 'raw',
                    'value' => function($data)use($type) {
                        $res = ''
                                . (($type == 'ToDo') ? Html::a(
                                        '<i class="im-arrow-right2 rotate-360 s16"></i>', Yii::$app->urlManager->createAbsoluteUrl(['workflow/next-action/next-action', 'type' => 'claim', 'peid' => $data['ProcessEntityID'], 'sort' => Yii::$app->request->get('sort'), 'page' => Yii::$app->request->get('page'),]), [
                                    'onclick' => "showModal(this); return false;",
                                    'class' => 'nextAction btn btn-xs tip',
                                    'data-original-title' => "Next Action",
                                    'style' => 'color:#AACF72;padding:1px 2px;',
                                    'data-pjax' => "0",
                                    'data-peid' => $data['ProcessEntityID'],
                                    'data-rowid' => $data['claimIdRef'],
                                    "data-toggle" => 'tooltip'
                                        ]
                                ) : '')
                                . '<a title="" data-original-title="Action Diagram" data-toggle="tooltip" class="btn btn-xs tip " data-pjax="0" style="padding:1px 2px;color:#646464;" href="' . Yii::$app->urlManager->createAbsoluteUrl(['workflow/workflow/graph', 'peid' => $data['ProcessEntityID']]) . '" target="_blank"><i class="en-flow-branch s16"></i></a>'
                                . '<a title="" data-original-title="Claim Detail" data-toggle="tooltip" class="btn btn-xs tip " data-pjax="0" style="padding:1px 2px;color:#AE85BE;" href="' . Yii::$app->urlManager->createAbsoluteUrl(['claim/claim/view', 'peid' => $data['ProcessEntityID']]) . '" target="_blank" onclick="return hideNextActionBox(' . $data["ProcessEntityID"] . ');" ><i class="fa-legal s16" style="font-size:18px"></i></a>'
                        ;
                        $res.=
                         ($type == 'All') ?  Html::a(
                                '<i class="fa-trash" style="font-size:18px"></i>',"#", [
                                    'data-original-title' => 'Budget Delete',
                                    'class' => 'btn btn-xs tip',
                                    'data-pjax' => '0',
                                    'style' => 'color:#466FA3;padding:1px;',
                                    'delete-url' => Yii::$app->urlManager->createAbsoluteUrl(['claim/claim/delete-confirm','peid' => $data["ProcessEntityID"]]),
                                    'onclick' => 'showDeleteclaimModal(this)',
                        ]) : '';
                        
                        return $res;
                        ;
                    },
                    'options' => [
                        'width' => 100,
                    ],
                ]
            ],
        ]);
        ?>
        <?php Pjax::end(); ?>
    </div>
    <?php GridViewBox::end() ?> 
</div>

<?php

Modal::begin([
    'id'=>'claim-workflow-modal',
    'size' => Modal::SIZE_LARGE,

]);
echo '<div id="claim-workflow-modal-data"></div>';
Modal::end();

\yii\bootstrap\Modal::begin([
    'id' => 'delete-claim-modal',
]);
\yii\bootstrap\Modal::end();

?>

<script>
    function showDeleteclaimModal(obj){
        $.ajax({
            url: $(obj).attr('delete-url'),
        }).done(function (data) {
            $('#delete-claim-modal .modal-body').html(data);
            $('#delete-claim-modal').modal('show');
        });
        return false;
    }
    
	function newClaim() {
        var uri = "<?= Yii::$app->urlManager->createAbsoluteUrl(["claim/claim/create"]); ?>";
        ajaxProgressLoader('show','#claim-list-pjax');
        $.ajax({
            url: uri,
        }).done(function (data) {
            $('#claim-workflow-modal-data').html(data);
            $('#claim-workflow-modal').modal('show');
        }).error(function(data){
            message=data.responseText;
            showMsgBoxInBottomRight(message.substring(17), 'error');
        });
        ajaxProgressLoader('hide','#claim-list-pjax');
    }
	
    
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
    $('#claim-list-pjax').on('pjax:complete', function () {
            dubleclick();
    });

</script>

<?php
Modal::begin([
    'id' => 'claim-modal',
    'size' => Modal::SIZE_LARGE,
    'header' => '<h4 class="modal-title"><i class="im-arrow-right2"></i> Next action</h4>',
    'options' => [
        'class' => 'fade modal in animated pulse'
    ]
]);
Pjax::begin(['id' => 'pjax-modal']);
$js = "
        $('#pjax-modal').on('pjax:send', function(){
            ajaxProgressLoader('show', '.modal-content');
        });
        
        $('#pjax-modal').on('pjax:complete', function(){
            ajaxProgressLoader('hide', '.modal-content');
        });
        function showModal(obj){
            ajaxProgressLoader('show', '#claim-list-pjax');
            var url = $(obj).attr('href');
            $.ajax({
                url: url
            })
            .done(function(data){
                $('#pjax-modal').html(data);
                 $('#claim-modal').modal('show');
                 ajaxProgressLoader('hide', '#claim-list-pjax');
            });
            return false;
        }
        
    ";
$this->registerJs($js, yii\web\View::POS_END);
Pjax::end();
Modal::end();
?>

<style>
    .dblclick{
        cursor: pointer;
    }
</style>