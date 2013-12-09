<?php

class PDFController extends SiteBaseController {

    public function actionSimulationDetailPDF()
    {

        /*$pdf = Yii::createComponent('application.components.tcpdf.tcpdf',
            'P', 'mm', 'A4', true, 'UTF-8');
        //$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $images_dir = __DIR__.'/../system_data/tcpdf/images/';
        // $pdf->SetMargins(0,0,0, true);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        //это нужно для того чтоб сделать картинку на всю страницу
        $pdf->SetAutoPageBreak(false, 0);

        // $pdf->setImageScale(3);
        //большой JPG в фоне
        $pdf->AddPage();
        $pdf->Image($images_dir.'page1.jpg', 0, 0, 210, 297);

        //текст на русском
        $pdf->SetFont('proxima-nova-bold', '', 20, '', false);
        $pdf->SetX(20);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        //прямоугольник
        $pdf->Rect(90, 90, 40, 10, 'F', '', array(255, 170, 96));
        //скуругленные углы
        $pdf->RoundedRect($x='30', $y='60', $w='50', $h='20', $r = '2', '1111', 'F', '', array(255, 170, 96));

        //картинка-срелка повёрнутая на какой-то не кратный 90 градус (33 к примеру)
        //$pdf->AddPage();
        $pdf->StartTransform();
        // Rotate 20 degrees counter-clockwise centered by (70,110) which is the lower left corner of the rectangle

        $pdf->Rotate(-33, 47.5, 109.5);
        $pdf->Image($images_dir.'arrow.png', 30, 92, 35, 35);
        //$pdf->Image('images/page1.jpg', 0, 0, 210, 297);
        // Stop Transformation
        $pdf->StopTransform();

        $pdf->Circle(25,105,20);
        //$pdf->Circle(25,105,10, 90, 180, null);
        $pdf->PieSector(100, 140, 20, 20, 180, 'F', false, 0, 2);
        //$pdf->Circle(25,105,10, 270, 360, 'C');
        //$mask = $pdf->Image('images/blick.png', 60, 110, 35, 35, 'PNG');
        //$pdf->Image('images/blick.png', 60, 110, 35, 35, 'PNG','','',false, 300, '', false, $mask);

        //$mask = $pdf->Image('images/mask.png', 50, 140, 100, '', '', '', '', false, 300, '', true);
        //$pdf->Image('images/stars.png', 50, 140, 100, '', '', 'http://www.tcpdf.org', '', false, 300, '', false, $mask);

        $pdf->Image($images_dir.'blick.png', 70, 70, 100, '', '', 'http://www.tcpdf.org', '', false, 300);


        //текст на русском
        $pdf->SetFont('arialcyr', '', 7, '', false);
        $pdf->SetX(40);
        $pdf->SetY(132);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        $pdf->SetFont('proxima-nova-regular', '', 14, '', false);
        $pdf->SetX(40);
        $pdf->SetY(152);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        $pdf->SetFont('proxima-nova-bold', '', 14, '', false);
        $pdf->SetX(40);
        $pdf->SetY(162);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        $pdf->SetFont('proximanovaltb', '', 14, '', false);
        $pdf->SetX(40);
        $pdf->SetY(172);
        $pdf->Write(0, "123 Фыв Asd", '', 0, '', false, 0, false, false, 0);

        $pdf->SetFont('proxima-nova-regular', 'U', 14, '', false);
        $pdf->SetX(40);
        $pdf->SetY(182);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        $pdf->SetFont('proxima-nova-regular', 'I', 14, '', false);
        $pdf->SetX(40);
        $pdf->SetY(192);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        $pdf->Output('test.pdf');*/

        $pdf = new AssessmentPDF();
        /*$pdf->addPage();
        $pdf->writeTextBold('Иван Иванов', 3.5, 3.5, 21);
        $pdf->addRatingPercentile(92.4, 37.6, 4);
        $pdf->addRatingOverall(85, 48, 100);
        $pdf->addSpeedometer(19.8, 109.2, 23);
        $pdf->addSpeedometer(87.9, 109.2, 56);
        $pdf->addSpeedometer(156.9, 109.2, 97);*/
        $pdf->page_number = 2;
        $pdf->addPage();//*/
        $pdf->writeTextBold('Иван Иванов', 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 184.1, 28.4, 10, array(255,255,255));
        $pdf->addTimeDistribution(53.9, 89.7,    25,25,50);
        $pdf->addOvertime(156.2, 90.7,  50, 25, 25,  120);

        $pdf->writeTextBold('100%', 177, 175.84, 10, [255,255,255]);//Распеределение времени

        $pdf->writeTextBold('87%', 82.1, 197.5, 16);//Продуктивное время


        $pdf->writeTextBold('100%', 185, 197.5, 16);//Не продуктивное время

        $pdf->addTimeBarProductive(33, 218, 30, 100);

        /*$pdf->addPage();
        $pdf->addPage();
        $pdf->addPage();
        $pdf->addPage();
        $pdf->addPage();*/
        $pdf->renderOnBrowser('Assessment_v2');
    }

} 