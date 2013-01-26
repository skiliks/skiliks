<?php

set_time_limit(0);

/**
 * Сервис по работе с документом Excel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentService {
    
    public static function existsInSimulation($simId, $fileId, $excelTemplateId) {
        $document = ExcelDocumentModel::model()->bySimulation($simId)->byFile($fileId)->byDocument($excelTemplateId)->find();
        if (!$document) return false;
        return $document->id;    
    }
    
    /**
     * Копирование документа
     * 
     * @param string $documentName наименование документа
     * @param int $simId идентификатор симуляции
     * @return int идентификатор скопированного документа
     */
    public static function copy($documentTemplateId, $simId) {
        $connection = Yii::app()->db;
        $transaction = $connection->beginTransaction();
        try
        {
        
            $template = ExcelDocumentTemplate::model()->byId($documentTemplateId)->find();
            if (!$template) throw new Exception("ExcelDocumentService::copy - cant find document template by id $documentTemplateId");
            
            $templateId = $template->id;
            $fileTemplateId = $template->file_id;
            
            // определить идентификатор файла в симуляции
            $file = MyDocumentsModel::model()->bySimulation($simId)->byTemplateId($fileTemplateId)->find();
            if (!$file) throw new Exception("can find file : $fileTemplateId in sim : $simId");

            // создаем документ
            $document = new ExcelDocumentModel();
            $document->document_id = $templateId;
            $document->sim_id = $simId;
            $document->file_id = $file->id;
            $document->insert();
            $documentId = $document->id;

          $transaction->commit();
          return $documentId;
        }
        catch(Exception $e) // в случае ошибки при выполнении запроса выбрасывается исключение
        {
            $transaction->rollBack();
            throw new Exception($e->getMessage());
        }
        
        return false;
    }
    
    /**
     * Получение идентификатора документа по его имени
     * @param string $documentName 
     */
    public static function getIdByName($documentName, $simId) {
        $documentTemplate = ExcelDocumentTemplate::model()->byName($documentName)->find();
        if (!$documentTemplate) return false;
        
        $templateId = $documentTemplate->id;
        
        $document = ExcelDocumentModel::model()->bySimulation($simId)->byDocument($templateId)->find();
        if (!$document) return false;
        
        return $document->id;
    }
    
    public static function getTemplateIdByFileId($fileId) {
        $documentTemplate = ExcelDocumentTemplate::model()->byFile($fileId)->find();
        if (!$documentTemplate) return false;
        return $documentTemplate->id;
    }
    
    /**
     * Получение идентификатора документа по коду файла
     * @param type $code
     * @param type $simId 
     */
    public static function getIdByFileCode($code, $simId) 
    {
        $fileId = self::getFileIdByFileCode($code, $simId);
        
        // получить идентификатор документа в экселе
        $excelDocumentTemplateId = self::getTemplateIdByFileId($fileTemplateId);
   
        // проверим а есть ли такой документ в симуляции
        if (!MyDocumentsService::existsInSim($simId, $fileId)) {
            // скопируем файл в симуляцию
            $fileId = MyDocumentsService::copyToSimulation($simId, $fileTemplateId);
            
            // скопируем экселевский файл
            $docId = ExcelDocumentService::copy($excelDocumentTemplateId, $simId);
            return $docId;
        }
        
        // проверим а есть ли документ в экселевских файлах
        $docId = self::existsInSimulation($simId, $fileId, $excelDocumentTemplateId);
        if (!$docId) {
            return ExcelDocumentService::copy($excelDocumentTemplateId, $simId);
        }
        
        return $docId;
    }
    
    /**
     * Получение идентификатора документа по коду файла
     * @param type $code
     * @param type $simId 
     */
    public static function getFileIdByFileCode($code, $simId) 
    {
        // определить fileId по коду
        $fileTemplateId = MyDocumentsService::getTemplateIdByCode($code);
        if (!$fileTemplateId) return false;
        
        // определить fileId по шаблону
        $fileId = MyDocumentsService::getFileIdByTemplateId($simId, $fileTemplateId);
        
        return $fileId; 
    }
}


