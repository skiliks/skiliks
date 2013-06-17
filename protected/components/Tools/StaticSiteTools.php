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
        if (Yii::app()->getController()->getId() == 'static/simulations') {
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
}