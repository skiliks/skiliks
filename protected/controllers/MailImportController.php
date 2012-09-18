<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MailImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailImportController extends AjaxController{
    
    public function actionImport() {
        $fileName = 'media/xls/mail.csv';
        
        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }
        //var_dump($characters); die();
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) {
                continue;
            }
            
            if ($index > 88) {
                echo('all done'); die();
            }
            
            //var_dump($row);
            $code = $row[0];
            $sendingDate = $row[1];
            $sendingTime = $row[2];
            $fromCode = $row[3];
            $toCode = $row[5];
            $copies = $row[7];
            $subject = iconv("Windows-1251", "UTF-8", $row[9]);
            $message = iconv("Windows-1251", "UTF-8", $row[10]);
            $attachment = $row[11];
            
            $group = null;
            // Анализ кода, определение группы
            if (preg_match("/MY\d+/", $code)) {
                $group = 1;
            }
            
            if (preg_match("/M\d+/", $code)) {
                //$group = 1;
            }
            
            if (preg_match("/MSY\d+/", $code)) {
                $group = 3;
            }
            
            if (!isset($characters[$fromCode])) {
                Logger::debug("cant find character from by code $fromCode");
                echo("cant find character by code $fromCode");
                die();
            }
            $fromId = $characters[$fromCode];
            
            // tmp
            $copiesArr = array();
            if (strstr($copies, ',')) {
                $copiesArr = explode(',', $copies);
            }
            
            if (strstr($toCode, ',')) {
                $toCode = explode(',', $toCode);
            }
            $receivers = array();
            if (is_array($toCode)) {
                $receivers = $toCode;
                $toCode = $toCode[0];
            }
            
            if (!isset($characters[$toCode])) {
                Logger::debug("cant find character to by code $toCode");
                echo("cant find character to by code $toCode");
                die();
            }
            $toId = $characters[$toCode];
            
            $date = explode('.', $sendingDate);
            $time = explode(':', $sendingTime);
            if (!isset($time[1])) {
                $time[0] = 0;
                $time[1] = 0;
            }
            if (!isset($date[1])) {
                echo("code $code date : ");
                var_dump($sendingDate); die();
            }
            
            if (!isset($time[1])) {
                echo("code $code time : ");
                var_dump($sendingTime); die();
            }
            
            $sendingDate = null;
            if (isset($date[1])) {
                $sendingDate = mktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
            }
            
            
            
            
            $model = MailTemplateModel::model()->byCode($code)->find();
            if (!$model) {
                $model = new MailTemplateModel();
                $model->group_id = $group;
                $model->sender_id = $fromId;
                $model->receiver_id = $toId;
                $model->subject = $subject;
                $model->message = $message;
                $model->sending_date = $sendingDate;
                $model->code = $code;
                

                $model->insert();
            }
            else {
                $model->group_id = $group;
                $model->sender_id = $fromId;
                $model->receiver_id = $toId;
                $model->subject = $subject;
                $model->message = $message;
                $model->sending_date = $sendingDate;
                $model->update();
            }
            
            
            foreach($receivers as $index => $receiverCode) {
                if (!isset($characters[$receiverCode])) {
                    echo("cant find receiver by code $receiverCode"); die();
                }
                $receiverId = $characters[$receiverCode];
                echo("r = $receiverId  id: {$model->id} <br/>");
                
                
                $dmo = MailReceiversTemplateModel::model()->byMailId($model->id)->byReceiverId($receiverId)->find();
                if (!$dmo) {
                    $dmo = new MailReceiversTemplateModel();
                    $dmo->mail_id = $model->id;
                    $dmo->receiver_id = $receiverId;
                    $dmo->insert();
                }
            }
            
            
            // а теперь учтем копии
            foreach($copiesArr as $index => $characterCode) {
                if (!isset($characters[$characterCode])) {
                    echo("cant find chracter by code $characterCode"); die();
                }
                $characterId = $characters[$characterCode];
                
                $dmo = MailCopiesTemplateModel::model()->byMailId($model->id)->byReceiverId($characterId)->find();
                if (!$dmo) {
                    $dmo = new MailCopiesTemplateModel();
                    $dmo->mail_id = $model->id;
                    $dmo->receiver_id = $characterId;
                    $dmo->insert();
                }
            }
        }
        fclose($handle);
        echo("Done");
    }
    
    public function actionImportTasks() {
        $fileName = 'media/xls/mail_tasks.csv';
        
        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }
        
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) {
                continue;
            }
            
            if ($index > 88) {
                echo('all done'); die();
            }
            
            //var_dump($row);
            $code = $row[0];
            $task = iconv("Windows-1251", "UTF-8", $row[1]);
            $duration = $row[2];
            
            
            $mail = MailTemplateModel::model()->byCode($code)->find();
            if (!$mail) {
                echo("cant find mail by code $code"); die();
            }
            
            
            $model = new MailTasksModel();
            $model->mail_id = $mail->id;
            $model->name = $task;
            $model->duration = $duration;
            $model->insert();
        }
        fclose($handle);
        echo("Done");
    }
    
    public function actionImportPhrases() {
        $fileName = 'media/xls/mail_phrases.csv';
        
        $special = array();
        $standart = array();
        
        $system = array();
        $system[] ='.';
        $system[] =',';
        $system[] =':';
        $system[] =';';
        $system[] ='-';
        $system[] ='"';
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
            }
            
            if ($index > 66) {
                echo('all done'); die();
            }
            
            //var_dump($row);
            if ($row[0] != '') {
                $special[] = iconv("Windows-1251", "UTF-8", $row[0]);
            }
            
            if (isset($row[1]) &&  $row[1]!='') {
                $standart[] = iconv("Windows-1251", "UTF-8", $row[1]);
            }
            
            
            
            /*$mail = MailTemplateModel::model()->byCode($code)->find();
            if (!$mail) {
                echo("cant find mail by code $code"); die();
            }
            
            
            $model = new MailTasksModel();
            $model->mail_id = $mail->id;
            $model->name = $task;
            $model->duration = $duration;
            $model->insert();*/
        }
        fclose($handle);
        
        foreach($special as $phrase) {
            $model = new MailPhrasesModel();
            $model->character_theme_id = 1;
            $model->name = $phrase;
            $model->insert();
        }
        
        foreach($standart as $phrase) {
            $model = new MailPhrasesModel();
            $model->character_theme_id = null;
            $model->name = $phrase;
            $model->phrase_type = 2;
            $model->insert();
        }
        
        foreach($system as $phrase) {
            $model = new MailPhrasesModel();
            $model->character_theme_id = null;
            $model->name = $phrase;
            $model->phrase_type = 1;
            $model->insert();
        }
        
        
        echo("Done");
    }
}

?>
