<?php

/**
 * Класс генерации PDF
 *
 * Печатная копия попапа с оценками
 *
 * @version 1.0
 * @author Tugay Ivan <ivan@skiliks.com>
 * @project skiliks
 */
class AssessmentPDF {

    /**
     * Скруглённые углы бара с левой стороны
     */
    const ROUNDED_LEFT = '0011';

    /**
     * Скруглённые углы бара с правой стороны
     */
    const ROUNDED_RIGHT = '1100';

    /**
     * Скруглённые углы бара с обох сторон
     */
    const ROUNDED_BOTH = '1111';

    /**
     * Без скруглённых углов
     */
    const ROUNDED_NONE = '0000';

    /* ----------------------------- */

    /**
     * Бар по позитивной шкале
     */
    const BAR_POSITIVE = 'positive';

    /**
     * Бар по негативной шкале
     */
    const BAR_NEGATIVE = 'negative';

    /* ----------------------------- */

    /**
     * Класс для генерации pdf
     * @var TCPDF
     * @link http://tcpdf.org
     */
    public $pdf = null;

    /**
     * Путь к jpg, png и eps файлам для pdf
     * @var string
     */
    private $images_dir = null;

    /**
     * Номер страницы
     * @var int
     */
    public $page_number = 1;

    /**
     * Создание шаблона pdf задание параметров и конфигов
     * Еденица измерения мм для всего документа
     */

    public  $image_height = null;

    public  $image_width = null;

    public  $debug = false;

    public function __construct() {
        $this->pdf = Yii::createComponent('application.components.tcpdf.tcpdf',
            'P', 'mm', 'A4', true, 'UTF-8');

        //Убрать отступы по краям
        $this->pdf->SetMargins(0,0,0, true);

        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        //это нужно для того чтоб сделать картинку на всю страницу
        $this->pdf->SetAutoPageBreak(false, 0);
    }

    /**
     * Создание страницы по шаблону с eps на весь экран
     */
    public function addPage($number = null) {
        $this->pdf->AddPage();
        if($number !== null){
            $this->page_number = $number;
        }
        // "297.2" вместо "297", чтобы квадраты номеров страниц внизу соприкасались с краем страницы
        // видимо, из-за какой-то ошибки рендера, картинка высотой 297 чуть короче страницы высотой 297
        // и под номером страницы видна белая полоса 0,2 мм
        $this->pdf->ImageEps($this->images_dir.$this->page_number++.'_.eps', 0, 0, 210, 297.2);
    }


    public function addSinglePage($path, $x=0, $y=0, $w=210, $h=297.2) {
        $orientation = ($h>$w) ? 'P' : 'L';
        $this->pdf->AddPage($orientation, [$w, $h]);
        if(null === $this->image_height || null === $this->image_width) {
            $this->image_height = $h;
            $this->image_width = $w;
        }
        $this->pdf->ImageEps($this->images_dir.$path.'.eps', $x, $y, $this->image_width, $this->image_height);
        $this->image_height = null;
        $this->image_width = null;
    }

    public function setEpsSize($w, $h){
        $this->image_height = $h;
        $this->image_width = $w;
    }

    public function renderOnBrowser($name) {
        $this->pdf->Output($name.'.pdf');
    }

    public function saveOnDisk($name) {
        $this->pdf->Output($name.'.pdf', 'F');
        exec("convert ".$name.".pdf ".$name.".jpg");
    }

    /**
     * Печать текста Proxima Nova Bold
     * @param $text текст который будет напечатан
     * @param $x отступ от левого края в мм
     * @param $y отступ от левого края в мм
     * @param $size размер шрифта
     * @param array $color цвет RGB
     */
    public function writeTextBold($text, $x, $y, $size, $color = array(0,0,0)) {
        $this->pdf->SetFont('proxima-nova-bold', '', $size);
        $this->pdf->SetY($y);
        $this->pdf->SetX($x);
        $this->pdf->SetTextColorArray($color);
        $this->pdf->Write(0, $text);
    }

    /**
     * Печать текста Proxima Nova Regular
     * @param $text текст который будет напечатан
     * @param $x отступ от левого края в мм
     * @param $y отступ от левого края в мм
     * @param $size размер шрифта
     * @param array $color цвет RGB
     */
    public function writeTextRegular($text, $x, $y, $size, $color = array(0,0,0)) {
        $this->pdf->SetFont('proxima-nova-regular', '', $size);
        $this->pdf->SetY($y);
        $this->pdf->SetX($x);
        $this->pdf->SetTextColorArray($color);
        $this->pdf->Write(0, $text);
    }

    public function writeTextLeftRegular($width, $height, $x, $y, $size, $text, $font = 'proxima-nova-regular') {
        $this->pdf->SetFont('proxima-nova-regular', '', $size);
        $this->pdf->setCellHeightRatio(1.1);
        $this->pdf->SetFont($font, '', $size - 1);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->MultiCell($width, $height, $text, 1, 'L', $fill=true, $ln=1, $x, $y);
        $this->pdf->setCellHeightRatio(1);
    }

    public function writeTextCenterRegular($width, $height, $x, $y, $size, $text) {
        $this->pdf->SetFont('proxima-nova-regular', '', $size);
        $this->pdf->SetTextColor(0,0,0);
        $this->pdf->MultiCell($width, $height, $text, 1, 'C', $fill=true, $ln=1, $x, $y);


    }

    /**
     * Процентиль
     * @param $x отступ от левого края в мм
     * @param $y отступ от левого края в мм
     * @param $value int значение процентиля
     */
    public function addRatingPercentile($x, $y, $value) {
        $value = round($value);
        $max_width = 13.1;
        $width = $max_width*round($value)/100;

        // серый фон
        $this->pdf->Rect($x+1.12, $y+1.5, 14, 4.1, 'F', '', array(89, 89, 89));

        // ораневая полоса прогресса
        $this->pdf->Rect($x+1.19, $y+1.5, $width, 4.1, 'F', '', array(240, 87, 41));
        $this->pdf->Image($this->images_dir.'percentile.png', $x, $y, 23.96, 7.03);

        $this->writeTextBold('P'.$value, $x+14.9, $y+2.16, 8.13);
    }

    /**
     * Общая оценка
     * @param $x отступ от левого края в мм
     * @param $y отступ от левого края в мм
     * @param $value int вес оценки
     */
    public function addRatingOverall($x, $y, $value)
    {
        $value = round($value);
        $max_width = 22.2;
        $width = $max_width*$value/100;
        $this->pdf->Rect($x+0.9, $y+1, $width, 4.5, 'F', '', array(245, 144, 29));

        $this->pdf->Image($this->images_dir.'stars.png', $x, $y, 23.96, 7.03);

        //$this->writeTextBold($value.'%', $x+28, $y+1.5, 10, [255,255,255]);
        $this->addPercentSmallInfo($value, $x+28, $y+1.5, [255,255,255]);
    }

    /**
     * Добавляет спидометр
     * @param $x отступ от левого края в мм
     * @param $y отступ от левого края в мм
     * @param $value величина в процентах
     */
    public function addSpeedometer($x, $y, $value)
    {
        $value = round($value);
        $x0 = $x;
        $y0 = $y;
        $this->pdf->Image($this->images_dir.'rainbow.png', $x, $y, 29.55, 13.72);

        $this->pdf->StartTransform();
        // Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle
        $x+=12.195;
        $y-=5.3;
        $max_width = 180;
        $angle = (($max_width*$value/100)-90)*-1;
        //var_dump($angle);
        //exit;
        $this->pdf->Rotate($angle, $x+3, $y+19.1);
        $this->pdf->Image($this->images_dir.'arrow.png', $x, $y, 5.16, 40);
        $this->pdf->StopTransform();
        //var_dump($y0 - 118);
        //exit;
        $x = ($x0 +12.7) + (cos(deg2rad(180+$max_width*$value/100)) * 21);
        $y = ($y0 + 8.8) + (sin(deg2rad(180+$max_width*$value/100)) * 21);
        if($value < 25) {
            $x-=8;
            //$y-=1;
        }elseif($value >25 && $value < 50) {
            $x-=3;
            $y-=2;
        }elseif($value >50 && $value <74){
            $x-=3.9;
            $y-=0.8;
        }
        $this->writeTextBold($value.'%', $x+2, $y+2, 12);
    }

    /**
     * Метод изображает на странице круговую диаграмму "Распределение времени"
     *
     * @param float $x, миллиметры
     * @param float $y, миллиметры
     * @param float $productive_time_percent
     * @param float $unproductive_time_percent
     * @param float $communications_management__percent
     *
     * @return void
     */
    public function addTimeDistribution($x, $y, $productive_time_percent, $unproductive_time_percent, $communications_management__percent) {

        $productive_time_percent = round($productive_time_percent);
        $unproductive_time_percent = round($unproductive_time_percent);
        $productive_time = 360*$productive_time_percent/100; // в градусах
        $unproductive_time = 360*$unproductive_time_percent/100; // в градусах

        // непродуктивное время
        $this->pdf->SetFillColor(194, 219, 221);
        $this->pdf->PieSector($x, $y, 24, 0, 360, 'F', false, 0);

        // важные задачи
        $this->pdf->SetFillColor(62, 130, 138);
        $this->pdf->PieSector($x, $y, 24, 360-$productive_time, 360, 'FD', false, 90);

        // неважные задачи
        $this->pdf->SetFillColor(235, 49, 49);
        $this->pdf->PieSector($x, $y, 24, 360-$unproductive_time, 360, 'FD', false, 90+360 - $productive_time);

        // расчёт положения цифр с процентами {

        // радиус окнужности на которой
        // будут находится геометрические центры надписей
        $radius = 16; // миллиметров

        // выполнение приоритетных задачь:
        // величина смещения, относительно центра круга, в мм
        $shifts = $this->getXYforTimeDistribution($productive_time/2, $radius);
        $text = (9 < $productive_time_percent) ? $productive_time_percent . '%' : ''; // подпись
        $this->writeTextBold($text, $x + $shifts['x'], $y + $shifts['y'], 11.87);

        // выполнение приоритетных задачь:
        $shifts = $this->getXYforTimeDistribution($productive_time + ($unproductive_time/2), $radius);
        $text = (9 < $unproductive_time_percent) ? $unproductive_time_percent . '%' : '';
        $this->writeTextBold($text, $x + $shifts['x'], $y + $shifts['y'], 11.87);

        // Двухминутные задачи:
        $shifts = $this->getXYforTimeDistribution(
            $productive_time + $unproductive_time + (360 - $productive_time - $unproductive_time)/2,
            $radius
        );
        $other_time = 100 - $productive_time_percent - $unproductive_time_percent;
        $text = (9 < $other_time) ? $other_time . '%' : '';
        $this->writeTextBold($text, $x + $shifts['x'], $y + $shifts['y'], 11.87);

        // расчёт положения цифр с процентами }
    }

    /**
     * Служебный метод, для определения проекций отрезка на оси X и Y
     * (используется пририсовании подписей в круговой диаграмме "Распределение времени")
     *
     * @param float $alphaOriginal - в градусах градусах
     * @param float $radius, в миллиметрах
     *
     * @return array of float ['x', 'y']
     */
    public function getXYforTimeDistribution($alphaOriginal, $radius)
    {
        // определяем квадрант
        // $alpha - угол, который всегда ближе к Y оси координат
        if (0 <= $alphaOriginal && $alphaOriginal < 90) {
            $k = 1;
            $alpha = $alphaOriginal;
        } elseif (90 <= $alphaOriginal && $alphaOriginal < 180) {
            $k = 2;
            $alpha = 180 - $alphaOriginal;
        } elseif (180 <= $alphaOriginal && $alphaOriginal < 270) {
            $k = 3;
            $alpha = $alphaOriginal - 180;
        } elseif (270 <= $alphaOriginal && $alphaOriginal <= 360) {
            $k = 4;
            $alpha = 360 - $alphaOriginal;
        }

        // далее для расчётов, нам нужен угол меньше 90 градусов в конкретном квадранте

        $beta = 90 - $alpha;

        // по X ось направлена в право
        // по Y ось направлена вниз (!)
        if ($k == 1 || $k == 2) { $kx = 1; } else { $kx = -1; }
        if ($k == 1 || $k == 4) { $ky = -1; } else { $ky = 1; }

        // высчитываем проекции радиуса, повёрнутого на $alphaOriginal градусов
        // на оси X и Y
        $x1 = $radius * cos(deg2rad($beta)) * $kx;
        $y1 = $radius * cos(deg2rad($alpha)) * $ky;

        // наппись "80%" в ширину прмерно 10 мм, а в всоту 4 мм.
        // отнимаем от координат смещения по половине этого расстояния,
        // чтоб центр цифры с % был в координатах {x1, y1}
        $x1 = $x1 - 5;
        $y1 = $y1 - 2;

        // возвращаем смещения
        return [
            'x' => $x1,
            'y' => $y1,
        ];
    }

    /**
     * @param float $x, мм
     * @param float $y, мм
     * @param float $time, секунды
     */
    public function addOvertime($x, $y, $time) {
        $time = (int)round($time);
        if($time === 0) {

        }elseif($time<= 30) {
            // зелёная шкала
            $this->pdf->SetFillColor(136, 200, 134);
            $this->pdf->PieSector($x, $y, 24, 360-($time*360/120), 360, 'F', false, 90);
        }elseif($time > 30 && $time <= 60) {
            // зелёная шкала
            $this->pdf->SetFillColor(136, 200, 134);
            $this->pdf->PieSector($x, $y, 24, 360-(30*360/120), 360, 'F', false, 90);

            // желтая шкала
            $this->pdf->SetFillColor(246, 242, 155);
            $this->pdf->PieSector($x, $y, 24, 360-(($time-30)*360/120), 360, 'F', false, 0);
        }else{
            // зелёная шкала
            $this->pdf->SetFillColor(136, 200, 134);
            $this->pdf->PieSector($x, $y, 24, 360-(30*360/120), 360, 'F', false, 90);

            // желтая шкала
            $this->pdf->SetFillColor(246, 242, 155);
            $this->pdf->PieSector($x, $y, 24, 360-(($time-30)*360/120), 360, 'F', false, 0);

            // красная шкала
            $this->pdf->SetFillColor(236, 48, 48);
            $this->pdf->PieSector($x, $y, 24, 360-(($time-60)*360/120), 360, 'F', false, -90);
        }

        $this->pdf->SetFillColor(100, 101, 103);
        $this->pdf->PieSector($x, $y, 18.15, 0, 360, 'F', false, 0);
        if ($time < 10) {
            $x-=7.7;
            $y-=12;
        } elseif ($time >= 10 && $time < 100) {
            $x-=12.5;
            $y-=12;
        } elseif ($time >= 100 && $time < 110) {
            $x-=16.5;
            $y-=12;
        } elseif ($time >= 110 && $time < 120) {
            $x-=14.5; // единица - узкая цифра. И "112" получается уже чем "102" или "120"
            $y-=12;
        } else {
            $x-=16.5;
            $y-=12;
        }
        $this->writeTextRegular($time, $x, $y, 56.79, [255, 255, 255]);
    }

    /**
     * @param float $x, мм
     * @param float $y, мм
     * @param int $value, минуты
     * @param int $max_value, минуты
     */
    public function addTimeBarProductive($x, $y, $value, $max_value) {
        $value = (int)round($value);
        if ((int)$max_value === 0) {
            $width = 0;
        } else {
            $width = 57 * ($value/$max_value);
        }
        if($value === $max_value) {
            $round_corner = '1111';
        } else {
            $round_corner = '0011';
        }
        if($width!==0 && $value !== 0){
            $this->pdf->RoundedRect($x, $y, $width, '6.7', $r = '1', $round_corner, 'F', '', array(62, 130, 138));
        }
        $x+= ($width/2)-4;
        $y+=1;
        if (9 < $value && 6 < $width) {
            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
        } elseif($value < 10 && 2 < $width) {
            $x+= 1.5;
            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
        }
    }

    /**
     * @param float $x, мм
     * @param float $y, мм
     * @param int $value, минуты
     * @param int $max_value, минуты
     */
    public function addTimeBarUnproductive($x, $y, $value, $max_value) {
        $value = (int)round($value);
        if((int)$max_value === 0) {
            $width = 0;
        }else{
            $width = 57 * ($value/$max_value);
        }
        if($value === $max_value) {
            $round_corner = '1111';
        }else{
            $round_corner = '0011';
        }
        if($width!==0 && $value !== 0) {
            $this->pdf->RoundedRect($x, $y, $width, '6.7', $r = '1', $round_corner, 'F', '', [235, 49, 49]);
        }
        $x+= ($width/2)-4;
        $y+=1;
//        if($x >= 139) {
//            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
//        }
        if (9 < $value && 6 < $width) {
            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
        } elseif($value < 10 && 2 < $width) {
            $x+= 1.5;
            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
        }
    }

    /**
     * @param float $x, мм
     * @param float $y, мм
     * @param float $value, проценты
     * @param $max_width, мм
     * @param string $round_corner, парамент TCPDF отвечающий за скругление углов
     * @param string $type, позитивная/негативная шкала
     */
    public function addUniversalBar($x, $y, $value, $max_width, $round_corner, $type) {
        $value = (int)round($value);
        if($type === self::BAR_POSITIVE) {
            $color = [62, 130, 138];
        }else{
            $color = [235, 49, 49];
        }
        $width = $max_width * $value/100;
        $this->pdf->SetLineStyle(array('width' => 0.2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->pdf->RoundedRect($x, $y, $max_width, '6.6', $r = '1', $round_corner, 'FD', '', [189,210,213]);
        if((int)$value !== 100) {
            if($round_corner === self::ROUNDED_RIGHT){
                $round_corner = self::ROUNDED_NONE;
            } else {
                $round_corner = self::ROUNDED_LEFT;
            }
        }
        if((int)$value !== 0){
            $this->pdf->RoundedRect($x, $y, $width, '6.6', $r = '1', $round_corner, 'FD', '', $color);
        }

        $x+= ($width/2)-5.4;
        if($type === self::BAR_NEGATIVE) {
            if($value < 24){
                $x+=1.4;
            }else{
                $x+=1.1;
            }
        }
        if($width >= 10) {
            $this->writeTextBold($value.'%', $x, $y+1, 12, [255,255,255]);
        }
    }

    /**
     * Находит максимальное значение среди TimeManagementAggregated
     * @param array $time
     * @return int
     */
    public function getMaxTimeNegative($time)
    {
        $data = [
            round($time[TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS]),
            round($time[TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS]),
            round($time[TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS]),
            round($time[TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL]),
            round($time[TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING]),
        ];

        return (int)max($data);
    }

    /**
     * Находит максимальное значение среди TimeManagementAggregated
     * @param $time
     * @return int
     */
    public function getMaxTimePositive($time)
    {
        $data = [
            round($time[TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS]),
            round($time[TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS]),
            round($time[TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS]),
            round($time[TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL]),
            round($time[TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING]),
        ];

        return (int)max($data);
    }

    /**
     * Геттер категории с массива
     * @param $performance
     * @param $category
     * @return int
     */
    public function getPerformanceCategory($performance, $category)
    {
        if(isset($performance[$category])){
            return (int)round($performance[$category]);
        } else {
            return 0;
        }
    }

    /**
     * Выводит маленькие проценты напротив областей обучений
     * @param $percent
     * @param $x мм
     * @param $y мм
     * @param array $color
     */
    public function addPercentSmallInfo($percent, $x, $y, $color=[255,255,255]) {
        $percent = (int)round($percent);
        if($percent <= 9) {
            $x+= 1.7;
        } elseif($percent > 10 && $percent < 100) {
            $x+= 0.9;
        }
        $this->writeTextBold($percent.'%', $x, $y, 10, $color);

    }

    /**
     * Выводит средьние проценты
     * @param $percent
     * @param $x мм
     * @param $y мм
     * @param array $color
     */
    public function addPercentMiddleInfo($percent, $x, $y, $color=[0,0,0]) {
        $percent = (int)round($percent);
        if($percent <= 9) {
            $x+= 2.4;
        } elseif($percent > 10 && $percent < 100) {
            $x+= 1.2;
        }
        $this->writeTextBold($percent.'%', $x, $y, 16, $color);

    }

    /**
     * Выводит большие проценты
     * @param int $percent
     * @param int $x мм
     * @param int $y мм
     * @param array $color
     */
    public function addPercentBigInfo($percent, $x, $y, $color=[0,0,0]) {

        $percent = (int)round($percent);
        if($percent <= 9) {
            $x+= 3;
        } elseif($percent > 10 && $percent < 100) {
            $x+= 1.2;
        }
        $this->writeTextBold($percent.'%', $x, $y, 18, $color);

    }

    /**
     * @param string $path
     */
    public function setImagesDir($path)
    {
        $this->images_dir = __DIR__.'/../system_data/tcpdf/'.$path;
    }

    public function writeHtml($html, $padding_top) {
        /*
        1.не работает
        2.высота в мм
        3. true - 0 = верхний левый угол)
        */
        $this->pdf->setXY(0, $padding_top, true);

        $this->pdf->SetFont('proxima-nova-regular', '', 11);
        $this->pdf->SetFont('dejavusans', '', 11);
        $this->pdf->SetTextColor(0,0,0);
        $html = '<style>
                 </style>
                <body>
                    <table style="background-color: #ff0000;">
                        '.$html.'
                    </table>
                </body>';

        /*$html = '
                <!-- EXAMPLE OF CSS STYLE -->
                <style>
                </style>
                <body>
                <table>
                <tr>
                <td></td>
                <td><h3>aaaa</h3>
                <font face="dejavusans" style="font-weight: bold;">текст 1</font><br/>
                <font face="dejavusans">текст 2</font><br/>
                <span style="font-weight: bold;">bold text текст 3</span><br/>
                <span>текст 4 </span></td>
                </tr>
                <table>
                </body>';*/


        $this->pdf->writeHTML($html, true, false, true, false, '');
    }
}