<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\workflow\assets;

class WorkflowListAsset extends \yii\web\AssetBundle{
    public $sourcePath = '@app/modules/workflow/bower';
    
    public $js = [
        'js/wf_graph.js',
    ];
   
    public $depends = [
        'yii\web\JqueryAsset',
        'app\modules\workflow\assets\WorkflowAsset'
    ];
}
