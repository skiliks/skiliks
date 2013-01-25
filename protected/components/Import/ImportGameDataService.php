<?php
/**
 * @author slavka
 */
class ImportGameDataService
{
    private $filename     = null;
    
    private $import_id    = null;
    
    private $errors       = null;
    
    private $cache_method = null;
    
    private $columnNoByName = array();
    
    public function __construct()
    {
        $this->filename = __DIR__ . '/../../../media/Scenario_v22.19_TP_Await_added.xlsx';
        $this->import_id = $this->getImportUUID();
        $this->cache_method = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
    }


    /**
     * Import characters, requires nothing
     *
     * @return mixed array
     * 
     * @throws Exception
     */
    public function importCharacters()
    {
        $reader = $this->getReader();
        
        // load sheet {
        $reader->setLoadSheetsOnly('Faces_new');
        $excel = $reader->load($this->filename);
        $sheet = $excel->getSheetByName('Faces_new');
        // load sheet }
        
        $this->setColumnNumbersByNames($sheet);
        
        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if (NULL === $this->getCellValue($sheet, 'id_персонажа', $i)) {
                continue;
            }

            // try to find exists entity 
            $character = Characters::model()->byCode($this->getCellValue($sheet, 'id_персонажа', $i))->find();
            
            // create entity if not exists {
            if (!$character) {
                $character = new Characters();
            }
            // create entity if not exists }
            
            // update data {
            $character->code      = $this->getCellValue($sheet, 'id_персонажа', $i);
            $character->title     = $this->getCellValue($sheet, 'Должность', $i);
            $character->fio       = $this->getCellValue($sheet, 'ФИО - short', $i);
            $character->email     = $this->getCellValue($sheet, 'e-mail', $i);
            $character->skype     = $this->getCellValue($sheet, 'skype', $i);
            $character->phone     = $this->getCellValue($sheet, 'телефон', $i);
            $character->import_id = $this->import_id;
            
            // save
            $character->save();
            
            $importedRows++;
        }
        
        // delete old unused data {
        Characters::model()->deleteAll(
            'import_id<>:import_id', 
            array('import_id' => $this->import_id)
        );
        // delete old unused data }
        
        return array(
            'imported_characters' => $importedRows, 
            'errors' => false, 
        );
    }
    
    public function importCharactersPointsTitles() 
    {
        $start_time = microtime(true);
        $count_add_ceil = 0;
        $count_edit_ceil = 0;
        $count_add_title = 0;
        $count_edit_title = 0;
        $count_str = 0;
        $count_col = 0;
        $array_add_ceil = array();
        $array_edit_ceil = array();
        $array_add_title = array();
        $array_edit_title = array();
        $temp = array();

        $filename = __DIR__ . '/../../../media/Forma1_new_170912_v3.xlsx';

        if (file_exists($filename)) {
            $SimpleXLSX = new SimpleXLSX($filename);
            $xlsx_data = $SimpleXLSX->rows();
        } else {
            throw new Exception("Файл {$filename} не найден!");
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {
            $db_parent = Yii::app()->db->createCommand()
                ->select('id, parent_id, code, title, scale, type_scale')
                ->from('characters_points_titles')
                ->where(' parent_id IS NULL ')
                ->queryAll();

            unset($xlsx_data[0]);
            unset($xlsx_data[count($xlsx_data)]);

            $titles = array();
            $titles_ceil = array();

            foreach ($xlsx_data as $row) {
                $pos = array_search(array($row[1], $row[2]), $titles_ceil);
                if ($pos == false) {
                    $titles_ceil[] = array($row[1], $row[2]);
                    $pos = array_search(array($row[1], $row[2]), $titles_ceil);
                    $titles[] = array($row[0], $pos, $row[3], $row[4], $row[5], $row[1]);
                } else {
                    $titles[] = array($row[0], $pos, $row[3], $row[4], $row[5], $row[1]);
                }
            }

            $count_str = count($xlsx_data);
            $count_col = count($xlsx_data[1]);

            unset($xlsx_data);

            $command = Yii::app()->db->createCommand();

            foreach ($titles_ceil as $k1 => $title) {
                $found = false;
                //Поиск совпаденией и обновление записей по коду
                foreach ($db_parent as $k2 => $data) {
                    if ($data['code'] == $title[0]) {
                        if ($data['title'] == $title[1]) {
                            
                        } else {
                            //TODO:Изменить запись
                            $command->update('characters_points_titles', array(
                                'title' => $title[1]
                                    ), 'id=:id', array(':id' => $data['id']));
                            $array_edit_ceil[] = $title[0];
                            $count_edit_ceil++;
                        }
                        $found = true;
                        unset($titles_ceil[$k1]);
                        unset($db_parent[$k2]);
                    } else {
                        
                    }
                }
                if (!$found) {
                    //TODO:Добавить запись
                    $command->insert('characters_points_titles', array(
                        'code' => $title[0],
                        'title' => $title[1]
                    ));
                    //unset($db_data[$k1]);
                    $count_add_ceil++;
                    $array_add_ceil[] = $title[0];
                }
            }


            $db_data = Yii::app()->db->createCommand()
                ->select('p2.id, p1.code as p_code, p2.code, p2.title, p2.scale, p2.type_scale')
                ->from('characters_points_titles p1')
                ->join('characters_points_titles p2', 'p1.id = p2.parent_id')
                ->queryAll();

            $db_keys = Yii::app()->db->createCommand()
                ->select('id, code')
                ->from('characters_points_titles')
                ->queryAll();

            $keys = array();
            foreach ($db_keys as $row) {
                $keys[$row['code']] = $row['id'];
            }

            $type_scale = array('positive' => '1', 'negative' => '2', 'personal' => '3');
            foreach ($titles as $k1 => $title) {
                $found = false;
                //Поиск совпаденией и обновление записей по коду
                foreach ($db_data as $k2 => $data) {
                    if ($data['code'] == $title[0]) {
                        if ($data['title'] == $title[2] && $data['scale'] == $title[3] && $data['type_scale'] == $type_scale[$title[4]] && $data['p_code'] == $title[5]) {
                        } else {
                            //TODO:Изменить запись
                            
                            $command->update('characters_points_titles', array(
                                'parent_id' => $keys[$title[5]],
                                'title' => $title[2],
                                'scale' => $title[3],
                                'type_scale' => $type_scale[$title[4]]
                                    ), 'id=:id', array(':id' => $data['id']));
                            $count_edit_title++;
                            $array_edit_title[] = $title[0];
                        }
                        $found = true;
                        unset($titles[$k1]);
                        unset($db_data[$k2]);
                    } else {
                        
                    }
                }
                
                if (!$found) {
                    //TODO:Добавить запись
                    $command->insert('characters_points_titles', array(
                        'code' => $title[0],
                        'title' => $title[2],
                        'scale' => $title[3],
                        'type_scale' => $title[4]
                    ));
                    $count_add_title++;
                    $array_add_title[] = $title[0];
                }
            }

            $transaction->commit();
        } catch (Exception $e) {

            $transaction->rollback();
            
            return array(
                'status' => false,
                'text'   => $e->getMessage() . " в файле " . $e->getFile() . " на строке  " . $e->getLine() . '<br>',
            );
        }
        
        $end_time = microtime(true);
        
        // ---
        
        $html = sprintf(
            '<h3>Файл - %s </h3><br/>
            Размер - %s Кбайт <br/>
            Время последнего изменения файла  - %s <br/>
            Количество обработаных строк данных - %s по %s колонки <br/>
            Лишних наименований целей обучения в бд: %s <br/>
            ',
            $filename,
            filesize($filename) / 1024,
            date("d.m.Y H:i:s.", filemtime($filename)),
            $count_str,
            $count_col,
            count($db_parent)
        );
        
        // if you want - you can fihish $html
        /*echo "Время последнего изменения файла  - " . date("d.m.Y H:i:s.", filemtime($filename)) . " <br>";
        echo "Время импорта - " . ($end_time - $start_time) . ' c. <br>';
        echo "Количество обработаных строк данных - " . $count_str . " по " . $count_col . ' колонки <br>';
        echo "Обновлено {$count_edit_ceil} наименований целей обучения <br>";
        if ($array_edit_ceil != array()) {
            echo "Cреди них : " . implode(" , ", $array_edit_ceil) . " <br>";
        }
        echo "Добавлено  {$count_add_ceil} наименованиий целей обучения <br>";
        if ($array_add_ceil != array()) {
            echo "Cреди них : " . implode(" , ", $array_add_ceil) . " <br>";
        }
        echo "Обновлено {$count_edit_title} наименованиий требуемого поведения <br>";
        if ($array_edit_title != array()) {
            echo "Cреди них : " . implode(" , ", $array_edit_title) . " <br>";
        }
        echo "Добавлено {$count_add_title} наименованиий требуемого поведения <br>";
        if ($array_add_title != array()) {
            echo "Cреди них : " . implode(" ,", $array_add_title) . " <br>";
        }
        echo "Лишних наименований целей обучения в бд " . count($db_parent) . '<br>';
        if ($db_parent != array()) {
            foreach ($db_parent as $t) {
                $temp[] = $t['code'];
            }
            echo "Cреди них : " . implode(" , ", $temp) . " <br>";
        }
        echo "Лишних наименований требуемого поведения в бд " . count($db_data) . '<br>';
        $temp = array();
        if ($db_parent != array()) {
            foreach ($db_data as $t) {
                $temp[] = $t['code'];
            }
            echo "Cреди них : " . implode(" , ", $temp) . " <br>";
        }
        echo "</h3>";*/
        
        return array(
            'status' => true,
            'text'   => $html,
        );
    }
    
    public function importEmails()
    {        
        $fileName = __DIR__.'/../../../media/mail.csv';
        if(!file_exists($fileName)) {
            return array(
                'status' => false,
                'text'   => "Файл {$fileName} не найден!"
            );
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
        
        $emailIds = array(); // to delete old letters after import
        $emailToCopyIds = array(); // to delete old letter-cope relations after import
        $emailToRecipientIds = array(); // to delete old letter-recipient relations after import
        $emailToPointIds = array(); // to delete old letter-point relations after import
        $emailSubjectsIds = array(); // to delete old letter-"theme" relations after import
     
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
        
        // Get all exist system mail_templates to avoid SQL queries againts each request {
        $existsMailTemplate = array();
        foreach (MailTemplateModel::model()->findAll() as $mailTemplate) {
            $existsMailTemplate[$mailTemplate->code] = $mailTemplate;
        }
        // Get all mail_templates }
        
        // Get all exist system mail_themes to avoid SQL queries againts each request {
        $existsMailThemes = array();
        foreach (MailCharacterThemesModel::model()->findAll() as $mailTheme) {
            $existsMailThemes[$mailTheme->text] = $mailTheme;
        }
        // Get all mail_themes }
        
        $exists = array();
        
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        $index = 0;
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {            
            $index++;
            
            if ($index == 2) {
                // get point codes
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
           
            $code = $row[0];  // A, Код письма
            $sendingDate = $row[1]; // B, ата отправки
            $sendingTime = $row[2]; // C, время отправки
            $fromCode = $row[3]; // D, От кого (код - character_id)
            $toCode = $row[5];  // F, Кому (код - character_id)
            $copies = $row[7]; // H, Копия (код - character_ids)

            $subject = $row[9];  // J, тема
            $subject = StringTools::fixReAndFwd($subject);
            
            // Письмо
            $message = $row[10]; // K
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
            $source = null;
            if ( preg_match("/MY\d+/", $code) ) {
                $group = 1;
                $type = 3;
                $counter['MY']++;
                $source = 'inbox';
            } else if( preg_match("/M\d+/", $code) ) {
                $type = 1;
                $counter['M']++;
                $source = 'inbox';
            } else if( preg_match("/MSY\d+/", $code) ) {
                $group = 3;
                $type = 4;
                $counter['MSY']++;
                $source = 'outbox';
            } else if( preg_match("/MS\d+/", $code) ){
                $type = 2;
                $counter['MS']++;
                $source = 'outbox';
            } else {
                return array(
                    'status' => false,
                    'text'   => "Ошибка: \$code = $code <br/> Line $index."
                );
            }
            
            if (!isset($characters[$fromCode])) {                
                return array(
                    'status' => false,
                    'text'   => "cant find character by code $fromCode <br/> Line $index",
                );
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
                return array(
                    'status' => false,
                    'text'   => "Can`t find character by code $toCode",
                );
            }
            $toId = $characters[$toCode];
            
            $date = explode('/', $sendingDate);
            $time = explode(':', $sendingTime);
            if (!isset($time[1])) {
                $time[0] = 0;
                $time[1] = 0;
            }
            if (!isset($date[1])) {
                return array(
                    'status' => false,
                    'text'   => "line: $index, code $code date : $sendingDate<br/>Line $index",
                );
            }
            
            if (!isset($time[1])) {
                return array(
                    'status' => false,
                    'text'   => "line: $index, code $code time : $sendingTime",
                );
            }
            
            $sendingDate = null;
            if (isset($date[1])) {
                $sendingDate = gmmktime($time[0], $time[1], 0, $date[1], $date[0], $date[2]);
            }
            
            // themes update {
            if (false === isset($existsMailThemes[$subject])) {
                $subjectEntity = new MailCharacterThemesModel();
                $subjectEntity->character_id        = $fromCode;
                $subjectEntity->text                = $subject;
                $subjectEntity->letter_number       = $code;
                $subjectEntity->wr                  = 'W';
                $subjectEntity->wr                  = 'W';
                $subjectEntity->constructor_number  = 'B1'; // base-default
                $subjectEntity->phone               = 0;    // this is email
                $subjectEntity->phone_dialog_number = '';   // this is email
                $subjectEntity->mail                = 1;    // this is email
                $subjectEntity->source              = $source; 
                $subjectEntity->save();
                // theme must exist, this is dirty hack to create them
                // @todo: fix asap with reimport
            } else {
                $subjectEntity = $existsMailThemes[$subject];
            }

            $emailSubjectsIds[] = $subjectEntity->id;
            // themes update }
            
            $emailTemplateEntity = null;
            if (isset($existsMailTemplate[$code])) {
                $emailTemplateEntity = $existsMailTemplate[$code];
            }
            if (null === $emailTemplateEntity) {
                $emailTemplateEntity = new MailTemplateModel();
                $emailTemplateEntity->group_id           = $group;
                $emailTemplateEntity->sender_id          = $fromId;
                $emailTemplateEntity->receiver_id        = $toId;
                $emailTemplateEntity->subject_id         = $subjectEntity->id;
                $emailTemplateEntity->message            = $message;
                $emailTemplateEntity->sent_at       = $sendingDate;
                $emailTemplateEntity->code               = $code;
                $emailTemplateEntity->type               = $type;
                $emailTemplateEntity->type_of_importance = $typeOfImportance;

                $emailTemplateEntity->insert();
            }
            else {
                $emailTemplateEntity->group_id           = $group;
                $emailTemplateEntity->sender_id          = $fromId;
                $emailTemplateEntity->receiver_id        = $toId;
                $emailTemplateEntity->subject_id         = $subjectEntity->id;
                $emailTemplateEntity->message            = $message;
                $emailTemplateEntity->sent_at       = $sendingDate;
                $emailTemplateEntity->type               = $type;
                $emailTemplateEntity->type_of_importance = $typeOfImportance;
                $emailTemplateEntity->update();
            }
            
            $emailIds[] = $emailTemplateEntity->id;
            
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
                
                $pointEntity = MailPointsModel::model()->byMailId($emailTemplateEntity->id)->byPointId($pointId)->find();
                if (null === $pointEntity) {
                    $pointEntity = new MailPointsModel();
                    $pointEntity->mail_id = $emailTemplateEntity->id;
                    $pointEntity->point_id = $pointId;
                    $pointEntity->add_value = $value;
                    $pointEntity->insert();
                }
                else {
                    $pointEntity->point_id = $pointId;
                    $pointEntity->add_value = $value;
                    $pointEntity->update();
                }
                
                $emailToPointIds[] = $pointEntity->id;
                
                if ( 1 == (int)$value) {
                    $counter['mark-1']++;
                } else {
                    $counter['mark-0']++;
                }

                $columnIndex++;
            }

            foreach($receivers as $ind => $receiverCode) {
                if (!isset($characters[$receiverCode])) {
                    return array(
                        'status' => false,
                        'text'   => "cant find receiver by code $receiverCode",
                    );
                }
                $receiverId = $characters[$receiverCode];
                
                // Проверяется не значится ли у нас для такого письма уже такой получатель и если нет то добавляем запись
                $mrt = MailReceiversTemplateModel::model()->byMailId($emailTemplateEntity->id)->byReceiverId($receiverId)->find();
                if (null === $mrt) {
                    $mrt = new MailReceiversTemplateModel();
                    $mrt->mail_id = $emailTemplateEntity->id;
                    $mrt->receiver_id = $receiverId;
                    $mrt->insert();
                }
                
                $emailToRecipientIds[] = $mrt->id;
            }
            
            // а теперь учтем копии
            foreach($copiesArr as $ind => $characterCode) {
                if (!isset($characters[$characterCode])) {
                    return array(
                        'status' => false,
                        'text'   => "cant find chracter by code $characterCode",
                    );
                }
                $characterId = $characters[$characterCode];
                
                $mct = MailCopiesTemplateModel::model()
                    ->byMailId($emailTemplateEntity->id)
                    ->byReceiverId($characterId)
                    ->find();
                
                if (null === $mct) {
                    $mct = new MailCopiesTemplateModel();
                    $mct->mail_id = $emailTemplateEntity->id;
                    $mct->receiver_id = $characterId;
                    $mct->insert();
                }
                
                $emailToCopyIds[] = $mct->id;
            }
            
            $counter['all']++;
        }
        fclose($handle);
        
        // remove old entities {
       // copy relations {
       $emailCopyEntities = MailCopiesTemplateModel::model()
            ->byIdsNotIn(implode(',', $emailToCopyIds))
            ->findAll();
        
       foreach ($emailCopyEntities as $entity) {
           $entity->delete();
       }
       unset($entity);
       // copy relations }
       
       // recipient relations {
       $emailRecipientEntities = MailReceiversModel::model()
            ->byIdsNotIn(implode(',', $emailToRecipientIds))
            ->findAll();
        
       foreach ($emailRecipientEntities as $entity) {
           $entity->delete();
       }
       unset($entity);
       // recipient relations }
       
       // points relations {
       $emailPointsEntities = MailPointsModel::model()
            ->byIdsNotIn(implode(',', $emailToPointIds))
            ->findAll();
        
       foreach ($emailPointsEntities as $entity) {
           $entity->delete();
       }
       unset($entity);
       // points relations }
       
       // "theme" relations {
       $emailSubjectEntities = MailCharacterThemesModel::model()
            ->byIdsNotIn(implode(',', $emailSubjectsIds))
            ->findAll();
        
       foreach ($emailSubjectEntities as $entity) {
           $entity->delete();
       }
       unset($entity);
       // "theme" relations }
       
       // mail templates {
       $emailTemplates = MailTemplateModel::model()
            ->byIdsNotIn(implode(',', $emailIds))
            ->findAll();
        
       foreach ($emailTemplates as $emailTemplate) {
           $emailTemplate->delete();
       }
       // mail templates }
       // remove old entities }
        
        $html = sprintf(
           'Must be values regarding  (21 Dec 2012)  <br/>
            Lines imported: %s . must be 97<br/>
            <br/>
            Inbox: %s. must be 42<br/>
            - MY: %s. must be 38<br/>
            - M: %s. must be 4<br/>
            <br/>
            Outbox: %s. must be 55<br/>
            - MSY: %s. must be 1<br/>
            - MS: %s. must be 54<br/>
            <br/>
            Marks codes amount: %s must be 114<br/>
            - Marks "0": %s. must be 13<br/>
            - Marks "1": %s. must be 32<br/>
            <br/>
            Email import was finished. <br>
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
        
        return array(
            'status' => true,
            'text'   => $html,
        );
    }

    /**
     * Requires characters, emails
     *
     * @return array
     */
    public function importEmailSubjects()
    {
        $fileName = __DIR__.'/../../../media/xls/mail_themes.csv';
        
        $characterMailThemesIds = array(); // to remove all old characterMailThemes after import
        
        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }
        
        $handle = fopen($fileName, "r");
        if (!$handle) {
            return array(
                'status' => true,
                'text'   => "cant open $fileName",
            );
        }
        
        $html = '';
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) continue;
            
            // Определение кода персонажа
            $characterCode = $row[0]; // A
            if (!isset($characters[$characterCode])) { 
                return array(
                    'status' => true,
                    'text'   => "cant find character by code $characterCode",
                );
            }

            $characterId        = $characters[$characterCode];  
            // Определим тему письма
            $subjectText        = $row[2]; // C
            $subjectText        = StringTools::fixReAndFwd($subjectText);
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
            $mailCode           = ('' !== $mailCode) ? $mailCode : null;
            // Mail W/R
            $wr                 = $row[8]; // I
            // Mail constructor number
            $constructorNumber  = $row[9]; // J
            // Source of outbox email
            $source             = $row[10]; // K
            
            // определить код темы
            //$subjectModel = MailThemesModel::model()->byName($subject)->bySimIdNull()->find();
            /*if (null === $subjectModel) {
                $subjectModel = new MailThemesModel();
                $subjectModel->name = $subject;
                $subjectModel->insert();
            }*/
            
            $mailCharacterTheme = MailCharacterThemesModel::model()
                ->byLetterNumber($mailCode)
                ->byText($subjectText)
                ->byCharacter($characterId)
                ->find();
            
            if (null === $mailCharacterTheme) {
                $mailCharacterTheme = new MailCharacterThemesModel();
            }
            
            $mailCharacterTheme->text                   = $subjectText;
            $mailCharacterTheme->letter_number          = $mailCode;
            $mailCharacterTheme->character_id           = $characterId;
            $mailCharacterTheme->wr                     = $wr;
            $mailCharacterTheme->constructor_number     = $constructorNumber;
            $mailCharacterTheme->phone                  = $phone;
            $mailCharacterTheme->phone_wr               = $phoneWr;
            $mailCharacterTheme->phone_dialog_number    = $phoneDialogNumber;
            $mailCharacterTheme->mail                   = $mail;
            $mailCharacterTheme->source                 = $source;
            
            try {
                $mailCharacterTheme->save();
                $characterMailThemesIds[] = $mailCharacterTheme->id;
                // skip extra success messages
                /*$html .= sprintf(
                    'Succesfully imported - email from "%s", %s subject "%s" . [MySQL id: %s] <br/>',
                    $row[1],
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                    $row[2],
                    $mailCharacterTheme->id
                );*/
            } catch(CDbException $e) {
                $html .= sprintf(
                    'Error during import line %s. <br/> DB error message: %s <br/>',
                    $index,
                    $subjectText,
                    $e->getMessage()
                );
                
            } catch(Exception $e) {
                $html .= sprintf(
                    'Error during import line %s. Error message: %s <br/>',
                    $index,
                    $e->getMessage()
                );
                
            }
        }
        fclose($handle);
        
        // remove all old, unused characterMailThemes after import {
        $oldThemes = MailCharacterThemesModel::model()->byIdsNotIn(implode(',', $characterMailThemesIds))->findAll();
        foreach ($oldThemes as $oldTheme) {
            $oldTheme->delete();
        }
        // remove all old, unused characterMailThemes after import }
        
        $html .= "processed rows: ".($index-1)."  must be 113 (21 Dec 2012)<br/>";
        $html .= "Email from characters import finished! <br/>";
        
        return array(
            'status' => true,
            'text'   => $html,
        );        
    }
    
    public function importTasks() 
    {
        $fileName = '../media/xls/tasks.csv';
        
        $handle = $this->checkFileExists($fileName) ;
        $index = 0;

        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            
            if ($index == 1) continue;
              
            // Код
            $code       = $row[0]; // A
            // Тип старта задачи
            $startType  = $row[1]; // B
            // Список дел в to-do-list
            $name       = iconv("Windows-1251", "UTF-8", $row[2]); // C
            // Жесткая
            $startTime  = $row[3]; // D
            if ($startTime != '') {
                if (strstr($startTime, ':')) {
                    $timeData = explode(':', $startTime);
                    if (count($timeData) > 1) {
                      $startTime = $timeData[0]*60 + $timeData[1];
                    }
                }
            }
            
            // Категория
            $category   = $row[4];  // E
            // Мин.
            $duration   = $row[5];  // F
            
            $task = Tasks::model()->byCode($code)->find();
            if (!$task) {
                $task = new Tasks();
                $task->code = $code;
            }
            
            $task->title = $name;
            $task->start_time = $startTime;
            $task->duration = $duration;
            if ($startTime > 0) {
                $task->type = 2;
            } else {
                $task->type = 1;
            }
            $task->start_type = $startType;
            $task->category = $category;
            
            $task->save();
        }
        fclose($handle);
        
        return array(
            'status' => true,
            'text'   => sprintf('%s tasks have been imported.', $index),
        );  
    } 
    
    /**
     * Импорт задач для писем M-T
     */
    public function importMailTasks() 
    {
        $fileName = '../media/xls/mail_tasks.csv';
        
        $characters = array();
        $charactersList = Characters::model()->findAll();
        foreach($charactersList as $characterItem) {
            $characters[$characterItem->code] = $characterItem->id;
        }
        
        $connection=Yii::app()->db; 
        
        $handle = $this->checkFileExists($fileName);
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) continue;
            
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
            
            $model = MailTasksModel::model()->model()->byId($index-1)->find();
            if (!$model) {
                return array(
                    'status' => False,
                    'text'   => sprintf('Error on line %s, %s.', $index, $code),
                );  
            }
            $model->name        = $task;
            $model->duration    = $duration;
            $model->code        = $code;
            $model->wr          = $wr;
            $model->category    = $category;
            $model->save();
            
        }
        fclose($handle);
        
        return array(
            'status' => true,
            'text'   => sprintf('%s mail tasks have been imported.', $index),
        );  
    }
    
    /**
     * Импорт событий из писем
     */
    public function importMailEvents() 
    {
        $fileName = '../media/xls/mail2.csv';
        $handle = $this->checkFileExists($fileName);
        
        $events = EventService::getAllCodesList();
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
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
        }
        fclose($handle);
        
        return array(
            'status' => true,
            'text'   => sprintf('%s mail events have been imported.', $index),
        );    
    }
    
    public function importMailSendingTime() 
    {
        $fileName = '../media/xls/mail2.csv';
        $handle = $this->checkFileExists($fileName);
        
        $events = EventService::getAllCodesList();
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
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
        
        return array(
            'status' => true,
            'text'   => sprintf('%s mails have been updated.', $index),
        );   
    }
    
    public function importMailAttaches()
    {
        $fileName = '../media/xls/mail2.csv';
        $handle = $this->checkFileExists($fileName);
        
        $documents = MyDocumentsService::getAllCodes();
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 1) {
                continue;
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
                     }
                }
            }
            
        }
        fclose($handle);
        
        return array(
            'status' => true,
            'text'   => sprintf('%s mail attaches have been imported.', $index),
        );       
    }
    
    /**
     * Импорт документов
     */
    public function importMyDocuments() 
    {
        $fileName = '../media/xls/documents.csv';
        
        $handle = $this->checkFileExists($fileName);
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            if ($index > 29) {
                echo('all done'); die();
            }
            
            // Определим код документа
            $code       = $row[0]; // A
            // Определим тип документа
            $type       = $row[1]; // B
            // Имя файла в системе
            $fileName   = iconv("Windows-1251", "UTF-8", $row[2]); // C
            // Исходный файл
            $srcFile    = iconv("Windows-1251", "UTF-8", $row[3]); // D
            // Расширение файла
            $format     = $row[4]; // E
            
            //if ($type == '-') continue;
            
            $document = MyDocumentsTemplateModel::model()->byCode($code)->find();
            if (!$document) {
                $document = new MyDocumentsTemplateModel();
                $document->code         = $code;
            }
            
            $document->fileName     = $fileName.'.'.$format;
            $document->srcFile      = $srcFile;
            $document->format       = $format;
            $document->type         = $type;
            $document->save();
        }
        fclose($handle);
        
        return array(
            'status' => true,
            'text'   => sprintf('%s document records have been imported.', $index),
        );   
    }

    /**
     * Get unique import ID
     *
     * @return string
     */
    protected function getImportUUID()
    {
        return uniqid();
    }
    
    public function importActivityEfficiencyConditions()
    {
        $reader = $this->getReader();
        
        // load sheet {
        $reader->setLoadSheetsOnly('Activities');
        $excel = $reader->load($this->filename);
        $sheet = $excel->getSheetByName('Activities');
        // load sheet }
        
        $this->setColumnNumbersByNames($sheet);
        
        $importedRows = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            // try to find exists entity {
            $activityEfficiencyCondition = ActivityEfficiencyCondition::model()
                ->byActivityId($this->getCellValue($sheet, 'Activity_code', $i))
                ->byType($this->getCellValue($sheet, 'Result_type', $i))
                ->byResultCode($this->getCellValue($sheet, 'Result_code', $i))
                ->find();
            // try to find exists entity }
            
            // create entity if not exists {
            if (null === $activityEfficiencyCondition) {
                $activityEfficiencyCondition = new ActivityEfficiencyCondition();
                $activityEfficiencyCondition->activity_id = $this->getCellValue($sheet, 'Activity_code', $i);
                $activityEfficiencyCondition->type        = $this->getCellValue($sheet, 'Result_type', $i);
                $activityEfficiencyCondition->result_code = $this->getCellValue($sheet, 'Result_code', $i);
            }
            // create entity if not exists }
            
            // update data {
            $activityEfficiencyCondition->operation            = $this->getCellValue($sheet, 'Result_operation', $i);
            $activityEfficiencyCondition->efficiency_value     = $this->getCellValue($sheet, 'All_Result_value', $i);
            $activityEfficiencyCondition->fail_less_coeficient = $this->getCellValue($sheet, 'Fail_Less_Coef', $i);
            $activityEfficiencyCondition->import_id            = $this->import_id;
            // update data }
            
            // save
            $activityEfficiencyCondition->save();
            
            $importedRows++;
        }
        
        // delete old unused data {
        ActivityEfficiencyCondition::model()->deleteAll(
            'import_id<>:import_id', 
            array('import_id' => $this->import_id)
        );
        // delete old unused data }
        
        return array(
            'imported_activityEfficiencyConditions' => $importedRows, 
            'errors' => false, 
        );
    }
    
    /**
     * @return PHPExcel_Reader
     */
    private function getReader()
    {
        PHPExcel_Settings::setCacheStorageMethod($this->cache_method);

        return PHPExcel_IOFactory::createReader('Excel2007'); 
    }
    
    /**
     * @param PHPExcel_Sheet $sheet
     * @return void
     */
    public function setColumnNumbersByNames($sheet)
    {
        for ($i = 0; ; $i++) {
            $row_title = $sheet->getCellByColumnAndRow($i, 1)->getValue();
            if (null !== $row_title) {
                $this->columnNoByName[$row_title] = $i;
            } else {
                return;
            }
        }
    }
    
    private function getCellValue($sheet, $columnName, $i)
    {
        return $sheet->getCellByColumnAndRow(
            $this->columnNoByName[$columnName], 
            $i->key()
        )->getValue();
    }

    /**
     * @return array()
     */
    /**
     * Import activity
     *
     * @return array
     */
    public function importActivity()
    {
        $activity_types = array(
            'Documents_leg'   => 'document_id',
            'Manual_dial_leg' => 'dialog_id',
            'System_dial_leg' => 'dialog_id',
            'Inbox_leg'       => 'mail_id',
            'Outbox_leg'      => 'mail_id',
            'Window'          => 'window_id'
        );

        $reader = $this->getReader();

        // load sheet {
        $reader->setLoadSheetsOnly('Leg_actions');
        $excel = $reader->load($this->filename);
        $sheet = $excel->getSheetByName('Leg_actions');
        // load sheet }
        
        // save colums numbers by column titles 
        $this->setColumnNumbersByNames($sheet);
        
        $activities = array();
        $activity_actions = 0;
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            // get Leg_type value
            $leg_type = $sheet->getCellByColumnAndRow($this->columnNoByName['Leg_type'], $i->key())->getValue();
            
            // we haven`t skype agent jet
            if ($leg_type === 'Skype_leg') {
                continue;
            }

            // get Activity code
            $activityCode = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_code'], $i->key())->getValue();
            if ($activityCode == '') {
                break;
            }
            
            //try to find exest activity in DB
            $activity = Activity::model()->findByPk($activityCode);
            
            // create Activity 
            if ($activity === null) {
                $activity = new Activity();
                $activity->id = $activityCode;
            }
                
            // update activities counter
            $activities[$activity->id] = true;
            
            // update activity values {
            $activity->parent      = $sheet->getCellByColumnAndRow($this->columnNoByName['Parent'], $i->key())->getValue();
            $activity->grandparent = $sheet->getCellByColumnAndRow($this->columnNoByName['Grand parent'], $i->key())->getValue();
            $activity->name        = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_name'], $i->key())->getValue();
            $activity->numeric_id  = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_id'], $i->key())->getValue();
            $activity->type        = $sheet->getCellByColumnAndRow($this->columnNoByName['Activity_type'], $i->key())->getValue();
                        
            $category = $sheet->getCellByColumnAndRow($this->columnNoByName['Категория'], $i->key())->getValue();
            $activity->category_id = ($category === '-' ? null : $category);
            
            $activity->import_id   = $this->import_id;
            if (false === $activity->validate()) {
                return array('errors' => $activity->getErrors());
            }
            $activity->save();
            // update activity values }
            
            // 
            $type = $activity_types[$leg_type];
            $xls_act_value = $sheet->getCellByColumnAndRow($this->columnNoByName['Leg_action'], $i->key())->getValue();
            
            # Converting XLS codes to our
            if ($xls_act_value === '-') {
                $values = array();
            } else if ($type === 'dialog_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear jet
                    $values = Dialogs::model()->findAll();
                } else {
                    $values = array(Dialogs::model()->findByAttributes(array('code' => $xls_act_value)));
                }
            } else if ($type === 'mail_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear jet
                    $values = MailTemplateModel::model()->findAll();
                } else {
                    $values = array(MailTemplateModel::model()->findByAttributes(array('code' => $xls_act_value)));
                }
            } else if ($type === 'document_id') {
                if ($xls_act_value === 'all') {
                    // @todo: not clear jet
                    $values = MyDocumentsTemplateModel::model()->findAll();
                } else {
                    $values = array(MyDocumentsTemplateModel::model()->findByAttributes(array('code' => $xls_act_value)));
                }
            } else if ($type === 'window_id') {
                # TODO
                $values = array(Window::model()->findByAttributes(array('subtype' => $xls_act_value)));
            } else {
                return array('errors' => 'Can not handle type:' . $type);
            }
            
            // update relation Activiti to Document, Dialog replic ro Email {
            foreach ($values as $value) {
                $activityAction = ActivityAction::model()->findByAttributes(array(
                    'activity_id' => $activity->primaryKey,
                    $type => $value->id
                ));
                if ($activityAction === null) {
                    $activityAction = new ActivityAction();
                }
                $activityAction->import_id = $this->import_id;
                $activityAction->activity_id = $activity->id;
                $activityAction->is_keep_last_category = 
                    $sheet->getCellByColumnAndRow($this->columnNoByName['Keep last category'], $i->key())->getValue();
                $activityAction->$type = $value->id;
                if (!$activityAction->validate()) {
                    $this->errors = $activityAction->getErrors();
                    return array('errors' => $this->errors);
                }
                $activityAction->save();
            }
            // update relation Activity to Document, Dialog replic ro Email }
            
            $activity_actions ++;
        }
        
        // delete old unused data {
        ActivityAction::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));
        Activity::model()->deleteAll('import_id<>:import_id', array('import_id' => $this->import_id));
        // delete old unused data }
        
        return array(
            'activity_actions' => $activity_actions, 
            'errors' => false, 
            'activities' => count($activities)
        );
    }

    /**
     * Only must to use functions. Has correct import order
     */
    public function importAll() {
        $result = [];
        $dialog_import = new DialogImportService();
        $result['characters'] = $this->importCharacters();
//        $result['characters_points_titles'] = $this->importCharactersPointsTitles();
//        $result['emails'] = $this->importEmails();
//        $result['emails_subjects'] = $this->importEmailSubjects();
        #$result['mail_attaches'] = $this->importMailAttaches();
        #$result['mail_events'] = $this->importMailEvents();
        #$result['mail_sending_time'] = $this->importMailSendingTime();
        #$result['tasks'] = $this->importTasks();
        #$result['mail_tasks'] = $this->importMailTasks();
        #$result['my_documents'] = $this->importMyDocuments();
//        $result['activity'] = $this->importActivity();
//        $result['activity_efficiency_conditions'] = $this->importActivityEfficiencyConditions();
//        $result['dialog'] = $dialog_import->import();
        return $result;
    }

    /* ----- */

    /**
     * @param string $fileName, like 'media/xls/characters.csv'
     *
     * @throws Exception
     * @return file handler
     *
     * @throw Exception
     */
    private function checkFileExists($fileName) 
    {
        $handle = fopen($fileName, "r");
        if (!$handle) {
            throw new Exception("cant open $fileName");
        }
        
        return $handle;
    }
}

