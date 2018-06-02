<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-08-13 00:31
 */

namespace lbmzorx\components\action;

use yii;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;

class UploadAction extends \yii\base\Action
{

    public $modelClass;

    public $imgClass;

    public $imgAttribute='uploadFile';
    public $imgConfig=[];
    /**
     * 依赖模型，
     * 除了模型以外，还有依赖
     * @var
     */
    public $depandeClass;

    public $scenario = 'update';

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
//        \yii::$app->response->format=yii\web\Response::FORMAT_JSON;
        $request=\Yii::$app->getRequest();

        if(empty($this->imgConfig['class'])){
            $this->imgConfig['class']=$this->imgClass;
        }
        $imgModel=Yii::createObject($this->imgConfig);

        if ($request->isPost) {
            $imgModel->{$this->imgAttribute} =\yii\web\UploadedFile::getInstance($imgModel, $this->imgAttribute);
            if ($imgModel->upload()) {
                $status=true;
            }else{
                $err=current($imgModel->getFirstErrors());
                $status=false;
            }
        }else{
            $err=\yii::t('app','Parameter Error!');
            $status=false;
//            return ['status'=>false,'success' =>0,'message'=> "Upload only support post data",'msg' => "Upload only support post data"];
        }

        if($request->isAjax){
            yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            \yii::$app->response->format=yii\web\Response::FORMAT_JSON;
            if($status==true){
                return ['status'=>true,'success'=>1,'message'=>yii::t('app','Upload Success'),'msg'=>yii::t('app','Upload Success'),'url'=>$imgModel->urlName];
            }else {
                return ['status'=>false,'success' =>0,'message'=>$err,'msg' =>$err];
            }
        }else{
            if($status==true){
                return json_encode(['status'=>true,'success'=>1,'msg'=>yii::t('app','Upload Success'),'url'=>$imgModel->urlName]);
//                if( $this->successRedirect ) return $this->controller->redirect($this->successRedirect);
//                return $this->controller->refresh();
            }else {
                yii::$app->getSession()->setFlash('error', $err);
                return json_encode(['status'=>false,'success'=>0,'message'=>$err,'msg'=>$err,'url'=>$imgModel->urlName]);

//                return $this->controller->redirect($this->viewFile);
            }

        }
    }
}