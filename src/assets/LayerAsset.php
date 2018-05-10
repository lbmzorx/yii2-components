<?php

namespace lbmzorx\components\assets;

use yii\web\AssetBundle;
use yii\web\View;

/**
 * Main backend application asset bundle.
 */
class LayerAsset extends AssetBundle
{
    public $sourcePath = '@@vendor/lbmzorx/yii2-components/src/static/js/layer';

    public $js = [
        'layer.js'
    ];

    public $jsOptions=[
        'position'=>View::POS_END,
    ];
}
