<?php

class m130208_170945_add_mail_prefix_refwd extends CDbMigration
{
	public function up()
	{
        $this->insert('mail_prefix', [
            'code' => 'refwd', 'title' => 'Re: Fwd: '
        ]);
        $this->insert('mail_prefix', [
            'code' => 'fwdre', 'title' => 'Fwd: Re: '
        ]);
	}

	public function down()
	{
		$this->delete('mail_prefix', "code = 'refwd'");
        $this->delete('mail_prefix', "code = 'fwdre'");
	}
}