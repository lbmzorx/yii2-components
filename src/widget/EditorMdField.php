<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/7
 * Time: 21:20
 */

namespace lbmzorx\components\widget;

use common\assets\EditormdAsset;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveField;

class EditorMdField extends ActiveField
{
    public $mdOptions=[];
    public $mdJsOptions=[];

    public function textarea($options = [])
    {
        $options = array_merge($this->inputOptions, $options);
        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);

        $idMd=$this->getMdeditorId();
        $mdOptions=$this->mdOptions;

        $mdOptions=empty($mdOptions['id'])?array_merge($this->mdOptions,['id'=>$idMd]):$this->mdOptions;
        $this->parts['{input}'] = Html::tag('div',Html::activeTextarea($this->model, $this->attribute, $options),$mdOptions);
        $this->renderMdJs();

        return $this;
    }

    public function renderMdJs(){
        $view=\yii::$app->getView();
        EditormdAsset::register($view);
        $editormdUrl=$view->assetBundles[\common\assets\EditormdAsset::className()]->baseUrl;
        $idMd=$this->getMdeditorId();

        $mdJsOptions=ArrayHelper::merge($this->mdJsOptions,[
            'width'=>'100%',
            'height'=>'640',
            'syncScrolling'=>"single",
            'watch'=> true,
            'emoji' => true,
            'codeFold'=>true,
            'preview'=>false,
            //syncScrolling'=>'false',
            'saveHTMLToTextarea'=>true,    // 保存 HTML 到 Textarea
            'searchReplace'=>true,
            //watch'=>' false',                // 关闭实时预览
            'htmlDecode'=>"style',script',iframe|on*",            // 开启 HTML 标签解析，为了安全性，默认不开启
            //toolbar'=>' false',             //关闭工具栏
            //previewCodeHighlight'=>' false', // 关闭预览 HTML 的代码块高亮，默认开启
            'taskList'=>true,

            'tocm'=>true,         // Using [TOCM]
            'tex'=>true,                   // 开启科学公式TeX语言支持，默认关闭
            'flowChart'=>true,             // 开启流程图支持，默认关闭
            'sequenceDiagram'=>true,       // 开启时序/序列图支持，默认关闭',
        ]);
        $mdJsOptions=ArrayHelper::merge($mdJsOptions,[
            'path'=>$editormdUrl.'/lib/',
            'name'=>$this->attribute,
        ]);

        $mdJsOptionsJson=Json::encode($mdJsOptions);
        $keymd=substr(md5($idMd),0,10);
        $view->registerJs("var md{$keymd}; $(function() { md{$keymd}=editormd('$idMd',$mdJsOptionsJson);$('.editormd-preview-close-btn').hide()})");
    }

    public function getMdeditorId(){
        $inputID = $this->getInputId();
        return "mdeditor$inputID";
    }
}