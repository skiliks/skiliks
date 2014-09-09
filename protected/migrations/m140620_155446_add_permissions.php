<?php

class m140620_155446_add_permissions extends CDbMigration
{
	public function up()
    {
        $this->truncateTable('action');

        $this->addColumn('action', 'group', 'varchar(60)');
        $this->addColumn('action', 'order_no', 'varchar(5)');

        $this->insert('action', [
            'comment'  => 'Из админки можно запустить игру в специальном интерфейсе, который упрощает тестирование.',
            'subject'  => 'Право запуска DEV симуляции',
            'title'    => 'start_dev_mode',
            'group'    => 'общее',
            'order_no' => '1.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право генерировать и скачивать сводный аналитический файл по всем аккаунтам',
            'comment'  => '',
            'title'    => 'consolidated_analytic_file_generate_download',
            'group'    => 'Управление',
            'order_no' => '8.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра всего списка приглашений',
            'comment'  => '',
            'title'    => 'invites_list_view',
            'group'    => 'Приглашения',
            'order_no' => '5.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра подробной информации о любом Приглашении',
            'comment'  => '',
            'title'    => 'invites_details_view',
            'group'    => 'Приглашения',
            'order_no' => '5.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право разрешать/запрещать стартовать любую симуляция по уже завершенному приглашению заново',
            'comment'  => '',
            'title'    => 'invites_allow_restart_finished_simulation',
            'group'    => 'Приглашения',
            'order_no' => '5.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право менять статус любому приглашению',
            'comment'  => '',
            'title'    => 'invite_status_change',
            'group'    => '',
            'order_no' => '5.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право смотреть попап оценки любого приглашения',
            'comment'  => '',
            'title'    => 'sim_results_popup_view',
            'group'    => '',
            'order_no' => '5.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Право смотреть ексель логи и сводный бюджет любого приглашения',
            'comment'  => '',
            'title'    => 'sim_logs_and_d1_view',
            'group'    => '',
            'order_no' => '5.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право сменить текущую пройденную симуляцию у любого приглашения',
            'comment'  => '',
            'title'    => 'invite_sim_change',
            'group'    => '',
            'order_no' => '5.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право откатить любое приглашение',
            'comment'  => '',
            'title'    => 'invite_roll_back',
            'group'    => '',
            'order_no' => '5.8',
        ]);

        $this->insert('action', [
            'subject'  => 'Право пересчитать любое приглашение',
            'comment'  => '',
            'title'    => 'invite_recalculate',
            'group'    => '',
            'order_no' => '5.9',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра всего списка симуляций',
            'comment'  => '',
            'title'    => 'invite_list_view',
            'group'    => 'Симуляции',
            'order_no' => '6.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право смотреть подробную информацию по любой симуляции',
            'comment'  => '',
            'title'    => 'invite_details_view',
            'group'    => 'Симуляции',
            'order_no' => '6.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра лога запросов к серверу по любой симуляции',
            'comment'  => '',
            'title'    => 'sim_server_requests_list_view',
            'group'    => 'Симуляции',
            'order_no' => '6.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право включать / выключать аварийную панель',
            'comment'  => '',
            'title'    => 'sim_on_off_emergency_panel',
            'group'    => 'Симуляции',
            'order_no' => '6.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра рейтинга симуляций',
            'comment'  => '',
            'title'    => 'sim_rating_view',
            'group'    => 'Статистика',
            'order_no' => '7.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать список подписавшихся на рассылку',
            'comment'  => '',
            'title'    => 'subscribers_list_view',
            'group'    => 'Поддержка',
            'order_no' => '3.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать и редактировать отзывы',
            'comment'  => '',
            'title'    => 'feedback_view_edit',
            'group'    => 'Поддержка',
            'order_no' => '3.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать логи авторизации',
            'comment'  => '',
            'title'    => 'auth_logs_view',
            'group'    => 'Поддержка',
            'order_no' => '3.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать список супер-админов',
            'comment'  => '',
            'title'    => 'admins_list_view',
            'group'    => 'Управление',
            'order_no' => '8.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотривать список забаненных пользователей',
            'comment'  => '',
            'title'    => 'banned_users_list_view',
            'group'    => 'Поддержка',
            'order_no' => '3.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать список текущих симуляций',
            'comment'  => '',
            'title'    => 'online_sim_list_view',
            'group'    => 'Поддержка',
            'order_no' => '3.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра списка корпоративных пользователей',
            'comment'  => '',
            'title'    => 'corp_users_list_view',
            'group'    => 'Пользователи',
            'order_no' => '4.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право импорта списка корпоративных пользователей',
            'comment'  => '',
            'title'    => 'corp_users_list_import',
            'group'    => 'Пользователи',
            'order_no' => '4.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра списка всех пользователей*',
            'comment'  => '',
            'title'    => 'all_users_list_view',
            'group'    => 'Пользователи',
            'order_no' => '4.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право посмотра детальной информации о любом пользователе*',
            'comment'  => '',
            'title'    => 'user_details_view',
            'group'    => 'Пользователи',
            'order_no' => '4.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право сменить пароль любому пользователю',
            'comment'  => '',
            'title'    => 'user_change_password',
            'group'    => 'Пользователи',
            'order_no' => '4.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Право войти в аккаунт любого пользователя',
            'comment'  => '',
            'title'    => 'user_login_ghost',
            'group'    => 'Пользователи',
            'order_no' => '4.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать логи любого аккаунта',
            'comment'  => '',
            'title'    => 'user_logs_view',
            'group'    => 'Пользователи',
            'order_no' => '4.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право заблокировать/разблокировать авторизацию любого аккаунта',
            'comment'  => '',
            'title'    => 'user_auth_block_unblock',
            'group'    => 'Пользователи',
            'order_no' => '4.8',
        ]);

        $this->insert('action', [
            'subject'  => 'Право забанить/разбанить любой корп. аккаунт',
            'comment'  => '',
            'title'    => 'corp_user_ban_unban',
            'group'    => 'Пользователи',
            'order_no' => '4.9',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра логов движения приглашений любого аккаунта. И текущего количества симуляции в этом аккаунте*',
            'comment'  => '',
            'title'    => 'user_invite_movement_logs_view',
            'group'    => 'Пользователи',
            'order_no' => '4.10',
        ]);

        $this->insert('action', [
            'subject'  => 'Право групповой отправки приглашений от имени любого пользователя',
            'comment'  => '',
            'title'    => 'user_balk_send_invites',
            'group'    => 'Пользователи',
            'order_no' => '4.11',
        ]);

        $this->insert('action', [
            'subject'  => 'Право исключать/добавлять любой аккаунт в нашу рассылку',
            'comment'  => '',
            'title'    => 'user_add_remove_from_news_mail_list',
            'group'    => 'Пользователи',
            'order_no' => '4.12',
        ]);

        $this->insert('action', [
            'subject'  => 'Право управления скидкой любого аккаунта',
            'comment'  => '',
            'title'    => 'user_discount_edit',
            'group'    => 'Пользователи',
            'order_no' => '4.13',
        ]);

        $this->insert('action', [
            'subject'  => 'Право менять данные “для менеджеров по продажам” у любого аккайнта',
            'comment'  => '',
            'title'    => 'user_sales_manager_data_edit',
            'group'    => 'Пользователи',
            'order_no' => '4.14',
        ]);

        $this->insert('action', [
            'subject'  => 'Право добавлять/отнимать симуляции у аккаунта',
            'comment'  => '',
            'title'    => 'user_add_remove_simulations',
            'group'    => 'Пользователи',
            'order_no' => '4.15',
        ]);

        $this->insert('action', [
            'subject'  => 'Право изменить email для аккаунта',
            'comment'  => '',
            'title'    => 'user_change_email',
            'group'    => 'Пользователи',
            'order_no' => '4.16',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать заказы',
            'comment'  => '',
            'title'    => 'orders_view',
            'group'    => 'Заказы',
            'order_no' => '2.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право редактировать заказы',
            'comment'  => '',
            'title'    => 'orders_edit',
            'group'    => 'Заказы',
            'order_no' => '2.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право смотреть список импотров и делать реимпорт',
            'comment'  => '',
            'title'    => 'system_ake_re_import',
            'group'    => 'Управление',
            'order_no' => '8.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право проверки консистенстности оценок и просмотра статистики таких проверок',
            'comment'  => '',
            'title'    => 'system_validate_sim_results_cache',
            'group'    => 'Управление',
            'order_no' => '8.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право генерации и скачивания картинок для ПРББ',
            'comment'  => '',
            'title'    => 'support_prbb_generete_download',
            'group'    => 'Поддержка',
            'order_no' => '3.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать статистику по серверам',
            'comment'  => '',
            'title'    => 'statistic_view',
            'group'    => 'Статистика',
            'order_no' => '7.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать очередь писем',
            'comment'  => '',
            'title'    => 'support_mail_queue_view',
            'group'    => 'Поддержка',
            'order_no' => '3.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать текст писем',
            'comment'  => '',
            'title'    => 'support_mail_details_view',
            'group'    => 'Поддержка',
            'order_no' => '3.8',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра статискики регистрации ',
            'comment'  => '',
            'title'    => 'statistic_registration_view',
            'group'    => 'Статистика',
            'order_no' => '7.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать и редактировать список безплатных доменов',
            'comment'  => '',
            'title'    => 'support_free_mail_services_list_view_edit',
            'group'    => 'Поддержка',
            'order_no' => '3.9',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра и редактрования настроект проекта',
            'comment'  => '',
            'title'    => 'system_setting_view_edit',
            'group'    => 'Управление',
            'order_no' => '8.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Право заходить в админку',
            'comment'  => '',
            'title'    => 'common_use_admin_area',
            'group'    => 'Общее',
            'order_no' => '1.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право редактировать белый список (корпоративных аккаунтов)',
            'comment'  => '',
            'title'    => 'user_white_list_edit',
            'group'    => 'Управление',
            'order_no' => '8.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право создать новую роль',
            'comment'  => '',
            'title'    => 'system_role_add',
            'group'    => 'Управление',
            'order_no' => '8.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право редактировать набор прав внутри роли',
            'comment'  => '',
            'title'    => 'system_role_edit',
            'group'    => 'Управление',
            'order_no' => '8.8',
        ]);

        $startDevMode = YumAction::model()->findByAttributes([
            'title'    => 'start_dev_mode'
        ]);

        $profiles = YumProfile::model()->findAllByAttributes([
            'email' => [
                'slavka@skiliks.com',
                'asd@skiliks.com',
                'ivan@skiliks.com',
                'tony@skiliks.com',
                'tatiana@skiliks.com',
                'nina@skiliks.com',
            ]
        ]);

        $this->delete('permission', " type = 'user' ");

        foreach ($profiles as $profile) {
            $rolePermission = new YumPermission();
            $rolePermission->type = YumPermission::TYPE_USER;
            $rolePermission->principal_id = $profile->user->id;
            $rolePermission->subordinate_id = $profile->user->id;
            $rolePermission->action =  $startDevMode->id;
            $rolePermission->template = 1;
            $rolePermission->save(false);
        }
	}

	public function down() { }
}