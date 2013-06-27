<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 22.02.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */
class DocumentsUnitTest extends CDbTestCase
{
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
     * Checks if user can open excel
     */
    public function testCanOpenExcel()
    {
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);


        $documentTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D1',
            'scenario_id' => $simulation->scenario_id,
        ]);

        $file = MyDocument::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'template_id' => $documentTemplate->primaryKey,
        ]);
        $zoho = new ZohoDocuments($simulation->primaryKey, $file->primaryKey, $file->template->srcFile);
        $zoho->response = "﻿HTTP/1.1 302 Found
Strict-Transport-Security: max-age=432000
Set-Cookie: zscookcsr=d9d5b062-7748-4484-85ed-da9bc82fc14f; Path=/
Set-Cookie: JSESSIONID=2245B8142B2DB13921082FAB5D7BB741; Path=/
Location: https://sheet.zoho.com/editor.do?doc=c2826da1f9894a54366f67ddf2326ff00c1ce3234acde876
Content-Type: text/html;charset=UTF-8
Content-Length: 0
Date: Wed, 27 Feb 2013 17:06:17 GMT
Server: ZGS";
        //$this->assertEquals($zoho->getUrl(), 'https://sheet.zoho.com/editor.do?doc=c2826da1f9894a54366f67ddf2326ff00c1ce3234acde876');
    }

    public function testLoadZohoFile(){

        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $invite = new Invite();
        $invite->scenario = new Scenario();
        $invite->receiverUser = $user;
        $invite->scenario->slug = Scenario::TYPE_FULL;
        $simulation = SimulationService::simulationStart($invite, Simulation::MODE_PROMO_LABEL);
        $documentTemplate = DocumentTemplate::model()->findByAttributes([
            'code' => 'D1',
            'scenario_id' => $simulation->scenario_id,
        ]);

        $file = MyDocument::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'template_id' => $documentTemplate->primaryKey,
        ]);
        $budgetPath = __DIR__ . '/files/D1.xls';
        ZohoDocuments::saveFile(
            "0-".$file->primaryKey,
            $budgetPath,
            'xls'
        );
        $save_file = __DIR__.'/../../../'.sprintf(
            'documents/zoho/%s.%s',
            $file->uuid,
            'xls'
        );
        $this->assertEquals(MyDocument::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'template_id' => $documentTemplate->primaryKey,
        ])->is_was_saved, 1);
        $this->assertFileExists($save_file);
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
