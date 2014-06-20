<?php

class m140620_155446_add_permissions extends CDbMigration
{
	public function up()
	{
//        $this->addColumn('action', 'group', 'VARCHAR(60)');
//        $this->addColumn('action', 'order_no', 'VARCHAR(5)');

        $this->delete('action', ' title = "run_full_simulation" ');

        $this->update('action', [
            'comment'  => 'Из админки можно запустить игру в специальном интерфейсе, который упрощает тестирование.',
            'subject'  => 'Право запуска DEV симуляции',
            'group'    => 'Общее',
            'order_no' => '1.1',
        ], ' title = "start_dev_mode" ');

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
            'title'    => '',
            'group'    => 'Симуляции',
            'order_no' => '6.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право смотреть подробную информацию по любой симуляции',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Симуляции',
            'order_no' => '6.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра лога запросов к серверу по любой симуляции',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Симуляции',
            'order_no' => '6.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право включать / выключать аварийную панель',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Симуляции',
            'order_no' => '6.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра рейтинга симуляций',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Статистика',
            'order_no' => '7.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать список подписавшихся на рассылку',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать и редактировать отзывы',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать логи авторизации',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать список супер-админов',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотривать список забаненных пользователей',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать список текущих симуляций',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра списка корпоративных пользователей',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право импорта списка корпоративных пользователей',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра списка всех пользователей*',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право посмотра детальной информации о любом пользователе*',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право сменить пароль любому пользователю',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Право войти в аккаунт любого пользователя',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать логи любого аккаунта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право заблокировать/разблокировать авторизацию любого аккаунта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.8',
        ]);

        $this->insert('action', [
            'subject'  => 'Право забанить/разбанить любой корп. аккаунт',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.9',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра логов движения приглашений любого аккаунта. И текущего количества симуляции в этом аккаунте*',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.10',
        ]);

        $this->insert('action', [
            'subject'  => 'Право групповой отправки приглашений от имени любого пользователя',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.11',
        ]);

        $this->insert('action', [
            'subject'  => 'Право исключать/добавлять любой аккаунт в нашу рассылку',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.12',
        ]);

        $this->insert('action', [
            'subject'  => 'Право управления скидкой любого аккаунта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.13',
        ]);

        $this->insert('action', [
            'subject'  => 'Право менять данные “для менеджеров по продажам” у любого аккайнта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.14',
        ]);

        $this->insert('action', [
            'subject'  => 'Право добавлять/отнимать симуляции у аккаунта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.15',
        ]);

        $this->insert('action', [
            'subject'  => 'Право изменить email для аккаунта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Пользователи',
            'order_no' => '4.16',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать заказы',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Заказы',
            'order_no' => '2.1',
        ]);

        $this->insert('action', [
            'subject'  => 'Право редактировать заказы',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Заказы',
            'order_no' => '2.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право смотреть список импотров и делать реимпорт',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право проверки консистенстности оценок и просмотра статистики таких проверок',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.4',
        ]);

        $this->insert('action', [
            'subject'  => 'Право генерации и скачивания картинок для ПРББ',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать статистику по серверам',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Статистика',
            'order_no' => '7.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать очередь писем',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать текст писем',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.8',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра статискики регистрации ',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Статистика',
            'order_no' => '7.3',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просматривать и редактировать список безплатных доменов',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Поддержка',
            'order_no' => '3.9',
        ]);

        $this->insert('action', [
            'subject'  => 'Право просмотра и редактрования настроект проекта',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.5',
        ]);

        $this->insert('action', [
            'subject'  => 'Позволять заходить в админку',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Общее',
            'order_no' => '1.2',
        ]);

        $this->insert('action', [
            'subject'  => 'Право редактировать белый список (корпоративных аккаунтов)',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.6',
        ]);

        $this->insert('action', [
            'subject'  => 'Право создать новую роль',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.7',
        ]);

        $this->insert('action', [
            'subject'  => 'Право редактировать набор прав внутри роли',
            'comment'  => '',
            'title'    => '',
            'group'    => 'Управление',
            'order_no' => '8.8',
        ]);
	}

	public function down() { }
}