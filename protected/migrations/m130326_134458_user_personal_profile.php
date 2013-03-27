<?php

class m130326_134458_user_personal_profile extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_personal', 'birthday', 'date');
        $this->addColumn('user_account_personal', 'location', 'VARCHAR(255)');

        $this->delete('professional_statuses');

        // Cannot truncate a table referenced in a foreign key constraint
        // (`skiliks_11`.`user_account_personal`, CONSTRAINT `fk_user_account_personal_professional_status_id`
        // FOREIGN KEY (`professional_status_id`) REFERENCES `skiliks_11`.`professional_statuses` (`id`)).

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