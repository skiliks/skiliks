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
        /** @var Meeting[] $meetings */
        $meetings = $simulation->game_type->getMeetings($criteria);

        $result = [];
        foreach ($meetings as $meeting) {
            if (FlagsService::isAllowToStartMeeting($meeting, $simulation)) {
                $result[] = [
                    'id' => $meeting->id,
                    'label' => $meeting->icon_text,
                    'description' => $meeting->popup_text,
                    'duration' => $meeting->duration
                ];
            }
        }

        return $result;
    }

    public static function leave(Simulation $simulation, $meetingId)
    {
        $currentTime = explode(':', $simulation->getGameTime()); // get time asap for better accuracy

        if (empty($meetingId)) {
            throw new LogicException('Meeting ID was not specified');
        }

        /** @var Meeting $meeting */
        $meeting = Meeting::model()->findByPk($meetingId);
        if (empty($meeting)) {
            throw new LogicException('Meeting was not found');
        }

        // We need this to prevent choosing same meeting before next getState brings correct logs {
        $log = new LogMeeting();
        $log->sim_id = $simulation->id;
        $log->meeting_id = $meeting->id;
        $log->start_time = $simulation->getGameTime();
        $log->save();
        // We need this to prevent choosing same meeting before next getState brings correct logs }

        //$currentTime = explode(':', $simulation->getGameTime());
        $shiftedTime = $currentTime[0] * 60 + $currentTime[1] + $meeting->duration;
        //SimulationService::setSimulationClockTime($simulation, floor($shiftedTime / 60), $shiftedTime % 60);

        return floor($shiftedTime / 60) . ':' . ($shiftedTime % 60);
    }
}