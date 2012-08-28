<?php


/**
 * Description of MyDocumentsService
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
            select :simId, fileName, id, hidden from my_documents_template";
        
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
    public static function existsInSim($simId, $fileId) {
        return MyDocumentsModel::model()->bySimulation($simId)->byId($fileId)->find();
    }
}

?>
