<?php
/**
 * Created by Vladimir Boyko Skilix.
 */

/**
 *
 * Выбирает полный лог всех юзеров из базы
 */
class GenerateReport2ForAllCorporateUsersCommand extends CConsoleCommand
{
    public function actionIndex($assessment_version) // 7 days
    {

        $users = YumUser::model()->findAll();
        foreach($users as $user) {
            /* @var $user YumUser */
            if($user->isCorporate()) {
                echo 'Username '.$user->username."\r\n";
                SimulationService::saveLogsAsExcelReport2ForCorporateUser($user->account_corporate, $assessment_version);
            }
        }
        echo "Done \r\n";
    }
}