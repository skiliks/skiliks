<?php

/**
 * Returns Character list for phone, mail and other purposes
 * Class CharacterController
 */
class CharacterController extends SimulationBaseController
{
	public function actionList()
	{
        $simulation = $this->getSimulationEntity();

        $characters = $simulation->game_type->getCharacters([]);
        $data['data'] = array_map(
            function (Character $character) {
                return $character->getAttributes([
                    'id', 'title', 'fio', 'email', 'code', 'phone',
                ]);
            },
        $characters);

        $data['result'] = self::STATUS_SUCCESS;

        $this->sendJSON($data);
	}
}