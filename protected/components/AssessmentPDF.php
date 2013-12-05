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

    public function writeTextBold($text, $x, $y, $size) {
        $this->pdf->SetFont('proxima-nova-bold', '', $size);
        $this->pdf->SetY($y);
        $this->pdf->SetX($x);
        $this->pdf->Write(0, $text);
    }

    public function addRatingPercentile($x, $y, $value) {
        $max_width = 13.1;
        $width = $max_width*$value/100;

        $this->pdf->Rect($x+1.12, $y+1.5, 14, 4.1, 'F', '', array(89, 89, 91));
        $this->pdf->Rect($x+1.19, $y+1.5, $width, 4.1, 'F', '', array(210, 91, 47));
        $this->pdf->Image($this->images_dir.'Percentile.png', $x, $y, 23.96, 7.03);

        $this->writeTextBold('P'.$value, $x+14.9, $y+2.16, 8.13);
    }

}