<?php

class m130411_080035_add_csenario_duration extends CDbMigration
{
	public function up()
	{
        $this->addColumn('scenario', 'duration_in_game_min', 'INT');

        $this->update('scenario', [
            'duration_in_game_min' => 80
        ],
        " slug = 'lite' ");

        $this->update('scenario', [
            'duration_in_game_min' => 495
        ],
        " slug = 'full' ");
	}

	public function down()
	{
		$this->dropColumn('scenario', 'duration_in_game_min');
	}
}