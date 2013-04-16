<?php

/**
 * Returns Character list for phone, mail and other purposes
 * Class CharacterController
 */
class CharacterController extends AjaxController
{
	public function actionList()
	{
        $simulation = $this->getSimulationEntity();
        $characters = $simulation->game_type->getCharacters([]);
        $characterData = array_map(function (Character $character) { return $character->getAttributes(['id', 'title', 'fio', 'email', 'code', 'phone']);}, $characters);
		$this->sendJSON($characterData);
	}
}