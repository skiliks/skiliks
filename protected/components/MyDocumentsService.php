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
    public static function init($simId)
    {
        $sql = "insert into my_documents (sim_id, fileName, template_id, hidden)
            select :simId, fileName, id, hidden from my_documents_template where type='start'";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
    }

    /**
     * Проверяет существует ли заданный файл в рамках симуляции
     * @param int $simId
     * @param int $fileId 
     * @return MyDocumentsModel
     */
    public static function existsInSim($simId, $fileId)
    {
        return MyDocumentsModel::model()->bySimulation($simId)->byId($fileId)->find();
    }

    /**
     * Получить шаблон файла по его id
     * @param int $id 
     * @return int
     */
    public static function getTemplate($id)
    {
        $document = MyDocumentsModel::model()->byId($id)->find();
        if (!$document)
            return false;
        return $document->template_id;
    }

    /**
     * @param int $simId
     * @param int $templateId
     * @return boolean
     */
    public static function getFileIdByTemplateId($simId, $templateId)
    {
        $document = MyDocumentsModel::model()->bySimulation($simId)->byTemplateId($templateId)->find();
        if (!$document)
            return false;
        return $document->id;
    }

    /**
     * Определение айди документа по его коду
     * @param string $code
     * @return int
     */
    public static function getTemplateIdByCode($code)
    {
        $document = DocumentTemplate::model()->byCode($code)->find();

        if (!$document)
            return false;
        return $document->id;
    }

    /**
     * @return mixed array
     */
    public static function getAllCodes()
    {
        $documents = DocumentTemplate::model()->findAll();

        $list = array();
        foreach ($documents as $document) {
            $list[$document->code] = $document->id;
        }
        return $list;
    }
    
    /**
     * 
     * @param Simulations $simulation
     * @return array[]
     */
    public static function getDocumentsList($simulation)
    {
        $documents = MyDocumentsModel::model()
            ->bySimulation($simulation->id)
            ->visible()
            ->orderByFileName()
            ->findAll();

        $list = array();
        foreach ($documents as $document) {
            /**
             * @var $document MyDocumentsModel
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
     * @param Simulations $simulation
     * @param MyDocumentsModel $file
     * 
     * @return boolean
     */
    public static function makeDocumentVisibleInSimulation($simulation, $file)
    {
        $file = MyDocumentsModel::model()
            ->findByAttributes(['sim_id' => $simulation->id, 'id' => $file->primaryKey]);
        $file->hidden = 0;
        $file->save();
        $status = true;

        return $status;
    }
    
    /**
     * @param Simulations $simulation
     * @param integer $fileId
     * 
     * @return mixed array
     */
    public static function checkDocumentTime($simulation, $fileId)
    {
        $document = MyDocumentsModel::model()->findByPk($fileId);
        
        if (NULL === $document) {
            // this document not null because any simulation must have consolidated budget
            $document = MyDocumentsModel::model()
                ->bySimulation($simulation->id)
                ->byTemplateId(DocumentTemplate::CONSOLIDATED_BUDGET_ID)
                ->find();
        }
        
        $result = array();
        if(NULL == $fileId){
            $result['id']   = $document->id;
            $result['time'] = self::getFileTime($simulation, $document);
        }else{

            $result['time'] = self::getFileTime($simulation, $document);
        }
        
        return $result;
    }
    
    /**
     * @param Simulations $simulation
     * @param MyDocumentsModel $document
     * 
     * @return integer || NULL
     */
    private static function getFileTime($simulation, $document) 
    {
        $zohoDocument = new ZohoDocuments($simulation->id, $document->id, str_replace(' ', '_', $document->fileName));
        
        if(file_exists($zohoDocument->getUserFilepath())){
            $time = filemtime($zohoDocument->getUserFilepath());
            if($time !== false){
                return $time;
            }
        }
        
        return null;
    }
}

