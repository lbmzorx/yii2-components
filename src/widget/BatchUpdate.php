<?php
namespace lbmzorx\components\widget;

use common\assets\LayerAsset;
use Yii;
use yii\helpers\Html;
use yii\base\Widget;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BatchUpdate extends Widget
{

    public $options=[];
    public $griViewKey=0;
    public $name = '';
    public $isIcon=true;
    public $btnIcon='';
    public $unChoose = 'At least Choose One to update!';
    public $jsConfirmMsg="Are you want to update ?";
    public $jsConfirm=['ok','cancel'];
    public $jsSelect=['ok','cancel'];
    public $attribute='';


    public function init()
    {
        if( empty($this->options['id'])){
            $this->options['id']=$this->attribute.'-batch_update';
        }
        if( empty($this->options['class'])){
            $this->options['class']='btn btn-success';
        }
        parent::init();
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
            $name=$trush.'&nbsp;'.Yii::t('app',"Batch Update {name}", ['name' => $this->name]);
        }else{
            $name=Yii::t('app',"Batch Update {name}", ['name' => $this->name]);
        }
        return Html::button($name,$this->options);
    }


    public function renderJs(){
        $view = \yii::$app->getView();

        foreach ($this->jsSelect as $k=>$v){
            $this->jsSelect[$k]=Yii::t('app',$v);
        }
        $jsSelect = implode('\',\'',$this->jsSelect);
        foreach ($this->jsConfirm as $k=>$v){
            $this->jsConfirm[$k]=Yii::t('app',$v);
        }
        $jsConfirm = implode('\',\'',$this->jsConfirm);

        $confimMsg=Yii::t('app',$this->jsConfirmMsg);
        $unChoose = Yii::t('app',$this->unChoose);

        $view->registerAssetBundle(LayerAsset::className());
        $view->registerJs(<<<SCRITYT
$('#{$this->options['id']}').click(function(){           
    var keys = $('#w{$this->griViewKey}').yiiGridView('getSelectedRows');
    var dom_status_change=$('#{$this->attribute}-change-dom');
  
    if( typeof keys !="undefined" && keys.length>0){
        dom_status_change.find('input[name="id"]').val(keys);                     
        batchUpdate(dom_status_change,'#w{$this->griViewKey}',['{$jsSelect}'],"{$confimMsg}",['{$jsConfirm}'])
    }else{
        layer.alert('$unChoose');            
    }
});
        
SCRITYT
        );
    }

}
