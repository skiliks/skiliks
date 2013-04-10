<?php

class m130410_153233_add_time_management_aggregared_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('time_management_aggregated', [
            'id'         => 'pk',
            'sim_id'     => 'int NOT NULL',
            'slug'       => 'VARCHAR(60) NOT NULL',
            'value'      => 'DECIMAL(6,2) NOT NULL',
            'unit_label' => 'VARCHAR(60) NOT NULL',
        ]);

        $this->addColumn('dialog_subtypes', 'slug', 'VARCHAR(60) NOT NULL');

        $this->update('dialog_subtypes', ['slug' => DialogSubtype::SLUG_CALL]       , " title='Звонок' ");
        $this->update('dialog_subtypes', ['slug' => DialogSubtype::SLUG_PHONE_TALK] , " title='Разговор по телефону' ");
        $this->update('dialog_subtypes', ['slug' => DialogSubtype::SLUG_VISIT]      , " title='Визит' ");
        $this->update('dialog_subtypes', ['slug' => DialogSubtype::SLUG_KNOCK_KNOCK], " title='Стук в дверь' ");
        $this->update('dialog_subtypes', ['slug' => DialogSubtype::SLUG_MEETING]    , " title='Встреча' ");
	}

	public function down()
	{
		$this->dropTable('time_management_aggregated');

        $this->dropColumn('dialog_subtypes', 'slug');
	}
}