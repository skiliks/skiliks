<?php

/**
 * Сервис управления флагами
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsService 
{
    /**
     * Определяет правило по заданному коду события
     * @param string $code
     * @param int $stepNumber, dialog step no
     * @param int $replicaNumber, dialog replica no
     * @param int $excelId, dialog excel id
     * @return FlagsRulesModel 
     */
    public static function getRuleByCode($code, $stepNumber = false, $replicaNumber = false, $excelId = null) 
    {
        if ($stepNumber !== false && $replicaNumber !== false) {
            $request = FlagsRulesModel::model()
                    ->byName($code)
                    ->byStepNumber($stepNumber)
                    ->byReplicaNumber($replicaNumber)
                    ->byRecordIdOrNullOrZero($excelId);

            return $request->find();
        }

        return FlagsRulesModel::model()->byName($code)->find();
    }

    /**
     * @param integer $ruleId
     * @return array
     */
    public static function getFlags($ruleId) 
    {
        $rules = FlagsRulesContentModel::model()->byRule($ruleId)->findAll();

        $list = array();
        foreach ($rules as $rule) {
            $list[$rule->getFlagName()] = $rule->getValue();
        }

        return $list;
    }

    /**
     * Сравнение флагов из симуляции с флагами из правила
     * @param type $simulationFlags
     * @param type $flags 
     * @return true
     */
    public static function compareFlags($simulationFlags, $flags) 
    {
        foreach ($flags as $flag => $value) {
            if (!isset($simulationFlags[$flag]))
                return false;
            if ($simulationFlags[$flag] != $value)
                return false;
        }
        return true;
    }

    /**
     * Проверяет выполняются ли правила для данного кода диалога
     * 
     * @param string $code код события
     * @param int $simId идентификатор симуляции
     * @param int $stepNumber, dialog step no
     * @param int $replicaNumber, dialog replica no
     * @param int $excelId, dialog excel id
     * @return array
     */
    public static function checkRule($code, $simId, $stepNumber = 1, $replicaNumber = 0, $excelId = null) 
    {
        $result = array();

        // определим код правила
        $ruleModel = self::getRuleByCode($code, $stepNumber, $replicaNumber, $excelId);

        if (null === $ruleModel) {
            $result['ruleExists'] = false;
            return $result; // для данного диалога не задано правила
        }

        $result['ruleExists'] = true;
        $result['recId'] = (int) $ruleModel->getRecordId();
        $result['stepNumber'] = $ruleModel->getStepNo();
        $result['replicaNumber'] = $ruleModel->getReplicaNo();
        $result['compareResult'] = false;

        // получим флаги для этого правила
        $flags = FlagsService::getFlags($ruleModel->getId());
        if (count($flags) == 0) {
            return $result; // для данного кода нет правил
        }

        // получить флаги в рамках симуляции
        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags) == 0)
            return $result; // у нас пока нет установленных флагов - не чего сравнивать

        // проверить на совпадение флагов с теми что есть в симуляции
        if (FlagsService::compareFlags($simulationFlags, $flags)) {
            $result['compareResult'] = true;
        }

        return $result;
    }

    /**
     * @param string $simId
     * @param string $flag
     * @param string $value
     */
    public static function setFlag($simId, $flag, $value) 
     {
        $model = SimulationFlagsModel::model()->bySimulation($simId)->byFlag($flag)->find();
        if (!$model) {
            $model = new SimulationFlagsModel();
            $model->sim_id = $simId;
        }
        $model->flag = $flag;
        $model->value = $value;
        $model->save();
    }

    /**
     * Установка первоначальных значений флагов в рамках симуляции.
     * @param int $simId 
     */
    public static function initDefaultValues($simId) 
    {
        for ($index = 1; $index <= 22; $index++) {
            self::setFlag($simId, 'F' . $index, 0);
        }
    }

    ## will broken
    public static function skipReplica($dialog, $simId) {
        $flagInfo = FlagsService::checkRule($dialog->code, $simId, $dialog->step_number, $dialog->replica_number);

        if ($flagInfo['ruleExists'] === false)
            return false; // нет правил

        if (isset($flagInfo['stepNumber']) && isset($flagInfo['replicaNumber'])) {  // если заданы правила для шага и реплики
            if ($flagInfo['stepNumber'] == $dialog->step_number && $flagInfo['replicaNumber'] == $dialog->replica_number) {
                if ($flagInfo['compareResult'] === true) { // если выполняются условия правил флагов
                    if ($flagInfo['recId'] != $dialog->excel_id) {
                        $flagInfo['action'] = 'skip';
                        return $flagInfo; // эта реплика не пойдет в выборку
                    }
                } else {
                    // условие сравнение не выполняется
                    if ($flagInfo['recId'] == $dialog->excel_id) {
                        $flagInfo['action'] = 'skip';
                        return $flagInfo; // эта реплика не пойдет в выборку
                    }
                }
            }
        }

        $flagInfo['action'] = 'break';
        return $flagInfo;
    }
}

