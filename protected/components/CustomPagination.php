<?php
/**
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 7/26/13
 * Time: 5:41 PM
 * To change this template use File | Settings | File Templates.
 */

class CustomPagination extends CPagination
{
    public function createPageUrl($controller, $page)
    {
        $params = $this->params === null ? $_GET : $this->params;

        $params[$this->pageVar] = $page+1;

        return $controller->createUrl($this->route, $params);
    }
}