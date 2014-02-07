<?php

class ImportCorporateInfo {
    public $columnNoByName = [];
    public function import()
    {
        $reader = PHPExcel_IOFactory::createReader('Excel2007');

        // prevent read string "11:00" like "0.45833333333333" even by getValue()
        $reader->setReadDataOnly(true);
        $excel = $reader->load(__DIR__.'/../migrations/data/corporate_accounts.xlsx');

        $sheet = $excel->getSheetByName("Все");
        $this->setColumnNumbersByNames($sheet);
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $email = $this->getCellValue($sheet, 'Email', $i);

            /* @var $profile YumProfile */
            $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
            if(null === $profile) {
                continue;
            }
            if($profile->user->isCorporate() === false){
                continue;
            }

            $profile->user->account_corporate->site = $this->getCellValue($sheet, 'Домен', $i);
            $profile->user->account_corporate->company_name_for_sales = $this->getCellValue($sheet, 'Название компании', $i);
            $profile->user->account_corporate->description_for_sales = $this->getCellValue($sheet, 'Описание', $i);
            $profile->user->account_corporate->industry_for_sales = $this->getCellValue($sheet, 'Отрасль', $i);
            $profile->user->account_corporate->save(false);
            echo $email."\r\n";
        }

        return false;
    }

    private function getCellValue($sheet, $columnName, $i, $increment = 0)
    {
        return $sheet->getCellByColumnAndRow(
            $this->columnNoByName[$columnName] + $increment,
            $i->key()
        )->setDataType(PHPExcel_Cell_DataType::TYPE_STRING)->getCalculatedValue();
    }

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
} 