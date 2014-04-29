<?php

/**
 * Class AnalyticalFileGenerator
 */
class AnalyticalFileGenerator {

    /**
     * @var array $sheets
     */
    public $sheets = [];

    /*
     * Список соответствия [имя листа] = номер листа
     * @var string[]
     */
    public $sheets_map = [];

    /*
     * Список соответствия [имя листа] = номер последней не пустой строки данного листа
     * @var string[]
     */
    public $sheet_row_position = [];

    /*
     * Список соответствия [имя листа] = номер последней не пустой колонки
     * в последней не пустой строке данного листа
     *
     * @var string[]
     */
    public $sheet_column_position = [];

    /**
     * Имя текущего листа:
     * - на данный лист осуществляется вставка данных
     * - sheet_number указывает на номер этого листа
     * - [column_number;row_number] является позицией курсора на этом листе
     *
     * @var string|null $sheet_name
     */
    public $sheet_name = null;

    /**
     * @var int $sheet_number
     */
    public $sheet_number = 0;

    /**
     * @var int $column_number
     */
    public $column_number = 0;

    /**
     * @var int
     */
    public $row_number = 0;

    /**
     * @var PHPExcel $document
     */
    public $document;

    public $info_name = '';

    public $info_company_name = '';

    /**
     * @var integer $info_simulation_id;
     */
    public $info_simulation_id;

    /**
     * Надо ли добавлять пятый лист с поведениями
     * @var bool $is_add_behaviours
     */
    public $is_add_behaviours = false;

    /**
     * @var string[], список id поведений
     * [ id => false ]
     * Используется как эталонны й пр ипроверке, все ли поведения добавлены на лист поведений
     */
    public $behaviourIds = [];

    /**
     * @var HeroBehaviour[], список обьектов-поведений
     * [ id => HeroBehaviour ]
     * Используется чтоб слать меньше запросов к БД
     */
    public $behaviourObjects = [];

    /**
     * Создаёт объект ексель документа
     */
    public function createDocument() {
        $this->document = new PHPExcel();
        $this->document->removeSheetByIndex(0);
    }

    /**
     * Инициализирует данные о HeroBehaviours для addBehavioursSheet()
     */
    public function initBehavioursData() {
        if (0 == count($this->behaviourIds) && 0 == count($this->behaviourObjects)) {
            $allBehaviours = HeroBehaviour::model()->findAll(' scenario_id = 2 ');
            foreach($allBehaviours as $behaviour) {
                /* @var HeroBehaviour $behaviour */
                $this->behaviourIds[$behaviour->id] = false;
                $this->behaviourObjects[$behaviour->id] = $behaviour;
            }
        }
    }

    /**
     * @param string $text, содержимое ячейки колонки
     * @param integer $width, ширина колонки
     *
     * @return
     */
    public function addColumn($text, $width = null, $isLast = false) {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        $sheet->setCellValueByColumnAndRow($this->column_number, $this->row_number, $text);
        $sheet->getStyleByColumnAndRow($this->column_number, $this->row_number)
            ->getBorders()->getOutline()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        if($width !== null){
            $sheet->getColumnDimensionByColumn($this->column_number)->setWidth($width);
        }

        $this->column_number++;

        return $sheet;
    }

    /**
     * @param string $text         cell text
     * @param string $format       one of PHPExcel_Style_NumberFormat::FORMAT_{...}
     * @param null|integer $width  cell width
     * @param string $BGcolor      cell background color
     */
    public function addColumnRight($text, $format, $width = null, $BGcolor = 'FFFF99') {
        $sheet = $this->addColumn($text, $width);

        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())
            ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())
            ->getNumberFormat()->setFormatCode($format);

    }

    /**
     * Добавляет строку в документ с первыми треся предустановленными колонками:
     * - Наименование Компании
     * - ФИО
     * - ID симуляции
     *
     * В случае если строка первая на листе -- задаёт заголовки колонок листа
     */
    public function addRow(){
        $this->row_number++;
        $this->column_number = 0;

        $this->sheet_row_position[$this->sheet_name] = $this->row_number;
        $this->sheet_column_position[$this->sheet_name] = $this->column_number;

        if($this->row_number === 1) {
            $this->setBoldFirstRow();
            $this->addColumn('Наименование Компании', 25);
            $this->addColumn('ФИО', 24);
            $this->addColumn('ID симуляции', 15);
            $this->setBorderBold();
        } else {
            $this->addColumn($this->info_company_name);
            $this->addColumn($this->info_name);
            $this->addColumn($this->info_simulation_id);
        }
    }


    /**
     * @param string $name
     */
    public function addSheet($name) {
        if (false == isset($this->sheets_map[$name])) {
            /* @var $this->document PHPExcel */
            $sheet = new PHPExcel_Worksheet($this->document, $name);
            $this->document->addSheet($sheet);

            $this->sheet_number++;
            $this->sheets_map[$name] = $this->sheet_number;
            $this->sheet_name = $name;

            $this->sheets[$this->sheet_number] = $sheet;
        } else {
            $this->sheet_number = $this->sheets_map[$name];
            $this->sheet_name = $name;
            $this->sheets[$this->sheet_number] = $this->document->getSheetByName($name);
        }


        if (false == isset($this->sheet_row_position[$name])) {
            $this->sheet_row_position[$name] = 0;
        }

        if (false == isset($this->sheet_column_position[$name])) {
            $this->sheet_column_position[$name] = 0;
        }

        $this->row_number = $this->sheet_row_position[$name];
        $this->column_number = $this->sheet_column_position[$name];
    }

    /**
     *
     */
    public function setBorderBold() {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        for ($i = 0; $i < $this->column_number; $i++) {
            $sheet->getStyleByColumnAndRow($i, $this->row_number)
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(\PHPExcel_Style_Border::BORDER_MEDIUM);
        }
    }

    /**
     *
     */
    public function setBoldFirstRow(){
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        $sheet->getStyle('A1:Z1')->applyFromArray(['font' => ['bold' => true]]);
    }

    /**
     * Сохраняет документ из $this->document
     * в файл по адресу self::createPathForAnalyticsFile()
     */
    public function save($assessment_version, $filename = 'custom') {
        $excelWriter = new PHPExcel_Writer_Excel2007($this->document);
        $path = SimulationService::createPathForAnalyticsFile($filename, $assessment_version);
        $excelWriter->save($path);
    }

    /**
     * @param Simulation $simulation
     */
    public function setInfoBySimulation(Simulation $simulation) {
        if($simulation->invite === null || $simulation->invite->ownerUser === null || $simulation->invite->ownerUser->getAccount() === null){
            $this->info_company_name = 'getAccount() return null or user or invite not define, very bad';
        } elseif ($simulation->invite->ownerUser->getAccount() instanceof \UserAccountPersonal){
            $this->info_company_name = 'user account is personal';
        } else {
            $this->info_company_name =  $simulation->invite->ownerUser->getAccount()->ownership_type.
                ' '.$simulation->invite->ownerUser->getAccount()->company_name;
        }
        if($simulation->invite !== null) {
            $this->info_name = $simulation->invite->lastname . " " . $simulation->invite->firstname;

        } else {
            $this->info_name = 'invite not found';
        }
        $this->info_simulation_id = $simulation->id;
    }

    /**
     *
     */
    public function runAssessment_v2(array $simulations) {
        /* @var $simulations Simulation[] */

        $this->addSheet("Итоговый рейтинг");

        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }

            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('Управленческие навыки');
            $this->addColumnRight(
                $data['management']['total']/100,
                PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            );
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['management']['short_text']), null, true);

            // ---

            $this->addRow();
            $this->addColumn('Результативность');
            $this->addColumnRight(
                $data['performance']['total']/100,
                PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            );
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance']['short_text']), null, true);

            // ---

            $this->addRow();
            $this->addColumn('Эффективность использования времени');
            $this->addColumnRight(
                $data['time']['total']/100,
                PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            );
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time']['short_text']), null, true);

            // ---

            $this->addRow();
            $this->addColumn('Итоговый рейтинг');
            $this->addColumnRight(
                $data['overall']/100,
                PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            );
            $this->addColumn('-', null, true);

            // ---

            $this->addRow();
            $this->addColumn('Процентиль');
            $this->addColumnRight(
                $data['percentile']['total']/100,
                PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00
            );
            $this->addColumn('-', null, true);

            $this->setBorderBold();
        }

        $this->setColumnBgColor('E', 'F');

        $this->setBorderAll('A', 'F');
        $this->setBorderRight('D', 'D');
        $this->setBorderRight('E', 'E');
        $this->setBorderRight('F', 'F');
        $this->setBorderLeft('A', 'A');

        $this->setBorderTop('A', 'F');

        $this->setBorderBold();

        $this->setTitle('A', 'F');

        $this->setColumnAlign('A', 'D', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('E', 'E', 2);
        $this->setColumnAlign('F', 'F', 2);

        // } Итоговый рейтинг

        $this->addSheet("Управленческие навыки");

        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }
            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.1 Использование планирования в течение дня');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][1]['1_1']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.task_managment.day_planing']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.task_managment.day_planing']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.1 Использование планирования в течение дня');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][1]['1_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.task_managment.day_planing']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.task_managment.day_planing']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][1]['1_2']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][1]['1_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][1]['1_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][1]['1_3']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.4 Прерывание при выполнении задач');
            $this->addColumn('positive');
            $this->addColumnRight('не оценивается', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.4 Прерывание при выполнении задач');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][1]['1_4']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(',')'], '', $dataText['popup']['management.task_managment.tasks_interruprion']['short_text']));
            $this->addColumn($dataText['popup']['management.task_managment.tasks_interruprion']['text'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight($data['management'][1]['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.1 Использование делегирования для управления объемом задач');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][2]['2_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.1 Использование делегирования для управления объемом задач');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][2]['2_1']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.2 Управление ресурсами различной квалификации');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][2]['2_2']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.2 Управление ресурсами различной квалификации');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][2]['2_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.3 Использование обратной связи');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][2]['2_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.3 Использование обратной связи');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][2]['2_3']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight($data['management'][2]['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['management.communication_managment.comunication_channel']['short_text']));
            $this->addColumn($dataText['popup']['management.communication_managment.comunication_channel']['text'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
            $this->addColumn('negative');
            $this->addColumnRight('не оценивается', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.2 Эффективная работа с почтой');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_2']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.2 Эффективная работа с почтой');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][3]['3_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.3 Эффективная работа со звонками');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['management.communication_managment.effective_calls']['short_text']));
            $this->addColumn($dataText['popup']['management.communication_managment.effective_calls']['text'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.3 Эффективная работа со звонками');
            $this->addColumn('negative');
            $this->addColumnRight('не оценивается', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.4 Эффективное управление встречами');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_4']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['text_positive'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.4 Эффективное управление встречами');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][3]['3_4']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['text_negative'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight($data['management'][3]['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->setBorderBold();
        }

        $this->setColumnBgColor('G', 'H');

        $this->setBorderAll('A', 'I');
        $this->setBorderLeft('A', 'A');
        $this->setBorderRight('F', 'F');
        $this->setBorderRight('G', 'G');
        $this->setBorderRight('H', 'H');
        $this->setBorderRight('I', 'I');

        $this->setBorderTop('A', 'H');

        $this->setBorderBold();

        $this->setTitle('A', 'I');

        $this->setColumnAlign('A', 'E', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('I', 'I', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('C', 'C', 2);
        $this->setColumnAlign('F', 'H', 2);

        ////////////////////////////////////////////////
        $this->addSheet("Результативность");

        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }
            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('Срочно');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '0'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.urgent']['short_text']));
            $this->addColumn($dataText['popup']['performance.urgent']['text'], null, true);

            $this->addRow();
            $this->addColumn('Высокий приоритет');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '1'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.high']['short_text']));
            $this->addColumn($dataText['popup']['performance.high']['text'], null, true);

            $this->addRow();
            $this->addColumn('Средний приоритет');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '2'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.middle']['short_text']));
            $this->addColumn($dataText['popup']['performance.middle']['text'], null, true);

            $this->addRow();
            $this->addColumn('Двухминутные задачи');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '2_min'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.two_minutes']['short_text']));
            $this->addColumn($dataText['popup']['performance.two_minutes']['text'], null, true);

            $this->setBorderBold();
        }

        $this->setColumnBgColor('E', 'F');

        $this->setBorderAll('A', 'G');
        $this->setBorderLeft('A', 'A');
        $this->setBorderRight('D', 'D');
        $this->setBorderRight('E', 'E');
        $this->setBorderRight('F', 'F');
        $this->setBorderRight('G', 'G');

        $this->setBorderTop('A', 'G');

        $this->setTitle('A', 'G');

        $this->setColumnAlign('A', 'D', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('G', 'G', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('C', 'F', 2);

        //////////////////////////////////////////////////////////
        $this->addSheet("Эффект. использования времени");

        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }
            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('1. Распределение времени, %');
            $this->addColumn('Продуктивное время (выполнение приоритетных задач)');
            $this->addColumnRight($data['time']['time_spend_for_1st_priority_activities']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.productive_time']['short_text']));
            $this->addColumn($dataText['popup']['time.productive_time']['text'], null, true);

            // ---

            var_dump($dataText['popup']['time.not_productive_time']['short_text']);
            var_dump($dataText['popup']['time.not_productive_time']['text']);

            $this->addRow();
            $this->addColumn('1. Распределение времени, %');
            $this->addColumn('Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumnRight($data['time']['time_spend_for_non_priority_activities']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.not_productive_time']['short_text']));
            $this->addColumn($dataText['popup']['time.not_productive_time']['text'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1. Распределение времени, %');
            $this->addColumn('Время ожидания и бездействия');
            $this->addColumnRight($data['time']['time_spend_for_inactivity']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.waiting_time']['short_text']));
            $this->addColumn($dataText['popup']['time.waiting_time']['text'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('2. Сверхурочное время (минуты)');
            $this->addColumn('Сверхурочное время');
            $this->addColumnRight($data['time']['workday_overhead_duration'], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.over_time']['short_text']));
            $this->addColumn($dataText['popup']['time.over_time']['text'], null, true);

            // ---

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Работа с документами');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Встречи');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Звонки');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Работа с почтой');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Планирование');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Работа с документами');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Встречи');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Звонки');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Работа с почтой');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Планирование');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->setBorderBold();
        }

        $this->setColumnBgColor('F', 'G');

        $this->setBorderLeft('A', 'A');
        $this->setBorderRight('E', 'E');
        $this->setBorderRight('F', 'F');
        $this->setBorderRight('G', 'G');
        $this->setBorderRight('H', 'H');
        $this->setBorderRight('H', 'H');

        $this->setBorderTop('A', 'H');

        $this->setBorderBold();

        $this->setTitle('A', 'H');

        $this->setColumnAlign('A', 'E', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('H', 'H', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('C', 'C', 2);
        $this->setColumnAlign('F', 'G', 2);

        ////////////////////////////////////////////////////
        if ($this->is_add_behaviours) {
            $this->addBehavioursSheet($simulations);
        }
    }

    /**
     * @param Simulation[] $simulations
     *
     * Задаёт преобразование оценоа 1.1-1.5 в 1.1-1.5 или 1.1-1.4
     * @param string $management_interpretation_mode ['va_to_v1';'v1_to_v2']
     */
    public function runAssessment_v1(array $simulations, $management_interpretation_mode = 'v1_to_v1') {
        /* @var $simulations Simulation[] */

        $this->addSheet("Итоговый рейтинг");

        $this->addRow();

        $this->addColumn('Тип оценки', 40);
        $this->addColumn('Оценка', 14);
        $this->addColumn('Текст', 26);
        ////////////////////////////////////////////////////
        if (Yii::app() instanceof CConsoleApplication) {
            echo "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }
            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('Управленческие навыки');
            $this->addColumnRight($data['management']['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['management']['short_text']), null, true);

            $this->addRow();
            $this->addColumn('Результативность');
            $this->addColumnRight($data['performance']['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance']['short_text']), null, true);

            $this->addRow();
            $this->addColumn('Эффективность использования времени');
            $this->addColumnRight($data['time']['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time']['short_text']), null, true);

            $this->addRow();
            $this->addColumn('Итоговый рейтинг');
            $this->addColumnRight($data['overall']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('Процентиль');
            if (isset($data['percentile'])) {
                $this->addColumnRight($data['percentile']['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            } else {
                /* @var $assessmentRecord AssessmentOverall */
                $assessmentRecord = AssessmentOverall::model()->findByAttributes([
                    'assessment_category_code' => AssessmentCategory::PERCENTILE,
                    'sim_id'                   => $simulation->id
                ]);
                if( null !== $assessmentRecord ) {
                    $this->addColumnRight($assessmentRecord->value/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                }else{
                    $this->addColumnRight('--', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                }
            }
            $this->addColumn('-', null, true);

            $this->setBorderBold();
        }


        $this->addSheet("Управленческие навыки");

        $this->addRow();

        $this->addColumn('Группа навыков', 42);
        $this->addColumn('Навык', 50);
        $this->addColumn('Шкала оценки', 15);
        $this->addColumn('Навык, оценка (0-100%)', 16);
        $this->addColumn('Текст', 26);
        $this->addColumn('Текстовая рекомендация', 120);

        //$this->setBorderBold();

        ////////////////////////////////////////////////
        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }
            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);

            // 1.x) ###############################################
            if ('v1_to_v1' == $management_interpretation_mode) {
                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.1 Определение приоритетов');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.2 Использование планирования в течение дня');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.2 Использование планирования в течение дня');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.3 Правильное определение приоритетов задач при планировании');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.3 Правильное определение приоритетов задач при планировании');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_3']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.4 Прерывание при выполнении задач');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_4']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.4 Прерывание при выполнении задач');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_4']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                // если оценка за 1.5 = 0, то её нет в кеш попапе!
                if (false == isset($data['management'][1]['1_5'])) {
                    $data['management'][1]['1_5'] = ['-' => 0];
                }

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.5 Завершение начатых задач');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_5']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

            } elseif ('v1_to_v2' == $management_interpretation_mode) {
                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.1 Использование планирования в течение дня');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_2']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn($dataText['popup']['management.task_managment.day_planing']['short_text_positive']);
                $this->addColumn($dataText['popup']['management.task_managment.day_planing']['text_positive'], null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.1 Использование планирования в течение дня');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn($dataText['popup']['management.task_managment.day_planing']['short_text_negative']);
                $this->addColumn($dataText['popup']['management.task_managment.day_planing']['text_negative'], null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['short_text_positive']);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['text_positive'], null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_3']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['short_text_negative']);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_planing']['text_negative'], null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
                $this->addColumn('positive');
                $this->addColumnRight($data['management'][1]['1_4']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['short_text_positive']);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['text_positive'], null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_4']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['short_text_negative']);
                $this->addColumn($dataText['popup']['management.task_managment.tasks_priority_execution']['text_negative'], null, true);

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.4 Прерывание при выполнении задач');
                $this->addColumn('positive');
                $this->addColumnRight('не оценивается', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                $this->addColumn('-');
                $this->addColumn('-', null, true);

                // если оценка за 1.4 = 0, то её нет в кеш попапе!
                if (false == isset($data['management'][1]['1_5'])) {
                    $data['management'][1]['1_5'] = ['-' => 0];
                }

                $this->addRow();
                $this->addColumn('1. Управление задачами с учётом приоритетов');
                $this->addColumn('1.4 Прерывание при выполнении задач');
                $this->addColumn('negative');
                $this->addColumnRight($data['management'][1]['1_5']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
                $this->addColumn(str_replace(['(',')'], '', $dataText['popup']['management.task_managment.tasks_interruprion']['short_text']));
                $this->addColumn($dataText['popup']['management.task_managment.tasks_interruprion']['text'], null, true);
            }

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight($data['management'][1]['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            // 2.x) ###############################################
            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.1 Использование делегирования для управления объемом задач');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][2]['2_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['text_positive'], null, true);

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.1 Использование делегирования для управления объемом задач');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][2]['2_1']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.people_managment.delegation']['text_negative'], null, true);

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.2 Управление ресурсами различной квалификации');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][2]['2_2']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['text_positive'], null, true);

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.2 Управление ресурсами различной квалификации');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][2]['2_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.people_managment.resource_quality']['text_negative'], null, true);

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.3 Использование обратной связи');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][2]['2_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['text_positive'], null, true);

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.3 Использование обратной связи');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][2]['2_3']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.people_managment.feedback']['text_negative'], null, true);

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight($data['management'][2]['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-');
            $this->addColumn('-', null, true);


            // 3.x) ###############################################
            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_1']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['management.communication_managment.comunication_channel']['short_text']));
            $this->addColumn($dataText['popup']['management.communication_managment.comunication_channel']['text'], null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
            $this->addColumn('negative');
            $this->addColumnRight('не оценивается', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.2 Эффективная работа с почтой');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_2']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['text_positive'], null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.2 Эффективная работа с почтой');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][3]['3_2']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_mail']['text_negative'], null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.3 Эффективная работа со звонками');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_3']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['management.communication_managment.effective_calls']['short_text']));
            $this->addColumn($dataText['popup']['management.communication_managment.effective_calls']['text'], null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.3 Эффективная работа со звонками');
            $this->addColumn('negative');
            $this->addColumnRight('не оценивается', PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.4 Эффективное управление встречами');
            $this->addColumn('positive');
            $this->addColumnRight($data['management'][3]['3_4']['+']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['short_text_positive']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['text_positive'], null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.4 Эффективное управление встречами');
            $this->addColumn('negative');
            $this->addColumnRight($data['management'][3]['3_4']['-']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['short_text_negative']);
            $this->addColumn($dataText['popup']['management.communication_managment.effective_meetings']['text_negative'], null, true);

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight($data['management'][3]['total']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->setBorderBold();
        }
        ////////////////////////////////////////////////
        $this->addSheet("Результативность");

        $this->addRow();
        $this->addColumn('Группа задач', 20);
        $this->addColumn('Результативность, оценка (0-100%)', 20);
        $this->addColumn('Текст', 26);
        $this->addColumn('Текстовая рекомендация', 120);

        ///////////////////////////////////////////////////////////
        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }

            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('Срочно');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '0'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.urgent']['short_text']));
            $this->addColumn($dataText['popup']['performance.urgent']['text'], null, true);

            $this->addRow();
            $this->addColumn('Высокий приоритет');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '1'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.high']['short_text']));
            $this->addColumn($dataText['popup']['performance.high']['text'], null, true);

            $this->addRow();
            $this->addColumn('Средний приоритет');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '2'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.middle']['short_text']));
            $this->addColumn($dataText['popup']['performance.middle']['text'], null, true);

            $this->addRow();
            $this->addColumn('Двухминутные задачи');
            $this->addColumnRight($this->getPerformanceCategory($data['performance'], '2_min'), PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['performance.two_minutes']['short_text']));
            $this->addColumn($dataText['popup']['performance.two_minutes']['text'], null, true);

            $this->setBorderBold();
        }
        //////////////////////////////////////////////////////////
        $this->addSheet("Эффект. использования времени");

        $this->addRow();
        $this->addColumn('Группа параметров', 55);
        $this->addColumn('Параметр', 45);
        $this->addColumn('Эффективность использования времени, оценка', 24);
        $this->addColumn('Текст', 26);
        $this->addColumn('Текстовая рекомендация', 120);

        //$this->setBorderBold();
        ////////////////////////////////////////////////////
        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }

            $data = json_decode($simulation->getAssessmentDetails(), true);
            $dataText = unserialize($simulation->popup_tests_cache);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('1. Распределение времени, %');
            $this->addColumn('Продуктивное время (выполнение приоритетных задач)');
            $this->addColumnRight($data['time']['time_spend_for_1st_priority_activities']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.productive_time']['short_text']));
            $this->addColumn($dataText['popup']['time.productive_time']['text'], null, true);

            $this->addRow();
            $this->addColumn('1. Распределение времени, %');
            $this->addColumn('Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumnRight($data['time']['time_spend_for_non_priority_activities']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.not_productive_time']['short_text']));
            $this->addColumn($dataText['popup']['time.not_productive_time']['text'], null, true);

            $this->addRow();
            $this->addColumn('1. Распределение времени, %');
            $this->addColumn('Время ожидания и бездействия');
            $this->addColumnRight($data['time']['time_spend_for_inactivity']/100, PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.waiting_time']['short_text']));
            $this->addColumn($dataText['popup']['time.waiting_time']['text'], null, true);

            $this->addRow();
            $this->addColumn('2. Сверхурочное время (минуты)');
            $this->addColumn('Сверхурочное время');
            $this->addColumnRight($data['time']['workday_overhead_duration'], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn(str_replace(['(', ')'], '', $dataText['popup']['time.over_time']['short_text']));
            $this->addColumn($dataText['popup']['time.over_time']['text'], null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Работа с документами');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Встречи');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Звонки');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Работа с почтой');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
            $this->addColumn('Планирование');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Работа с документами');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Встречи');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Звонки');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Работа с почтой');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->addRow();
            $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
            $this->addColumn('Планирование');
            $this->addColumnRight($data['time'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING], PHPExcel_Style_NumberFormat::FORMAT_NUMBER);
            $this->addColumn('-');
            $this->addColumn('-', null, true);

            $this->setBorderBold();
        }
        ////////////////////////////////////////////////////
        if ($this->is_add_behaviours) {
            $this->addBehavioursSheet($simulations);
        }
    }

    /**
     * @param Simulation[] $simulations
     */
    public function addBehavioursSheet($simulations) {

        $this->initBehavioursData();

        $this->addSheet("Поведения");

        if (0 == $this->sheet_row_position["Поведения"]) {
            $this->addRow();
            $this->addColumn('Номер требуемого поведения', 14);
            $this->addColumn('Номер цели обучения', 14);
            $this->addColumn('Наименование цели обучения', 30);
            $this->addColumn('Наименование требуемого поведения', 80);
            $this->addColumn('Оценка полученная в симуляции', 24);
        }
        //////////////////////////////////////////////////
        if (Yii::app() instanceof CConsoleApplication) {
            echo  "\r\n";
        }
        foreach($simulations as $simulation) {
            if (Yii::app() instanceof CConsoleApplication) {
                echo '.'; // каждая точка - это одна симуляция
            }
            $this->setInfoBySimulation($simulation);

            $usedBehaviours = $this->behaviourIds;

            /* @var AssessmentAggregated $behaviour */
            foreach ($simulation->assessment_aggregated as $behaviour) {
                if (isset($usedBehaviours[$behaviour->point->id])) {
                    $usedBehaviours[$behaviour->point->id] = true;
                }
                $this->addRow();
                $this->addColumn($behaviour->point->code);
                $this->addColumn($behaviour->point->learning_goal->code);
                $this->addColumn($behaviour->point->learning_goal->title);
                $this->addColumn($behaviour->point->title);
                $this->addColumn($behaviour->value);
            }

            foreach ($usedBehaviours as $id => $value) {
                if (false == $value) {
                    $this->addRow();
                    $this->addColumn($this->behaviourObjects[$id]->code);
                    $this->addColumn($this->behaviourObjects[$id]->learning_goal->code);
                    $this->addColumn($this->behaviourObjects[$id]->learning_goal->title);
                    $this->addColumn($this->behaviourObjects[$id]->title);
                    $this->addColumn('-');
                }
            }

            $this->setBorderBold();
        }

        $this->setBorderAll('A', 'F');

        $this->setBorderTop('A', 'F');
        $this->setBorderLeft('A', 'A');
        $this->setBorderRight('H', 'H');
        $this->setBorderBold();

        $this->setTitle('A', 'H');

        $this->setColumnAlign('A', 'H', 2);
        $this->setColumnAlign('A', 'B', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $this->setColumnAlign('F', 'G', 2, \PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    }

    /**
     * Геттер категории с массива
     * @param $performance
     * @param $category
     * @return int
     */
    public function getPerformanceCategory($performance, $category)
    {
        if (isset($performance[$category])){
            return $performance[$category]/100;
        } else {
            return 0;
        }
    }

    /**
     * Добавляет автофильтры для всех колонок всех листов
     */
    public function setAutoFilters() {
        // надо знать на каждом листе имя поледней колонки
        // но, $sheet->getHighestColumn() - не подходит, она возвращает "Y"
        // @link: http://http://stackoverflow.com/questions/5577856/phpexcel-column-loop
        $lastColumns = ['F', 'I', 'G', 'H', 'H'];

        $n = 0;
        foreach ($this->sheets as $sheet) {
            // @link: setAutoFilter() http://phpexcel.codeplex.com/workitem/15399
            $sheet->setAutoFilter(sprintf('A1:%s%s', $lastColumns[$n], $sheet->getHighestRow()));
            $n ++;
        }
    }

    /**
     * @param int $column
     * @param string $BGcolor
     */
    public function setColumnBgColor($column1 = 'A', $column2 = 'A',$BGcolor = 'FFFF99') {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s2:%s%s', $column1, $column2, $sheet->getHighestRow());

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'fill' => [
                    'type'       => \PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => ['rgb' => $BGcolor],
                ]
            ]
        );
    }

    /**
     * @param int $column
     * @param string $BGcolor
     */
    public function setBorderRight($column1 = 'A', $column2 = 'A', $border = 'medium') {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s2:%s%s', $column1, $column2, $sheet->getHighestRow());

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'borders' => [
                    'right' => [
                        'style' => $border,
                    ],
                ]
            ]
        );
    }

    /**
     * @param int $column
     * @param string $BGcolor
     */
    public function setBorderLeft($column1 = 'A', $column2 = 'A', $border = 'medium') {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s2:%s%s', $column1, $column2, $sheet->getHighestRow());

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'borders' => [
                    'left' => [
                        'style' => $border,
                    ],
                ]
            ]
        );
    }

    /**
     * @param int $column
     * @param string $BGcolor
     */
    public function setBorderBottom($column1 = 'A', $column2 = 'Z', $start = 1, $end = 1, $border = 'medium') {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s%s:%s%s', $column1, $start, $column2, $end);

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'borders' => [
                    'bottom' => [
                        'style' => $border,
                    ],
                ]
            ]
        );
    }

    /**
     * @param int $column
     * @param string $BGcolor
     */
    public function setBorderTop($column1 = 'A', $column2 = 'A', $start = 1, $end = 1, $border = 'medium') {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s%s:%s%s', $column1, $start, $column2, $end);

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'borders' => [
                    'top' => [
                        'style' => $border,
                    ],
                ]
            ]
        );
    }

    /**
     * @param int $column
     * @param string $BGcolor
     */
    public function setBorderAll($column1 = 'A', $column2 = 'A', $border = 'thin') {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s2:%s%s', $column1, $column2, $sheet->getHighestRow());

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'borders' => [
                    'left' => [
                        'style' => $border,
                    ],
                    'right' => [
                        'style' => $border,
                    ],
                    'top' => [
                        'style' => $border,
                    ],
                    'bottom' => [
                        'style' => $border,
                    ],
                ]
            ]
        );
    }

    /**
     * @param string $column1
     * @param string $column2
     * @param bool $wrap
     * @param string $horizontal
     * @param string $vertical
     */
    public function setColumnAlign($column1 = 'A', $column2 = 'A', $startRow = '1',
        $horizontal = 'center', $vertical = 'center', $wrap = true) {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s%s:%s%s', $column1, $startRow, $column2, $sheet->getHighestRow());

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'alignment' => array(
                    'wrap'       => $wrap,
                    'horizontal' => $horizontal,
                    'vertical'   => $vertical
                ),
            ]
        );
    }

    /**
     * @param string $column1
     * @param string $column2
     * @param bool $wrap
     * @param string $horizontal
     * @param string $vertical
     */
    public function setTitle($column1 = 'A', $column2 = 'A',
        $horizontal = 'center', $vertical = 'center', $wrap = true) {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];

        $diapason = sprintf('%s1:%s1', $column1, $column2);

        $sheet->getStyle($diapason)->applyFromArray(
            [
                'alignment' => array(
                    'wrap'       => $wrap,
                    'horizontal' => $horizontal,
                    'vertical'   => $vertical
                ),
                'borders' => [
                    'right' => [
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    ],
                    'left' => [
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    ],
                    'top' => [
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    ],
                    'bottom' => [
                        'style' => \PHPExcel_Style_Border::BORDER_MEDIUM,
                    ],
                ]
            ]
        );
    }
}