<?php
/**
 * Class UpdateScDocsCache
 */
class UpdateScDocsCacheCommand extends CConsoleCommand {

    public function actionIndex()
    {
        echo "Начинаем кеширование: \n";

        $templates = DocumentTemplate::model()->findAll();
        foreach ($templates as $template) {
            if (in_array($template->format, ['xls', 'xlsx'])) {
                echo "{$template->fileName} \n ({$template->getCacheFilePath()}): \n";

                if (file_exists($template->getCacheFilePath())) {
                    unlink($template->getCacheFilePath());
                }

                echo "удалён, ";

                $scData = ScXlsConverter::xls2sc($template->getFilePath());

                file_put_contents($template->getCacheFilePath(), json_encode($scData));
                echo "cache обновлён. \n\n";
            }
        }

        echo "Готово! \n";
    }
}