<?php

set_time_limit(0);

/**
 * Сервис импорта диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogImportService {
    
        
    protected $_characters = array();    
    protected $_charactersStates = array();    
    protected $_dialogSubtypes = array();    
    protected $_charactersPoints = array();
    protected $columnAlias = array();
    
    protected $countZeros = 0;
    protected $countOnes = 0; 
    protected $countMarks = 0; 
    protected $processedEvents = 0;
    protected $processedEventNew = 0;
    protected $processedEventUpdated = 0;
    
    protected $alreadyImportedEvents = array();
    protected $eventT = null; // event with code 'T'
    protected $lines = array();
    protected $markCodes = array();
    protected $dialogs = array(); // indexed by excel_id
    protected $importedEvents = array();
    protected $flagNames = array();
    protected $updatedDialogs = 0;
    protected $updatedFlagRules = 0;
    protected $updatedFlagRuleValues = 0;
    
    protected $importedEventsIds = array();
    protected $importedDialogsIds = array();
    protected $importedCharacterPointsIds = array();

    public function __construct() 
    {
        $this->_characters          = $this->getCharacters();
        $this->_charactersStates    = $this->getCharactersStates();
        $this->_dialogSubtypes      = $this->getDialogSubtypes();
        $this->_charactersPoints    = $this->getCharactersPoints(); 
        $this->_columnAlias = array(
            'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7,
            'I' => 8, 'J' => 9, 'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14,
            'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 
        );
        $this->eventT = EventsSamples::model()->byCode('T')->find();
    }

    private function resetCounters()
    {
        $this->countOnes  = 0;
        $this->countZeros = 0;
        $this->countMarks = 0;
        $this->processedEvents = 0;
        $this->processedEventNew = 0;
        $this->processedEventUpdated = 0;
        
        $this->alreadyImportedEvents = array();
    }

    /**
     * Just clean-up Excel cell value
     * 
     * @param string $str
     * @return string
     */
    protected function _convert($str) 
    {
        $str = trim($str);
        
        return $str;
    }
    
    /**
     * Загрузка всех персонажей
     * 
     * @return array of Character
     */
    protected function getCharacters() 
    {
        $characters = Characters::model()->findAll();
        
        $list = array();
        foreach($characters as $character) {
            if ($character->code != '')
                $list[$character->code] = $character->id;
        }
        
        return $list;
    }
    
    /**
     * @return array of DialogSubtype
     */
    protected function getDialogSubtypes()
    {      
        $subtypes = DialogSubtypes::model()->findAll();
        
        $list = array();
        foreach($subtypes as $subtype) {
            $list[$subtype->title] = $subtype->id;
        }
        
        return $list;
    }
    
    protected function _getCharacterIdByName($characterName) {
        if (isset($this->_characters[$characterName])) {
            return (int)$this->_characters[$characterName];
        }
        return null;
    }

    protected function _getCharacterStateIdByName($name) {
        if (isset($this->_charactersStates[$name])) {
            return (int)$this->_charactersStates[$name];
        }
        return 1; // todo: investigate why 1 instead of NULL
    }
    
    protected function _getDialogSubtypeIdByName($name) {
        if (isset($this->_dialogSubtypes[$name])) {
            return (int)$this->_dialogSubtypes[$name];
        }
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
    
    /**
     * 
     * @param string $time
     * @return integer
     */
    private function timeToInt($time) 
    {
        if (strstr($time, ':')) {
            $explodedTime = explode(':', $time);
            if (isset($explodedTime[1])) {
                return $explodedTime[0] * 60 + $explodedTime[1];
            } else {
                return (int)$time;
            }                    
        }
    }
    
    private function importEventsFromLines()
    {
        // remove all events, exept exists in our import file {
        $keys = array();
        foreach($this->lines as $row) {
            $keys[] = $this->_convert($row['C']);
        }      
        
        // also T, D, P and M events willn`t be removed.
        // @todo: update inport to import Documents, PlanerEvents, eMails 
        // and check is T(terminator) task exist
        $isSimpleEvent = isset($event->code[0]) && false === in_array($event->code[0], array('T', 'E', 'D', 'P'));
        
        foreach(EventsSamples::model()->findAll() as $event) {
            if ($isSimpleEvent) {
                $event->delete();
            }
        }         
        // remove all events, exept exists in our import file }
        
        foreach($this->lines as $lineNo => $row) 
        {
            $code = $row['C'];
            
            $isFirstRecordAboutEvent = (
                $code != '-' &&                                          // code
                $code != '' &&                                           // code
                (int)$row['K'] == 1 &&                                   // step_number, 1 - first step in dialog
                (int)$row['L'] == 0 &&                                   // replica_number, 0 - first replica in dialog
                false === in_array($code, $this->alreadyImportedEvents)  // processed at first during current import
            );
            
            if (false === $isFirstRecordAboutEvent) {
                continue; // ignore this line
            }
            
            $this->importedEvents[] = $code;
            
            // Проверяем, а нету ли уже такого события
            $event = EventsSamples::model()->byCode($code)->find();
            if (!$event) {
                $event = new EventsSamples(); // Создаем событие
                $event->code = $code;
                $this->processedEventNew++;
            } else {
                $this->processedEventUpdated++;
            }
            
            $event->title = $this->_convert($row['D']);
            $event->on_ignore_result = 7; // ничего
            $event->on_hold_logic = 1; // ничего
            $event->trigger_time = $this->timeToInt($row['E']);
            
            $this->wrappedSave($event, $row, $lineNo);
            
            $this->importedEventsIds[] = $event->id;
            
            $this->processedEvents++;
            
            // events with wisitors has two items with step_number == 1 && $replica_number == 0
            // but all rows in excel sorted in right (for dialogs phrases)  way
            // so we can store event id in array, and check has it been alredy imported or not
            $this->alreadyImportedEvents[] = $code;         
        } 
    }

    /**
     * @param string $code
     * 
     * @return integer
     */
    private function getNextEventId($code)
    {
        $event = EventsSamples::model()->byCode($code)->find();
        if (null === $event) {
            return ('0' === $code || '-' === $code) ? null : $this->eventT->id;
        } else {
            return $event->id;
        }
    }
    
    private function importDialogsFromLines()
    {
        foreach($this->lines as $lineNo => $row) {
            $isDifferent = false; // use to choose save dialog or not
            $code = $row['C'];
            $excelId = (int)$row['A'];  
            
            $dialog = Dialogs::model()->byExcelId($excelId)->find();
            if (null === $dialog) {
                // Создаем диалог
                $dialog = new Dialogs();
                $dialog->excel_id = $excelId;
                $isDifferent = true;
            }
            
            if ($dialog->code != $code) {
                $isDifferent = true;
                $dialog->code = $code;
            }
            
            if ($dialog->excel_id != $excelId) {
                $isDifferent = true;
                $dialog->excel_id = $excelId;
            }
            

            if ($dialog->event_result != 7) {
                $isDifferent = true;
                $dialog->event_result = 7; // ничего
            }
            
            $characterFromId = $this->_getCharacterIdByName($row['G']);
            if ($dialog->ch_from != $characterFromId) {
                $isDifferent = true;
                $dialog->ch_from = $characterFromId;
            }
            
            $characterFromState = $this->_getCharacterStateIdByName($row['H']);
            if ($dialog->ch_from_state != $characterFromState) {
                $isDifferent = true;
                $dialog->ch_from_state = $characterFromState;
            }
            
            $characterToId = $this->_getCharacterIdByName($row['I']);
            if ($dialog->ch_to != $characterToId) {
                $isDifferent = true;
                $dialog->ch_to = $characterToId;
            }
            
            $characterToState = $this->_getCharacterStateIdByName($row['J']);
            if ($dialog->ch_to_state != $characterToState) {
                $isDifferent = true;
                $dialog->ch_to_state = $characterToState;
            }
            
            $dialogSubtypeId = $this->_getDialogSubtypeIdByName($row['S']);
            if ($dialog->dialog_subtype != $dialogSubtypeId) {
                $isDifferent = true;
                $dialog->dialog_subtype = $dialogSubtypeId;            
            }
            
            $nextEventId = $this->getNextEventId($row['O']);
            if ($dialog->next_event != $nextEventId) {
                $isDifferent = true;
                $dialog->next_event = $nextEventId;
            }
            
            $nextEventCode = ('0' === $row['O']) ? '-' : $row['O'];
            if ($dialog->next_event_code != $nextEventCode) {
                $isDifferent = true;
                $dialog->next_event_code = $nextEventCode;
            }
            
            if ($dialog->text != $row['M']) {
                $isDifferent = true;
                $dialog->text = $row['M'];
            }
            
            if ($dialog->duration = $row['F']) {
                $isDifferent = true;
                $dialog->duration = $row['F']; // Определим длительность  
            }
            
            if ($dialog->step_number != $row['K']) {
                $isDifferent = true;
                $dialog->step_number = $row['K']; // Номер шага
            }
            
            if ($dialog->replica_number != $row['L']) {
                $isDifferent = true;
                $dialog->replica_number = $row['L'];    // Номер реплики  
            }
            
            if ($dialog->delay != $row['F']) {
                $isDifferent = true;
                $dialog->delay = $row['F'];
            }
            
            if ($dialog->flag != $row['T']) {
                $isDifferent = true;
                $dialog->flag = $row['T']; 
            }
            
            $isUseInDemo = ('да' == $row['EJ']) ? 1 : 0;
            if ($dialog->demo != $isUseInDemo) {
                $isDifferent = true;
                $dialog->demo = $isUseInDemo; 
            }
            
            if ($dialog->type_of_init != $row['EI']) {
                $isDifferent = true;
                $dialog->type_of_init = $row['EI']; 
            }
            
            $soundFile = ($row['P'] == 'N/A' || $row['P'] == '-') ? $file = '' : $row['P'];
            if ($dialog->sound != $soundFile) {
                $isDifferent = true;
                $dialog->sound = ($row['P'] == 'N/A' || $row['P'] == '-') ? $file = '' : $row['P'];       
            }
                   
            if ($isDifferent) {
                $this->wrappedSave($dialog, $row, $lineNo);
                $this->updatedDialogs++;
            }
            
            $this->importedDialogsIds[] = $dialog->id;
            
            $this->dialogs[$excelId] = $dialog;
        }    
    }
    
    private function importMarksFromLines()
    {
        foreach($this->lines as $lineNo => $row) {
            foreach($this->markCodes as $key => $code) {
                $exceld = (int)$row['A'];
                if ('' != $row[$key] && isset($this->dialogs[$exceld])) {
                    $dialogId = $this->dialogs[$exceld]->id;
                    // сделать вставку в characters_points
                    // если есть point с таким кодом
                    if (isset($this->_charactersPoints[$code])) {
                        $markId = $this->_charactersPoints[$code];
                        
                        // проверим а вдруг есть такая оценка
                        $charactersPoints = CharactersPoints::model()->byDialog($dialogId)->byPoint($markId)->find();
                        if (null === $charactersPoints) {
                            $charactersPoints = new CharactersPoints();
                            $charactersPoints->dialog_id = $dialogId;
                            $charactersPoints->point_id = $markId;
                        }
                        $charactersPoints->add_value = $row[$key];
                        
                        $this->wrappedSave($charactersPoints, $row, $lineNo);
                        
                        $this->importedCharacterPointsIds[] = $charactersPoints->id;
                        
                        if (1 == $charactersPoints->add_value) {
                            $this->countOnes++;
                        } else {
                            $this->countZeros++;
                        }
                        $this->countMarks++;
                    }
                    
                }
            }
        }    
    }

    /**
     * Импорт Диалогов
     * @param type $fileName
     * @return int 
     */
    public function import()
    {
        $fileName = 'media/ALL_DIALOGUES.csv';
        $this->resetCounters();
        
        try {
            $this->getRowsFromCsv(__DIR__."/../../".$fileName);
            $this->getMarkCodesFromCsv(__DIR__."/../../".$fileName);
        } catch (Exception $e) {
            return false;
        }
        
        $this->importEventsFromLines(); // импортируем события        
        $this->importDialogsFromLines(); // импортируем диалоги
        $this->importMarksFromLines(); // загрузим оценки
        $this->importReplica($fileName);
        
        $events = EventsSamples::model()
            ->byIdsNotIn(implode(',', $this->importedEventsIds))
            ->likeCode('D%')
            ->likeCode('P%')
            ->likeCode('M%')
            ->likeCode('T')
            ->findAll();
        foreach ($events as $event) {
            $event->delete();
        }
        foreach (CharactersPoints::model()->byIdsNotIn(implode(',', $this->importedCharacterPointsIds))->findAll() as $point) {
            $point->delete();
        }
        foreach (Dialogs::model()->byIdsNotIn(implode(',', $this->importedDialogsIds))->findAll() as $dialog) {
            $dialog->delete();
        }
        
        return array(
            'must_be_values_actual_date' => '21 Dec 2012',
            'replics'                    => count($this->lines),
            'events'                     => $this->processedEvents,
            'events-new'                 => $this->processedEventNew,
            'events-updated'             => $this->processedEventUpdated,
            'ones'                       => $this->countOnes,
            'zeros'                      => $this->countZeros,
            'marks'                      => $this->countMarks,
            'pointCodes'                 => count($this->markCodes),
            'updatedDialogs'             => $this->updatedDialogs,
        );
    }
    
    /**
     * Used to display detailed error with line number
     * 
     * @param mixedObject $entity
     * @param array of strings $row
     * @param integer $lineNo
     * 
     * @throws Exception
     */
    protected function wrappedSave($entity, $row, $lineNo) 
    {
        try {
            $entity->save();
        } catch (Exception $e) {
            throw new Exception(sprintf(
                'Строка: %s. Ошибка: %s. Данные из строки: %s.',
                $lineNo,
                $e->getMessage(),
                str_replace(array('"',"'"), array('\"', "\'"), implode(', ', $row))
            ));
        }
    }


    /**
     * Explode Excel file to colums
     * 
     * @param string $fileName
     * 
     * @return array
     * 
     * @throws Exception
     */
    private function getRowsFromCsv($fileName)
    {
        $this->lines = array();
        
        $handle = fopen($fileName, "r");
        
        if (!$handle) {
            throw new Exception(sprintf(
                "Не могу открыть файл %s.",
                $fileName
            ));
        }
        
        fgetcsv($handle, 5000, ";"); // skip first line
        fgetcsv($handle, 5000, ";"); // skip second line

        while (FALSE !== ($row = fgetcsv($handle, 5000, ";")) ) 
        {            
            if (!isset($row[1])) {
                continue;
            }
            $this->lines[] = $this->makeNamedRowData($row);
        }
        
        fclose($handle);
        
        return true;
    }
    
    /**
     * @param string $fileName
     */
    private function getMarkCodesFromCsv($fileName)
    {
        $this->markCodes = array();
        
        $handle = fopen($fileName, "r");        
        if (!$handle) {
            throw new Exception(sprintf(
                "Не могу открыть файл %s.",
                $fileName
            ));
        }
        
        $i = 0;      
        
        $row = fgetcsv($handle, 5000, ";");
        while (FALSE !== ($row = fgetcsv($handle, 5000, ";"))) 
        {            
            $formatedRow = $this->makeNamedRowData($row);
            foreach ($formatedRow as $key => $cell) {
                $i++;
                if (23 < $i && $i < 138) { // 23 : 'W', 138: 'EH'
                    if (null == $cell) {
                        fclose($handle);
                        return true;
                    } else {
                        $this->markCodes[$key] = $cell;
                    }
                }
            }
            
            fclose($handle);         
            return true; 
        }  
    }
    
    /*
     * Just change indexes from numbers to letters, for easy usage
     * 
     * @param array of string $row
     * 
     * @return array of string
     */
    private function makeNamedRowData($row)
    {
        $abc = array('','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $abc2 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $i = 0;
        
        $result = array();
        
        // mport all cells in row from 'A' to 'XX', if there is no item ir row -> end.
        foreach ($abc as $firstSign) {
            foreach ($abc2 as $secondSign) {
                $excelName = $firstSign.$secondSign;
                    if (isset($row[$i])) {
                        $result[$excelName] = $this->_convert($row[$i]);
                    } else {
                        return $result;
                    }
                $i++;
            }
        }
        
        if (false === isset($result['EJ'])) {
            throw new Exception("There is no 'is use in demo' column in .csv file. Line ".($i+2));
        }
        
        return $result;
    }


    public function importEvents($fileName) {
        $handle = fopen($fileName, "r");
        if (!$handle) return false;
        
        $index = 0;
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            $eventCode = $row[$this->_columnAlias['O']];
            if ($eventCode == '-') continue;
            
            // определим event time
            $eventTimeStr = $row[$this->_columnAlias['E']];

            $eventTime = 0;
            if (strstr($eventTimeStr, ':')) {
                $eventTimeData = explode(':', $eventTimeStr);
                if (isset($eventTimeData[1])) {
                    $eventTime = $eventTimeData[0] * 60 + $eventTimeData[1];
                }
            }            
            // проверим а есть ли такое событие
            $event = EventsSamples::model()->byCode($eventCode)->find();
            if (!$event) {
                $event = new EventsSamples();
                $event->code = $eventCode;
                $event->trigger_time = $eventTime;
                $event->on_ignore_result = 0;
                $event->on_hold_logic = 1;
                $event->insert();
                
                //echo("insert event $eventCode <br/>");
            }
            
            
        }
        fclose($handle);
    }
    
    public function importReplica($fileName) {
        $handle = fopen($fileName, "r");
        if (!$handle) return false;
        
        $index = 0;
        $columns = array();
        $delays = array();
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            $isFinalReplica = $this->_convert($row[$this->_columnAlias['N']]);
            if ($isFinalReplica == 'да') {
                $excelId = $row[$this->_columnAlias['A']];
                $dialog = Dialogs::model()->byExcelId($excelId)->find();
                if ($dialog) {
                    $dialog->is_final_replica = 1;
                    $dialog->save();
                }
            }
        }
        fclose($handle);
    }
    
    public function importText($fileName) {
        $handle = fopen($fileName, "r");
        if (!$handle) return false;
        
        $index = 0;
        $columns = array();
        $delays = array();
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            $text = $this->_convert($row[$this->_columnAlias['M']]);
            $nextEventCode = $row[$this->_columnAlias['O']];
            $excelId = $row[$this->_columnAlias['A']];
            $dialog = Dialogs::model()->byExcelId($excelId)->find();
            if ($dialog) {
                $dialog->text = $text;
                $dialog->next_event_code = $nextEventCode;
                $dialog->save();
            }
            
        }
        fclose($handle);
    }
    
    public function importFlagRules($fileName) {
        $transaction=FlagsRulesContentModel::model()->dbConnection->beginTransaction();
        try {
            $this->resetCounters();

            $handle = fopen($fileName, "r");
            if (!$handle) return false;

            foreach(FlagsRulesContentModel::model()->findAll() as $flagContent) {
                $flagContent->delete();
            }

            foreach(FlagsRulesModel::model()->findAll() as $flag) {
                $flag->delete();
            }

            $lineNo = 1;
            while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
                if (1 === $lineNo) {
                    for ($i = 4; $i < 19; $i++) {
                        $this->flagNames[$i] = $row[$i];
                    }
                    $lineNo++;
                    continue;
                }

                $recordId = (0 !== (int)$row[0]) ? (int)$row[0] : null;

                $flagRule = FlagsRulesModel::model()
                    ->byName($row[1])
                    ->byStepNumber((int)$row[2])
                    ->byReplicaNumber((int)$row[3])
                    ->byRecordIdOrNull($recordId )
                    ->find();
                if (null === $flagRule) {
                    $flagRule = new FlagsRulesModel();
                    $flagRule->setRecordId($recordId)
                        ->setStepNo($row[2])
                        ->setReplicaNo($row[3])
                        ->setEventCode($row[1]);
                }

                $this->wrappedSave($flagRule, $row, $lineNo);

                for ($i = 4; $i < 19; $i++) {
                    if ('1' === $row[$i] || '0' === $row[$i] ) {

                        $flagRuleContent = FlagsRulesContentModel::model()
                            ->byRule($flagRule->getId())
                            ->byFlagName($this->flagNames[$i])
                            ->find();
                        if (null === $flagRuleContent) {
                            $flagRuleContent = new FlagsRulesContentModel();

                            $flagRuleContent->setRuleId($flagRule->getId())
                                ->setFlagName($this->flagNames[$i]);
                        }

                        $flagRuleContent->setValue($row[$i]);

                        $this->wrappedSave($flagRuleContent, $row, $lineNo);
                        $this->updatedFlagRuleValues++;
                    }
                }

                $this->updatedFlagRules++;
                $lineNo++;
            }
            fclose($handle);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
        }
        return sprintf(
            'Импорт правил для флагов завершен. <br/>
             Сток с событиями обработано: %s. <br/>
             Правил для событий обработано: %s. <br/>',
            $this->updatedFlagRules,
            $this->updatedFlagRuleValues
        );
    }
    
    public function importFlags($fileName) {
        $handle = fopen($fileName, "r");
        if (!$handle) return false;
        
        $index = 0;
        $columns = array();
        $delays = array();
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
        
            // Определяем флаг
            $flag = $row[$this->_columnAlias['T']];
            if ($flag == '') continue;
            // Убеждаемся что поле имеет нужный нам формат
            if (!preg_match('/^F\d+$/', $flag)) continue;
            
            $excelId = $row[$this->_columnAlias['A']];
            
            $dialog = Dialogs::model()->byExcelId($excelId)->find();
            if ($dialog) {
                // Записываем флаг в модель диалога
                $dialog->flag = $flag;
                $dialog->save();
            }
            
        }
        fclose($handle);
    }
    
    public function updateFiles($fileName) {
        $handle = fopen($fileName, "r");
        if (!$handle) return false;
        
        $resultHtml = '';
        
        $index = 0;
        $columns = array();
        $delays = array();
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            $excelId    = $row[$this->_columnAlias['A']];
            $code       = $row[$this->_columnAlias['C']];
            $file       = $this->_convert($row[$this->_columnAlias['P']]);
            if ($file == 'N/A' || $file == '-') $file = '';
            
            $dialog = Dialogs::model()->byExcelId($excelId)->find();
            if ($dialog) {
                //$dialog->code   = $code;
                $dialog->sound  = $file;
                $dialog->save();
                $resultHtml .= "updated : $excelId sound : $file <br/>";
            }
            else {
                $resultHtml .= "cant find $excelId <br/>";
            }
            
        }
        fclose($handle);
        
        $resultHtml .= "Done";
        return $resultHtml;
    }
    
    public function updateDemo() {
        $fileName = 'media/xls/dialogs_demo.csv';
        $handle = fopen($fileName, "r");
        if (!$handle) return false;
        
        $html = '';
        
        $index = 0;
        $columns = array();
        $delays = array();
        $pointsCodes = array();
        while (($row = fgetcsv($handle, 5000, ";")) !== FALSE) {
            $index++;
            if ($index <= 2) continue;
            
            $excelId    = $row[$this->_columnAlias['A']];
            $demo       = $this->_convert($row[$this->_columnAlias['D']]);
            if ($demo == 'да') {
                $dialog = Dialogs::model()->byExcelId($excelId)->find();
                if ($dialog) {
                    $dialog->demo = 1;
                    $dialog->save();
                    $html .= "updated : $excelId <br/>";
                }
                else {
                    $html .= "cant find $excelId <br/>";
                }
            }
            
            
            
        }
        fclose($handle);
        
        $html .="Done";
        return $html;
    }
}


