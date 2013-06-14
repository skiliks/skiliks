<?php

class ScXlsConverter {

    /**
     * @param PHPExcel|string $xls
     * @return array
     * @throws RuntimeException
     */
    public static function extractXlsFile($xls)
    {
        Yii::import('ext.sheetnode.*');
        require_once 'modules/sheetnode_phpexcel/sheetnode_phpexcel.import.inc';

        if ($xls instanceof PHPExcel) {
            $excel = $xls;
        } elseif (is_string($xls) && is_readable($xls)) {
            $excel = PHPExcel_IOFactory::load($xls);
        } else {
            throw new RuntimeException('Specified xls file does not exist or not readable');
        }

        $result = [];
        foreach ($excel->getAllSheets() as $sheet) {
            $result[$sheet->getTitle()] = [
                'name' => $sheet->getTitle(),
                'content' => _sheetnode_phpexcel_import_do($excel, $sheet)
            ];
        }

        return $result;
    }

    /**
     * @param array $sheetsData
     * @param PHPExcel|string $xls
     */
    public static function writeScDataToXls(array $sheetsData, $xls)
    {
        Yii::import('ext.sheetnode.*');
        require_once 'modules/sheetnode_phpexcel/sheetnode_phpexcel.export.inc';
        require_once 'sheetnode.module';
        require_once 'socialcalc.inc';

        if ($xls instanceof PHPExcel) {
            $excel = $xls;
        } else {
            $excel = new PHPExcel();
            $excel->removeSheetByIndex(0);
        }

        foreach ($sheetsData as $sheetData) {
            $sheet = $excel->getSheetByName($sheetData['name']) ?: $excel->createSheet();
            _sheetnode_phpexcel_export_sheet($sheet, $sheetData['name'], socialcalc_parse($sheetData['content']));
        }

        $excel->setActiveSheetIndex(0);

        if (is_string($xls) && file_exists(dirname($xls))) {
            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $writer->save($xls);
        }
    }
}