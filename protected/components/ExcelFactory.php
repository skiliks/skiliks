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
}


