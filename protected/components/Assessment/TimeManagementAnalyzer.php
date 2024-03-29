<?php

/**
 * Эфективность использования времени
 */
class TimeManagementAnalyzer
{
    /**
     * @var null|Simulation
     */
    public $simulation = null;

    /**
     * Сколько человек пересидел
     * @var null
     */
    public $GameOverhead = null;

    /**
     * Сколько было потрачено на задачи первого приоритета
     * @var null
     */
    public $firstPriorityTotal = null;

    /**
     * Задпачинове время по групам
     * @var array
     */
    public $durationsGrouped = [
        '1st_priority' => [
            TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS   => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL        => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS    => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING     => 0,
            'total' => 0,
        ],
        'non_priority' => [
            TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS   => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL        => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS    => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING     => 0,
            'total' => 0,
        ],
        'inactivity' => [
            'total' => 0,
        ],
    ];

    /**
     * @param Simulation $simulation
     */
    public function __construct($simulation)
    {
        $simulation->refresh();
        $this->simulation = $simulation;
    }

    /**
     * Расчет и сохранение оценок
     */
    public function calculateAndSaveAssessments()
    {
        $this->calculateGameOverhead();

        $this->prepareDurationsForCalculation();

        $this->calculateDetailedTimeSpend();
        $this->calculateGlobalTimeSpend();

        $this->calculateEfficiency();
    }

    /**
     * Расчет времени которое человек пересидел на работе
     */
    public function calculateGameOverhead()
    {
        $endLog = LogActivityActionAggregated::model()->find(sprintf(
            " sim_id = %s ORDER BY end_time DESC ",
            $this->simulation->id
        ));

        if (null == $endLog) {
            $GameOverhead = 0;
        } else {
            list($startHours, $startMinutes, $startSeconds) = explode(':', $this->simulation->game_type->end_time);
            list($endHours, $endMinutes, $endSeconds) = explode(':', $endLog->end_time);

            $GameOverhead = ($endHours*60 + $endMinutes
                - $startHours*60 - $startMinutes);

            // protect from DEV mode sim stop
            if ($GameOverhead < 0) {
                $GameOverhead = 0;
            }
        }

        $assessment = new TimeManagementAggregated();
        $assessment->slug = TimeManagementAggregated::SLUG_WORKDAY_OVERHEAD_DURATION;
        $assessment->sim_id = $this->simulation->id;
        $assessment->value = $GameOverhead;
        $assessment->unit_label = TimeManagementAggregated::getUnitLabel(TimeManagementAggregated::SLUG_WORKDAY_OVERHEAD_DURATION);
        $assessment->save();

        $this->GameOverhead = $GameOverhead;
    }


    /**
     * Method calculates and stores efficiency (whole and overtime)
     * $this->GameOverhead => overtime in minutes
     * $this->firstPriorityTotal -> time spend for the primary tasks
     * $value is total time efficiency in percents
     */

    /**
     * Метод считает и записывает в базу эффективность затраченного времени (рабочего времени и овертайм)
     * $this->GameOverhead => овертайм время в минутах
     * $this->firstPriorityTotal => время потраченное на выполнение задач 1-й категории
     * $value => общая эффективность в процентах
     */

    public function calculateEfficiency()
    {
        $overTimePercentage = (1 - $this->GameOverhead/120)*100;

        $value = round($this->firstPriorityTotal*2/3 + $overTimePercentage * 1/3,2);

        $assessment = new TimeManagementAggregated();
        $assessment->slug = TimeManagementAggregated::SLUG_EFFICIENCY;
        $assessment->sim_id = $this->simulation->id;
        $assessment->value = $value;
        $assessment->unit_label = TimeManagementAggregated::getUnitLabel(TimeManagementAggregated::SLUG_EFFICIENCY);
        $assessment->save();
    }

    /**
     * Расчет по групам затраченого времени
     */
    public function prepareDurationsForCalculation()
    {
        $timeSpendActivityCodes = ['A_wait','A_wrong_call','A_not_sent','A_incorrect_sent'];

        foreach ($this->simulation->log_activity_actions_aggregated as $logItem) {

            $itemLegType = $logItem->activityAction->leg_type;
            $itemActivityCode = $logItem->activityAction->activity->code;

            if (in_array($itemActivityCode, $timeSpendActivityCodes)) {
                //$this->durationsGrouped['inactivity']['total'] += TimeTools::timeToSeconds($logItem->duration);
                continue;
            }

            // 1st priority
            if (in_array($logItem->category, [0, 1, 2, '2_min', '0', '1', '2']) ||
                $logItem->keep_last_category_after_60_sec === LogActivityActionAggregated::KEEP_LAST_CATEGORY_YES) {
                // 1st, doc
                if (ActivityAction::LEG_TYPE_DOCUMENTS == $itemLegType) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['1st_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // 1st, meeting
                if ((ActivityAction::LEG_TYPE_MANUAL_DIAL == $itemLegType || ActivityAction::LEG_TYPE_SYSTEM_DIAL == $itemLegType || ActivityAction::LEG_TYPE_MEETING == $itemLegType)
                    && (null === $logItem->activityAction->dialog || $logItem->activityAction->dialog->dialogSubtype->isMeeting())) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['1st_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // 1st, phone call
                if ((ActivityAction::LEG_TYPE_MANUAL_DIAL == $itemLegType || ActivityAction::LEG_TYPE_SYSTEM_DIAL == $itemLegType)
                    && $logItem->activityAction->dialog->dialogSubtype->isPhoneCall()) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['1st_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // 1st, mail
                if (ActivityAction::LEG_TYPE_INBOX == $itemLegType ||
                    ActivityAction::LEG_TYPE_OUTBOX == $itemLegType) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['1st_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // 1st, plan
                if (ActivityAction::LEG_TYPE_WINDOW == $itemLegType &&
                    $logItem->activityAction->isPlan()) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['1st_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }
            } else {
                // non priority
                // non, doc
                if (ActivityAction::LEG_TYPE_DOCUMENTS == $itemLegType) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['non_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // non, meeting
                if ((ActivityAction::LEG_TYPE_MANUAL_DIAL == $itemLegType || ActivityAction::LEG_TYPE_SYSTEM_DIAL == $itemLegType || ActivityAction::LEG_TYPE_MEETING == $itemLegType)
                    && (null === $logItem->activityAction->dialog || $logItem->activityAction->dialog->dialogSubtype->isMeeting())) {;
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['non_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // non, phone call
                if ((ActivityAction::LEG_TYPE_MANUAL_DIAL == $itemLegType || ActivityAction::LEG_TYPE_SYSTEM_DIAL == $itemLegType)
                    && $logItem->activityAction->dialog->dialogSubtype->isPhoneCall()) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['non_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // non, mail
                if (ActivityAction::LEG_TYPE_INBOX == $itemLegType ||
                    ActivityAction::LEG_TYPE_OUTBOX == $itemLegType) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['non_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }

                // non, plan
                if (ActivityAction::LEG_TYPE_WINDOW == $itemLegType &&
                    $logItem->activityAction->isPlan()) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING]
                        += TimeTools::timeToSeconds($logItem->duration);
//                    $this->durationsGrouped['non_priority']['total']
//                        += TimeTools::timeToSeconds($logItem->duration);
                    continue;
                }
            }
        }
    }

    /**
     * Расчет общих показателей эфективности затрат времени
     */
    public function calculateGlobalTimeSpend()
    {
        // seconds
        $totalTime = $this->durationsGrouped['inactivity']['total']
            + $this->durationsGrouped['non_priority']['total']
            + $this->durationsGrouped['1st_priority']['total'];

        $assessment_1st = new TimeManagementAggregated();
        $assessment_1st->sim_id = $this->simulation->id;
        $assessment_1st->slug = TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES;
        if (0 == $this->durationsGrouped['1st_priority']['total']) {
            $assessment_1st->value = 0;
        } else {
            $assessment_1st->value = round($this->durationsGrouped['1st_priority']['total']*100 / $totalTime, 2);
        }
        $assessment_1st->unit_label = TimeManagementAggregated::getUnitLabel(TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_1ST_PRIORITY_ACTIVITIES);
        $assessment_1st->save();

        $this->firstPriorityTotal = $assessment_1st->value;
        if($this->firstPriorityTotal >= 95){
            $this->firstPriorityTotal = 100;
        }

        $assessment_non = new TimeManagementAggregated();
        $assessment_non->sim_id = $this->simulation->id;
        $assessment_non->slug = TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES;
        if (0 == $this->durationsGrouped['non_priority']['total']) {
            $assessment_non->value = 0;
        } else {
            $assessment_non->value = round($this->durationsGrouped['non_priority']['total']*100 / $totalTime, 2);
        }
        $assessment_non->unit_label = TimeManagementAggregated::getUnitLabel(TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_NON_PRIORITY_ACTIVITIES);
        $assessment_non->save();

        $assessment_i = new TimeManagementAggregated();
        $assessment_i->sim_id = $this->simulation->id;
        $assessment_i->slug = TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY;
        $assessment_i->value = 100 - $assessment_1st->value - $assessment_non->value; // to protect against round differences
        $assessment_i->unit_label = TimeManagementAggregated::getUnitLabel(TimeManagementAggregated::SLUG_GLOBAL_TIME_SPEND_FOR_INACTIVITY);
        $assessment_i->save();
    }

    /**
     * Расчет детальных показателей затрат времени
     */
    public function calculateDetailedTimeSpend()
    {
        $first = $this->durationsGrouped['1st_priority'];
        $non = $this->durationsGrouped['non_priority'];

        $this->durationsGrouped['1st_priority']['total'] = 0;
        $this->durationsGrouped['non_priority']['total'] = 0;

        /* 1st { */

        $slug = TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS;
        $assessment_doc = new TimeManagementAggregated();
        $assessment_doc->sim_id = $this->simulation->id;
        $assessment_doc->slug = $slug;
        $assessment_doc->value = round($first[$slug]/ 60);
        $assessment_doc->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_doc->save();
        $this->durationsGrouped['1st_priority'][$slug] = $assessment_doc->value * 60;
        $this->durationsGrouped['1st_priority']['total'] += $assessment_doc->value * 60;

        $slug = TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS;
        $assessment_meet = new TimeManagementAggregated();
        $assessment_meet->sim_id = $this->simulation->id;
        $assessment_meet->slug = $slug;
        $assessment_meet->value = round($first[$slug]/ 60);
        $assessment_meet->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_meet->save();
        $this->durationsGrouped['1st_priority'][$slug] = $assessment_meet->value * 60;
        $this->durationsGrouped['1st_priority']['total'] += $assessment_meet->value * 60;

        $slug = TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS;
        $assessment_call = new TimeManagementAggregated();
        $assessment_call->sim_id = $this->simulation->id;
        $assessment_call->slug = $slug;
        $assessment_call->value = round($first[$slug]/ 60);
        $assessment_call->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_call->save();
        $this->durationsGrouped['1st_priority'][$slug] = $assessment_call->value * 60;
        $this->durationsGrouped['1st_priority']['total'] += $assessment_call->value * 60;

        $slug = TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL;
        $assessment_mail = new TimeManagementAggregated();
        $assessment_mail->sim_id = $this->simulation->id;
        $assessment_mail->slug = $slug;
        $assessment_mail->value = round($first[$slug]/ 60);
        $assessment_mail->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_mail->save();
        $this->durationsGrouped['1st_priority'][$slug] = $assessment_mail->value * 60;
        $this->durationsGrouped['1st_priority']['total'] += $assessment_mail->value * 60;

        $slug = TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING;
        $assessment_plan = new TimeManagementAggregated();
        $assessment_plan->sim_id = $this->simulation->id;
        $assessment_plan->slug = $slug;
        $assessment_plan->value = round($first[$slug]/ 60);
        $assessment_plan->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_plan->save();
        $this->durationsGrouped['1st_priority'][$slug] = $assessment_plan->value * 60;
        $this->durationsGrouped['1st_priority']['total'] += $assessment_plan->value * 60;

        /* 1st } */

        unset($assessment_doc, $assessment_meet, $assessment_call, $assessment_plan, $assessment_mail);

        /* non { */

        $slug = TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS;
        $assessment_doc = new TimeManagementAggregated();
        $assessment_doc->sim_id = $this->simulation->id;
        $assessment_doc->slug = $slug;
        $assessment_doc->value = round($non[$slug]/ 60);
        $assessment_doc->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_doc->save();
        $this->durationsGrouped['non_priority'][$slug] = $assessment_doc->value * 60;
        $this->durationsGrouped['non_priority']['total'] += $assessment_doc->value * 60;

        $slug = TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS;
        $assessment_meet = new TimeManagementAggregated();
        $assessment_meet->sim_id = $this->simulation->id;
        $assessment_meet->slug = $slug;
        $assessment_meet->value = round($non[$slug]/ 60);
        $assessment_meet->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_meet->save();
        $this->durationsGrouped['non_priority'][$slug] = $assessment_meet->value * 60;
        $this->durationsGrouped['non_priority']['total'] += $assessment_meet->value * 60;

        $slug = TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS;
        $assessment_call = new TimeManagementAggregated();
        $assessment_call->sim_id = $this->simulation->id;
        $assessment_call->slug = $slug;
        $assessment_call->value = round($non[$slug]/ 60);
        $assessment_call->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_call->save();
        $this->durationsGrouped['non_priority'][$slug] = $assessment_call->value * 60;
        $this->durationsGrouped['non_priority']['total'] += $assessment_call->value * 60;

        $slug = TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL;
        $assessment_mail = new TimeManagementAggregated();
        $assessment_mail->sim_id = $this->simulation->id;
        $assessment_mail->slug = $slug;
        $assessment_mail->value = round($non[$slug]/ 60);
        $assessment_mail->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_mail->save();
        $this->durationsGrouped['non_priority'][$slug] = $assessment_mail->value * 60;
        $this->durationsGrouped['non_priority']['total'] += $assessment_mail->value * 60;

        $slug = TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING;
        $assessment_plan = new TimeManagementAggregated();
        $assessment_plan->sim_id = $this->simulation->id;
        $assessment_plan->slug = $slug;
        $assessment_plan->value = round($non[$slug]/ 60);
        $assessment_plan->unit_label = TimeManagementAggregated::getUnitLabel($slug);
        $assessment_plan->save();
        $this->durationsGrouped['non_priority'][$slug] = $assessment_plan->value * 60;
        $this->durationsGrouped['non_priority']['total'] += $assessment_plan->value * 60;

        /* non } */

        $this->durationsGrouped['inactivity']['total'] =
            8*60*60 + 15*60 + $this->GameOverhead*60
            - $this->durationsGrouped['1st_priority']['total']
            - $this->durationsGrouped['non_priority']['total'];
    }
}