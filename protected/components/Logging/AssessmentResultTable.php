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
        return 'Assessment results';
    }

    public function getHeaders()
    {
        return ['Point Code', 'Point ID', 'Value', 'Type Scale'];
    }

    protected function getRow($assessmentPoint)
    {
        return [
            $assessmentPoint->point->code,
            $assessmentPoint->point->title,
            $assessmentPoint->value,
            $assessmentPoint->point->type_scale
        ];
    }
}