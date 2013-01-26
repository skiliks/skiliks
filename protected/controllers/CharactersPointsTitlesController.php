<?php
/**
 * Adminka
 * Description of CharactersPointsTitles
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitlesController extends AjaxController
{
 
    /**
     * Return data for "Примеры событий" table in adminka
     */
    public function actionDraw()
    {
        $behaviours = System::getBehavioursListForAdminka();
        
        $this->sendJSON(array(
            'result'  => 1,
            'rows'    => $behaviours,
            'records' => count($behaviours),
        ));
    } 
    
    /**
     * Отдает информацию по всем комбикам
     */
    public function actionGetSelect() 
    {
        $this->sendJSON(array(
            'result'=>1,
            'data' => array()
        ));
    }
    
    /**
     * We need it just because, currently adminka steel send request
     */
    public function actionGetCharactersPointsTitlesHtml() {}
}


