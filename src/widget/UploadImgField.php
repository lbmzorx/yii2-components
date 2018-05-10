<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/7
 * Time: 21:20
 */

namespace lbmzorx\components\widget;

use common\assets\LayuiAsset;
use yii\helpers\Html;
use yii\widgets\ActiveField;
class UploadImgField extends ActiveField
{
    public $imgOptions=[];
    public $jsOptions=[];


    /**
     * @param array $options
     * @return $this
     */
    public function fileInput($options = [])
    {

        $id=$this->getInputId();
        $btn=Html::button(\Yii::t('app','Upload Image'),['class'=>'layui-upload btn btn-info','id'=>'btn-'.$id]);
        $list=Html::tag('div',
            Html::img($this->model->{$this->attribute},['class'=>'layui-upload-img','id'=>'img-'.$id]).Html::tag('p','',['id'=>'p-'.$id])
            ,['class'=>'layui-upload-list']
        );
        $img=Html::activeInput('hidden',$this->model, $this->attribute, $options);

        $this->addAriaAttributes($options);
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::tag('div',$btn.$list.$img,['class'=>'layui-upload']);

        $this->renderJs();
        return $this;
    }

    public function renderJs(){
        $view=\yii::$app->getView();

        $id=$this->getInputId();
        $url=$this->jsOptions['urlUpload'];
        $uploadName=$this->jsOptions['field'];

        $failedmsg=\Yii::t('app','Failed Upload');
        $retry = \yii::t('app','Retry');

        LayuiAsset::register($view);
        $view->registerJs(<<<SCRIPT
        
        layui.use(['upload','layer'], function(){
            var layer = layui.layer,
                upload = layui.upload;
            var uploadInst = upload.render({
                elem: '#btn-{$id}'
                ,url: '{$url}'
                ,field :'{$uploadName}'
                ,before: function(obj){
                  //预读本地文件示例，不支持ie8
                  obj.preview(function(index, file, result){
                    $('#img-{$id}').attr('src', result); //图片链接（base64）
                  });
                }
                ,done: function(res){
                  //如果上传失败
                  if(res.success != 1){
                    return layer.msg(res.msg);
                  }else{
                    $("#{$id}").val(res.url);
                    $('#p-{$id}').html('');
                  }
                }
                ,error: function(){
                    //演示失败状态，并实现重传
                    var demoText = $('#p-{$id}');
                    demoText.html('<span style="color: #FF5722;">{$failedmsg}</span> <a class="layui-btn layui-btn-mini demo-reload">{$retry}</a>');
                    demoText.find('.demo-reload').on('click', function(){
                        uploadInst.upload();
                    });
                }
            });
        });
SCRIPT
);


    }

}