<?php

class SyncDocCommand extends CConsoleCommand{

    public function init() {
        ini_set('memory_limit', '900M');
    }

    public function actionIndex()
    {


        $this->CPrint("Init Documents");
        $my_docs = DocumentTemplate::model()->findAllByAttributes(['format'=>'xlsx']);

        $src_templates = Yii::app()->getBasePath()."/../documents/src_templates/";
        $templates = Yii::app()->getBasePath()."/../documents/templates/";
        /* @var DocumentTemplate $doc */
        foreach($my_docs as $doc) {
            if(file_exists($src_templates.$doc->fileName)) {
                $this->CPrint("Найден шаблон {$doc->fileName}");
                copy($src_templates.$doc->fileName, $templates.$doc->srcFile);
            }else{
                $this->CPrint("Не найден шаблон {$doc->fileName}");
            }
        }


    }

    public function CPrint($string){
        echo "\n{$string}\n";
    }

}