<?php

class m130211_092932_add_prefixes extends CDbMigration
{ 
	public function up()
	{
        $this->insert('mail_prefix', [
            'code' => 'fwdfwd', 'title' => 'Fwd: Fwd: '
        ]);
        $this->insert('mail_prefix', [
            'code' => 'fwdrere', 'title' => 'Fwd: Re: Re:'
        ]);
        $this->insert('mail_prefix', [
            'code' => 'fwdrerere', 'title' => 'Fwd: Re: Re: Re:'
        ]);
        $this->insert('mail_prefix', [
            'code' => 'rererere', 'title' => 'Re:: Re: Re: Re:'
        ]);
	}

	public function down()
	{
        $this->delete('mail_prefix', "code = 'fwdfwd'");
        $this->delete('mail_prefix', "code = 'fwdrere'");
        $this->delete('mail_prefix', "code = 'fwdrerere'");
        $this->delete('mail_prefix', "code = 'rererere'");
	}
}