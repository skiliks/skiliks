<?php

class m140904_191214_fix_action_4_2 extends CDbMigration
{
	public function up()
	{
//        $this->update(
//            'action',
//            ['title' => 'corp_users_list_export', 'subject' => 'Право экспорта списка корпоративных пользователей'],
//            " order_no = '4.2' "
//        );
//        $this->update(
//            'action',
//            ['title' => 'system_make_re_import'],
//            " title = 'system_ake_re_import' "
//        );

//        $this->insert('action', [
//            'subject'  => 'Право сменить роль пользователя',
//            'comment'  => '',
//            'title'    => 'user_change_role',
//            'group'    => 'Пользователи',
//            'order_no' => '4.17',
//        ]);
//
//        $this->insert('action', [
//            'subject'  => 'Право редактировать список позиций',
//            'comment'  => '',
//            'title'    => 'user_edit_vacations',
//            'group'    => 'Пользователи',
//            'order_no' => '4.18',
//        ]);

//        $this->insert('action', [
//            'subject'  => 'Право просмотра лога действий над симуляцией по любой симуляции',
//            'comment'  => '',
//            'title'    => 'sim_site_logs_view',
//            'group'    => 'Симуляции',
//            'order_no' => '6.5',
//        ]);

//        $this->update(
//            'action',
//            ['title' => 'simulations_list_view'],
//            " order_no = '6.1' "
//        );
//
//        $this->update(
//            'action',
//            ['title' => 'simulation_details_view'],
//            " order_no = '6.2' "
//        );

        $this->delete('action', " order_no = '6.2' ");

        $this->update(
            'action',
            ['order_no' => '6.2' ],
            " order_no = '6.5' "
        );
	}

	public function down()
	{
		echo "m140904_191214_fix_action_4_2 does not support migration down.\n";
	}
}