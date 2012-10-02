<?php


/**
 * Сервис по работе с телефоном
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
    
    /**
     * Регистрация исходящих вызовов
     * @param int $simId
     * @param int $characterId 
     */
    public static function registerOutgoing($simId, $characterId) {
        $model = new PhoneCallsModel();
        $model->sim_id      = $simId;
        $model->call_date   = time();
        $model->call_type   = 1;
        $model->from_id     = 1;
        $model->to_id       = $characterId; // какому персонажу мы звоним
        $model->insert();
    }
}

?>
