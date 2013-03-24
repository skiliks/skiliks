<?php

class m130324_223136_add_vacancies extends CDbMigration
{
	public function up()
	{
        $this->createTable('professional_occupation', [
            'id'    => 'pk',
            'label' => 'VARCHAR(120) NOT NULL',
        ]);

        $this->createTable('professional_specialization', [
            'id'    => 'pk',
            'label' => 'VARCHAR(120) NOT NULL',
        ]);

        $this->createTable('vacancy', [
            'id'                             => 'pk',
            'professional_occupation_id'     => 'INT',
            'professional_specialization_id' => 'INT',
            'label'                          => 'VARCHAR(120) NOT NULL',
            'link'                           => 'TEXT',
            'import_id'                      => 'VARCHAR(60)',
        ]);

        $this->addForeignKey(
            'vacancy_fk_professional_occupation',
            'vacancy',
            'professional_occupation_id',
            'professional_occupation',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'vacancy_fk_professional_specialization',
            'vacancy',
            'professional_specialization_id',
            'professional_specialization',
            'id',
            'SET NULL',
            'CASCADE'
        );
	}

	public function down()
	{
		echo "m130324_223136_add_vacancies does not support migration down.\n";
	}
}