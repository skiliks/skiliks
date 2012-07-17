<?php



/**
 * Сервис  по работе с симуляциями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationService {
    
    /**
     * Получить идентификатор симуляции для заданого пользователя
     * @param int $uid
     * @return int
     */
    public static function get($uid) {
        $simulation = Simulations::model()->byUid($uid)->find();
        if (!$simulation) return false;
        return $simulation->id;
    }
    
    /**
     * Определяет игровое время в рамках заданной симуляции
     * @param int $simId 
     * @return int игровое время
     */
    public static function getGameTime($simId) {
        $simulation = Simulations::model()->byId($simId)->find();
        if (!$simulation) throw new Exception('Не могу определить симуляцию');
        $startTime = $simulation->start;
        $simulationTime = time() - $startTime;  // сколько времени реально длится симуляция
        
        return $simulationTime * 4; // возвращаем игровое время
    }
}

?>
