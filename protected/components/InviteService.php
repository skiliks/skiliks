<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 7/5/13
 * Time: 6:12 PM
 * To change this template use File | Settings | File Templates.
 */

class InviteService {
    /**
     * @param Invite $invite
     * @param string $notice
     */
    public static function logAboutInviteStatus(Invite $invite, $notice = '--')
    {
        $comment = '';

        $log = new LogInvite();

        $log->action = $notice;

        if (!empty($invite)) {
            $log->status = $invite->getStatusText();
            $log->sim_id = $invite->simulation_id;
            $log->invite_id = $invite->id;
        } else {
            $comment .= 'Инвайт не задан';
        }

        $log->comment = $comment;
        $log->real_date = date('Y-m-d H:i:s');

        $log->save(false);
    }

    public static function  isSimulationOverrideDetected(Invite $invite) {
        //Проверка для корпоративного и персонального на то что по даному инвайту
        //уже начата другая симуляция
        return (null !== $invite->simulation_id &&
            false === $invite->scenario->isAllowOverride());
    }
}