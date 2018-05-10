<?php
namespace lbmzorx\components\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class ToastrAsset extends AssetBundle
{
    public $sourcePath='@vendor/lbmzorx/yii2-components/src/static/js/toastr';

    public $css=[
        'toastr.css'
    ];
    public $js = [
        'toastr.js'
    ];

    public $depends=[
        'common\assets\JqueryAsset',
    ];
}
