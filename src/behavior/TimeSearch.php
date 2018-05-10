<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2018-01-22 17:23
 */
namespace lbmzorx\components\behavior;

use lbmzorx\components\event\SearchEvent;
class TimeSearch extends \yii\base\Behavior
{

    public $timeAttributes = [];

    public $delimiter = "~";

    public $format = "int";


    public function init()
    {
        parent::init();
    }

    public function events()
    {
        return [
            SearchEvent::BEFORE_SEARCH => 'beforeSearch'
        ];
    }

    public function beforeSearch($event)
    {
        /** @var $event \lbmzorx\components\events\SearchEvent */
        foreach ($this->timeAttributes as $filed => $attribute) {
            if($attribute !== null) $timeAt = $event->sender->{$attribute};
            if( !empty($timeAt) ){
                $time =explode($this->delimiter, $timeAt);
                sort($time);
                if( $this->format === 'int' ){
                    $startAt = strtotime($time[0]);
                    $endAt = strtotime($time[1]);
                }else{
                    $startAt = $time[0];
                    $endAt = $time[1];
                }
                $event->query->andFilterWhere(['>=',$filed,$startAt]);
                $event->query->andFilterWhere(['<=',$filed,$endAt]);
            }
        }
    }
}