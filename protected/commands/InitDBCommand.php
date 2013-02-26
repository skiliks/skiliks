<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 26.02.13
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 */

class InitDBCommand extends CConsoleCommand {
    private $user = "skiliks";
    private $password = "skiliks123";

    private function mysql($command, $database=null){
        $mysql = trim(shell_exec("which mysql"));
        $user = $this->user;
        $password = $this->password;
        $escCommand = str_replace("\"","\\\"", $command);
        $mysqlCmd = "$mysql -u $user -p$password -e \"$escCommand\"";
        if ($database !== null) {
            $mysqlCmd .= " -D$database";
        }
        shell_exec($mysqlCmd);
    }
    private function runMigrationTool() {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);
        $args = array('yiic', 'migrate', '--interactive=0');
        $runner->run($args);
    }
    private function runCreateAdmin($user) {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);
        $args = array('yiic', 'createadmin', '--email=' . $user[0], '--password=' . $user[1]);
        $runner->run($args);
    }
    public function actionIndex($database, $forceDelete = false)
    {
        if ($forceDelete) {
            $this->mysql("DROP DATABASE $database");
        }
        $this->mysql("CREATE DATABASE $database");
        $this->mysql("source db.sql", $database);
        $this->runMigrationTool();
        $import = new ImportGameDataService();
        $import->importAll();
        $users = Yii::app()->params['initial_data']['users'];
        foreach ($users as $users) {
            $this->runCreateAdmin($users);
        }
    }
}