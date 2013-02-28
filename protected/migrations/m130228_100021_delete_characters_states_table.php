<?php

class m130228_100021_delete_characters_states_table extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_dialogs_ch_from_state', 'replica');
        $this->dropForeignKey('fk_dialogs_ch_to_state', 'replica');
        $this->dropTable('characters_states');
	}

	public function down()
	{
		echo "m130228_100021_delete_characters_states_table does not support migration down.\n";
	}
}