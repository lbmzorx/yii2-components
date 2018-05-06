
hp
namespace lbmzorx\components\widget;

use common\assets\LayerAsset;
use Yii;
use yii\helpers\Html;
use yii\base\Widget;
use yii\helpers\Url;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BatchDelete extends Widget
{

    public $options=[];
    public $griViewKey=0;
    public $name = 'Batch Deletes';
    public $isIcon=true;
    public $deleteUrl='delete';
    public $jsParams=[];
    public $jsConfirmMsg="Do you want to delete these items?";
    public $jsbtn=['ok','cancel'];
    public $btnIcon = 'trash';
    public $unChoose = 'At least Choose One to Delete!';

    /**
     * Initializes the pager.
     */
    public function init()
    {
        if( empty($this->options['id'])){
            $this->options['id']='batch_delete';
        }
        if( empty($this->options['class'])){
            $this->options['class']='btn btn-success';
        }
        $this->deleteUrl=Url::to([$this->deleteUrl]);
        parent::init();
    }

    /**
     * Executes the widget.
     * This overrides the parent implementation by displaying the generated page buttons.
     */
    public function run()
    {
        $this->renderJs();
        echo $this->renderButton();
    }

    public function renderButton(){

         if($this->isIcon){
             $trush = Html::tag('i', '', ['class' => "fa fa-{$this->btnIcon}"]);
             $name=$trush.'&nbsp;'.Yii::t('app', $this->name);
         }else{
             $name=$this->name;
         }
         return Html::button($name,$this->options);
    }

    public function renderJs(){
        $view = \yii::$app->getView();

        $msg=Yii::t('app',$this->jsConfirmMsg);
        $delimeter='';
        $data='';
        if($this->jsParams){
            $data=json_encode($this->jsParams);
            $delimeter=',';
        }


        foreach ($this->jsbtn as $k=>$v){
            $this->jsbtn[$k]=Yii::t('app',$v);
        }
        $btn = implode('\',\'',$this->jsbtn);
        $unChoose = Yii::t('app',$this->unChoose);
        $view->registerAssetBundle(LayerAsset::className());
        $view->registerJs(<<<SCRITYT
    $('#{$this->options['id']}').click(function(){
        var keys = $('#w{$this->griViewKey}').yiiGridView('getSelectedRows'); 
        if(keys.length>0){
        layer.confirm("{$msg}",{
            btn:['{$btn}'],
            yes:function(){
                $.post('{$this->deleteUrl}',{id:keys{$delimeter}{$data}},function(res){
                    if(res.status){
                        layer.msg(res.msg,{time:1000},function(){
                            $.pjax.reload('#w{$this->griViewKey}');
                        });
                    }else{
                        layer.alert(res.msg);
                    }
                },'json');     
            }
        });            
        }else{
            layer.alert('{$unChoose}');
        }  
    });
SCRITYT
);
    }
}
