<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\workflow\assets;

class WorkflowAsset extends \yii\web\AssetBundle{
    public $sourcePath = '@app/modules/workflow/bower';
    
    public $js = [
        'js/d3.v3.min.js',      
    ];
    
    public $css = [
        'css/wf.css'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}
