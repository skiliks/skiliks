<?php

/**
 * Структура письма для сайта
 */
class SiteEmailOptions {

    /**
     * От кого
     * @var string
     */
    public $from;
    /**
     * Кому
     * @var string
     */
    public $to;
    /**
     * Тема
     * @var string
     */
    public $subject;
    /**
     * Текст письма
     * @var string
     */
    public $body;
    /**
     * Изображения для оформления
     * @var array
     */
    public $embeddedImages = [];

} 