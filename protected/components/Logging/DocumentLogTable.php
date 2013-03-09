<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 09.03.13
 * Time: 15:03
 * To change this template use File | Settings | File Templates.
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
     * @param $logDocument LogDocument
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