<?php

class ScXlsConverter
{
    const TYPE_XLS = 'xls';

    const TYPE_SC = 'sc';

    const TYPE_SERIALIZE = 'serialize';

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
            $result[$sheet->getTitle()] = array (
                'name' => $sheet->getTitle(),
                'content' => _sheetnode_phpexcel_import_do($excel, $sheet)
            );
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
            if (NULL === $sheetData['name']) {
                continue;
            }

            $sheetData['name'] = str_replace('СУММ', 'SUM', $sheetData['name']);

            $sheet = $excel->getSheetByName($sheetData['name']) ?: $excel->createSheet();
            _sheetnode_phpexcel_export_sheet($sheet, $sheetData['name'], socialcalc_parse($sheetData['content']));
        }

        $excel->setActiveSheetIndex(0);

        if (null === $xlsPath) {
            $xlsPath = tempnam('/tmp', 'excel_');
        }

        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $writer->save($xlsPath);

        // We need this hack due to wrong export of empty cells
        $excel = PHPExcel_IOFactory::load($xlsPath);

        return $excel;
    }

    public static function getType($filePath)
    {
        if (is_file($filePath)) {
            if (PHPExcel_IOFactory::identify($filePath)) {
                return self::TYPE_XLS;
            }

            $content = file_get_contents($filePath);
            if (0 === strpos($content, 'socialcalc:version:1.0')) {
                return self::TYPE_SC;
            }

            if (false !== unserialize($content)) {
                return self::TYPE_SERIALIZE;
            }
        }

        return null;
    }
}