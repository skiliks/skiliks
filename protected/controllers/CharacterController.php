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

        $data['data'] = [];

        foreach ($characters as $character) {
            $characterData = $character->getAttributes([
                'id', 'title', 'fio', 'email', 'code', 'phone',
            ]);

            // этот метод вызывается 1 раз за игру, поэтому проще поместить запросы в базк сюда,
            // чем наращивать колонки в БД
            $characterData['has_mail_theme'] = (int) (0 < OutboxMailTheme::model()->countByAttributes([
                'character_to_id' => $characterData['id'],
                'scenario_id'     => $simulation->game_type->id,
            ]));

            $data['data'][] = $characterData;
        }

        $data['result'] = self::STATUS_SUCCESS;

        $this->sendJSON($data);
	}
}