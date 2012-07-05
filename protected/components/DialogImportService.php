<?php



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
    
    protected function _convert($str) {
        return iconv("Windows-1251", "UTF-8", $str);
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
    
    protected function _timeToInt($time) {
        $arr = explode(':', $time);
        if (count($arr)>1) {
            return mktime($arr[0], $arr[1]);
        }    
        return 0;
    }
    
    public function import($fileName) {
        $this->_characters = $this->getCharacters();
        $this->_charactersStates = $this->getCharactersStates();
        $this->_dialogSubtypes = $this->getDialogSubtypes();
        
        
        //$fileName = "media/data.csv";
        $handle = fopen($fileName, "r");
        if (!$handle) throw new Exception("cant open $fileName");
        
        $index = 1;
        $columns = array();
        $delays = array();
        while (($row = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $index++;
            if ($index < 6) continue;
            
            $columns[] = array(
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
                'O' => $row[14]
            );
            
            if ($row[0] != '')
            if (!isset($delays[$row[0]])) {
                
                $delays[$row[0]] = $row[2];
            }
        }
        fclose($handle);
        
        #######################################################
        // импорт
        $processed = 0;
        
        // В начале импортируем события
        foreach($columns as $index=>$row) {
            if (($row['I'] == 1) && ($row['J'] == 0)) {
               
                // Создаем событие
                $event = new EventsSamples();
                $event->code = $this->_convert($row['A']);
                $event->title = $this->_convert($row['B']);
                $event->on_ignore_result = 0;
                $event->on_hold_logic = 1;
                $event->insert();
                $processed++;
            }
        }
        
        // теперь импортируем диалоги
        foreach($columns as $index=>$row) {
            
            // Создаем диалог
            $dialog = new Dialogs();
            $characterName = $this->_convert($row['E']);
            $dialog->ch_from = $this->_getCharacterIdByName($characterName);


            $characterState = $this->_convert($row['F']);
            $dialog->ch_from_state = $this->_getCharacterStateIdByName($characterState);
            if ($dialog->ch_from_state == null) {
                continue;
            }

            $dialog->ch_to = $this->_getCharacterIdByName($this->_convert($row['G']));
            $dialog->ch_to_state = $this->_getCharacterStateIdByName($this->_convert($row['H']));

            if ($dialog->ch_to_state == null) {
                //echo($row['H']);
                continue;
            }

            $dialog->dialog_subtype = $this->_getDialogSubtypeIdByName($this->_convert($row['O']));

            $dialog->text = $this->_convert($row['K']);
            $dialog->duration = $row['D'];
            $dialog->event_result = 0;
            $dialog->code = $this->_convert($row['A']);
            $dialog->step_number = $row['I'];
            $dialog->replica_number = $row['J'];       

            $event = EventsSamples::model()->byCode($this->_convert($row['M']))->find();
            if ($event)
                $dialog->next_event = $event->id;
            else 
                $dialog->next_event = null;


            $delay = $this->_timeToInt($row['C']);
            //echo($row['A']);
            //echo('start : '.$delay);
            if ($delay > 0) {
                if (isset($delays[$row['M']])) {
                    //echo('end '.$delays[$row['M']]);
                    $delay2 = $this->_timeToInt($delays[$row['M']]);

                    //echo("$delay - $delay2");
                    $delay = abs($delay - $delay2);
                }
                else {
                    $delay = 0;
                }
            }
            //var_dump($delay);


            $dialog->delay = $delay;       
            $dialog->insert();       
            $processed++;
        }
            
            
        
        
        //var_dump($columns);
        
        
        return $processed;
    }
}

?>
