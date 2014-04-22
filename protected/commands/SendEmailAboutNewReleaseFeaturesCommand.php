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

            $mailOptions->h1      = sprintf('%s,</br>', $name);
            $mailOptions->text1   = '
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    Благодарим вас за интерес к нашему продукту!
                </p>
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    Мы добавили новые возможности, которыми вы можете воспользоваться прямо сейчас. И это БЕСПЛАТНО для всех ранее пройденных полных версий игры.
                </p>
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    1. Детальная обратная связь по всем оцениваемым навыкам. Теперь оценка стала еще подробнее и нагляднее!
                </p>
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    2. Индивидуальный план развития управленческих навыков. Теперь вы точно знаете, какие именно навыки сотрудника нужно развивать.
                </p>
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    Для получения пунктов 1 и 2 достаточно кликнуть на "Полный отчет" в
                    <a target="_blank" href="https://dl.dropboxusercontent.com/u/20682175/Skiliks_full_report.png">инфографике</a>
                    или посмотреть примеры
                    <a target="_blank" href="https://dl.dropboxusercontent.com/u/20682175/Full_report.pdf">здесь</a>
                    и
                    <a target="_blank" href="https://dl.dropboxusercontent.com/u/20682175/Development_plan.pdf">
                    здесь
                    </a>.
                </p>
                <p style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                    3. Компания, где 20 и более сотрудников прошли симуляцию, сможет получить Отчет по диагностике управленческого потенциала во всей компании, отделу, команде.
                    Этот отчет дает системное понимание зон развития навыков управленческой команды.
                    Более подробно с информацией можно ознакомиться <a target="_blank" href="http://skiliks.com/static/product-diagnostic#.U05yK-aSz1g">здесь</a>
                </p>
            ';

            $sent = UserService::addLongEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_JELEZNIJ);

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