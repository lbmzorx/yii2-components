<?php
/**
 * Created by PhpStorm.
 * User: aa
 * Date: 2017/12/8
 * Time: 11:13
 */
namespace lbmzorx\components\behavior;

use common\models\tool\UploadImg;
use common\components\tools\Gd;
use yii\base\Behavior;
use yii\base\Exception;
use yii\base\Object;

/**
 * Class ImageDeal
 * 自动处理图片
 * @package app\components
 */
class ImageDeal extends Behavior
{
    /**
     * 图片操作
     */
    const IMG_THUMB=1;  //压缩
    const IMG_WATER=2;  //打水印
    /**
     * @var $imgTool
     * imgTool Class Name
     */
    public $imgTool;
    public $imgTool_params=[];
    /**
     * @var Object $_imgTool
     * Object to deal with img
     */
    private $_imgTool;

    /**
     * @var  \yii\web\UploadedFile //图片
     */
    public $img;
    public $imgFullName;       //图片的完整名称

    public $operate;            //图片操作
    /**
     * 图片操作参数
     * 1、 如果是压缩图片
     * 要有三个参数
     *    1 $width 最大宽度
     *    2 $height 最大高度
     *    3 可选 $type  压缩类型 参考 \app\tool\Gd
     * 2、加水印
     *    1 $source 水印图片路径
     *    2 可选$locate 水印位置 （默认右下角）
     *    3 可选$alpha  水印透明度
     * @var array $operate_params
     */
    public $operate_params=[];  //图片操作参数

    public $thumb_threshold=512000;    //默认大于500k才压缩

    public function events()
    {
        return [
            UploadImg::EVENT_AFTER_SAVEIMG => 'operation',
        ];
    }

    /**
     * 获取图片名字
     * @return bool
     */
    public function getImgFullName(){
        $imgmodel=$this->owner;
        $this->imgFullName=$imgmodel->getFullFile();
        return $this->imgFullName;
    }

    /**
     * 创建图片工具
     * @return bool|object
     */
    protected function createTool(){
        if(!($this->_imgTool instanceof Object)){
            if(!$this->getImgFullName()){
                $this->throwOnTool('image file');
            }

            $this->_imgTool=new Gd($this->imgFullName);
        }
        return $this->_imgTool;
    }

    /**
     * 抛出异常
     * @param $attribut
     * @throws Exception
     */
    public function throwOnTool($attribut){
        $msg=\Yii::t('app',"There is no {$attribut}!");
        throw new Exception($msg);
    }

    /**
     * 图片操作
     * 功能1 压缩
     * 功能2 加水印
     * @param $event
     */
    public function operation($event){
        if ($event->name == UploadImg::EVENT_AFTER_VALIDATE
            && empty($this->owner->imageFile)){
            return;
        }
        if(!$this->createTool()){
            return;
        }
        if(
            $this->owner->isthumb&&
            $this->operate==self::IMG_THUMB){
            $this->thumbImg();
        }
        if(
            $this->owner->iswater&&
            $this->operate==self::IMG_WATER){
            $this->waterImg();
        }
    }

    /**
     * 压缩图片
     */
    public function thumbImg(){
        if($this->owner->imageFile->size>$this->thumb_threshold){
            $this->_imgTool->thumb(...$this->operate_params);
            $this->_imgTool->save($this->imgFullName);
        }
    }

    /**
     * 加水印
     */
    public function waterImg(){
        if($this->owner->imageFile->size>$this->thumb_threshold){
            $this->_imgTool->water(...$this->operate_params);
            $this->_imgTool->save($this->imgFullName);
        }
    }

}