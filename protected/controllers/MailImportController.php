<?php

/**
 * Импорт почты
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailImportController extends AjaxController{
    
    public function actionImport()
    {        
        $fileName = __DIR__.'/../../media/mail.csv';
        if(!file_exists($fileName)) {
            echo "Файл {$fileName} не найден!";
            die;
        }
        
        $counter = array(
            'all'  => 0,
            'MY'     => 0,
            'M'      => 0,
            'MSY'    => 0,
            'MS'     => 0,
            'mark-codes' => 0,
            'mark-0' => 0,
            'mark-1' => 0,
        );
     
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
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {            
            $index++;
            
            if ($index == 2) {
                // загрузим кодов
                $START_COL = 20;
                $END_COL = 134;
                $columnIndex = $START_COL;
                while($columnIndex < $END_COL) {
                    $pointsCodes[$columnIndex] = $row[$columnIndex];
                    $columnIndex++;
                    $counter['mark-codes']++;
                }
                continue;
            }
            
            if ($index <= 2) {
                continue;
            }
           
            // Код письма
            $code = $row[0];  // A
            // дата отправки
            $sendingDate = $row[1]; // B
            // время отправки
            $sendingTime = $row[2]; // C
            // От кого (код)
            $fromCode = $row[3]; // D
            // Кому (код)
            $toCode = $row[5];  // F
            // Копия (код)
            $copies = $row[7]; // H
            // тема
            $subject = iconv("Windows-1251", "UTF-8", $row[9]);  // J
            // Письмо
            $message = iconv("Windows-1251", "UTF-8", $row[10]); // K
            // Вложение
            $attachment = $row[11];  // L
            
            $typeOfImportance = trim($row[13]);
            
            if (false === isset($exists[$code])) {
                $exists[$code] = 1;
            } else {
                $exists[$code]++;
            }
            
            $group = 5;
            $type = 0;
            // определение группы по коду
            if ( preg_match("/MY\d+/", $code) ) {
                $group = 1;
                $type = 3;
                $counter['MY']++;
            } else if( preg_match("/M\d+/", $code) ) {
                $type = 1;
                $counter['M']++;
            } else if( preg_match("/MSY\d+/", $code) ) {
                $group = 3;
                $type = 4;
                $counter['MSY']++;
            } else if( preg_match("/MS\d+/", $code) ){
                $type = 2;
                $counter['MS']++;
            } else {
                echo "Ошибка: \$code = $code <br/> Line $index."; //TODO: Дописать описание
                echo var_dump($code);
                die;
            }
            
            if (!isset($characters[$fromCode])) {                
                echo "cant find character by code $fromCode <br/>";
                echo "Line $index";
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
                echo("Can`t find character by code $toCode");
                die();
            }
            $toId = $characters[$toCode];
            
            $date = explode('/', $sendingDate);
            $time = explode(':', $sendingTime);
            if (!isset($time[1])) {
                $time[0] = 0;
                $time[1] = 0;
            }
            if (!isset($date[1])) {
                echo("line: $index, code $code date : ");
                var_dump($sendingDate); 
                echo "<br/>Line $index";
                die();
            }
            
            if (!isset($time[1])) {
                echo("line: $index, code $code time : ");
                var_dump($sendingTime); 
                die();
            }
            
            $sendingDate = null;
            if (isset($date[1])) {
                $sendingDate = gmmktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
            }
            
            // themes update {
            $subjectEntity = MailThemesModel::model()->byName($subject)->find();
            if (null === $subjectEntity) {
                $subjectEntity = new MailThemesModel();
                $subjectEntity->name = $subject;
                $subjectEntity->insert();
            }
            
            $subjectId = $subjectEntity->id; 
            // themes update }
            
            $model = MailTemplateModel::model()->byCode($code)->find();
            if (!$model) {
                $model = new MailTemplateModel();
                $model->group_id = $group;
                $model->sender_id = $fromId;
                $model->receiver_id = $toId;
                $model->subject = $subject;
                $model->subject_id = $subjectId;
                $model->message = $message;
                $model->sending_date = $sendingDate;
                $model->code = $code;
                $model->type = $type;
                $model->type_of_importance = $typeOfImportance;

                $model->insert();
                //echo("insert code: $code index $index <br/>");
            }
            else {
                $model->group_id = $group;
                $model->sender_id = $fromId;
                $model->receiver_id = $toId;
                $model->subject = $subject;
                $model->subject_id = $subjectId;
                $model->message = $message;
                $model->sending_date = $sendingDate;
                $model->type = $type;
                $model->type_of_importance = $typeOfImportance;
                $model->update();
                
                //echo("updated code: $code index $index <br/>");
            }
            
            // учтем поинты (оценки, marks)
            $columnIndex = $START_COL;
            while($columnIndex < $END_COL) {
                $value = $row[$columnIndex];
                if ($value == '') {
                    $columnIndex++;
                    continue;
                }
                
                $pointCode = $pointsCodes[$columnIndex];
                if (!isset($pointsInfo[$pointCode])) throw new Exception("cant get point id by code $pointCode");
                $pointId = $pointsInfo[$pointCode];
                
                $pointModel = MailPointsModel::model()->byMailId($model->id)->byPointId($pointId)->find();
                if (null === $pointModel) {
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
                
                if ( 1 == (int)$value) {
                    $counter['mark-1']++;
                } else {
                    $counter['mark-0']++;
                }

                $columnIndex++;
            }

            foreach($receivers as $ind => $receiverCode) {
                if (!isset($characters[$receiverCode])) {
                    echo("cant find receiver by code $receiverCode"); die();
                }
                $receiverId = $characters[$receiverCode];
                //echo("r = $receiverId  id: {$model->id} <br/>");
                
                // Проверяется не значится ли у нас для такого письма уже такой получатель и если нет то добавляем запись
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
            
            $counter['all']++;
        }
        fclose($handle);
        
        echo sprintf(
           'Lines imported: %s . must be 86<br/>
            <br/>
            Inbox: %s. must be 42<br/>
            - MY: %s. must be 38<br/>
            - M: %s. must be 4<br/>
            <br/>
            Outbox: %s. must be 44<br/>
            - MSY: %s. must be 1<br/>
            - MS: %s. must be 43<br/>
            <br/>
            Marks codes amount: %s must be 114<br/>
            - Marks "0": %s. must be 86<br/>
            - Marks "1": %s. must be 97<br/>
            <br/>
            Email import was finished.
            ',
            $counter['all'],
            $counter['M'] + $counter['MY'],
            $counter['M'],
            $counter['MY'],
            $counter['MS'] + $counter['MSY'],
            $counter['MSY'],
            $counter['MS'],
            $counter['mark-codes'],
            $counter['mark-0'],
            $counter['mark-1']
        );
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
            
            // Mail code
            $code       = $row[0];  // A
            // Task
            $task       = iconv("Windows-1251", "UTF-8", $row[1]); // B
            // Duration
            $duration   = $row[2]; // C
            // Task W/R
            $wr         = $row[3]; // D
            // Category
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
        
        $import = new ImportMailPhrases();
        $import->run();
        //exit;
//        $special = array();
//        $standart = array();
//        
//        $system = array();
//        $system[] ='.';
//        $system[] =',';
//        $system[] =':';
//        $system[] =';';
//        $system[] ='-';
//        $system[] ='"';
//        
//        $handle = fopen($fileName, "r");
//        if (!$handle) throw new Exception("cant open $fileName");
//        $index = 0;
//        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
//            $index++;
//            if ($index == 1) {
//                $code1 = 'В1'; //$row[0];
//                $code2 = $row[1];
//                continue;
//            }
//            
//            if ($index > 66) {
//                echo('all done'); die();
//            }
//            
//            //var_dump($row);
//            if ($row[0] != '') {
//                $name1 = iconv("Windows-1251", "UTF-8", $row[0]);
//            }
//            
//            if (isset($row[1]) &&  $row[1]!='') {
//                $name2 = iconv("Windows-1251", "UTF-8", $row[1]);
//            }
//            
//            $model = new MailPhrasesModel();
//            $model->character_theme_id = null;
//            $model->name = $name1;
//            $model->code = $code1;
//            $model->insert();
//            
//            $model = new MailPhrasesModel();
//            $model->character_theme_id = null;
//            $model->name = $name2;
//            $model->code = $code2;
//            $model->insert();
            
            /*$mail = MailTemplateModel::model()->byCode($code)->find();
            if (!$mail) {
                echo("cant find mail by code $code"); die();
            }
            
            
            $model = new MailTasksModel();
            $model->mail_id = $mail->id;
            $model->name = $task;
            $model->duration = $duration;
            $model->insert();*/
//        }
//        fclose($handle);
        
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
        
//        foreach($system as $phrase) {
//            $model = new MailPhrasesModel();
//            $model->character_theme_id = null;
//            $model->name = $phrase;
//            $model->code = 'SYS';
//            $model->insert();
//        }
        
        
        echo("Done");
    }
    
    /**
     * Импорт тем для писем F-S-C
     */
    public function actionImportThemes() {
        $fileName = __DIR__.'/../../media/xls/mail_themes.csv';
        
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
            
            // Определение кода персонажа
            $characterCode = $row[0]; // A
            if (!isset($characters[$characterCode])) throw new Exception("cant find character by code $characterCode");

            $characterId        = $characters[$characterCode];  
            // Определим тему письма
            $subject            = $row[2]; // C
            // Phone
            $phone              = $row[3]; // D
            // Phone W/R
            $phoneWr            = $row[4]; // E
            // Phone dialogue number
            $phoneDialogNumber  = $row[5]; // F
            // Mail
            $mail               = $row[6]; // G
            // Mail letter number
            $mailCode           = $row[7]; // H
            // Mail W/R
            $wr                 = $row[8]; // I
            // Mail constructor number
            $constructorNumber  = $row[9]; // J
            // Source of outbox email
            $source             = $row[10]; // K
            
            // определить код темы
            $subjectModel = MailThemesModel::model()->byName($subject)->find();
            if (null === $subjectModel) {
                $subjectModel = new MailThemesModel();
                $subjectModel->name = $subject;
                $subjectModel->insert();
            }
            $subjectId = $subjectModel->id;
            
            $mailCharacterTheme = MailCharacterThemesModel::model()
                ->byCharacter($characterId)
                ->byTheme($subjectId)
                ->find();
            if (null === $mailCharacterTheme) {
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
            $mailCharacterTheme->source                   = $source;
            
            try {
                $mailCharacterTheme->save();            
                echo sprintf(
                    'Succesfully imported - email from "%s", %s subject "%s" . [MySQL id: %s] <br/>',
                    $row[1],
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                    $row[2],
                    $mailCharacterTheme->id
                );
            } catch(CDbException $e) {
                echo sprintf(
                    'Error during import line %s. DB error message: %s <br/>',
                    $index,
                    $e->getMessage()
                );
                
            } catch(Exception $e) {
                echo sprintf(
                    'Error during import line %s. Error message: %s <br/>',
                    $index,
                    $e->getMessage()
                );
                
            }
        }
        fclose($handle);
        
        echo("processed rows: $index <br/>");
        echo("Email from characters import finished! <br/>");
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


