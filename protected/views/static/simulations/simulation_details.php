<?php /** @var Simulation $simulation */ ?>
<style>
    .sections > div {
        display: none;
    }
    .sections > div.active {
        display: block;
    }

    .prev {
        float: left;
    }

    .next {
        float: right;
    }
</style>

<h1><?php echo $simulation->user->profile->firstname ?> <?php echo $simulation->user->profile->lastname ?></h1>

<div class="simulation-details">
    <script type="text/javascript">
        var assessmentResult = <?= json_encode($simulation->getAssessmentDetails()); ?>;
    </script>

    <ul class="navigation">
        <li><a href="#main"><?php echo Yii::t('site', 'Main') ?></a></li>
        <li><a href="#managerial-skills"><?php echo Yii::t('site', 'Managerial skills') ?></a></li>
        <li><a href="#productivity"><?php echo Yii::t('site', 'Productivity') ?></a></li>
        <li><a href="#time-management"><?php echo Yii::t('site', 'Time management') ?></a></li>
        <li><a href="#personal-qualities"><?php echo Yii::t('site', 'Personal qualities') ?></a></li>
    </ul>

    <div class="sections">
        <div id="main">
            <?php $this->renderPartial('partials/tab_main', ['simulation' => $simulation]) ?>
        </div>

        <div id="managerial-skills">
            <?php $this->renderPartial('partials/tab_managerial_skills', []) ?>
        </div>

        <div id="productivity">
            <?php $this->renderPartial('partials/tab_productivity', ['simulation' => $simulation]) ?>
        </div>

        <div id="time-management">
            <?php $this->renderPartial('partials/tab_time_management', []) ?>
        </div>

        <div id="personal-qualities">
            <?php $this->renderPartial('partials/tab_personal_skills', ['simulation' => $simulation, 'learning_areas'=>$learning_areas]) ?>
        </div>
    </div>

    <div class="footer">
        <a class="prev" href="#prev"><?php echo Yii::t('site', 'Previous') ?></a>
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
                    me.prev();
                } else if ($(this).is(o.nextSelector)) {
                    me.next();
                } else {
                    history.pushState({id: id}, '', o.changeHash ? this.href : null);
                    me.open(id);
                }

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
            var o = this.options;

            if (this.idList.indexOf(id) > -1) {
                this.$sections
                    .removeClass(o.activeClass)
                    .filter('#' + id)
                    .addClass(o.activeClass)
                    .trigger('open');

                this.$links
                    .removeClass(o.activeClass)
                    .filter('[href=#' + id + ']')
                    .addClass(o.activeClass);

                this.current = id;
            }
        },

        prev: function() {
            var index = (this.idList.indexOf(this.current) || this.idList.length) - 1;
            this.open(this.idList[index]);
        },

        next: function() {
            var index = this.idList.indexOf(this.current) + 1;
            if (index == this.idList.length) {
                index = 0;
            }
            this.open(this.idList[index]);
        }
    });

    $(function() {
        var nav = new DetailsNavigator('.simulation-details', {
            sectionSelector: '.sections > div'
        });

        nav.$sections.on('open', function() {
            $(this).find('.chart-gauge, .chart-bar, .chart-bullet').each(function() {
                this.chartObject.refresh();
            });
        });
    });
</script>

