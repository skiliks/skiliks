<?php

class m140612_174030_add_max_browser_support_configs extends CDbMigration
{
	public function up()
	{
        $this->execute("
        INSERT INTO `project_config` (`id`, `alias`, `type`, `value`, `is_use_in_simulation`, `description`) VALUES
            (null, 'chrome_max_support', 'String', '35', '1', 'Максимальная версия хрома, в которой разрешено начинать симуляцию.\r\nПример: 33'),
            (null, 'msie_max_support', 'String', '11', '1', 'Максимальная версия IE, в которой разрешено начинать симуляцию.\r\nПример: 11'),
            (null, 'firefox_max_support', 'String', '30', '1', 'Максимальная версия FF, в которой разрешено начинать симуляцию.\r\nПример: 30'),
            (null, 'safari_max_support', 'String', '7', '1', 'Максимальная версия сафари, в которой разрешено начинать симуляцию.\r\nПример: 7');
        ");
	}

	public function down()
	{
        $this->execute("
          DELETE FROM `project_config` WHERE `alias` =  'chrome_max_support';
        ");

        $this->execute("
          DELETE FROM `project_config` WHERE `alias` =  'msie_max_support';
        ");

        $this->execute("
          DELETE FROM `project_config` WHERE `alias` =  'firefox_max_support';
        ");

        $this->execute("
          DELETE FROM `project_config` WHERE `alias` =  'safari_max_support';
        ");
	}
}