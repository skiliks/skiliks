<?php
/**
 * Сервис для отправки писем.
 * 
 * @package skiliks
 * @subpackage components
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailSender{
    
    /**
     * отправляем письмо
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return array
     */
    public static function send($to, $subject, $message, $fromName, $fromEmail)
    {
        //return mail($to, $subject, $message);
        
        $mimeType = 'text/html';
        $charset = 'UTF-8';
        
        $un        = strtoupper(uniqid(time()));
        $fromName = '=?'.$charset.'?B?'.base64_encode($fromName).'?=';
        $head      = "From: $fromName <$fromEmail>\r\n";
        $head     .= "To: $to\n";
        //$head     .= "Subject: $subject\n";
        $head     .= "X-Mailer: PHPMail Tool\n";
        $head     .= "Reply-To: $fromName <$fromEmail>\r\n";
        $head     .= "Mime-Version: 1.0\n";
        $head     .= "Content-Type:$mimeType; charset=".$charset. ";";
        $head     .= "boundary=\"----------".$un."\"\n\n";
        
        return @mail($to, $subject, $message, $head);
    }
}