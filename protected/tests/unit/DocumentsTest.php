<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 22.02.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */
class DocumentsTest extends CDbTestCase
{
    /**
     * Checks if user can open attachment from e-mail
     */
    public function testCanOpenDocument()
    {
        // $this->markTestSkipped();

        // init simulation
        $user = YumUser::model()->findByAttributes(['username' => 'asd']);
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);

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
        $simulation = SimulationService::simulationStart(Simulation::MODE_PROMO_ID, $user);
        $documentTemplate = DocumentTemplate::model()->findByAttributes(['code' => 'D1']);
        $file = MyDocument::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'template_id' => $documentTemplate->primaryKey
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
        $this->assertEquals($zoho->getUrl(), 'https://sheet.zoho.com/editor.do?doc=c2826da1f9894a54366f67ddf2326ff00c1ce3234acde876');
    }

}
