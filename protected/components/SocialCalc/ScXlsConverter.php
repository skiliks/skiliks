<?php

/**
 * Class ScXlsConverter
 */
class ScXlsConverter
{
    /**
     * Тип xls
     */
    const TYPE_XLS = 'xls';

    /**
     * Тип sc
     */
    const TYPE_SC = 'sc';

    /**
     * Серелизация
     */
    const TYPE_SERIALIZE = 'serialize';

    /**
     * Сохраняет файл xls в sc
     * @param PHPExcel|string $xls
     * @return array структуру sc
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
     * Конвертация sc документа в xls
     * @param array $sheetsData
     * @param null $xlsPath
     * @return PHPExcel
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

            $sheetData['content'] = str_ireplace(
                [
                    ':СУММ(',
                    ':сумм(',
                    ':Сумм(',
                    ':сУмм(',
                    ':суМм(',
                    ':СУмМ(',
                    ':СУМм(',
                    ':сУММ(',
                    ':СуММ(',
                    ':СУмМ(',
                    ':СумМ(',
                    ':СуМм(',
                    ':сУмМ(',
                    ':Сумм(',
                    ':сУмм(',
                    ':суМм(',
                    ':сумМ(',
                    ':СУмм(',
                    ':сУМм(',
                    ':суММ(',
                ]
                , ':SUM(',
                $sheetData['content']
            );

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

    /**
     *
     * @param $filePath
     * @return null|string
     */
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