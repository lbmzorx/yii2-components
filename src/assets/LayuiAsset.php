<?php

namespace lbmzorx\components\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class LayuiAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lbmzorx/yii2-components/src/static/vendor/layui-v2.2.5/';

    public $css=[
        'css/layui.css'
    ];
    public $js = [
        'layui.js'
    ];

    public $depends=[
        'common\assets\JqueryAsset',
    ];
}
