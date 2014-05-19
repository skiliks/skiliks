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

        $data['data'] = SimulationService::getCharactersList($simulation);
        $data['result'] = self::STATUS_SUCCESS;

        $this->sendJSON($data);
	}
}