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
    
    /**
     * @param mixed array $params
     * @return bool
     */
    public static function notifyUser($params) 
    {
        $url = Yii::app()->createAbsoluteUrl('registration/activate', array('code' => $params['code']));
        
        $message = "Поздравляем {$params['email']}, вы успешно зарегистрированы и ваш пароль {$params['password']}. 
        Для активации перейдите по <a href='{$url}'>ссылке</a>";
        
        return self::send(
            $params['email'], 
            'Регистрация завершена', 
            $message, 
            'Skiliks - game the skills!', 
            'info@skiliks.com'
        );
    }
    
    /**
     * @param Users $user
     * @param string $password, not encypted password
     */
    public static function notifyUserAboutPassword($user, $password)
    {
        MailSender::send(
            $email, 
            'Skiliks : восстановление пароля', 
            "{$user->email}, ваш новый пароль {$password}", 
            'skiliks', 
            'info@skiliks.com'
        );
    }
}