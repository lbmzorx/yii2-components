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
use yii\web\Response;

class ChangeStatusAction extends Action
{
    public $modelClass;
    public $scenario = 'update';
    /** @var string 模板路径，默认为action id  */
    public $viewFile = 'index';

    /**
     * 状态参数
     * @return array|string
     */
    public function run()
    {
        /* @var $model \yii\db\ActiveRecord */
        $request=yii::$app->getRequest();
        $id=$request->post('id');
        $key=$request->post('key');
        $value=$request->post('value');

        if($id){
            $ids = explode(',', $id);
            $t=Yii::$app->db->beginTransaction();
            $k=0;
            foreach ($ids as $id){
                if(preg_match('/[\d]+/',$id)){
                    $model = ($this->modelClass)::findOne($id);
                    if($model){
                        $model->setScenario( $this->scenario );
                        if($model->$key==$value){
                            $k++;
                            continue;
                        }
                        if($model->load([$key=>$value],'')&&$model->save()){
                            continue;
                        }else{
                            $msg= ['status'=>false,'msg'=>Yii::t('app',current($model->getFirstErrors()))];
                            break;
                        }
                    }
                }
            }
            if($k==count($ids)){
                $msg= ['status'=>false,'msg'=>Yii::t('app','Unchange')];
            }
            if(isset($msg)){
                $t->rollBack();
            }else{
                $t->commit();
                $msg= ['status'=>true,'msg'=>Yii::t('app','Edit success')];
            }
        }else{
            $msg= ['status'=>true,'msg'=>Yii::t('app',"Invalid id")];
        }

        if($request->isAjax){
            \yii::$app->getResponse()->format=Response::FORMAT_JSON;
            return $msg;
        }else{
            if($msg['status']==true){
                \yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            }else{
                \yii::$app->getSession()->setFlash('error', yii::t('app', $msg['msg']));
            }

            return $this->controller->redirect($this->viewFile);
        }
    }

}