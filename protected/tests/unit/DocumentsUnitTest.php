<?php

class DocumentsUnitTest extends PHPUnit_Framework_TestCase {
    public function testGetExcel(){
        $file = new MyDocument();
        $file->setPrimaryKey(2);
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $file->template = $scenario->getDocumentTemplate(['code' => 'D1']);
        $this->assertStringEndsWith('.sc' , $file->template->srcFile);
        $this->assertEquals($file->getSheetList()[0]['name'], 'Сводный');
        $file->setSheetContent('Сводный', 'жопа');
    }
}
