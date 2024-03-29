<?php

/**
 * Сервис управления флагами
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class FlagsService 
{
    /**
     * Проверка можем ли запустить диалог
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
     * Возвращает состояние флагов для дев. режима в тестах
     * @param Simulation  $simulation
     * @return array
     */
    public static function getFlagsState(Simulation $simulation) {
        $result = [];
        
        // display flags for developers only ! :) no chanses for cheatting
        if ($simulation->isDevelopMode()) {
            foreach (SimulationFlag::model()->findAllByAttributes(['sim_id' => $simulation->id]) as $flag) {

                $result[$flag->flag] = $flag->value;
            }
        }

        return $result;
    }

    /**
     * Возвращает состояние флагов для дев. режима
     * @param Simulation  $simulation
     * @return mixed array
     */
    public static function getFlagsStateForJs(Simulation $simulation) {
        $result = [];
        // display flags for developers only ! :) no chanses for cheatting
        if ($simulation->isDevelopMode()) {
            $stack = SimulationFlagQueue::model()->findAllByAttributes(['sim_id' => $simulation->id]);
            foreach (SimulationFlag::model()->findAllByAttributes(['sim_id' => $simulation->id]) as $flag) {
                $result[$flag->flag]['value'] = $flag->value;
                $result[$flag->flag]['name'] = $flag->flagObj->description;
                $result[$flag->flag]['time'] = self::getSwitchTime($stack, $flag);
            }
        }
        return $result;
    }

    /**
     * @param array $stack очередь флагов
     * @param SimulationFlag $flag
     * @return null|string
     */
    public static function getSwitchTime(array $stack, SimulationFlag $flag){
        foreach($stack as $item){
            if($item->flag_code === $flag->flag){
                return $item->switch_time;
            }
        }
        return null;
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
     * Устанавлевает значение флагов
     * @param Simulation $simulation
     * @param $flag название флага
     * @param $value значение флага 1 или 0
     * @return SimulationFlag
     * @throws Exception
     */
    public static function setFlag(Simulation $simulation, $flag, $value)
    {
        $simulationFlag = SimulationFlag::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'flag' => $flag
        ]);
        if(empty($flag)){
            throw new Exception(" Set empty flag ");
        }
        if (null === $simulationFlag) {
            $simulationFlag         = new SimulationFlag();
            $simulationFlag->sim_id = $simulation->id;
            $simulationFlag->flag   = $flag;
        }

        $simulationFlag->value = $value;
        $simulationFlag->save();

        return $simulationFlag;
    }

    /**
     * @param $simulation
     * @param $mailCode
     * @return bool
     */
    public static function isAllowToSendMail(Simulation $simulation, $mailCode)
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
     * @param $flag_code
     * @return SimulationFlag
     */
    public static function getFlag($simulation, $flag_code)
    {
        return SimulationFlag::model()->findByAttributes(['sim_id' => $simulation->id, 'flag'=>$flag_code]);
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

    /**
     * @param Simulation $simulation
     * @param Flag $flag
     */
    public static function addFlagDelayAfterReplica(Simulation $simulation, Flag $flag) {
        $queue = new SimulationFlagQueue();
        $queue->sim_id = $simulation->id;
        $queue->flag_code = $flag->code;
        $queue->switch_time = (new DateTime($simulation->getGameTime()))->modify("+{$flag->delay} minutes")->format('H:i:s');//setTimestamp(strtotime($simulation->getGameTime().' + 30 minutes'))->format('H:i:s');
        $queue->is_processed = SimulationFlagQueue::NONE;
        $queue->value = 1;
        $queue->save(false);
    }

    /**
     * @param Simulation $simulation
     */
    public static function copyTimeFlagsToQueue(Simulation $simulation) {
        /** @var FlagSwitchTime[] $timeFlags */
        $timeFlags = $simulation->game_type->getFlagsSwitchTime([]);
        foreach ($timeFlags as $timeFlag) {
            $queue = new SimulationFlagQueue();
            $queue->sim_id = $simulation->id;
            $queue->flag_code = $timeFlag->flag_code;
            $queue->switch_time = $timeFlag->time;
            $queue->is_processed = SimulationFlagQueue::NONE;
            $queue->value = $timeFlag->value;
            $queue->save();
        }
    }

    /**
     * @param Simulation $simulation
     */
    public static function checkFlagsDelay(Simulation $simulation) {

        $flags = SimulationFlagQueue::model()->findAll("sim_id = :sim_id and is_processed = :is_processed and switch_time <= :switch_time", [
            'sim_id' => $simulation->id, 'switch_time' => $simulation->getGameTime(), 'is_processed' => SimulationFlagQueue::NONE
        ]);
        /* @var SimulationFlagQueue $flag */
        foreach($flags as $flag) {
            FlagsService::setFlag($simulation, $flag->flag_code, $flag->value);
            $flag->is_processed = SimulationFlagQueue::DONE;
            $flag->update();
        }
    }

    /**
     * @param Meeting $meeting
     * @param Simulation $simulation
     * @return bool
     */
    public static function isAllowToStartMeeting(Meeting $meeting, Simulation $simulation)
    {
        /** @var FlagAllowMeeting[] $rules */
        $rules = $simulation->game_type->getFlagAllowMeetings(['meeting_id' => $meeting->id]);
        foreach ($rules as $rule) {
            /** @var SimulationFlag $simFlag */
            $simFlag = self::getFlag($simulation, $rule->flag_code);
            if ($rule->value !== $simFlag->value) {
                return false;
            }
        }

        return true;
    }
}

