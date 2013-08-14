<?php

namespace application\components\Logging;


class LogAssessment214gTable extends LogTable
{
    public function getId()
    {
        return 'log-assessment-214g';
    }

    public function getTitle()
    {
        return 'Assessment log 214g';
    }

    public function getHeaders()
    {
        return [
            'Code',
            'Parent',
            'Start time'
        ];
    }

    /**
     * @param \LogAssessment214g $row
     * @return array
     */
    protected function getRow($row)
    {

        return [
            $row->code,
            $row->parent,
            $row->start_time,
        ];
    }


    public function getRowId($logMail)
    {
        return '';
    }
}