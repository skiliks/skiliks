<?php

class SiteUnitTest extends CDbTestCase
{
    public function testGoogleAnalyticsExists()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped();
        }

        $url = 'http://loc.skiliks.com';
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);

        $this->assertContains("(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');", $response);
        $this->assertContains("ga('create', 'UA-42053049-1', 'skiliks.com')", $response);
        $this->assertContains("ga('send', 'pageview');", $response);
        $this->assertContains('google-analytics.com/analytics.js', $response);
    }

    public function testTCPDFworks()
    {
        /**
         * @var TCPDF $pdf
         */
        $pdf = Yii::createComponent('application.components.tcpdf.tcpdf',
            'P', 'mm', 'A4', true, 'UTF-8');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        //текст на русском
        $pdf->SetFont('arialcyr', '', 7, '', false);
        $pdf->SetX(40);
        $pdf->SetY(132);
        $pdf->Write(0, "Иван Иванов", '', 0, '', false, 0, false, false, 0);

        $pdf->Output('test.pdf');

        // если тест дошел до этой строки - значит TCPD работает
        $this->assertTrue(true);
    }
} 