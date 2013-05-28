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

        // update file name (version) only after import done {
        $scenario = Scenario::model()->findByAttributes(['slug' => $scenario]);
        if (null !== $scenario) {
            $scenario->filename = basename($this->filename);
            $scenario->save(false);
        }
        // update file name (version) only after import done }

        echo "\n'Import $method' complete. \n";
    }
}
