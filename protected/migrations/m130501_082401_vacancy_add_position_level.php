<?php

class m130501_082401_vacancy_add_position_level extends CDbMigration
{
	public function up()
	{
        $this->createTable('position_level', [
            'slug'  => 'VARCHAR(50) NOT NULL PRIMARY KEY',
            'label' => 'VARCHAR(120) NOT NULL',
        ]);

        $this->insert('position_level', ['slug' => 'manager'   , 'label' => 'Руководитель']);
        $this->insert('position_level', ['slug' => 'specialist', 'label' => 'Специалист']);

        $this->addColumn('vacancy', 'position_level_slug', 'VARCHAR(50) NOT NULL');

        $this->update('vacancy', ['position_level_slug' => 'manager']);

        $this->addForeignKey(
            'vacancy_FK_position_level',
            'vacancy',
            'position_level_slug',
            'position_level',
            'slug',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropForeignKey('vacancy_FK_position_level', 'vacancy');
        $this->dropTable('position_level');
        $this->dropColumn('vacancy', 'position_level_slug');
	}
}