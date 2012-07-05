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
        if (!is_dir('media')) mkdir ('media');

        $allowedExt = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 10 * 1024 * 1024; //1 MB

        if (!isset($_FILES)) {
            return false;  // нечего загружать
        }
        
        //проверяем размер и тип файла
        //$ext = end(explode('.', strtolower($_FILES['Filedata']['name'])));
        $path_info = pathinfo(strtolower($_FILES['Filedata']['name']));
        /*$ext = $path_info['extension'];
        if (!in_array($ext, $allowedExt)) {
            return;
        }*/
        
        if ($maxFileSize < $_FILES['Filedata']['size']) {
            return;
        }
        if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
            $fileName = $uploadDir.$_FILES['Filedata']['name'];
                    //если файл с таким именем уже существует...
            if (file_exists($fileName)) {
                //...добавляем текущее время к имени файла
                $nameParts = explode('.', $_FILES['Filedata']['name']);
                $nameParts[count($nameParts)-2] .= time();
                $fileName = $uploadDir.implode('.', $nameParts);
            }
            move_uploaded_file($_FILES['Filedata']['tmp_name'], $fileName);
            //echo '<img src="'.$fileName.'" alt="'.$fileName.'" />';
            return $fileName;
        }
        
    }
}

?>
