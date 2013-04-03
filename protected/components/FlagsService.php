<?php

/**
 * Сервис управления флагами
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsService 
{
    /**
     * @param Simulation $simulation
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
        $currentFlagState = FlagsService::getFlags($simulation);
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
     * @param Simulation  $simulation
     * @return mixed array
     */
    public static function getFlagsState(Simulation $simulation) {
        $result = [];
        
        // display flags for developers only ! :) no chanses for cheatting
        if ($simulation->isDevelopMode()) {
            foreach (SimulationFlag::model()->bySimulation($simulation->id)->findAll() as $flag) {
                $result[$flag->flag] = $flag->value;
            }
        }        
        return $result;
    }

    /**
     * @param Simulation  $simulation
     * @return mixed array
     */
    public static function getFlagsStateForJs(Simulation $simulation) {
        $result = [];

        // display flags for developers only ! :) no chanses for cheatting
        if ($simulation->isDevelopMode()) {
            foreach (SimulationFlag::model()->bySimulation($simulation->id)->findAll() as $flag) {
                $result[$flag->flag]['value'] = $flag->value;
                $result[$flag->flag]['name'] = $flag->flagObj->description;
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
     * @warning ! code will never use!
     * @warning this code smells like it dead year ago
     * 
     * @param string $code код события
     * @param $simulation
     * @param int $stepNumber, dialog step no
     * @param int $replicaNumber, dialog replica no
     * @param int $excelId, dialog excel id
     * @return array
     */
    public static function checkRule($code, Simulation $simulation, $stepNumber = 1, $replicaNumber = 0, $excelId = null)
    {
        $result = array();

        $rules = $simulation->game_type->getFlagBlockReplicas([
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
        $simulationFlags = FlagsService::getFlags($simulation);
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
     * @param Simulation $simulation
     * @param string $flag
     * @param string $value
     */
    public static function setFlag($simulation, $flag, $value)
    {
        $simulationFlag = SimulationFlag::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'flag' => $flag
        ]);

        if (null === $simulationFlag) {
            $simulationFlag         = new SimulationFlag();
            $simulationFlag->sim_id = $simulation->id;
            $simulationFlag->flag   = $flag;
        }

        $simulationFlag->value = $value;
        $simulationFlag->save();

        return $simulationFlag;
    }

    public static function isAllowToSendMail($simulation, $mailCode)
    {
        $mail_template = MailTemplate::model()->findByAttributes(['code' => $mailCode]);
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
        $currentFlagState = FlagsService::getFlags($simulation);

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
     * @param Simulation $simulation
     * @param string $flagName
     * @return bool
     */
    public static function switchFlag($simulation, $flagName)
    {
        $flag = SimulationFlag::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'flag'   => $flagName
        ]);

        if (NULL === $flag) {
            return false;
        }

        $value = (1 == $flag->value) ? 0 : 1; // inversion

        self::setFlag($simulation, $flagName, $value);

        return true;
    }

    /**
     * Получить список флагов диалогов в рамках симуляции
     * @param $simulation
     * @return array
     */
    public static function getFlags($simulation)
    {
        $flags = SimulationFlag::model()->findAllByAttributes(['sim_id' => $simulation->id]);
        $list = array();
        foreach ($flags as $flag) {
            $list[$flag->flag] = $flag->value;
        }

        return $list;
    }
}

