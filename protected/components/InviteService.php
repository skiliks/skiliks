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

    /**
     * @param Invite $invite
     * @return bool
     */
    public static function  isSimulationOverrideDetected(Invite $invite) {
        //Проверка для корпоративного и персонального на то что по даному инвайту
        //уже начата другая симуляция
        return (null !== $invite->simulation_id &&
            false === $invite->scenario->isAllowOverride());
    }

    /**
     * Метод откланяет приглашение из DeclineExplanation
     *
     * @param YumUser $user
     * @param DeclineExplanation $declineExplanation
     *
     * @return null|string
     */
    public static function declineInvite(YumUser $user, DeclineExplanation $declineExplanation) {

        if (null === $declineExplanation->invite) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует');
            return '/dashboard';
        }

        if ((int)$declineExplanation->invite->status === Invite::STATUS_DELETED) {
            Yii::app()->user->setFlash('success', 'Выбранного к отмене приглашения не существует');
            return '/dashboard';
        }

        if ($user->id !== $declineExplanation->invite->receiver_id &&
            $user->id !== $declineExplanation->invite->owner_id &&
            strtolower($user->profile->email) !== strtolower($declineExplanation->invite->email) &&
            null !== $declineExplanation->invite->receiver_id) {

            Yii::app()->user->setFlash('success', 'Вы не можете удалить чужое приглашение');
            return '/dashboard';
        }

        $initValue = $declineExplanation->invite->ownerUser->getAccount()->getTotalAvailableInvitesLimit();

        $declineExplanation->invite->ownerUser->getAccount()->invites_limit++;
        $declineExplanation->invite->ownerUser->getAccount()->save(false);

        UserService::logCorporateInviteMovementAdd(sprintf("Пользователь %s отклонил приглашение номер %s. В аккаунт возвращена одна симуляция.",
            $declineExplanation->invite->email, $declineExplanation->invite->id),  $declineExplanation->invite->ownerUser->getAccount(), $initValue);


        $declineExplanation->invite_recipient_id = $declineExplanation->invite->receiver_id;
        $declineExplanation->invite_owner_id = $declineExplanation->invite->owner_id;
        $declineExplanation->vacancy_label = $declineExplanation->invite->getVacancyLabel();
        $declineExplanation->created_at = date('Y-m-d H:i:s');

        $invite_status = $declineExplanation->invite->status;
        $declineExplanation->invite->status = Invite::STATUS_DECLINED;
        $declineExplanation->invite->updated_at = (new DateTime('now', new DateTimeZone('Europe/Moscow')))->format("Y-m-d H:i:s");
        $declineExplanation->invite->update(false, ['status']);
        InviteService::logAboutInviteStatus($declineExplanation->invite, 'Пользователь сменил статус с '.Invite::getStatusNameByCode($invite_status)." на ".Invite::getStatusNameByCode($declineExplanation->invite->status));

        if (!$user->isAuth()) {
            Yii::app()->user->setFlash('success', UserService::renderPartial('static/dashboard/_thank_you_form', []));
            return '/';
        } elseif ($user->isPersonal()) {
            return '/dashboard';
        }
        return null;
    }
}