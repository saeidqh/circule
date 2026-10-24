
<div class="pb-content" style="overflow: hidden; width: 100%; max-height: 750px; 
     margin-left: 0px; margin-top: 0px; left:0px;right:0px; bottom:0px;top:0px; border:solid 1px purple;padding:0 0 0 0 ;overflow:hidden;"> 
    <div class="grid-box-body">    
        <div class="row h100">
            <div class="graph-view" style="opacity: 10; margin: -15;">
                <?php
                //echo '<div class="row">'. readfile(Yii::$app->getModule('workflow')->getBasePath() . '/bower/graph/ProccessDiagram' . $pid . '.svg') . '</div>';
                $svgFilePath= Yii::$app->getModule('workflow')->getBasePath() . '/bower/graph/ProccessDiagram' . $pid . '.svg';
                        if(file_exists($svgFilePath)){
                            ob_start();
                            readfile($svgFilePath);
                            $svgContent = ob_get_clean();
                            echo  '<div class="row" style="text-align:center;">';
                            echo $svgContent;
                            echo '</div>';
                        }
                        else{
                            
                        }
                ?>
            </div>
        </div>
    </div>
</div>

