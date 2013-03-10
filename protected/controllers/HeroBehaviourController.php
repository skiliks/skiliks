<?php
/**
 * Adminka
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class HeroBehaviourController extends AjaxController
{
 
    /**
     * Return data for "Примеры событий" table in adminka
     */
    public function actionDraw()
    {
        $list = array();

        foreach (HeroBehaviour::model()->findAll() as $behaviour) {
            $list[] = array(
                'id'    => $behaviour->id,
                'cell'  => array(
                    $behaviour->id,
                    (NULL !== $behaviour->laerning_goal) ? $behaviour->laerning_goal->title : '--',
                    $behaviour->code,
                    $behaviour->title,
                    $behaviour->scale
                )
            );
        }

        $behaviours = $list;
        
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
    public function actionGetHeroBehaviourHtml() {}
}


