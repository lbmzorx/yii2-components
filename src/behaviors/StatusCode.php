<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 1:17
 */
namespace lbmzorx\components\behaviors;

use Yii;
use yii\base\Behavior;

class StatusCode extends Behavior
{
    public static $cssCode=[0=>'warning',1=>'success',2=>'danger',3=>'info',4=>'primary',];

    /**
     * 获取状态码
     * @param $attribute
     * @param $statuCode
     * @param string $default
     * @return string
     */
     public function getStatusCode($attribute,$statuCode,$default=''){
         $class=get_class($this->owner);
         $value=$this->owner->{$attribute};
         return isset($class::${$statuCode}[$value])?Yii::t('app',$class::${$statuCode}[$value]):$default;
    }

    public function getStatusCss($attribute,$statuCode,$default=0){
        $class=get_class($this->owner);
        $value=$this->owner->{$attribute};
        if(property_exists($class,$statuCode)){
            if(isset($class::${$statuCode}[$value])){
                return $class::${$statuCode}[$value];
            }
        }
        return isset(static::$cssCode[$default])?static::$cssCode[$default]:$default;
    }

    public static function tranStatusCode($statusCode,$category){
        $tran=[];
        foreach ($statusCode  as $k=>$v){
            $tran[$k]=Yii::t($category,$v);
        }
        return $tran;
    }

}