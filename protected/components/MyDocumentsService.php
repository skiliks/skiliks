<?php


/**
 * Description of MyDocumentsService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MyDocumentsService {
    
    public static function init($simId) {
        $sql = "insert into my_documents (sim_id, fileName)
            select :simId, fileName from my_documents_template";
        
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);       
        $command->bindParam(":simId", $simId, PDO::PARAM_INT);
        $command->execute();
    }
}

?>
