<?php



/**
 * Сервис управления флагами
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsService {
    
    public static function getRules($rule) {
        $ruleModel = FlagsRulesModel::model()->byName($rule)->find();
        if (!$ruleModel) return false;
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
     * @param string $code 
     * @return bool || array
     */
    public static function checkRule($code, $simId) {
        // определим код правила
        $ruleModel = FlagsRulesModel::model()->byName($code)->find();
        if (!$ruleModel) return false; // для данного диалога не задано правила
        
        // получим флаги для этого правила
        $flags = FlagsService::getFlags($ruleModel->id);
        if (count($flags) == 0) return false; // для данного кода нет правил
        
        // получить флаги в рамках симуляции
        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags)==0) return false; // у нас пока нет установленных флагов - не чего сравнивать
        
        // проверить на совпадение флагов с теми что есть в симуляции
        if (FlagsService::compareFlags($simulationFlags, $flags)) {
            return array(
                'recId'         => $ruleModel->rec_id,
                'stepNumber'    => $ruleModel->step_number,
                'replicaNumber' => $ruleModel->replica_number
            );
        }
        
        return false;
    }
}

?>
