<?php

class m130829_180447_add_mail_prefix extends CDbMigration
{
	public function up()
	{
        $this->insert('mail_prefix', ['code' => 're:re:fwd:', 'title' => 'Re: Re: Fwd:']);
	}

	public function down()
	{
		echo "no back migrations";
	}
}