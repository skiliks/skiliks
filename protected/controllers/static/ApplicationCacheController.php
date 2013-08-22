<?php
class ApplicationCacheController extends SiteBaseController{

    public function actionManifest() {

        $main = __DIR__.'/../../config/main.php';
        header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime($main)) . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Content-type: text/cache-manifest");
        $assets = $this->getAssetsUrl();
        $this->layout = false;
        $this->render('//static/applicationcache/manifest', ['assets'=>$assets]);

    }

    public function actionPageForCache() {

        $this->layout = false;
        $this->render('//static/applicationcache/page_for_cache');

    }

}