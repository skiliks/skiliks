<?php

class m130222_083315_fix_log_windows_data extends CDbMigration
{
	public function up()
	{
        $this->update('log_windows', ['window' => 1], 'sub_window = :w', ['w' => 1]);
        $this->update('log_windows', ['window' => 3], 'sub_window = :w', ['w' => 3]);

        $this->update('log_windows', ['window' => 11], 'sub_window = :w', ['w' => 11]);
        $this->update('log_windows', ['window' => 12], 'sub_window = :w', ['w' => 12]);
        $this->update('log_windows', ['window' => 13], 'sub_window = :w', ['w' => 13]);
        $this->update('log_windows', ['window' => 14], 'sub_window = :w', ['w' => 14]);

        $this->update('log_windows', ['window' => 21], 'sub_window = :w', ['w' => 21]);
        $this->update('log_windows', ['window' => 22], 'sub_window = :w', ['w' => 22]);
        $this->update('log_windows', ['window' => 23], 'sub_window = :w', ['w' => 23]);
        $this->update('log_windows', ['window' => 24], 'sub_window = :w', ['w' => 24]);

        $this->update('log_windows', ['window' => 31], 'sub_window = :w', ['w' => 31]);
        $this->update('log_windows', ['window' => 32], 'sub_window = :w', ['w' => 32]);

        $this->update('log_windows', ['window' => 41], 'sub_window = :w', ['w' => 41]);
        $this->update('log_windows', ['window' => 42], 'sub_window = :w', ['w' => 42]);

        $this->dropColumn('log_windows', 'sub_window');
	}

	public function down()
	{
		echo "m130222_083315_fix_log_windows_data does not support migration down.\n";
	}

}