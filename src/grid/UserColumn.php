<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 19:32
 */

namespace lbmzorx\components\grid;


use Yii;
use yii\helpers\Html;
/**
 * @inheritdoc
 */
class UserColumn extends \yii\grid\DataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $img=Html::img($model->user['head'],['style'=>'with:50px;heigth:50px;','alt'=>$model->user['name']]);
        return Html::a($img,['/user/default/index','id'=>$model->user['id']]);
    }

}