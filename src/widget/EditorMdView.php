<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/7
 * Time: 21:20
 */

namespace lbmzorx\components\widget;

use common\assets\EditormdLibAsset;
use Yii;
use common\assets\EditormdAsset;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\i18n\Formatter;
use yii\base\Model;
use yii\web\View;

class EditorMdView extends Widget
{

    /**
     * @var Model object the data model whose details are to be displayed. This can be a [[Model]] instance,
     * an associative array, an object that implements [[Arrayable]] interface or simply an object with defined
     * public accessible non-static properties.
     */
    public $model;

    public $attribute;

    public $options=[];
    public $optionTextarea=[];
    public $mdJsOptions=[];

    /**
     * Initializes the detail view.
     * This method will initialize required property values.
     */
    public function init()
    {
        if ($this->attribute === null) {
            throw new InvalidConfigException('Please specify the "attribute" property.');
        }

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getMdeditorId();
        }
    }

    /**
     * Renders the detail view.
     * This is the main entry of the whole detail view rendering.
     */
    public function run()
    {
        if($this->model){
            $this->renderMdJs();
            echo $this->renderTextarea();
        }
    }



    public function renderTextarea($options = [])
    {
        $optionTextarea = array_merge($this->optionTextarea, $options);

        $idMd=$this->getMdeditorId();
        $options=empty($this->options['id'])?array_merge($this->options,['id'=>$idMd]):$this->options;
        return Html::tag('div',Html::activeTextarea($this->model, $this->attribute, $optionTextarea),$options);
    }

    public function renderMdJs(){
        $view=\yii::$app->getView();
        EditormdAsset::register($view);
        EditormdLibAsset::register($view);
        $editormdUrl=$view->assetBundles[\common\assets\EditormdAsset::className()]->baseUrl;
        $idMd=$this->getMdeditorId();

        $mdJsOptions=ArrayHelper::merge($this->mdJsOptions,[
            'htmlDecode'=>'style,script,iframe',
            'emoji'=>false,
            'taskList'=> false,
            'tex'=> false,
            'flowChart'=> false,
            'sequenceDiagram'=> false,
        ]);
        $mdJsOptions=ArrayHelper::merge($mdJsOptions,[
            'path'=>$editormdUrl.'/lib/',
            'name'=>$this->attribute,
        ]);

        $mdJsOptionsJson=Json::encode($mdJsOptions);
        $keymd=substr(md5($idMd),0,10);
        $view->registerJs("$(function() {var md$keymd=editormd.markdownToHTML('$idMd',$mdJsOptionsJson);})",View::POS_END);
    }

    public function getMdeditorId(){
        $inputID = $this->getId();
        return "mdeditor$inputID";
    }

}