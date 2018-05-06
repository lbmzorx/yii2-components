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
use yii\web\Response;

class ChangeStatusAction extends Action
{
    public $modelClass;
    public $scenario = 'default';
    /** @var string 模板路径，默认为action id  */
    public $viewFile = 'index';

    /**
     * 状态参数
     * @return array|string
     */
    public function run()
    {
        /* @var $model yii\db\ActiveRecord */
        $request=yii::$app->getRequest();
        $id=$request->post('id');
        $key=$request->post('key');
        $value=$request->post('value');

        if($id){
            $ids = explode(',', $id);
            $t=Yii::$app->db->beginTransaction();
            foreach ($ids as $id){
                if(preg_match('/[\d]+/',$id)){
                    $model = ($this->modelClass)::findOne($id);
                    if($model){
                        $model->setScenario( $this->scenario );
                        if($model->load([$key=>$value],'')&&$model->save()){
                            continue;
                        }else{
                            $t->rollBack();
                            $msg= ['status'=>false,'msg'=>Yii::t('app',current($model->getFirstErrors()))];
                            break;
                        }
                    }
                }
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
            return $this->controller->redirect($this->viewFile);
        }
    }

}