<?php

class DocumentsUnitTest extends PHPUnit_Framework_TestCase {
    public function testGetExcel(){
        $file = new MyDocument();
        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_FULL]);
        $file->template = $scenario->getDocumentTemplate(['code' => 'D1']);
        $this->assertStringEndsWith('.sc' , $file->template->srcFile);
        print_r($file->getContents());
    }
}
