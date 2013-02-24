<?php

/**
 * Description of DebugController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DebugController extends AjaxController{

    public function actionIndex(){

        echo Dialog::model()->findByAttributes(['code' => 'ET1.1', 'replica_number'=>2])->id;
    }

}


