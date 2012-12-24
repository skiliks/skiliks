<?php


/**
 * Контроллер загрузки сценария
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ScenarioController extends AjaxController{
 
    /**
     * Загрузка сценария
     * @return type 
     */
    public function actionUpload() 
    {
        try {
            $fileName = UploadHelper::uploadSimple(); // загружаем файл

            if (!$fileName) throw new Exception ('Не могу загрузить файл');

            // импорт файла {
            $service = new DialogImportService();
            $results = $service->import($fileName);
            // импорт файла }

            // success message
            $html = sprintf(
                '<script language="javascript" type="text/javascript">
                    alert("Импорт завершен. Обработано событий %s (новых: %s, существующих: %s). Обработано реплик %s. Обработано типов оценок %s. Добавлено оценок \"1\" %s, \"0\" %s. ");                    
                </script>',
                $results['events'],
                $results['events-new'],
                $results['events-updated'],
                $results['replics'],
                $results['pointCodes'],
                $results['ones'],
                $results['zeros']
            );
        } catch (Exception $exc) {
            $html = sprintf(
                '<script language="javascript" type="text/javascript">
                    alert("Ошибка: %s.");                    
                </script>',
                str_replace(array("'", '"', '`', "\n"), array('','','', ' '), $exc->getMessage())
            );
        }   
        
        return $this->_sendResponse(200, $html, 'text/html');
    }
}
