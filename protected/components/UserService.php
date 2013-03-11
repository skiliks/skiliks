<?php



/**
 * Сервис по работе с пользователями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserService {

    const CAN_START_SIMULATION_IN_DEV_MODE = 'start_dev_mode';

    /**
     * Получить список режимов запуска симуляции доступных пользователю: {promo, developer}
     * @param int $uid 
     * @return array
     */
    public static function getModes($user)
    {
        $modes = [];
        $modes[1] = Simulation::MODE_PROMO_LABEL;

        if ($user->can(self::CAN_START_SIMULATION_IN_DEV_MODE)) {
            $modes[2] = Simulation::MODE_DEVELOPER_LABEL;
        }
        
        return $modes;
    }
    
    public static function addUserSubscription($email)
    {
        $response = ['result'  => 0];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] =  Yii::t('site', "Invalid email - '{email}'!", ['{email}' => $email]);
        } elseif (EmailsSub::model()->findByEmail($email)) {
            $response['message'] =  Yii::t('site', "Email - {email} has been already added before!", ['{email}' => $email]);
        } else {
            $subscription = new EmailsSub();
            $subscription->email = $email;
            $subscription->save();

            $response['result'] =  1;
            $response['message'] =  Yii::t('site', 'Email {email} has been successfully added!', ['{email}' => $email]);
        }

        return $response;
    }
}


