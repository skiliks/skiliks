<?php
/**
 * Adminka
 * Контроллер диалогов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogsController extends AjaxController
{
    /**
     * Return data for "Примеры событий" table in adminka
     */
    public function actionDraw()
    {
        $dialogs = DialogService::getDialogsListForAdminka();
        
        $this->sendJSON(array(
            'result'  => 1,
            'rows'    => $dialogs,
            'records' => count($dialogs),
        ));
    } 
    
    /**
     * Отдает информацию по всем комбикам
     */
    public function actionGetSelect() 
    {
        $this->sendJSON(array(
            'result'=>1,
            'data'=>array()
        ));
    }
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetDialogTypesHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetDialogSubtypesHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetCharactersHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetCharactersStatesHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetEventsResultsHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetEventsSamplesHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetEventsCodesHtml() {}
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetNextEventHtml() {}
}


