<?php

class m130401_111522_add_sim_type_to_invite extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'simulation_type', 'VARCHAR(20) DEFAULT '.Simulation::TYPE_FULL);
        $this->update('invites', ['simulation_type' => 1]);

        $action = YumAction::model()->findByAttributes(['title' => UserService::CAN_START_FULL_SIMULATION]);

        foreach (UserAccountPersonal::model()->findAll() as $account) {
            $this->insert('permission', [
                'principal_id'   => $account->user->id,
                'subordinate_id' => $account->user->id,
                'type'           => 'user',
                'action'         => $action->id,
                'template'       => 1,
                'comment'        => null,
            ]);
        }
	}

	public function down()
	{
        $this->dropColumn('invites', 'simulation_type');
	}
}