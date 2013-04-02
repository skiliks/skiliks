<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 2/12/13
 * Time: 4:20 PM
 * To change this template use File | Settings | File Templates.
 *
 * php
 */
class ImportCommand extends CConsoleCommand
{
    public function actionIndex($method = 'All', $scenario)
    {
        ini_set('memory_limit', '900M');

        echo "\nStart 'Import $method'. \n";

        $import = new ImportGameDataService($scenario);
        $import->{'import' . $method}();


        echo "\n'Import $method' complete. \n";
    }
}
