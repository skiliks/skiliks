<?php

class m130924_112214_add_refer_invites_to_corporate_account extends CDbMigration
{
	public function up()
	{
        $fields = ["id"=>"pk",
                   "referral_id" => "INT(10) unsigned DEFAULT NULL",
                   "referrer_email" => "VARCHAR(150) DEFAULT NULL",
                   "referrer_id" => "INT(10) unsigned DEFAULT NULL",
                   "invited_at" => "DATETIME DEFAULT NULL",
                   "registered_at" => "DATETIME DEFAULT NULL"
        ];
        $this->createTable("referrals", $fields);
        $this->createIndex("referral_id_index", "referrals", "referral_id");
        $this->createIndex("referrer_id_index", "referrals", "referrer_id");
        $this->addForeignKey("referral_id_fk", "referrals", "referral_id", "user", "id");
        $this->addForeignKey("referrer_id_fk", "referrals", "referrer_id", "user", "id");
        $this->addColumn("user_account_corporate", "referrals_invite_limit", "INT");
	}

	public function down()
	{
        $this->dropTable("referrals");
        $this->dropColumn("user_account_corporate", "referrals_invite_limit");
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}