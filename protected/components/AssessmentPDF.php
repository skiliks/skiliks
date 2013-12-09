<?php

class AssessmentPDF {

    /**
     * @var TCPDF
     */
    public $pdf;
    public $images_dir;
    public $page_number = 1;
    public function __construct() {
        $this->pdf = Yii::createComponent('application.components.tcpdf.tcpdf',
            'P', 'mm', 'A4', true, 'UTF-8');
        $this->images_dir = __DIR__.'/../system_data/tcpdf/images/';

        //Убрать отступы по краям
        $this->pdf->SetMargins(0,0,0, true);

        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);

        //это нужно для того чтоб сделать картинку на всю страницу
        $this->pdf->SetAutoPageBreak(false, 0);
    }

    public function addPage() {
        $this->pdf->AddPage();
        $this->pdf->ImageEps($this->images_dir.$this->page_number++.'_.eps', 0, 0, 210, 297);
    }

    public function renderOnBrowser($name) {
        $this->pdf->Output($name.'.pdf');
    }

    public function writeTextBold($text, $x, $y, $size, $color = array(0,0,0)) {
        $this->pdf->SetFont('proxima-nova-bold', '', $size);
        $this->pdf->SetY($y);
        $this->pdf->SetX($x);
        $this->pdf->SetTextColorArray($color);
        $this->pdf->Write(0, $text);
    }

    public function writeTextRegular($text, $x, $y, $size, $color = array(0,0,0)) {
        $this->pdf->SetFont('proxima-nova-regular', '', $size);
        $this->pdf->SetY($y);
        $this->pdf->SetX($x);
        $this->pdf->SetTextColorArray($color);
        $this->pdf->Write(0, $text);
    }

    public function addRatingPercentile($x, $y, $value) {
        $max_width = 13.1;
        $width = $max_width*$value/100;

        $this->pdf->Rect($x+1.12, $y+1.5, 14, 4.1, 'F', '', array(89, 89, 91));
        $this->pdf->Rect($x+1.19, $y+1.5, $width, 4.1, 'F', '', array(210, 91, 47));
        $this->pdf->Image($this->images_dir.'Percentile.png', $x, $y, 23.96, 7.03);

        $this->writeTextBold('P'.$value, $x+14.9, $y+2.16, 8.13, [255,255,255]);
    }

    public function addRatingOverall($x, $y, $value)
    {
        $max_width = 22.2;
        $width = $max_width*$value/100;
        $this->pdf->Rect($x+0.9, $y+1.5, $width, 4.1, 'F', '', array(223, 146, 46));
        $this->pdf->Image($this->images_dir.'Stars.png', $x, $y, 23.96, 7.03);

        $this->writeTextBold($value.'%', $x+28, $y+1.5, 10);
    }

    public function addSpeedometer($x, $y, $value)
    {
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
        $this->pdf->Image($this->images_dir.'Arrow.png', $x, $y, 5.16, 40);
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

        $productive_time = 360*$productive_time_percent/100; // в градусах
        $unproductive_time = 360*$unproductive_time_percent/100; // в градусах

        $this->pdf->SetFillColor(202, 219, 220);
        $this->pdf->PieSector($x, $y, 24, 0, 360, 'F', false, 0);


        $this->pdf->SetFillColor(61, 106, 113);
        $this->pdf->PieSector($x, $y, 24, 360-$productive_time, 360, 'F', false, 90);

        $this->pdf->SetFillColor(205, 56, 54);
        $this->pdf->PieSector($x, $y, 24, 360-$unproductive_time, 360, 'F', false, 90+360 - $productive_time);

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

        // прочее:
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

    public function addOvertime($x, $y, $red, $green, $yellow, $time) {

        $red = (360 - (360*$red/100));
        //var_dump($productive_time);
        //exit;
        $green = (360 - (360*$green/100));

        $this->pdf->SetFillColor(248, 243, 159);
        $this->pdf->PieSector($x, $y, 24, 0, 360, 'F', false, 0);

        $this->pdf->SetFillColor(205, 56, 54);
        $this->pdf->PieSector($x, $y, 24, $red, 360, 'F', false, -90);

        $this->pdf->SetFillColor(158, 200, 138);
        $this->pdf->PieSector($x, $y, 24, $green, 360, 'F', false, -90+$red);


        $this->pdf->SetFillColor(100, 101, 103);
        $this->pdf->PieSector($x, $y, 18.15, 0, 360, 'F', false, 0);
        if($time < 10) {
            $x-=7.7;
            $y-=12;
        }elseif($time >= 10 && $time < 100) {
            $x-=12.5;
            $y-=12;
        } else {
            $x-=16.5;
            $y-=12;
        }
        $this->writeTextRegular($time, $x, $y, 56.79, [255, 255, 255]);
    }

    public function addTimeBarProductive($x, $y, $value, $max_value) {

        $width = 57 * ($value/$max_value);
        if((int)$value === 100) {
            $round_corner = '1111';
        }else{
            $round_corner = '0011';
        }
        $this->pdf->RoundedRect($x, $y, $width, '6.7', $r = '1', $round_corner, 'F', '', array(61, 102, 113));
        $x+= ($width/2)-4;
        $y+=1;
        if($x >= 34.8) {
            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
        }

    }

    public function addTimeBarUnproductive($x, $y, $value, $max_value) {

        $width = 57 * ($value/$max_value);
        if((int)$value === 100) {
            $round_corner = '1111';
        }else{
            $round_corner = '0011';
        }
        $this->pdf->RoundedRect($x, $y, $width, '6.7', $r = '1', $round_corner, 'F', '', [205,56,54]);
        $x+= ($width/2)-4;
        $y+=1;
        //var_dump($x);
        //exit;
        if($x >= 139) {
            $this->writeTextBold($value, $x, $y, 12, [255,255,255]);
        }

    }

}