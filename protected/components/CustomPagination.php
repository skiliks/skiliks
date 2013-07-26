<?php
/**
 *
 *
 * Created by JetBrains PhpStorm.
 * User: root
 * Date: 7/26/13
 * Time: 5:41 PM
 * To change this template use File | Settings | File Templates.
 */

class CustomPagination extends CPagination
{
    /**
     * Переопределяю чтоб ссылка на первую страницу имела такой же формат, как и ссылка на последнюю
     * иначе получается что первая страница выдаёт 404
     *
     * @param CController $controller
     * @param int $page
     * @return string
     */
    public function createPageUrl($controller, $page)
    {
        $params = $this->params === null ? $_GET : $this->params;

        $params[$this->pageVar] = $page+1;

        return $controller->createUrl($this->route, $params);
    }
}