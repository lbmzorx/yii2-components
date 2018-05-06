<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2018/1/16
 * Time: 12:35
 */

namespace lbmzorx\components\widget;


use yii\base\Widget;
use yii\helpers\Html;

class RangeTime extends Widget
{
    public $dom;
    public $options=[];
    private $_options=[
        'type'=>'datetime',
        'range'=>'~',
        'calendar'=>'true',
        'done'=>'function(value){$(this.elem).val(value);$(this.elem).change();}',
    ];

    public $tips;
    public $tips_message;   //dom默认是对应时间范围的值，'{dom}'表示
    public $tips_dom;
    public $tips_event='mouseover';
    public $tipsOptions=[];
    private $_tipsOptions=['tips'=>'[1,\'#78BA32\']'];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return Html::encode($this->message);
    }

    public function config(){
        \common\assets\LayuiAsset::register($this->view);







        $this->view->registerJs(<<<SCRIPT
        var laydate;var layuier;layui.use(['laydate','layer'], function(){ layuidate = layui.laydate;layuier = layui.layer;
   //日期时间范围
  laydate.render({
    elem: '#add-time-input'
    ,type: 'datetime'
    ,range: true    
    ,calendar: true
    ,range: '~'
    ,closeStop: '#add-time-input' //这里代表的意思是：点击 test1 所在元素阻止关闭事件冒泡。如果不设定，则无法弹出控件
    ,done: 
  });
  $('#add-time-input').mouseover(function(){
    layuier.tips($("#add-time-input").val(),"#add-time-input",{
        tips:       
    });
  });
  
  laydate.render({
    elem: '#edit-time-input'
    ,type: 'datetime'
    ,range: true
    ,calendar: true
    ,closeStop: '#add-time-input' //这里代表的意思是：点击 test1 所在元素阻止关闭事件冒泡。如果不设定，则无法弹出控件
    ,done: function(value, date, endDate){       
        $(this.elem).val(value);
        $(this.elem).change();
    }
  });
});
  
$('#edit-time-input').mouseover(function(){
    layuier.tips($("#edit-time-input").val(),"#edit-time-input",{
        tips: [1, '#78BA32']
    });
});
SCRIPT
            ,\yii\web\View::POS_END);

    }
    public function configTimeRange(){
        if(is_array($this->dom)){
            foreach ($this->dom as $k=>$v){
                $this->options[$k]['elem']=$v;
                if(!isset($this->options[$k]['closeStop'])){
                    $this->options[$k]['closeStop']=$v;
                }
                $this->options[$k]=array_merge($this->_options,$this->options[$k]);
            }
        }else{
            $this->options['elem']=$this->dom;
            if(!isset($this->options['closeStop'])){
                $this->options['closeStop']=$this->dom;
            }
            $this->options=array_merge($this->_options,$this->options);
        }
    }

    public function configTips(){
        if($this->tips!==false){
            if(is_array($this->tips)){
                foreach ($this->tips as $k=>$v){
                    $this->tipsOptions[$k]['elem']=$v;
                    $this->tipsOptions[$k]=array_merge($this->_tipsOptions,$this->tipsOptions[$k]);
                    $this->renderTips($v,$v,$this->tips_event,$this->tips_message,$this->tipsOptions[$k]);
                }
            }else{
                $this->options=array_merge($this->_options,$this->options);
                $this->renderTips($this->tips,$this->tips,$this->tips_event,$this->tips_message,$this->tipsOptions);
            }
        }
    }

    public function renderDateTime(){

    }

    public function renderTips($dom,$binddom,$event,$msg='{dom}',$config){
        $msg=str_replace($msg,'$('.$dom.').val()','{dom}');
        return '$('.$binddom.').'.$event.'(function(){ layuier.tips('.$msg.','.$dom.','.json_encode($config).');});';
    }

}