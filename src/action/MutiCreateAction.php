<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/2
 * Time: 21:45
 */

namespace lbmzorx\components\action;

use Yii;
use yii\base\Action;
use yii\db\Transaction;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\web\HttpException;

class MutiCreateAction extends Action
{
    public $modelClass;

    public $scenario = 'create';

    public $data = [];

    public $indexView='index';
    /** @var string 模板路径，默认为action id  */
    public $viewFile = null;

    public $transation=true;

    public $depandeClass;

    private $_linkStack=[];  //关系

    private $_condition=[];

    /** @var  string|array 编辑成功后跳转地址,此参数直接传给yii::$app->controller->redirect() */
    public $successRedirect;

    /**
     * create创建页
     *
     * @return array|string
     */
    public function run()
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass;
        $depances=$this->newDepance();

        $model->setScenario( $this->scenario );
        $request=yii::$app->getRequest();
        if ($request->getIsPost()) {
            $depances['model']=$model;
            if($this->transation){
                /**
                 * @var $t \yii\db\Transaction
                 */
                $t=($this->modelClass)::getDb()->beginTransaction();
            }

            $status=$this->save($depances);

            if ($status == true) {
                if($this->transation){
                    $t->commit();
                }
                yii::$app->getSession()->setFlash('success', yii::t('app', 'Created Success'));
                if($request->isAjax){
                    return ['status'=>true,'msg'=>yii::t('app','Created Success')];
                }else{
                    return $this->controller->redirect([$this->indexView]);
                }
            } else {
                if($this->transation){
                    $t->rollBack();
                }
                if($request->isAjax){
                    return ['status'=>false,'msg'=>yii::$app->getSession()->getFlash('error')];
                }
                return $status;
            }
        }
        $model->loadDefaultValues();
        $data = [
            'model' => $model,
        ];

        if($depances){
            foreach ($depances as $name=>$depance){
                $data[$name]=$depance;
            }
        }

        if( is_array($this->data) ){
            $data = array_merge($data, $this->data);
        }elseif ($this->data instanceof \Closure){
            $data = call_user_func_array($this->data, [$model, $this]);
        }
        $this->viewFile === null && $this->viewFile = $this->id;
        return $this->controller->render($this->viewFile, $data);
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param $depances
     * @return bool
     */
    protected function save($models){
        $status=false;

        if(!empty($models)){
            foreach($this->_linkStack as $name){
                /**
                 * @var \yii\base\Model $models[$name];
                 */
                $load=$models[$name]->load(Yii::$app->getRequest()->post());
                if( $data=$this->loadCondition('model',$models) ){
                    $models[$name]->setAttributes($data);
                }
                if ( $load&&$models[$name]->save()) {
                    $status= true;
                }else{
                    $status= false;

                    yii::$app->getSession()->setFlash('error', current($models[$name]->getFirstErrors()));
                    break;
                }
            }
        }
        return $status;
    }

    /**
     * 解析条件
     * @param $name
     * @param $models
     * @return array|bool
     */
    protected function loadCondition($name,$models){
        if(isset($this->_condition[$name]) && !empty($this->_condition[$name])){
            $data=[];
            foreach ($this->_condition[$name] as $attribute=>$v){
                if(preg_match('/{(?P<model>\w+):(?P<attribute>\w+)}/',$v,$match)){
                    if(isset($models[$match['model']])){
                        if(isset($models[$match['model']]->{$match['attribute']})){
                            $data[$attribute]=$models[$match['model']]->{$match['attribute']};
                        }
                    }
                }
            }
            if(!empty($data)){
                return $data;
            }
        }
        return false;
    }


    /**
     * 解析模型依赖关系
     */
    protected function analysisLinkStack($condition,$name){
        foreach ($condition as $k=>$v){
            if(preg_match('/{(?P<model>\w+):(?P<attribute>\w+)}/',$k,$match)){
                $this->orderStack($match['model'],$name);
                $this->_condition[$name][$v]=$k;
            }
            if(preg_match('/{(?P<model>\w+):(?P<attribute>\w+)}/',$v,$match)){
                $this->orderStack($name,$match['model']);
                $this->_condition[$match['model']][$match['attribute']]='{'.$name.':'.$k.'}';
            }
        }
    }

    /**
     * 插入模型正确的位置
     * @param $model
     * @param $name
     */
    protected function orderStack($model,$name){
        $inModel=in_array($model,$this->_linkStack);
        $inName=in_array($name,$this->_linkStack);
        if( $inModel&&$inName){
            $flip=array_flip($this->_linkStack);
            if($flip[$model]>$flip[$name]){    //
                unset($this->_linkStack[$flip[$model]]);
                array_splice($this->_linkStack,$flip[$name],0,$model);
            }
        }elseif( $inModel &&(!$inName) ){
            $this->_linkStack[]=$name;
        }else if( !$inModel&&$inName ){
            $flip=array_flip($this->_linkStack);
            array_splice($this->_linkStack,$flip[$name],0,$model);
        }else{
            $this->_linkStack[]=$model;
            $this->_linkStack[]=$name;
        }
    }



    /**
     * new depance model
     * @return array|bool|Yii\db\ActiveRecord
     */
    protected function newDepance(){
        if(is_array($this->depandeClass) && !empty($this->depandeClass)){
            if(!isset($this->depandeClass['class'])){
                $depanceModels=[];
                foreach ($this->depandeClass as $key=>$depance){
                    $name=StringHelper::basename($depance['class']);

                    /**
                     * @var \yii\db\ActiveRecord $depanceModels
                     */
                    $depanceModels[$name]=Yii::createObject($depance['class']);

                    if(isset($depance['scenario'])){
                        $depanceModels[$name]->setScenario($depance['scenario']);
                    }
                    if(isset($depance['condition'])){
                        $this->analysisLinkStack($depance['condition'],$name);
                    }
                }
            }else{
                $name=StringHelper::basename($this->depandeClass['class']);
                /**
                 * @var \yii\db\ActiveRecord $depanceModels
                 */
                $depanceModels[$name]=Yii::createObject($this->depandeClass['class']);
                if(isset($this->depandeClass['scenario'])){
                    $depanceModels[$name]->setScenario($this->depandeClass['scenario']);
                }
                if(isset($this->depandeClass['condition'])){
                    $this->analysisLinkStack($this->depandeClass['condition'],$name);
                }
            }
            if(!empty($depanceModels)){
                return $depanceModels;
            }
        }
        return false;
    }


}