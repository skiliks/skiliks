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
            'Assessment group',
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

            return [
                $row->point->learning_goal->code,
                $row->point->learning_goal->title,
                $row->point->code,
                $row->point->title,
                $row->point->type->value,
                $row->point->scale,
                $row->point->group->name,
                $row->value
            ];

    }
}
/**
 * @}
 */