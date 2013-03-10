<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/1/13
 * Time: 10:33 PM
 * To change this template use File | Settings | File Templates.
 */
class GameContentAnalyzer
{
    public $eventsStartedByTime = [];

    public $hoursChainOfEventsStartedByTime = [];

    public $eventsStartedByCall = [];

    public $eventsFromPlan = [];

    public $aEvents = []; // RAW EventForAnalyze before separation

    public $dialogs = [];

    public $emails = [];

    public $replicas = [];

    // array indexed by dialog.code, contain array of flag that is necessary to run dialog
    public $flagsBlockDialog;

    // array indexed by replica.id, contain array of flag that is necessary to run replica
    public $flagsBlockReplica;

    // array indexed by mail.code, contain array of flag that is necessary to run mail
    public $flagsBlockMail;

    // array indexed by flag.code, contain array of mail.codes that send after flag switch
    public $flagsRunMail;

    const TYPE_PLAN   = 'plan';
    const TYPE_MAIL   = 'mail';
    const TYPE_DIALOG = 'dialog';

    const FLAGS_FROM_EXCEL_FILE = false; // this is boolean option in $this->uploadFlags()

    public function __construct() {

    }

    /**
     * @param EventSample $events
     */
    public function uploadEvents($events) {
        foreach ($events as $event) {
            if (in_array($event->code, ['T', ''])) {
                continue;
            }

            $aEvent = new EventForAnalyze();
            $aEvent->event       = $event;
            $aEvent->type        = $this->getEventType($event);
            $aEvent->title       = $this->getEventTitle($aEvent);
            $aEvent->cssIcon     = $this->getCssIcon($aEvent);
            $aEvent->cssRowColor = $this->getCssRowColor($aEvent);
            $aEvent->replicas    = [];

            $this->setUpReplicas($aEvent);

            $this->aEvents[trim($aEvent->event->code)] = $aEvent;
        }
    }

    /**
     * @param array of FlagsBlockDialog $sFlagsBlockDialog
     * @param array of FlagsBlockReplica $sFlagsBlockReplica
     * @param array of FlagsBlockMail $sFlagsBlockMail
     * @param array of FlagsRunMail $sFlagsRunMail
     */
    public function uploadFlags ($sFlagsBlockDialog, $sFlagsBlockReplica, $sFlagsBlockMail, $sFlagsRunMail, $isFromBd = true)
    {
        // BlockDialog
        foreach ($sFlagsBlockDialog as $sFlagBlockDialog) {
            $this->flagsBlockDialog[$sFlagBlockDialog->dialog_code][] = $sFlagBlockDialog;
        }

        // BlockReplica
        foreach ($sFlagsBlockReplica as $sFlagBlockReplica) {
            $this->flagsBlockReplica[$sFlagBlockReplica->replica_id][] = $sFlagBlockReplica;
        }

        // BlockMail
        foreach ($sFlagsBlockMail as $sFlagBlockMail) {
            if ($isFromBd) {
                $mailTemplate = MailTemplate::model()->findByPk($sFlagBlockMail->mail_template_id);
                $code = $mailTemplate->code;
            } else {
                $code = $sFlagBlockMail->mail_template_id; // in on fly import we set mail.code to FlagBlockMail.mail_template_id
            }
            $this->flagsBlockMail[$code][] = $sFlagBlockMail;
        }

        // BlockMail
        foreach ($sFlagsRunMail as $sFlagRunMail) {
            $this->flagsRunMail[$sFlagRunMail->mail_code][] = $sFlagRunMail;
        }
    }

    /**
     *
     */
    public function updateProducedBy()
    {
        foreach ($this->replicas as $dialog) {
            foreach ($dialog as $step) {
                foreach ($step as $replica) {
                    if (null != $replica->next_event_code
                        && isset($this->aEvents[$replica->next_event_code])
                        && false == isset($this->aEvents[$replica->next_event_code]->producedBy[$replica->code])) {
                        $this->aEvents[$replica->next_event_code]->producedBy[$replica->code] = true;
                    }
                }
            }
        }
    }

    /**
     *
     */
    public function updateDelays()
    {
        foreach ($this->dialogs as $dialog) {
            if (isset($this->aEvents[$dialog->code])) {
                $this->aEvents[$dialog->code]->delay = (int)$dialog->delay;
            }
        }
    }

    /**
     *
     */
    public function separateEvents()
    {
        foreach ($this->aEvents as $aEvent) {
            if (null == $aEvent->event->trigger_time)
            {
                $this->eventsFromPlan[$aEvent->event->code] = $aEvent;
            }
            elseif ('00:00:00' == $aEvent->event->trigger_time)
            {
                $this->eventsStartedByCall[$aEvent->event->code] = $aEvent;
            }
            else
            {
                $aEvent->startTime = (string)$aEvent->event->trigger_time;
                $this->eventsStartedByTime[$aEvent->event->code] = $aEvent;
            }
        }
    }

    /**
     *
     */
    public function initHoursChain()
    {
        $i = 0;
        $hours   = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'];

        // trick, but very easy solution :)
        $this->hoursChainOfEventsStartedByTime[$i] = new HourForContentAnalyze();
        $i++;
        $this->hoursChainOfEventsStartedByTime[$i] = new HourForContentAnalyze();
        $i++;

        foreach ($this->eventsStartedByTime as $aEvent) {

            // assign MY emails to 8:00
            // (Yesterday will be the best, but 8:00 is good enought to highlught that there are yesterday emails)
            if ('MY' == substr($aEvent->event->code, 0, 2)) {
                $this->hoursChainOfEventsStartedByTime[0]->events[] = $aEvent;
                continue;
            }

            if ($hours[$i] <= $aEvent->startTime) {
                $i++;
                $this->hoursChainOfEventsStartedByTime[$i] = new HourForContentAnalyze();
            }

            $this->hoursChainOfEventsStartedByTime[$i]->events[] = $aEvent;
            $this->hoursChainOfEventsStartedByTime[$i]->title = $hours[$i-1];
        }
    }

    /**
     *
     */
    public function buildTimeChains()
    {
        $this->tree = [];
        foreach ($this->eventsStartedByTime as $aEvent) {
            if (false == in_array($aEvent->event->code, ['RST1'])) {
                //continue;
            }

            if (in_array(substr($aEvent->event->code, 0,1),['M','P','D','T'])) {
                continue;
            }

            $tree = [];
            $branch_0 = [
                ['code' => $aEvent->event->code, 'step' => null, 'replica' => null, 'prevCode' => null, 'startTime' => $aEvent->startTime]
            ];

            // $tree send by link
            $this->addTimeAssessment($tree, $branch_0, $aEvent->event->code, $aEvent->startTime, $aEvent->event->code);

            $this->tree[$aEvent->event->code] = $tree;

        }
    }

    /**
     * @param $tree
     * @param $branch
     * @param $code
     * @param $startTime
     * @param $initialEventCode
     */
    private function addTimeAssessment(&$tree, $branch, $code, $startTime, $initialEventCode)
    {
        $code = trim($code);
        $aEvent = $this->getAEvent($code);

        $allNextEvents = [];

        if (false == $aEvent) {

        } else {
            if (in_array(substr($aEvent->event->code, 0,1),['M'])) {
                $aEvent->replicas = [];
            }

            $stepN = 1;
            foreach ($aEvent->replicas as $step) {
                foreach ($step as $replica) {
                    // set-up flagToSwitch
                    if (null !== $replica->flag_to_switch && isset($this->aEvents[$replica->code])) {
                        $this->aEvents[$initialEventCode]->flagsToSwitch[$replica->flag_to_switch] = 1;
                    }

                    $flagsToBlock = [];
                    $flagsToBlockHtml = '';

                    // dialog
                    if (isset($this->flagsBlockDialog[$aEvent->event->code])) {
                        foreach ($this->flagsBlockDialog[$aEvent->event->code] as $flagBlock) {
                            $flagsToBlockHtml .= sprintf(
                                ' <span class="label label-info">%s&rarr;%s</span> ',
                                $flagBlock->flag_code,
                                $flagBlock->value
                            );
                            $flagsToBlock[] = $flagBlock->flag_code;
                        }
                    }

                    // replica
                    if (isset($this->flagsBlockReplica[$replica->id])) {
                        foreach ($this->flagsBlockReplica[$replica->id] as $flagBlock) {
                            $flagsToBlockHtml .= sprintf(
                                ' <span class="label label-info">%s&rarr;%s</span> ',
                                $flagBlock->flag_code,
                                $flagBlock->value
                            );
                            $flagsToBlock[] = $flagBlock->flag_code;
                        }
                    }

                    // mail
                    if (isset($this->flagsBlockMail[$aEvent->event->code])) {
                        foreach ($this->flagsBlockMail[$aEvent->event->code] as $flagBlock) {
                            $flagsToBlockHtml .= sprintf(
                                ' <span class="label label-info">%s&rarr;%s</span> ',
                                $flagBlock->flag_code,
                                $flagBlock->value
                            );
                            $flagsToBlock[] = $flagBlock->flag_code;
                        }
                    }

                    // "T"
                    if ('T' == $replica->next_event_code) {
                        $allNextEvents[] = [
                            'code'             => 'T',
                            'prevCode'         => $code,
                            'step'             => $stepN,
                            'replica'          => $replica->replica_number,
                            'flagToSwitch'     => $replica->flag_to_switch,
                            'startTime'        => TimeTools::timeStringPlusSeconds($startTime, 80*$stepN),
                            'flagsToBlock'     => $flagsToBlock,
                            'flagsToBlockHtml' => $flagsToBlockHtml,
                        ];
                    } elseif('P' == substr($replica->next_event_code, 0, 1)) {
                        $allNextEvents[] = [
                            'code'             => $replica->next_event_code,
                            'prevCode'         => $code,
                            'step'             => $stepN,
                            'replica'          => $replica->replica_number,
                            'flagToSwitch'     => $replica->flag_to_switch,
                            'startTime'        => TimeTools::timeStringPlusSeconds($startTime, 80*$stepN),
                            'flagsToBlock'     => $flagsToBlock,
                            'flagsToBlockHtml' => $flagsToBlockHtml,
                        ];
                    } elseif(null === $replica->next_event_code) {
                        // null
                    } elseif('D' == substr($replica->next_event_code, 0, 1)) {
                        $allNextEvents[] = [
                            'code'             => $replica->next_event_code,
                            'prevCode'         => $code,
                            'step'             => $stepN,
                            'replica'          => $replica->replica_number,
                            'flagToSwitch'     => $replica->flag_to_switch,
                            'startTime'        => TimeTools::timeStringPlusSeconds($startTime, 80*$stepN),
                            'flagsToBlock'     => $flagsToBlock,
                            'flagsToBlockHtml' => $flagsToBlockHtml,
                        ];
                    } elseif('M' == substr($replica->next_event_code, 0, 1)) {
                        if (null === $this->dialogs[$replica->code]->delay) {
                            $k = 80*$stepN; // delay 80 sec per step
                        } else {
                            $k = 80*$stepN + $this->dialogs[$replica->code]->delay; // + delay pefore event will be started
                        }
                        $allNextEvents[] = [
                            'code'             => $replica->next_event_code,
                            'prevCode'         => $code,
                            'step'             => $stepN,
                            'replica'          => $replica->replica_number,
                            'flagToSwitch'     => $replica->flag_to_switch,
                            'startTime'        => TimeTools::timeStringPlusSeconds($startTime, $k),
                            'flagsToBlock'     => $flagsToBlock,
                            'flagsToBlockHtml' => $flagsToBlockHtml,
                        ];
                    } else {
                        if (null === $this->dialogs[$replica->code]->delay) {
                            $k = 80*$stepN; // delay 80 sec per step
                        } else {
                            $k = 80*$stepN + $this->dialogs[$replica->code]->delay; // + delay pefore event will be started
                        }
                        $allNextEvents[] = [
                            'code'             => trim($replica->next_event_code),
                            'prevCode'         => $code,
                            'step'             => $stepN,
                            'replica'          => $replica->replica_number,
                            'flagToSwitch'     => $replica->flag_to_switch,
                            'startTime'        => TimeTools::timeStringPlusSeconds($startTime, $k),
                            'flagsToBlock'     => $flagsToBlock,
                            'flagsToBlockHtml' => $flagsToBlockHtml,
                        ];
                    }
                }

                $stepN++;
            }

            foreach ($allNextEvents as $nextEvent) {
                $tmp   = $branch;
                $tmp[] = $nextEvent;

                $possibleNextEvent = $this->getAEvent($nextEvent['code']);
                if (false != $possibleNextEvent) {
                    $this->addTimeAssessment($tree, $tmp, $nextEvent['code'], $nextEvent['startTime'], $initialEventCode);
                } else {
                    $tree[] = $tmp;
                }
            }
        }
    }

    /**
     *
     */
    public function updateAEventsDurations()
    {
        foreach ($this->eventsStartedByTime as $aEvent) {
            if ('M' == substr($aEvent->event->code,0,1)) {
                // 30 seconds to read an email
                $aEvent->durationFrom = TimeTools::timeStringPlusSeconds($aEvent->startTime, 30);
                $aEvent->durationTo   = TimeTools::timeStringPlusSeconds($aEvent->startTime, 30);
            }
            if (false === isset($this->tree[$aEvent->event->code])) {
                continue;
            }
            $min = '24:59:59';
            $max = '00:00:00';
            foreach ($this->tree[$aEvent->event->code] as $branch) {
                foreach ($branch as $point) {
                     if ($point['startTime'] != $aEvent->event->trigger_time && $point['startTime'] < $min) {
                         $min = $point['startTime'];
                    }
                    if ($max < $point['startTime']) {
                        $max = $point['startTime'];
                    }
                }
            }

            $aEvent->durationFrom = $min;
            $aEvent->durationTo   = $max;
        }
    }

    /**
     * @param Replica $events
     */
    public function uploadDialogs($dialogs)
    {
        foreach ($dialogs as $dialog) {
            $this->dialogs[$dialog->code] = $dialog;
        }
    }

    /**
     * @param Replica $events
     */
    public function uploadReplicas($replicas)
    {
        foreach ($replicas as $replica) {
            $this->replicas[$replica->code][$replica->step_number][$replica->replica_number] = $replica;
        }
    }

    /**
     * @param Replica $events
     */
    public function uploadEmails($emails)
    {
        foreach ($emails as $email) {
            $this->emails[$email->code] = $email;
        }
    }

    /**
     * @param $aEvent
     */
    public function setUpReplicas($aEvent)
    {
        if (isset($this->replicas[$aEvent->event->code])) {
            $aEvent->replicas = $this->replicas[$aEvent->event->code];
        }
    }

    /**
     *
     */
    public function updatePossibleNextEvents()
    {
        foreach ($this->replicas as $dialog) {
            foreach ($dialog as $step) {
                foreach ($step as $replica) {
                    if (null != $replica->next_event_code
                        && isset($this->aEvents[$replica->code])
                        && 'T' != $replica->next_event_code) {

                        $this->aEvents[$replica->code]->possibleNextEvents[$replica->next_event_code] = true;
                    }
                }
            }
        }
    }

    /* ------- */

    /* render */

    /**
     * @param $replica
     * @return string
     */
    public function getFormattedReplicaFlag($replica)
    {
        if (null === $replica->flag_to_switch) {
            return '';
        } else {
            return sprintf('<span class="label label-info">%s&rarr;1</span>',$replica->flag_to_switch,1);
        }
    }

    /**
     * @param $aEvent
     * @return string
     */
    public function getFormattedAEventFlags($aEvent)
    {
        $html = '';
        if (0 != (count($aEvent->flagsToSwitch))) {
            $html = ', Может переключить: ';
            foreach ($aEvent->flagsToSwitch as $flagCode => $value) {
                $html .= sprintf('<span class="label label-info">%s&rarr;1</span> ', $flagCode, $value);
            }
        }

        return $html;
    }

    /**
     * @param $aEvent
     * @return string
     */
    public function getCssSaveEventCode($aEvent)
    {
        return str_replace('.', '_', $aEvent->event->code);
    }

    /**
     * @param $aEvent
     * @return string
     */
    public function getFormattedAEventHeader($aEvent)
    {
        if ($aEvent->durationFrom == $aEvent->durationTo) {
            $endTime = $aEvent->durationFrom;
        } else {
            $endTime = sprintf('%s ~ %s', $aEvent->durationFrom, $aEvent->durationTo);
        }

        $variantsSwitcher = '';
        if (isset($this->tree[$aEvent->event->code])){
            $variantsSwitcher = sprintf(
                '<a data-id="%s-variations" class="switcher pull-left">Скрыть/показать варианты развития события</a>',
                $this->getCssSaveEventCode($aEvent)
            );
        }

        $delay = 'нет задержки';
        if (0 != $aEvent->delay) {
            $delay = sprintf('( задержка: %s мин )', $aEvent->delay);
        }

        if (null != count($aEvent->startTime)) {
            // start by time
            $producedBy = '';
        } elseif(0 != count($aEvent->producedBy)) {
            // start by replica in dialog
            $producedBy = ', является последствием: ';

            foreach ($aEvent->producedBy as $key => $value) {
                $producedBy .= sprintf(
                    '<a href="#%s" title="%s"><span class="label label-warning">%s</span></a> ',
                    $key,
                    $this->getEventTitleByCode($key),
                    $key
                );
            }
        } elseif (isset($this->flagsRunMail[$aEvent->event->code])) {
            // start by flag switch
            $flagsRunMail = [];
            foreach ($this->flagsRunMail[$aEvent->event->code] as $flagRun) {
                $flagsRunMail[] = sprintf(
                    '<span class="label label-info">%s</span>',
                    $flagRun->flag_code
                );
            }
            unset($flagRun);

            $producedBy = ', будет запущен при переключении: '.implode(', ', $flagsRunMail);
        } else {
            // warning! Event will never start.
            $producedBy = '<span class="label label-important">Никогда не будет вызван!</span>';
        }

        $flagsBlockHtml ='';
        if (isset($this->flagsBlockDialog[$aEvent->event->code]) || isset($this->flagsBlockMail[$aEvent->event->code]) ) {

            $flagsBlockDialog = [];
            if (isset($this->flagsBlockDialog[$aEvent->event->code])) {
                foreach ($this->flagsBlockDialog[$aEvent->event->code] as $flagBlock) {
                    $flagsBlockDialog[] = sprintf(
                        '<span class="label label-info">%s&rarr;%s</span>',
                        $flagBlock->flag_code,
                        $flagBlock->value
                    );
                }
                unset($flagBlock);
            }

            $flagsBlockMail = [];
            if (isset($this->flagsBlockMail[$aEvent->event->code])) {
                foreach ($this->flagsBlockMail[$aEvent->event->code] as $flagBlock) {
                    $flagsBlockMail[] = sprintf(
                        '<span class="label label-info">%s&rarr;%s</span>',
                        $flagBlock->flag_code,
                        $flagBlock->value
                    );
                }
                unset($flagBlock);
            }

            $flagsBlockHtml = 'Требует для запуска: '.implode(', ', $flagsBlockDialog).implode(', ', $flagsBlockMail);
        }

        return sprintf(
            '<i class="%s"></i>
                <strong>%s</strong>, %s /  <i class="icon-time"></i>
                c %s<!-- startTime --> до %s<!-- endTime -->,
                %s<!-- delay -->
                %s <!-- formattedAEventFlags -->
                %s <!-- producedBy --> <br/>
                %s <!-- variantsSwitcher -->
                 &nbsp; %s <!-- depend on flags -->
                ',
            $aEvent->cssIcon,
            $aEvent->event->code,
            $aEvent->title,
            $aEvent->startTime,
            $endTime,
            $delay,
            $this->getFormattedAEventFlags($aEvent),
            $producedBy,
            $variantsSwitcher,
            $flagsBlockHtml
        );
    }

    /**
     * @param EventSample $event
     */
    private function getEventType($event) {
        if (substr($event->code, 0, 1) == "M") {
            return self::TYPE_MAIL;
        } elseif (substr($event->code, 0, 1) == "P") {
            return self::TYPE_PLAN;
        } else {
            return self::TYPE_DIALOG;
        }
    }

    /* Title { */

    /**
     * @param $code
     * @param $step
     * @param $replicaNumber
     * @return string
     */
    public function getReplicaHintByCodeStepReplicaNumber($code, $step, $replicaNumber)
    {
        $result = $this->getAEvent($code);
        if ($result) {
            if (false == isset($this->replicas[$code])) {
                echo $code; exit;
            }
            if (false == isset($this->replicas[$code][$step])) {
                echo 'S: '.$step; exit;
            }
            if (false == isset($this->replicas[$code][$step][$replicaNumber])) {
                echo $replicaNumber; exit;
            }
            return $result->title.' -- '.$this->replicas[$code][$step][$replicaNumber]->text;
        }
        return '';
    }

    /**
     * @param $code
     * @return string
     */
    public function getEventTitleByCode($code)
    {
        $result = $this->getAEvent($code);
        if ($result) {
            return $result->title;
        }
        return '';
    }

    /**
     * @param $code
     * @return bool
     */
    public function getAEvent($code)
    {
        $code = trim($code);

        if ('MS' == substr($code, 0, 2)) {
            return false;
        }

        if ('P' == substr($code, 0, 1)) {
            return false;
        }

        if (false == in_array($code, [null, 'T', 'D2','CS4','D1'])) {
            return $this->aEvents[$code];
        }

        return false;
    }

    /**
     * @param $aEvent
     * @return mixed
     */
    private function getEventTitle($aEvent)
    {
        $methodName = 'get'.ucfirst($aEvent->type).'Title';
        return $this->{$methodName}($aEvent->event);
    }

    /**
     * @param $event
     * @return mixed
     */
    public function getDialogTitle($event)
    {
        if (false == isset($this->dialogs[$event->code])) {
            //var_dump($event->code); die;
        }
        return $this->dialogs[$event->code]->title;
    }

    /**
     * @param $event
     * @return string
     */
    public function getMailTitle($event)
    {
        return sprintf(
            '%s "%s"',
            $event->code,
            $this->emails[$event->code]->subject_obj->text
        );
    }

    /**
     * @param $event
     * @return mixed
     */
    public function getPlanTitle($event)
    {
        return $event->code;
    }

    /* Title } */

    /* CssIcon { */

    /**
     * @param $aEvent
     * @return mixed
     */
    private function getCssIcon($aEvent)
    {
        $methodName = 'get'.ucfirst($aEvent->type).'CssIcon';
        return $this->{$methodName}($aEvent->event);
    }

    /**
     * @param $event
     * @return mixed
     */
    public function getDialogCssIcon($event)
    {
        $a = [
            'visit'       => 'icon-user',
            'phone_call'  => 'icon-bell',
            'phone_talk'  => 'icon-comment',
            'knock_knock' => 'icon-briefcase',
        ];

        return $a[$this->dialogs[$event->code]->type];
    }

    /**
     * @param $event
     * @return string
     */
    public function getMailCssIcon($event)
    {
        return 'icon-envelope';
    }

    /**
     * @param $event
     * @return string
     */
    public function getPlanCssIcon($event)
    {
        return 'icon-calendar';
    }

    /* CssIcon } */

    /* RowClass { */

    /**
     * @param $aEvent
     * @return mixed
     */
    private function getCssRowColor($aEvent)
    {
        $methodName = 'get'.ucfirst($aEvent->type).'RowColor';
        return $this->{$methodName}($aEvent->event);
    }

    /**
     * @param $event
     * @return mixed
     */
    public function getDialogRowColor($event)
    {
        $a = [
            'visit'       => 'background-color: #F2FFE5;',
            'phone_call'  => 'background-color: #E5F2FF;',
            'phone_talk'  => 'background-color: #E5F2FF;',
            'knock_knock' => 'background-color: #F2FFE5;',
        ];

        return $a[$this->dialogs[$event->code]->type];
    }

    /**
     * @param $event
     * @return string
     */
    public function getMailRowColor($event)
    {
        return 'background-color: #FFFFB3;';
    }

    /**
     * @param $event
     * @return string
     */
    public function getPlanRowColor($event)
    {
        return ' ';
    }

    /* RowClass } */
}
