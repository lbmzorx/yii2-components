<?php

namespace lbmzorx\components\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class JsencryptAsset extends AssetBundle
{
    public $sourcePath = '@@vendor/lbmzorx/yii2-components/src/static/js/jsencrypt/';

    public $js = [
        'jsencrypt.min.js'
    ];

}
