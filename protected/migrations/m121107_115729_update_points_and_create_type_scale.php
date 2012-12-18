<?php

class m121107_115729_update_points_and_create_type_scale extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('characters_points_titles', 'title', 'TEXT NOT NULL');
        $this->alterColumn('characters_points_titles', 'scale', 'FLOAT(10,2) NULL COMMENT "Scale"');
        $this->addColumn('characters_points_titles', 'type_scale', 'TINYINT NULL');
        $this->createTable('type_scale', array(
            'id' => 'pk',
            'value' => 'string NOT NULL'
        ));
        $this->insert('type_scale', array('value' => 'positive'));
        $this->insert('type_scale', array('value' => 'negative'));
        $this->insert('type_scale', array('value' => 'personal'));
        $this->createTable('log_dialog', array(
            'id' => 'pk',
            'sim_id' => 'INT(11) NOT NULL',
            'point_id' => 'INT(11) NOT NULL',
            'dialog_id' => 'INT(11) NOT NULL'
        ));
        
        $importServide = new ImportGameDataService();
        $importServide->importCharactersPointsTitles();
	}

	public function down()
	{
        $this->dropColumn('characters_points_titles', 'type_scale');
        $this->dropTable('type_scale');
        $this->dropTable('log_dialog');
	}

}