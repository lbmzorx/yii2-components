<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/2
 * Time: 21:45
 */

namespace lbmzorx\components\actions;

use Yii;
use yii\base\Action;

class CreateAction extends Action
{
    public $modelClass;

    public $scenario = 'default';

    public $data = [];

    public $indexView='index';
    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /** @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect() */
    public $successRedirect;

    /**
     * create创建页
     *
     * @return array|string
     */
    public function run()
    {
        /* @var $model yii\db\ActiveRecord */
        $model = new $this->modelClass;
        $model->setScenario( $this->scenario );
        $request=yii::$app->getRequest();
        if ($request->getIsPost()) {
            if ($model->load($request->post()) && $model->save()) {
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Created Success'));
                if($request->isAjax){
                    return ['status'=>true,'msg'=>yii::t('app','Created Success')];
                }else{
                    return $this->controller->redirect([$this->indexView]);
                }
            } else {
                $errors = $model->getErrors();
                $err = '';
                foreach ($errors as $v) {
                    $err .= Yii::t('app',$v[0]) . '<br>';
                }
                Yii::$app->getSession()->setFlash('error', $err);
                if($request->isAjax){
                    return ['status'=>false,'msg'=>$err];
                }
            }
        }
        $model->loadDefaultValues();
        $data = [
            'model' => $model,
        ];
        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof \Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        return $this->controller->render($this->viewFile, $data);
    }

}