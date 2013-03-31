<?php

class m130331_132026_assesment_group extends CDbMigration
{
	public function up()
	{
        $this->createTable('assessment_group', [
            'id' => 'pk',
            'name'=>'varchar(255) DEFAULT NULL',
            'import_id' => "varchar(14) NOT NULL DEFAULT '00000000000000' COMMENT 'setvice value,used to remove old data after reimport.'"
        ]);

        $this->addColumn('hero_behaviour', 'group_id', 'INT(11) NULL DEFAULT NULL');

        $this->addForeignKey('fk_hero_behaviour_group_id', 'hero_behaviour', 'group_id',
            'assessment_group', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_hero_behaviour_group_id', 'hero_behaviour');
        $this->dropTable('assessment_group');
        $this->dropColumn('hero_behaviour', 'group_id');
	}

}