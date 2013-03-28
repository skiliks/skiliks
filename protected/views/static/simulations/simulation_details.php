
<h1><?php echo $simulation->user->profile->firstname ?> <?php echo $simulation->user->profile->lastname ?></h1>

<div id="simulation-details-tabs">
    <ul id="simulation-details">
        <li><a href="#tab-main"><?php echo Yii::t('site', 'Main') ?></a></li>
        <li><a href="#tab-productivity"><?php echo Yii::t('site', 'Productivity') ?></a></li>
        <li><a href="#tab-time-management"><?php echo Yii::t('site', 'Time management') ?></a></li>
        <li><a href="#tab-managerial-skills"><?php echo Yii::t('site', 'Managerial skills') ?></a></li>
        <li><a href="#tab-personal-qualities"><?php echo Yii::t('site', 'Personal qualities') ?></a></li>
    </ul>

    <div id="tab-main">
        <?php $this->renderPartial('_tab_main', ['simulation'=>$simulation]) ?>
    </div>

    <div id="tab-productivity">
        <?php $this->renderPartial('_tab_productivity', []) ?>
    </div>

    <div id="tab-time-management">
        <?php $this->renderPartial('_tab_time_management', []) ?>
    </div>

    <div id="tab-managerial-skills">
        <?php $this->renderPartial('_tab_managerial_skills', []) ?>
    </div>

    <div id="tab-personal-qualities">
        <?php $this->renderPartial('_tab_personal_skills', []) ?>
    </div>
</div>

<script>
    $(function() {
        $( "#simulation-details-tabs" ).tabs();
    });
</script>

