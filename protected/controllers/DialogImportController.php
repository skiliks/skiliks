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
        $result = $service->import('media/ALL_DIALOGUES.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, var_export($result, true), 'text/html');
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
        $service->updateDemo('media/xls/dialogs_demo.csv');
        
        if (false !== $result) {
            $this->_sendResponse(200, 'Done!');
        } else {
            $this->_sendResponse(200, 'Error.');
        }
    }

}

