<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/21
 * Time: 21:54
 */

namespace lbmzorx\components\event;


use yii\base\ModelEvent;
class UploadEvent extends ModelEvent
{
    const EVENT_BEFORE_UPLOAD    = 'beforeUpload'; //登录前的事件
    const EVENT_AFTRE_UPLOAD    = 'afterUpload';  //登录后的事件
    const EVENT_FAILED_UPLOAD   = 'failedUpload'; //登录失败事件
    const EVENT_SUCCESS_UPLOAD  = 'successUpload';//登录成功事件

    public $user;

}