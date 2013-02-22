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
    public function testCanOpenDocument()
    {
        // $this->markTestSkipped();

        // init simulation
        $simulation_service = new SimulationService();
        $user = Users::model()->findByAttributes(['email' => 'asd']);
        $simulation = $simulation_service->simulationStart(Simulations::TYPE_PROMOTION, $user);
        $messages = array_values(MailBoxService::getMessages(array(
            'folderId'   => 1,
            'order'      => 'name',
            'orderType'  => 'ASC',
            'simId'      => $simulation->id
        )));
        $tmp_messages = array_filter($messages, function ($item) {return $item['subject'] === 'По ценовой политике';});
        $attachment_id = $tmp_messages[0]['attachmentFileId'];
        $file = MyDocumentsModel::model()->findByPk($attachment_id);
        $this->assertEquals(MyDocumentsService::makeDocumentVisibleInSimulation($simulation, $file), true);
        $name = $tmp_messages[0]['attachmentName'];
        $this->assertCount(1,array_filter(
            MyDocumentsService::getDocumentsList($simulation),
            function ($doc) use ($name) {
                return $doc['name'] === $name;
            }));
    }
}
