<?php

/**
 *
 * @author slavka
 */
class m121216_151512_wide_mail_log extends CDbMigration
{
	public function up()
	{
        $this->addColumn(
            'log_mail', 
            'full_coincidence', 
            "VARCHAR(5) DEFAULT '-' COMMENT 'Code of MS mail template, that fully considenced with user email.'");
        $this->addColumn(
            'log_mail', 
            'part1_coincidence', 
            "VARCHAR(5) DEFAULT '-' COMMENT 'Code of MS mail template, that partly (type1) considenced with user email.'");
        $this->addColumn(
            'log_mail', 
            'part2_coincidence', 
            "VARCHAR(5) DEFAULT '-' COMMENT 'Code of MS mail template, that partly (part2) considenced with user email.'");
        $this->addColumn(
            'log_mail', 
            'is_coincidence', 
            "TINYINT(1) DEFAULT 0 COMMENT 'Summarize considerence. Boolean.'");
	}

	public function down()
	{
            $this->dropColumn('log_mail', 'is_coincidence');
            $this->dropColumn('log_mail', 'part2_coincidence');
            $this->dropColumn('log_mail', 'part1_coincidence');
            $this->dropColumn('log_mail', 'full_coincidence');
	}
}

