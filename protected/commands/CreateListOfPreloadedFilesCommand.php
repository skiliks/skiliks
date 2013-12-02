<?php

class CreateListOfPreloadedFilesCommand extends CConsoleCommand {

    public $javascript_preload = '';

    public function actionIndex()
    {
        $this->javascript_preload  = "<script type='text/javascript'>\r\n";
        $this->javascript_preload .= "var preLoadImages = [";
        $preloadedImagesArray = [];

        $assets = __DIR__.'/../assets/';
        $cache = array_merge(
            $cache = Yii::app()->params['imagesForPreLoad'],
            [
                // в CSS критически важен порядок следования файлов
                // этотму мы прописываем все пути впроть до файла
                'js/jquery/jquery-ui.css',
                'js/bootstrap/css/bootstrap.css',
                'js/jquery/jquery-ui-1.8.23.slider.css',
                'js/jquery/jquery.mCustomScrollbar.css',
                'js/elfinder-2.0-rc1/css/elfinder.min.css',
                'js/elfinder-2.0-rc1/css/theme.css',
                'css/tag-handler.css',
                'css/ddSlick.css',
                'css/main.css',

                'compiled_css/simulation.css',
                'compiled_css/manual.css',
                'compiled_css/plan.css',
                'compiled_css/mail.css',
                'compiled_css/documents.css',
            ]
        );

        foreach ($cache as $path) {
            echo $assets.$path;
            if (file_exists($assets.$path) && is_file($assets.$path)) {
                echo ' - OK!';
                $preloadedImagesArray[] = sprintf("\r\n '<?= \$assetsUrl ?>/%s' ", $path);
            } elseif (is_dir($assets.$path)) {
                $files = scandir($assets.$path);
                $count = 0;
                foreach ($files as $file) {
                    if (file_exists($assets.$path.'/'.$file) && is_file($assets.$path.'/'.$file)) {
                        $count++;
                        $preloadedImagesArray[] = sprintf("\r\n '<?= \$assetsUrl ?>/%s/%s' ", $path, $file);
                    }
                }
                echo sprintf(' (%s) - OK!', $count);
            } else {
                echo ' - NOT FOUND!';
            }
            echo "\r\n";
        }
        // добавляем пустой элемент, так как в JS массив не может заканчиватсья запятой
        $this->javascript_preload .= implode(',', $preloadedImagesArray);
        $this->javascript_preload .= "];\r\n</script>";

        file_put_contents(__DIR__.'/../views/static/applicationcache/preload_images.php', $this->javascript_preload);

    }

}