<?php

/**
 * Импорт почты
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailImportController extends AjaxController{
    
    public function actionImport()
    {        
        $importService = new ImportGameDataService();
        $result = $importService->importEmails();    	

        $this->renderText($result['text']);
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
        $result = $import->run();       
        
        $this->renderText($result['text']);
    }
    
    /**
     * Импорт тем для писем F-S-C
     */
    public function actionImportThemes() 
    {
    	$importService = new ImportGameDataService();
        $result = $importService->importEmailSubjects();    	

        $this->renderText($result['text']);
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


