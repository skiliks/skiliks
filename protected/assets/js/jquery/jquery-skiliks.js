/**
 * .center()
 *
 * Можно было положить в окна - но мы сделали "плагин"
 */
jQuery.fn.center = function () {
    "use strict";
    this.css("position","absolute");
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
        $(window).scrollTop()) + "px");
    this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
        $(window).scrollLeft()) + "px");
    return this;
};