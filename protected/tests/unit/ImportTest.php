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
    public function test_1_CharacterImport() {
        $import = new ImportGameDataService();
        $import->importCharacters();
    }

    public function test_1_1_0_CharacterPoints() {
        $import = new ImportGameDataService();
        $import->importCharactersPointsTitles();
    }

    public function test_2_importLearningGoals() {
        $import = new ImportGameDataService();
        $import->importLearningGoals();
    }

    public function test_3_DialogImport() {
        $import = new ImportGameDataService();
        $import->importDialogReplicas();

        $this->assertEquals(Dialogs::model()->count(), 821);
        $this->assertNotNull(Dialogs::model()->findByAttributes(['code' => 'S12.3']));
    }

    public function test_4_0_1_EmailSubject() {
        $import = new ImportGameDataService();
        $import->importEmailSubjects();
        $this->assertEquals(CommunicationTheme::model()->countByAttributes(['phone' => 1]), 67);
        $this->assertEquals(CommunicationTheme::model()->countByAttributes(['mail' => 1]), 112);

    }

    public function test_4_1_Emails() {
        $import = new ImportGameDataService();
        $import->importEmails();
    }

    public function test_5_EventsSamples() {
        $import = new ImportGameDataService();
        $import->importEventSamples();
    }

    public function test_6_ActivityImport() {
        $import = new ImportGameDataService();
        $import->importActivity();
    }
}
