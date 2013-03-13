<?php
namespace application\components\Logging;

    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Значения оценки по mail box
 */
class AssessmentCalculationTable extends LogTable
{
    public function getId()
    {
        return 'assessment-calculation';
    }

    public function getTitle()
    {
        return 'Assessment - calculation';
    }

    public function getHeaders()
    {
        return [
            'Номер цели обучения',
            'Наименование цели обучения',
            'Номер поведения',
            'Наименование поведения',
            'Тип поведения',
            'Вес поведения',
            'Тип оценки',
            'Значение',
        ];
    }

    /**
     * @param $assessmentCalculation
     * @return string
     */
    public function getRowId($assessmentCalculation)
    {
        return sprintf(
            'assessment-calculation-%s assessment-calculation-learning-goal-%s ',
            $assessmentCalculation[2],
            $assessmentCalculation[0]
        );
    }

    /**
     * @param \AssessmentCalculation $row
     * @return array
     * @throws \Exception
     */
    protected function getRow($row)
    {
        if ($row->point->learning_goal_code == 331 or $row->point->learning_goal_code == 332) {
            $assessmentType = '3. Оценка Mail Inbox';
        } else if ($row->point->learning_goal_code == 333) {
            $assessmentType = '4. Оценка Mail Outbox';
        } else {
            $assessmentType = '-';
        }

        return [
            $row->point->learning_goal->code,
            $row->point->learning_goal->title,
            $row->point->code,
            $row->point->title,
            $row->point->type_scale,
            $row->point->scale,
            $assessmentType,
            $row->value
        ];
    }
}
/**
 * @}
 */