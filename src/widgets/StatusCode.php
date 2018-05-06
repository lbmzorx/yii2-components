<?php
namespace lbmzorx\components\widget;

use Yii;
use yii\helpers\Html;
use yii\base\Widget;


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
class StatusCode extends Widget
{

    public $options=[];
    public $url='change-code';
    public $exceptCode=[];
    public $attribute;
    public $dataClass;

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        echo $this->renderDom();
    }

    public function renderDom(){
        $string=
            '<div id="'.$this->attribute.'-change-code" style="display: block;">\n'.
            '<div style="padding: 10px;">\n'.
              Html::beginForm([$this->url],'post')."\n".
            '<input type="hidden" name="key" value="status">\n'.
            '<input type="hidden" name="id" value="">\n';

        $attributeCode=$this->attribute."_code";
        $statusCode=($this->dataClass)::$$attributeCode;
            foreach ($statusCode as $k=>$v){
                $attibutecss = $this->attribute."_css";
                $css='warning';
                if(empty(($this->dataClass)::$$attibutecss)){
                    if(!empty(\lbmzorx\components\behaviors\StatusCode::$cssCode[$k])){
                        $css=\lbmzorx\components\behaviors\StatusCode::$cssCode[$k];
                    }
                }else{
                    $css=($this->dataClass)::$$attibutecss;
                }
                if(!in_array($k,$this->exceptCode)){
                        $string.='<label class="checkbox-inline" style="margin: 5px 10px;">\n'.
                            Html::input('radio','value',$k).Html::tag('span',$v,['class'=>'btn btn-'.$css])."\n".
                    '</label>\n';
                }
        }
        $string.=Html::endForm().'\n</div>\n</div>';
        return $string;
    }
}
