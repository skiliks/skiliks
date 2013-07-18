<?php
class ApplicationCacheController extends SiteBaseController{

    public function actionManifest() {

        $assets = $this->getAssetsUrl();
        $this->layout = false;
        $this->render('//static/applicationcache/manifest', ['assets'=>$assets]);

    }

    public function actionPageForCache() {

        $this->layout = false;
        $this->render('//static/applicationcache/page_for_cache');

    }

}