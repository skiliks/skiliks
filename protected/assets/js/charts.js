(function(window, $, d3) {
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
        this.maxValue = options.max || 100;
        this.el = {
            chart: $chart,
            value: $value
        };

        $chart[0].chartObject = this;

        if (options.class) {
            $chart.addClass(options.class);
        }
        if (options.hideMax === true) {
            $chart.addClass('max-hidden');
        }

        $chart.append($value)
            .css('width', 0)
            .appendTo(container);

        this.setValue(value);
    }

    $.extend(Bar.prototype, {
        setValue: function(value) {
            var me = this;

            me.value = value;

            me.el.value.html('&nbsp;')
                .animate({width: value / me.maxValue * 100 + '%'}, {
                    easing: 'easeOutSine',
                    duration: me.options.duration || 2000,
                    complete: function() {
                        $(this).html('&nbsp; ' + (me.options.valueRenderer ? me.options.valueRenderer(value) : value));
                    }
                });
        },
        refresh: function() {
            var me = this,
                v = this.value;

            this.setValue(0);
            this.el.value.finish();

            if (me.options.hideMax !== true) {
                me.el.chart
                    .css('width', 0)
                    .animate({width: '100%'}, 500, function() {
                        me.setValue(v);
                    });
            } else {
                me.el.chart.css('width', '100%');
                me.setValue(v);
            }
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

    function Pie(container, values, options) {
        var me = this,
            $chart = $('<div class="chart-pie"/>'),
            chart, arcs, radius, colorScale, drawer, translate, centerCircle, sum;

        this.options = options = options || {};

        $chart.appendTo(container);

        radius = me.dimension / 2 - 40;
        sum = d3.sum(values);
        colorScale = options.colors ? d3.scale.ordinal().range(options.colors) : d3.scale.category20();
        translate = 'translate(' + me.dimension / 2 + ', ' + me.dimension / 2 + ')';
        this.total = options.total || sum;

        this.arcDrawer = d3.svg.arc()
            .innerRadius(options.donut ? radius * 0.75 : 0)
            .outerRadius(radius);

        this.pie = d3.layout.pie()
            .sort(null)
            .value(function(d) { return d; });

        this.chart = chart = d3.select($chart[0])
            .append('svg')
            .data([new Array(values.length)])
            .attr('width', me.dimension)
            .attr('height', me.dimension);

        if (options.donut) {
            drawer = d3.svg.arc()
                .innerRadius(0)
                .outerRadius(radius);

            centerCircle = chart.append('g').attr('transform', translate);

            centerCircle.append('path')
                .data(this.pie([sum]))
                .attr('d', drawer)
                .style('fill', options.bgColor || '#f4f4f5');

            this.centerText = centerCircle.append('text')
                .data([sum])
                .attr('fill', '#f2f2f2')
                .attr('dy', '.4em')
                .style('text-anchor', 'middle')
                .style('font-size', '65px');
        }

        arcs = chart.selectAll('g.arc')
            .data(this.pie)
            .enter()
            .append('g')
            .attr('class', 'arc')
            .attr('transform', translate);

        this.paths = arcs
            .append('path')
            .attr('d', this.arcDrawer)
            .style('fill', function(d, i) {
                return colorScale(i);
            });

        this.texts = arcs.append('text')
            .attr('dy', '.35em')
            .attr('fill', 'white')
            .style('text-anchor', 'middle')
            .style('font-size', '18px');

        $chart[0].chartObject = this;

        if (options.scaled) {
            $chart.addClass('scaled');
        }
        if (options.class) {
            $chart.addClass(options.class);
        }

        this.setValue(values);
    }

    $.extend(Pie.prototype, {
        dimension: 300,
        setValue: function(values, animate) {
            var me = this,
                start = new Date(),
                duration = this.options.duration || 2000,
                from = this.values || Array.apply(null, new Array(values.length)).map(Number.prototype.valueOf, 0).concat(this.total),
                interpolator = d3.interpolateArray(from, values.concat(this.total - d3.sum(values)));

            this.values = values;

            this.chart.selectAll('text').text('');

            if (animate !== false) {
                me.animateTimer = setInterval(function() {
                    var t = (new Date() - start) / duration,
                        stepValues;

                    t = t >= 1 ? 1 : t;
                    stepValues = interpolator(t);

                    me._drawPaths(stepValues);
                    if (t >= 1) {
                        stepValues = stepValues.slice(0, stepValues.length - 1);
                        me._updateCenterText(d3.sum(stepValues));
                        me._updateLabels(stepValues);
                        clearInterval(me.animateTimer);
                        delete this.animateTimer;
                    }
                }, 20);
            } else {
                me._drawPaths(values);
                me._updateCenterText(d3.sum(values));
                me._updateLabels(values);
            }
        },
        refresh: function() {
            var v = this.values;

            if (this.animateTimer) {
                clearInterval(this.animateTimer);
                delete this.animateTimer;
            }

            this.values = null;
            this.setValue(v);
        },
        _drawPaths: function(values) {
            this.paths
                .data(this.pie(values))
                .attr('d', this.arcDrawer);
        },
        _updateLabels: function(values) {
            var me = this;

            if (me.options.hideLabels !== true) {
                me.texts
                    .data(this.pie(values))
                    .attr('transform', function(d) {
                        return 'translate(' + me.arcDrawer.centroid(d) + ')';
                    })
                    .text(function(d) {
                        return d.data / d3.sum(me.values) < 0.12 ? '' : d.data + '%';
                    });
            }
        },
        _updateCenterText: function(value) {
            var me = this;

            if (me.centerText) {
                setTimeout(function() {
                    me.centerText
                        .data([value])
                        .text(function(d) {
                            return d;
                        });
                }, 1000);
            }
        }
    });

    window.charts = {
        Gauge: Gauge,
        Bar: Bar,
        Bullet: Bullet,
        Pie: Pie
    };

    return window.charts;
})(window, jQuery, d3);