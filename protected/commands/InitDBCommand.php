<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 26.02.13
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 */

class InitDBCommand extends CConsoleCommand {
    private function mysql($command, $database=null){
        $mysql = trim(shell_exec("which mysql"));
        $user = Yii::app()->db->username;
        $password = Yii::app()->db->password;
        $escCommand = str_replace("\"","\\\"", $command);
        $mysqlCmd = "$mysql -u $user -e \"$escCommand\"";
        if ($password) {
            $mysqlCmd .= " -p$password";
        }
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
        $args = array(
            'yiic',
            'createuser',
            '--email=' . $user[0], '--password=' . $user[1], '--isAdmin=' . (isset($user[2]) ? "false" : "true")
        );
        $runner->run($args);
    }
    public function actionIndex($database, $forceDelete = false)
    {
        if ($forceDelete) {
            $this->mysql("DROP DATABASE $database");
        }
        $this->mysql("CREATE DATABASE $database CHARSET utf8 COLLATION utf8_general_ci");
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