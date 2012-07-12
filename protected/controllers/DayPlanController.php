<?php



/**
 * Контроллер дневного плана
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DayPlanController extends AjaxController{
    
    const YEAR = 0;
    const MONTH = 1;
    const DAY = 2;
    const HOUR = 3;
    const MINUTE = 4;
    const SECOND = 5;
    
    /**
     * Преобразовывает дату в массив
     * @param type $date
     * @return type 
     */
    protected function dateToArr($date) {
        return explode('-', date('Y-m-d-G-i-S', $date));
    }
    
    /**
     *
     * @param array $ids
     * @return array
     */
    protected function _loadTasksTitle($ids) {
        $tasksCollection = Tasks::model()->byIds($tasks)->findAll();
        $tasks = array();
        foreach($tasksCollection as $task) {
            $tasks[$task->id] = $task->title;
        }
        
        return $tasks;
    }
    
    /**
     * Получить список для плана дневной
     */
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);
            if ($sid) throw new Exception("Не передан sid");
            
            // получаем uid
            $uid = SessionHelper::getUidBySid($sid);
            if (!$uid) throw new Exception("Не определить uid по sid : {$sid}");

            // получаем идентификатор симуляции
            $simId = SimulationService::get($uid);
            if (!$simId) throw new Exception("Не определить сумуляцию по uid : {$uid}");
        
            $now = time();
            $date = explode('-', date('Y-m-d', $now));


            $fromTime   = mktime(0, 0, 0, $date[self::MONTH], $date[self::DAY], $date[self::YEAR]);
            $toTime = $fromTime + 2*24*60*60;
            //$toTime     = mktime(23, 59, 59, $date[1], $date[2], $date[0]);

            $data = array();
            $tasks = array();
            $plans = DayPlan::model()->byDate($fromTime, $toTime)->bySimulation($simId)->findAll();
            foreach($plans as $plan) {
                $tasks[] = $plan->task_id;

                $date = $this->dateToArr($plan->date);
                $data[] = array(
                    'date' => $date[self::HOUR].':'.$date[self::MINUTE],  // дата в формате hh:mm
                    'task_id' => $plan->task_id,
                    'day' => $date[self::DAY]  // день, на когда идут задачи
                );
            }

            if (count($data)==0)  {
                $data = array('result' => 1, 'data' => array());
                return $this->_sendResponse(200, CJSON::encode($data));
            }

            // загрузка названий задач
            $tasks = $this->_loadTasksTitle($tasks);

            // Подготовка ответа (сегодня, завтра)
            $list = array();
            foreach($data as $item) {
                if (isset($tasks[$item['task_id']]))
                    $item['title'] = $tasks[$item['task_id']];
                $list[$item['day']][] = $item;
            }
            
            ########################################################
            // Загрузка задач после отпуска
            $tasks = array();
            $vacationTasks = array();
            $vacationsCollection = DayPlanAfterVacation::model()->bySimulation($simId)->findAll();
            foreach($vacationsCollection as $item) {
                $date = $this->dateToArr($item->date);
                
                $tasks[] = $item->task_id;
                $vacationTasks[] = array(
                    'date' => $date[self::HOUR].':'.$date[self::MINUTE],  // дата в формате hh:mm
                    'task_id' => $item->task_id
                );
            }
            
            // загрузка названий задач
            $tasks = $this->_loadTasksTitle($tasks);
            
            // Добавляем задачи после отпуска
            $vacations = array();
            foreach($vacationTasks as $item) {
                if (isset($tasks[$item['task_id']]))
                    $item['title'] = $tasks[$item['task_id']];
                
                $vacations[] = $item;
            }
            $list[] = $vacations;

            $data = array('result' => 1, 'date' => $list);
            $this->_sendResponse(200, CJSON::encode($data));
        } catch (Exception $exc) {
            $data = array('result' => 0, 'message' => $exc->getMessage());
            $this->_sendResponse(200, CJSON::encode($data));
        }
    }
}

?>
