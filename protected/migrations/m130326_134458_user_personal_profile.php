<?php

class m130326_134458_user_personal_profile extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_personal', 'birthday', 'date');
        $this->addColumn('user_account_personal', 'location', 'VARCHAR(255)');

        $this->truncateTable('professional_statuses');

        $this->insert('professional_statuses', ['label' => 'Собственник']);
        $this->insert('professional_statuses', ['label' => 'Высшее руководство']);
        $this->insert('professional_statuses', ['label' => 'Линейный менеджер']);
        $this->insert('professional_statuses', ['label' => 'Проектный менеджер']);
        $this->insert('professional_statuses', ['label' => 'Основной персонал, специалист']);
        $this->insert('professional_statuses', ['label' => 'Вспомогательный персонал']);
        $this->insert('professional_statuses', ['label' => 'Индивидуальная деятельность']);
        $this->insert('professional_statuses', ['label' => 'Студент']);
	}

	public function down()
	{
        $this->dropColumn('user_account_personal', 'birthday');
        $this->dropColumn('user_account_personal', 'location');
	}
}