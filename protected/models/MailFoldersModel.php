<?php



/**
 * Модель папок почтовика
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailFoldersModel extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $name;
    
    const INBOX_ID  = 1;
    const SENDED_ID = 3;
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     * @depracated: so far mail/getFolders is deprecated too
     * @return mixed array
     */
    /*public static function getFoldersListForJson()
    {
        $folders = self::model()->findAll(array('limit' => 4));
        
        $list = array();
        
        foreach($folders as $folder) {
            $list[(int)$folder->id] = array(
                'id'       => (int)$folder->id,
                'folderId' => (int)$folder->id, // Fuck, some JS code need it.
                'name'     => $folder->name,
                'unreaded' => 0
            );
        }
        
        return $list;   
    }*/


    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'mail_group';
    }

}


