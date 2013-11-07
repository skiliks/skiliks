<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 8/22/13
 * Time: 6:05 PM
 * To change this template use File | Settings | File Templates.
 */

class m130000_000000_update_yum_users extends CDbMigration {

    public function up() {
//        $this->addColumn("user", "agree_with_terms", "VARCHAR(3) DEFAULT NULL");
//
//        $this->update(
//            'user',
//            ['agree_with_terms' => YumUser::AGREEMENT_MADE],
//            ' activationKey = 1');
//
//        $this->addColumn('user', 'is_admin', 'INT(1) NOT NULL DEFAULT 0');
//
//        $users = Yii::app()->params['initial_data']['users'];
//        foreach($users as $user) {
//            $user_db = YumUser::model()->findByAttributes(['username'=>$user['username']]);
//            if(null !== $user_db) {
//                $user_db->is_admin = 1;
//                $user_db->update();
//                echo $user['username']." - done\r\n";
//            }else{
//                echo $user['username']." - fail\r\n";
//            }
//        }
//
//        $arr = ['gugu', 'vad', 'kirill', 'ahmed', 'rkilimov'];
//
//        foreach ($arr as $username) {
//            $this->update(
//                'user',
//                ['is_admin' => 0],
//                "username = '".$username."'"
//            );
//        }
//        $this->addColumn('profile', "assessment_results_render_type", "varchar(30) DEFAULT 'percentil'");
//        $this->addColumn('user', "ip_address",  "varchar(15) DEFAULT NULL");
    }

    public function down()
    {
        $this->dropColumn("user", "agree_with_terms");

        $this->dropColumn('user', 'is_admin');

        $this->dropColumn('profile', "assessment_results_render_type");

        $users = Yii::app()->params['initial_data']['users'];
        foreach($users as $user) {
            $user_db = YumUser::model()->findByAttributes(['username'=>$user['username']]);
            if(null !== $user_db) {
                $user_db->is_admin = 0;
                $user_db->update();
                echo $user['username']." - done\r\n";
            }else{
                echo $user['username']." - fail\r\n";
            }
        }

        $arr = ['gugu', 'vad', 'kirill', 'ahmed', 'rkilimov', 'pernifin'];

        foreach ($arr as $username) {
            $this->update(
                'user',
                ['is_admin' => 0],
                "username = '".$username."'"
            );
        }
    }
}