<?php
/*
 * @property []UniversalLog $universal_log
 */
class ActivityActionAnalyzer {

    public $simulation;
    public $universal_log;
    public $activities;
    public $activity_categories;
    public $parent_ending;
    public function __construct(Simulation $simulation) {
        $this->simulation = $simulation;
        $this->universal_log = UniversalLog::model()->findAllByAttributes(['sim_id'=>$simulation->id]);
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
    }

    public function run() {
        foreach($this->universal_log as $universal_log){
            $activityActions = $this->findActivityActionByLog($universal_log);
            $activityActions = $this->excludeParentComplete($activityActions);
            $activityAction = $this->getHighPriorityCategory($activityActions);
            $this->saveLogActivityAction($activityAction, $universal_log);
        }
    }

    public function findActivityActionByLog(UniversalLog $log) {
        if(null !== $log->mail_id) {
            return ActivityAction::model()->findAllByAttributes(
                ['scenario_id'=>$this->simulation->game_type->id, 'mail_id'=>$log->mail_id]);
        }elseif(null !== $log->file_id) {
            return ActivityAction::model()->findAllByAttributes(
                ['scenario_id'=>$this->simulation->game_type->id, 'document_id'=>$log->file_id]);
        }elseif(null !== $log->replica_id){
            return ActivityAction::model()->findAllByAttributes(
                ['scenario_id'=>$this->simulation->game_type->id, 'dialog_id'=>$log->replica_id]);
        }elseif(null !== $log->meeting_id){
            return ActivityAction::model()->findAllByAttributes(
                ['scenario_id'=>$this->simulation->game_type->id, 'meeting_id'=>$log->meeting_id]);
        }elseif(null !== $log->window_id){
            return ActivityAction::model()->findAllByAttributes(
                ['scenario_id'=>$this->simulation->game_type->id, 'window_id'=>$log->window_id]);
        }else{
            throw new Exception("empty log");
        }
    }

    public function excludeParentComplete($activityActions) {
        /* @var $activityActions []ActivityAction */
        /* @var $activityAction ActivityAction */
        foreach($activityActions as $key => $activityAction) {
            $parent = $this->activities[$activityAction->activity_id]->parent;
            if(isset($this->parent_ending[$parent])) {
                unset($activityActions[$key]);
            }
        }

        return $activityActions;

    }

    public function getHighPriorityCategory($activityActions) {

        $priority = [];
        /* @var $this->activities[$activityAction->activity_id] Activity */
        foreach($activityActions as $index => $activityAction) {
            $priority[$this->activity_categories[$this->activities[$activityAction->activity_id]->category_id]] = $index;
        }

        if(false === ksort($priority)){
            throw new Exception('Sort fail');
        }
        $key = current($priority);
        if(false === $key){
            throw new Exception("array error");
        }else{
            return $activityActions[$key];
        }
    }

    public function saveLogActivityAction(ActivityAction $activityAction, UniversalLog $universal_log) {
        $logActivityAction = new LogActivityAction();
        $logActivityAction->sim_id = $this->simulation->id;
        $logActivityAction->activity_action_id = $activityAction->id;
        $logActivityAction->window = $universal_log->window_id;
        $logActivityAction->start_time = $universal_log->start_time;
        $logActivityAction->end_time = $universal_log->end_time;
        $logActivityAction->mail_id = $activityAction->mail_id;
        $logActivityAction->document_id = $activityAction->document_id;
        $logActivityAction->meeting_id = $activityAction->meeting_id;
        $logActivityAction->window_uid = $universal_log->window_uid;
        $logActivityAction->save(false);
    }

} 