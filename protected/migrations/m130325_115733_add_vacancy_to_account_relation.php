<?php

class m130325_115733_add_vacancy_to_account_relation extends CDbMigration
{
	public function up()
	{
        $this->addColumn('vacancy', 'user_id', 'INT UNSIGNED');

        $this->addForeignKey(
            'vacancy_fk_user',
            'vacancy',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropColumn('vacancy', 'user_id');
	}

}