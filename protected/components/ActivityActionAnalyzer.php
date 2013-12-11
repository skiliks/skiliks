<?php
/*
 * @property []UniversalLog $universal_log
 */
/**
 * Class ActivityActionAnalyzer
 */
class ActivityActionAnalyzer {
    /**
     * @var Simulation
     */
    public $simulation;
    /**
     * @var
     */
    public $universal_log;
    /**
     * @var
     */
    public $activities;
    /**
     * @var
     */
    public $activity_categories;
    /**
     * @var
     */
    public $parent_ending;
    /**
     * @var
     */
    public $mail_box;
    /**
     * @var
     */
    public $activity_action;
    /**
     * @var
     */
    public $windows;
    /**
     * @var
     */
    public $documents;

    /**
     * Главная цель: задать $this->universal_log.
     * $this->universal_log - используется дальше, для генерации log_activity_action
     *
     * @param Simulation $simulation
     */
    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
        $dialog_log = new UniversalLog();
        /* @var $universal_log UniversalLog */
        $universal_logs = UniversalLog::model()->findAllByAttributes(['sim_id'=>$simulation->id]);

        foreach($universal_logs as $key => $universal_log){

            /**
             * Особым образом обрабатываются диалоги
             *
             * Предобработка логов диалогов.
             * Для каждого диалога у нас несколько записей в universal_log.
             * Но для создания лога LegActions (Activity actions) нам нужна только последняя реплика.
             * Чтоб не портить universal_log - мы добавляем, но не сохраняем,
             * dialog_log с правильными start_time и end_time.
             */
            if(null !== $universal_log->replica_id) {

                $dialog_log->replica_id = $universal_log->replica_id;
                $dialog_log->start_time = (null === $dialog_log->start_time)?$universal_log->start_time:$dialog_log->start_time;
                $dialog_log->end_time = $universal_log->end_time;
                $dialog_log->window_id = $universal_log->window_id;
                $dialog_log->window_uid = $universal_log->window_uid;
                $dialog_log->last_dialog_id = $universal_log->last_dialog_id;

                if (isset($universal_logs[$key+1])) {
                    if (null === $universal_logs[$key+1]->replica_id) {
                        $this->appendUniversalLog($dialog_log);
                        $dialog_log = new UniversalLog();
                    }
                } else {
                    $this->appendUniversalLog($dialog_log);
                    $dialog_log = new UniversalLog();
                }
             } else {
                /**
                 * Все прочие окна (не диалоги)
                 */
                $this->appendUniversalLog($universal_log);
            }
        }

        foreach(Activity::model()->findAllByAttributes(['scenario_id'=>$simulation->game_type->id]) as $activity) {
            $this->activities[$activity->id] = $activity;
        }
        /* @var $activity_category ActivityCategory */
        foreach(ActivityCategory::model()->findAll(['order'=>'priority asc']) as $activity_category){
            $this->activity_categories[$activity_category->code] = $activity_category->priority;
        }
        /* @var $parent SimulationCompletedParent */
        foreach(SimulationCompletedParent::model()->findAllByAttributes(['sim_id'=>$simulation->id]) as $parent) {
            $this->parent_ending[$parent->parent_code] = $parent->end_time;
        }
        /* @var $mail MailBox */
        foreach(MailBox::model()->findAllByAttributes(['sim_id'=>$simulation->id]) as $mail) {
            $this->mail_box[$mail->id] = $mail;
        }
        /* @var $activity_action ActivityAction */
        foreach(ActivityAction::model()->findAllByAttributes(['scenario_id'=>$simulation->game_type->id]) as $activity_action) {

            if(null !== $activity_action->mail_id) {
                $this->activity_action['mail_id'][$activity_action->mail_id][] = $activity_action;
            } elseif(null !== $activity_action->document_id) {
                $this->activity_action['document_id'][$activity_action->document_id][] = $activity_action;
            } elseif(null !== $activity_action->dialog_id) {
                $this->activity_action['dialog_id'][$activity_action->dialog_id][] = $activity_action;
            } elseif(null !== $activity_action->meeting_id) {
                $this->activity_action['meeting_id'][$activity_action->meeting_id][] = $activity_action;
            } elseif(null !== $activity_action->window_id) {
                $this->activity_action['window_id'][$activity_action->window_id][] = $activity_action;
            } else {
                if($this->activities[$activity_action->activity_id]->code === 'A_wrong_call'){
                    $this->activity_action['A_wrong_call'] = $activity_action;
                }elseif($this->activities[$activity_action->activity_id]->code === 'A_not_sent'){
                    $this->activity_action['A_not_sent'] = $activity_action;
                }elseif($this->activities[$activity_action->activity_id]->code === 'A_incorrect_sent'){
                    $this->activity_action['A_incorrect_sent'] = $activity_action;
                }else{
                    throw new Exception("not found activity action");
                }
            }

        }

        /* @var $window Window */
        foreach(Window::model()->findAll() as $window) {
            $this->windows[$window->subtype] = $window->id;
        }

        /* @var $document MyDocument */
        foreach(MyDocument::model()->findAllByAttributes(['sim_id'=>$simulation->id]) as $document) {
            $this->documents[$document->id] = $document->template_id;
        }

    }

    /**
     * Create all activity action logs
     */
    public function run() {
        /* @var $universal_log UniversalLog */
        foreach($this->universal_log as $key => $universal_log){
            $activityActions = $this->findActivityActionByLog($universal_log);
            $activityActionsActual = $this->excludeParentComplete($activityActions, $universal_log);
            $activityAction = $this->getHighPriorityCategory($activityActionsActual, $universal_log);
            if(null !== $activityAction){
                $this->saveLogActivityAction($activityAction, $universal_log);
            }
        }
    }

    /**
     * Ищет совпадение логов в LegAction Detail
     * @param UniversalLog $log
     * @return array ActivityAction
     * @throws Exception
     */
    public function findActivityActionByLog(UniversalLog $log) {
        if(null !== $log->mail_id) {
            $mail_box = $this->mail_box[$log->mail_id];
            /* @var $mail_box MailBox */
            if($mail_box->isInBox()){
                return $this->activity_action['mail_id'][$mail_box->template_id];
            }elseif($mail_box->isOutBox()){
                if($mail_box->isMS()){
                    return $this->activity_action['mail_id'][$mail_box->template_id];
                } else {
                    if($mail_box->isSended()){
                        return [$this->activity_action['A_incorrect_sent']];
                    } else {
                        return [$this->activity_action['A_not_sent']];
                    }
                }
            }else{
                throw new Exception(" Bad mail folder ");
            }
        } elseif(null !== $log->file_id) {
            return $this->activity_action['document_id'][$this->documents[$log->file_id]];
        } elseif(null !== $log->replica_id) {
            return $this->activity_action['dialog_id'][$log->replica_id];
        } elseif(null !== $log->meeting_id) {
            return $this->activity_action['meeting_id'][$log->meeting_id];
        } elseif($log->window_id === $this->windows[Window::MAIL_NEW]) {
            return [$this->activity_action['A_not_sent']];
        }elseif($log->window_id === $this->windows[Window::PHONE_TALK]) {
            return [$this->activity_action['A_wrong_call']];
        }elseif(null !== $log->window_id) {
            return $this->activity_action['window_id'][$log->window_id];
        }
        throw new Exception("activity action not found");
    }

    /**
     * Исключает Перенты что завершились
     * @param array $activityActions
     * @param UniversalLog $universal_log
     * @return mixed
     */
    public function excludeParentComplete(array $activityActions, UniversalLog $universal_log) {
        /* @var $activityActions []ActivityAction */
        /* @var $activityAction ActivityAction */
        foreach($activityActions as $key => $activityAction) {
            $parent = $this->activities[$activityAction->activity_id];
            if(isset($this->parent_ending[$parent->parent]) && strtotime($universal_log->start_time) >= strtotime($this->parent_ending[$parent->parent])) {
               unset($activityActions[$key]);
            }
        }

        return $activityActions;

    }

    /**
     * Берёт самую приоритетную категорию
     * @param ActivityAction[] $activityActions
     * @param UniversalLog $universal_log
     * @return ActivityAction
     * @throws Exception
     */
    public function getHighPriorityCategory(array $activityActions, UniversalLog $universal_log) {

        $priority = [];
        /* @var $this->activities[$activityAction->activity_id] Activity */
        foreach($activityActions as $index => $activityAction) {
            if(!isset($priority[$this->activity_categories[$this->activities[$activityAction->activity_id]->category_id]])){
                $priority[$this->activity_categories[$this->activities[$activityAction->activity_id]->category_id]] = $index;
            }
        }

        if(false === ksort($priority)){
            throw new Exception('Sort fail');
        }
        $key = current($priority);
        if(false === $key){
            if(count($priority) === 0){
                return $this->activity_action['window_id'][$universal_log->window_id][0];
            }else{
                throw new Exception("array error");
            }
        }else{
            return $activityActions[$key];
        }
    }

    /**
     * Добавляет UniversalLog в массив логов
     * @param UniversalLog $universal_log
     */
    public function appendUniversalLog(UniversalLog $universal_log) {

        if((strtotime($universal_log->end_time) - strtotime($universal_log->start_time)) !== 0){
            $this->universal_log[] = $universal_log;
        }
    }

    /**
     * Сохраняет лог действий пользователя ввиде LogActivityAction
     * @param ActivityAction $activityAction
     * @param UniversalLog $universal_log
     */
    public function saveLogActivityAction(ActivityAction $activityAction, UniversalLog $universal_log) {
        $logActivityAction = new LogActivityAction(); //new LogActivityAction();
        $logActivityAction->sim_id = $this->simulation->id;
        $logActivityAction->activity_action_id = $activityAction->id;
        $logActivityAction->window = $universal_log->window_id;
        $logActivityAction->start_time = $universal_log->start_time;
        $logActivityAction->end_time = $universal_log->end_time;
        $logActivityAction->mail_id = $universal_log->mail_id;
        $logActivityAction->document_id = $universal_log->file_id;
        $logActivityAction->meeting_id = $universal_log->meeting_id;
        $logActivityAction->window_uid = $universal_log->window_uid;
        $logActivityAction->save(false);
    }


} 