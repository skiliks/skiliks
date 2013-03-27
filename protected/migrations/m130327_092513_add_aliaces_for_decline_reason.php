<?php

class m130327_092513_add_aliaces_for_decline_reason extends CDbMigration
{
	public function up()
	{
        $this->addColumn('decline_reason','alias',' VARCHAR (120)');

        $this->update('decline_reason', ['alias' => 'dont_want_to_register'], " label = 'Не хочу регистрироваться' ");
        $this->update('decline_reason', ['alias' => 'nor_interest_vacancy'], " label = 'Не интересует вакансия' ");
        $this->update('decline_reason', ['alias' => 'dont_want_pass_test'], " label = 'Не хочу проходить тест' ");
        $this->update('decline_reason', ['alias' => 'other'], " label = 'Другое' ");
	}

	public function down()
	{
         $this->dropColumn('decline_reason','alias');
	}
}