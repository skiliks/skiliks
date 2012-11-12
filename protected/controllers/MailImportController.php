<?php



/**
 * Импорт почты
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailImportController extends AjaxController{
    
    public function actionImport() {
        $fileName = 'media/mail.csv';
        
        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }
        
        // загрузим информацию о поинтах
        $pointsTitles = CharactersPointsTitles::model()->findAll();
        $pointsInfo = array();
        foreach($pointsTitles as $item) {
            $pointsInfo[$item->code] = $item->id;
        }
        
        $exists = array();
        
        //var_dump($characters); die();
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            
            if ($index == 2) {
                // загрузим кодов
                $columnIndex = 19;
                while(isset($row[$columnIndex]) && $row[$columnIndex] != '' &&  $columnIndex<=126) {
                    $pointsCodes[$columnIndex] = $row[$columnIndex];
                    $columnIndex++;
                }
                    //var_dump($pointsCodes); die();
                //$index++;
                continue;
            }
            
            if ($index <= 2) {
                continue;
            }
            
            if ($index > 88) {
                var_dump($exists);
                echo('all done'); die();
            }
            //var_dump($row);
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
            
            if (!isset($exists[$code])) {
                $exists[$code] = 1;
            }
            else {
                $exists[$code]++;
            }
            
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
                echo("index : $index");
                var_dump($row);
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
                echo("insert code: $code index $index <br/>");
            }
            else {
                $model->group_id = $group;
                $model->sender_id = $fromId;
                $model->receiver_id = $toId;
                $model->subject = $subject;
                $model->message = $message;
                $model->sending_date = $sendingDate;
                $model->update();
                
                echo("updated code: $code index $index <br/>");
            }
            
            // учтем поинты
            $columnIndex = 19;
            while($columnIndex<=126) {
                $value = $row[$columnIndex];
                if ($value == '') {
                    $columnIndex++;
                    continue;
                }
                
                $pointCode = $pointsCodes[$columnIndex];
                if (!isset($pointsInfo[$pointCode])) throw new Exception("cant get point id by code $pointCode");
                $pointId = $pointsInfo[$pointCode];
                
                $pointModel = MailPointsModel::model()->byMailId($model->id)->byPointId($pointId)->find();
                if (!$pointModel) {
                    $pointModel = new MailPointsModel();
                    $pointModel->mail_id = $model->id;
                    $pointModel->point_id = $pointId;
                    $pointModel->add_value = $value;
                    $pointModel->insert();
                }
                else {
                    $pointModel->point_id = $pointId;
                    $pointModel->add_value = $value;
                    $pointModel->update();
                }
                

                $columnIndex++;
            }
            
            foreach($receivers as $ind => $receiverCode) {
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
            foreach($copiesArr as $ind => $characterCode) {
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
    
    /**
     * Импорт задач для писем M-T
     */
    public function actionImportTasks() {
        $fileName = 'media/xls/mail_tasks.csv';
        
        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }
        
        $connection=Yii::app()->db;   

        
        /*$sql = 'ALTER TABLE `mail_tasks` AUTO_INCREMENT =1';
        $command = $connection->createCommand($sql);
        $command->execute();*/
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) continue;
            
            if ($index > 102) {
                echo('all done'); die();
            }
            
            $code       = $row[0];  // A
            $task       = iconv("Windows-1251", "UTF-8", $row[1]); // B
            $duration   = $row[2]; // C
            $wr         = $row[3]; // D
            $category   = $row[4]; // E
            
            $mail = MailTemplateModel::model()->byCode($code)->find();
            if (!$mail) {
                throw new Exception("cant find mail by code $code");
            }
            
            //$model = MailTasksModel::model()->model()->byMailId($mail->id)->find();
            $model = MailTasksModel::model()->model()->byId($index-1)->find();
            if (!$model) {
                throw new Exception("cant find model by index $index");
                $model = new MailTasksModel();
                $model->mail_id = $mail->id;
            }
            $model->name        = $task;
            $model->duration    = $duration;
            $model->code        = $code;
            $model->wr          = $wr;
            $model->category    = $category;
            $model->save();
            
            echo("save : id {$mail->id} task $task duration $duration code $code wr $wr category $category <br/>");
            
            //if (!$model->insert()) throw new Exception("cant create $code $duration");
            
        }
        fclose($handle);
        echo("Done");
    }
    
    /**
     * Импорт фраз для писем
     */
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
            if ($index == 1) {
                $code1 = 'В1'; //$row[0];
                $code2 = $row[1];
                continue;
            }
            
            if ($index > 66) {
                echo('all done'); die();
            }
            
            //var_dump($row);
            if ($row[0] != '') {
                $name1 = iconv("Windows-1251", "UTF-8", $row[0]);
            }
            
            if (isset($row[1]) &&  $row[1]!='') {
                $name2 = iconv("Windows-1251", "UTF-8", $row[1]);
            }
            
            $model = new MailPhrasesModel();
            $model->character_theme_id = null;
            $model->name = $name1;
            $model->code = $code1;
            $model->insert();
            
            $model = new MailPhrasesModel();
            $model->character_theme_id = null;
            $model->name = $name2;
            $model->code = $code2;
            $model->insert();
            
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
        
        /*foreach($special as $phrase) {
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
        }*/
        
        foreach($system as $phrase) {
            $model = new MailPhrasesModel();
            $model->character_theme_id = null;
            $model->name = $phrase;
            $model->code = 'SYS';
            $model->insert();
        }
        
        
        echo("Done");
    }
    
    /**
     * Импорт тем для писем F-S-C
     */
    public function actionImportThemes() {
        $fileName = 'media/xls/mail_themes.csv';
        
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
            if ($index <= 1) continue;
            
            if ($index > 115) { 
                echo("processed rows: $index");
                echo('all done'); die(); 
            }
            
            $characterCode = $row[0]; // A
            if (!isset($characters[$characterCode])) throw new Exception("cant find character by code $characterCode");

            $characterId        = $characters[$characterCode];  
            $subject            = iconv("Windows-1251", "UTF-8", $row[2]); // C
            $phone              = $row[3]; // D
            $phoneWr            = $row[4]; // E
            $phoneDialogNumber  = $row[5]; // F
            $mail               = $row[6]; // G
            $mailCode           = $row[7]; // H
            $wr                 = $row[8]; // I
            $constructorNumber  = $row[9]; // J
            
            // определить код темы
            $subjectModel = MailThemesModel::model()->byName($subject)->find();
            if (!$subjectModel) {
                $subjectModel = new MailThemesModel();
                $subjectModel->name = $subject;
                $subjectModel->insert();
            }
            $subjectId = $subjectModel->id;
            
           
            
            
            $mailCharacterTheme = MailCharacterThemesModel::model()->byCharacter($characterId)->byTheme($subjectId)->find();
            if (!$mailCharacterTheme) {
                $mailCharacterTheme = new MailCharacterThemesModel();
                $mailCharacterTheme->character_id   = $characterId;
                $mailCharacterTheme->theme_id       = $subjectId;
            }
            
            $mailCharacterTheme->letter_number          = $mailCode;
            $mailCharacterTheme->wr                     = $wr;
            $mailCharacterTheme->constructor_number     = $constructorNumber;
            $mailCharacterTheme->phone                  = $phone;
            $mailCharacterTheme->phone_wr               = $phoneWr;
            $mailCharacterTheme->phone_dialog_number    = $phoneDialogNumber;
            $mailCharacterTheme->mail                   = $mail;
            $r = $mailCharacterTheme->save();
            
            var_dump($r);
        }
        fclose($handle);
        echo("processed rows: $index");
        echo("All done!");
    }
    
    /**
     * Импорт событий из писем
     */
    public function actionImportEvents() {
        $fileName = 'media/xls/mail2.csv';
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        
        $events = EventService::getAllCodesList();
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
            }
            
            if ($index > 88) {
                echo('all done'); die();
            }
            
            $code = $row[0];
            $time = DateHelper::timeToTimstamp($row[2]);
            
            $event = EventsSamples::model()->byCode($code)->find();
            if (!$event) {
                $event = new EventsSamples();
                $event->code = $code;
            }
                
            $event->on_ignore_result = 0;	
            $event->on_hold_logic = 1;
            $event->trigger_time = $time; 
            $event->save();
            echo("update event : $code <br/>");
            
            
        }
        fclose($handle);
        echo("All done!");    
    }
    
    public function actionImportTime() {
        $fileName = 'media/xls/mail2.csv';
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        
        $events = EventService::getAllCodesList();
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
            }
            
            if ($index > 88) {
                echo('all done'); die();
            }
            
            $code = $row[0];
            $date = DateHelper::dateStringToTimestamp($row[1]);
            $time = DateHelper::timeToTimstamp($row[2]);
            
            $mail = MailTemplateModel::model()->byCode($code)->find();
            if ($mail) {
                $mail->sending_date = $date;
                $mail->sending_date_str = $row[1];
                $mail->sending_time = $time;
                $mail->sending_time_str = $row[2];
                $mail->save();
            }
            
        }
        fclose($handle);
        echo("All done!");    
    }
    
    public function actionImportAttache() {
        $fileName = 'media/xls/mail2.csv';
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        
        $documents = MyDocumentsService::getAllCodes();
        //var_dump($documents); die();
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
            }
            
            if ($index > 88) {
                echo('all done'); die();
            }
            
            $code = $row[0];
            $attache = $row[11];
            
            if ($attache == '' || $attache =='-') continue; // нет аттачей
                
            $mail = MailTemplateModel::model()->byCode($code)->find();
            if ($mail) {
                if (isset($documents[$attache])) {
                    $fileId = $documents[$attache];
                    
                    $attacheModel = MailAttachmentsTemplateModel::model()->byMailId($mail->id)->byFileId($fileId)->find();
                    if (!$attacheModel) {
                        $attacheModel = new MailAttachmentsTemplateModel();
                        $attacheModel->mail_id = $mail->id;
                        $attacheModel->file_id = $fileId;
                        $attacheModel->insert();
                        echo("insert attache : $attache <br/>");
                    }
                }
            }
            
        }
        fclose($handle);
        echo("All done!");    
    }
}

?>
