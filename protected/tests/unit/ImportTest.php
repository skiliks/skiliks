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

    public function test_2_importLearningGoals() {
        $import = new ImportGameDataService();
        $import->importLearningGoals();
    }

    public function test_2_DialogImport() {
        $import = new ImportGameDataService();
        $import->importDialogReplicas();
        $this->assertEquals(Dialogs::model()->count(), 800);
        $this->assertNotNull(Dialogs::model()->findByAttributes(['code' => 'S12.3']));
    }

    public function test_3_ActivityImport() {
        $import = new ImportGameDataService();
        $import->importActivity();
    }
}
