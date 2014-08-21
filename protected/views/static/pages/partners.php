<!--product-->
<section class="pull-content-center">
    <h1 class="pull-content-center">
        Партнёры
    </h1>

    <br/>

    <span class="column-full column-articles">
        <?php foreach ($data as $partner): ?>
            <article>
                <img class="publication-image pull-left" src="<?= $partner['img-src'] ?>">
                <div class="article-data pull-right pull-content-left">
                    <br/>
                    <h2 class="partners"><?= $partner['title'] ?></h2>
                    <div class="publication-description"><?= $partner['description'] ?></div>
                    <br/>
                </div>
            </article>

            <?php if (false == $partner['isLast']): ?>
                <span class="publication-separator"></span>
            <?php endif ?>
        <?php endforeach ?>
    </span>
</section>
<!--product end-->

<div class="clearfix"></div>