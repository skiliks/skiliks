<?php

class MailHelper
{
    /**
     * @param array $email
     * @return bool
     */
    public static function addMailToQueue($email)
    {
        $queue = new EmailQueue();
        $queue->sender_email = $email['from'];
        $queue->recipients = $email['to'];
        $queue->copies = '';
        $queue->subject = $email['subject'];
        $queue->body = $email['body'];
        $queue->created_at = (new DateTime('now'))->format("Y-m-d H:i:s");
        $queue->status = EmailQueue::STATUS_PENDING;
        $queue->attachments = json_encode(empty($email['embeddedImages'])?[]:$email['embeddedImages']);
        return $queue->save();
    }

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

    public static function sendNoticeEmail(YumUser $user) {

        if($user->isCorporate() && $user->account_corporate->corporate_email !== null && $user->profile->email !== $user->account_corporate->corporate_email) {
            $personal_email =  $user->profile->email;
            $tmp_emails[$user->id] = $personal_email;

            $corporate_email =  $user->account_corporate->corporate_email;

            $inviteEmailTemplate = Yii::app()->params['emails']['noticeEmail'];

            $body = (new CController("DebugController"))->renderPartial($inviteEmailTemplate, [
                'corporate_email' => $corporate_email,
                'personal_email' => $personal_email,
                'firstname' => $user->profile->firstname
            ], true);

            $mail = array(
                'from' => Yum::module('registration')->registrationEmail,
                'to' => $personal_email,
                'subject' => 'Обновление регистрации skiliks.com',
                'body' => $body,
                'embeddedImages' => [
                [
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top.png',
                    'cid'      => 'mail-top',
                    'name'     => 'mailtop',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-top-2.png',
                    'cid'      => 'mail-top-2',
                    'name'     => 'mailtop2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-1.png',
                    'cid'      => 'mail-right-1',
                    'name'     => 'mailright1',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-2.png',
                    'cid'      => 'mail-right-2',
                    'name'     => 'mailright2',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-right-3.png',
                    'cid'      => 'mail-right-3',
                    'name'     => 'mailright3',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],[
                    'path'     => Yii::app()->basePath.'/assets/img/mail-bottom.png',
                    'cid'      => 'mail-bottom',
                    'name'     => 'mailbottom',
                    'encoding' => 'base64',
                    'type'     => 'image/png',
                ],
            ]
            );
            MailHelper::addMailToQueue($mail);
            $user->profile->email =strtolower($corporate_email);
            $user->profile->update();
        }

    }

    public static function updateInviteEmail(Invite $invite){
        if($invite->ownerUser->profile->email !== $invite->email){
            $invite->email = $invite->ownerUser->profile->email;
            $invite->update();
        }
    }

    public static function createUrlWithHostname($path) {
        return Yii::app()->params['server_name'].ltrim($path, '/');
    }
}