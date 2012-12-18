<?php

/**
 * @author slavka
 */
class ImportGameDataService
{
    /**
     * @return mixed array
     * 
     * @throws Exception
     */
    public function importCaracters() 
    {
        $fileName = '../media/xls/characters.csv';
        
        $handle = $this->checkFileExists($fileName);
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index == 1) {
                continue;
            }

            $code  = $row[0];
            $title = iconv("Windows-1251", "UTF-8", $row[1]);
            $fio   = iconv("Windows-1251", "UTF-8", $row[2]);
            $email = $row[3];
            $skype = $row[4];
            $phone = $row[5];
            
            $model = Characters::model()->byCode($code)->find();
            if (!$model) {
                $model = new Characters();
            }
            
            // set walues {
            $model->code = $code;
            $model->title = $title;
            $model->fio = $fio;
            $model->email = $email;
            $model->skype = $skype;
            $model->phone = $phone;
            // set walues }
            
            // save {
            if (!$model) {
                $model->insert();
            }
            else {
                $model->update();
            }
            // save }
        }
        
        fclose($handle);
        
        return array(
            'status'  => true,
            'counter' => $index,
            'text'    => sprintf('%s records have been imported.', $index),
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
            Количество обработаных строк данных - %s по %s колонки <br>
            ',
            $filename,
            filesize($filename) / 1024,
            date("d.m.Y H:i:s.", filemtime($filename)),
            $count_str,
            $count_col
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
            $subject = StringTools::fixReAndFwd($subject);
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
            $subjectEntity = MailThemesModel::model()->byName($subject)->bySimIdNull()->find();
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
                    return array(
                        'status' => false,
                        'text'   => "cant find receiver by code $receiverCode",
                    );
                }
                $receiverId = $characters[$receiverCode];
                
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
                    return array(
                        'status' => false,
                        'text'   => "cant find chracter by code $characterCode",
                    );
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
        
        $html = sprintf(
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
        
        return array(
            'status' => true,
            'text'   => $html,
        );
    }
    
    public function importEmailSubjects()
    {
        $fileName = __DIR__.'/../../../media/xls/mail_themes.csv';
        
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
            $subject            = $row[2]; // C
            $subject            = StringTools::fixReAndFwd($subject);
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
            $subjectModel = MailThemesModel::model()->byName($subject)->bySimIdNull()->find();
            if (null === $subjectModel) {
                $subjectModel = new MailThemesModel();
                $subjectModel->name = $subject;
                $subjectModel->insert();
            }
            
            $mailCharacterTheme = MailCharacterThemesModel::model()
                ->byCharacter($characterId)
                ->byTheme($subjectModel->id)
                ->find();
            
            if (null === $mailCharacterTheme) {
                $mailCharacterTheme = new MailCharacterThemesModel();
                $mailCharacterTheme->character_id   = $characterId;
                $mailCharacterTheme->theme_id       = $subjectModel->id;
            }
            
            $mailCharacterTheme->letter_number          = $mailCode;
            $mailCharacterTheme->wr                     = $wr;
            $mailCharacterTheme->constructor_number     = $constructorNumber;
            $mailCharacterTheme->phone                  = $phone;
            $mailCharacterTheme->phone_wr               = $phoneWr;
            $mailCharacterTheme->phone_dialog_number    = $phoneDialogNumber;
            $mailCharacterTheme->mail                   = $mail;
            $mailCharacterTheme->source                  = $source;
            
            try {
                $mailCharacterTheme->save();            
                $html .= sprintf(
                    'Succesfully imported - email from "%s", %s subject "%s" . [MySQL id: %s] <br/>',
                    $row[1],
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
                    $row[2],
                    $mailCharacterTheme->id
                );
            } catch(CDbException $e) {
                $html .= sprintf(
                    'Error during import line %s. <br/> subject: %s, id: %s<br/> DB error message: %s <br/>',
                    $index,
                    $subject,
                    $subjectModel->id,
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
        
        $html .= "processed rows: $index <br/>";
        $html .= "Email from characters import finished! <br/>";
        
        return array(
            'status' => true,
            'text'   => $html,
        );        
    }


    /* ----- */
    
    /**
     * @param string $fileName, 'media/xls/characters.csv'
     * 
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

