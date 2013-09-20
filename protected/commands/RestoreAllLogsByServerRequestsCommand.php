<?php
/**
 * Удаляет все файлы которые не являются сводным бюджетом
 * Class DeleteNotD1Command
 */
class RestoreAllLogsByServerRequestsCommand extends CConsoleCommand {

    public $request = null;

    public $simulation = null;

    public $user = null;

    public function actionIndex()
    {
        echo "Начинаем \n";

        $simulation = Simulation::model()->findByPk(5508);
        $simulationRequests = LogServerRequest::model()->findAllByAttributes(['sim_id' => 5508]);

        $this->user = $simulation->user;

        // search unknown API calls {
        foreach ($simulationRequests as $requestObject) {

            // fix data for myDocuments/saveSheet request {
            if (false !== strpos($requestObject->request_url, '/index.php/myDocuments/saveSheet')) {
                $requestObject->request_url = '/index.php/myDocuments/saveSheet';
            }
            // fix data for myDocuments/saveSheet request }

            if (false === isset($this->metdods[$requestObject->request_url])) {
                echo 'Не найден метод '.$requestObject->request_url."\n";
                die();
            }

            $requestObject->refresh(); // fix changes in  myDocuments/saveSheet

            // set request
            $this->request = json_decode($requestObject->request_body, true);
        }
        // search unknown API calls }

        echo "Все запросы проверены. \n";

        foreach ($simulationRequests as $requestObject) {

            // fix data for myDocuments/saveSheet request {
            if (false !== strpos($requestObject->request_url, '/index.php/myDocuments/saveSheet')) {
                $documentId = str_replace('/index.php/myDocuments/saveSheet/', '', $requestObject->request_url);
                $requestObject->request_url = '/index.php/myDocuments/saveSheet';
            }
            // fix data for myDocuments/saveSheet request }

            if (isset($this->metdods[$requestObject->request_url])) {
                // notification
                echo $requestObject->frontend_game_time. ' '.$requestObject->request_url.'    [ '.$requestObject->id." ]\n";

                // set request
                $this->request = json_decode($requestObject->request_body, true);

                // add request data for myDocuments/saveSheet request {
                if (false !== strpos($requestObject->request_url, '/index.php/myDocuments/saveSheet')) {
                    $this->request['document_id'] = $documentId;
                }
                // add request data for myDocuments/saveSheet request }

                Yii::app()->session['gameTime'] = $this->request['time'];

                // call API method
                $this->{$this->metdods[$requestObject->request_url]}();
            } else {
                echo 'Не найден метод '.$requestObject->request_url."\n";
                die();
            }
        }

        echo "Готово! invite: ".$this->simulation->invite->id.'. sim id:'.$this->simulation->id." \n";
    }

    /**
     * Update $log[4]["mailId"] and $log[4]["fileId"]
     */
    public function updateLogsArray() {
        if (isset($this->request['logs'])) {
            foreach ($this->request['logs'] as $log) {
                if (isset($log[4]["mailId"])) {
                    // get new email {
                    $oldEmail = MailBox::model()->findByPk($log[4]["mailId"]);
                    $newEmail = MailBox::model()->findByAttributes([
                        'sim_id' => $this->simulation->id,
                        'template_id' => $oldEmail->template_id,
                    ]);
                    // get new email }
                    $log[4]["mailId"] = $newEmail->id;
                } elseif (isset($log[4]["fileId"])) {
                    // get new document {
                    $oldDocument = MyDocument::model()->findByPk($log[4]["fileId"]);
                    $document = MyDocument::model()->findByAttributes([
                        'sim_id'      => $this->simulation->id,
                        'template_id' => $oldDocument->template_id
                    ]);
                    // get new document }
                    $log[4]["fileId"] = $document->id;
                }
            }
        }
    }

    /**
     * SimulationController->actionStart()
     * @throws LogicException
     */
    public function apiSimulation_Start()
    {
        $mode = $this->request['mode'];
        $type = $this->request['type'];
        $screen_resolution = $this->request['screen_resolution'];
        $window_resolution = $this->request['window_resolution'];

        $invite_id = $this->request['invite_id'];
        $invite = Invite::model()->findByPk($invite_id);

        if (null == $invite) {
            throw new LogicException('You must have invite.');
        }

        $simulation = SimulationService::simulationStart($invite, $mode, $type);
        $simulation->screen_resolution = $screen_resolution;
        $simulation->window_resolution = $window_resolution;

        $simulation->update();

        $this->simulation = $simulation;

        echo ' > Создана новая симуляция: '.$simulation->id."\n";
    }

    /**
     * DayPlanController->actionGet(
     */
    public function apiDayPlan_Get()
    {
        // just getter
        // DayPlanService::getPlanList($this->simulation);
    }

    /**
     * SimulationController->actionStartPause()
     */
    public function apiSimulation_StartPause()
    {
        SimulationService::pause($this->simulation);
    }

    /**
     * TodoController->actionGet()
     */
    public function apiTodo_Get()
    {
        // just getter
        // DayPlanService::getTodoList($this->simulation);
    }

    /**
     * MailController->actionGetInboxUnreadCount()
     */
    public function apiMail_GetInboxUnreadCount()
    {
        // just getter
        // MailBoxService::getFoldersUnreadCount($this->simulation);
    }

    /**
     * CharacterController->actionList()
     */
    public function apiCharacter_List()
    {
        // just getter
    }

    /**
     * EventsController->actionGetState()
     */
    public function apiEvents_GetState()
    {
        $this->updateLogsArray();

        EventsManager::getState(
            $this->simulation,
            isset($this->request['logs']) ? $this->request['logs'] : null,
            isset($this->request['eventsQueueDepth']) ? $this->request['eventsQueueDepth'] : 0
        );
    }

    /**
     * MyDocumentsController->actionGetList()
     */
    public function apiMyDocuments_GetList()
    {
        // just getter
        // MyDocumentsService::getDocumentsList($this->simulation);
    }

    /**
     * MyDocumentsController->actionGetExcel()
     */
    public function apiMyDocuments_GetExcel()
    {
        $oldDocument = MyDocument::model()->findByPk($this->request['id']);

        $document = MyDocument::model()->findByAttributes([
            'sim_id'      => $this->simulation->id,
            'template_id' => $oldDocument->template_id
        ]);
        $document->getSheetList();
    }

    /**
     * SimulationController->actionStopPause(
     */
    public function apiSimulation_StopPause()
    {
        SimulationService::resume($this->simulation);
    }

    /**
     * DayPlanController->actionAdd()
     */
    public function apiDayPlan_Add()
    {
        DayPlanService::addTask(
            $this->simulation,
            $this->request['task_id'],
            $this->request['day'],
            $this->request['date']
        );
    }

    /**
     * DayPlanController->actionDelete()
     */
    public function apiDayPlan_Delete()
    {
        DayPlanService::deleteTask(
            $this->simulation,
            $this->request['task_id']
        );
    }

    /**
     * DialogController->actionGet()
     */
    public function apiDialog_Get()
    {
        $this->updateLogsArray();

        $dialog = new DialogService();
        $dialog->getDialog(
            $this->simulation->id,
            (isset($this->request['dialogId']) ? (int)$this->request['dialogId'] : 0),
            (isset($this->request['time']) ? (int)$this->request['dialogId'] : false)
        );
    }

    /**
     * PhoneController->actionGetList()
     */
    public function apiPhone_GetList()
    {
        // just getter
    }

    /**
     * MyDocumentsController->actionSaveSheet($id)
     */
    public function apiMyDocuments_SaveSheet()
    {
        $oldDocument = MyDocument::model()->findByPk($this->request['document_id']);

        $file = MyDocument::model()->findByAttributes([
            'sim_id'      => $this->simulation->id,
            'template_id' => $oldDocument->template_id,
        ]);

        $content = $this->request['model-content'];
        $name    = $this->request['model-name'];

        $file->setSheetContent($name, $content);
    }

    /**
     * TodoController->actionAdd()
     */
    public function apiTodo_Add()
    {
        DayPlanService::addTask(
            $this->simulation,
            $this->request['taskId'],
            DayPlan::DAY_TODO
        );
    }

    /**
     * MailController->actionGetMessages()
     */
    public function apiMail_GetMessages()
    {
        // just getter
    }

    /**
     * DayPlanController->actionCopyPlan()
     */
    public function apiDayPlan_CopyPlan()
    {
        $minutes = isset($this->request['minutes']) ? (int)$this->request['minutes'] : false;
        DayPlanService::copyPlanToLog($this->simulation, $minutes);
    }

    /**
     * MailController->actionGetThemes()
     */
    public function apiMail_GetThemes()
    {
        // just getter
    }

    /**
     * MailController->actionGetPhrases()
     */
    public function apiMail_GetPhrases()
    {
        // just getter
    }

    /**
     * MailController->actionGetMessage()
     */
    public function apiMail_GetMessage()
    {
        // just getter
    }

    /**
     * PhoneController->actionGetThemes()
     */
    public function apiPhone_GetThemes()
    {
        // just getter
    }

    /**
     * MeetingController->actionGetSubjects()
     */
    public function apiMeeting_GetSubjects()
    {
        // just getter
    }

    /**
     * SimulationController->actionConnect()
     */
    public function apiSimulation_Connect()
    {
        // just getter
    }

    /**
     * EventsController->actionUserSeeWorkdayEndMessage()
     */
    public function apiEvents_UserSeeWorkdayEndMessage()
    {
        // just additional logger
    }

    /**
     * PhoneController->actionCall()
     */
    public function apiPhone_Call()
    {
        PhoneService::call(
            $this->simulation,
            isset($this->request['themeId']) ? $this->request['themeId'] : false,
            isset($this->request['contactId']) ? $this->request['contactId'] : false,
            isset($this->request['time']) ? $this->request['time'] : '00:00'
        );
    }

    /**
     * PhoneController->actionMarkMissedCallsDisplayed()
     */
    public function apiPhone_MarkMissedCallsDisplayed()
    {
        PhoneService::markMissedCallsDisplayed($this->simulation);
    }

    /**
     * PhoneController->actionCallback()
     */
    public function apiPhone_Callback()
    {
        $phone = new PhoneService();

        $phone->callBack(
            $this->simulation,
            isset($this->request['dialog_code']) ? $this->request['dialog_code'] : false
        );
    }

    /**
     * EventsController->actionWait()
     */
    public function apiEvents_Wait()
    {
        EventsManager::waitEvent(
            $this->simulation,
            $this->request['eventCode'],
            $this->request['eventTime']
        );
    }

    /**
     * SimulationController->actionUpdatePause()
     */
    public function apiSimulation_UpdatePause()
    {
        $skipped = $this->request["skipped"];

        SimulationService::update(
            $this->simulation,
            $skipped
        );
    }

    /**
     * SimulationController->actionStop()
     */
    public function apiSimulation_Stop()
    {
        SimulationService::simulationStop(
            $this->simulation,
            isset($this->request['logs']) ? $this->request['logs'] : []
        );
    }

    /**
     * DayPlanController->actionSave()
     */
    public function apiDayPlan_Save()
    {
        DayPlanService::saveToXLS($this->simulation);
    }

    /**
     * MyDocumentsController->actionAdd()
     */
    public function apiMyDocuments_Add()
    {
        $oldDocument = MyDocument::model()->findByPk($this->request['attachmentId']);

        $file = MyDocument::model()->findByAttributes([
            'sim_id'      => $this->simulation->id,
            'template_id' => $oldDocument->template_id,
        ]);

        MyDocumentsService::makeDocumentVisibleInSimulation($this->simulation, $file);
    }

    /**
     * MeetingController->actionLeave()
     */
    public function apiMeeting_Leave()
    {
        MeetingService::leave(
            $this->simulation,
            isset($this->request['id']) ? $this->request['id'] : null
        );
    }

    /**
     * MailController->actionMarkRead()
     */
    public function apiMail_MarkRead()
    {
        $id = isset($this->request['id']) ? (int)$this->request['id'] : 0;
        $oldEmail = MailBox::model()->findByPk($id);
        $newEmail = MailBox::model()->findByAttributes([
            'sim_id' => $this->simulation->id,
            'template_id' => $oldEmail->template_id,
        ]);
        MailBoxService::markReaded($newEmail->id);
    }

    /**
     * MailController->actionReply()
     */
    public function apiMail_Reply()
    {
        $id = isset($this->request['id']) ? (int)$this->request['id'] : 0;
        $oldEmail = MailBox::model()->findByPk($id);
        $newEmail = MailBox::model()->findByAttributes([
            'sim_id' => $this->simulation->id,
            'template_id' => $oldEmail->template_id,
        ]);

        $messageToReply = $newEmail;
        MailBoxService::getMessageData($messageToReply, MailBoxService::ACTION_REPLY);
    }

    /**
     * MailController->actionForward()
     */
    public function apiMail_Forward()
    {
        $id = isset($this->request['id']) ? (int)$this->request['id'] : 0;
        $oldEmail = MailBox::model()->findByPk($id);
        $newEmail = MailBox::model()->findByAttributes([
            'sim_id' => $this->simulation->id,
            'template_id' => $oldEmail->template_id,
        ]);

        $messageToForward = $newEmail;
        MailBoxService::getMessageData($messageToForward, MailBoxService::ACTION_FORWARD);
    }

    /**
     * MailController->actionSendMessage()
     */
    public function apiMail_SendMessage()
    {
        $sendMailOptions = new SendMailOptions($this->simulation);
        $receivers = isset($this->request['receivers']) ? $this->request['receivers'] : '';
        $sendMailOptions->setRecipientsArray($receivers);

        if (false === $sendMailOptions->isValidRecipientsArray()) {
            $this->returnErrorMessage(null, "Не указан хотя бы один получатель!");
        }

        $messageId = isset($this->request['messageId']) ? $this->request['messageId'] : 0;
        // get new email {
        $oldEmail = MailBox::model()->findByPk($messageId);
        if (null !== $oldEmail) {
            $newEmail = MailBox::model()->findByAttributes([
                'sim_id' => $this->simulation->id,
                'template_id' => $oldEmail->template_id,
            ]);
            $messageId = $newEmail->id;
        }

        // get new email }

        // more sendMessagePro options
        $sendMailOptions->simulation   = $this->simulation;
        $sendMailOptions->messageId    = $messageId;
        $sendMailOptions->time         = isset($this->request['time']) ? $this->request['time'] : NULL;
        $sendMailOptions->copies       = isset($this->request['copies']) ? $this->request['copies'] : [];
        $sendMailOptions->phrases      = isset($this->request['phrases']) ? $this->request['phrases'] : [];
        $sendMailOptions->fileId       = isset($this->request['fileId']) ? (int)$this->request['fileId'] : 0;
        $sendMailOptions->subject_id   = isset($this->request['subject']) ? $this->request['subject'] : NULL;

        $sendMailOptions->setLetterType(isset($this->request['letterType']) ? $this->request['letterType'] : NULL);

        // key Action
        MailBoxService::sendMessagePro($sendMailOptions);
    }

    public $metdods = [
        '/index.php/simulation/start' => 'apiSimulation_Start',
        '/index.php/dayPlan/get' => 'apiDayPlan_Get',
        '/index.php/simulation/startPause' => 'apiSimulation_StartPause',
        '/index.php/todo/get' => 'apiTodo_Get',
        '/index.php/mail/getInboxUnreadCount' => 'apiMail_GetInboxUnreadCount',
        '/index.php/character/list' => 'apiCharacter_List',
        '/index.php/events/getState' => 'apiEvents_GetState',
        '/index.php/myDocuments/getList' => 'apiMyDocuments_GetList',
        '/index.php/myDocuments/getExcel' => 'apiMyDocuments_GetExcel',
        '/index.php/simulation/stopPause' => 'apiSimulation_StopPause',
        '/index.php/dayPlan/add' => 'apiDayPlan_Add',
        '/index.php/dayPlan/delete' => 'apiDayPlan_Delete',
        '/index.php/dialog/get' => 'apiDialog_Get',
        '/index.php/phone/getlist' => 'apiPhone_GetList',
        '/index.php/myDocuments/saveSheet' => 'apiMyDocuments_SaveSheet',
        '/index.php/todo/add' => 'apiTodo_Add',
        '/index.php/mail/getMessages' => 'apiMail_GetMessages',
        '/index.php/dayPlan/CopyPlan' => 'apiDayPlan_CopyPlan',
        '/index.php/mail/getThemes' => 'apiMail_GetThemes',
        '/index.php/mail/getPhrases' => 'apiMail_GetPhrases',
        '/index.php/mail/getMessage' => 'apiMail_GetMessage',
        '/index.php/phone/getThemes' => 'apiPhone_GetThemes',
        '/index.php/meeting/getSubjects' => 'apiMeeting_GetSubjects',
        '/index.php/simulation/Connect' => 'apiSimulation_Connect',
        '/index.php/events/userSeeWorkdayEndMessage' => 'apiEvents_UserSeeWorkdayEndMessage',
        '/index.php/phone/call' => 'apiPhone_Call',
        '/index.php/phone/markMissedCallsDisplayed' => 'apiPhone_MarkMissedCallsDisplayed',
        '/index.php/phone/callback' => 'apiPhone_Callback',
        '/index.php/events/wait' => 'apiEvents_Wait',
        '/index.php/simulation/updatePause' => 'apiSimulation_UpdatePause',
        '/index.php/simulation/stop' => 'apiSimulation_Stop',
        '/index.php/dayPlan/save' => 'apiDayPlan_Save',
        '/index.php/myDocuments/add' => 'apiMyDocuments_Add',
        '/index.php/meeting/leave' => 'apiMeeting_Leave',
        '/index.php/mail/MarkRead' => 'apiMail_MarkRead',
        '/index.php/mail/reply' => 'apiMail_Reply',
        '/index.php/mail/forward' => 'apiMail_Forward',
        '/index.php/mail/sendMessage' => 'apiMail_SendMessage',
    ];
}