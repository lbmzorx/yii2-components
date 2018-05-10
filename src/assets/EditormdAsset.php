<?php

namespace lbmzorx\components\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class EditormdAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lbmzorx/yii2-components/src/static/js/editor.md-master/';

    public $css=[
        'css/editormd.css',
    ];

    public $js = [
        'editormd.min.js',
    ];

    public $depends=[
        'jquery'=>'common\assets\JqueryAsset',
    ];
}
