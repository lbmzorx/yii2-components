<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/21
 * Time: 21:54
 */

namespace lbmzorx\components\event;


use yii\base\Event;

class LoginEvent extends Event
{
    const EVENT_BEFORE_LOGIN    = 'beforeLogin'; //登录前的事件
    const EVENT_AFTRE_LOGIN     = 'afterLogin';  //登录后的事件
    const EVENT_FAILED_LOGIN    = 'failedLogin'; //登录失败事件
    const EVENT_SUCCESS_LOGIN   = 'successLogin';//登录成功事件

    public $user;

}