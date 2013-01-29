<?php



/**
 * Фабрика для работы с Excel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
final class ExcelFactory {
    
    protected static $_doc = null;
    
    /**
     * New code!
     * @return ExcelDocument
     */
    public static function getDocument($documentId = false) {
        if (is_null(self::$_doc)) {
            self::$_doc = new ExcelDocument($documentId);
        }
        
        return self::$_doc;
    }
    
    /**
     * New code!
     * @return ExcelDocument
     */
    public static function getDocumentPath($simId, $documentId, $templateFileName = null)
    {
        $pathToUserFile = sprintf(
            'documents/%s/%s.xls',
            $simId,
            $documentId
        );        
        
        // use ZohoDocument __constructor() to create file, if it not exist
        new ZohoDocuments($simId, $documentId, $templateFileName); // FileName is not nessesary
        
        return $pathToUserFile;
    }
}


