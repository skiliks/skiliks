<?php

class m140623_124443_add_roles extends CDbMigration
{
	public function up()
	{
        $this->truncateTable('role');

        $this->insert('role', [
            'title' => 'СуперАдмин',
        ]);

        $superAdmin = YumRole::model()->findByAttributes(['title' => 'СуперАдмин']);

        $this->insert('role', [
            'title' => 'Админ',
        ]);

        $admin = YumRole::model()->findByAttributes(['title' => 'Админ']);

        $this->insert('role', [
            'title' => 'Тренер',
        ]);

        $couch = YumRole::model()->findByAttributes(['title' => 'Тренер']);

        $this->insert('role', [
            'title' => 'Пользователь сайта',
        ]);

        $siteUser = YumRole::model()->findByAttributes(['title' => 'Пользователь сайта']);

        $actions = YumAction::model()->findAll([
            'order' => 'order_no ASC',
        ]);

        $this->delete('permission', " type = 'role' ");

        // super admin
        foreach ($actions as $action) {
            // супер админ может всё! :)
            /** @var YumPermission $rolePermission */
            $rolePermission = new YumPermission();
            $rolePermission->type = YumPermission::TYPE_ROLE;
            $rolePermission->principal_id = $superAdmin->id;
            $rolePermission->subordinate_id = $superAdmin->id;
            $rolePermission->action = $action->id;
            $rolePermission->template = 1;
            $rolePermission->save(false);
        }

        // admin
        $arr = [
            '1.1' => true,
            '1.2' => true,
            '2.1' => true,
            '2.2' => true,
            '3.1' => true,
            '3.2' => true,
            '3.3' => true,
            '3.4' => true,
            '3.5' => true,
            '3.6' => true,
            '3.7' => true,
            '3.8' => true,
            '3.9' => true,
            '4.1' => true,
            '4.2' => true,
            '4.3' => true,
            '4.4' => true,
            '4.5' => true,
            '4.6' => true,
            '4.7' => true,
            '4.8' => true,
            '4.9' => true,
            '4.10' => true,
            '4.11' => true,
            '4.12' => true,
            '4.13' => true,
            '4.14' => true,
            '4.15' => true,
            '4.16' => false,
            '5.1' => true,
            '5.2' => true,
            '5.3' => true,
            '5.4' => true,
            '5.5' => true,
            '5.6' => true,
            '5.7' => true,
            '5.8' => true,
            '5.9' => true,
            '6.1' => true,
            '6.2' => true,
            '6.3' => true,
            '6.4' => true,
            '7.1' => true,
            '7.2' => true,
            '7.3' => true,
            '8.1' => true,
            '8.2' => true,
            '8.3' => false,
            '8.4' => false,
            '8.5' => false,
            '8.6' => false,
            '8.7' => false,
            '8.8' => false,
        ];

        // admin
        foreach ($actions as $action) {
            if (true == $arr[$action->order_no]) {
                /** @var YumPermission $rolePermission */
                $rolePermission = new YumPermission();
                $rolePermission->type = YumPermission::TYPE_ROLE;
                $rolePermission->principal_id = $admin->id;
                $rolePermission->subordinate_id = $admin->id;
                $rolePermission->action = $action->id;
                $rolePermission->template = 1;
                $rolePermission->save(false);
            }
        }

        // couch
        $arr = [
            '1.1' => false,
            '1.2' => true,
            '2.1' => false,
            '2.2' => false,
            '3.1' => false,
            '3.2' => false,
            '3.3' => false,
            '3.4' => false,
            '3.5' => false,
            '3.6' => false,
            '3.7' => false,
            '3.8' => false,
            '3.9' => false,
            '4.1' => true,
            '4.2' => false,
            '4.3' => true,
            '4.4' => true,
            '4.5' => false,
            '4.6' => false,
            '4.7' => false,
            '4.8' => false,
            '4.9' => false,
            '4.10' => false,
            '4.11' => false,
            '4.12' => false,
            '4.13' => false,
            '4.14' => false,
            '4.15' => false,
            '4.16' => false,
            '5.1' => true,
            '5.2' => true,
            '5.3' => false,
            '5.4' => false,
            '5.5' => true,
            '5.6' => true,
            '5.7' => false,
            '5.8' => false,
            '5.9' => false,
            '6.1' => false,
            '6.2' => true,
            '6.3' => false,
            '6.4' => false,
            '7.1' => false,
            '7.2' => false,
            '7.3' => false,
            '8.1' => false,
            '8.2' => false,
            '8.3' => false,
            '8.4' => false,
            '8.5' => false,
            '8.6' => false,
            '8.7' => false,
            '8.8' => false,
        ];

        // couch
        foreach ($actions as $action) {
            if (true == $arr[$action->order_no]) {
                /** @var YumPermission $rolePermission */
                $rolePermission = new YumPermission();
                $rolePermission->type = YumPermission::TYPE_ROLE;
                $rolePermission->principal_id = $couch->id;
                $rolePermission->subordinate_id = $couch->id;
                $rolePermission->action = $action->id;
                $rolePermission->template = 1;
                $rolePermission->save(false);
            }
        }

        // site user
        $arr = [
            '1.1' => false,
            '1.2' => false,
            '2.1' => false,
            '2.2' => false,
            '3.1' => false,
            '3.2' => false,
            '3.3' => false,
            '3.4' => false,
            '3.5' => false,
            '3.6' => false,
            '3.7' => false,
            '3.8' => false,
            '3.9' => false,
            '4.1' => false,
            '4.2' => false,
            '4.3' => false,
            '4.4' => false,
            '4.5' => false,
            '4.6' => false,
            '4.7' => false,
            '4.8' => false,
            '4.9' => false,
            '4.10' => false,
            '4.11' => false,
            '4.12' => false,
            '4.13' => false,
            '4.14' => false,
            '4.15' => false,
            '4.16' => false,
            '5.1' => false,
            '5.2' => false,
            '5.3' => false,
            '5.4' => false,
            '5.5' => false,
            '5.6' => false,
            '5.7' => false,
            '5.8' => false,
            '5.9' => false,
            '6.1' => false,
            '6.2' => false,
            '6.3' => false,
            '6.4' => false,
            '7.1' => false,
            '7.2' => false,
            '7.3' => false,
            '8.1' => false,
            '8.2' => false,
            '8.3' => false,
            '8.4' => false,
            '8.5' => false,
            '8.6' => false,
            '8.7' => false,
            '8.8' => false,
        ];

        // site user
        foreach ($actions as $action) {
            if (true == $arr[$action->order_no]) {
                /** @var YumPermission $rolePermission */
                $rolePermission = new YumPermission();
                $rolePermission->type = YumPermission::TYPE_ROLE;
                $rolePermission->principal_id = $siteUser->id;
                $rolePermission->subordinate_id = $siteUser->id;
                $rolePermission->action = $action->id;
                $rolePermission->template = 1;
                $rolePermission->save(false);
            }
        }
	}

	public function down()
	{
        $this->truncateTable('role');
	}
}