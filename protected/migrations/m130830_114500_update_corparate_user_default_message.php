<?php

class m130830_114500_update_corparate_user_default_message extends CDbMigration
{
	public function up()
	{
        $update_params  = array("default_invitation_mail_text" => "Продолжая начатую в 2012 году программу корпоративного развития, предлагаем Вам поучаствовать в прохождении тестовой версии симуляции компании \"Скиликс\".\r\n\r\nЭто компьютерная игра, по результатам которой планируется уточнить, что для Вас является зоной ближайшего развития и на чем нужно сосредоточиться для достижения лучших результатов.\r\n\r\nТакже это поможет скорректировать и уточнить цели и задачи внутрифирменного и внешнего обучения.");
        $this->update('user_account_corporate', $update_params, []);
	}

	public function down()
	{
		echo "m130830_114500_update_corparate_user_default_message does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}