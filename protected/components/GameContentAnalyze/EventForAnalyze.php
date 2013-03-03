<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/1/13
 * Time: 11:49 PM
 * To change this template use File | Settings | File Templates.
 */
class EventForAnalyze
{
    // @var EventSample
    public $event;

    public $type;

    public $title;

    public $startTime;

    public $cssIcon;

    public $delay = 0;

    public $durationFrom = '00:00:00';

    public $durationTo = '00:00:00';

    public $cssIconTitle;

    public $cssRowColor;

    public $replicas;

    public $producedBy = [];

    public $possibleNextEvents = [];
}
