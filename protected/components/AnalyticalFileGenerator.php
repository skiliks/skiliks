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
            $sheet->getColumnDimensionByColumn($this->column_number)
                ->setWidth($width);
        }
        $this->column_number++;
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
            $this->addColumn('');
            $this->addColumn('Смирнова Марина');
            $this->addColumn('9606');
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

    /**
     *
     */
    public function run() {
        $this->createDocument();

        $this->addSheet("Итоговый рейтинг");

        $this->addRow();

        $this->addColumn('Тип оценки', 40);
        $this->addColumn('Оценка', 14);
        $this->setBorderBold();
        ////////////////////////////////////////////////////
        $this->addRow();
        $this->addColumn('Управленческие навыки');
        $this->addColumn('26,91%');

        $this->addRow();
        $this->addColumn('Результативность');
        $this->addColumn('26,91%');

        $this->addRow();
        $this->addColumn('Эффективность использования времени');
        $this->addColumn('26,91%');

        $this->addRow();
        $this->addColumn('Итоговый рейтинг');
        $this->addColumn('26,91%');

        $this->addRow();
        $this->addColumn('Процентиль');
        $this->addColumn('26,91%');
        /////////////////////////////////////////////////////
        $this->setBorderBold();


        $this->addSheet("Управленческие навыки");

        $this->addRow();

        $this->addColumn('Группа навыков', 42);
        $this->addColumn('Навык', 50);
        $this->addColumn('Шкала оценки', 14);
        $this->addColumn('Навык, оценка (0-100%)', 18);

        $this->setBorderBold();
        ////////////////////////////////////////////////
        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.1 Использование планирования в течение дня');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.1 Использование планирования в течение дня');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.2 Правильное определение приоритетов задач при планировании');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.3 Выполнение задач в соответствии с приоритетами');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.4 Прерывание при выполнении задач');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('1.4 Прерывание при выполнении задач');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Управление задачами с учётом приоритетов');
        $this->addColumn('ИТОГО');
        $this->addColumn('combined');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('2.1 Использование делегирования для управления объемом задач');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('2.1 Использование делегирования для управления объемом задач');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('2.2 Управление ресурсами различной квалификации');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('2.2 Управление ресурсами различной квалификации');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('2.3 Использование обратной связи');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('2.3 Использование обратной связи');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Управление людьми');
        $this->addColumn('ИТОГО');
        $this->addColumn('combined');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.1 Оптимальное использование каналов коммуникации');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.2 Эффективная работа с почтой');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.2 Эффективная работа с почтой');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.3 Эффективная работа со звонками');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.3 Эффективная работа со звонками');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.4 Эффективное управление встречами');
        $this->addColumn('positive');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('3.4 Эффективное управление встречами');
        $this->addColumn('negative');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('3. Управление коммуникациями');
        $this->addColumn('ИТОГО');
        $this->addColumn('combined');
        $this->addColumn('0%');

        ////////////////////////////////////////////////
        $this->setBorderBold();
        $this->addSheet("Результативность");

        $this->addRow();
        $this->addColumn('Группа задач', 20);
        $this->addColumn('Результативность, оценка (0-100%)', 20);
        ///////////////////////////////////////////////////////////
        $this->addRow();
        $this->addColumn('Срочно');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('Высокий приоритет');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('Средний приоритет');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('Двухминутные задачи');
        $this->addColumn('0%');
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
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Распределение времени, %');
        $this->addColumn('Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('1. Распределение времени, %');
        $this->addColumn('Время ожидания и бездействия');
        $this->addColumn('0%');

        $this->addRow();
        $this->addColumn('2. Сверхурочное время (минуты)');
        $this->addColumn('Сверхурочное время');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Работа с документами');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Встречи');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Звонки');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Работа с почтой');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.1 Продуктивное время (выполнение приоритетных задач, минуты)');
        $this->addColumn('Планирование');
        $this->addColumn('0');
        ////
        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Работа с документами');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Встречи');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Звонки');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Работа с почтой');
        $this->addColumn('0');

        $this->addRow();
        $this->addColumn('1.2 Непродуктивное время (иные действия, не связанные с приоритетами)');
        $this->addColumn('Планирование');
        $this->addColumn('0');
        ////////////////////////////////////////////////////
        $this->setBorderBold();
        $this->save();

    }
} 