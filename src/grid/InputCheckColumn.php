<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27
 * Time: 22:00
 */

namespace lbmzorx\components\grid;

use yii\grid\Column;
use yii\widgets\InputWidget;

class InputCheckColumn  extends Column
{
    public $header='';
    public $attribute;

    protected function renderDataCellContent($model, $key, $index)
    {
        return $model->{$this->attribute};
    }
}