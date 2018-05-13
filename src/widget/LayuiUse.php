<?php
namespace lbmzorx\components\widget;

use lbmzorx\components\assets\LayuiAsset;
use Yii;
use yii\base\Exception;
use yii\base\Widget;
use yii\helpers\VarDumper;
use yii\web\View;

/**
 * LinkPager displays a list of hyperlinks that lead to different pages of target.
 *
 * LinkPager works with a [[Pagination]] object which specifies the total number
 * of pages and the current page number.
 *
 * Note that LinkPager only generates the necessary HTML markups. In order for it
 * to look like a real pager, you should provide some CSS styles for it.
 * With the default configuration, LinkPager should look good using Twitter Bootstrap CSS framework.
 *
 * For more details and usage information on LinkPager, see the [guide article on pagination](guide:output-pagination).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LayuiUse extends Widget
{

    public $module=['laydate'];
    public $content='';
    public static $usedModule=[];

    public static $layuiConfigCount=0;


    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        $this->renderJs();
    }

    public function checkModule(){
        if(!is_array($this->module)){
            $this->module=(array)$this->module;
        }

        $module=array_diff($this->module,static::$usedModule);
        static::$usedModule=array_merge(static::$usedModule,$module);
        return static::$usedModule;
    }

    public function renderJs(){
        $view = \yii::$app->getView();
        $view->registerAssetBundle(LayuiAsset::className());

        if(static::$layuiConfigCount==0){
            $dir=$view->assetBundles[LayuiAsset::className()]->baseUrl;
            $view->registerJs(<<<SCRITYT
layui.config({
  dir: '{$dir}/' //layui.js 所在路径
});
SCRITYT
                ,View::POS_END);
            static::$layuiConfigCount++;
        }


        $module=$this->checkModule();
        $var='';
        $defined='';
        foreach ($module as $v){
            $var ='var '.$v.';';
            $defined.=$v.'='.'layui.'.$v.';';
        }
        $used=implode('\',\'',$module);
        if($used){
            $script=<<<SCRITYT
/*layvar*start*/
{$var}
/*layvar*end*/
layui.use(['{$used}'],function(){
{$defined}
/*laycontent*start*/
{$this->content}
/*laycontent*end*/});
SCRITYT
            ;
            if(isset($view->js[View::POS_READY]['layui'])){
                $layuiCss=$view->js[View::POS_READY]['layui'];
                if(preg_match('/(\/\*laycontent\*start\*\/)([.\s\S]*)(\/\*laycontent\*end\*\/)/',$layuiCss,$match)){
                    $script=<<<SCRITYT
/*layvar*start*/
{$var}
/*layvar*end*/
layui.use(['{$used}'],function(){
{$defined}
/*laycontent*start*/{$match[2]}{$this->content}/*laycontent*end*/});
SCRITYT
                    ;
                }
            }
            $view->registerJs($script,View::POS_READY,'layui');
            }
    }
}
