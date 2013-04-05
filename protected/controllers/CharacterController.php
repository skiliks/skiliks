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
        $characterData = array_map(function (Character $character) { return $character->getAttributes(['title', 'fio', 'email', 'code', 'phone']);}, $characters);
		$this->sendJSON($characterData);
	}

	// Uncomment the following methods and override them if needed
	/*
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'inlineFilterName',
			array(
				'class'=>'path.to.FilterClass',
				'propertyName'=>'propertyValue',
			),
		);
	}

	public function actions()
	{
		// return external action classes, e.g.:
		return array(
			'action1'=>'path.to.ActionClass',
			'action2'=>array(
				'class'=>'path.to.AnotherActionClass',
				'propertyName'=>'propertyValue',
			),
		);
	}
	*/
}