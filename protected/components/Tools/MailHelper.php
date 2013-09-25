<?php

class MailHelper {
    public static function addMailToQueue($email) {
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
    public static function sendMailFromQueue() {
        $mails = EmailQueue::model()->findAll("status = :status order by id desc limit 10", ['status' => EmailQueue::STATUS_PENDING]);
        /* @var $mail EmailQueue */
        $result = ['done'=>0, 'fail'=>0];
        foreach($mails as $mail) {
            $mail->status = EmailQueue::STATUS_IN_PROGRESS;
            $mail->update();
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
} 