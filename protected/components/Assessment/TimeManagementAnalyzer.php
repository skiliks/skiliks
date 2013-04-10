<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 4/10/13
 * Time: 6:36 PM
 * To change this template use File | Settings | File Templates.
 */

class TimeManagementAnalyzer
{
    /**
     * @var null|Simulation
     */
    public $simulation = null;

    /**
     * @var int
     */
    public $gameDurationBySimStartAndSimStop = 0;

    /**
     * @var array
     */
    public $durationsGrouped = [
        '1st_priority' => [
            TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS   => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL        => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS    => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS => 0,
            TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING     => 0,
        ],
        'non_priority' => [
            TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS   => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL        => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS    => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS => 0,
            TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING     => 0,
        ],
    ];

    /**
     * @param Simulation $simulation
     */
    public function __construct($simulation)
    {
        $this->simulation = $simulation;
    }

    public function calculateAndSaveAssessments()
    {
        $this->calculateGameDurationBySimStartAndSimStop();

        $assessment = new TimeManagementAggregated();
        $assessment->slug = TimeManagementAggregated::SLUG_WORKDAY_DURATION;
        $assessment->sim_id = $this->simulation->id;
        $assessment->value = $this->gameDuration;
        $assessment->unit_label = TimeManagementAggregated::UNIT_LABEL_WORKDAY_DURATION;
        $assessment->save();
    }

    public function calculateGameDurationBySimStartAndSimStop()
    {
        $startLog = UniversalLog::model()->find(sprintf(
            " simulation = %s ORDER BY start_time ASC ",
            $this->simulation->id
        ));

        $endLog = UniversalLog::model()->find(sprintf(
            " simulation = %s ORDER BY end_time DESC ",
            $this->simulation->id
        ));

        list($startHours, $startMinutes, $startSeconds) = explode(':', $startLog->start_time);
        list($endHours, $endMinutes, $endSeconds) = explode(':', $endLog->end_time);

        $this->gameDurationBySimStartAndSimStop = ($startHours*60*60 + $startMinutes*60 + $startSeconds
            - $endHours*60*60 - $endMinutes*60 - $endSeconds);
    }

    public function prepareDurationsForCalculation()
    {
        foreach ($this->simulation->log_activity_actions_aggregated as $logItem) {

            $itemLegType = $logItem->activityAction->leg_type;

            // 1st priority
            if (in_array($logItem->category, [0, 1, 2, '2_min', '0', '1', '2'])) {
                // 1st, doc
                if (ActivityAction::LEG_TYPE_DOCUMENTS == $itemLegType) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_DOCUMENTS]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, meeting
                if (ActivityAction::LEG_TYPE_MANUAL_DIAL == $itemLegType &&
                    $logItem->ActivityAction->Replica->dialogSubtype->isMeeting()) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MAIL]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, phone call
                if (ActivityAction::LEG_TYPE_SYSTEM_DIAL == $itemLegType &&
                    $logItem->ActivityAction->Replica->dialogSubtype->isPhoneCall()) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_MEETINGS]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, mail
                if (ActivityAction::LEG_TYPE_INBOX == $itemLegType ||
                    ActivityAction::LEG_TYPE_OUTBOX == $itemLegType) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PHONE_CALLS]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, plan
                if (ActivityAction::LEG_TYPE_WINDOW == $itemLegType &&
                    $logItem->ActivityAction->isPlan()) {
                    $this->durationsGrouped['1st_priority'][TimeManagementAggregated::SLUG_1ST_PRIORITY_PLANING]
                        += $logItem->getDurationInSeconds();
                    continue;
                }
            } else {
                // non priority
                // 1st, doc
                if (ActivityAction::LEG_TYPE_DOCUMENTS == $itemLegType) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_DOCUMENTS]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, meeting
                if (ActivityAction::LEG_TYPE_MANUAL_DIAL == $itemLegType &&
                    $logItem->ActivityAction->Replica->dialogSubtype->isMeeting()) {;
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_MAIL]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, phone call
                if (ActivityAction::LEG_TYPE_SYSTEM_DIAL == $itemLegType &&
                    $logItem->ActivityAction->Replica->dialogSubtype->isPhoneCall()) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_MEETINGS]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, mail
                if (ActivityAction::LEG_TYPE_INBOX == $itemLegType ||
                    ActivityAction::LEG_TYPE_OUTBOX == $itemLegType) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_PHONE_CALLS]
                        += $logItem->getDurationInSeconds();
                    continue;
                }

                // 1st, plan
                if (ActivityAction::LEG_TYPE_WINDOW == $itemLegType &&
                    $logItem->ActivityAction->isPlan()) {
                    $this->durationsGrouped['non_priority'][TimeManagementAggregated::SLUG_NON_PRIORITY_PLANING]
                        += $logItem->getDurationInSeconds();
                    continue;
                }
            }
        }
    }

    public function calculateGlobalTimeSpend()
    {

    }

    public function calculateDetailedTimeSpend()
    {

    }
}