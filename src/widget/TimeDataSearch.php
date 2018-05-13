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
class TimeDataSearch extends Widget
{

    public $options=[];
    public $griViewKey;
    public $name = 'Batch Deletes';
    public $isIcon=true;
    public $deleteUrl='delete';
    public $jsParams=[];
    public $jsConfirmMsg="Are you want to delete ";
    public $jsbtn=['ok','cancer'];
    public $btnIcon = 'trash';

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
        echo $this->renderButton();
    }

    public function renderButton(){

         if($this->isIcon){
             $trush = Html::tag('i', '', ['class' => "fa fa-{$this->btnIcon}"]);
             $name=$trush.'&nbsp;'.Yii::t('app', $this->name);
         }else{
             $name=$this->name;
         }
         return Html::a($name,$this->deleteUrl,$this->options);
    }


    public function renderJs(){
        $view = \yii::$app->getView();

        $msg=Yii::t('app',$this->confirmMsg);
        $delimeter='';
        $data='';
        if($this->jsParams){
            $data=json_encode($this->jsParams);
            $delimeter=',';
        }


        foreach ($this->jsbtn as $k=>$v){
            $this->jsbtn[$k]=Yii::t('app',$v);
        }
        $btn = implode('\',\'',$this->jsbtn);

        LayerUse::widget([]);

        $view->registerAssetBundle(LayuiAsset::className());
        $view->registerJs(<<<SCRITYT
         $('#{$this->options['id']}').click(function(){
            var keys = $('#w{$this->griViewKey}').yiiGridView('getSelectedRows');            
            layer.comfirm("{$msg}",{
                btn:['{$btn}'],
                function(){
                    $.post('{$this->deleteUrl}',{id:keys{$delimeter}{$data}},function(res){
                        if(res.status){
                            layer.msg(res.msg,{time:1000},function(){
                                $.pjax.reload('#w{$this->griViewKey}');
                            });
                        }else{
                            layer.alert(res.msg);
                        }
                    },'json');     
                }
            }); 
        });
SCRITYT
);
    }
}
