<?php

/**
 * Структура письма для сайта
 */
class SiteEmailOptions {

    // Значения для $this->template
    const TEMPLATE_FIKUS     = 'fikus';
    const TEMPLATE_ANJELA    = 'anjela';
    const TEMPLATE_DENEJNAIA = 'denejnaia';
    const TEMPLATE_JELEZNIJ  = 'jeleznij';
    const TEMPLATE_KRUTKO    = 'krutko';
    const TEMPLATE_TRUDIAKIN = 'trudiakin';

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

    /**
     * Разбивает строку $text на 2 и заносит в $this->text1, $this->text2 так,
     * чтобы большой text1 не рвал вёрстку письма
     *
     * @param string $text
     */
    public function setText($text)
    {
        $rowLength = 85; // количество букв, число найдено просто подбором

        $text = str_replace('<br>', '<br/>', $text);

        // точка в которой текст будет делиться между $this->text1 и $this->text2
        $delimiter = 0;

        /* счётчик строк. С его помощью мы проверяем, что верхний текст по высоте помещается в вёрстку */
        /* предел 14 строк */
        $rowCounter = 0;

        /* $p1, $p2 - position1, position2 */
        $p2 = 0;

        $n = substr_count($text, '<br/>');

        for ($i = 0; $i < $n + 1; $i++) {
            $p1 = $p2;
            $p2 = strpos($text, '<br/>', $p1);

            if ($p2 < 1 && 0 < $i) {
                $p2 = strlen($text);
            } else {
                $p2 += 5;
            }

            $rowCounter += ceil(($p2 - $p1)/$rowLength);

            // в верхний блок помещается до 14 строк
            if ($rowCounter < 14) {
                $delimiter = $p2;
                // считаем дальше
            } else {
                $previousRowCounter = $rowCounter - ceil(($p2 - $p1)/$rowLength);
                $extraCharacters = $p2 - $p1 - (14 - $previousRowCounter)*$rowLength;
                $delimiter = $p2 - $extraCharacters;

                // верхний блок ($this->text1) уже заполнен
                break;
            }
        }

        $this->text1 = substr($text, 0, $delimiter);
        $this->text2 = substr($text, $delimiter);

        // добавляем знак переноса, если он нужен:
        // 1. text2 не пустой
        // 2. в конце text1 и в начале text2 нет пробела или знаков припинания
        if (
            '' < $this->text2
            && $this->text1[strlen($this->text1) - 1] !== ' '
            && $this->text1[strlen($this->text1) - 1] !== ','
            && $this->text1[strlen($this->text1) - 1] !== '.'
            && $this->text1[strlen($this->text1) - 1] !== '!'
            && $this->text1[strlen($this->text1) - 1] !== '?'
            && $this->text1[strlen($this->text1) - 1] !== ':'
            && $this->text1[strlen($this->text1) - 1] !== '-'
            && $this->text1[strlen($this->text1) - 1] !== '—'
            && $this->text1[strlen($this->text1) - 1] !== '>'
            && $this->text1[strlen($this->text1) - 1] !== '"'
            && $this->text1[strlen($this->text1) - 1] !== "'"
            && $this->text1[strlen($this->text1) - 1] !== '«'
            && $this->text1[strlen($this->text1) - 1] !== '»'
            && $this->text2[0] !== ' '
            && $this->text2[0] !== ','
            && $this->text2[0] !== '.'
            && $this->text2[0] !== '!'
            && $this->text2[0] !== '?'
            && $this->text2[0] !== ':'
            && $this->text2[0] !== '-'
            && $this->text2[0] !== '—'
            && $this->text2[0] !== '<'
            && $this->text2[0] !== '"'
            && $this->text2[0] !== "'"
            && $this->text2[0] !== '«'
            && $this->text2[0] !== '»'
        ) {
            $this->text1 .= ' -';
        }
    }
}