<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-22 17:17
 */
namespace lbmzorx\components\event;

class SearchEvent extends \yii\base\Event
{
    const BEFORE_SEARCH = 1;
    const AFTER_SEARCH = 2;

    /** @var $query \yii\db\ActiveQuery */
    public $query;

}