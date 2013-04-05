<?php

class SyncDocCommand extends CConsoleCommand{

    public function init() {
        ini_set('memory_limit', '900M');
    }

    public function actionIndex()
    {


        $this->CPrint("Init Documents");

        $src_templates = Yii::app()->getBasePath()."/../documents/src_templates/";
        $srcTemplateHandle = opendir($src_templates);
        $templates = Yii::app()->getBasePath()."/../documents/templates/";
        while ($doc = readdir($srcTemplateHandle)) {
            if (is_dir($doc)) {
                continue;
            }
            if(file_exists($src_templates.$doc)) {
                $this->CPrint("Найден шаблон {$doc}");
                $dstFile = StringTools::CyToEn($doc);
                $dstFile = str_replace(' ', '_', $dstFile);
                $dstFile = str_replace('.docx', '.pdf', $dstFile);
                $dstFile = str_replace('.pptx', '.pdf', $dstFile);

                copy($src_templates.$doc, $templates . $dstFile);
            }else{
                $this->CPrint("Не найден шаблон {$doc}");
            }
        }


    }

    public function CPrint($string){
        echo "\n{$string}\n";
    }

}
