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
        $this->setBoldFirstRow();
        $this->addColumn('Наименование Компании', 30);
        $this->addColumn('ФИО', 20);
        $this->addColumn('ID симуляции', 50);
        $this->addColumn('Тип оценки', 30);
        $this->addColumn('Оценка', 30);

        $this->setBorderBold();


        $this->addSheet("Управленческие навыки");

        $this->addRow();
        $this->setBoldFirstRow();
        $this->addColumn('Наименование Компании', 30);
        $this->addColumn('ФИО', 20);
        $this->addColumn('ID симуляции', 50);
        $this->addColumn('Группа навыков', 30);
        $this->addColumn('Навык', 30);
        $this->addColumn('Шкала оценки', 30);
        $this->addColumn('Навык, оценка (0-100%)', 30);

        $this->setBorderBold();


        $this->addSheet("Результативность");

        $this->addRow();
        $this->setBoldFirstRow();
        $this->addColumn('Наименование Компании', 30);
        $this->addColumn('ФИО', 20);
        $this->addColumn('ID симуляции', 50);
        $this->addColumn('Группа задач', 30);
        $this->addColumn('Результативность, оценка (0-100%)', 30);

        $this->setBorderBold();


        $this->addSheet("Эффект. использования времени");

        $this->addRow();
        $this->setBoldFirstRow();
        $this->addColumn('Наименование Компании', 30);
        $this->addColumn('ФИО', 20);
        $this->addColumn('ID симуляции', 50);
        $this->addColumn('Группа параметров', 30);
        $this->addColumn('Парамерт', 30);
        $this->addColumn('Эффективность использования времени, оценка', 30);

        $this->setBorderBold();

        $this->save();

    }
} 