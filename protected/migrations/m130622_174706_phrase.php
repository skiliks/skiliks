<?php

class m130622_174706_phrase extends CDbMigration
{
	public function up()
	{
        $this->addColumn('mail_phrases', 'column_number', 'int(5) not null default 0');
	}

	public function down()
	{
        $this->dropColumn('mail_phrases', 'column_number');
	}

}