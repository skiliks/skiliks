<?php

class m140530_112013_add_project_configs extends CDbMigration
{
	public function up()
	{
        $this->createTable('project_config', [
            'id'                   => 'pk',
            'alias'                => 'VARCHAR(120)',
            'type'                 => 'VARCHAR(30)',
            'value'                => 'VARCHAR(30)',
            'is_use_in_simulation' => 'TINYINT(1)',
            'description'          => 'TEXT',
        ]);

        $this->createTable('site_log_project_config', [
            'id'                => 'pk',
            'user_id'           => 'INT(10) UNSIGNED',
            'project_config_id' => 'INT(11)',
            'created_at'        => 'DATETIME',
            'log'               => 'TEXT',
        ]);

        $this->addForeignKey('site_log_generate_project_config_fk_user',
            'site_log_project_config', 'user_id',
            'user', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('site_log_generate_project_config_fk_project_config',
            'site_log_project_config', 'project_config_id',
            'project_config', 'id', 'CASCADE', 'CASCADE');

        $this->execute("
        INSERT INTO `project_config` (`id`, `alias`, `type`, `value`, `is_use_in_simulation`, `description`) VALUES
            (2, 'chrome_version_to_block', 'String', '', '1', 'Версии хрома, в которых запрещено начинать симуляцию.\r\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\r\nПустое после - во всех версиях хрома можно играть.\r\nПример:21,22,23'),
            (3, 'internet_explorer_version_to_block', 'String', '', '1', 'Версии internet explorer, в которых запрещено начинать симуляцию.\r\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\r\nПустое после - во всех версиях IE можно играть.\r\nПример:21,22,23'),
            (4, 'firefox_version_to_block', 'String', '', '1', 'Версии Firefox, в которых запрещено начинать симуляцию.\r\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\r\nПустое после - во всех версиях FF можно играть.\r\nПример:21,22,23'),
            (5, 'safari_version_to_block', 'String', '', '1', 'Версии сафари, в которых запрещено начинать симуляцию.\nНастройка нужна, чтоб можно было экстренно заблокировать какую-то версию браузера, если в ней обнаружился критический баг.\nПустое после - во всех версиях safari можно играть.\r\nПример:21,22,23');
        ");
	}

	public function down()
	{
		$this->dropForeignKey('site_log_generate_project_config_fk_project_config', 'site_log_project_config');
		$this->dropForeignKey('site_log_generate_project_config_fk_user', 'site_log_project_config');

        $this->dropTable('project_config');
        $this->dropTable('site_log_project_config');
	}
}