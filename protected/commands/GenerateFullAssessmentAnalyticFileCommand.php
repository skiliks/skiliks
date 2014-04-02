<?php
/**
 * Генерирует сводный аналитический файл по всем завершенным симуляция
 */
class GenerateFullAssessmentAnalyticFileCommand extends CConsoleCommand
{
    public function actionIndex($email=null)
    {
        if($email !== null){
            $profile = YumProfile::model()->findByAttributes(['email'=>$email]);
            $users_account = UserAccountCorporate::model()->findAllByAttributes(['user_id'=>$profile->user_id]);
        } else {
            $project_path = __DIR__."/../../";
            $path = "rm -rf ".$project_path."protected/system_data/analytic_files_2/*";
            echo "Start ".$path."\r\n";
            exec($path);
            echo "End\r\n";
            $users_account = UserAccountCorporate::model()->findAll();
        }
        /* @var UserAccountCorporate[] $users_account */
        $i = 10;
        echo "Found ".count($users_account)." accounts \n";
        foreach($users_account as $account) {
            //if($account->user->profile->email !== 'arkadiy.kulik@insead.edu') continue;

            $account->cache_full_report = null;
            $account->save(false);
            if(UserService::generateFullAssessmentAnalyticFile($account->user) === false ){
                echo "Account ".$account->user->profile->email." empty \n";
            } else {
                echo "Account ".$account->user->profile->email." complete \n";
            }
            unset ($account);
            //echo "Account ".$account->user->profile->email." complete \n";
        }
    }
}