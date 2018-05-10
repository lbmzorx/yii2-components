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

        $request=\Yii::$app->getRequest();

        $id=$request->get($this->paramSign);
        if($id){
            $model= call_user_func([$this->modelClass, 'findOne'], $id);
            $this->imgConfig['nameModel']=$model;
        }
        if(empty($this->imgConfig['class'])){
            $this->imgConfig['class']=$this->imgClass;
        }
        $imgModel=Yii::createObject($this->imgConfig);

        if ($request->isPost) {
            $imgModel->imageFile =\yii\web\UploadedFile::getInstance($imgModel, 'imageFile');
            if ($imgModel->upload()) {
                $status=true;
            }else{
                $err=current($imgModel->getFirstErrors());
                $status=false;
            }
        }else{
            return "{msg:ok}";
//            throw new BadRequestHttpException(yii::t('app', "Upload only support post data"));
        }

        if($request->isAjax){
            yii::$app->getSession()->setFlash('success', yii::t('app', 'Success'));
            \yii::$app->response->format=yii\web\Response::FORMAT_JSON;
            if($status==true){
                return ['success'=>1,'message'=>yii::t('app','Upload Success'),'url'=>$imgModel->urlName];
            }else {
                return ['success' =>0,'message' =>$err];
            }
        }else{
            if($status==true){
                return json_encode(['success'=>1,'message'=>yii::t('app','Upload Success'),'url'=>$imgModel->urlName]);
//                if( $this->successRedirect ) return $this->controller->redirect($this->successRedirect);
//                return $this->controller->refresh();
            }else {
                yii::$app->getSession()->setFlash('error', $err);
                return $this->controller->redirect($this->viewFile);
            }

        }
    }
}