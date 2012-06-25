<?php

include_once('protected/controllers/DictionaryController.php');

/**
 * Description of DialogBranchesController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogBranchesController extends DictionaryController{
    
    /**
     * Имя выбираемой таблицы
     * @var string
     */
    protected $_tableName = 'dialog_branches';
    
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
}

?>
