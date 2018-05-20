<?php

namespace lbmzorx\components\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class EditormdAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lbmzorx/yii2-components/src/static/vendor/editor.md-master/';

    public $css=[
        'css/editormd.css',
    ];

    public $js = [
        'editormd.min.js',
    ];

    public $depends=[
        'jquery'=>'yii\web\JqueryAsset',
    ];
}
