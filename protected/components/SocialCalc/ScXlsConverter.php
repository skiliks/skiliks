<?php

class ScXlsConverter {

    /**
     * @param PHPExcel|string $xls
     * @return array
     * @throws RuntimeException
     */
    public static function xls2sc($xls)
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
     */
    public static function sc2xls(array $sheetsData, $xlsPath = null)
    {
        Yii::import('ext.sheetnode.*');
        require_once 'modules/sheetnode_phpexcel/sheetnode_phpexcel.export.inc';
        require_once 'sheetnode.module';
        require_once 'socialcalc.inc';

        $excel = new PHPExcel();
        $excel->removeSheetByIndex(0);

        foreach ($sheetsData as $sheetData) {
            $sheet = $excel->getSheetByName($sheetData['name']) ?: $excel->createSheet();
            _sheetnode_phpexcel_export_sheet($sheet, $sheetData['name'], socialcalc_parse($sheetData['content']));
        }

        $excel->setActiveSheetIndex(0);

        if (null !== $xlsPath) {
            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
            $writer->save($xlsPath);
        }

        return $excel;
    }
}