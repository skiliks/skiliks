<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 3/21/13
 * Time: 9:32 PM
 * To change this template use File | Settings | File Templates.
 */

class StaticSiteTools
{
    /**
     * @param HttpRequest $request
     *
     * @return string
     */
    public static function getBodyClass($request)
    {
        $url = $request->getUrl();

        $results = 'action-'.Yii::app()->getController()->getAction()->getId();
        $results .= ' controller-'.str_replace('/','-',Yii::app()->getController()->getId());
        $results .= sprintf(
            ' action-controller-%s-%s',
            Yii::app()->getController()->getAction()->getId(),
            str_replace('/','-',Yii::app()->getController()->getId())
        );

        if (Yii::app()->getController()->getId() == 'static/pages') {
            if (Yii::app()->getController()->getAction()->getId() == 'product') {
                $results .= " inner";
            }
            if (Yii::app()->getController()->getAction()->getId() == 'team') {
                $results .= " inner-team";
            }
            if (Yii::app()->getController()->getAction()->getId() == 'tariffs') {
                $results .= " inner-registration";
            }
        }
        if (Yii::app()->getController()->getId() == 'static/userAuth') {
            $results .= " inner-registration";
        }
        if (Yii::app()->getController()->getId() == 'static/dashboard') {
                $results .= " inner";
        }

        if (Yii::app()->getController()->getId() == 'static/profile') {
            $results .= " inner";
        }
        if (Yii::app()->getController()->getId() == 'registration') {
            $results .= " inner";
        }
        if (Yii::app()->getController()->getId() == 'auth') {
            $results .= " inner";
        }

        return $results;
    }

    /**
     * @param HttpRequest $request
     *
     * @return string
     */
    public static function getContainerClass($request)
    {
        $url = $request->getUrl();

        $result = 'container';

        if ($request->getPathInfo() == 'site/comingSoonSuccess') {
            $result .= ' main-page';
        }

        if ($request->getPathInfo() == 'team') {
            $result .= ' team-page';
        }

        return $result;
    }

    /**
     * This is very simple method, it doesn`t allow to use custom criteria, multiple $labelColumns and $idColumns
     * If it will be necessary - I add formatValuesArrayPro
     *
     * @param string $model, model class ame, like 'Invitation'
     * @param string $labelColumn
     * @param string $idColumn
     * @param string $conditions, like ' where sim_id = 25 '
     * @param string $params, like ' sort_order DESC '
     */
    public static function formatValuesArrayLite($model, $idColumn = 'id', $labelColumn = 'label', $conditions = '', $emptyValue = false, $params = [])
    {
        $result = [];

        if (false !== $emptyValue) {
            $result[''] = $emptyValue;
        }

        try {
            foreach ($model::model()->findAll($conditions, $params) as $item) {
                $result[$item->{$idColumn}] = $item->{$labelColumn};
            }
        } catch (CDbException $e) {
            // this is Not results Exception
        }

        return $result;
    }

    /**
     * Not finished. Must be updated for i18n
     *
     * @param float $amount
     * @param string $currencyCode, ISO-3
     * @param string $language
     * @param string $pattern, Yii formatter pattern
     *
     * @link: http://www.yiiframework.com/doc/api/1.1/CNumberFormatter#formatCurrency-detail
     */
    public static function getI18nCurrency($amount, $currencyCode, $language = 'ru_RU', $pattern = '#,##0.00')
    {
        $cn = new CNumberFormatter($language);
        return $cn->format($pattern, $amount, $currencyCode);
    }

    /**
     * @param CHttpRequest $request
     * @param string $language: 'ru','en'
     *
     * @return mixed
     */
    public static function getLangSwitcherUrl(CHttpRequest $request, $language) {
        $url = $request->getUrl();

        if ('ru' == $language) {
            $url = str_replace('/en', '', $url);
            $url = str_replace('/ru', '/en', $url);
            if (false === strpos($url, '/en')) {
                $url .= '/en';
            }
        } else {
            $url = str_replace('/ru', '', $url);
            $url = str_replace('/en', '/ru', $url);
            if (false === strpos($url, '/ru')) {
                $url .= '/ru';
            }
        }

        $url = str_replace('//','/', $url);

        return $url;
    }

    /**
     * @param CHttpRequest $request
     * @param CController $controller
     *
     * @return bool
     */
    public static function isLangSwitcherUrlVisible(CHttpRequest $request, CController $controller) {
        return (0 === strpos($request->getPathInfo(), 'static/team')) ||
            (0 === strpos($request->getPathInfo(), 'static/product')) ||
            (0 === strpos($request->getPathInfo(), 'static/tariffs')) ||
            ($controller->getId() == 'static/pages' && $controller->getAction()->getId() == 'index');
    }

    /**
     * @return string, JSON
     */
    public static function getRandomAssessmentDetails()
    {
        $arr = [80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100];
        shuffle($arr); // чтоб значения не повторялись

        $overall = $arr[0];
        $percentile = $arr[1]/100;
        $management = $arr[2];
        $performance = $arr[3];
        $time = $arr[4];

        // 1.1 {
        $management_1 = $arr[5];

        $management_1_positive = [];
        $management_1_positive[0] = $arr[6];
        $management_1_positive[1] = $arr[7];
        $management_1_positive[2] = $arr[8];
        $management_1_positive[3] = $arr[9];

        $management_1_negative = [];
        $management_1_negative[0] = 0;
        $management_1_negative[1] = 0;
        $management_1_negative[2] = 0;
        $management_1_negative[3] = 0;
        $management_1_negative[4] = 0;

        // случайно проставляю одно значение в негативной шкале 10-20%
        $management_1_negative[rand(0,4)] = rand(15,23);
        // 1.1 }

        // 1.2 {
        $management_2 = $arr[10];

        $management_2_positive = [];
        $management_2_positive[0] = $arr[11];
        $management_2_positive[1] = $arr[12];
        $management_2_positive[2] = $arr[13];
        $management_2_positive[3] = $arr[14];

        $management_2_negative = [];
        $management_2_negative[0] = 0;
        $management_2_negative[1] = 0;
        $management_2_negative[2] = 0;
        $management_2_negative[3] = 0;

        // случайно проставляю одно значение в негативной шкале 10-20%
        $management_2_negative[rand(0,2)] = rand(15,23);
        // 1.2 }

        // 1.3 {
        $management_3 = $arr[15];

        $management_3_positive = [];
        $management_3_positive[0] = $arr[16];
        $management_3_positive[1] = $arr[17];
        $management_3_positive[2] = $arr[18];

        $management_3_negative = [];
        $management_3_negative[0] = 0;
        $management_3_negative[1] = 0;
        $management_3_negative[2] = 0;

        // случайно проставляю одно значение в негативной шкале 10-20%
        $management_3_negative[rand(0,2)] = rand(15,23);
        // 1.3 }

        // $arr[18-20] не хватает на все значения пРезультатовности,
        // так что смешиваем массив ещё раз
        shuffle($arr);

        $performance_0 = $arr[0];
        $performance_1 = $arr[1];
        $performance_2 = $arr[2];
        $performance_2min = $arr[3];

        // Тайм менеджмент {
        $workdayOverheadDuration = rand(3, 30);

        $time_spend_for_1st_priority_activities = rand(80, 100);
        $time_spend_for_1st_non_priority_activities = 0.6 * (100 - $time_spend_for_1st_priority_activities);
        $time_spend_for_1st_inactivity = 0.4 * (100 - $time_spend_for_1st_priority_activities);

        $r[0] = rand(0, 100);
        $r[1] = rand(0, 100);
        $r[2] = rand(0, 100);
        $r[3] = rand(0, 100);
        $r[4] = rand(0, 100);

        $sum = $r[0] + $r[1] + $r[2] + $r[3] +$r[4];

        $r_normalized[0] = $r[0] / $sum;
        $r_normalized[1] = $r[1] / $sum;
        $r_normalized[2] = $r[2] / $sum;
        $r_normalized[3] = $r[3] / $sum;
        $r_normalized[4] = $r[4] / $sum;

        $first_priority_documents   = 450 * $r_normalized[0];
        $first_priority_meetings    = 450 * $r_normalized[1];
        $first_priority_phone_calls = 450 * $r_normalized[2];
        $first_priority_mail        = 450 * $r_normalized[3];
        $first_priority_planning    = 450 * $r_normalized[4];

        $r[0] = rand(0, 100);
        $r[1] = rand(0, 100);
        $r[2] = rand(0, 100);
        $r[3] = rand(0, 100);
        $r[4] = rand(0, 100);

        $sum = $r[0] + $r[1] + $r[2] + $r[3] +$r[4];

        $r_normalized[0] = $r[0] / $sum;
        $r_normalized[1] = $r[1] / $sum;
        $r_normalized[2] = $r[2] / $sum;
        $r_normalized[3] = $r[3] / $sum;
        $r_normalized[4] = $r[4] / $sum;

        $non_priority_documents   = 60 * $r_normalized[0];
        $non_priority_meetings    = 60 * $r_normalized[1];
        $non_priority_phone_calls = 60 * $r_normalized[2];
        $non_priority_mail        = 60 * $r_normalized[3];
        $non_priority_planning    = 60 * $r_normalized[4];

        // } Тайм менеджмент

        $result = "{
                'overall':'$overall',
                'percentile' : {
                                 total : '$percentile'
                               },
                'additional_data':{
                    'management':'5',
                    'performance':'35',
                    'time':'15'
                },
                'management':{
                    'total':'$management',
                    '1':{
                        'total':'$management_1',
                        '1_1':{
                            '+':'$management_1_positive[0]',
                            '-':'$management_1_negative[0]'
                        },
                        '1_2':{
                            '+':'$management_1_positive[1]',
                            '-':'$management_1_negative[1]'
                        },
                        '1_3':{
                            '+':'$management_1_positive[2]',
                            '-':'$management_1_negative[2]'
                        },
                        '1_4':{
                            '+':'$management_1_positive[3]',
                            '-':'$management_1_negative[3]'
                        },
                        '1_5':{
                            '+':'0',
                            '-':'$management_1_negative[4]'
                        }
                    },
                    '3':{
                        'total':'$management_2',
                        '3_1':{
                            '+':'$management_2_positive[0]',
                            '-':'$management_2_negative[0]'
                        },
                        '3_2':{
                            '+':'$management_2_positive[1]',
                            '-':'$management_2_negative[1]'
                        },
                        '3_3':{
                            '+':'$management_2_positive[2]',
                            '-':'$management_2_negative[2]'
                        },
                        '3_4':{
                            '+':'$management_2_positive[3]',
                            '-':'$management_2_negative[3]'
                        }
                    },
                    '2':{
                        'total':'$management_3',
                        '2_1':{
                            '+':'$management_3_positive[0]',
                            '-':'$management_3_negative[0]'},
                        '2_2':{
                            '+':'$management_3_positive[1]',
                            '-':'$management_3_negative[1]'},
                        '2_3':{
                            '+':'$management_3_positive[2]',
                            '-':'$management_3_negative[2]'
                        }
                    }
                },
                'performance':{
                    'total':'$performance',
                    '0': '$performance_0',
                    '1': '$performance_1',
                    '2': '$performance_2',
                    '2_min': '$performance_2min',
                },
                'time':{
                    'total':'$time',
                    'workday_overhead_duration': '$workdayOverheadDuration',
                    'time_spend_for_1st_priority_activities':'$time_spend_for_1st_priority_activities',
                    'time_spend_for_non_priority_activities':'$time_spend_for_1st_non_priority_activities',
                    'time_spend_for_inactivity':'$time_spend_for_1st_inactivity',
                    '1st_priority_documents':'$first_priority_documents',
                    '1st_priority_meetings':'$first_priority_meetings',
                    '1st_priority_phone_calls':'$first_priority_phone_calls',
                    '1st_priority_mail':'$first_priority_mail',
                    '1st_priority_planning':'$first_priority_planning',
                    'non_priority_documents':'$non_priority_documents',
                    'non_priority_meetings':'$non_priority_meetings',
                    'non_priority_phone_calls':'$non_priority_phone_calls',
                    'non_priority_mail':'$non_priority_mail',
                    'non_priority_planning':'$non_priority_planning',
                    'efficiency':'$time'},
                'personal':{'9':'0.000000','10':'0.000000','12':'0.000000','13':'0.000000','14':'0.000000','15':'0.000000','16':'0.000000','11':'0.000000'}
                }";
        return $result;
    }

    /**
     * Задаёт переметр чата который позволяет получить более подробную информацию о пользователе в чате
     *
     * @param YumUser $yiiUser
     *
     * @return string
     *
     * @link: http://siteheart.com/ru/doc/sso
     */
    public static function getSiteHeartAuth($yiiUser)
    {
        $user = [
        'avatar' => '',
        'data'   => []
        ];

        if (null == $yiiUser || false == $yiiUser->isAuth()) {
            $user['nick']  = 'т';
            $user['id']    = null;
            $user['email'] = null;
        } else {
            $user['nick']  = sprintf(
                '%s %s (%s)',
                ucfirst($yiiUser->profile->firstname),
                ucfirst($yiiUser->profile->lastname),
                $yiiUser->profile->email
            );
            $user['id']    = $yiiUser->id;
            $user['email'] = $yiiUser->profile->email;
        }

        $time = time();
        $secret = Yii::app()->params['SiteHeartSecretKey'];
        $user_base64 = base64_encode( json_encode($user) );
        $sign = md5($secret . $user_base64 . $time);

        return $user_base64 . "_" . $time . "_" . $sign;
    }
}