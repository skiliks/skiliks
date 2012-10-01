<?php


/**
 * Description of PhoneService
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneService {
    
    /**
     * Получить список тем для телефона.
     * @param int $id
     * @return array
     */
    public static function getThemes($id) {
        $themes = MailCharacterThemesModel::model()->byCharacter($id)->byPhone()->findAll();
        $themeIds = array();
        foreach($themes as $theme) {
            $themeIds[] = $theme->theme_id;
        }
        
        if (count($themeIds) == 0) return array();
        
        $themes = MailThemesModel::model()->byIds($themeIds)->findAll();
        $list = array();
        foreach($themes as $theme) {
            $list[$theme->id] = $theme->name;
        }
        
        return $list;
    }
}

?>
