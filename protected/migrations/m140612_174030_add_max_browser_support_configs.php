<?php

class m140612_174030_add_max_browser_support_configs extends CDbMigration
{
	public function up()
	{
        $this->execute("
        INSERT INTO `project_config` (`id`, `alias`, `type`, `value`, `is_use_in_simulation`, `description`) VALUES
            (null, 'chrome_max_support', 'String', '35', '1', 'Версии хрома, в которых запрещено начинать симуляцию.\r\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\r\nПустое после - во всех версиях хрома можно играть.\r\nПример:21,22,23'),
            (null, 'msie_max_support', 'String', '11', '1', 'Версии internet explorer, в которых запрещено начинать симуляцию.\r\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\r\nПустое после - во всех версиях IE можно играть.\r\nПример:21,22,23'),
            (null, 'firefox_max_support', 'String', '30', '1', 'Версии Firefox, в которых запрещено начинать симуляцию.\r\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\r\nПустое после - во всех версиях FF можно играть.\r\nПример:21,22,23'),
            (null, 'safari_max_support', 'String', '7', '1', 'Версии сафари, в которых запрещено начинать симуляцию.\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\nПустое после - во всех версиях safari можно играть.\r\nПример:21,22,23');
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