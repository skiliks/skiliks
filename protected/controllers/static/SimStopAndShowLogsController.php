<?php
/**
 * По идее это устаревшая админка.
 * Её надо будет выпилить
 */

use application\components\Logging\LogTableList as LogTableList;
class SimStopAndShowLogsController extends SiteBaseController
{
    /**
     * SimStop and show logs
     */
    public function actionDisplayLog()
    {
        $simId = Yii::app()->request->getParam('simulation');
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);

        assert($simulation !== null);

        $this->layout = 'sim_stop_adn_show_logs';
        $logTableList = new LogTableList($simulation);

        $this->render('log', [
            'simulation' => $simulation,
            'log_tables' => $logTableList->asArray()
        ]);
    }

    /**
     * Save logs to excel on page "SimStop and show logs"
     */
    public function actionSaveLog()
    {
        $simId = Yii::app()->request->getParam('simulation');
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);
        $logTableList = new LogTableList($simulation);
        $excelWriter = $logTableList->asExcel();
        $filename = sprintf('Log_%s_%s', $simulation->primaryKey, date("Y-m-d"));
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"$filename.xlsx\"");
        $excelWriter->save('php://output');
    }

    public function actionLog()
    {
//        $send_json = true;
//        $action = array(
//            'type' => Yii::app()->request->getParam('type', 'DialogDetail'),
//            'data' => (string)Yii::app()->request->getParam('data', 'json'),
//            'params' => array('order_col')
//        );
//        $result = array('result' => 1, 'message' => "Done");
//        if (isset($action['type'])) {
//            $method = "get{$action['type']}";
//            if (method_exists('LogHelper', $method)) {
//                if (isset($action['data'])) {
//                    if (isset($action['params']) AND is_array($action['params'])) {
//
//                        $db_data = LogHelper::$method($action['data']);
//                        if (is_array($db_data)) {
//                            $result += $db_data;
//                        } else {
//                            $send_json = false;
//                        }
//
//                    } else {
//                        throw new Exception("Не указаны параметры!");
//                    }
//
//                } else {
//                    throw new Exception("Не указан тип результата!");
//                }
//            } else {
//                throw new Exception("Не найдено действие!");
//            }
//        } else {
//            throw new Exception("Не указан тип действия!");
//        }
//        if ($send_json) {
//            $this->sendJSON($result);
//        }
    }


    public function actionDialogsAnalyzer()
    {
//        $this->checkUser();
//
//        error_reporting(E_ALL);
//        ini_set('display_errors', '1');
//
//        $sDialogs = Dialog::model()->findAll();
//
//        $eReplicas = Replica::model()->findAll();
//
//        $sEmails = MailTemplate::model()->findAll();
//
//        $sEvents = EventSample::model()->findAll(
//            " code NOT LIKE 'MS%' AND code NOT LIKE 'P%' ORDER BY trigger_time ASC "
//        );
//
//        $sFlagsBlockDialog  = FlagBlockDialog::model()->findAll();
//        $sFlagsBlockReplica = FlagBlockReplica::model()->findAll();
//        $sFlagsBlockMail    = FlagBlockMail::model()->findAll();
//        $sFlagsRunMail      = FlagRunMail::model()->findAll();
//
//        $sHeroBehaviours = HeroBehaviour::model()->findAll();
//        $sReplicaPoints  = ReplicaPoint::model()->findAll();
//        $sMailPoints     = MailPoint::model()->findAll();
//
//        $a = new GameContentAnalyzer();
//
//        $a->uploadDialogs($sDialogs);
//        $a->uploadReplicas($eReplicas);
//        $a->uploadEmails($sEmails);
//        $a->uploadEvents($sEvents);
//        $a->uploadFlags($sFlagsBlockDialog, $sFlagsBlockReplica, $sFlagsBlockMail, $sFlagsRunMail);
//
//        $a->uploadPoints($sHeroBehaviours, $sReplicaPoints, $sMailPoints);
//
//        // update statistic
//        $a->updateProducedBy();
//        $a->updateDelays();
//
//        $a->updatePossibleNextEvents();
//
//        // organize data for output
//        $a->separateEvents();
//        $a->initHoursChain();
//        $a->buildTimeChains();
//        $a->updateAEventsDurations();
//
//        $this->layout = 'admin';
//        $this->render('dialogs_analyzer',
//            [
//                'analyzer'     => $a,
//                'sourceName'   => 'база данных',
//                'isDbMode'     => true,
//            ]
//        );
    }

    public function actionUploadDialogsToAnalyzer()
    {
//        $this->checkUser();
//        /**
//         * No comments :)
//         */
//        echo '<html>
//            <body>
//
//            <form action="/admin/uploadedFileAnalyzer" method="post"
//            enctype="multipart/form-data">
//                <label for="file">Filename:</label>
//                <input type="file" name="file" id="file"><br>
//                <input type="submit" name="submit" value="Analyze">
//            </form>
//
//            </body>
//            </html>';
//        Yii::app()->end(); // кошерное die
    }

    public function actionUploadedFileAnalyzer()
    {
//        $this->checkUser();
//
//        if ($_FILES["file"]["error"] > 0)
//        {
//            // pattern "Sparta": do it or die!
//            echo "Error: " . $_FILES["file"]["error"] . "<br>";
//            Yii::app()->end(); // кошерное die
//        }
//
//        error_reporting(E_ALL);
//        ini_set('display_errors', '1');
//
//        $importGameContentAnalyzerDataService = new ImportGameContentAnalyzerDataService();
//        $importGameContentAnalyzerDataService->setFilename($_FILES["file"]["tmp_name"]);
//
//        $sDialogs = $importGameContentAnalyzerDataService->importDialogs();
//
//        $eReplicas = $importGameContentAnalyzerDataService->importDialogReplicas();
//
//        $sEmails = $importGameContentAnalyzerDataService->importEmails();
//
//        $sEvents = $importGameContentAnalyzerDataService->importEventSamples();
//
//        $indexedEvents = [];
//        foreach ($sEvents as $sEvent) {
//            $indexedEvents[$sEvent->code] = $sEvent;
//        }
//
//        $sFlags = $importGameContentAnalyzerDataService->importFlagsRules($indexedEvents);
//        $sFlagsBlockDialog  = $sFlags['BlockDialog'];
//        $sFlagsBlockReplica = $sFlags['BlockReplica'];
//        $sFlagsBlockMail    = $sFlags['BlockMail'];
//        $sFlagsRunMail      = $sFlags['RunMail'];
//
//        $a = new GameContentAnalyzer();
//
//        $a->uploadDialogs($sDialogs);
//        $a->uploadReplicas($eReplicas);
//        $a->uploadEmails($sEmails);
//        $a->uploadEvents($sEvents);
//        $a->uploadFlags(
//            $sFlagsBlockDialog,
//            $sFlagsBlockReplica,
//            $sFlagsBlockMail,
//            $sFlagsRunMail,
//            GameContentAnalyzer::FLAGS_FROM_EXCEL_FILE
//        );
//
//        // update statistic
//        $a->updateProducedBy();
//        $a->updateDelays();
//
//        $a->updatePossibleNextEvents();
//
//        // organize data for output
//        $a->separateEvents();
//        $a->initHoursChain();
//        $a->buildTimeChains();
//        $a->updateAEventsDurations();
//
//        $this->layout = 'admin';
//        $this->render('dialogs_analyzer',
//            [
//                'analyzer'     => $a,
//                'sourceName'   => $_FILES["file"]["name"],
//                'isDbMode'     => false,
//            ]
//        );
    }
}
