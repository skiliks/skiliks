<?php

class SendEmailAboutNewReleaseFeaturesCommand extends CConsoleCommand
{
    public function actionIndex($email)
    {
        $emails = [];
        $mode = $email;

        if($mode === 'all' || $mode === 'test') {
            /* @var $profile YumProfile */
            foreach(YumProfile::model()->findAll() as $profile) {
                $emails[$profile->email] = $profile->firstname;
            }
        }elseif($mode === 'all_corporate') {
            /* @var $account UserAccountCorporate */
            foreach(UserAccountCorporate::model()->findAll() as $account) {
                $emails[$account->user->profile->email]=$account->user->profile->firstname;
            }
        }elseif($mode === 'all_personal'){
            /* @var $account UserAccountPersonal */
            foreach(UserAccountPersonal::model()->findAll() as $account) {
                $emails[$account->user->profile->email]=$account->user->profile->firstname;
            }
        }else{
            $emails_temp = explode(',', $email);
            foreach($emails_temp as $email){
                $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
                $emails[$profile->email] = $profile->firstname;
            }
        }
        $count_send = 0;
        $not_send = [];
        $not_valid = [];
        foreach($emails as $email => $name) {

            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $not_valid[] = $email;
                continue;
            }

            if ($mode === 'test') {
                if (in_array($email, ['tony@skiliks.com', 'leah.levin@skiliks.com', 'masha@skiliks.com'])) {
                    continue;
                }
            }

            $mailOptions = new SiteEmailOptions();
            $mailOptions->from = Yum::module('registration')->registrationEmail;
            $mailOptions->to = $email;
            $mailOptions->subject = 'Новые возможности skiliks';

            $mailOptions->h1      = sprintf('Приветствуем, %s!', $name);
            $mailOptions->text1   = '
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    Рады сообщить о выходе новой версии skiliks!<br/><br/>
                    Сначала о главном:
                    теперь skiliks можно использовать в браузерах InternetExplorer 10 и 11!
                    Спасибо поклонникам Microsoft за терпение :-).<br/><br/>
                    В рабочем кабинете корпоративного пользователя добавлены:<br/>
                </p>
                <ul style="color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    <li> Инструкция по интерпретации результатов оценки, где пошагово изложен
                        алгоритм анализа полученных результатов
                        (<a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
                            href="' . MailHelper::createUrlWithHostname("public/site/Skiliks_results.pdf") .'">скачать</a>)</li>
                    <li> Сводный аналитический отчёт по всем прошедшим тестирование,
                        выгруженный в Excel</li>
                </ul>
            ';
            $mailOptions->text2   = '
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    В результатах оценки добавлены:
                </p>
                <ul style="color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    <li> Детальное описание каждого показателя</li>
                    <li> Полный отчёт с результатами оценки и описанием всех показателей в формате pdf для печати</li>
                </ul>
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    Кроме того, мы повысили стабильность процесса тестирования, точность результатов,
                    улучшили работу настроек безопасности, сделали кабинет и симуляцию немного удобнее.<br/><br/>
                    Используйте skiliks с удовольствием и делитесь своими впечатлениями!<br/><br/>
                    Пожалуйста, <a style="text-decoration:none;color:#147b99;font-family:Tahoma, Geneva, sans-serif;font-size:14px;"
                    href="' . MailHelper::createUrlWithHostname("user/auth") . '">зайдите</a> в ваш кабинет для запуска новой версии и отправки приглашений кандидатам.<br/><br/>
                    Спасибо, что вы с нами!
                 </p>
            ';

            $sent = UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_ANJELA);

//            $mail->body = $body;
//            $mail->embeddedImages = [
//                    [
//                        'path'     => Yii::app()->basePath.'/assets/img/mailtopangela.png',
//                        'cid'      => 'mail-top-angela',
//                        'name'     => 'mailtopangela',
//                        'encoding' => 'base64',
//                        'type'     => 'image/png',
//                    ],[
//                        'path'     => Yii::app()->basePath.'/assets/img/mailanglabtm.png',
//                        'cid'      => 'mail-bottom-angela',
//                        'name'     => 'mailbottomangela',
//                        'encoding' => 'base64',
//                        'type'     => 'image/png',
//                    ],[
//                        'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
//                        'cid'      => 'mail-bottom',
//                        'name'     => 'mailbottom',
//                        'encoding' => 'base64',
//                        'type'     => 'image/png',
//                    ],
//                ];

//            $sent = MailHelper::addMailToQueue($mail);
            if($sent) {
                $count_send++;
            }else{
                $not_send[] = $email;
            }
        }

        echo "Отправлено ".$count_send."\n";
        if(!empty($not_send)){
            echo "Не отправлено ".count($not_send).' - '.implode(','. $not_send)."\n";
        }
        if(!empty($not_valid)) {
            echo "Не валидные ".count($not_valid).' - '.implode(',', $not_valid)."\n";
        }

    }

}