<?php

class m130522_120148_update_at extends CDbMigration
{

	public function safeUp()
	{
        $this->addColumn('invites', 'updated_at', 'DATETIME DEFAULT NULL');
        $invites = Invite::model()->findAll();
        foreach($invites as $invite) {
            /* @var $invite Invite */
            if(!empty($invite->sent_time) && $invite->sent_time !== '0' && $invite->sent_time !== null){
                $invite->updated_at = (new DateTime())->setTimestamp((int)$invite->sent_time)->format("Y-m-d H:i:s");
                $invite->save();
            }
        }
	}

	public function safeDown()
	{
        $this->dropColumn('invites', 'updated_at');
	}

}