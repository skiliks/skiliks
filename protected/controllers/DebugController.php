<?php

class DebugController extends SiteBaseController
{

    public function actionIndex()
    {
        $sim_id = Yii::app()->request->getParam('sim_id');
        $simulation = Simulation::model()->findByPk($sim_id);

        TimeManagementAggregatedDebug::model()->deleteAllByAttributes(['sim_id'=>$simulation->id]);

        $tma = new TimeManagementAnalyzerDebug($simulation);
        $tma->calculateAndSaveAssessments();
        $assessment_debug = TimeManagementAggregatedDebug::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'slug'=>'1st_priority_phone_calls'
        ]);

        $assessment = TimeManagementAggregated::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'slug'=>'1st_priority_phone_calls'
        ]);

        echo 'sim_id = '.$simulation->id.'1st_priority_phone_calls debug - '.$assessment_debug->value.' real - '.$assessment->value;
    }

    public function actionStyleCss()
    {
        $k[17] = 0;
        if(empty($k[20])) {
            echo "lol";
        }
    }

    public function actionStyleForPopupCss()
    {
        $this->layout = false;
        $this->render('style_for_popup_css');
    }

    public function actionStyleGrid()
    {
        $this->layout = false;
        $this->render('style_grid');
    }

    public function actionStyleGridResults()
    {
        $this->layout = false;
        $this->render('style_grid_results');
    }

    public function actionStyleBlocks()
    {
        $this->layout = false;
        $this->render('style_blocks');
    }

    public function actionStyleEmpty1280()
    {
        $this->layout = false;
        $this->render('style_empty_1280');
    }

    public function actionStyleEmpty1024()
    {
        $this->layout = false;
        $this->render('style_empty_1024');
    }

    public function actionXxx()
    {
//        $simulation = Simulation::model()->findByPk(900);
//
//        $ccb = new CheckConsolidatedBudget($simulation->id);
//        $ccb->calcPoints();
//        die;


//        $doc = new MyDocument();
//        $doc->fileName = 'Сводный бюджет_2014_план.xls';
//        $doc->sim_id = 714;
//        $doc->template_id = 20;
//        $doc->save(false);
//        $doc->refresh();
//
//        // var MyDocument $doc
//        $scData = $doc->getSheetList();
//
//        $filePath = tempnam('/tmp', 'excel_');
//
//        ScXlsConverter::sc2xls($scData, $filePath);
//
//        if (file_exists($filePath)) {
//            $xls = file_get_contents($filePath);
//        } else {
//            throw new Exception("Файл не найден");
//        }
//
//        $filename = $doc->sim_id . '_' . $doc->template->fileName;
//        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
//        header('Content-Disposition: attachment; filename="' . $filename . '"');
//        echo $xls;

//        echo '<pre>';

        $scDoc = file_get_contents('http://loc.skiliks.com/6025_svodnyj_byudzhet_2014_plan');
        $scDoc = str_replace('СУММ', 'SUM', $scDoc);

        $excel = ScXlsConverter::sc2xls(json_decode($scDoc, true));

        PHPExcel_Calculation::getInstance()->clearCalculationCache();

        $worksheetNames = Yii::app()->params['analizer']['excel']['consolidatedBudget']['worksheetNames'];

        $whConsolidated = $excel->getSheetByName($worksheetNames['consolidated']);


//
//
//        $xlsFile =  new \PHPExcel();
//        $xlsFile->removeSheetByIndex(0);
//        $xlsFile->addSheet($whConsolidated);
//
//        $n10 = $whConsolidated->getCell('N10')->getCalculatedValue();
//        $n10 = $whConsolidated->getCell('N10')->getValue();


//        $whConsolidated->getCell('N10')->setValue('=SUM(продажи!B6:продажи!D6)');
//        // PHPExcel_Calculation::getInstance()->clearCalculationCache();
//
//        echo '</pre>';

//        $xlsFile =  new \PHPExcel();
//        $xlsFile->removeSheetByIndex(0);
//        $xlsFile->addSheet($whConsolidated);

//        $filePath = tempnam('/tmp', 'excel_');
//        ScXlsConverter::sc2xls(json_decode($scDoc, true), $filePath);
//
//        if (file_exists($filePath)) {
//            $xls = file_get_contents($filePath);
//        } else {
//            throw new Exception("Файл не найден");
//        }
//
//        $filename = 'D1.xlsx';
//        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
//        header('Content-Disposition: attachment; filename="' . $filename . '"');
//        echo $xls;
    }
    // PAYMENT CONTROLLER METHODS

    public function actionDoCashPayment() {

        /** var YumUser $user */
        $user = Yii::app()->user->data();

        if (!Yii::app()->request->getIsAjaxRequest() || !$user->isAuth() || !$user->isCorporate()) {
            echo 'false';
            Yii::app()->end();
        }

        $account = $user->account_corporate;


        $paymentMethod = new CashPaymentMethod();

        $account->inn                 = $paymentMethod->inn     = Yii::app()->request->getParam('inn');
        $account->cpp                 = $paymentMethod->cpp     = Yii::app()->request->getParam('cpp');
        $account->bank_account_number = $paymentMethod->account = Yii::app()->request->getParam('account');
        $account->bic                 = $paymentMethod->bic     = Yii::app()->request->getParam('bic');

        $errors = CActiveForm::validate($paymentMethod);

        if ($errors) {
            echo $errors;
        } elseif (!$account->hasErrors()) {
            $account->save();

            echo sprintf(
                Yii::t('site', 'Thanks for your order, Invoice was sent to %s. Plan will be available upon receipt of payment'),
                $user->profile->email
            );
        }
    }

    public function actionYyy()
    {
        
    }

    public function actionTCPDF()
    {
        /**
         * @var TCPDF $pdf
         */
        $pdf = Yii::createComponent('application.components.tcpdf.tcpdf',
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

        $pdf->Output('test.pdf');
    }

    /**
     * Список доверенных IP
     *
     * @var array
     */
    public static $allowedIps = [
        '62.205.135.161', // Киев - альфа нет
        '93.73.36.120',   //  Киев - воля
        '195.69.87.166'   // Киев - Андрей домашний
    ];

    /**
     * Возвращает конкатенированые в одну строку 3 последних лог-файла nginx
     *
     * Умеет распаковать access.log.2.gz
     *
     * @return string
     */
    private function getLogs()
    {
//        $file_0 = file_get_contents('/var/log/nginx/access.log');
//        $file_1 = file_get_contents('/var/log/nginx/access.log.1');
//
//        $z = gzopen('/var/log/nginx/access.log.2.gz','r') or die("can't open: $php_errormsg");
//        $file_2 = '';
//        while ($line = gzgets($z,1024)) {
//            $file_2 .= $line;
//        }
//
//        $file = $file_0."\n".$file_1."\n".$file_2;

        $file = file_get_contents(__DIR__.'/access.log');
        $file = htmlspecialchars($file);

        return $file;
    }

    public static $hackerIps = [
        '150.70.97.99','150.70.97.115','150.70.173.56','150.70.75.32','150.70.173.57','150.70.97.98','150.70.97.119','150.70.97.114',
        '150.70.75.38','150.70.172.111','150.70.173.46','150.70.173.49','150.70.172.104','150.70.97.124','150.70.173.54','150.70.172.205',
        '150.70.64.211','150.70.64.214','150.70.173.45','150.70.97.120','150.70.97.96','150.70.173.43','150.70.97.97','150.70.173.58',
        '150.70.173.47','150.70.173.51','150.70.173.52','150.70.97.117','150.70.173.48','150.70.97.113','150.70.173.41','150.70.173.50',
        '150.70.97.112','150.70.173.44','150.70.172.200','150.70.173.42','150.70.173.40','150.70.173.53','150.70.97.125','150.70.97.118',
        '150.70.97.121','150.70.97.123','150.70.97.127','150.70.97.87','150.70.97.88','150.70.97.43','150.70.97.89','150.70.97.126',
        '150.70.97.116','150.70.173.55','150.70.173.59','150.70.97.122', '218.37.236.7',
        '58.61.152.123', '217.199.169.106', '202.130.161.195', '80.250.232.92', '75.126.189.226',
        '94.229.74.238', '178.158.214.36', '82.221.102.181', /*'77.47.204.138' Таня? */

    ];

    /**
     * Разбирает строку лога на объект с предсказуемыми значениями
     *
     * @param $line
     * @return stdClass
     */
    private function parseLogLine($line)
    {
        $log = new stdClass();
        $log->isHacker = false;
        $log->isStrange = false;

        $line = str_replace('&quot;', '"', $line);

        $lineArr = explode(' ', $line);

        $ip = $lineArr[0];

        if (false == isset($lineArr[3])) {
            echo $line;
            die;
        }

        $lineArr[3] = str_replace(['['],'',$lineArr[3]);
        $lineArr[4] = str_replace([']'],'',$lineArr[4]);
        $date = $lineArr[3];

        $request = $lineArr[8];

        if (400 == $request) {
            $request = $lineArr[6];
        }

        $response = $lineArr[7].' '.$lineArr[10];

        if ('- "-"' == $response) {
            $response = $lineArr[8];
        }

        // ---

        $log->ip = $ip;
        $log->line = $line;
        $log->request = $request;
        $log->response = $response;
        $log->comment = '';
        $log->isHackAction = false; // это топытка взлома?
        $log->isTrusted = false; // Это логи действий разработчиков

        // --- Combine user agent

        // ---

        // hacks {
        $lineArr = array_merge($lineArr, ['','','','','','','','','','','','','','','','','','','','','']);
        // hacks }

        if ('localhost' == $lineArr[5]) {
            $userAgent = $lineArr[5];

        } elseif ('new.skiliks.com' == $lineArr[5]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];

        } elseif ('www.proxy-alert.com' == $lineArr[5]) {
            $userAgent = $lineArr[8];
            $log->isStrange = true;

        } elseif (('skiliks.com' == $lineArr[5]  && '' == $lineArr[16])
            || ('www.skiliks.com' == $lineArr[5] && '' == $lineArr[16])) {
            $userAgent = '-';

        } elseif ('"Apache-HttpClient/4.2' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15];

        } elseif ('"facebookexternalhit/1.1' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('"msnbot/2.0b' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('"FAST-WebCrawler/3.8"' == $lineArr[13]) {
            $userAgent = $lineArr[13];
            $log->comment = '>> SEO bot?  ';

        } elseif ('/apple-touch-icon.png' == $lineArr[8]
            || '/apple-touch-icon-120x120-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-76x76.png' == $lineArr[8]
            || '/apple-touch-icon-72x72-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-72x72.png' == $lineArr[8]
            || '/apple-touch-icon-76x76-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-114x114-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-152x152-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-144x144-precomposed.png' == $lineArr[8]
            || '/apple-touch-icon-144x144.png' == $lineArr[8]
            || '/apple-touch-icon-152x152.png' == $lineArr[8]
            || '/apple-touch-icon-114x114.png' == $lineArr[8]
            || '/apple-touch-icon-120x120.png' == $lineArr[8]) {

                // What is apple touch icon?
                // @link: http://www.computerhope.com/jargon/a/appletou.htm
                if ('' == $lineArr[16]) {
                    $userAgent = '-';
                } else {
                    $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];
                }

        } elseif ('212.24.63.49' == $lineArr[0]) {
            $userAgent = 'ROBO-CASSA';

        } elseif ('//%63%67%69%2D%62%69%6E' == substr($lineArr[8], 0, 23)) {
            $log->comment = '>> HACKER!  ';
            $log->request = urldecode($lineArr[8]);
            $userAgent = '-';
            $log->isHacker = true;
            $log->isHackAction = true;

        } elseif ('TweetedTimes' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17].' ';

        } elseif ('bingbot/2.0;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

        } elseif ('TweetmemeBot/3.0;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

        } elseif ('openstat.ru/Bot)"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];

        } elseif ('(http://pear.php.net/package/http_request2)' == $lineArr[14]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];
            $log->comment = '>> HACKER?  ';
            $log->isHacker = true;
            $log->isHackAction = true;

        } elseif ('support@digg.com)"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];
            $log->comment = '>> SEO robot?  ';
            $log->isStrange = true;

        } elseif ('"Apache-HttpClient/4.2.2' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];
            $log->comment = '>> HACKER?  ';
            $log->isHacker = true;
            $log->isHackAction = true;

        } elseif ('"EventMachine' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];
            $log->isStrange = true;

        } elseif ('"AddThis.com' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];

        } elseif ('"newsme/1.0;' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];
            $log->comment = '>> SEO robot?  ';
            $log->isStrange = true;

        } elseif ('"ShowyouBot' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('Googlebot/2.1;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

        } elseif ('YandexBot/3.0;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

        } elseif ('Yahoo!' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17];

        } elseif ('Google' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17];

        } elseif ('vkShare;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

        } elseif ('http://js-kit.com/"' == $lineArr[16]) {
            $userAgent = '-';
            $log->comment = '>> HACKER?  ' ;
            $log->isHacker = true;
            $log->isHackAction = true;

        } elseif ('Ezooms/1.0;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];
            $log->comment = '>> ???  ' ;
            $log->isStrange = true;

        } elseif ('BIXOCRAWLER;' == $lineArr[15]) {
            // @link: https://github.com/bixo/bixo !!!
            // Bixo is an open source Java web mining toolkit that runs as a series of Cascading pipes.
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17];
            $log->comment = '>> Content analyzer!  ' ;
            $log->isHaker = true;

        } elseif ('AhrefsBot/5.0;' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];
            $log->comment = '>> ???  ' ;

        }  elseif ('"research' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];
            $log->comment = '>> Content analyzer?  ' ;
            $log->isHaker = true;
            $log->isHackAction = true;

        }  elseif ('"InAGist' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];
            $log->comment = 'SEO robot?';
            $log->isStrange = true;

        } elseif ('+metauri.com"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];
            $log->isStrange = true;

        } elseif ('Feedfetcher-Google;(+http://www.google.com/feedfetcher.html)"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];

        } elseif ('"http://longurl.org"' == $lineArr[12]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];
            $log->comment = '>> HACKER?  ';
            $log->isHacker = true;
            $log->isHackAction = true;

        } elseif ('"Sentry/6.4.0"' == $lineArr[13]) {
            $userAgent = '-';

        } elseif ('"Google-HTTP-Java-Client/1.17.0-rc' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('"Crowsnest/0.5' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];
            $log->comment = '>> ???  ';
            $log->isStrange = true;

        } elseif ('CPython/2.7.2+' == $lineArr[14]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];
            $log->comment = '>> HACKER?  ';
            $log->isHacker = true;
            $log->isHackAction = true;

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[16]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[18]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17]. ' '.$lineArr[18];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[14]) {
            $userAgent = $lineArr[13];

        } else {
            if ('' == $lineArr[20] /*|| false == isset($lineArr[14]) || false == isset($lineArr[15])
                || false == isset($lineArr[16]) || false == isset($lineArr[17]) || false == isset($lineArr[18])
                || false == isset($lineArr[19]) || false == isset($lineArr[20])*/) {
                var_dump($lineArr);
                echo ' >> ' . $line . '<br/>'; die;
                $userAgent = '???';
            } else {
                $userAgent = $lineArr[13].
                    ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17]
                    .' '.$lineArr[18].' '.$lineArr[19].' '.$lineArr[20].' ';
            }
        }

        $log->date = $date;
        $log->userAgent = $userAgent;

        // Trusted {
        if (in_array($log->ip, self::$allowedIps)) {
            $log->isTrusted = true;
        }

        if ('GET 304' == $log->response) {
            $log->isTrusted = true;
        }

        if ('GET 303' == $log->response) {
            $log->isTrusted = true;
        }

        if ('GET 302' == $log->response) {
            $log->isTrusted = true;
        }

        if ('POST 302' == $log->response && '/registration' == $log->request) {
            $log->isTrusted = true;
        }

        if (-1 < strstr($log->line, 'apple-touch-icon')) {
            $log->isTrusted = true;
        }

        if (-1 < strstr($log->line, 'favicon')) {
            $log->isTrusted = true;
        }

        if (-1 < strstr($log->line, '///s7.addthis.com/js/300/addthis_widget.js%23pubid=ra-5158c9c22198d938')) {
            $log->isTrusted = true;
        }

        if ('GET 200' == $log->response) {
            $log->isTrusted = true;
        }

        if ('HEAD 200' == $log->response) {
            $log->isTrusted = true;
        }

        if ('POST 200' == $log->response) {
            $log->isTrusted = true;
        }
        // Trusted }

        if (' 400' == $log->response) {
            $log->isStrange = true;
        }

        if ('OPTIONS 405' == $log->response) {
            $log->isStrange = true;
        }

        if ('POST 499' == $log->response) {
            $log->isStrange = true;
        }

        if ('GET 499' == $log->response) {
            $log->isStrange = true;
        }

        if ('GET 206' == $log->response) {
            $log->isStrange = true;
        }

        if (-1 < strstr($log->line, 'storage.skiliks.com')
            && -1 < strstr($log->line, 'GET / HTTP/1.1 403')) {
            $log->isStrange = true;
        }

        if (in_array($log->ip, self::$hackerIps)) {

            $log->isHacker = true;
        }

        if ($log->isHacker
            && -1 < strstr($log->line, 'admin')) {
            $log->isHackAction = true;
        }

        if ('/cgi-bin/php' == substr($lineArr[8], 0, 12)) {
            $log->comment .= '>> HACKER!  ';
            $log->isHacker = true;
            $log->isHackAction = true;
        }

        return $log;
    }

    /**
     * Прототип функции которая возвращает все логи за последние сутки "как они есть".
     *
     */
    public function actionGetFullLog()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        header("Content-Type:text/plain");
        header("Content-Disposition: attachment; filename=access.log");
        echo $this->getLogs();
    }

    /**
     * Прототип функции которая возвращает все логи от одного IP за последние сутки.
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetIpRequests()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $targetIp = $_GET['ip'];

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>Requests from IP %s :</h1>', $targetIp);

        echo '<pre>';

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if ($targetIp == $lineObj->ip) {
                echo sprintf(
                    '%15s %15s %15s %50s   %90s',
                    $lineObj->date,
                    $lineObj->ip,
                    $lineObj->response,
                    $lineObj->request,
                    $lineObj->userAgent
                ) . '<br/>';
            }
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все логи с текстом $text за последние сутки.
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetText()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $targetText = $_GET['text'];

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>Requests with $TEXT %s :</h1>', $targetText);

        echo '<pre>';

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if (-1 < strpos($lineObj->line, $targetText)) {
                /*echo sprintf(
                        '%15s %15s %15s %50s %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';*/
                echo $line."\n";
            }
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все подозрительные логи
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetHackers()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        $attacks = [];
        $otherFormHacker = [];
        $strange = [];

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if ($lineObj->isHackAction) {
                $attacks[] = sprintf(
                        '%15s %15s %15s %50s %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            } elseif ($lineObj->isHacker && false == $lineObj->isHackAction) {
                $otherFormHacker[] = sprintf(
                        '%15s %15s %15s %50s %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            } elseif ($lineObj->isStrange) {
                $strange[] = sprintf(
                        '%15s %15s %15s %50s %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            }
        }

        echo '<pre>';

        // -------------

        echo sprintf('<h1>Hacker attack requests:</h1>');

        foreach ($attacks as $attack) {
            echo $attack;
        }

        // -------------

        echo sprintf('<h1>Other hacker requests:</h1>');

        foreach ($otherFormHacker as $other) {
            echo $other;
        }

        // -------------

        echo sprintf('<h1>Strange requests:</h1>');

        foreach ($strange as $strangeLog) {
            echo $strangeLog;
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все подозрительные логи
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetOnlyNew()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>New requests:</h1>');

        echo '<pre>';

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if ($lineObj->isHackAction
                || $lineObj->isHacker
                || $lineObj->isTrusted
                || $lineObj->isStrange
            ) {

            } else {
                echo sprintf(
                        ' %15s %15s %15s %50s %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            }
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все
     * логи попыток доступа в URL like '%admin%',
     * кроме доверенных IP
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetAdminCrackers()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>They try to crack adminka!</h1>');

        echo '<pre>';

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if (-1 < strstr($lineObj->line, 'admin')
                && false == in_array($lineObj->ip, self::$allowedIps)) {

                    echo sprintf(
                        '%15s %15s %15s %100s   %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            }
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает, сгруппированные по IP и юзер агенту,
     * логи попыток доступа в URL like '%admin%',
     * кроме доверенных IP
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetAdminCrackersGrouped()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>They try to crack adminka!</h1>');
        echo sprintf('<h4 style="color: grey;">Grouped by IP-userAgent:</h4>');

        echo '<pre>';

        $ips = [];

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if (-1 < strstr($lineObj->line, 'admin')
                && false == in_array($lineObj->ip, self::$allowedIps)) {

                    $ips[$lineObj->ip.' '.$lineObj->userAgent] =  sprintf(
                        '%15s %15s %15s %100s   %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            }
        }

        foreach ($ips as $ip) {
            echo $ip;
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все
     * логи попыток авторизации, кроме доверенных IP
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetAuthCrackers()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>Authorization logs:</h1>');

        echo '<pre>';

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if ((
                    -1 < strstr($lineObj->line, 'user/auth ')
                    || -1 < strstr($lineObj->line, '/admin_area ')
                    || -1 < strstr($lineObj->line, '/admin_area/dashboard ')
                    || -1 < strstr($lineObj->line, '/admin_area/login ')
                ) && false == in_array($lineObj->ip, self::$allowedIps)) {

                    echo sprintf(
                        '%15s %15s %15s %100s   %90s',
                        $lineObj->date,
                        $lineObj->ip,
                        $lineObj->response,
                        $lineObj->request,
                        $lineObj->userAgent
                    ) . '<br/>';
            }
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все ,
     * сгруппированные по IP и юзер агенту и коду ответа нашего сервера,
     * логи попыток авторизации, кроме доверенных IP
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetAuthCrackersGrouped()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>Authorization logs:</h1>');
        echo sprintf('<h4 style="color: grey;">Grouped by IP-userAgent-ResponseCode:</h4>');

        echo '<pre>';

        $ips = [];

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if ((
                    -1 < strstr($lineObj->line, 'user/auth ')
                    || -1 < strstr($lineObj->line, '/admin_area ')
                    || -1 < strstr($lineObj->line, '/admin_area/dashboard ')
                    || -1 < strstr($lineObj->line, '/admin_area/login ')
                ) && false == in_array($lineObj->ip, self::$allowedIps)) {

                    $index = $lineObj->ip.' '.$lineObj->userAgent.' '.$lineObj->response;

                    if (false == isset($ips[$index])) {
                        $ips[$index]['counter'] = 0;
                    }

                    $ips[$index]['log'] = sprintf(
                            '%15s %15s %15s %100s   %90s',
                            $lineObj->date,
                            $lineObj->ip,
                            $lineObj->response,
                            $lineObj->request,
                            $lineObj->userAgent
                        ) . '<br/>';
                    $ips[$index]['counter']++;
            }
        }

        foreach ($ips as $ip) {
            echo sprintf('%3s :: %s', $ip['counter'], $ip['log']);
        }

        echo '</pre>
            <br/> That is all.';
    }

    public function actionLogAnalyzer()
    {
//        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2' != $_POST['dsinvoejgdb']) {
//            Yii::log('Somebody try to use debug controller!', 'warning');
//            Yii::app()->end();
//        }

//        $targetIp = $_POST['ip'];

        $targetIp = '194.44.36.154';

        $file_0 = file_get_contents(__DIR__.'/access.log');
        $file_1 = file_get_contents(__DIR__.'/access.log.1');

        $z = gzopen(__DIR__.'/access.log.2.gz','r') or die("can't open: $php_errormsg");
        $file_2 = '';
        while ($line = gzgets($z,1024)) {
            $file_2 .= $line;
        }

        $file = $file_0."\n".$file_1."\n".$file_2;

        $rows = explode("\n", $file);

        echo '<pre>';

        $ips = [];

        foreach ($rows as $line) {

            $line = trim($line);
            if (empty($line)) {
                continue;
            }

//            if (-1 = strpos($line, ' 404')) {
//                    continue;
//             }
//                $line = str_replace(
//                    [
//                        'www.skiliks.com 144.76.56.104 GET',
//                        '- - ',
//                        'HTTP/1.1 404',
//                        'skiliks.com 144.76.56.104 GET',
//                        'skiliks.com 144.76.56.104'
//                    ],
//                    '',
//                    $line
//                );
//                if (-1 < strpos($line, '19/Nov/2013:17:22:02')) {
//                    continue;
//                }
//                if (-1 < strpos($line, '19/Nov/2013:08:49:00')) {
//                    continue;
//                }
//                if (-1 < strpos($line, 'http://vk.com/dev/Share')) {
//                    continue;
//                }
//                if (-1 < strpos($line, '62.205.135.161')) {
//                    continue;
//                }
//                if (-1 < strpos($line, '195.132.196.206')) {
//                    continue;
//                }
//                if (-1 < strpos($line, '199.217.113.218')) {
//                    continue;
//                }
//                if (-1 < strpos($line, 'apple-touch-icon')) {
//                    continue;
//                }

                // $first = substr($line, 0,3);
                //$first = substr($line, 0, 13);
                //echo $first . '<br/>';



//                    if (-1 < strpos($line, '.ogg')) {
//                        continue;
//                    }

            $lineArr = explode(' ', $line);

//            print_r($lineArr);
//            die;

            $ip = $lineArr[0];

            $lineArr[3] = str_replace(['['],'',$lineArr[3]);
            $lineArr[4] = str_replace([']'],'',$lineArr[4]);
            $date = $lineArr[3];

            // print_r($lineArr);

            $request = $lineArr[8];

            if (400 == $request) {
                $request = $lineArr[6];
            }

            $response = $lineArr[7].' '.$lineArr[10];

            if ('- "-"' == $response) {
                $response = $lineArr[8];
            }

            // standard
            if ('localhost' == $lineArr[5]) {
                $userAgent = $lineArr[5];

            } elseif ('new.skiliks.com' == $lineArr[5]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];

            } elseif ('skiliks.com' == $lineArr[5] && false == isset($lineArr[16])
                || 'www.skiliks.com' == $lineArr[5] && false == isset($lineArr[16])) {
                $userAgent = '-';

            } elseif ('"Apache-HttpClient/4.2' == $lineArr[13]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15];

            } elseif ('"facebookexternalhit/1.1' == $lineArr[13]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14];

            } elseif ('/apple-touch-icon.png' == $lineArr[8]
                || '/apple-touch-icon-120x120-precomposed.png' == $lineArr[8]
                || '/apple-touch-icon-76x76.png' == $lineArr[8]
                || '/apple-touch-icon-72x72-precomposed.png' == $lineArr[8]
                || '/apple-touch-icon-72x72.png' == $lineArr[8]
                || '/apple-touch-icon-76x76-precomposed.png' == $lineArr[8]
                || '/apple-touch-icon-precomposed.png' == $lineArr[8]
                || '/apple-touch-icon-120x120.png' == $lineArr[8]) {
                if (false == isset($lineArr[16])) {
                    $userAgent = '-';
                } else {
                    $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];
                }

            } elseif ('TweetedTimes' == $lineArr[15]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17].' ';

            } elseif ('bingbot/2.0;' == $lineArr[15]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

            } elseif ('openstat.ru/Bot)"' == $lineArr[15]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15];

            } elseif ('"ShowyouBot' == $lineArr[13]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14];

            } elseif ('Googlebot/2.1;' == $lineArr[15]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

            } elseif ('YandexBot/3.0;' == $lineArr[15]) {
                $userAgent = $lineArr[13]. ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16];

            } elseif ('"Sentry/6.4.0"' == $lineArr[13]) {
                $userAgent = '-';

            } else {
                if (false == isset($lineArr[19])) {
                    print_r($lineArr);
                    die;
                }
                $userAgent = $lineArr[13].
                    ' '.$lineArr[14].' '.$lineArr[15].' '.$lineArr[16].' '.$lineArr[17]
                    .' '.$lineArr[18].' '.$lineArr[19].' '.$lineArr[20].' ';
            }

            //die;

            if ($targetIp == $ip) {

                if (400 == $request) {
                    echo '  >>  ' . $line. '<br/>';
                }

                    //echo $ip . '<br/>';
                    // echo implode(' ', $lineArr) . '<br/>';
                    echo sprintf('%15s %15s %15s %50s   %90s', $date, $ip, $response, $request, $userAgent) . '<br/>';
                    //die;
                    if ('-' == $userAgent) {
                        echo '  >>  ' . $line. '<br/>';
                    }
                }

                //$ips[$ip] = $ip;
            }


        //echo implode(', ', $ips);

        echo '</pre> <br/> That is all.';
    }
}

