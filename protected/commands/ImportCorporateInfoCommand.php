<?php

class ImportCorporateInfoCommand extends CConsoleCommand
{
    public function actionIndex() // 7 days
    {
        $import = new ImportCorporateInfo();
        $import->import();
    }
}