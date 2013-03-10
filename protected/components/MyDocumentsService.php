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
     * @param type $simId 
     */
    public static function init($simulation)
    {
        $sql = "insert into my_documents (sim_id, fileName, template_id, hidden)
            select :simId, fileName, id, hidden from my_documents_template where type='start'";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simulation->id, PDO::PARAM_INT);
        $command->execute();
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

