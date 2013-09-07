<?php

class m130830_102243_set_user_default_message extends CDbMigration
{
	public function up()
	{
        $this->addColumn('user_account_corporate', 'default_invitation_mail_text', 'TEXT');
        $update_columns = array("user_account_corporate");
        $update_params  = array("default_invitation_mail_text" => "Продолжая начатую в 2012 году программу корпоративного развития, предлагаем Вам поучаствовать в прохождении тестовой версии симуляции компании \"Скиликс\".\n\n Это компьютерная игра, по результатам которой планируется уточнить, что для Вас является зоной ближайшего развития и на чем нужно сосредоточиться для достижения лучших результатов.\n\n Также это поможет скорректировать и уточнить цели и задачи внутрифирменного и внешнего обучения.");
        $this->update('user_account_corporate', $update_params, []);
	}

	public function down()
	{
        $this->dropColumn("user_account_corporate", "default_invitation_mail_text");
	}

}