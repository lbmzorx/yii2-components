<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/7
 * Time: 21:20
 */

namespace lbmzorx\components\widget;


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveField;

class InputAddField extends ActiveField
{
    public $firstOption = [];
    public $firstContent='';

    public $endOption=[];
    public $endContent='';

    public $isTime=false;
    public $timeType='';
    public $dateConfig=[];

    public $isTips=false;
    public $tipsType='title';//layer
    public $tipsCofig=[];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->endOption=ArrayHelper::merge($this->endOption,[
            'class'=>'input-group-btn',
        ]);
        $this->firstOption=ArrayHelper::merge($this->firstOption,[
            'class'=>'input-group-addon',
        ]);

        if(!empty($this->timeType)){
            $this->dateConfig=array_merge($this->dateConfig,[
                'type'=>$this->timeType,
            ]);
        }
    }

    public function passwordInput($options=[]){
        parent::passwordInput($options);
        $first='';
        if($this->firstContent){
            $first=Html::tag('span',$this->firstContent,$this->firstOption);
        }
        $end='';
        if($this->endContent){
            $end=Html::tag('span',$this->endContent,$this->endOption);
        }
        $this->parts['{input}']='<div class="input-group">'.$first.$this->parts['{input}'].$end.'</div>';
        if($this->isTime){
            $this->renderTimeTypeJs();
        }
        if($this->isTips){
            $this->renderTipsJs();
        }

        return $this;
    }

    public function textInput($options = [])
    {
        parent::textInput($options); // TODO: Change the autogenerated stub

        $first='';
        if($this->firstContent){
            $first=Html::tag('span',$this->firstContent,$this->firstOption);
        }
        $end='';
        if($this->endContent){
            $end=Html::tag('span',$this->endContent,$this->endOption);
        }
        $this->parts['{input}']='<div class="input-group">'.$first.$this->parts['{input}'].$end.'</div>';
        if($this->isTime){
            $this->renderTimeTypeJs();
        }
        if($this->isTips){
            $this->renderTipsJs();
        }

        return $this;
    }

    /**
     * @param array $items
     * @param array $options
     * @return $this
     */
    public function dropDownList($items, $options = [])
    {
        parent::dropDownList($items, $options); // TODO: Change the autogenerated stub
        $first='';
        if($this->firstContent){
            $first=Html::tag('span',$this->firstContent,$this->firstOption);
        }
        $end='';
        if($this->endContent){
            $end=Html::tag('span',$this->endContent,$this->endOption);
        }
        $this->parts['{input}']='<div class="input-group">'.$first.$this->parts['{input}'].$end.'</div>';
        return $this;
    }

    protected function renderTimeTypeJs(){
        $this->dateConfig['elem']='#'.$this->getInputId();
        if(empty($this->dateConfig['closeStop'])){
            $this->dateConfig['closeStop']=$this->dateConfig['elem'];
        }
        $config=json_encode($this->dateConfig);

        $str=',\'done\':function(value, date, endDate){$(this.elem).val(value); $(this.elem).change();}';
        $config=rtrim($config,'}').$str.'}';

        $content=<<<SCRIPT
laydate.render({$config});
SCRIPT;
        LayuiUse::widget(['content'=>$content,'module'=>['laydate']]);
    }

    protected function renderTipsJs(){
        $view=\yii::$app->getView();
        $id='#'.$this->getInputId();
        if($this->tipsType=='title'){
            $scipt=<<<SCRIPT
$('$id').change(function(){
    $(this).attr('title',$(this).val());
});
SCRIPT;
        }elseif($this->tipsType=='layer'){
            if(empty($this->tipsCofig)){
                $this->tipsCofig['tips']=[1, '#78BA32'];
            }
            $config=json_encode($this->tipsCofig,JSON_FORCE_OBJECT);

            LayerUse::widget([]);
            $scipt=<<<SCRIPT
$('$id').mouseover(function(){
    layer.tips($('{$id}').val(),'{$id}',{$config});
});
SCRIPT;
        }

        if(isset($scipt)){
            $view->registerJs($scipt);
        }
    }

}