<?php

class DocumentsUnitTest extends PHPUnit_Framework_TestCase {

    /**
     * Checks if user can open attachment from e-mail
     */
    public function testCanOpenDocument()
    {
        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);

        $messages = array_values(MailBoxService::getMessages(array(
            'folderId'   => 1,
            'order'      => 'name',
            'orderType'  => 'ASC',
            'simId'      => $simulation->id
        )));
        $tmpMessages = array_filter($messages, function ($item) {return $item['subject'] === 'По ценовой политике';});

        $attachmentId = $tmpMessages[0]['attachmentFileId'];
        $file = MyDocument::model()->findByPk($attachmentId);
        $this->assertEquals(MyDocumentsService::makeDocumentVisibleInSimulation($simulation, $file), true);
        $name = $tmpMessages[0]['attachmentName'];
        $this->assertCount(1,array_filter(
            MyDocumentsService::getDocumentsList($simulation),
            function ($doc) use ($name) {
                return $doc['name'] === $name;
            }));
    }

    /**
     *
     */
    public function testGetExcel()
    {
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $file = new MyDocument();
        $file->template_id = $scenario->getDocumentTemplate(['code' => 'D1'])->id;
        $file->fileName = $scenario->getDocumentTemplate(['code' => 'D1'])->fileName;
        $file->save(false);
        $file->refresh();

        $this->assertStringEndsWith('.xls' , $file->template->srcFile);
        $this->assertEquals('сводный', $file->getSheetList()[0]['name']);
        $this->assertNotNull('Что-то', $file->getSheetList()[3]['content']);
    }

    /**
     *
     */
    public function testSaveExcel()
    {
        $this->markTestSkipped(); // нужно подумать что тестировать

        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);

        $file = new MyDocument();
        $file->template_id = $scenario->getDocumentTemplate(['code' => 'D1'])->id;
        $file->fileName = $scenario->getDocumentTemplate(['code' => 'D1'])->fileName;
        $file->save(false);

        $this->assertStringEndsWith('.xls' , $file->template->srcFile);
        $this->assertEquals('сводный', $file->getSheetList()[0]['name']);

        $this->assertNotNull('Что-то', $file->getSheetList()[3]['content']);
    }
}
