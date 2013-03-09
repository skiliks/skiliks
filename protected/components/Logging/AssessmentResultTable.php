<?php
namespace application\components\Logging;


/**
 * Class AssessmentResultTable
 * @package application\components\Logging
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
        return ['Номер поведения', 'Point Title', 'Тип поведения', 'Оценка по поведению'];
    }

    /**
     * @param $assessmentPoint AssessmentAggregated
     * @return array
     */
    protected function getRow($assessmentPoint)
    {
        return [
            $assessmentPoint->point->code,
            $assessmentPoint->point->title,
            $assessmentPoint->point->getTypeScaleTitle(),
            $assessmentPoint->value,
        ];
    }
}