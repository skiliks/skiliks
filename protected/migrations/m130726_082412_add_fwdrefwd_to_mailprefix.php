<?php

class m130726_082412_add_fwdrefwd_to_mailprefix extends CDbMigration
{
	public function up()
	{
        $this->insert('mail_prefix', [
            'code'  => 'fwdrefwd',
            'title' => 'Fwd: Re: Fwd:',
         ]);
	}

	public function down()
	{
		echo "m130726_082412_add_fwdrefwd_to_mailprefix does not support migration down.\n";
	}
}