<?php

class m130120_215027_todo extends CDbMigration
{
	public function up()
	{
        $this->delete('todo');
        $this->alterColumn('todo', 'adding_date', 'datetime DEFAULT NULL COMMENT \'Дата добавления задачи\'');
	}

	public function down()
	{
        $this->alterColumn('todo', 'adding_date', 'int(11) DEFAULT NULL COMMENT \'Дата добавления задачи\'');
	}
}