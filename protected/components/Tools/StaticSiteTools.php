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

        if ($request->getPathInfo() == '') {
            $results .= '';
        } else if ($request->getPathInfo() == '/team') {
            $results .= " inner-team";
        } else if ($request->getPathInfo() == '/registration/choose-account-type') {
            $results .= " inner-registration";
        } else {
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

        if ($request->getPathInfo() == '') {
            $result .= ' main-page';
        }

        if ($request->getPathInfo() == 'site/comingSoonSuccess') {
            $result .= ' main-page';
        }

        if ($request->getPathInfo() == 'team') {
            $result .= ' team-page';
        }

        return $result;
    }
}