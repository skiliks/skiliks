(function(window, $) {
    'use strict';

    $.easing.easeOutElasticSoft = function(p) {
        return p === 0 || p === 1 ? p :	1 + Math.pow(2, -8 * p - 1) * Math.sin((-p * 80 - 7.5) * Math.PI / 15);
    };

    function Gauge(container, value, options) {
        var $chart = $('<div class="chart-gauge"/>'),
            $arrow = $('<span class="pointer"/>'),
            $value = $('<span class="chart-value"/>');

        this.options = options = options || {};
        this.el = {
            chart: $chart,
            arrow: $arrow,
            value: $value
        };

        $chart[0].chartObject = this;

        $chart.append($arrow)
            .append($value)
            .appendTo(container);

        if (options.class) {
            $chart.addClass(options.class);
        }

        this.setValue(value);
    }

    $.extend(Gauge.prototype, {
        pointerLength: 110,
        setValue: function(value) {
            var me = this,
                deg = value / 100 * 180,
                rad = deg * Math.PI / 180,
                left = (Math.cos(rad - Math.PI) + 1) * me.pointerLength * 1.03 - 4,
                bottom = Math.sin(rad) * me.pointerLength * 0.95 + 1;

            me.value = value;
            me.el.value.text('');

            me.el.arrow.animate({textIndent: deg}, {
                easing: 'easeOutElasticSoft',
                duration: me.options.duration || 2000,
                step: function(now) {
                    $(this).css('transform', 'rotate(' + now + 'deg)');
                },
                complete: function() {
                    me.el.value.text(value + '%').css({
                        left: left + 'px',
                        bottom: bottom + 'px'
                    });
                }
            });
        },
        refresh: function() {
            var v = this.value;

            this.setValue(0);
            this.el.arrow.finish();

            this.setValue(v);
        }
    });

    function Bar(container, value, options) {
        var me = this,
            $chart = $('<div class="chart-bar"/>'),
            $value = $('<span class="chart-value"/>');

        this.options = options = options || {};
        this.el = {
            chart: $chart,
            value: $value
        };

        $chart[0].chartObject = this;

        if (options.class) {
            $chart.addClass(options.class);
        }

        $chart.append($value)
            .css('width', 0)
            .appendTo(container)
            .animate({width: /*$(container).width()*/'100%'}, 1000, function() {
                me.setValue(value);
            });
    }

    $.extend(Bar.prototype, {
        setValue: function(value) {
            var me = this;

            me.value = value;

            me.el.value.html('&nbsp;')
                .animate({width: value + '%'}, {
                    easing: 'easeOutSine',
                    duration: me.options.duration || 2000,
                    complete: function() {
                        $(this).html('&nbsp; ' + value + '%');
                    }
                });
        },
        refresh: function() {
            var v = this.value;

            this.setValue(0);
            this.el.value.finish();

            this.setValue(v);
        }
    });

    function Bullet(container, value, options) {
        var me = this,
            $chart = $('<div class="chart-bullet"/>'),
            $bullet = $('<div class="bullet"/>'),
            $bar = $('<div class="bar"/>'),
            $value = $('<span class="chart-value"/>');

        this.options = options = options || {};
        this.el = {
            chart: $chart,
            bullet: $bullet,
            bar: $bar,
            value: $value
        };

        $chart[0].chartObject = this;

        if (options.class) {
            $chart.addClass(options.class);
        }

        if (options.displayValue) {
            $chart.addClass('valued');
        }

        $chart.append($bullet)
            .append($bar)
            .append($value)
            .appendTo(container);

        if (options.class) {
            $chart.addClass(options.class);
        }

        this.setValue(value);
    }

    $.extend(Bullet.prototype, {
        setValue: function(value) {
            var me = this,
                width = me.el.chart.width(),
                left = value / 100 * width + 20 * -Math.sin(value / 100 * Math.PI / 2) - 5;

            me.value = value;

            me.el.value.hide();
            me.el.bullet.animate({left: left + 'px'}, {
                easing: 'easeOutQuint',
                duration: me.options.duration || 2000
            });
            me.el.bar.animate({width: value + '%'}, {
                easing: 'easeOutQuint',
                duration: me.options.duration || 2000,
                complete: function() {
                    me.el.value.show().css('left', left + 'px').text(value + '%');
                }
            });
        },
        refresh: function() {
            var v = this.value;

            this.setValue(0);
            this.el.bullet.finish();
            this.el.bar.finish();

            this.setValue(v);
        }
    });

    window.charts = {
        Gauge: Gauge,
        Bar: Bar,
        Bullet: Bullet
    };

    return window.charts;
})(window, jQuery);