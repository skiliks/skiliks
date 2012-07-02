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
        'id', 'title'
    );
    
    
    
    /**
     * Обработчик изменения записи.
     * @return int
     */
    protected function _editHandler() {
        $id = (int)Yii::app()->request->getParam('id', false);
        $title = Yii::app()->request->getParam('title', false);

        $model = CharactersPointsTitles::model()->findByPk($id);
        $model->title = $title;
        $model->save();
        return 1;
    }
    
    /**
     * Обработчик на добавление записи.
     * @return int
     */
    protected function _addHandler() {
        $title = Yii::app()->request->getParam('title', false);

        $model = new CharactersPointsTitles();
        $model->title = $title;
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
                'characters_points_titles' => $this->_getComboboxData('characters_points_titles', 'title', ' WHERE parent_id is null ')
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
    
}

?>
