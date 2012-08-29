<?php



/**
 * Description of ViewerTemplateModel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ViewerTemplateModel extends CActiveRecord{

    /**
     *
     * @param type $className
     * @return ViewerTemplateModel
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'viewer_template';
    }
    
    
    public function byFile($fileId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "file_id = {$fileId}"
        ));
        return $this;
    }
}

?>
