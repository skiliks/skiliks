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
    
    public function testGetExcel()
    {
        $file = new MyDocument();
        $file->setPrimaryKey(2);
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $file->template = $scenario->getDocumentTemplate(['code' => 'D1']);
        $this->assertStringEndsWith('.sc' , $file->template->srcFile);
        $this->assertEquals($file->getSheetList()[0]['name'], 'Сводный');
        $file->setSheetContent('Сводный', 'жопа');
    }
    
    public function testSaveExcel()
    {
        $file = new MyDocument();
        $file->setPrimaryKey(2);
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $file->template = $scenario->getDocumentTemplate(['code' => 'D1']);
        $this->assertStringEndsWith('.sc' , $file->template->srcFile);
        $this->assertEquals($file->getSheetList()[0]['name'], 'Сводный');

        $file->setSheetContent('Логистика', 'Что-то');
        $file->setSheetContent('Сводный', 'жопа');
        $this->assertEquals($file->getSheetList()[3]['content'], 'Что-то');
    }

    public function testMissedDocuments()
    {
        $docPath = realpath(__DIR__ . '/../../../' . Yii::app()->params['zoho']['xlsTemplatesDirPath']);
        $allDocuments = DocumentTemplate::model()->findAll();

        foreach ($allDocuments as $document) {
            $this->assertFileExists($docPath . '/' . $document->srcFile);
        }
    }
}
