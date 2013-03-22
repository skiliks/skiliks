<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 11:33 PM
 * To change this template use File | Settings | File Templates.
 */

class SimulationsController extends AjaxController implements AccountPageControllerInterface
{
    /**
     * @return string
     */
    public function getBaseViewPath()
    {
        return '/static/simulations';
    }

    /**
     *
     */
    public function actionIndex()
    {
        $this->accountPagesBase();
    }

    /**
     *
     */
    public function actionPersonal()
    {
        $this->render('simulations_personal', []);
    }

    /**
     *
     */
    public function actionCorporate()
    {
        $this->render('simulations_personal', []);
    }

    /**
     *
     */
    public function actionDetails($id)
    {
        // this page currently will be just RU
        Yii::app()->language = 'ru';

        $simulation = Simulation::model()->findByPk($id);

        $this->layout = false;

        $this->render('simulation_details', [
            'simulation' => $simulation,
        ]);
    }
}