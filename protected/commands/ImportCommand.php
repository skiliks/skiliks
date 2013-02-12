<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 2/12/13
 * Time: 4:20 PM
 * To change this template use File | Settings | File Templates.
 */
class ImportCommand
{
    public function init() {

    }

    public function run() {
        echo "\nStart 'Import all'. \n";
        $import = new ImportGameDataService();
        $import->importAll();
        echo "\n'Import all' complete. \n";
    }
}
