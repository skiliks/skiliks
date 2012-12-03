<?php


/**
 * Сервис моих документов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsService {
    
    /**
     * Копируем все документы для симуляции пользователя
     * @param type $simId 
     */
    public static function init($simId) {
        $sql = "insert into my_documents (sim_id, fileName, template_id, hidden)
            select :simId, fileName, id, hidden from my_documents_template where type='start'";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);       
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
    }
    
    public static function copyToSimulation($simId, $fileId) {
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
    public static function existsInSim($simId, $fileId) {
        return MyDocumentsModel::model()->bySimulation($simId)->byId($fileId)->find();
    }
    
    /**
     * Получить шаблон файла по его id
     * @param int $id 
     * @return int
     */
    public static function getTemplate($id) {
        $document = MyDocumentsModel::model()->byId($id)->find();
        if (!$document) return false;
        return $document->template_id;
    }
    
    public static function getFileIdByTemplateId($simId, $templateId) {
        $document = MyDocumentsModel::model()->bySimulation($simId)->byTemplateId($templateId)->find();
        if (!$document) return false;
        return $document->id;
    }
    
    /**
     * Определение айди документа по его коду
     * @param string $code
     * @return int
     */
    public static function getTemplateIdByCode($code) {
        $document = MyDocumentsTemplateModel::model()->byCode($code)->find();
        
        if (!$document) return false;
        return $document->id;
    }
    
    public static function getAllCodes() {
        $documents = MyDocumentsTemplateModel::model()->findAll();
        
        $list = array();
        foreach($documents as $document) {
            $list[$document->code] = $document->id;
        }
        return $list;
    }
}


