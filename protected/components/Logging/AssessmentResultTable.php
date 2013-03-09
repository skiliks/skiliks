<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 08.03.13
 * Time: 8:23
 * To change this template use File | Settings | File Templates.
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