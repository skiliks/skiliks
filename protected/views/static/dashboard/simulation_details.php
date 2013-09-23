<?php /** @var Simulation $simulation */ /* @var YumUser $user */ /* @var Invite $invite */  ?>
<?php if($user->isPersonal()) { ?>
    <h1><?php echo $user->profile->firstname ?> <?php echo $user->profile->lastname ?></h1>
<?php } elseif(null === $simulation->invite) { ?>
    <?php // это хак для просмотра результатов lite симуляций,
          //в случае одновременного запуска нескольких lite симуляций по одному и томе же инвайту  ?>
    <h1><?php echo $user->profile->firstname ?> <?php echo $user->profile->lastname ?></h1>
<?php } else{ ?>
    <h1><?php echo $simulation->invite->firstname ?> <?php echo $simulation->invite->lastname ?></h1>
<?php } ?>
<div class="simulation-details scenario-<?= $simulation->game_type->slug ?>">
    <script type="text/javascript">
        var AR = <?= $details; ?>;

        function drawChartBlock(classPrefix, data, codes) {
            var i, k;
            for (k = 1; k <= 2 * codes.length; k++) {
                i = Math.ceil(k / 2);
                new charts.Bar(
                    '.' + classPrefix + '-' + i + ' .' + (k % 2 ? 'chartbar' : 'chartproblem'),
                    Math.round(data && data[codes[i - 1]] ? data[codes[i - 1]][k % 2 ? '+' : '-'] : 0),
                    { valueRenderer: function(v) { return v + '%';}, 'class': (k % 2 ? '' : 'redbar') }
                );
            }
        }
    </script>
    <div class="navigatnwrap scenario-<?= $simulation->game_type->slug ?>-box">
        <ul class="navigation">
            <li><a href="#main"><?php echo Yii::t('site', 'Main') ?></a></li>
            <li><a href="#managerial-skills"><?php echo Yii::t('site', 'Managerial skills') ?></a></li>
            <li><a href="#productivity"><?php echo Yii::t('site', 'Productivity') ?></a></li>
            <li><a href="#time-management"><?php echo Yii::t('site', 'Time management') ?></a></li>
            <?php /* not in release 1.2
                <li><a href="#personal-qualities"><?php echo Yii::t('site', 'Personal qualities') ?></a></li>
            */ ?>
        </ul>
    </div>

    <div class="sections">
        <div id="main">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_main', [
                'data' => json_decode($details, true)['additional_data']
            ]) ?>
        </div>

        <div id="managerial-skills">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills', []) ?>
        </div>

        <div id="managerial-skills-1-2">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills_1_2', []) ?>
        </div>

        <div id="managerial-skills-3-4">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_managerial_skills_3_4', []) ?>
        </div>

        <div id="productivity">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_productivity') ?>
        </div>

        <div id="time-management">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_time_management', []) ?>
        </div>

        <div id="time-management-detail">
            <?php $this->renderPartial($simulation->results_popup_partials_path.'/tab_time_management_detail', []) ?>
        </div>
    </div>

    <div class="estmfooter">
        <a class="prev" href="#prev"><?php echo Yii::t('site', 'Back') ?></a>
        <a class="next" href="#next"><?php echo Yii::t('site', 'Next') ?></a>
    </div>
</div>

<script>
    function DetailsNavigator(container, options) {
        var o = this.options = $.extend(this.options, options || {});

        this.$container = $(container);
        this.$sections = this.$container.find(o.sectionSelector);
        this.$links = this.$container.find(o.linkSelector);
        this.idList = $.map($.makeArray(this.$sections), function(section) {
            return section.id
        });

        this.init();
    }

    $.extend(DetailsNavigator.prototype, {
        options: {
            sectionSelector: '.section',
            linkSelector: 'a[href^=#]',
            prevSelector: '.prev',
            nextSelector: '.next',

            activeClass: 'active',
            changeHash: true
        },

        init: function() {
            var me = this,
                o = me.options;

            this.$links.on('click.Navigator', function(e) {
                var id = $(this).attr('href').slice(1);

                if ($(this).is(o.prevSelector)) {
                    id = me.prev();
                } else if ($(this).is(o.nextSelector)) {
                    id = me.next();
                } else {
                    me.open(id);
                }

                history.pushState({id: id}, '', o.changeHash ? '#' + id : null);
                e.preventDefault();
            });

            $(window).on('popstate.Navigator', function(e) {
                var state = e.originalEvent.state,
                    id = state ? state.id : me.idList[0];

                if (id) {
                    me.open(id);
                }
            });

            if (me.idList.length) {
                me.open(me.idList[0]);
            }
        },

        destroy: function() {
            $(this.$container).find('a').off('.Navigator');
            $(window).off('.Navigator');
        },

        open: function(id) {
            var o = this.options,
                link;

            if (this.idList.indexOf(id) > -1 && this.current !== id) {
                this.$sections
                    .removeClass(o.activeClass)
                    .filter('#' + id)
                    .addClass(o.activeClass)
                    .trigger('open');

                link = this.$links
                    .removeClass(o.activeClass)
                    .filter('[href=#' + id + ']')
                    .addClass(o.activeClass);

                if (link.attr('data-parent')) {
                    this.$links
                        .filter('[href=#' + link.attr('data-parent') + ']')
                        .addClass(o.activeClass);
                }

                this.current = id;
            }
        },

        prev: function() {
            var index = (this.idList.indexOf(this.current) || this.idList.length) - 1;

            this.open(this.idList[index]);
            return this.idList[index];
        },

        next: function() {
            var index = this.idList.indexOf(this.current) + 1;
            if (index == this.idList.length) {
                index = 0;
            }

            this.open(this.idList[index]);
            return this.idList[index];
        }
    });

    $(function() {
        var nav = new DetailsNavigator('.simulation-details', {
            sectionSelector: '.sections > div'
        });

        nav.$sections.on('open', function() {
            $(this).find('.chart-gauge, .chart-bar, .chart-bullet, .chart-pie').each(function() {
                this.chartObject.refresh();
            });
        });
    });
</script>

