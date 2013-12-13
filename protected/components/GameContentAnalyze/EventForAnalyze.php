<?php

/**
 * Это анализатор и коментировать не нужно
 * Class EventForAnalyze
 */
class EventForAnalyze
{
    // @var EventSample
    /**
     * @var
     */
    public $event;

    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $title;

    /**
     * @var
     */
    public $startTime;

    /**
     * @var
     */
    public $cssIcon;

    /**
     * @var int
     */
    public $delay = 0;

    /**
     * @var string
     */
    public $durationFrom = '00:00:00';

    /**
     * @var string
     */
    public $durationTo = '00:00:00';

    /**
     * @var
     */
    public $cssIconTitle;

    /**
     * @var
     */
    public $cssRowColor;

    /**
     * @var
     */
    public $replicas;

    /**
     * @var array
     */
    public $producedBy = [];

    /**
     * @var array
     */
    public $possibleNextEvents = [];

    /**
     * @var array
     */
    public $flagsToSwitch = [];

    /**
     * @var array
     */
    public $flagsBlock = [];
}
