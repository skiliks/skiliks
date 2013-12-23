<?php

/**
 * Class MailHelper
 */
class MailHelper
{
    /**
     * Добавление письма в очередь
     * @param SiteEmailOptions $email
     * @return bool
     */
    public static function addMailToQueue(SiteEmailOptions $email)
    {
        $queue = new EmailQueue();
        $queue->sender_email = $email->from;
        $queue->recipients = $email->to;
        $queue->copies = '';
        $queue->subject = $email->subject;
        $queue->body = $email->body;
        $queue->created_at = (new DateTime('now'))->format("Y-m-d H:i:s");
        $queue->status = EmailQueue::STATUS_PENDING;
        $queue->attachments = json_encode(empty($email->embeddedImages)?[]:$email->embeddedImages);
        return $queue->save();
    }

    /**
     * Добавление писем в очередь
     * @param SiteEmailOptions[] $emails
     * @return bool
     */
    public static function addMailsToQueue(array $emails)
    {
        $return = [];
        foreach($emails as $email) {
            $return[] = self::addMailToQueue($email);
        }
        return $return;
    }

    /**
     * Отправка писем с очереди
     * @return array
     */
    public static function sendMailFromQueue()
    {
        $mails = EmailQueue::model()->findAll("status = :status order by id desc limit 10", ['status' => EmailQueue::STATUS_PENDING]);

        /* @var $mail EmailQueue */
        $result = ['done'=>0, 'fail'=>0];

        foreach($mails as $mail) {
            $mail->status = EmailQueue::STATUS_IN_PROGRESS;
            $mail->update();
        }

        foreach($mails as $mail) {
            try{
                $sent = YumMailer::send([
                    'from'=>$mail->sender_email,
                    'to'=>$mail->recipients,
                    'subject'=>$mail->subject,
                    'body'=>$mail->body,
                    'embeddedImages'=>json_decode($mail->attachments, true)
                ]);
            }catch (Exception $e){
                $sent = false;
                $mail->errors = "\r\n".$e->getMessage()."\r\n".$e->getTraceAsString();
            }
            if($sent){
                $result['done']++;
                $mail->status = EmailQueue::STATUS_SENDED;
                $mail->sended_at = (new DateTime('now'))->format("Y-m-d H:i:s");
                $mail->update();
            } else {
                $result['fail']++;
                $mail->status = EmailQueue::STATUS_PENDING;
                $mail->update();
            }
        }
        return $result;
    }

    /**
     * Обновление email'а инвайта
     * @param Invite $invite
     */
    public static function updateInviteEmail(Invite $invite){
        if($invite->ownerUser->profile->email !== $invite->email){
            $invite->email = $invite->ownerUser->profile->email;
            $invite->update();
        }
    }

    /**
     * Создание ссылки с именем хоста
     * @param string $path url
     * @return string ссылка
     */
    public static function createUrlWithHostname($path) {
        return Yii::app()->params['server_name'].ltrim($path, '/');
    }

    /**
     * Отпрвка письма о подозрительной активности
     * @param Invite $invite
     */
    public static function sendEmailAboutActivityToStudySimulation(Invite $invite) {

        if($invite->owner_id === $invite->receiver_id && null !== $invite->receiverUser && false == $invite->receiverUser->can(UserService::CAN_START_SIMULATION_IN_DEV_MODE && $invite->scenario->slug === Scenario::TYPE_FULL)) {
            $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
            $count = (int)Invite::model()->count("receiver_id = :user_id and owner_id = :user_id and scenario_id = :scenario_id and (status = :in_progress or status = :completed)", [
                'user_id'=>$invite->owner_id,
                'in_progress' => Invite::STATUS_IN_PROGRESS,
                'completed' => Invite::STATUS_COMPLETED,
                'scenario_id'=>$scenario->id
            ]);
            if($count >= 2) {
                $mailOptions = new SiteEmailOptions();
                $mailOptions->from = Yum::module('registration')->registrationEmail;
                $mailOptions->to = 'support@skiliks.com';
                $mailOptions->subject = 'Внимание! Подозрительная активность от аккаунта '.$invite->ownerUser->profile->email
                    .' на '.Yii::app()->params['server_domain_name'];
                $mailOptions->h1      = '';
                $mailOptions->text1   = '
                    <p  style="margin:0 0 15px 0;color:#555545;font-family:Tahoma, Geneva, sans-serif;font-size:14px;text-align:justify;line-height:20px;">
                        Есть подозрение, что аккаунт <a
                        href="' . MailHelper::createUrlWithHostname("admin_area/user/".$invite->receiver_id."/details") .'"></a>
                        '. $invite->email .', пытается изучить фул симуляцию.
                    </p>
                ';

                UserService::addStandardEmailToQueue($mailOptions, SiteEmailOptions::TEMPLATE_FIKUS);
            }
        }
    }
} 
