<?php

class m130424_101523_add_sex_to_character extends CDbMigration
{
	public function up()
	{
        $this->addColumn('characters', 'sex', "VARCHAR(1) DEFAULT NULL");
	}

	public function down()
	{
        $this->dropdColumn('characters', 'sex');
	}
}