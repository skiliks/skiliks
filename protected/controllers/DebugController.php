<?php

/**
 *
 *
 *
 */
class DebugController extends AjaxController{

    public function actionIndex()
    {
        echo Replica::model()->findByAttributes(['code' => 'ET1.1', 'replica_number'=>2])->id;
    }
}

