<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 10:00
 */

namespace lbmzorx\components\action;

use lbmzorx\components\helper\ModelHelper;
use yii;
use yii\web\Response;
use yii\web\UnprocessableEntityHttpException;

class SortAction extends \yii\base\Action
{

    public $modelClass;

    public $scenario = 'update';

    public $primaryKey='id';

    public $isTransacion=true;
    /**
     * 排序操作
     *
     */
    public function run()
    {
        $status=false;
        $err=Yii::t('app','Invalid Parameter');
        if (yii::$app->getRequest()->getIsPost()) {
            $request = yii::$app->getRequest();
            $post=$request->post();
            $err='is not post';
            if(!empty($post[$this->primaryKey])){
                $id=$post[$this->primaryKey];
                unset($post[$this->primaryKey]);
                $otherAttr=$post;
                if($id){
                    if(is_array($id)){
                        $keys=[];
                        foreach ($id as $v){
                            if(preg_match('/^[1-9][\d]*$/',$v)){
                                $keys[]=$v;
                            }
                        }
                        if(!empty($keys)){
                            $models=call_user_func([$this->modelClass, 'findAll'],[$this->primaryKey=>$keys]);
                            if(!empty($models)){
                                $t=\yii::$app->db->beginTransaction();
                                $tStatus=true;
                                foreach ($models as $model){
                                    /**
                                     * @var \yii\db\ActiveRecord $model
                                     */
                                    $model->setScenario($this->scenario);
                                    if( !($model->load($otherAttr,'') && $model->save())){
                                        $t->rollBack();
                                        $err=ModelHelper::getErrorAsString($model->getErrors());
                                        $tStatus=false;
                                        break;
                                    }
                                }
                                if($tStatus){
                                    $status=true;
                                    $t->commit();
                                }
                            }
                        }
                    }else if(preg_match('/^[1-9][\d]*$/',$id)){
                        $model = call_user_func([$this->modelClass, 'findOne'], $id);
                        if(!empty($model)){
                            /**
                             * @var \yii\db\ActiveRecord $model
                             */
                            $model->setScenario($this->scenario);
                            if( !($model->load($otherAttr,'') && $model->save())){
                                $err=ModelHelper::getErrorAsString($model->getErrors());
                            }else{
                                $status=true;
                            }
                        }
                    }
                }else{
                    $err='is not id';
                }
            }else{
                $err='is not post';
            }
        }
        if (yii::$app->getRequest()->getIsAjax()) {
            yii::$app->getResponse()->format = Response::FORMAT_JSON;
            if( $status ){
                return ['status'=>'true','msg'=>yii::t('app','Edit Success!')];
            }else{
                return ['status'=>'false','msg'=>$err];
            }
        } else {
            if( !$status ){
                yii::$app->getSession()->setFlash('error', $err);
            }
            return $this->controller->goBack();
        }

    }

}