<?php

set_time_limit(0);

/**
 * Сервис импорта диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogImportService {
    
    protected $_columns = array(
        'A' => 0,
        'B' => 1,
        'C' => 2,
        'D' => 3,
        'E' => 4,
        'F' => 5,
        'G' => 6,
        'H' => 7,
        'I' => 8,
        'J' => 9,
        'K' => 10,
        'L' => 11,
        'M' => 12,
        'N' => 13,
        'O' => 14
    );
    
    protected $_characters = array();
    
    protected $_charactersStates = array();
    
    protected $_dialogSubtypes = array();
    
    protected $_charactersPoints = array();
    
    protected function _convert($str) {
        $str = str_replace(chr(hexdec('A0')), " ", $str);
        
        $str = preg_replace('/\s\s+/', ' ', $str);
         
        
        /*$str = str_replace("&nbsp;", "", $str);
        $str = str_replace("\t", "", $str);
        $str = str_replace("  ", "", $str);*/
        
        
        $str = iconv("Windows-1251", "UTF-8", $str);
        $str = trim($str);
        
        //echo($str.'<br/>');
        /*$str = str_replace("&nbsp;", "", $str);
        $str = str_replace("\t", "", $str);
        $str = str_replace("  ", "", $str);
        */
        //$str = preg_replace("/^(\-\s+)/", "- ", $str);
        
        //echo($str.'<br/>');
        
        return $str;
    }
    
    /**
     * Загрузка всех персонажей
     */
    protected function getCharacters() {
        $characters = Characters::model()->findAll();
        
        $list = array();
        foreach($characters as $character) {
            $list[$character->title] = $character->id;
        }
        
        return $list;
    }
    
    protected function getDialogSubtypes() {
        $subtypes = DialogSubtypes::model()->findAll();
        
        $list = array();
        foreach($subtypes as $subtype) {
            $list[$subtype->title] = $subtype->id;
        }
        
        return $list;
    }
    
    
    
    protected function _getCharacterIdByName($characterName) {
        if (isset($this->_characters[$characterName])) {
            return $this->_characters[$characterName];
        }
        //var_dump($characterName);
        return null;
    }

    protected function _getCharacterStateIdByName($name) {
        if (isset($this->_charactersStates[$name])) {
            return $this->_charactersStates[$name];
        }
        //var_dump($characterName);
        return null;
    }
    
    protected function _getDialogSubtypeIdByName($name) {
        if (isset($this->_dialogSubtypes[$name])) {
            return $this->_dialogSubtypes[$name];
        }
        //var_dump($characterName);
        return null;
    }

    protected function getCharactersStates() {
        $charactersStates = CharactersStates::model()->findAll();
        
        $list = array();
        foreach($charactersStates as $character) {
            $list[$character->title] = $character->id;
        }
        
        return $list;
    }
    
    protected function getCharactersPoints() {
        $charactersPoints = CharactersPointsTitles::model()->findAll();
        
        $list = array();
        foreach($charactersPoints as $point) {
            $list[$point->code] = $point->id;
        }
        
        return $list;
    }
    
    protected function _timeToInt($time) {
        $arr = explode(':', $time);
        if (count($arr)>1) {
            return mktime($arr[0], $arr[1]);
        }    
        return 0;
    }
    
    protected function _cleanData() {
        $connection=Yii::app()->db;   

        $sql = 'DELETE FROM `dialogs`';
        $command = $connection->createCommand($sql);
        $command->execute();
        
        $sql = 'DELETE FROM `events_samples`';
        $command = $connection->createCommand($sql);
        $command->execute();
        
        $sql = 'ALTER TABLE `dialogs` AUTO_INCREMENT =1';
        $command = $connection->createCommand($sql);
        $command->execute();

        $sql = 'ALTER TABLE `events_samples` AUTO_INCREMENT =1';
        $command = $connection->createCommand($sql);
        $command->execute();
    }
    
    /**
     * Импорт Диалогов
     * @param type $fileName
     * @return int 
     */
    public function import($fileName) {
        $this->_characters = $this->getCharacters();
        $this->_charactersStates = $this->getCharactersStates();
        $this->_dialogSubtypes = $this->getDialogSubtypes();
        $this->_charactersPoints = $this->getCharactersPoints();
        
        
        // clean
        $connection=Yii::app()->db;   

        $this->_cleanData();

        
        Logger::debug("started");
        
        //$fileName = "media/data.csv";
        
        //$arrLines = file($fileName);
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        
        $index = 1;
        $columns = array();
        $delays = array();
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
        /*foreach($arrLines as $line) {    
            $row = explode(';', $line);*/
            
            if ($index == 1) {
                $index++;
                continue;
            }
            
            if ($index == 2) {
                // загрузим кодов
                $columnIndex = 19;
                while(isset($row[$columnIndex]) && $row[$columnIndex] != '') {
                    $pointsCodes[$columnIndex] = $row[$columnIndex];
                    $columnIndex++;
                }
                
                $index++;
                continue;
            }
            
            //var_dump($row);die();
            
            $index++;
            
            
            /*if (!isset($row[11])) {
                echo($line);
                var_dump($row); die();
            }*/
            
            if (!isset($row[1])) {
                //var_dump($row); die();
                continue;
            }
            
            $column = array(
                'A' => $row[0],
                'B' => $row[1],
                'C' => $row[2],
                'D' => $row[3],
                'E' => $row[4],
                'F' => $row[5],
                'G' => $row[6],
                'H' => $row[7],
                'I' => $row[8],
                'J' => $row[9],
                'K' => $row[10],
                'L' => $row[11],
                'M' => $row[12],
                'N' => $row[13],
                'O' => $row[14],
                'P' => $row[15],
                'Q' => $row[16],
                'R' => $row[17],
                'S' => $row[18]
            );
            
            if ($row[0] != '')
            if (!isset($delays[$row[0]])) {
                
                $delays[$row[0]] = $row[2];
            }
            
            // загружаем кода
            $codeIndex = 19;
            while(isset($row[$codeIndex])) {
                $column[$codeIndex] = $row[$codeIndex];
                $codeIndex++;
            }
            
            //if ($index > 2) 
            
            $columns[] = $column;
        }
        fclose($handle);
        
        /*echo('<hr/>');
        var_dump($columns); die();*/
        
        #######################################################
        // импорт
        $processed = 0;
        
        // В начале импортируем события
        foreach($columns as $index=>$row) {
            $code = $this->_convert($row['B']);
            if ($code == '-' || $code == '') continue;
            
            // Проверяем, а нету ли уже такое события
            if (!EventsSamples::model()->byCode($code)->find()) {
                // Создаем событие
                $event = new EventsSamples();
                $event->code = $code;
                $event->title = $this->_convert($row['C']);
                $event->on_ignore_result = 0;
                $event->on_hold_logic = 1;
                $event->insert();
                $processed++;
            }
        }
        
        // Это временный код - его задача создать события типа - М9 М10, D3, P3, T (без номера)
        foreach($columns as $index=>$row) {
            $code = $this->_convert($row['M']);
            if ($code == '-' || $code == '') continue;
            if (!EventsSamples::model()->byCode($code)->find()) {
                // Создаем событие
                $event = new EventsSamples();
                $event->code = $code;
                $event->title = "запуск события {$code}";
                $event->on_ignore_result = 0;
                $event->on_hold_logic = 1;
                $event->insert();
                $processed++;
            }
        }
        ///////////////////////////////
        
        
        // теперь импортируем диалоги
        foreach($columns as $index=>$row) {
            
            // Создаем диалог
            $dialog = new Dialogs();
            $characterName = $this->_convert($row['F']);
            $chFrom = (int)$this->_getCharacterIdByName($characterName);
            if ($chFrom == 0)                continue;
            
            $dialog->ch_from = $chFrom;
            Logger::debug("ch_name=".$row['F']);
            //Logger::debug("ch_from=".$dialog->ch_from);

            $characterState = $this->_convert($row['G']);
            //if ($characterState == '') $characterState = 'уравновешенное';
            
            $dialog->ch_from_state = $this->_getCharacterStateIdByName($characterState);
            if ($dialog->ch_from_state == null) {
                $dialog->ch_from_state = 1; //continue;
            }

            //$row['G'] = strtolower($row['G']);
            $characterToId = (int)$this->_getCharacterIdByName($this->_convert($row['H']));
            if ($characterToId == 0) {
                $characterToId = null;
                //echo("character ".$row['H']); die();
            }
            if ($characterToId > 0)
                $dialog->ch_to = $characterToId;
            
            $dialog->ch_to_state = $this->_getCharacterStateIdByName($this->_convert($row['I']));

            if ($dialog->ch_to_state == null) {
                $dialog->ch_to_state = 1;
                //echo($row['H']);
                //continue;
            }

            if (!isset($row['R'])) {
                var_dump($row); die();
            }
            
            $dialogSubtype = (int)$this->_getDialogSubtypeIdByName($this->_convert($row['R']));
            if ($dialogSubtype == 0) {
                var_dump($this->_convert($row['R'])); die();
            }
            
            $dialog->dialog_subtype = $dialogSubtype;

            $dialog->text = $this->_convert($row['L']);
            $dialog->duration = $row['E'];
            $dialog->event_result = 0;
            $dialog->code = $this->_convert($row['B']);
            $dialog->step_number = $row['J'];
            $dialog->replica_number = $row['K'];       

            $event = EventsSamples::model()->byCode($this->_convert($row['N']))->find();
            if ($event)
                $dialog->next_event = $event->id;
            else 
                $dialog->next_event = null;


            $delay = $this->_timeToInt($row['D']);
            //echo($row['A']);
            //echo('start : '.$delay);
            if ($delay > 0) {
                if (isset($delays[$row['N']])) {
                    //echo('end '.$delays[$row['M']]);
                    $delay2 = $this->_timeToInt($delays[$row['N']]);

                    //echo("$delay - $delay2");
                    $delay = abs($delay - $delay2);
                }
                else {
                    $delay = 0;
                }
            }
            //var_dump($delay);


            $dialog->delay = $delay;       
            $dialog->sound = $row['O'];       
            $dialog->insert();       
            
            
            // теперь загрузим оценки
            foreach($pointsCodes as $pointIndex => $pointCode) {
                Logger::debug("check code : $pointCode");
                Logger::debug("value is : ".$row[$pointIndex]);
                if ($row[$pointIndex] != '') {
                    // сделать вставку в characters_points
                    // если есть point с таким кодом
                    if (isset($this->_charactersPoints[$pointCode])) {
                        $pointId = $this->_charactersPoints[$pointCode];
                        Logger::debug("found point : ".$pointId);
                        
                        $charactersPoints = new CharactersPoints();
                        $charactersPoints->dialog_id = $dialog->id;
                        $charactersPoints->point_id = $pointId;
                        $charactersPoints->add_value = $row[$pointIndex];
                        $charactersPoints->insert();
                    }
                    
                }
            }
            
            $processed++;
        }
            
            
        
        
        //var_dump($columns);
        
        
        return $processed;
    }
}

?>
