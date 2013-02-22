<?php

/**
 * Сервис управления флагами
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsService 
{
    /**
     * @param Simulations $simulation
     * @param string $dialogCode, 'E1.1'
     *
     * @return bool
     */
    public static function isAllowToStartDialog($simulation, $dialogCode)
    {
        $flags = FlagBlockDialog::model()->findAllByAttributes([
            'dialog_code' => $dialogCode
        ]);

        // no flags - dialog is allowed to run
        if (NULL === $flags) {
            return true;
        }

        //  flags comparison {
        $currentFlagState = SimulationService::getFlags($simulation->id);
        foreach ($flags as $flag) {
            if (isset($currentFlagState[$flag->flag_code]) && $flag->value != $currentFlagState[$flag->flag_code]) {
                return false;
            }
        }
        //  flags comparison }

        // pass comparison - dialog is allowed to run
        return true;
    }

    /**
     * @param Simulations  $simulation
     * @return mixed array
     */
    public static function getFlagsState(Simulations $simulation) {
        $result = [];
        
        // display flags for developers only ! :) no chanses for cheatting
        if ($simulation->isDevelopMode()) {
            foreach (SimulationFlagsModel::model()->bySimulation($simulation->id)->findAll() as $flag) {
                $result[$flag->flag] = $flag->value;
            }        
        }        
        return $result;
    }

    /**
     * Сравнение флагов из симуляции с флагами из правила
     * @param mixed array $simulationFlags
     * @param array if FlagBlockReplica $rules
     *
     * @return boolean
     */
    public static function compareFlags($simulationFlags, $rules)
    {
        foreach ($rules as $rule) {
            if (false === isset($simulationFlags[$rule->flag_code])) {
                return false;
            }

            if ($simulationFlags[$rule->flag_code] != $rule->value) {
                return false;
            }
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

        $rules = FlagBlockReplica::model()->findAllByAttributes([
            'replica_id' => $excelId
        ]);

        $result['ruleExists']    = !!count($rules);
        $result['recId']         = (int)$excelId;
        $result['stepNumber']    = $stepNumber;
        $result['replicaNumber'] = $replicaNumber;
        $result['compareResult'] = true;

         if (count($rules) == 0) {
            return $result; // для данного кода нет правил
        }

        // получить флаги в рамках симуляции
        $simulationFlags = SimulationService::getFlags($simId);
        if (count($simulationFlags) == 0){
            return $result; // у нас пока нет установленных флагов - не чего сравнивать
        }

        // проверить на совпадение флагов с теми что есть в симуляции
        if (false === FlagsService::compareFlags($simulationFlags, $rules)) {
             $result['compareResult'] = false;
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
            $model->flag = $flag;
        }
        $model->value = $value;
        $model->save();
    }

    /**
     * Установка первоначальных значений флагов в рамках симуляции.
     * @param int $simId 
     */
    public static function initDefaultValues($simId) 
    {
        $flags = Flag::model()->findAll();
        foreach ($flags as $flag) {
            self::setFlag($simId, $flag->code, 0);
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

    public static function isAllowToSendMail($simulation, $mailCode)
    {
        $mail_template = MailTemplateModel::model()->findByAttributes(['code' => $mailCode]);
        if ($mail_template === null) {
            return true;
        }
        $flags = FlagBlockMail::model()->findAllByAttributes([
            'mail_template_id' => $mail_template->primaryKey
        ]);

        // no flags - dialog is allowed to run
        if (0 === count($flags)) {
            return true;
        }

        //  flags comparison {
        $currentFlagState = SimulationService::getFlags($simulation->id);

        foreach ($flags as $flag) {
            if (isset($currentFlagState[$flag->flag_code]) && $flag->value != $currentFlagState[$flag->flag_code]) {
                return false;
            }
        }
        //  flags comparison }

        // pass comparison - dialog is allowed to run
        return true;
    }
}

