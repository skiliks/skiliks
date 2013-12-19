<?php

class DebugController extends SiteBaseController
{

    public function actionIndex() {
       $var = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL])->getOutgoingPhoneTheme(['theme_id'=>3]);
       var_dump($var);
    }
}

