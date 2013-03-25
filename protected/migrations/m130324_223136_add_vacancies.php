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
            'id'                         => 'pk',
            'professional_occupation_id' => 'INT',
            'label'                      => 'VARCHAR(120) NOT NULL',
        ]);

        $this->addForeignKey(
            'professional_specialization_fk_professional_occupation',
            'professional_specialization',
            'professional_occupation_id',
            'professional_occupation',
            'id',
            'SET NULL',
            'CASCADE'
        );

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
        //$this->dropForeignKey('vacancy_fk_professional_occupation', 'vacancy');

        $this->dropTable('vacancy');
		$this->dropTable('professional_occupation');
		$this->dropTable('professional_specialization');
	}
}