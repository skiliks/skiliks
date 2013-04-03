<?php

/**
 * Сервис моих документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsService
{

    /**
     * Копируем все документы для симуляции пользователя
     * @param Simulation $simulation
     */
    public static function init(Simulation $simulation)
    {
        /** @var $documentTemplates DocumentTemplate[] */
        $documentTemplates = $simulation->game_type->getDocumentTemplates(['type' => 'start']);
        foreach ($documentTemplates as $documentTemplate) {
            $document = new MyDocument();
            $document->sim_id = $simulation->getPrimaryKey();
            $document->fileName = $documentTemplate->fileName;
            $document->template_id = $documentTemplate->getPrimaryKey();
            $document->hidden = $documentTemplate->hidden;
            $document->save();
        }
    }

    /**
     * 
     * @param Simulation $simulation
     * @return array[]
     */
    public static function getDocumentsList($simulation)
    {
        $documents = MyDocument::model()
            ->bySimulation($simulation->id)
            ->visible()
            ->orderByFileName()
            ->findAll();

        $list = array();
        foreach ($documents as $document) {
            /**
             * @var $document MyDocument
             */
            $list[] = array(
                'id' => $document->id,
                'name' => $document->template->fileName,
                'srcFile' => $document->template->srcFile,
                'mime' => $document->template->getMimeType()
            );
        }
        
        return $list;
    }
    
    /**
     * 
     * @param Simulation $simulation
     * @param MyDocument $file
     * 
     * @return boolean
     */
    public static function makeDocumentVisibleInSimulation($simulation, $file)
    {
        $file = MyDocument::model()
            ->findByAttributes(['sim_id' => $simulation->id, 'id' => $file->primaryKey]);
        $file->hidden = 0;
        $file->save();
        $status = true;

        return $status;
    }
}

