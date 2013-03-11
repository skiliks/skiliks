<?php
use application\components\Logging\LogTableList as LogTableList;
class AdminController extends AjaxController
{

    public function actionDisplayLog()
    {
        $simId = Yii::app()->request->getParam('simulation');
        /** @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($simId);

        assert($simulation);

        $this->layout = 'admin';
        $logTableList = new LogTableList($simulation);

        $this->render('log', [
            'simulation' => $simulation,
            'log_tables' => $logTableList->asArray()
        ]);
    }

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
        $send_json = true;
        $action = array(
            'type' => Yii::app()->request->getParam('type', 'DialogDetail'),
            'data' => (string)Yii::app()->request->getParam('data', 'json'),
            'params' => array('order_col')
        );
        $result = array('result' => 1, 'message' => "Done");
        if (isset($action['type'])) {
            $method = "get{$action['type']}";
            if (method_exists('LogHelper', $method)) {
                if (isset($action['data'])) {
                    if (isset($action['params']) AND is_array($action['params'])) {

                        $db_data = LogHelper::$method($action['data']);
                        if (is_array($db_data)) {
                            $result += $db_data;
                        } else {
                            $send_json = false;
                        }

                    } else {
                        throw new Exception("Не указаны параметры!");
                    }

                } else {
                    throw new Exception("Не указан тип результата!");
                }
            } else {
                throw new Exception("Не найдено действие!");
            }
        } else {
            throw new Exception("Не указан тип действия!");
        }
        if ($send_json) {
            $this->sendJSON($result);
        }
    }


    public function actionDialogsAnalyzer()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $sDialogs = Dialog::model()->findAll();

        $eReplicas = Replica::model()->findAll();

        $sEmails = MailTemplate::model()->findAll();

        $sEvents = EventSample::model()->findAll(
            " code NOT LIKE 'MS%' AND code NOT LIKE 'P%' ORDER BY trigger_time ASC "
        );

        $sFlagsBlockDialog  = FlagBlockDialog::model()->findAll();
        $sFlagsBlockReplica = FlagBlockReplica::model()->findAll();
        $sFlagsBlockMail    = FlagBlockMail::model()->findAll();
        $sFlagsRunMail      = FlagRunMail::model()->findAll();

        $sHeroBehaviours = HeroBehaviour::model()->findAll();
        $sReplicaPoints  = ReplicaPoint::model()->findAll();
        $sMailPoints     = MailPoint::model()->findAll();

        $a = new GameContentAnalyzer();

        $a->uploadDialogs($sDialogs);
        $a->uploadReplicas($eReplicas);
        $a->uploadEmails($sEmails);
        $a->uploadEvents($sEvents);
        $a->uploadFlags($sFlagsBlockDialog, $sFlagsBlockReplica, $sFlagsBlockMail, $sFlagsRunMail);

        $a->uploadPoints($sHeroBehaviours, $sReplicaPoints, $sMailPoints);

        // update statistic
        $a->updateProducedBy();
        $a->updateDelays();

        $a->updatePossibleNextEvents();

        // organize data for output
        $a->separateEvents();
        $a->initHoursChain();
        $a->buildTimeChains();
        $a->updateAEventsDurations();

        $this->layout = 'admin';
        $this->render('dialogs_analyzer',
            [
                'analyzer'     => $a,
                'sourceName'   => 'база данных',
                'isDbMode'     => true,
            ]
        );
    }

    public function actionUploadDialogsToAnalyzer() {
        /**
         * No comments :)
         */
        echo '<html>
            <body>

            <form action="/admin/uploadedFileAnalyzer" method="post"
            enctype="multipart/form-data">
                <label for="file">Filename:</label>
                <input type="file" name="file" id="file"><br>
                <input type="submit" name="submit" value="Analyze">
            </form>

            </body>
            </html>';
        die;
    }

    public function actionUploadedFileAnalyzer()
    {
        if ($_FILES["file"]["error"] > 0)
        {
            // pattern "Sparta": do it or die!
            die("Error: " . $_FILES["file"]["error"] . "<br>");
        }

        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $importGameContentAnalyzerDataService = new ImportGameContentAnalyzerDataService();
        $importGameContentAnalyzerDataService->setFilename($_FILES["file"]["tmp_name"]);

        $sDialogs = $importGameContentAnalyzerDataService->importDialogs();

        $eReplicas = $importGameContentAnalyzerDataService->importDialogReplicas();

        $sEmails = $importGameContentAnalyzerDataService->importEmails();

        $sEvents = $importGameContentAnalyzerDataService->importEventSamples();

        $indexedEvents = [];
        foreach ($sEvents as $sEvent) {
            $indexedEvents[$sEvent->code] = $sEvent;
        }

        $sFlags = $importGameContentAnalyzerDataService->importFlagsRules($indexedEvents);
        $sFlagsBlockDialog  = $sFlags['BlockDialog'];
        $sFlagsBlockReplica = $sFlags['BlockReplica'];
        $sFlagsBlockMail    = $sFlags['BlockMail'];
        $sFlagsRunMail      = $sFlags['RunMail'];

        $a = new GameContentAnalyzer();

        $a->uploadDialogs($sDialogs);
        $a->uploadReplicas($eReplicas);
        $a->uploadEmails($sEmails);
        $a->uploadEvents($sEvents);
        $a->uploadFlags(
            $sFlagsBlockDialog,
            $sFlagsBlockReplica,
            $sFlagsBlockMail,
            $sFlagsRunMail,
            GameContentAnalyzer::FLAGS_FROM_EXCEL_FILE
        );

        // update statistic
        $a->updateProducedBy();
        $a->updateDelays();

        $a->updatePossibleNextEvents();

        // organize data for output
        $a->separateEvents();
        $a->initHoursChain();
        $a->buildTimeChains();
        $a->updateAEventsDurations();

        $this->layout = 'admin';
        $this->render('dialogs_analyzer',
            [
                'analyzer'     => $a,
                'sourceName'   => $_FILES["file"]["name"],
                'isDbMode'     => false,
            ]
        );

    }

    public function actionIndex()
    {
        $assetsUrl = $this->getAssetsUrl();

        $config = Yii::app()->params['public'];
        $config['assetsUrl'] = $assetsUrl;

        Yii::app()->clientScript->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css')
            ->registerCssFile($assetsUrl . '/js/bootstrap/css/bootstrap.css')
            ->registerCssFile($assetsUrl . '/js/jgrid/css/ui.multiselect.css')
            ->registerCssFile($assetsUrl . '/js/jgrid/css/ui.jqgrid.css')
            ->registerCssFile($assetsUrl . '/js/jgrid/css/jquery-ui-1.8.2.custom.css');

        Yii::app()->clientScript->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js')
            ->registerScriptFile($assetsUrl . "/js/jquery/jquery-ui-1.8.24.custom.js")
            ->registerScriptFile($assetsUrl . "/js/jquery/jquery.hotkeys.js")
            ->registerScriptFile($assetsUrl . "/js/jquery/jquery.balloon.js")
            ->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap.js")
            ->registerScriptFile($assetsUrl . "/js/jgrid/js/jquery.jqGrid.min.js")
            ->registerScriptFile($assetsUrl . "/js/jgrid/js/i18n/grid.locale-ru.js")
            ->registerScriptFile($assetsUrl . "/js/game/lib/php.js");
        // ->registerScriptFile($assetsUrl . "/js/game/adminka/skiliks/engine_loader.js")

        $jsScriptsAtTheEndOfBody = '';
        $scripts = [
            "js/game/adminka/config.js",
            "js/game/adminka/jgridController.js",
            "js/game/adminka/frame_switcher.js",
            /*"js/game/lib/messages.js",*/
            /*"js/game/mouse.js",*/
            /*"js/game/game_logic.js",*/
            "js/game/adminka/sender.js",
            "js/game/adminka/receiver.js",
            "js/game/adminka/loading.js",
            "js/game/adminka/php.js",
            "js/game/adminka/menu_main.js",
            "js/game/adminka/world.js",
            "js/game/adminka/skiliks/characters_points_titles/characters_points_titles.js",
            "js/game/adminka/skiliks/dialog_branches/dialog_branches.js",
            "js/game/adminka/skiliks/dialogs/dialogs.js",
            "js/game/adminka/skiliks/events_results/events_results.js",
            "js/game/adminka/skiliks/events_samples/events_samples.js",
            "js/game/adminka/skiliks/events_choices/events_choices.js",
            "js/game/adminka/skiliks/scenario/scenario.js",
            "js/game/adminka/skiliks/logging/logging.js",
            "js/game/adminka/starter.js",
        ];
        /* Please, read the docs. It can be do using registerScriptFile */
        foreach ($scripts as $path) {
            $jsScriptsAtTheEndOfBody .= sprintf(
                '<script type="text/javascript" src="%s/%s"></script>', $assetsUrl, $path
            );
        }
        $this->layout = false;
        $this->render(
            'index',
            [
                'config'    => CJSON::encode($config),
                'assetsUrl' => $assetsUrl,
                'jsScripts' => $jsScriptsAtTheEndOfBody,
            ]
        );
    }
}
