<script>

    // 1) отображение подсказок на "?" в разделе "Управленческие навыки"
    function initHistToggle() {
        $('.action-toggle-learning-goal-description-hint').each(function(){
            if (false == $(this).hasClass('initiated')) {
                $(this).click(function() {
                    hideAllPopovers();

                    // подскаска с описание цели обучения
                    var hint = $(this).parent().find('.locator-learning-goal-description-hint');
                    if (hint.hasClass('hide')) {
                        hint.removeClass('hide');
                    } else {
                        hint.addClass('hide');
                    }
                });
                $(this).addClass('initiated');
            }
        });
    }

    (function ($) {
        console.log('V1');

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

        // 3) инициализация подсказок при переключении вкладок попапа результатов симуляции
        $('.simulation-details a').click(function() {
            initHistToggle();
        })
    })(jQuery);
</script>