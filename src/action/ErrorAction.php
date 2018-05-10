<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace lbmzorx\components\action;

use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\base\UserException;

/**
 * ErrorAction displays application errors using a specified view.
 *
 * To use ErrorAction, you need to do the following steps:
 *
 * First, declare an action of ErrorAction type in the `actions()` method of your `SiteController`
 * class (or whatever controller you prefer), like the following:
 *
 * ```php
 * public function actions()
 * {
 *     return [
 *         'error' => ['class' => 'yii\web\ErrorAction'],
 *     ];
 * }
 * ```
 *
 * Then, create a view file for this action. If the route of your error action is `site/error`, then
 * the view file should be `views/site/error.php`. In this view file, the following variables are available:
 *
 * - `$name`: the error name
 * - `$message`: the error message
 * - `$exception`: the exception being handled
 *
 * Finally, configure the "errorHandler" application component as follows,
 *
 * ```php
 * 'errorHandler' => [
 *     'errorAction' => 'site/error',
 * ]
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Dmitry Naumenko <d.naumenko.a@gmail.com>
 * @since 2.0
 */
class ErrorAction extends \yii\web\ErrorAction
{
    /**
     * @var string the view file to be rendered. If not set, it will take the value of [[id]].
     * That means, if you name the action as "error" in "SiteController", then the view name
     * would be "error", and the corresponding view file would be "views/site/error.php".
     */
    public $view;

    public $guestView;
    public $guestLayout='guest';

    public $userView;
    public $userLayout='main';

    /**
     * Runs the action.
     *
     * @return string result content
     */
    public function run()
    {
        if (Yii::$app->getRequest()->getIsAjax()) {
            return $this->renderAjaxResponse();
        }
        $this->chooseView();
        return $this->renderHtmlResponse();
    }

    /**
     * select view template of error view by different group
     */
    protected function chooseView(){
        if($this->guestView){
            if(Yii::$app->user->isGuest){
                $this->controller->layout=$this->guestLayout;
                $this->view=$this->guestView;
            }
        }
        if($this->userView){
            if(!Yii::$app->user->isGuest){
                $this->controller->layout=$this->userLayout;
                $this->view=$this->userView;
            }
        }
    }
}
