<?php
/**
 * Created by Administrator.
 * Date: 2018/6/3 18:13
 * github: https://github.com/lbmzorx
 */
namespace lbmzorx\components\action;

use yii;
use yii\base\Action;
use lbmzorx\components\helper\ModelHelper;

/**
 * Class FormAction
 *
 * this action will be common action for model, validate data and deal with id which data is posted from user.
 * if there is form
 * example : in controller
 * public function actions(){
 * return [
 * 'activate'=>[
        'class'=>FormAction::className(),
        'modelClass'=>ActivateForm::className(),
        'verifyMethod'=>'sendEmail',
        'successMsg'=>\yii::t('app','Congratulations! We have send an email to you account, please click it and activate you account'),
        'isErrorMsg'=>true,
        'successRedirectvView'=>'/site/login',
 * ],
 * ];
 * }
 * @package lbmzorx\components\action
 */
class AjaxFormAction extends Action
{
    /**
     * model class
     * @var
     */
    public $modelClass;
    /**
     * after validate post data, next method to execute
     * @var
     */
    public $verifyMethod;

    /**
     * different scenario has different rules to validate data
     * @var
     */
    public $scenario;

    /**
     * if success , msg to show
     * @var string|\Closure
     */
    public $successMsg;

    /**
     * if error , msg to from model
     * @var string
     */
    public $isErrorMsg=true;

    /**
     * if error , msg to show. when $isErrorMsg is false
     * @var string
     */
    public $errorMsg='';

    public $format;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        if($this->format==null){
            \yii::$app->response->format=yii\web\Response::FORMAT_JSON;
        }
    }

    public function run()
    {
        /**
         * @var $model \yii\base\Model
         */
        $model = new $this->modelClass;
        if($this->scenario){
            $model->setScenario($this->scenario);
        }

        $request=yii::$app->getRequest();
        if ($request->isPost){
            if ($model->load($request->post()) && $model->validate()) {
                $status=true;
                if(method_exists($model,$this->verifyMethod)){
                    if(! $model->{$this->verifyMethod}()){
                        $status=false;
                    }
                }
            } else {
                $status=false;
            }
            $msg='';
            if($status==true){
                if($this->successMsg){
                    $msg=$this->successMsg;
                }

                if( is_string($this->successMsg) ){
                    $msg =$this->successMsg;
                }elseif ($this->successMsg instanceof \Closure){
                    $msg = call_user_func_array($this->successMsg, [$model, $this]);
                }
            }else{
                if($this->isErrorMsg){
                    $msg='kong'.ModelHelper::getErrorAsString($model,$model->getErrors());
                }else{
                    $msg=$this->errorMsg;
                }
            }

            Yii::$app->getSession()->setFlash($status?'success':'error',$msg);

            return ['status'=>$status,'msg'=>$msg];
        }
        return ['status'=>false,'msg'=>'Error Message Type'];
    }
}