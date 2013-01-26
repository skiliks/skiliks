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

    public static function copyToSimulation($simId, $fileId)
    {
        $sql = "insert into my_documents (sim_id, fileName, template_id, hidden)
            select :simId, fileName, id, hidden from my_documents_template where id = :fileId ";

        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->bindParam(":fileId", $fileId, PDO::PARAM_INT);
        $command->execute();
        return $connection->getLastInsertID();
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
        $document = MyDocumentsTemplateModel::model()->byCode($code)->find();

        if (!$document)
            return false;
        return $document->id;
    }

    /**
     * @return mixed array
     */
    public static function getAllCodes()
    {
        $documents = MyDocumentsTemplateModel::model()->findAll();

        $list = array();
        foreach ($documents as $document) {
            $list[$document->code] = $document->id;
        }
        return $list;
    }

    /**
     * @param integer $fileId
     * 
     * @throws Exception
     * 
     * @return array of string, paths to file pages (images)
     */
    public static function getDocumentPages($fileId)
    {
        // получить шаблон файла
        $templateId = self::getTemplate($fileId);
        $pages = array();
        $fileId = 0;
        
        if (!$templateId) {
            throw new Exception("Немогу определить шаблон для файла {$fileId}");
        }

        $items = ViewerTemplateModel::model()->byFile($templateId)->findAll();

        foreach($items as $item) {
            $fileId = $item->file_id;
            $pages[] = $item->filePath;
        }

        // получим кода файлов
        $file = MyDocumentsTemplateModel::model()->byId($fileId)->find();

        foreach($pages as $index => $page) {
            $pages[$index] = $file->code.'/'.$page;
        }
        
        return $pages;
    }
    
    /**
     * 
     * @param Simulations $simulation
     * @return mixed array
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
            $list[] = array(
                'id' => $document->id,
                'name' => $document->template->srcFile,
                'mime' => $document->template->getMimeType()
            );
        }
        
        return $list;
    }
    
    /**
     * 
     * @param Simulations $simulation
     * @param integer || NULL $fileId
     * 
     * @return boolean
     */
    public static function makeDocumentVisibleInSimulation($simulation, $fileId)
    {
        $status = false;
        $file = MyDocumentsModel::model()
            ->bySimulation($simulation->id)
            ->byId($fileId)
            ->find();
        
        if (null !== $file) {
            $file->hidden = 0;
            $file->save();
            $status = true;
        }
        
        return $status;
    }
    
    /**
     * @param Simulation $simulation
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
                ->byTemplateId(MyDocumentsTemplateModel::CONSOLIDATED_BUDGET_ID)
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
     * @param tSimulation $simulation
     * @param MyDocument $document
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

