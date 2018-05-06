<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 19:32
 */

namespace lbmzorx\components\grid;

use lbmzorx\components\widget\LayuiUse;
use Yii;
use yii\helpers\Html;

/**
 * @inheritdoc
 */
class DateTimeColumn extends \yii\grid\DataColumn
{

    public $format = 'datetime';

    public $filter = "default";

    public $layerOptions = [];

    public $filterInputOptions = ["class" => "form-control date-time"];
    public static $layUsed=0;

    public function init()
    {
        parent::init();
        !isset($this->layerOptions['type']) && $this->layerOptions['type'] = 'datetime';
        !isset($this->layerOptions['range']) && $this->layerOptions['range'] = '~';
        !isset($this->layerOptions['theme']) && $this->layerOptions['theme'] = 'molv';
        !isset($this->layerOptions['max']) && $this->layerOptions['max'] = '0';
        !isset($this->layerOptions['calendar']) && $this->layerOptions['calendar'] = 'true';
    }

    protected function renderFilterCellContent()
    {
        $laydateJs =<<<str
            $('.date-time').each(function(){
                laydate.render({
                    elem: this,
                    type: '{$this->layerOptions['type']}',
                    range: '{$this->layerOptions['range']}',
                    theme: '{$this->layerOptions['theme']}',
                    max: {$this->layerOptions['max']},
                    //显示公历
                    calendar: {$this->layerOptions['calendar']},          
                    done: function(value, date, endDate){                    
                        $(this.elem).val(value);
                        $(this.elem).change();                   
                    }
                });
                $(this).mouseover(function(){
                    layer.tips($(this).val(),this,{
                        tips: [1, '#78BA32']       
                    });
                });
            });
str;
      LayuiUse::widget(['content'=>$laydateJs]);
//        if(static::$layUsed==0){
//            yii::$app->getView()->registerJs($laydateJs);
//        }

        if ($this->grid->filterModel->hasErrors($this->attribute)) {
            Html::addCssClass($this->filterOptions, 'has-error');
            $error = ' ' . Html::error($this->grid->filterModel, $this->attribute, $this->grid->filterErrorOptions);
        } else {
            $error = '';
        }
        return Html::activeTextInput($this->grid->filterModel, $this->attribute, $this->filterInputOptions) . $error;

    }
}