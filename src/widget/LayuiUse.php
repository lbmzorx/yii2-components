<?php
namespace lbmzorx\components\widget;

use common\assets\LayerAsset;
use common\assets\LayuiAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\base\Widget;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

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

    public $options=[];
    public $layuiUse=['layerdate'];
    public $content='';
    public static $used=0;

    /**
     * Initializes the pager.
     */
    public function init()
    {
        if( empty($this->options['id'])){
            $this->options['id']='batch_delete';
        }
        if( empty($this->options['class'])){
            $this->options['class']='btn btn-success';
        }
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        $this->renderJs();
    }

    public function renderJs(){
        $view = \yii::$app->getView();
        $view->registerAssetBundle(LayuiAsset::className());

        if(static::$used==0){
            $view->registerJs(<<<SCRITYT
        var laydate;
        layui.use(['laydate'], function(){
            laydate=layui.laydate;
            {$this->content}
        });
SCRITYT
            );
            static::$used++;
        }

    }
}
