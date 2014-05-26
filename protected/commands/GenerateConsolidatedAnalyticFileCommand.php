<?php
/**
 * Команда генерирует аналитический файл по всем прохождениям реальных людей
 *
 * Ексель состоит из пяти листов:
 * - оценки верхнего уровня
 * - управленческие навыки подробно
 * - результативность подробно
 * - управление временем подробно
 * - баллы за поведения за игру
 *
 * и сохраняет в папку protected/system_data/analytic_files_2/*
 */
class GenerateConsolidatedAnalyticFileCommand extends CConsoleCommand
{
    public function actionIndex($assessment_version = 'v2')
    {
        $start_time = time();

        $results = SiteLogGenerateConsolidatedAnalyticFile::generate($assessment_version);

        echo "Calc ".(count($results['v1']) + count($results['v2']))." \r\n";
        echo "Done ".(count($results['v1']) + count($results['v2']))." \r\n";
        var_dump(date('H:i:s', $start_time));
        var_dump(date('H:i:s', time()));
    }
}