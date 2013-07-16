<?php

class MeetingService
{
    public static function getList(Simulation $simulation)
    {
        $meetings = $simulation->game_type->getMeetings([]);

        return array_map(function(Meeting $meeting) {
            return [
                'id' => $meeting->id,
                'label' => $meeting->task->title,
                'description' => $meeting->label,
                'duration' => $meeting->duration
            ];
        }, $meetings);
    }
}