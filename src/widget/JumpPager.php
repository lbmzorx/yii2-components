<?php
namespace lbmzorx\components\widget;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Request;
use yii\widgets\LinkPager;


class JumpPager extends LinkPager
{
    /**跳转**/    
    public $jOptions=[                  //跳转容器
        'class' => 'pagination',
        'style'=>"margin-left: 10px;vertical-align: top;",
    ];

    public $jLiInputCssClass='';        //跳转输入框li元素类
    public $jLiInputOptions=[];         //跳转输入框li元素选项
    public $jLiButtonCssClass;          //跳转按钮li元素类
    public $jLiButtonOptions=[];        //跳转按钮li元素选项

    public $jInputOptions=[             //跳转输入框input元素选项，可以重写改线以得新的样式
        'style'=>"
        height: 35px;
		border-bottom-left-radius: 4px;
    	border-top-left-radius: 4px;
    	background-color: #fff;
    	border: 1px solid #ddd;
	    color: #337ab7;   
	    line-height: 1.42857;
	    margin-left: -1px;
	    padding: 6px 0px 6px 6px;
	    width: 60px;
	    position: relative;
	    text-decoration: none;",
        'onkeyup'=>'',
        'onchange'=>'',
    ];
    public $jButtonOptions = [          //跳转输入框button元素选项，可以重写改线以得新的样式
        'style'=>"float: right;"
    ];

    public $jInpuType='number';         //跳转input类型
    public $jButtonLabel='Jump';         //跳转按钮名称

    public $jInputIdHeader='ji';        //跳转输入框id的前缀
    public $jButtonIdHeader='jb';       //跳转按钮id的前缀
    public static $jCounter=0;          //当前页跳转的计数

    public $sOptions=[                  //跳转容器
        'class' => 'pagination',
        'style'=>'margin-left: 10px;vertical-align: middle;float:right',
    ];

    public $sLiInputCssClass;           //跳转输入框li元素类
    public $sLiInputOptions=[];         //跳转输入框li元素选项
    public $sLiButtonCssClass;          //跳转按钮li元素类
    public $sLiButtonOptions=[];        //跳转按钮li元素选项

    public $sInpuType='number';         //跳转input类型
    public $sButtonLabel='PageSize';         //跳转按钮名称

    public $sInputOptions=[             //跳转输入框input元素选项，可以重写改线以得新的样式
        'style'=>"
		height: 35px;
		border-bottom-left-radius: 4px;
    	border-top-left-radius: 4px;
    	background-color: #fff;
    	border: 1px solid #ddd;
	    color: #337ab7;   
	    line-height: 1.42857;
	    margin-left: -1px;
	    padding: 6px 0px 6px 6px;
	    width: 60px;
	    position: relative;
	    text-decoration: none;
    	",

    ];
    public $sButtonOptions=[            //跳转输入框button元素选项，可以重写改线以得新的样式
        'style'=>"float: right;",
    ];

    public $sInputIdHeader='si';        //跳转输入框id的前缀
    public $sButtonIdHeader='sb';       //跳转按钮id的前缀
    public static $sCounter=0;          //当前页跳转的计数

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        parent::run();
        echo $this->renderJButtons();
        echo $this->renderSButtons();
    }

    /**
     * 渲染跳转
     * @return string the rendering result
     */
    protected function renderJButtons()
    {
        return $this->renderTemplateButtons('j',$this->pagination->pageParam);
    }

    /**
     *渲染单页记录数
     * @return string
     */
    protected function renderSButtons(){
        return $this->renderTemplateButtons('s',$this->pagination->pageSizeParam);
    }

    /**
     * 渲染按钮
     * @param $type
     * @param $pageParams
     * @return string
     */
    protected function renderTemplateButtons($type,$pageParams){
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }
        if(!$this->sButtonLabel){
            return '';
        }

        $buttons = [];
        $page=$this->pagination->getPage();
        $pageSize=$this->pagination->getPageSize();
        $BaseUrl=$this->unsetUrlParams($pageParams);

        $inputParam=$type=='j'?$page:$pageSize;
        $buttons[]=$this->renderTemplateInput($type,$inputParam,$BaseUrl,$pageParams);
        $buttons[]=$this->renderTemplateButton($type,$page);

        $options = $this->{$type.'Options'};
        $tag = ArrayHelper::remove($options, 'tag', 'ul');
        return Html::tag($tag, implode("\n", $buttons), $options);
    }

    /**
     * 渲染输入框
     * @param $type
     * @param $inputParam
     * @param $baseUrl
     * @return string
     */
    protected function renderTemplateInput($type,$inputParam,$baseUrl,$jsParam){
        $options = $this->{$type.'LiInputOptions'};
        $linkWrapTag = ArrayHelper::remove($options , 'tag' , 'li');
        Html::addCssClass($options, $this->{$type.'LiInputCssClass'});

        $inputParam=$type=='j'?($inputParam+1):$inputParam;
        $linkOptions = $this->{$type.'InputOptions'};
        $counter = self::${$type.'Counter'};
        $linkOptions['min'] = 1;

        $linkOptions['max'] = $type=='j'?$this->pagination->getPageCount():$this->pagination->pageSizeLimit[1];
        $linkOptions['id'] = $this->{$type.'InputIdHeader'}.$counter;
        $linkOptions['baseurl']=$baseUrl;

        if(empty($linkOptions['js'])){
            $this->renderJs($linkOptions['id'],$this->{$type.'ButtonIdHeader'}.$counter,$linkOptions['max'],$jsParam);
        }

        return Html::tag($linkWrapTag,Html::input($this->{$type.'InpuType'},empty($linkOptions['name'])?'name':$linkOptions['name'],$inputParam,$linkOptions),$options);

    }

    /**
     * 渲染按钮
     * @param $type
     * @param $page
     * @return string
     */
    protected function renderTemplateButton($type,$page){
        $options = $this->{$type.'LiButtonOptions'};
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, $this->{$type.'LiButtonCssClass'});

        $buttonOptions = $this->{$type.'ButtonOptions'};
        $buttonOptions['id']=$this->{$type.'ButtonIdHeader'}.self::${$type.'Counter'};
        return Html::tag($linkWrapTag, Html::a($this->{$type.'ButtonLabel'},$this->pagination->createUrl($page),$buttonOptions),$options);
    }


    /**
     * 渲染页面js
     * @param $idInput
     * @param $idButton
     * @param $max
     * @param $pageName
     */
    protected function renderJs($idInput,$idButton,$max,$pageName){
        \yii::$app->view->registerJs(<<<SCRIPT
            $('#$idInput').keyup(function(){jump$idInput();});        
            $('#$idInput').change(function(){jump$idInput();});
            function jump$idInput(){
                var v=$('#$idInput').val();
                v=/^[\d]+$/.test(v)&&(v>=1)&&(v<=$max)?v:1;
                $(this).val(v);           
                $('#$idButton').attr('href',$('#$idInput').attr('baseurl')+'&$pageName='+v);
            }        
SCRIPT
        );
    }
    
    /**
     * 去掉url中的某个参数
     * @param $pageName
     * @return string
     */
    protected function unsetUrlParams($paraName){
        $request = Yii::$app->getRequest();
        $params = $request instanceof Request ? $request->getQueryParams() : [];

        $pageSize = $this->pagination->getPageSize();
        $params[$this->pagination->pageSizeParam] = $pageSize;
        $page    = $this->pagination->getPage();
        $params[$this->pagination->pageParam] = $page;

        unset($params[$paraName]);

        $params[0] = $this->pagination->route === null ? Yii::$app->controller->getRoute() : $this->pagination->route;
        return \yii::$app->urlManager->createUrl($params);
    }
}
