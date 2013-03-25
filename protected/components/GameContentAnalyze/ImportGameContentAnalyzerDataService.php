<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/4/13
 * Time: 12:07 AM
 * To change this template use File | Settings | File Templates.
 */
class ImportGameContentAnalyzerDataService
{
    private $filename = null;

    private $import_id = null;

    private $errors = null;

    private $cache_method = null;

    public function __construct()
    {
        $files = glob(__DIR__ . '/../../../media/scenario*.xlsx');
        $files = array_combine($files, array_map("filemtime", $files));
        arsort($files);

        $this->filename = key($files);

        $this->import_id = $this->getImportUUID();
        $this->cache_method = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
    }

    public function setFilename($name)
    {
        $this->filename = $name;
    }

    /**
     * Returns cached excel file
     * @return PHPExcel
     */
    private function getExcel()
    {
        if (!isset($this->excel)) {
            $this->excel = $this->getReader()->load($this->filename);
        }
        return $this->excel;
    }

    /**
     * Get sheet by name
     *
     * @param string $pName Sheet name
     * @return PHPExcel_Worksheet
     * @throws Exception
     */
    public function getSheetByName($pName = '')
    {
        $worksheetCount = count($this->_workSheetCollection);
        for ($i = 0; $i < $worksheetCount; ++$i) {
            if ($this->_workSheetCollection[$i]->getTitle() == $pName) {
                return $this->_workSheetCollection[$i];
            }
        }

        return null;
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param int $row
     * @return void
     */
    private function setColumnNumbersByNames($sheet, $row = 1)
    {
        for ($i = 0; ; $i++) {
            $row_title = $sheet->getCellByColumnAndRow($i, $row)->getValue();
            if (null !== $row_title) {
                $this->columnNoByName[$row_title] = $i;
            } else {
                return;
            }
        }
    }

    /**
     * @param PHPExcel_Worksheet $sheet
     * @param string $columnName
     * @param PHPExcel_Worksheet_RowIterator $i
     * @param int $increment
     * @return mixed
     */
    private function getCellValue($sheet, $columnName, $i, $increment = 0)
    {
        return $sheet->getCellByColumnAndRow(
            $this->columnNoByName[$columnName] + $increment,
            $i->key()
        )->setDataType(PHPExcel_Cell_DataType::TYPE_STRING)->getCalculatedValue();
    }

    /**
     * @return PHPExcel_Reader_Excel2003XML
     */
    private function getReader()
    {
        PHPExcel_Settings::setCacheStorageMethod($this->cache_method);

        if (!isset($this->reader)) {
            $this->reader = PHPExcel_IOFactory::createReader('Excel2007');

            // prevent read string "11:00" like "0.45833333333333" even by getValue()
            $this->reader->setReadDataOnly(true);
        }

        return $this->reader;
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

    /* --------------------- */

    public function importDialogs()
    {
        $importedDialogs = [];

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        $this->importedEvents = [];

        // Events from dialogs {
        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {

            $code = $this->getCellValue($sheet, 'Event_code', $i);

            if ($code === null)
                continue;

            if ($code === '-' || $code === '') {
                continue;
            }
            if (in_array($code, $this->importedEvents)) {
                continue;
            }

            $dialog = new Dialog(); // Создаем событие
            $dialog->code = $code;
            $dialog->title          = $this->getCellValue($sheet, 'Наименование события', $i);
            $dialog->setTypeFromExcel($this->getCellValue($sheet, 'Тип интерфейса диалога', $i));
            $dialog->start_by       = $this->getCellValue($sheet, 'Тип запуска', $i);
            $dialog->delay          = $this->getCellValue($sheet, 'Задержка, мин', $i);
            $dialog->category       = $this->getCellValue($sheet, 'Категория события', $i);
            $dialog->start_time     = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Начало, время', $i), 'hh:mm:ss');
            $dialog->is_use_in_demo = ('да' == $this->getCellValue($sheet, 'Использовать в DEMO', $i)) ? true : false;
            $dialog->import_id      = $this->import_id;

            $importedDialogs[] = $dialog;

            $this->importedEvents[] = $code;
        }
        // Events from dialogs }

        // Create crutch events (Hello, Sergey) {
        $dialogT = new Dialog(); // Создаем событие
        $dialogT->code = 'T';
        $dialogT->title = 'Конечное событие';
        $dialogT->start_by       = Dialog::START_BY_DIALOG;
        $dialogT->delay          = 0;
        $dialogT->is_use_in_demo = true;
        $dialogT->category       = 5;
        $dialogT->import_id      = $this->import_id;
        $importedDialogs[] = $dialogT;
        // }

        return $importedDialogs;
    }

    /**
     *
     */
    public function importEventSamples()
    {
        $importedEvents = [];

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        $this->importedEvents = [];

        // Events from dialogs {
        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {

            $code = $this->getCellValue($sheet, 'Event_code', $i);

            if ($code === null)
                continue;

            if ($code === '-' || $code === '') {
                continue;
            }

            if (in_array($code, $this->importedEvents)) {
                continue;
            }

            $event = new EventSample();
            $event->code = $code;
            $event->title = $this->getCellValue($sheet, 'Наименование события', $i);
            $event->on_ignore_result = 7; // ничего
            $event->on_hold_logic = 1; // ничего
            $event->trigger_time = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Начало, время', $i), 'hh:mm:ss');
            $event->import_id = $this->import_id;

            if (null == $event->trigger_time) {
                $event->trigger_time = '00:00:00'; // emulate DB
                $key = (int)('1900'.rand(100000,999999));  // move to the end
            } else {
                $key = (int)(str_replace(':', '', substr($event->trigger_time, 0, 5)).rand(100000,999999));
            }

            $this->importedEvents[] = $code;

            $importedEvents[$key] = $event;
        }
        // Events from dialogs }

        // Create crutch events (Hello, Sergey) {
        $event = new EventSample(); // Создаем событие
        $event->code = 'T';
        $event->title = 'Конечное событие';
        $event->on_ignore_result = 7; // ничего
        $event->on_hold_logic = 1; // ничего
        $event->trigger_time = 0;
        $event->import_id = $this->import_id;

        $importedEvents[(int)('1900'.rand(100000,999999))] = $event; // move to the end
        // }

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);
        // load sheet }

        // import "email"-events, but Mxx only
        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Mail_code', $i);

            if ($code === null || 'MS' == substr($code, 0, 2)) {
                continue;
            }

            $sendingTime = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Time', $i), 'hh:mm:ss');
            assert($sendingTime !== null);

            $event = new EventSample();
            $event->code = $code;
            $event->on_ignore_result = 7;
            $event->on_hold_logic = 1;
            $event->trigger_time = $sendingTime;
            $event->import_id = $this->import_id;

            if (null == $event->trigger_time) {
                $event->trigger_time = '00:00:00'; // emulate DB
                $key = (int)('1900'.rand(100000,999999));  // move to the end
            } else {
                $key = (int)(str_replace(':', '', substr($event->trigger_time, 0, 5)).rand(100000,999999));
            }

            $importedEvents[$key] = $event;
        }

        // sort by time
        ksort($importedEvents);

        return $importedEvents;
    }

    public function importEmails()
    {
        $importedEmails = [];

        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Mail');
        $this->columnNoByName = [];
        $this->setColumnNumbersByNames($sheet, 2);
        // load sheet }

        $index = 0;

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {
            $code = $this->getCellValue($sheet, 'Mail_code', $i);
            if (null === $code || '' === $code) {
                continue;
            }
            $sendingDate = date('Y-m-d', (int)PHPExcel_Shared_Date::ExcelToPHP($this->getCellValue($sheet, 'Date', $i)));
            $sendingTime = PHPExcel_Style_NumberFormat::toFormattedString($this->getCellValue($sheet, 'Time', $i), 'hh:mm:ss');

            $fromCode = $this->getCellValue($sheet, 'From _code', $i);
            $toCode = $this->getCellValue($sheet, 'To_code', $i);

            // Письмо
            $message = $this->getCellValue($sheet, 'Mail_body', $i);

            $flag = $this->getCellValue($sheet, 'Переключение флагов 1', $i);

            $typeOfImportance = trim($sheet->getCellByColumnAndRow($this->columnNoByName['Mail_type_for_assessment'], $i->key())->getValue());

            $group = 5;
            $type = 0;
            // определение группы по коду
            $source = null;
            if (preg_match("/MY\d+/", $code)) {
                $group = 1;
                $type = 3;
            } else if (preg_match("/M\d+/", $code)) {
                $type = 1;
            } else if (preg_match("/MSY\d+/", $code)) {
                $group = 3;
                $type = 4;
            } else if (preg_match("/MS\d+/", $code)) {
                $type = 2;
            } else {
                assert(false, 'Unknown code: ' . $code);
            }

            if (strstr($toCode, ',')) {
                $toCode = explode(',', $toCode);
            }

            if (is_array($toCode)) {
                $toCode = $toCode[0];
            }

            $time = explode(':', $sendingTime);
            if (!isset($time[1])) {
                $time[0] = 0;
                $time[1] = 0;
            }

            $themePrefix = $this->getCellValue($sheet, 'Theme_prefix', $i);
            if ($themePrefix === '-') {
                $themePrefix = null;
            }

            $subjectEntity = new CommunicationTheme();
            $subjectEntity->text = '';

            $emailTemplateEntity = new MailTemplate();
            $emailTemplateEntity->code = $code;
            $emailTemplateEntity->group_id = $group;
            $emailTemplateEntity->sender_id = $fromCode;
            $emailTemplateEntity->receiver_id = $toCode;
            $emailTemplateEntity->subject_obj = $subjectEntity;
            $emailTemplateEntity->message = $message;
            $emailTemplateEntity->sent_at = $sendingDate . ' ' . $sendingTime;
            $emailTemplateEntity->type = $type;
            $emailTemplateEntity->type_of_importance = $typeOfImportance;
            $emailTemplateEntity->import_id = $this->import_id;
            $emailTemplateEntity->flag_to_switch = (NULL == $flag) ? NULL : $flag;

            $importedEmails[$code] = $emailTemplateEntity;
        }

        return $importedEmails;
    }

    /**
     *
     */
    public function importDialogReplicas()
    {
        $importedReplica = [];

        // load sheet {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('ALL DIALOGUES(E+T+RS+RV)');
        // load sheet }

        $this->setColumnNumbersByNames($sheet, 2);

        $subtypes  = [
            'Звонок'               => 1,
            'Разговор по телефону' => 2,
            'Визит'                => 3,
            'Встреча'              => 4,
            'Стук в дверь'         => 5,
        ];

        for ($i = $sheet->getRowIterator(3); $i->valid(); $i->next()) {

            // in the bottom of excel sheet we have a couple of check sum, that aren`t replics sure.
            $replicaExcelId = $this->getCellValue($sheet, 'id записи', $i);
            if (NULL == $replicaExcelId) {
                continue;
            }

            $replica = new Replica(); // Создаем событие
            $replica->excel_id = $replicaExcelId;

            // a lot of dialog properties: {
            $replica->code         = $this->getCellValue($sheet, 'Event_code', $i);
            $replica->event_result = 7; // ничего
            $fromCharacterCode   = $this->getCellValue($sheet, 'Персонаж-ОТ (код)', $i);
            $replica->ch_from      = $fromCharacterCode;
            $toCharacterCode     = $this->getCellValue($sheet, 'Персонаж-КОМУ (код)', $i);
            $replica->ch_to        = $toCharacterCode;

            $subtypeAlias = $this->getCellValue($sheet, 'Тип интерфейса диалога', $i);
            if (!isset($subtypes[$subtypeAlias])) {
                throw new Exception('Unknown dialog type: ' . $subtypeAlias);
            }
            $replica->dialog_subtype = (isset($subtypes[$subtypeAlias])) ? $subtypes[$subtypeAlias] : NULL; // 1 is "me"

            $code = $this->getCellValue($sheet, 'Event_result_code', $i);
            $text = $this->getCellValue($sheet, 'Реплика', $i);
            $text = preg_replace('/^\s*-[\s ]*/', ' — ', $text);
            $replica->text = $text;

            //$replica->next_event = $this->getNextEventId($code);
            $replica->next_event_code = ('-' == $code) ? NULL : $code;
            $replica->step_number     = $this->getCellValue($sheet, '№ шага в диалоге', $i);
            $replica->replica_number  = $this->getCellValue($sheet, '№ реплики в диалоге', $i);
            $replica->delay           = $this->getCellValue($sheet, 'Задержка, мин', $i);
            $replica->fantastic_result = $this->getCellValue($sheet, 'Отправка письма фант образом', $i) ?: $this->getCellValue($sheet, 'Открытие полученного письма фант образом', $i);

            $flagCode = $this->getCellValue($sheet, 'Переключение флагов 1', $i);
            if ($flagCode !== '') {
                $replica->flag_to_switch = $flagCode;
            } else {
                $replica->flag_to_switch = null;
            }

            $isUseInDemo           = ('да' == $this->getCellValue($sheet, 'Использовать в DEMO', $i)) ? 1 : 0;
            $replica->demo         = $isUseInDemo;
            $replica->type_of_init = $this->getCellValue($sheet, 'Тип запуска', $i);

            $sound          = $this->getCellValue($sheet, 'Имя звук/видео файла', $i);
            $replica->sound = ($sound == 'нет' || $sound == '-') ? NULL : $sound;

            $isFinal                   = $this->getCellValue($sheet, 'Конечная реплика (да/нет)', $i);
            $replica->is_final_replica = ('да' === $isFinal) ? true : false;

            $replica->import_id = $this->import_id;
            // a lot of dialog properties: }

            $importedReplica[] = $replica;
        }

        return $importedReplica;
    }

    public function importFlagsRules($events)
    {
        $excel = $this->getExcel();
        $sheet = $excel->getSheetByName('Flags');
        // load sheet }

        $this->setColumnNumbersByNames($sheet);

        $importedFlagsRunMail = [];
        $importedFlagsBlockMail = [];
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('mail' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            // we run, immediately after flag was switched, email without trigger time only
            if ('00:00:00' == $events[$this->getCellValue($sheet, 'Run_code', $i)]->trigger_time) {
                // create entity
                $mailFlag = new FlagRunMail();
                $mailFlag->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
                $mailFlag->mail_code = $this->getCellValue($sheet, 'Run_code', $i);
                $mailFlag->import_id = $this->import_id;
                $importedFlagsRunMail[] = $mailFlag;
            }

            // Flag blocks mail always {
            $mailFlag = new FlagBlockMail();
            $mailFlag->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            $mailFlag->mail_template_id = $this->getCellValue($sheet, 'Run_code', $i);
            $mailFlag->value            = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $mailFlag->import_id        = $this->import_id;
            $importedFlagsBlockMail[] = $mailFlag;
            // Flag blocks mail always }
        }

        $importedFlagsBlockReplica = [];
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('replica' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            // create entity
            $flagBlockReplica = new FlagBlockReplica();
            $flagBlockReplica->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            $flagBlockReplica->replica_id = (int)$this->getCellValue($sheet, 'Run_code', $i);


            $flagBlockReplica->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);
            $flagBlockReplica->import_id = $this->import_id;

            $importedFlagsBlockReplica[] = $flagBlockReplica;
        }

        // for Dialogs {
        $importedFlagsBlockDialog = [];
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            if ('dialog' != $this->getCellValue($sheet, 'Flag_run_type', $i)) {
                continue;
            }

            // create entity
            $flagBlockDialog = new FlagBlockDialog();
            $flagBlockDialog->flag_code = $this->getCellValue($sheet, 'Flag_code', $i);
            $flagBlockDialog->dialog_code = $this->getCellValue($sheet, 'Run_code', $i);

            $flagBlockDialog->value = $this->getCellValue($sheet, 'Flag_value_to_run', $i);

            $importedFlagsBlockDialog[] = $flagBlockDialog;
        }
        // for Dialogs }

        return [
            'RunMail'   => $importedFlagsRunMail,
            'BlockReplica' => $importedFlagsBlockReplica,
            'BlockDialog'  => $importedFlagsBlockDialog,
            'BlockMail'    => $importedFlagsBlockMail
        ];
    }
}
