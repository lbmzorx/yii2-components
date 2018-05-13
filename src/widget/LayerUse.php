<?php
namespace lbmzorx\components\widget;

use lbmzorx\components\assets\LayerAsset;
use Yii;
use yii\base\Widget;
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
class LayerUse extends Widget
{

    public static $used=0;


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
        $view->registerAssetBundle(LayerAsset::className());
        $dir=$view->assetBundles[LayerAsset::className()]->baseUrl;
        if(static::$used==0){
            $view->registerJs(<<<SCRITYT
$('#layuicss-layer').attr('href','$dir/theme/default/layer.css?v='+layer.v);
layer.path='$dir';
SCRITYT
            ,View::POS_END);
            static::$used++;
        }
    }
}
