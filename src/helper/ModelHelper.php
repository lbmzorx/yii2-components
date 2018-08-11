<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/25
 * Time: 15:51
 */

namespace lbmzorx\components\helper;


class ModelHelper
{
    /**
     * @param $model \yii\base\Model
     * @param $errors
     * @return string
     */
    public static function getErrorAsString($model,$errors){
        $err = '';
        foreach ($errors as $k=>$v) {
            $err .=$model->getAttributeLabel($k).':'.$v[0] . '<br>';
        }
        return $err;
    }

    /**
     * @param $model \yii\base\Model
     * @param $errors
     * @return string
     */
    public static function getErrorToString($model){
        $err = '';
        foreach ($model->getErrors() as $k=>$v) {
            $err .=$model->getAttributeLabel($k).':'.$v[0] . '<br>';
        }
        return $err;
    }


    public static function clearRules($attribute,$rules){
        foreach ($rules as $k=>$rule){
            if(isset($rules[0])){
                if(is_string($rule[0]) && $rule[0]==$attribute){
                    unset($rules[$k]);
                }
                if(is_array($rule[0]) && in_array($attribute,$rule[0])){
                    if(count($rule[0])>1){
                        $flip = array_flip($rule[0]);
                        unset($rules[$k][$flip[$attribute]]);
                    }else{
                        unset($rules[$k]);
                    }
                }
            }
        }
    }
}