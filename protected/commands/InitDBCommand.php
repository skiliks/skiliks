<?php
/**
 * Script drop db if --forceDelete=true,
 * than init db --database=skiliks_10 with initial db state SQL
 * than init schema for YUM module
 * than runs all migration scripts
 * than runs import script
 * than init base simulation users
 *
 * php protected/yiic.php initdb --database=skiliks_10 --forceDelete=true  // if db exists
 * php protected/yiic.php initdb --database=skiliks_10 // if db not exists
 *
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 26.02.13
 * Time: 16:35
 * To change this template use File | Settings | File Templates.
 */

class InitDBCommand extends CConsoleCommand
{
    public function actionIndex($database, $forceDelete = false)
    {
        
        if($forceDelete) {
            $this->removeAllDocumentsAndLogs();
        }
        //:TODO Нужны права рута
        echo "\n Drop `$database`.";
        // DROP DATABASE
        if ($forceDelete) {
            $this->mysql("DROP DATABASE $database");
        }

        echo "\n Create `$database`.";
        // CREATE DATABASE
        $this->mysql("CREATE DATABASE $database CHARSET utf8 COLLATE utf8_general_ci");

        echo "\n Run base SQL.";
        $filePath = realpath(__DIR__ . '/../../db.sql');
        $this->mysql_import("$database < ". $filePath);

        //$this->runInstallUserManagement();

        echo "\n Apply migrations to `$database`.";
        // Migrations
        $this->runMigrationTool();

        // Import
        $import = new ImportGameDataService('lite');
        $import->importAll();

        $import = new ImportGameDataService('full');
        $import->importAll();

        $import = new ImportGameDataService('tutorial');
        $import->importAll();

        // init users
        $this->runInitYumUsers();
    }

    private function runInstallUserManagement()
    {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);

        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);

        $args = array('yiic', 'installusermanagement', '');
        $runner->run($args);
    }

    private function runInitYumUsers()
    {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);

        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);

        $args = array('yiic', 'initbaseusers', '');
        $runner->run($args);
    }

    private function mysql($command)
    {
        $user = Yii::app()->db->username;
        $password = Yii::app()->db->password;
        $connectionString = Yii::app()->db->connectionString;
        $host = str_replace('mysql:host=', '', explode(';', $connectionString)[0]);
        $escCommand = str_replace("\"", "\\\"", $command);
        $mysqlCmd = "mysql -h$host -u $user -p$password -e \"$escCommand\"  --default-character-set=UTF8";
        echo $mysqlCmd."\n";
        shell_exec($mysqlCmd);
    }

    private function mysql_import($command)
    {
        $user = Yii::app()->db->username;
        $password = Yii::app()->db->password;
        $connectionString = Yii::app()->db->connectionString;
        $host = str_replace('mysql:host=', '', explode(';', $connectionString)[0]);
        $mysqlCmd = "mysql -h$host -u $user -p$password $command --default-character-set=UTF8";
        echo $mysqlCmd."\n";
        shell_exec($mysqlCmd);
    }

    private function runMigrationTool()
    {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);

        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);

        $args = array('yiic', 'migrate', '--interactive=0');
        $runner->run($args);
    }

    private function runCreateAdmin($user)
    {
        $commandPath = Yii::app()->getBasePath() . DIRECTORY_SEPARATOR . 'commands';
        $runner = new CConsoleCommandRunner();
        $runner->addCommands($commandPath);
        $commandPath = Yii::getFrameworkPath() . DIRECTORY_SEPARATOR . 'cli' . DIRECTORY_SEPARATOR . 'commands';
        $runner->addCommands($commandPath);
        $args = array(
            'yiic',
            'createuser',
            '--email=' . $user['username'], '--password=' . $user['password'], '--isAdmin=' . ((isset($user['is_admin']) && 1 == $user['is_admin']) ? "true" : "false")
        );
        $runner->run($args);
    }

    private function removeAllDocumentsAndLogs(){
        $project_path = __DIR__."/../../";
        $path = "rm -rf ".$project_path."documents/user/*";
        echo "Start ".$path."\r\n";
        exec($path);
        echo "End\r\n";
        $path = "rm -rf ".$project_path."protected/logs/*";
        echo "Start ".$path."\r\n";
        exec($path);
        echo "End\r\n";
        $path = "rm -rf ".$project_path."protected/system_data/analytic_files_2/*";
        echo "Start ".$path."\r\n";
        exec($path);
        echo "End\r\n";
        $path = "rm -rf ".$project_path."protected/system_data/prb_bank/pdf_slices/*";
        echo "Start ".$path."\r\n";
        exec($path);
        echo "End\r\n";
        echo "Delete All docs and logs \r\n";
    }

}