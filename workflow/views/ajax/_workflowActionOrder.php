<?php
use yii\helpers\Html;    
?>

<div class="arrow" style="top:20px;"></div> 
<div class="popover-content">
    <?php
    $count = count($actionOrderModels);
    if ($count > 0) {
        $html = '<table class="table psa-grid table-striped "> <tbody> ';

        $html.="<th>Action Title</th><th>Message1</th><th>Message2</th><th>Message3</th><th>Required Message</th><th>is Default</th>";
        foreach ($actionOrderModels as $model) {
            if ($model->RequiredMessage) {
                $requiredMessage = 'checked';
            } else {
                $requiredMessage = 'unchecked';
            }
            if ($model->isDefaultNextAction) {
                $isDefault = 'checked';
            } else {
                $isDefault = 'unchecked';
            }
            $html.='<tr><td>';
            $html.=Html::encode($model->nextAction->ActionTitle1);
            $html.='</td><td>';
            $html.=(isset($model->AutoMessage1))? Html::encode($model->AutoMessage1) : '---';
            $html.='</td><td>';
            $html.=(isset($model->AutoMessage2))? Html::encode($model->AutoMessage2) : '---';
            $html.='</td><td>';
            $html.=(isset($model->AutoMessage3))? Html::encode($model->AutoMessage3) : '---';
            $html.='</td><td>';
            $html.='<input type="checkbox" style="cursor:default;" disabled ' . $requiredMessage . '>';
            $html.='</td><td>';
            $html.='<input type="checkbox" style="cursor:default;" disabled ' . $isDefault . '>';
            $html.='</td></tr>';
        }
        $html.='</tbody></table>';
        echo $html;
    } else {
        echo '<b>Finished Action</b>';
    }
    ?>
</div>









