<?php

class m130603_130951_update_exist_not_active_users_agree_with_terms_row extends CDbMigration
{
	public function up()
	{
        $this->update(
            'user',
            ['agree_with_terms' => YumUser::AGREEMENT_MADE],
            ' activationKey = 1');
	}

	public function down()
	{
		echo "m130603_130951_update_exist_not_active_users_agree_with_terms_row does not support migration down.\n";
	}
}