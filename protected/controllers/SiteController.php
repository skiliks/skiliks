<?php
class SiteController extends AjaxController
{
    /**
     * This is defaut Yii action.
     * It never useded in API or frontend static pages.
     * So, we display error message for user if aout sscript call this action.
     */
    public function actionIndex()
    {
        $this->returnErrorMessage(
            'Controller/action not founded.',
            'Controller/action not founded.'
        );
    }

    /**
     * We handle Yii rroes and savethem to Yii.log. 
     * User see just standard notise
     */
    public function actionError()
    {
        $this->returnErrorMessage(Yii::app()->errorHandler->error);
    }
}


