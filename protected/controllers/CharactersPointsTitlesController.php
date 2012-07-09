<?php



/**
 * Description of CharactersPointsTitles
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitlesController extends DictionaryController{
    
    /**
     * Имя выбираемой таблицы
     * @var string
     */
    protected $_tableName = 'characters_points_titles';
    
    /**
     * Поля, по которым можно осуществлять фильтрацию
     * @var array
     */
    protected $_searchParams = array(
        'id', 'title', 'code', 'parent_id', 'scale'
    );
    
    
    
    /**
     * Обработчик изменения записи.
     * @return int
     */
    protected function _editHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);
        $title = Yii::app()->request->getParam('title', false);
        $code = Yii::app()->request->getParam('code', false);
        $parentId = (int)Yii::app()->request->getParam('parent_id', false);
        $scale = (int)Yii::app()->request->getParam('scale', false);
        
        if ($parentId == -1) $parentId = null;
        
        $model = CharactersPointsTitles::model()->findByPk($id);
        $model->title = $title;
        $model->code = $code;
        $model->parent_id = $parentId;
        $model->scale = $scale;
        $model->save();
        return 1;
    }
    
    /**
     * Обработчик на добавление записи.
     * @return int
     */
    protected function _addHandler() {
        $title = Yii::app()->request->getParam('title', false);
        $code = Yii::app()->request->getParam('code', false);
        $parentId = (int)Yii::app()->request->getParam('parent_id', false);
        $scale = (int)Yii::app()->request->getParam('scale', false);
        if ($parentId == -1) $parentId = null;

        $model = new CharactersPointsTitles();
        $model->title = $title;
        $model->code = $code;
        $model->parent_id = $parentId;
        $model->scale = $scale;
        $model->insert();
        return 1;
    }
    
    /**
     * Обработчик удаление записи
     * @return int
     */
    protected function _delHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);

        $model = CharactersPointsTitles::model()->findByPk($id);
        $model->delete();
        return 1;
    }
    
    /**
     * Отдает информацию по всем комбикам
     */
    public function actionGetSelect() {
        $result = array(
            'result'=>1,
            'data'=>array(
                'characters_points_titles' => $this->_getComboboxData('characters_points_titles', 'title', ' WHERE parent_id is null ', 'Все')
            )
        );
        
        $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionGetCharactersPointsTitlesHtml() {
        $this->_sendResponse(200, $this->_getComboboxHtml(
                'characters_points_titles',
                'title', ' WHERE parent_id is null '
            ), 'text/html');
    }
    
    protected function _prepareSql() {
        return "select 
                    cpt.id, 
                    cptp.title   as parent_id, 
                    cpt.code, 
                    cpt.title, 
                    cpt.scale
                from {$this->_tableName} as cpt
                left join {$this->_tableName} as cptp on (cptp.id = cpt.parent_id) ";
    }
}

?>
