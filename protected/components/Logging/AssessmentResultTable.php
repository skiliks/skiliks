<?php
namespace application\components\Logging;

/**
 * \addtogroup Logging
 * @{
 */

/**
 * Class AssessmentResultTable
 *
 * Аггрегированые оценки
 */
class AssessmentResultTable extends LogTable
{
    public function getId()
    {
        return 'assessment-results';
    }

    public function getTitle()
    {
        return 'Assessment - aggregate';
    }

    public function getHeaders()
    {
        return [
            'Номер поведения',
            'Point Title',
            'Тип поведения',
            'Assessment group',
            'Оценка',
            'Коеффициент',
            'Исправленная оценка',
        ];
    }

    /**
     * @param \AssessmentAggregated $assessmentPoint
     * @return array
     */
    protected function getRow($assessmentPoint)
    {
        return [
            $assessmentPoint->point->code,
            $assessmentPoint->point->title,
            $assessmentPoint->point->type->value,
            $assessmentPoint->point->group->name,
            number_format($assessmentPoint->value, 2),
            (null == $assessmentPoint->coefficient_for_fixed_value) ? 1 : $assessmentPoint->coefficient_for_fixed_value,
            $assessmentPoint->fixed_value,
        ];
    }

    /**
     * @param $assessmentPoint
     * @return string
     */
    public function getRowId($assessmentPoint)
    {
        return sprintf(
            'assessment-aggregated-%s assessment-aggregated-learning-goal-%s ',
            $assessmentPoint[2],
            $assessmentPoint[0]
        );
    }
}
