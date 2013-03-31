(function(window, $) {
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

        $chart.append($arrow)
            .append($value)
            .appendTo(container);

        if (options.class) {
            $chart.addClass(options.class);
        }

        this.setValue(value);
    }

    function Bar(container, value, options) {
        var me = this,
            $chart = $('<div class="chart-bar"/>'),
            $value = $('<span class="chart-value"/>');

        this.options = options = options || {};
        this.el = {
            chart: $chart,
            value: $value
        };

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

    function Bullet(container, value, options) {
        var me = this,
            $chart = $('<div class="chart-bullet"/>'),
            $bullet = $('<div class="bullet"/>'),
            $value = $('<span class="chart-value"/>');

        this.options = options = options || {};
        this.el = {
            chart: $chart,
            bullet: $bullet,
            value: $value
        };

        if (options.class) {
            $chart.addClass(options.class);
        }

        $chart.append($bullet)
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
                left = (Math.cos(rad - Math.PI) + 1) * me.pointerLength * 1.27 + 5,
                bottom = Math.sin(rad) * me.pointerLength + 10;

            me.value = value;

            me.el.value.text(value + '%').css({
                left: left + 'px',
                bottom: bottom + 'px'
            });

            me.el.arrow.animate({textIndent: deg}, {
                easing: 'easeOutElasticSoft',
                duration: me.options.duration || 2000,
                step: function(now) {
                    $(this).css('transform', 'rotate(' + now + 'deg)');
                }
            });
        },
        refresh: function() {
            this.setValue(this.value);
        }
    });

    $.extend(Bar.prototype, {
        setValue: function(value) {
            var me = this;

            me.value = value;

            me.el.value
                .html('&nbsp; ' + value + '%')
                .animate({width: value + '%'}, {
                    easing: 'easeOutSine',
                    duration: me.options.duration || 2000
                });
        },
        refresh: function() {
            this.setValue(this.value);
        }
    });

    $.extend(Bullet.prototype, {
        setValue: function(value) {
            var me = this,
                width = me.el.chart.width(),
                left = value / 100 * width - 17;

            me.value = value;

            me.el.bullet.animate({left: left + 'px'}, {
                easing: 'easeOutQuint',
                duration: me.options.duration || 2000
            });
            me.el.value.animate({width: value + '%'}, {
                easing: 'easeOutQuint',
                duration: me.options.duration || 2000
            });
        },
        refresh: function() {
            this.setValue(this.value);
        }
    });

    window.charts = {
        Gauge: Gauge,
        Bar: Bar,
        Bullet: Bullet
    };

    return window.charts;
})(window, jQuery);