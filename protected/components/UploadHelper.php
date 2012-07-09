<?php


/**
 * Загрузчик файлов
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UploadHelper {
    
    /**
     * Осуществить загрузку файлов.
     */
    public static function upload($uploadDir = 'media/') {
        try {
            $result = 0;
            if (!is_dir('media')) mkdir ('media');

            $allowedExt = array('jpg', 'jpeg', 'png', 'gif');
            $maxFileSize = 10 * 1024 * 1024; //1 MB

            if (!isset($_FILES))  throw new Exception('нечего загружать');

            Logger::debug("files : ".var_export($_FILES, true));
            Logger::debug("post : ".var_export($_POST, true));
            Logger::debug("get : ".var_export($_GET, true));




            //проверяем размер и тип файла
            //$ext = end(explode('.', strtolower($_FILES['Filedata']['name'])));
            $path_info = pathinfo(strtolower($_FILES['Filedata']['name']));
            /*$ext = $path_info['extension'];
            if (!in_array($ext, $allowedExt)) {
                return;
            }*/

            if ($maxFileSize < $_FILES['Filedata']['size']) throw new Exception('wrong file size');
            
            if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
                $fileName = $uploadDir.$_FILES['Filedata']['name'];
                        //если файл с таким именем уже существует...
                if (file_exists($fileName)) {
                    //...добавляем текущее время к имени файла
                    $nameParts = explode('.', $_FILES['Filedata']['name']);
                    $nameParts[count($nameParts)-2] .= time();
                    $fileName = $uploadDir.implode('.', $nameParts);
                }
                $result = (int)move_uploaded_file($_FILES['Filedata']['tmp_name'], $fileName);
            }

            return "<script language=\"javascript\" type=\"text/javascript\">
                        window.top.window.scenario.stopUpload({$result});
                    </script>";
        } catch (Exception $exc) {
            return "<script language=\"javascript\" type=\"text/javascript\">
                        window.top.window.scenario.stopUpload(0);
                    </script>";
        }
    }
    
    public static function uploadSimple($uploadDir = 'media/') {
        if (!isset($_FILES)) return false;
        
        $fileName = false;
        foreach($_FILES as $key => $file) {
            $fileName = $uploadDir.$file['name'];
            if (!move_uploaded_file($file['tmp_name'], $fileName)) return false;
        }
        
        return $fileName;
    }
}

?>
