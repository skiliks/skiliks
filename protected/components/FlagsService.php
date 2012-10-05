<?php



/**
 * Сервис управления флагами
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsService {
    
    /*public static function getRules($rule) {
        $ruleModel = FlagsRulesModel::model()->byName($rule)->find();
        if (!$ruleModel) return false;
    }*/
    
    /**
     * Определяет правило по заданному коду события
     * @param string $code
     * @param int $stepNumber
     * @param int $replicaNumber
     * @return FlagsRulesModel 
     */
    public static function getRuleByCode($code, $stepNumber = false, $replicaNumber = false) {
        if ($stepNumber!==false && $replicaNumber!==false) {
            Logger::debug("get rule by code $code stepNumber $stepNumber replicaNumber $replicaNumber");
            return FlagsRulesModel::model()->byName($code)->byStepNumber($stepNumber)->byReplicaNumber($replicaNumber)->find();
        }
        
        Logger::debug("get rule by code $code");
        return FlagsRulesModel::model()->byName($code)->find();
    }
    
    public static function getFlags($ruleId) {
        $rules = FlagsRulesContentModel::model()->byRule($ruleId)->findAll();
        
        $list = array();
        foreach($rules as $rule) {
            $list[$rule->flag] = $rule->value;
        }
        
        return $list;
    }

    /**
     * Сравнение флагов из симуляции с флагами из правила
     * @param type $simulationFlags
     * @param type $flags 
     * @return true
     */
    public static function compareFlags($simulationFlags, $flags) {
        foreach($flags as $flag => $value) {
            if (!isset($simulationFlags[$flag])) return false;
            if ($simulationFlags[$flag] != $value) return false;
        }
        return true;
    }

    /**
     * Проверяет выполняются ли правила для данного кода диалога
     * 
     * @param string $code код события
     * @param int $simId идентификатор симуляции
     * @return array
     */
    public static function checkRule($code, $simId, $stepNumber = 1, $replicaNumber = 0) {
        // определим код правила
        $ruleModel = self::getRuleByCode($code, $stepNumber, $replicaNumber);
        if (!$ruleModel) return false; // для данного диалога не задано правила
        
        $result = array();
        $result['ruleExists']       = true;
        $result['recId']            = $ruleModel->rec_id;
        $result['stepNumber']       = $ruleModel->step_number;
        $result['replicaNumber']    = $ruleModel->replica_number;
        $result['compareResult']    = false;
        
        // получим флаги для этого правила
        $flags = FlagsService::getFlags($ruleModel->id);
        if (count($flags) == 0) return $result; // для данного кода нет правил
        
        // получить флаги в рамках симуляции
        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags)==0) return $result; // у нас пока нет установленных флагов - не чего сравнивать
        
        // проверить на совпадение флагов с теми что есть в симуляции
        if (FlagsService::compareFlags($simulationFlags, $flags)) {
            $result['compareResult'] = true;
        }
        
        return $result;
    }
}

?>
