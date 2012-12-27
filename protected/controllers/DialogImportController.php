<?php

/**
 * Description of DialogImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogImportController extends AjaxController
{
    public function actionImport()
    {
        $service = new DialogImportService();
        $result = $service->import();
        
        $html = sprintf(
            'Lines %s [must be 821] <br/>
            Columns 137 [must be 137] <br/>
            <br/>
            Marks codes amount: %s [must be 114] <br/>
            <br/>
                Marks "0": %s [must be 408] <br/>
                Marks "1": %s [must be 620] <br/>
                tolal %s [1028] <br/>
            ',
            $result['replics'],
            $result['pointCodes'],
            $result['zeros'],
            $result['ones'],
            $result['zeros']+$result['ones']
        );
        
        if (false !== $result) {
            $this->_sendResponse(200, $html, 'text/html');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionImportEvents()
    {
        $service = new DialogImportService();
        $result = $service->importEvents('media/xls/scenario.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionImportReplica()
    {
        $service = new DialogImportService();
        $result = $service->importReplica('media/xls/scenario.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionImportFlags()
    {
        $service = new DialogImportService();
        $result = $service->importFlags('media/xls/scenario.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionImportFlagRules()
    {
        $service = new DialogImportService();
        $result = $service->importFlagRules('media/flag-rules.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, $result);
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    /**
     * WTF?
     */
    public function actionImportFlagRuless()
    {
        $service = new DialogImportService();
        $result = $service->importFlagRules('media/flag_rules.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, $result);
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionImportText()
    {
        $service = new DialogImportService();
        $result = $service->importText('media/xls/scenario.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionUpdateFiles()
    {
        $service = new DialogImportService();
        $service->updateFiles('media/xls/scenario4.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

    public function actionUpdateDemo()
    {
        $service = new DialogImportService();
        $service->updateDemo();
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

}

