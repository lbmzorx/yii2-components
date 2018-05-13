<?php
/**
 * Created by Administrator.
 * Date: 2018/5/10 19:25
 * github: https://github.com/lbmzorx
 */
namespace lbmzorx\components\assets;

use yii\web\AssetBundle;
class LbmzorxAsset extends AssetBundle
{
    public $sourcePath='@vendor/lbmzorx/yii2-components/src/static';
    public $js=[
        'js/lbmzorx.js',
    ];

    public $depends=[
        'jquery'=>'yii\web\JqueryAsset',
        'layer'=>'lbmzorx\components\assets\LayerAsset',
    ];
}