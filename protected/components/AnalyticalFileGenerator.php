<?php

/**
 * Class AnalyticalFileGenerator
 */
class AnalyticalFileGenerator {

    /**
     * @var array
     */
    public $sheets = [];

    /**
     * @var int
     */
    public $sheet_number = 0;

    /**
     * @var int
     */
    public $column_number = 0;

    /**
     * @var int
     */
    public $row_number = 0;

    /**
     * @var PHPExcel
     */
    public $document;

    public $info_name = '';

    public $info_company_name = '';

    public $info_simulation_id;

    /**
     *
     */
    public function generateV1() {

    }

    /**
     *
     */
    public function generateV2() {

    }

    /**
     * @param $text
     */
    public function addColumn($text, $width = null) {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        $sheet->setCellValueByColumnAndRow($this->column_number, $this->row_number, $text);
        $sheet->getStyleByColumnAndRow($this->column_number, $this->row_number)
            ->getBorders()->getOutline()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        if($width !== null){
            $sheet->getColumnDimensionByColumn($this->column_number)->setWidth($width);
            $sheet->getStyleByColumnAndRow($this->column_number, $sheet->getHighestRow())
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        if(is_numeric($text)){
            $sheet->getStyleByColumnAndRow($this->column_number, $sheet->getHighestRow())
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        if((is_float(mb_substr($text, 0, mb_strlen($text) - 1)) || is_numeric(mb_substr($text, 0, mb_strlen($text) - 1))) && mb_substr($text, mb_strlen($text) - 1, mb_strlen($text)) === '%') {
            $sheet->getStyleByColumnAndRow($this->column_number, $sheet->getHighestRow())
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $sheet->getStyleByColumnAndRow($this->column_number, $sheet->getHighestRow())
                ->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);

        }
        $this->column_number++;
        return $sheet;
    }


    public function addColumnRight($text, $width = null) {
        $sheet = $this->addColumn($text, $width);
        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())->getFill()
            ->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'FFFF99')
            ));
    }

    /**
     *
     */
    public function addRow(){
        $this->row_number++;
        $this->column_number = 0;
        if($this->row_number === 1) {
            $this->setBoldFirstRow();
            $this->addColumn('Наименование Компании', 24);
            $this->addColumn('ФИО', 24);
            $this->addColumn('ID симуляции', 14);
        } else {
            $this->addColumn($this->info_company_name);
            $this->addColumn($this->info_name);
            $this->addColumn($this->info_simulation_id);
        }
    }

    /**
     *
     */
    public function createDocument() {
        $this->document =  new PHPExcel();
        $this->document->removeSheetByIndex(0);
    }

    /**
     * @param $name
     */
    public function addSheet($name) {
        /* @var $this->document PHPExcel */
        $sheet = new PHPExcel_Worksheet($this->document, $name);
        $this->document->addSheet($sheet);
        $this->sheet_number++;
        $this->sheets[$this->sheet_number] = $sheet;
        $this->row_number = 0;
        $this->column_number = 0;
    }

    public function setBorderBold() {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        for ($i = 0; $i < $this->column_number; $i++) {
            $sheet->getStyleByColumnAndRow($i, $this->row_number)
                ->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
        }
        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())
            ->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
    }

    public function setBoldFirstRow(){
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        $sheet->getStyle('A1:Z1')->applyFromArray(['font' => ['bold' => true]]);
    }

    /**
     *
     */
    public function save() {

        $assessment_version = 'v1';
        $excelWriter = new PHPExcel_Writer_Excel2007($this->document);
        $path = SimulationService::createPathForAnalyticsFile('custom', $assessment_version);
        $excelWriter->save($path);
    }

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
    public function run(array $simulations) {
        /* @var $simulations Simulation[] */
        $this->createDocument();

        $this->addSheet("Итоговый рейтинг");

        $this->addRow();

        $this->addColumn('Тип оценки', 40);
        $this->addColumn('Оценка', 14);
        $this->setBorderBold();
        ////////////////////////////////////////////////////
        foreach($simulations as $simulation) {
            $data = json_decode($simulation->getAssessmentDetails(), true);

            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('Управленческие навыки');
            $this->addColumnRight(round($data['management']['total'], 2).'%');

            $this->addRow();
            $this->addColumn('Результативность');
            $this->addColumnRight(round($data['performance']['total'], 2).'%');

            $this->addRow();
            $this->addColumn('Эффективность использования времени');
            $this->addColumnRight(round($data['time']['total'], 2).'%');

            $this->addRow();
            $this->addColumn('Итоговый рейтинг');
            $this->addColumnRight(round($data['overall'], 2).'%');

            $this->addRow();
            $this->addColumn('Процентиль');
            $this->addColumnRight(round($data['percentile']['total'], 2).'%');
            /////////////////////////////////////////////////////
            $this->setBorderBold();
        }


        $this->addSheet("Управленческие навыки");

        $this->addRow();

        $this->addColumn('Группа навыков', 42);
        $this->addColumn('Навык', 50);
        $this->addColumn('Шкала оценки', 14);
        $this->addColumn('Навык, оценка (0-100%)', 18);

        $this->setBorderBold();
        ////////////////////////////////////////////////
        foreach($simulations as $simulation) {
            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.1 Использование планирования в течение дня');
            $this->addColumn('negative');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.1 Использование планирования в течение дня');
            $this->addColumn('positive');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
            $this->addColumn('positive');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
            $this->addColumn('negative');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
            $this->addColumn('positive');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
            $this->addColumn('negative');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.4 Прерывание при выполнении задач');
            $this->addColumn('positive');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('1.4 Прерывание при выполнении задач');
            $this->addColumn('negative');
            $this->addColumnRight('%');

            $this->addRow();
            $this->addColumn('1. Управление задачами с учётом приоритетов');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.1 Использование делегирования для управления объемом задач');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.1 Использование делегирования для управления объемом задач');
            $this->addColumn('negative');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.2 Управление ресурсами различной квалификации');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.2 Управление ресурсами различной квалификации');
            $this->addColumn('negative');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.3 Использование обратной связи');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('2.3 Использование обратной связи');
            $this->addColumn('negative');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('2. Управление людьми');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
            $this->addColumn('negative');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.2 Эффективная работа с почтой');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.2 Эффективная работа с почтой');
            $this->addColumn('negative');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.3 Эффективная работа со звонками');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.4 Эффективное управление встречами');
            $this->addColumn('positive');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('3.4 Эффективное управление встречами');
            $this->addColumn('negative');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('3. Управление коммуникациями');
            $this->addColumn('ИТОГО');
            $this->addColumn('combined');
            $this->addColumnRight('0%');
        }
        ////////////////////////////////////////////////
        $this->setBorderBold();
        $this->addSheet("Результативность");

        $this->addRow();
        $this->addColumn('Группа задач', 20);
        $this->addColumn('Результативность, оценка (0-100%)', 20);
        ///////////////////////////////////////////////////////////

        foreach($simulations as $simulation) {
            $this->setInfoBySimulation($simulation);
            $this->addRow();
            $this->addColumn('Срочно');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('Высокий приоритет');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('Средний приоритет');
            $this->addColumnRight('0%');

            $this->addRow();
            $this->addColumn('Двухминутные задачи');
            $this->addColumnRight('0%');
        }
        //////////////////////////////////////////////////////////
        $this->setBorderBold();
        $this->addSheet("Эффект. использования времени");

        $this->addRow();
        $this->addColumn('Группа параметров', 55);
        $this->addColumn('Параметр', 45);
        $this->addColumn('Эффективность использования времени, оценка', 14);
        ////////////////////////////////////////////////////
        $this->addRow();
        $this->addColumn('1. Распределение времени, %');
        $this->addColumn('Продуктивное время (выполнение приоритетных задач)');
        $this->addColumnRight('0%');

        $this->addRow();
        $this->addColumn('1. Распределение времени, %');
        $this->addColumn('Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumnRight('0%');

        $this->addRow();
        $this->addColumn('1. Распределение времени, %');
        $this->addColumn('Время ожидания и бездействия');
        $this->addColumnRight('0%');

        $this->addRow();
        $this->addColumn('2. Сверхурочное время (минуты)');
        $this->addColumn('Сверхурочное время');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Работа с документами');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Встречи');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Звонки');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Работа с почтой');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Планирование');
        $this->addColumnRight('0');
        ////
        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Работа с документами');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Встречи');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Звонки');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Работа с почтой');
        $this->addColumnRight('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Планирование');
        $this->addColumnRight('0');
        ////////////////////////////////////////////////////
        $this->setBorderBold();
        $this->save();

    }
} 