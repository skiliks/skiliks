<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 29.01.13
 * Time: 22:58
 * To change this template use File | Settings | File Templates.
 */
class ImportTest extends CDbTestCase
{
    public function test_Full_Import()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $import = new ImportGameDataService();
            $import->importCharacters();
            $import->importCharactersPointsTitles();
            $import->importLearningGoals();
            $import->importDialogReplicas();
            $this->assertEquals(Dialogs::model()->count(), 821);
            $this->assertNotNull(Dialogs::model()->findByAttributes(['code' => 'S12.3']));
            $import->importEmailSubjects();
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['character' => null]), 100);
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['phone' => 1]), 67);
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['mail' => 1]), 112);
            $import->importEmails();
            $import->importMailTasks();
            $import->importEventSamples();
            $import->importTasks();
            $import->importMyDocuments();
            $import->importActivity();
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['text' => '!проблема с сервером!']),4);
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
        }
    }
}
