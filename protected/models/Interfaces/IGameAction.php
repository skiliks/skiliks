<?php
/**
 * Class IGameAction is common for all actions in product
 *
 * Example of Action is document, mail template, dialog, window. This used to better understanding Anton's logic
 */
interface IGameAction {
    /**
     * @return string
     */
    function getCode();
}