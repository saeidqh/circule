<?php

use yii\helpers\Html;

if ($type == 'cashRequest') {
    $url = Yii::$app->urlManager->createAbsoluteUrl(['cashRequest/cashrequest/create']);
} elseif ($type == 'capital') {
    $url = Yii::$app->urlManager->createAbsoluteUrl(['capital/capital/create']);
} elseif ($type == 'tender') {
    $url = Yii::$app->urlManager->createAbsoluteUrl(['tender/tender/create']);
} elseif ($type == 'automation') {
    $url = Yii::$app->urlManager->createAbsoluteUrl(['automation/automation/create']);
} elseif ($type == 'employee-settlement') {
    $url = Yii::$app->urlManager->createAbsoluteUrl(['automation/employee-settlement/create']);
} else {
    $url = '#';
}
?>
<div class="process-content">
    <?php
    $newData = array();
    for ($i = 0; $i < count($data); $i++) {
        $newData[$data[$i]['ProcessID']] = $data[$i]['ProcessTitle'];
    }
    echo Html::radioList('ProcessID', $data[0]['ProcessID'], $newData)
    ?>    
</div>   
<button type=button class="btn btn-sm btn-success" id="btn-create-process-entirt" onClick="createProcessEntity(this)" data-ajax-url="<?= $url ?>" ><i class="fa-plus"></i> Create</button>
<button type=button class="btn btn-sm btn-warning" data-dismiss=modal><i class="br-cancel"></i> Close</button>


<style>
    .process-content label{
        display:block;
        direction: rtl;
    }
</style>

<script type="text/javascript" >
    function createProcessEntity(obj){
    var pid = null;
    $('input:radio[name=ProcessID]:checked').each(function(){
        pid = $(this).val(); 
    });
    var ajaxUrl = $(obj).attr('data-ajax-url');
    
    window.location.href = ajaxUrl+"&pid="+pid;
}
</script>
