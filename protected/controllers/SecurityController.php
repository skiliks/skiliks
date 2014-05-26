<?php
/**
 * Контроллер для анализа логов nginx
 */
class SecurityController extends SiteBaseController {

    /**
     * Список доверенных IP
     *
     * @var array
     */
    public static $allowedIps = [
        '62.205.135.161', // Киев - альфа нет
        '93.73.36.120',   //  Киев - воля
        '195.69.87.166',   // Киев - Андрей домашний
        '62.205.135.161',   // Teamcity
        '188.32.209.89', // Таня
        '77.47.204.138', // Таня
        '86.62.110.225', // Лея
        '218.32.56.5', // www.freesitestatus.com/monitored-by-user-slavka
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
        $file = '';

        // чтоб с сервака забирать

//        $file_0 = file_get_contents(__DIR__.'/../access-logs/access.log');
//        $file_1 = file_get_contents(__DIR__.'/../access-logs/access.log.1');
//
//        $file_2 = '';
//
//        for ($i = 2; $i < 10; $i++) { // 37
//            $z = gzopen(__DIR__.'/../access-logs/access.log.'.$i.'.gz','r') or die("can't open: $php_errormsg");
//            while ($line = gzgets($z,1024)) {
//                $file_2 .= $line;
//            }
//
//            $file_2 .= $i."\n";
//        }
//
//        $file = $file_0."\n".$file_1."\n".$file_2;

        // --------------------------

        $file = file_get_contents(__DIR__.'/../access-logs/access.log');

        // $file = htmlspecialchars($file);

        return $file;
    }

    public static $hackerIps = [
        /*
         * seo-bot
         * подбор пароля пользователя
         * попытка взлома админки
         * попытка полученяи доступа к серверу, БД, ФС, вополнения сценария на сервере
         * DDOS
         */

        /*
         * Обращается напрямую к IP
         */
        '194.226.108.23' /* 700+ */ ,
        '194.226.108.22' /* 200+ */ ,
        '194.226.108.22' /* 200+ */,
        '46.39.37.145' /* 100+ */,

        '144.76.56.104' /* это IP продакшена */,

        '150.70.97.99','150.70.97.115','150.70.173.56','150.70.75.32','150.70.173.57', '150.70.97.98',
        '150.70.75.38','150.70.172.111','150.70.173.46','150.70.173.49','150.70.172.104', '150.70.97.124',
        '150.70.64.211','150.70.64.214','150.70.173.45','150.70.97.120','150.70.97.96', '150.70.173.43',
        '150.70.97.97','150.70.173.58', '150.70.173.41','150.70.173.50', '150.70.173.54', '150.70.172.205',
        '150.70.173.47','150.70.173.51','150.70.173.52','150.70.97.117','150.70.173.48', '150.70.97.113',
        '150.70.97.112','150.70.173.44','150.70.172.200','150.70.173.42','150.70.173.40', '150.70.173.53',
        '150.70.97.125','150.70.97.118', '150.70.97.89','150.70.97.126', '150.70.97.119', '150.70.97.114',
        '150.70.97.121','150.70.97.123','150.70.97.127','150.70.97.87','150.70.97.88', '150.70.97.43',
        '150.70.97.116','150.70.173.55','150.70.173.59','150.70.97.122', '218.37.236.7', '157.55.34.32',
        '58.61.152.123', '217.199.169.106', '202.130.161.195', '80.250.232.92', '75.126.189.226', '199.30.26.221',
        '94.229.74.238', '178.158.214.36', '82.221.102.181', '66.249.73.34' /* 3 */, '80.86.84.72' /* 9 */, '188.143.234.6',
        '171.101.226.159' /* 3 */, '103.31.186.84', '85.114.135.126', '95.211.192.202', '37.9.53.51', '1.234.83.11',
        '192.241.133.80', '97.79.239.37', '91.93.20.67', '87.240.182.162', '212.252.216.10', '188.138.115.94',
        '125.27.11.39', '213.183.59.3', '77.73.105.179', '46.119.112.117', '178.210.65.38', '37.57.133.189',
        '78.46.42.239','188.64.170.222','188.143.232.103','217.20.184.45', '82.193.120.229',
        '94.23.67.170','88.190.220.13' /* 15 */,'192.151.144.234','94.102.56.237','197.242.150.130','157.55.33.182',
        '94.102.53.219' /* 4 */,'192.99.11.13','63.141.227.74' /* 6 */,'152.104.210.52','62.75.181.134' /*2*/,'103.7.52.7',
        '211.155.95.122' /* 10 */,'77.120.96.66' /* 4 */,'152.104.210.52'/* 5 */ ,'95.7.38.93' /* 4 */,'162.243.72.5' /* 4 */,'203.128.84.186',
        '5.61.39.55' /* 2 */,'95.211.223.32' /* 2 */,'94.102.49.37' /* 3 */,'80.86.84.72','176.31.13.28','',
        '91.121.98.48' /* 6 */,'91.210.189.145' /* 3 */,'133.242.12.230'/* 5 */,'46.164.129.180' /* 5 */,'66.249.73.235','85.214.104.62',
        '72.167.113.216' /* 5 */,'118.175.36.34' /* 3 */,'27.97.100.234' /*13*/,'66.249.66.222'/*1*/,'195.3.146.93'/*2*/,'24.133.78.224'/*5*/,'94.102.51.155'/*10*/,
        '46.163.71.99'/*1*/,'178.77.71.185'/*4*/,'77.222.56.204'/*1*/,'122.164.52.86'/*2*/,'195.3.146.93'/*2*/,'194.28.71.96'/*1*/,'109.120.163.60'/*1*/,
        '216.55.166.67'/*8*/,'','','','','','',
        '','','','','','','',
        // '93.75.179.229', // он знает /admin_area
        /*'77.47.204.138' Таня? */
        /*'188.32.209.89' Антон? Таня! :) */

    ];

    // sitemap.xml
    // Mozila

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



        if (in_array($line, ['2', '3', '4','5','6','7','8','9','10',11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,34,33,35,36])) {
            return null;
        }

        if (false == isset($lineArr[3])) {
            echo 'Not log string! => '.$line;
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

        } elseif ('aiHitBot/2.7;' == $lineArr[15]) {
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

        } elseif ('"Mozilla/6.0' == $lineArr[13] && '"-"' == $lineArr[16]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15];

        } elseif (-1 < strstr($lineArr[13], 'Evernote') == $lineArr[13] && '"-"' == $lineArr[16]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15];

        } elseif ('"Yandex.Disk' == $lineArr[13] && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('/emails/new-year-2014/kakprivestidelavporyadok.mp3.zip' == $lineArr[8]) {
            $userAgent = $lineArr[12]. ' '.$lineArr[13];
            $log->isTrusted = true;

        }  elseif ('"(DreamPassport/3.0;' == $lineArr[13] && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

            // !!!

        } elseif (-1 < strstr($lineArr[13], 'Slurp') && '' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16];

        } elseif (-1 < strstr($lineArr[13], 'NING') && '' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16];

        } elseif (-1 < strstr($lineArr[13], 'Yahoo') && '' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16];

        } elseif (-1 < strstr($lineArr[13], 'Mozilla') && isset($lineArr[42]) && '' == $lineArr[42]) {
            $userAgent = '';
            for ($c = 13; $c < 42; $c++) {
                $userAgent .= $lineArr[$c];
            }

        }  elseif (-1 < strstr($lineArr[13], 'Googlebot') && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif (-1 < strstr($lineArr[13], 'Mozilla') && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif (-1 < strstr($lineArr[13], 'Mozilla') && '' == $lineArr[19]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17]. ' '.$lineArr[18];

        } elseif (-1 < strstr($lineArr[13], 'Mozila') && ('"-"' == $lineArr[16] || '0.000--' == $lineArr[17] )) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[16];

        } elseif (-1 < strstr($lineArr[13], 'FeedlyApp') && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif (-1 < strstr($lineArr[13], 'Lynx') && '"-"' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16];

        } elseif (-1 < strstr($lineArr[13], 'Mozilla') && '"-"' == $lineArr[15]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17];

        } elseif ('"-"' == $lineArr[12] && '"unknown"' == $lineArr[17]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[18]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17]. ' '.$lineArr[18];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[14]) {
            $userAgent = $lineArr[13];

        } elseif ('"-"' == $lineArr[12] && '"-"' == $lineArr[34]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17]
                . ' '.$lineArr[18]. ' '.$lineArr[19]. ' '.$lineArr[20]. ' '.$lineArr[21]
                . ' '.$lineArr[22]. ' '.$lineArr[23]. ' '.$lineArr[24]. ' '.$lineArr[25]
                . ' '.$lineArr[26]. ' '.$lineArr[27]. ' '.$lineArr[28]. ' '.$lineArr[29]
                . ' '.$lineArr[30]. ' '.$lineArr[31]. ' '.$lineArr[32]. ' '.$lineArr[33];

        } elseif ('"Mozilla/5.0' == $lineArr[13] && '"-"' == $lineArr[18]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15]. ' '.$lineArr[16]. ' '.$lineArr[17]. ' '.$lineArr[18];

        } elseif ('href=\x22http://www.alexaboostup.com\x22&gt;Alexa' == $lineArr[14]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14]. ' '.$lineArr[15];

        } elseif ('"Wget/1.13.4' == $lineArr[13]) {
            $userAgent = $lineArr[13]. ' '.$lineArr[14];
            $log->isHacker = true;

        } else {
            if ('' == $lineArr[20] /*|| false == isset($lineArr[14]) || false == isset($lineArr[15])
                || false == isset($lineArr[16]) || false == isset($lineArr[17]) || false == isset($lineArr[18])
                || false == isset($lineArr[19]) || false == isset($lineArr[20])*/) {
                var_dump($lineArr);
                //echo ' >> ' . $line . '<br/>';
                die;
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

        if (in_array($log->response, ['GET 304', 'GET 303', 'GET 302'])) {
            $log->isTrusted = true;
        }


        if ('POST 302' == $log->response && '/dashboard' == $log->request) {
            $log->isTrusted = true;
        }

        if ('POST 302' == $log->response && '/registration/' == $log->request) {
            $log->isTrusted = true;
        }

        if ('POST 302' == $log->response && '/registration' == $log->request) {
            $log->isTrusted = true;
        }

        if ('POST 302' == $log->response && -1 < strstr($log->request, '/registration/by-link/')
            && 67 == strlen($log->request)) {
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

        if (-1 < strstr($log->line, 'storage2.skiliks.com')
            && -1 < strstr($log->line, 'GET / HTTP/1.1 403')) {
            $log->isStrange = true;
        }

        if (-1 < strstr($log->line, 'addthis.com')) {
            $log->isTrusted = true;
        }

        if ($this->isHackerRequest($log)) {
            $log->isHackAction = true;
            $log->isHacker = true;
        }
        if (in_array($log->ip, self::$hackerIps)) {
            $log->isHacker = true;
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

            if (null == $lineObj) {
                continue;
            }

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

            if (null == $lineObj) {
                continue;
            }

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

            if ($this->isHackAdminkaRequest($lineObj)) {

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
        $n = [];

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if (null == $lineObj) {
                continue;
            }

            if ($this->isHackAdminkaRequest($lineObj, $lineObj->ip)) {
                $index = $lineObj->ip.' '.$lineObj->userAgent;

                if (false == isset($n[$index])) {
                    $n[$index] = 1;
                } else {
                    $n[$index]++;
                }

                $ips[$index] =  sprintf(
                        '%3s :: %15s %15s %15s %100s   %90s',
                        $n[$index],
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
     * @param StdClass $line
     *
     * @return bool
     */
    public function isHackAdminkaRequest($line) {
        if ((-1 < strstr($line->request, '/admin_area ')
                || -1 < strstr($line->request, 'admin')
                || -1 < strstr($line->request, 'admin.php')
                || -1 < strstr($line->request, 'wp-login.php')
                || -1 < strstr($line->request, '/admin_area/dashboard ')
                || -1 < strstr($line->request, '/admin_area/login ')
            ) && false == in_array($line->ip, self::$allowedIps)) {
            return true;
        }

        return false;
    }

    /**
     * @param StdClass $line
     *
     * @return bool
     */
    public function isHackerRequest($line) {
        if (
                (-1 < strstr($line->request, 'user/auth ')
                || -1 < strstr($line->request, 'setup')
                || -1 < strstr($line->request, '/phpmyadmin')
                || -1 < strstr($line->request, '/webmail/')
                || -1 < strstr($line->request, '//mail/')
                || -1 < strstr($line->request, '//mail.php/')
                || -1 < strstr($line->request, '/wordpress')
                || -1 < strstr($line->request, '/joomla')
                || -1 < strstr($line->request, '/joomla')
                || -1 < strstr($line->request, '/drupal')
                || -1 < strstr($line->request, '/blog')
                || -1 < strstr($line->request, '/install.php')
                || -1 < strstr($line->request, 'LICENSE_AFL.txt')
                || -1 < strstr($line->request, '.html')
                || -1 < strstr($line->request, '/catmin/')
                || -1 < strstr($line->request, 'wwwroot')
                || -1 < strstr($line->request, '\x01')
                || -1 < strstr($line->request, '\x02')
                || -1 < strstr($line->request, '\x03')
                || -1 < strstr($line->request, '.rar')
                || -1 < strstr($line->request, '.zip')
                || '/downloader' == $line->request
                || '/mage' == $line->request
                || '/wp' == $line->request
                || -1 < strstr($line->request, '/news.php')
                || -1 < strstr($line->request, '/do.php')
                || -1 < strstr($line->request, '/pma')
                || -1 < strstr($line->request, '/forum')
                || -1 < strstr($line->request, 'cgi-bin')
            )
            && false == in_array($line->ip, self::$allowedIps)) {
            return true;
        }

        if ($this->isHackAdminkaRequest($line)) {
            return true;
        }

        return false;
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

            if ($lineObj->isHacker) {
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

            if ($lineObj->isHacker) {

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

    /**
     * Прототип функции которая возвращает все ,
     * сгруппированные по IP и юзер агенту логи
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetGroupedRequests()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>Grouped by request logs:</h1>');
        echo sprintf('<h4 style="color: grey;">Grouped by IP-userAgent-ResponseCode:</h4>');

        echo '<pre>';

        $ips = [];

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if (null === $lineObj) {
                continue;
            }

            if (in_array($lineObj->ip, self::$allowedIps)) {
                continue;
            }

            if ('/index.php/events/getState' == $lineObj->request) {
                continue;
            }

            $index = $lineObj->ip.' '.$lineObj->userAgent.' '.$lineObj->request;

            if (false == isset($ips[$index])) {
                $ips[$index]['counter'] = 0;
            }

            $ips[$index]['obj'] = $lineObj;

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

        foreach ($ips as $ip) {
            if (100 < $ip['counter'] && $ip['obj']->request != '144.76.56.104') {
                echo sprintf('%3s :: %s', $ip['counter'], $ip['log']);
            }
        }

        echo '</pre><hr/>';
        echo '<h1> Запросы напрямую к 144.76.56.104</h1><pre>';

        foreach ($ips as $ip) {
            if ($ip['obj']->request == '144.76.56.104') {
                echo sprintf('%3s :: %s', $ip['counter'], $ip['log']);
            }
        }

        echo '</pre>
            <br/> That is all.';
    }

    /**
     * Прототип функции которая возвращает все ,
     * сгруппированные по IP и юзер агенту и коду ответа нашего сервера,
     * логи с 404, 429, 500 ошибкой
     *
     * Отображает:
     * дату запроса - IP - результат запроса - URL запроса - user agent (если есть)
     *
     */
    public function actionGetHttpErrorLogs()
    {
        if ('jdndsuiqw12009c3mv-NCALA023-4NLDL2-nCDp--23LKLCK-23=2-r=-2lasSDFVdn923cVESskd3865SVedfvAFD' != $_GET['dsinvoejgdb']) {
            Yii::log('Somebody try to use debug controller!', 'warning');
            Yii::app()->end();
        }

        $file = $this->getLogs();

        $rows = explode("\n", $file);

        echo sprintf('<h1>404, 405, 429, 400 Error logs:</h1>');
        echo '<p>400 400 Bad Request. The request cannot be fulfilled due to bad syntax.</p>';
        echo '<p>405 Method Not Allowed</p>';
        echo '<p>429 Too Many Requests</p>';

        echo '<pre>';

        foreach ($rows as $line) {

            $line = trim($line);
            if (true == empty($line)) {
                continue;
            }

            $lineObj = $this->parseLogLine($line);

            if ((
                    -1 < strstr($lineObj->response, '400')
                    || -1 < strstr($lineObj->response, '404')
                    || -1 < strstr($lineObj->response, '429')
                    || -1 < strstr($lineObj->response, '500')
                ) && false == in_array($lineObj->ip, self::$allowedIps)
                && false == $lineObj->isTrusted
            ) {

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

    public function actionLogAnalyzer()
    {
        $targetIp = '194.44.36.154';

        $file_0 = file_get_contents(__DIR__.'/access.log');
        $file_1 = file_get_contents(__DIR__.'/access.log.1');

        $z = gzopen (__DIR__.'/access.log.2.gz','r');
        if (false == $z) {
            throw new Exception("can't open: $php_errormsg");
        }

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

            $lineArr = explode(' ', $line);

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