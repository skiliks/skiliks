<?php



/**
 * Фабрика для работы с Excel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
final class ExcelFactory {
    
    protected static $_doc = null;
    
    /**
     *
     * @return ExcelDocument
     */
    public static function getDocument($documentId = false) {
        //Logger::debug("getDocument : ".var_export(self::$_doc, true));
        if (is_null(self::$_doc)) {
            self::$_doc = new ExcelDocument($documentId);
        }
        
        return self::$_doc;
    }
    
    /**
     *
     * @return ExcelDocument
     */
    public static function getDocumentPath($simId, $documentId, $templateFileName = null)
    {
        $pathToUserFile = sprintf(
            'documents/%s/%s.xls',
            $simId,
            $documentId
        );        
        
        // use ZohoDocument to create file, if it not exist
        new ZohoDocuments($simId, $documentId, $templateFileName); // FileName is not nessesary
        
        return $pathToUserFile;
    }
}


