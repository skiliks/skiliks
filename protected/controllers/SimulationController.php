<?php



/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController{
    
    protected function _fillDayPlan($simId) {
        $tasks = array(14, 15, 19, 8, 17);  //, 21
        
        $tasks = Tasks::model()->byIds($tasks)->findAll();
        
        foreach($tasks as $task) {
            
            $date = $task->start_time;
            
            $dayPlan = new DayPlan();
            $dayPlan->sim_id = $simId;
            $dayPlan->task_id = $task->id;
            $dayPlan->date = $date;
            $dayPlan->day = 1; //rand(1, 2);
            $dayPlan->insert();
                    
            
        }
    }
    
    /**
     * Предустановка задач в симуляции
     * @param type $simId 
     */
    protected function _fillTodo($simId) {
        //$tasks = array(1, 2, 3, 4, 5, 6, 7, 9, 10, 11, 12, 13, 16, 18);
        
        $tasks = Tasks::model()->byStartType('start')->findAll();
        
        foreach($tasks as $task) {
            if ($task->code != 'P017') {
                Logger::debug("add todo task : {$task->code}");
                TodoService::add($simId, $task->id);
            }
            else {
                Logger::debug("add day plan task : {$task->code}");
                $dayPlan = new DayPlan();
                $dayPlan->sim_id    = $simId;	
                $dayPlan->date      = $task->start_time;	
                $dayPlan->day       = 1;	
                $dayPlan->task_id   = $task->id;
                $dayPlan->insert();
            }
        }
    }
    
    protected function _createEventByCode($code, $simId) {
        $event = EventsSamples::model()->byCode($code)->find();
        if ($event) {
            $eventsTriggers = new EventsTriggers();
            $eventsTriggers->sim_id         = $simId;
            $eventsTriggers->event_id       = $event->id;
            $eventsTriggers->trigger_time   = $event->trigger_time; 
            $eventsTriggers->save();
        }
    }
    
    /**
     * Старт симуляции
     */
    public function actionStart() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            $stype = (int)Yii::app()->request->getParam('stype', false); // тип симуляции 1 - promo, 2 - dev

            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception('Не могу найти такого пользователя');

            // Удаляем предыдущую симуляцию
            $simulation = Simulations::model()->findByAttributes(array('user_id'=>$uid));
            if ($simulation) $simulation->delete();

            // @todo: проверить а имеет ли право пользователь на эту симуляцию
            if (!UserService::isMemberOfGroup($uid, $stype)) {
                throw new Exception('У вас нет прав для старта этой симуляции');
            }

            // Создаем новую симуляцию
            $simulation = new Simulations();
            $simulation->user_id    = $uid;
            $simulation->status     = 1;
            $simulation->start      = time();
            $simulation->difficulty = 1;
            $simulation->type       = $stype;
            $simulation->insert();

            $simId = $simulation->id;

            // Сделать вставки в events triggers
            $events = EventsSamples::model()->findAll();  // limit(1)->
            foreach($events as $event) {
                
                if (EventService::isPlan($event->code)) continue;
                if (EventService::isDocument($event->code)) continue;
                
                Logger::debug("create trigger : {$event->code}");
                $eventsTriggers = new EventsTriggers();
                $eventsTriggers->sim_id         = $simId;
                $eventsTriggers->event_id       = $event->id;
                $eventsTriggers->trigger_time   = $event->trigger_time; 
                $eventsTriggers->save();
            }

            #######################
            // временно добавим тестовые ивенты
            $this->_createEventByCode('#plog', $simId); // будем логировать план в 11 часов
            /*$this->_createEventByCode('M11', $simId);
            $this->_createEventByCode('P6', $simId);*/

            #################################################

            // предустановка задач в todo!
            $this->_fillTodo($simId);

            // предустановка задач в план дневной
            //++$this->_fillDayPlan($simId);

            // Копируем игроку его документ в рамках его симуляции
            //ExcelDocumentService::copy('Сводный бюджет', $simId);

            // скопируем документы
            MyDocumentsService::init($simId);

            // Установим дефолтовые значения для почтовика
            MailBoxService::initDefaultSettings($simId);

            // проставим дефолтовые значени флагов для симуляции пользователя
            FlagsService::initDefaultValues($simId);

            $result = array('result' => 1, 'speedFactor' => SKILIKS_SPEED_FACTOR);
            $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
    
    /**
     * Остановка симуляции
     */
    public function actionStop() {
        
        $sid = Yii::app()->request->getParam('sid', false);
            SessionHelper::setSid($sid);
        
        $simId = SessionHelper::getSimIdBySid($sid);
        SimulationService::calcPoints($simId);
        //return;
        
        $uid = (int)Yii::app()->request->getParam('uid', false);
        
        $model = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($model) {
            $model->end = time();
            $model->status = 0;
            $model->save();
        }
        
        // залогируем состояние плана
        DayPlanLogger::log($simId, 2);
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetPoint() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("empty sid");
            
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception("cant find user by sid {$sid}");

            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception("cant find simulation for uid {$uid}");
            
            $result = array();
            $result['result'] = 1;
            // определяем duration симуляции
            //$dialogsDuration = SimulationsDialogsDurations::model()->bySimulation($simulation->id)->find();
            $dialogsDuration = SimulationsDialogsDurations::model()->findByAttributes(array('sim_id'=>$simulation->id));
            if ($dialogsDuration) {
                $result['duration'] = $dialogsDuration->duration;    
            }
            else {
                $result['duration'] = 0;
            }
            
            
            // загружаем поинты
            $sql = "select 
                        sdp.count,
                        sdp.value,
            
                        sdp.count_negative,
                        sdp.value_negative,    
            
                        sdp.count6x,
                        sdp.value6x,
            
                        cpt.code,
                        cpt.title
                    from simulations_dialogs_points as sdp
                    left join characters_points_titles as cpt on (cpt.id = sdp.point_id)
                    where sdp.sim_id = {$simulation->id}";
            
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
        
            $dataReader = $command->query();
        
            foreach($dataReader as $row) { 
                
                $avg = 0;
                if ($row['count'] > 0) $avg = $row['value'] / $row['count'];
                
                $avgNegative = 0;
                if ($row['count_negative'] > 0) $avgNegative = $row['value_negative'] / $row['count_negative'];
                
                $avg6x = 0;
                if ($row['count6x'] > 0) $avg6x = $row['value6x'] / $row['count6x'];
                
                $result['points'][] = array(
                    'code' => $row['code'],
                    'title' => $row['title'],
                    
                    'count' => $row['count'],
                    'value' => $row['value'],
                    'avg' => $avg,
                    
                    'countNegative' => $row['count_negative'],
                    'valueNegative' => $row['value_negative'],
                    'avgNegative' => $avgNegative,
                    
                    'count6x' => $row['count6x'],
                    'value6x' => $row['value6x'],
                    'avg6x' => $avg6x
                );
            }        
                    
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
    
    /**
     * Изменение времени симуляции
     */
    public function actionChangeTime() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if (!$sid) throw new Exception("empty sid");

            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception("cant find user by sid {$sid}");

            $simulation = Simulations::model()->byUid($uid)->find();
            if (!$simulation) throw new Exception("cant find simulation for uid {$uid}");
           
            $hour = (int)Yii::app()->request->getParam('hour', false);
            $min = (int)Yii::app()->request->getParam('min', false);
            
            #################
            $variance = time() - $simulation->start;
            $variance = $variance*SKILIKS_SPEED_FACTOR;

            $unixtimeMins = round($variance/60);
            $clockH = round($unixtimeMins/60);
            $clockM = $unixtimeMins-($clockH*60);
            $clockH = $clockH + 9;

            $simulation->start = ($simulation->start - (($hour-$clockH)*60*60/SKILIKS_SPEED_FACTOR)-(($min-$clockM)*60/SKILIKS_SPEED_FACTOR));
            //Logger::debug($str)
            
            ###################
            /*$data = date('Y-m-d', time());
            Logger::debug("set date: ".var_export($data, true));
            $data = explode('-', $data);
            $year = $data[0];
            $month = $data[1];
            $day = $data[2];
            
            $time = $hour * 60 + $min;
            $time = $time / 4;
            
            $simulation->start = mktime(0, 0, 0, $month, $day, $year) + $time; */
            $simulation->save();
            
            $result = array('result' => 1);
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array('result' => 0, 'message' => $exc->getMessage());
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
}

?>
