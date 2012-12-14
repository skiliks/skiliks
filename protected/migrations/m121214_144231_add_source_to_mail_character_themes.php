<?php

class m121214_144231_add_source_to_mail_character_themes extends CDbMigration
{
	public function up()
	{
        $this->addColumn(
            'mail_character_themes', 
            'source', 
            "VARCHAR(32) DEFAULT NULL COMMENT 'Used to score user behaviour.'");
	}

	public function down()
	{
            $this->dropColumn('mail_character_themes', 'source');
	}
}