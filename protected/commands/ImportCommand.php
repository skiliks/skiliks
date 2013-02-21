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

    public function run($args) {
        ini_set('memory_limit','500M');
        $method = isset($args[0]) ? $args[0] : 'All';

        echo "\nStart 'Import $method'. \n";
        $import = new ImportGameDataService();
        $import->{'import'.$method}();
        echo "\n'Import $method' complete. \n";
    }
}
