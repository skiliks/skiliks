<?php



/**
 * Клипбоард Экселя
 * 
 * Связана с моделями: ExcelWorksheetModel.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelClipboard extends CActiveRecord{
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'excel_clipboard';
    }
}


