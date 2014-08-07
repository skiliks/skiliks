<!--product-->
<section class="pull-content-center">
    <h1 class="pull-content-center">
        <?= Yii::t('site', 'Articles')?>
    </h1>

    <br/>

    <span class="column-full column-articles">
        <?php foreach ($data as $article): ?>
            <article>
                <img class="publication-image pull-left" src="<?= $article['img-src'] ?>">
                <div class="article-data pull-right pull-content-left">
                    <div class="publication-date color-ffffff"><?= $article['date'] ?></div>
                    <h2><a href="<?= $article['link'] ?>"><?= $article['title'] ?></a></h2>
                    <div class="publication-description"><?= $article['description'] ?></div>
                    <a class="source-link" href="<?= $article['link'] ?>"><?= $article['label'] ?></a>
                </div>
            </article>

            <?php if (false == $article['isLast']): ?>
                <span class="publication-separator"></span>
            <?php endif ?>
        <?php endforeach ?>
    </span>
</section>
<!--product end-->

<div class="clearfix"></div>