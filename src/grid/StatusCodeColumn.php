<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 19:32
 */
namespace lbmzorx\components\grid;

use Yii;
/**
 * @inheritdoc
 */
class StatusCodeColumn extends \yii\grid\DataColumn
{

    public $jsOptions=[];
    public $griViewKey=0;
    public static $uesed = 0;
    public function init()
    {
        parent::init();
        if(empty($this->jsOptions) || empty($this->jsOptions['btn'])){
            $this->jsOptions['btn']=[Yii::t('app','ok'),Yii::t('app','cancel')];
        }
    }

    protected function renderFilterCellContent()
    {


        if(static::$uesed ==0){

            $btn = implode('\',\'',$this->jsOptions['btn']);
            $laydateJs =<<<str
        $('.{$this->attribute}-change').click(function(){
            var sval=$(this).attr('key'),this_dom=$(this),
                sid=$(this).attr('data-id');
            var dom_status_change=$('#{$this->attribute}-change-dom');                
            dom_status_change.find('input[value="'+sval+'"]').prop('checked','true');
            dom_status_change.find('input[name="id"]').val(sid);
            updateForm(dom_status_change,'#w{$this->griViewKey}',['{$btn}']);
        });
str;
            \yii::$app->getView()->registerJs($laydateJs);

        }
        return parent::renderFilterCellContent();
    }
}