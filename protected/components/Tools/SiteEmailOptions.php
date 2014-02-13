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
        // количество букв которое гарантировано помещается в строку
        // на большинстве устройств,
        // число найдено просто подбором
        $rowLength = 85;

        // Счётчик для подсчёта колисечтва символов в HTML коде ссылок.
        // Эту цифру мы потом вычитаем из длинны письма.
        // Иначе $text1 оказывается полу-пустой
        // -- программа полагает что в $text1 1000 символов, а 500 из них -- это HTML ссылок,
        // который не влияет на реальную длинну строки в письме.
        $totalLinksHtmlCodeLength = 0;

        $text = str_replace('<br>', '<br/>', $text);
        $text = str_replace('" >', '">', $text);

        // точка в которой текст будет делиться между $this->text1 и $this->text2
        $delimiter = 0;

        /* счётчик строк. С его помощью мы проверяем, что верхний текст по высоте помещается в вёрстку */
        /* предел $maxRows строк */
        $maxRows = 8;
        $rowCounter = 0;

        /* $p1, $p2 - position1, position2 */
        $p2 = 0;

        $n = mb_substr_count($text, '<br/>', 'UTF-8');

        // весь текст написан в одну строку
        if (0 == $n) {
            if (mb_strlen($text, 'UTF-8') < $rowLength * $maxRows) {
                // тест короче чем верхняя область
                $delimiter = mb_strlen($text, 'UTF-8');
            } else {
                // тест длиннее чем верхняя область
                $delimiter = $rowLength * $maxRows;
            }
        } else {
            // текст имеет несколько строк
            for ($i = 0; $i < $n + 1; $i++) {
                $p1 = $p2;
                $p2 = mb_strpos($text, '<br/>', $p1, 'UTF-8');

                if ($p2 < 1 && 0 < $i) {
                    $p2 = mb_strlen($text, 'UTF-8');
                } else {
                    $p2 += 5;
                }

                $rowCounter += ceil(($p2 - $p1)/$rowLength);

                // в верхний блок помещается до 14 строк
                if ($rowCounter < $maxRows) {
                    $delimiter = $p2;
                    // считаем дальше
                } else {
                    $previousRowCounter = $rowCounter - ceil(($p2 - $p1)/$rowLength);
                    $extraCharacters = $p2 - $p1 - (8 - $previousRowCounter)*$rowLength;
                    $delimiter = $p2 - $extraCharacters;

                    // верхний блок ($this->text1) уже заполнен
                    break;
                }
            }
        }

        $this->text1 = mb_substr($text, 0, $delimiter, 'UTF-8');
        $this->text2 = mb_substr($text, $delimiter, null, 'UTF-8');

        // Смещаем $delimiter до последнего пробела в $this->text1,
        // если $delimiter оказался в середине слова
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
            $lastSpaceInText1 = strrpos($this->text1, ' ');

            $delimiter = $lastSpaceInText1;

            $this->text1 = substr($text, 0, $delimiter);
            $this->text2 = substr($text, $delimiter);
        }
    }

    /**
     * Альтернативный вариант setText().
     * На данный момент недоработан.
     *
     * @param string $text
     */
    public function setCustomText($message) {

        $length_max = 690;
        $length = mb_strlen($message, 'UTF-8');
        if($length <= $length_max) {
            $this->text1 = $message;
            $this->text2 = '';
        } else {
            $text1 = mb_substr($message, 0, $length_max, 'UTF-8');
            $text2 = mb_substr($message, $length_max, $length - $length_max, 'UTF-8');
            if(mb_substr($message, $length_max, 1, 'UTF-8') === " "){
                $this->text1 = $text1;
                $this->text2 = $text2;
            } else {
                $pos = mb_strrpos($text1, " ", 0, "UTF-8");
                $this->text1 = mb_substr($message, 0, $pos, 'UTF-8');
                $this->text2 = /*mb_substr($message, $pos, $length_max - mb_strlen($this->text1, 'UTF-8'), 'UTF-8');.*/$text2;
            }
            //$this->text1 = mb_substr($message, 0, $length_max, 'UTF-8');
            //$this->text2 = mb_substr($message, $length_max, $length - $length_max, 'UTF-8');
        }

    }
}