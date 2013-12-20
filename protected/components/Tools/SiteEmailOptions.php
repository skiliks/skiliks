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

    /**
     * По сути - картинка, то какой гелой будет отображен справа от теста письма
     * 'anjela', 'denejnaia', 'fikus', 'jeleznij', 'krutko', 'trudiakin'
     * @var string
     */
    public $template = 'fikus';

    /**
     * Заголовок письма <title>
     * @var string
     */
    public $title = 'Skiliks - game the skills!';

    /**
     * Основной заголовок текста письма <h1>
     * @var string
     */
    public $h1 = 'Приветствуем!';

    /**
     * Верхний текст письма - сразу под <h1>, слева от изображение героя
     * @var string
     */
    public $text1 = '';

    /**
     * Нижний текст письма - под изображением героя.
     * @var string
     */
    public $text2 = '';
}