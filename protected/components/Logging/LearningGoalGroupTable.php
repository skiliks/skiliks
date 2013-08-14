<?php
namespace application\components\Logging;

class LearningGoalGroupTable extends LogTable
{
    public function getId()
    {
        return 'learning-goal-group';
    }

    public function getTitle()
    {
        return 'Learning Goal Group';
    }

    public function getHeaders()
    {
        return [
            'Code',
            'Text',
            'Coefficient K',
            'Percent',
            'Problem',
            'Max negative',
            'Max positive',
            'Total negative',
            'Total positive',
        ];
    }

    /**
     * @param \SimulationLearningGoalGroup $row
     * @return array
     * @throws \Exception
     */
    protected function getRow($row)
    {
        return [
            $row->learningGoalGroup->code,
            $row->learningGoalGroup->title,
            $row->coefficient,
            $row->percent,
            $row->problem,
            $row->max_negative,
            $row->max_positive,
            $row->total_negative,
            $row->total_positive
        ];
    }


    public function getRowId($row)
    {
        return sprintf(
            'learning-goal-code-group-%s ',
            $row[0]
        );
    }
}
/**
 * @}
 */