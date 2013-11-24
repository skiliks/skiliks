<?php

class CreateCacheManifestCommand extends CConsoleCommand {

    public $str_files = '';

    public function actionIndex()
    {
        $this->str_files = "CACHE MANIFEST\r\n";
        $this->str_files .= "<?php\r\n";//"<?php\r\n echo <<<MANIFEST\r\n";

        $assets = __DIR__.'/../assets/';
        $cache = [
            'img/documents',
            'img/interface',
            'img/mail',
            'img/main-screen',
            'img/phone',
            'img/planner',
            'img/tag-handler',
            'img/visitor',
            'img/papka-small.png',
            'img/pause.png',
            'img/phone-small.png',
            'img/plan-small.png',
            'img/pochta-small.png',
            'img/workplace-small.png'
        ];

        foreach ($cache as $path) {
            echo $assets.$path."\r\n";
            if(file_exists($assets.$path) && is_file($assets.$path)){
                $this->str_files .= 'echo $assets."/'.$path."\\r\\n\"; \r\n";
            } elseif (is_dir($assets.$path)) {
                $files = scandir($assets.$path);
                foreach ($files as $file) {
                    if (file_exists($assets.$path.'/'.$file) && is_file($assets.$path.'/'.$file)) {
                        $this->str_files .= 'echo $assets."/'.$path.'/'.$file."\\r\\n\"; \r\n";
                    }
                }
            }
        }

        $this->str_files .= "?>\r\n";
        $this->str_files .= "NETWORK:\r\n";
        $this->str_files .= "*\r\n";

        file_put_contents(__DIR__.'/../views/static/applicationcache/manifest.php', $this->str_files);

    }

}