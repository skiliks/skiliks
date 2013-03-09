<?php
namespace application\components\Logging;

/**
 * Class DocumentLogTable
 * @package application\components\Logging
 */
class DocumentLogTable extends LogTable {
    public function getId()
    {
        return 'document-log';
    }

    public function getTitle()
    {
        return 'Document log';
    }

    public function getHeaders()
    {
        return
            ['Код документа',	'Наименование документа',	'Игровое время - start',	'Игровое время - end'];
    }

    /**
     * @param $logDocument \LogDocument
     * @return array|void
     */
    protected function getRow($logDocument)
    {
        return [
            $logDocument->file->template->code,
            $logDocument->file->template->srcFile,
            $logDocument->start_time,
            $logDocument->end_time
        ];
    }

}