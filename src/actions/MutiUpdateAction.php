<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace lbmzorx\components\actions;

use yii;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

class MutiUpdateAction extends \yii\base\Action
{

    public $modelClass;

    /**
     * 依赖模型，
     * 除了模型以外，还有依赖
     * @var
     */
    public $depandeClass;

    public $scenario = 'default';

    public $paramSign = "id";

    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    /** @var array|\Closure 分配到模板中去的变量 */
    public $data;

    /** @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect() */
    public $successRedirect;

    public $transation=false;
    /**
     * update修改
     *
     * @return array|string|\yii\web\Response
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function run()
    {
        $id = yii::$app->getRequest()->get($this->paramSign, null);
        if (! $id) throw new BadRequestHttpException(yii::t('app', "{$this->paramSign} doesn't exit"));
        /* @var $model yii\db\ActiveRecord */
        $model = call_user_func([$this->modelClass, 'findOne'], $id);

        $depance=[];
        if($this->depandeClass){
            $depance=$this->getDepanceClass($model);
        }

        if (! $model) throw new BadRequestHttpException(yii::t('app', "Cannot find model by $id"));
        $model->setScenario( $this->scenario );

        if (yii::$app->getRequest()->getIsPost()) {
            if($this->transation==true){
                $t=\yii::$app->db->beginTransaction();
            }
            if($this->save($model,$depance)){
                if($this->transation==true){
                    $t->commit();
                }
                if( yii::$app->getRequest()->getIsAjax() ){
                    return ['status'=>true,'msg'=>'Success'];
                }else {
                    yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
                    if( $this->successRedirect ) return $this->controller->redirect($this->successRedirect);
                    return $this->controller->refresh();
                }
            }else{
                if($this->transation==true){
                    $t->rollBack();
                }
                if( yii::$app->getRequest()->getIsAjax() ){
                    throw new UnprocessableEntityHttpException(yii::$app->getSession()->getFlash('error'));
                }
            }

        }

        $this->viewFile === null && $this->viewFile = $this->id;
        $data = [
            'model' => $model,
        ];

        if($depance){
            if(is_array($depance)){
                foreach ($depance as $k=>$v){
                    $data[$k]=$v;
                }
            }else{
                $data['depance']=$depance;
            }
        }

        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof \Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        return $this->controller->render($this->viewFile, $data);
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param $depances
     * @return bool
     */
    protected function save($model,$depances){
        $status=false;
        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            $status= true;
        }else{
            yii::$app->getSession()->setFlash('error', current($model->getFirstErrors()));
        }
        if($status&&!empty($depances)){
            if(is_object($depances)){
                if ($depances->load(Yii::$app->getRequest()->post()) && $depances->save()) {
                    $status= true;
                }else{
                    yii::$app->getSession()->setFlash('error', current($depances->getFirstErrors()));
                    $status= false;
                }
            }elseif(is_array($depances)){
                foreach ($depances as $depance){
                    if(!$status){
                        break;
                    }
                    if ($depance->load(Yii::$app->getRequest()->post()) && $depance->save()) {
                        $status= true;
                    }else{
                        yii::$app->getSession()->setFlash('error', current($depance->getFirstErrors()));
                        $status= false;
                    }
                }
            }else{
                $status= false;
            }
        }
        return $status;
    }

    protected function getDepanceClass($model){
        if(is_array($this->depandeClass)){
            if(!isset($this->depandeClass['class'])){
                foreach ($this->depandeClass as $key=>$depance){
                    if(is_numeric($key)){
                        $name=yii\helpers\StringHelper::basename($depance['class']);
                        $depanceModel[$name]=$this->creatDepanceModel($depance,$model);
                        if( $depanceModel[$name] ==false ){
                            unset($depanceModel[$name]);
                            continue;
                        }
                    }
                }
            }else{
                $name=yii\helpers\StringHelper::basename($this->depandeClass['class']);
                $depanceModel[$name]=$this->creatDepanceModel($this->depandeClass,$model);
                if( $depanceModel == false ){
                    return false;
                }
            }
        }else{
            return false;
        }
        return isset($depanceModel)?$depanceModel:false;

    }

    /**
     * $depance=['class'=>'','more'=>true,'condition'=>['id'=>'{model:id}'],'scenario']
     *
     * @param $depance
     * @param $model
     * @return bool|mixed
     */
    protected function creatDepanceModel($depance,$model){
        if( isset($depance['condition'])){
            $condition=$this->getCondition($depance['condition'],$model);
            if(isset($depance['more']) && $depance['more']==true ){
                $depanceModel=$depance['class']::find()->where($condition)->all();
                if(isset($depance['scenario'])){
                    $depanceModel->setScenario($depance['scenario']);
                }
            }else{
                $depanceModel=call_user_func([$depance['class'], 'findOne'],$condition);
                if(isset($depance['scenario'])){
                    $depanceModel->setScenario($depance['scenario']);
                }
            }
            return $depanceModel;
        }
        return false;
    }

    /**
     * 多模型条件
     * '{model:attribute}',{model2:attribute}
     * @param $condition
     * @param $model
     * @return mixed
     */
    protected function getCondition($condition,$model){
        if(empty($condition)){
            return $condition;
        }
        if(is_array($condition)){
            foreach ($condition as $k=>$v){
                $result[$k]=$this->getCondition($v,$model);
            }
        }else{
            if(preg_match('/{([\w\d_]+):([\w\d_]+)}/',$condition,$match)){
                $result=$model->{$match[2]};
            }else{
                $result=$condition;
            }
        }
        return $result;
    }


}