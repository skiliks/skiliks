<?php

class m140223_234658_update extends CDbMigration
{
	public function up()
	{
        $this->renameColumn('site_log_authorization', 'referral_url', 'referer_url');
	}

	public function down()
	{
		echo "m140223_234658_update does not support migration down.\n";
		return false;
	}

}