<?php
/**
 * Class UpdateScDocsCache
 */
class ConvertSerializedScDocsToJsonCommand extends CConsoleCommand {

    public function actionIndex()
    {
        echo "Начинаем конвертирование: \n";

        $docs = MyDocument::model()->findAll();
        foreach ($docs as $doc) {
            echo ".";
            if (in_array($doc->template->format, ['xls', 'xlsx'])) {
                if (false == is_file($doc->getFilePath())) {
                    continue;
                }

                @$scData = unserialize(file_get_contents($doc->getFilePath()));

                if (empty($scData)) {
                    continue;
                }

                echo "{$doc->getFilePath()} \n";

                file_put_contents($doc->getFilePath(), json_encode($scData));
            }
        }

        echo "Готово! \n";
    }
}