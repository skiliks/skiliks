<?php

class MeetingService
{
    public static function getList(Simulation $simulation)
    {
        $logList = LogMeeting::model()->findAllByAttributes([
            'sim_id' => $simulation->id
        ]);

        $usedIds = array_map(function(LogMeeting $item) {
            return $item->meeting_id;
        }, $logList);

        $criteria = new CDbCriteria();
        $criteria->addNotInCondition('id', $usedIds);
        $meetings = $simulation->game_type->getMeetings($criteria);

        return array_map(function(Meeting $meeting) {
            return [
                'id' => $meeting->id,
                'label' => $meeting->task->title,
                'description' => $meeting->label,
                'duration' => $meeting->duration
            ];
        }, $meetings);
    }

    public static function leave(Simulation $simulation, $meetingId)
    {
        if (empty($meetingId)) {
            throw new LogicException('Meeting ID was not specified');
        }

        /** @var Meeting $meeting */
        $meeting = Meeting::model()->findByPk($meetingId);
        if (empty($meeting)) {
            throw new LogicException('Meeting was not found');
        }

        LogHelper::setMeetingLog($meeting, $simulation);

        $currentTime = explode(':', $simulation->getGameTime());
        $shiftedTime = $currentTime[0] * 60 + $currentTime[1] + $meeting->duration + 1; // 1 for skipped seconds
        SimulationService::setSimulationClockTime($simulation, floor($shiftedTime / 60), $shiftedTime % 60);
    }
}