<?php

class m140612_124739_add_timeToCheckSimStart_config extends CDbMigration
{
	public function up()
	{
        $this->execute("
        INSERT INTO `project_config` (`id`, `alias`, `type`, `value`, `is_use_in_simulation`, `description`) VALUES
            (null, 'time_to_check_sim_start', 'String', '595', NULL, 'Время в игровых минутах (00:00 = 0, 01:00 = 60) в которое быдет выполнятся проверка, что и документы, и список адресатов, и задачи успешно подгрузились при симстарте.\r\n 09:55 = 595, эта цифра и должна стоять как значение конфига. \r\nПример:595');
        ");
	}

	public function down()
	{
        $this->execute("
          DELETE FROM `project_config` WHERE `alias` =  'time_to_check_sim_start';
        ");
	}
}