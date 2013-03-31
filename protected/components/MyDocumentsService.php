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
        $sql = "insert into my_documents (sim_id, fileName, template_id, hidden)
            select :simId, fileName, id, hidden from my_documents_template where type='start' AND scenario_id=:scenario_id";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simulation->id, PDO::PARAM_INT);
        $scenarioId = $simulation->game_type->getPrimaryKey();
        $command->bindParam(":scenario_id", $scenarioId, PDO::PARAM_INT);
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

