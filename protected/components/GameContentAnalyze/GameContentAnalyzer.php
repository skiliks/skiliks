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

    const TYPE_PLAN   = 'plan';
    const TYPE_MAIL   = 'mail';
    const TYPE_DIALOG = 'dialog';

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

            $this->setUpReplicas($aEvent);

            $this->aEvents[trim($aEvent->event->code)] = $aEvent;
        }
    }

    public function updateProducedBy() {
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

    public function updateDelays() {
        foreach ($this->dialogs as $dialog) {
            $this->aEvents[$dialog->code]->delay = (int)$dialog->delay;
        }
    }

    public function separateEvents() {
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
                $aEvent->startTime   = substr((string)$aEvent->event->trigger_time, 0, 5);
                $this->eventsStartedByTime[$aEvent->event->code] = $aEvent;
            }
        }
    }

    public function initHoursChain() {
        $i = 0;
        $hours   = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'];

        foreach ($this->eventsStartedByTime as $event) {
            if ($hours[$i] <= $event->startTime) {
                $i++;
                $this->hoursChainOfEventsStartedByTime[$i] = new HourForContentAnalyze();
            }

            $this->hoursChainOfEventsStartedByTime[$i]->events[] = $event;
            $this->hoursChainOfEventsStartedByTime[$i]->title = $hours[$i-1];
        }

        // clean up memory
        $this->eventsStartedByTime = [];
    }

    /**
     * @param Replica $events
     */
    public function uploadDialogs($dialogs) {
        foreach ($dialogs as $dialog) {
            $this->dialogs[$dialog->code] = $dialog;
        }
    }

    /**
     * @param Replica $events
     */
    public function uploadReplicas($replicas) {
        foreach ($replicas as $replica) {
            $this->replicas[$replica->code][$replica->step_number][$replica->replica_number] = $replica;
        }
    }

    /**
     * @param Replica $events
     */
    public function uploadEmails($emails) {
        foreach ($emails as $email) {
            $this->emails[$email->code] = $email;
        }
    }

    public function setUpReplicas($aEvent) {
        if (isset($this->replicas[$aEvent->event->code])) {
            $aEvent->replicas = $this->replicas[$aEvent->event->code];
        }
    }

    public function updateDurations() {
        foreach ($this->aEvents as $aEvent) {
            $aEvent->duration = count($aEvent->replicas)*1.3; // 1 min 20 sec to read and answer one dialog step
        }
    }

    public function updatePossibleNextEvents() {
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

    public function getEventTitleByCode($code) {
        $result = $this->getAEvent($code);
        if ($result) {
            return $result->title;
        }
        return '';
    }

    public function getEventDurationByCode($code) {
        $result = $this->getAEvent($code);
        if ($result) {
            return $result->duration;
        }
        return 0;
    }

    public function getAEvent($code) {
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

    private function getEventTitle($aEvent) {
        $methodName = 'get'.ucfirst($aEvent->type).'Title';
        return $this->{$methodName}($aEvent->event);
    }

    public function getDialogTitle($event) {
        if (false == isset($this->dialogs[$event->code])) {
        var_dump($event->code); die;
    }
        return $this->dialogs[$event->code]->title;
    }

    public function getMailTitle($event) {
        return sprintf(
            '%s "%s"',
            $event->code,
            $this->emails[$event->code]->subject_obj->text
        );
    }

    public function getPlanTitle($event) {
        return $event->code;
    }

    /* Title } */

    /* CssIcon { */

    private function getCssIcon($aEvent) {
        $methodName = 'get'.ucfirst($aEvent->type).'CssIcon';
        return $this->{$methodName}($aEvent->event);
    }

    public function getDialogCssIcon($event) {
        $a = [
            'visit'       => 'icon-user',
            'phone_call'  => 'icon-bell',
            'phone_talk'  => 'icon-comment',
            'knock_knock' => 'icon-briefcase',
        ];

        return $a[$this->dialogs[$event->code]->type];
    }

    public function getMailCssIcon($event) {
        return 'icon-envelope';
    }

    public function getPlanCssIcon($event) {
        return 'icon-calendar';
    }

    /* CssIcon } */

    /* RowClass { */

    private function getCssRowColor($aEvent) {
        $methodName = 'get'.ucfirst($aEvent->type).'RowColor';
        return $this->{$methodName}($aEvent->event);
    }

    public function getDialogRowColor($event) {
        $a = [
            'visit'       => 'background-color: #F2FFE5;',
            'phone_call'  => 'background-color: #E5F2FF;',
            'phone_talk'  => 'background-color: #E5F2FF;',
            'knock_knock' => 'background-color: #F2FFE5;',
        ];

        return $a[$this->dialogs[$event->code]->type];
    }

    public function getMailRowColor($event) {
        return 'background-color: #FFFFB3;';
    }

    public function getPlanRowColor($event) {
        return ' ';
    }

    /* RowClass } */
}
