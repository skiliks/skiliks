/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.core.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    function c(b, c) {
        var e = b.nodeName.toLowerCase();
        if ("area" === e) {
            var f = b.parentNode,
                g = f.name,
                h;
            return !b.href || !g || f.nodeName.toLowerCase() !== "map" ? !1 : (h = a("img[usemap=#" + g + "]")[0], !! h && d(h))
        }
        return (/input|select|textarea|button|object/.test(e) ? !b.disabled : "a" == e ? b.href || c : c) && d(b)
    }

    function d(b) {
        return !a(b).parents().andSelf().filter(function () {
            return a.curCSS(this, "visibility") === "hidden" || a.expr.filters.hidden(this)
        }).length
    }
    a.ui = a.ui || {};
    if (a.ui.version) return;
    a.extend(a.ui, {
        version: "1.8.21",
        keyCode: {
            ALT: 18,
            BACKSPACE: 8,
            CAPS_LOCK: 20,
            COMMA: 188,
            COMMAND: 91,
            COMMAND_LEFT: 91,
            COMMAND_RIGHT: 93,
            CONTROL: 17,
            DELETE: 46,
            DOWN: 40,
            END: 35,
            ENTER: 13,
            ESCAPE: 27,
            HOME: 36,
            INSERT: 45,
            LEFT: 37,
            MENU: 93,
            NUMPAD_ADD: 107,
            NUMPAD_DECIMAL: 110,
            NUMPAD_DIVIDE: 111,
            NUMPAD_ENTER: 108,
            NUMPAD_MULTIPLY: 106,
            NUMPAD_SUBTRACT: 109,
            PAGE_DOWN: 34,
            PAGE_UP: 33,
            PERIOD: 190,
            RIGHT: 39,
            SHIFT: 16,
            SPACE: 32,
            TAB: 9,
            UP: 38,
            WINDOWS: 91
        }
    }), a.fn.extend({
        propAttr: a.fn.prop || a.fn.attr,
        _focus: a.fn.focus,
        focus: function (b, c) {
            return typeof b == "number" ? this.each(function () {
                var d = this;
                setTimeout(function () {
                    a(d).focus(), c && c.call(d)
                }, b)
            }) : this._focus.apply(this, arguments)
        },
        scrollParent: function () {
            var b;
            return a.browser.msie && /(static|relative)/.test(this.css("position")) || /absolute/.test(this.css("position")) ? b = this.parents().filter(function () {
                return /(relative|absolute|fixed)/.test(a.curCSS(this, "position", 1)) && /(auto|scroll)/.test(a.curCSS(this, "overflow", 1) + a.curCSS(this, "overflow-y", 1) + a.curCSS(this, "overflow-x", 1))
            }).eq(0) : b = this.parents().filter(function () {
                return /(auto|scroll)/.test(a.curCSS(this, "overflow", 1) + a.curCSS(this, "overflow-y", 1) + a.curCSS(this, "overflow-x", 1))
            }).eq(0), /fixed/.test(this.css("position")) || !b.length ? a(document) : b
        },
        zIndex: function (c) {
            if (c !== b) return this.css("zIndex", c);
            if (this.length) {
                var d = a(this[0]),
                    e, f;
                while (d.length && d[0] !== document) {
                    e = d.css("position");
                    if (e === "absolute" || e === "relative" || e === "fixed") {
                        f = parseInt(d.css("zIndex"), 10);
                        if (!isNaN(f) && f !== 0) return f
                    }
                    d = d.parent()
                }
            }
            return 0
        },
        disableSelection: function () {
            return this.bind((a.support.selectstart ? "selectstart" : "mousedown") + ".ui-disableSelection", function (a) {
                a.preventDefault()
            })
        },
        enableSelection: function () {
            return this.unbind(".ui-disableSelection")
        }
    }), a.each(["Width", "Height"], function (c, d) {
        function h(b, c, d, f) {
            return a.each(e, function () {
                c -= parseFloat(a.curCSS(b, "padding" + this, !0)) || 0, d && (c -= parseFloat(a.curCSS(b, "border" + this + "Width", !0)) || 0), f && (c -= parseFloat(a.curCSS(b, "margin" + this, !0)) || 0)
            }), c
        }
        var e = d === "Width" ? ["Left", "Right"] : ["Top", "Bottom"],
            f = d.toLowerCase(),
            g = {
                innerWidth: a.fn.innerWidth,
                innerHeight: a.fn.innerHeight,
                outerWidth: a.fn.outerWidth,
                outerHeight: a.fn.outerHeight
            };
        a.fn["inner" + d] = function (c) {
            return c === b ? g["inner" + d].call(this) : this.each(function () {
                a(this).css(f, h(this, c) + "px")
            })
        }, a.fn["outer" + d] = function (b, c) {
            return typeof b != "number" ? g["outer" + d].call(this, b) : this.each(function () {
                a(this).css(f, h(this, b, !0, c) + "px")
            })
        }
    }), a.extend(a.expr[":"], {
        data: function (b, c, d) {
            return !!a.data(b, d[3])
        },
        focusable: function (b) {
            return c(b, !isNaN(a.attr(b, "tabindex")))
        },
        tabbable: function (b) {
            var d = a.attr(b, "tabindex"),
                e = isNaN(d);
            return (e || d >= 0) && c(b, !e)
        }
    }), a(function () {
        var b = document.body,
            c = b.appendChild(c = document.createElement("div"));
        c.offsetHeight, a.extend(c.style, {
            minHeight: "100px",
            height: "auto",
            padding: 0,
            borderWidth: 0
        }), a.support.minHeight = c.offsetHeight === 100, a.support.selectstart = "onselectstart" in c, b.removeChild(c).style.display = "none"
    }), a.extend(a.ui, {
        plugin: {
            add: function (b, c, d) {
                var e = a.ui[b].prototype;
                for (var f in d) e.plugins[f] = e.plugins[f] || [], e.plugins[f].push([c, d[f]])
            },
            call: function (a, b, c) {
                var d = a.plugins[b];
                if (!d || !a.element[0].parentNode) return;
                for (var e = 0; e < d.length; e++) a.options[d[e][0]] && d[e][1].apply(a.element, c)
            }
        },
        contains: function (a, b) {
            return document.compareDocumentPosition ? a.compareDocumentPosition(b) & 16 : a !== b && a.contains(b)
        },
        hasScroll: function (b, c) {
            if (a(b).css("overflow") === "hidden") return !1;
            var d = c && c === "left" ? "scrollLeft" : "scrollTop",
                e = !1;
            return b[d] > 0 ? !0 : (b[d] = 1, e = b[d] > 0, b[d] = 0, e)
        },
        isOverAxis: function (a, b, c) {
            return a > b && a < b + c
        },
        isOver: function (b, c, d, e, f, g) {
            return a.ui.isOverAxis(b, d, f) && a.ui.isOverAxis(c, e, g)
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.widget.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    if (a.cleanData) {
        var c = a.cleanData;
        a.cleanData = function (b) {
            for (var d = 0, e;
                 (e = b[d]) != null; d++) try {
                a(e).triggerHandler("remove")
            } catch (f) {}
            c(b)
        }
    } else {
        var d = a.fn.remove;
        a.fn.remove = function (b, c) {
            return this.each(function () {
                return c || (!b || a.filter(b, [this]).length) && a("*", this).add([this]).each(function () {
                    try {
                        a(this).triggerHandler("remove")
                    } catch (b) {}
                }), d.call(a(this), b, c)
            })
        }
    }
    a.widget = function (b, c, d) {
        var e = b.split(".")[0],
            f;
        b = b.split(".")[1], f = e + "-" + b, d || (d = c, c = a.Widget), a.expr[":"][f] = function (c) {
            return !!a.data(c, b)
        }, a[e] = a[e] || {}, a[e][b] = function (a, b) {
            arguments.length && this._createWidget(a, b)
        };
        var g = new c;
        g.options = a.extend(!0, {}, g.options), a[e][b].prototype = a.extend(!0, g, {
            namespace: e,
            widgetName: b,
            widgetEventPrefix: a[e][b].prototype.widgetEventPrefix || b,
            widgetBaseClass: f
        }, d), a.widget.bridge(b, a[e][b])
    }, a.widget.bridge = function (c, d) {
        a.fn[c] = function (e) {
            var f = typeof e == "string",
                g = Array.prototype.slice.call(arguments, 1),
                h = this;
            return e = !f && g.length ? a.extend.apply(null, [!0, e].concat(g)) : e, f && e.charAt(0) === "_" ? h : (f ? this.each(function () {
                var d = a.data(this, c),
                    f = d && a.isFunction(d[e]) ? d[e].apply(d, g) : d;
                if (f !== d && f !== b) return h = f, !1
            }) : this.each(function () {
                var b = a.data(this, c);
                b ? b.option(e || {})._init() : a.data(this, c, new d(e, this))
            }), h)
        }
    }, a.Widget = function (a, b) {
        arguments.length && this._createWidget(a, b)
    }, a.Widget.prototype = {
        widgetName: "widget",
        widgetEventPrefix: "",
        options: {
            disabled: !1
        },
        _createWidget: function (b, c) {
            a.data(c, this.widgetName, this), this.element = a(c), this.options = a.extend(!0, {}, this.options, this._getCreateOptions(), b);
            var d = this;
            this.element.bind("remove." + this.widgetName, function () {
                d.destroy()
            }), this._create(), this._trigger("create"), this._init()
        },
        _getCreateOptions: function () {
            return a.metadata && a.metadata.get(this.element[0])[this.widgetName]
        },
        _create: function () {},
        _init: function () {},
        destroy: function () {
            this.element.unbind("." + this.widgetName).removeData(this.widgetName), this.widget().unbind("." + this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass + "-disabled " + "ui-state-disabled")
        },
        widget: function () {
            return this.element
        },
        option: function (c, d) {
            var e = c;
            if (arguments.length === 0) return a.extend({}, this.options);
            if (typeof c == "string") {
                if (d === b) return this.options[c];
                e = {}, e[c] = d
            }
            return this._setOptions(e), this
        },
        _setOptions: function (b) {
            var c = this;
            return a.each(b, function (a, b) {
                c._setOption(a, b)
            }), this
        },
        _setOption: function (a, b) {
            return this.options[a] = b, a === "disabled" && this.widget()[b ? "addClass" : "removeClass"](this.widgetBaseClass + "-disabled" + " " + "ui-state-disabled").attr("aria-disabled", b), this
        },
        enable: function () {
            return this._setOption("disabled", !1)
        },
        disable: function () {
            return this._setOption("disabled", !0)
        },
        _trigger: function (b, c, d) {
            var e, f, g = this.options[b];
            d = d || {}, c = a.Event(c), c.type = (b === this.widgetEventPrefix ? b : this.widgetEventPrefix + b).toLowerCase(), c.target = this.element[0], f = c.originalEvent;
            if (f)
                for (e in f) e in c || (c[e] = f[e]);
            return this.element.trigger(c, d), !(a.isFunction(g) && g.call(this.element[0], c, d) === !1 || c.isDefaultPrevented())
        }
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.mouse.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    var c = !1;
    a(document).mouseup(function (a) {
        c = !1
    }), a.widget("ui.mouse", {
        options: {
            cancel: ":input,option",
            distance: 1,
            delay: 0
        },
        _mouseInit: function () {
            var b = this;
            this.element.bind("mousedown." + this.widgetName, function (a) {
                return b._mouseDown(a)
            }).bind("click." + this.widgetName, function (c) {
                if (!0 === a.data(c.target, b.widgetName + ".preventClickEvent")) return a.removeData(c.target, b.widgetName + ".preventClickEvent"), c.stopImmediatePropagation(), !1
            }), this.started = !1
        },
        _mouseDestroy: function () {
            this.element.unbind("." + this.widgetName), a(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate)
        },
        _mouseDown: function (b) {
            if (c) return;
            this._mouseStarted && this._mouseUp(b), this._mouseDownEvent = b;
            var d = this,
                e = b.which == 1,
                f = typeof this.options.cancel == "string" && b.target.nodeName ? a(b.target).closest(this.options.cancel).length : !1;
            if (!e || f || !this._mouseCapture(b)) return !0;
            this.mouseDelayMet = !this.options.delay, this.mouseDelayMet || (this._mouseDelayTimer = setTimeout(function () {
                d.mouseDelayMet = !0
            }, this.options.delay));
            if (this._mouseDistanceMet(b) && this._mouseDelayMet(b)) {
                this._mouseStarted = this._mouseStart(b) !== !1;
                if (!this._mouseStarted) return b.preventDefault(), !0
            }
            return !0 === a.data(b.target, this.widgetName + ".preventClickEvent") && a.removeData(b.target, this.widgetName + ".preventClickEvent"), this._mouseMoveDelegate = function (a) {
                return d._mouseMove(a)
            }, this._mouseUpDelegate = function (a) {
                return d._mouseUp(a)
            }, a(document).bind("mousemove." + this.widgetName, this._mouseMoveDelegate).bind("mouseup." + this.widgetName, this._mouseUpDelegate), b.preventDefault(), c = !0, !0
        },
        _mouseMove: function (b) {
            return !a.browser.msie || document.documentMode >= 9 || !! b.button ? this._mouseStarted ? (this._mouseDrag(b), b.preventDefault()) : (this._mouseDistanceMet(b) && this._mouseDelayMet(b) && (this._mouseStarted = this._mouseStart(this._mouseDownEvent, b) !== !1, this._mouseStarted ? this._mouseDrag(b) : this._mouseUp(b)), !this._mouseStarted) : this._mouseUp(b)
        },
        _mouseUp: function (b) {
            return a(document).unbind("mousemove." + this.widgetName, this._mouseMoveDelegate).unbind("mouseup." + this.widgetName, this._mouseUpDelegate), this._mouseStarted && (this._mouseStarted = !1, b.target == this._mouseDownEvent.target && a.data(b.target, this.widgetName + ".preventClickEvent", !0), this._mouseStop(b)), !1
        },
        _mouseDistanceMet: function (a) {
            return Math.max(Math.abs(this._mouseDownEvent.pageX - a.pageX), Math.abs(this._mouseDownEvent.pageY - a.pageY)) >= this.options.distance
        },
        _mouseDelayMet: function (a) {
            return this.mouseDelayMet
        },
        _mouseStart: function (a) {},
        _mouseDrag: function (a) {},
        _mouseStop: function (a) {},
        _mouseCapture: function (a) {
            return !0
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.position.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.ui = a.ui || {};
    var c = /left|center|right/,
        d = /top|center|bottom/,
        e = "center",
        f = {}, g = a.fn.position,
        h = a.fn.offset;
    a.fn.position = function (b) {
        if (!b || !b.of) return g.apply(this, arguments);
        b = a.extend({}, b);
        var h = a(b.of),
            i = h[0],
            j = (b.collision || "flip").split(" "),
            k = b.offset ? b.offset.split(" ") : [0, 0],
            l, m, n;
        return i.nodeType === 9 ? (l = h.width(), m = h.height(), n = {
            top: 0,
            left: 0
        }) : i.setTimeout ? (l = h.width(), m = h.height(), n = {
            top: h.scrollTop(),
            left: h.scrollLeft()
        }) : i.preventDefault ? (b.at = "left top", l = m = 0, n = {
            top: b.of.pageY,
            left: b.of.pageX
        }) : (l = h.outerWidth(), m = h.outerHeight(), n = h.offset()), a.each(["my", "at"], function () {
            var a = (b[this] || "").split(" ");
            a.length === 1 && (a = c.test(a[0]) ? a.concat([e]) : d.test(a[0]) ? [e].concat(a) : [e, e]), a[0] = c.test(a[0]) ? a[0] : e, a[1] = d.test(a[1]) ? a[1] : e, b[this] = a
        }), j.length === 1 && (j[1] = j[0]), k[0] = parseInt(k[0], 10) || 0, k.length === 1 && (k[1] = k[0]), k[1] = parseInt(k[1], 10) || 0, b.at[0] === "right" ? n.left += l : b.at[0] === e && (n.left += l / 2), b.at[1] === "bottom" ? n.top += m : b.at[1] === e && (n.top += m / 2), n.left += k[0], n.top += k[1], this.each(function () {
            var c = a(this),
                d = c.outerWidth(),
                g = c.outerHeight(),
                h = parseInt(a.curCSS(this, "marginLeft", !0)) || 0,
                i = parseInt(a.curCSS(this, "marginTop", !0)) || 0,
                o = d + h + (parseInt(a.curCSS(this, "marginRight", !0)) || 0),
                p = g + i + (parseInt(a.curCSS(this, "marginBottom", !0)) || 0),
                q = a.extend({}, n),
                r;
            b.my[0] === "right" ? q.left -= d : b.my[0] === e && (q.left -= d / 2), b.my[1] === "bottom" ? q.top -= g : b.my[1] === e && (q.top -= g / 2), f.fractions || (q.left = Math.round(q.left), q.top = Math.round(q.top)), r = {
                left: q.left - h,
                top: q.top - i
            }, a.each(["left", "top"], function (c, e) {
                a.ui.position[j[c]] && a.ui.position[j[c]][e](q, {
                    targetWidth: l,
                    targetHeight: m,
                    elemWidth: d,
                    elemHeight: g,
                    collisionPosition: r,
                    collisionWidth: o,
                    collisionHeight: p,
                    offset: k,
                    my: b.my,
                    at: b.at
                })
            }), a.fn.bgiframe && c.bgiframe(), c.offset(a.extend(q, {
                using: b.using
            }))
        })
    }, a.ui.position = {
        fit: {
            left: function (b, c) {
                var d = a(window),
                    e = c.collisionPosition.left + c.collisionWidth - d.width() - d.scrollLeft();
                b.left = e > 0 ? b.left - e : Math.max(b.left - c.collisionPosition.left, b.left)
            },
            top: function (b, c) {
                var d = a(window),
                    e = c.collisionPosition.top + c.collisionHeight - d.height() - d.scrollTop();
                b.top = e > 0 ? b.top - e : Math.max(b.top - c.collisionPosition.top, b.top)
            }
        },
        flip: {
            left: function (b, c) {
                if (c.at[0] === e) return;
                var d = a(window),
                    f = c.collisionPosition.left + c.collisionWidth - d.width() - d.scrollLeft(),
                    g = c.my[0] === "left" ? -c.elemWidth : c.my[0] === "right" ? c.elemWidth : 0,
                    h = c.at[0] === "left" ? c.targetWidth : -c.targetWidth,
                    i = -2 * c.offset[0];
                b.left += c.collisionPosition.left < 0 ? g + h + i : f > 0 ? g + h + i : 0
            },
            top: function (b, c) {
                if (c.at[1] === e) return;
                var d = a(window),
                    f = c.collisionPosition.top + c.collisionHeight - d.height() - d.scrollTop(),
                    g = c.my[1] === "top" ? -c.elemHeight : c.my[1] === "bottom" ? c.elemHeight : 0,
                    h = c.at[1] === "top" ? c.targetHeight : -c.targetHeight,
                    i = -2 * c.offset[1];
                b.top += c.collisionPosition.top < 0 ? g + h + i : f > 0 ? g + h + i : 0
            }
        }
    }, a.offset.setOffset || (a.offset.setOffset = function (b, c) {
        /static/.test(a.curCSS(b, "position")) && (b.style.position = "relative");
        var d = a(b),
            e = d.offset(),
            f = parseInt(a.curCSS(b, "top", !0), 10) || 0,
            g = parseInt(a.curCSS(b, "left", !0), 10) || 0,
            h = {
                top: c.top - e.top + f,
                left: c.left - e.left + g
            };
        "using" in c ? c.using.call(b, h) : d.css(h)
    }, a.fn.offset = function (b) {
        var c = this[0];
        return !c || !c.ownerDocument ? null : b ? a.isFunction(b) ? this.each(function (c) {
            a(this).offset(b.call(this, c, a(this).offset()))
        }) : this.each(function () {
            a.offset.setOffset(this, b)
        }) : h.call(this)
    }),
        function () {
            var b = document.getElementsByTagName("body")[0],
                c = document.createElement("div"),
                d, e, g, h, i;
            d = document.createElement(b ? "div" : "body"), g = {
                visibility: "hidden",
                width: 0,
                height: 0,
                border: 0,
                margin: 0,
                background: "none"
            }, b && a.extend(g, {
                position: "absolute",
                left: "-1000px",
                top: "-1000px"
            });
            for (var j in g) d.style[j] = g[j];
            d.appendChild(c), e = b || document.documentElement, e.insertBefore(d, e.firstChild), c.style.cssText = "position: absolute; left: 10.7432222px; top: 10.432325px; height: 30px; width: 201px;", h = a(c).offset(function (a, b) {
                return b
            }).offset(), d.innerHTML = "", e.removeChild(d), i = h.top + h.left + (b ? 2e3 : 0), f.fractions = i > 21 && i < 22
        }()
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.draggable.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.draggable", a.ui.mouse, {
        widgetEventPrefix: "drag",
        options: {
            addClasses: !0,
            appendTo: "parent",
            axis: !1,
            connectToSortable: !1,
            containment: !1,
            cursor: "auto",
            cursorAt: !1,
            grid: !1,
            handle: !1,
            helper: "original",
            iframeFix: !1,
            opacity: !1,
            refreshPositions: !1,
            revert: !1,
            revertDuration: 500,
            scope: "default",
            scroll: !0,
            scrollSensitivity: 20,
            scrollSpeed: 20,
            snap: !1,
            snapMode: "both",
            snapTolerance: 20,
            stack: !1,
            zIndex: !1
        },
        _create: function () {
            this.options.helper == "original" && !/^(?:r|a|f)/.test(this.element.css("position")) && (this.element[0].style.position = "relative"), this.options.addClasses && this.element.addClass("ui-draggable"), this.options.disabled && this.element.addClass("ui-draggable-disabled"), this._mouseInit()
        },
        destroy: function () {
            if (!this.element.data("draggable")) return;
            return this.element.removeData("draggable").unbind(".draggable").removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled"), this._mouseDestroy(), this
        },
        _mouseCapture: function (b) {
            var c = this.options;
            return this.helper || c.disabled || a(b.target).is(".ui-resizable-handle") ? !1 : (this.handle = this._getHandle(b), this.handle ? (c.iframeFix && a(c.iframeFix === !0 ? "iframe" : c.iframeFix).each(function () {
                a('<div class="ui-draggable-iframeFix" style="background: #fff;"></div>').css({
                    width: this.offsetWidth + "px",
                    height: this.offsetHeight + "px",
                    position: "absolute",
                    opacity: "0.001",
                    zIndex: 1e3
                }).css(a(this).offset()).appendTo("body")
            }), !0) : !1)
        },
        _mouseStart: function (b) {
            var c = this.options;
            return this.helper = this._createHelper(b), this.helper.addClass("ui-draggable-dragging"), this._cacheHelperProportions(), a.ui.ddmanager && (a.ui.ddmanager.current = this), this._cacheMargins(), this.cssPosition = this.helper.css("position"), this.scrollParent = this.helper.scrollParent(), this.offset = this.positionAbs = this.element.offset(), this.offset = {
                top: this.offset.top - this.margins.top,
                left: this.offset.left - this.margins.left
            }, a.extend(this.offset, {
                click: {
                    left: b.pageX - this.offset.left,
                    top: b.pageY - this.offset.top
                },
                parent: this._getParentOffset(),
                relative: this._getRelativeOffset()
            }), this.originalPosition = this.position = this._generatePosition(b), this.originalPageX = b.pageX, this.originalPageY = b.pageY, c.cursorAt && this._adjustOffsetFromHelper(c.cursorAt), c.containment && this._setContainment(), this._trigger("start", b) === !1 ? (this._clear(), !1) : (this._cacheHelperProportions(), a.ui.ddmanager && !c.dropBehaviour && a.ui.ddmanager.prepareOffsets(this, b), this._mouseDrag(b, !0), a.ui.ddmanager && a.ui.ddmanager.dragStart(this, b), !0)
        },
        _mouseDrag: function (b, c) {
            this.position = this._generatePosition(b), this.positionAbs = this._convertPositionTo("absolute");
            if (!c) {
                var d = this._uiHash();
                if (this._trigger("drag", b, d) === !1) return this._mouseUp({}), !1;
                this.position = d.position
            }
            if (!this.options.axis || this.options.axis != "y") this.helper[0].style.left = this.position.left + "px";
            if (!this.options.axis || this.options.axis != "x") this.helper[0].style.top = this.position.top + "px";
            return a.ui.ddmanager && a.ui.ddmanager.drag(this, b), !1
        },
        _mouseStop: function (b) {
            var c = !1;
            a.ui.ddmanager && !this.options.dropBehaviour && (c = a.ui.ddmanager.drop(this, b)), this.dropped && (c = this.dropped, this.dropped = !1);
            var d = this.element[0],
                e = !1;
            while (d && (d = d.parentNode)) d == document && (e = !0);
            if (!e && this.options.helper === "original") return !1;
            if (this.options.revert == "invalid" && !c || this.options.revert == "valid" && c || this.options.revert === !0 || a.isFunction(this.options.revert) && this.options.revert.call(this.element, c)) {
                var f = this;
                a(this.helper).animate(this.originalPosition, parseInt(this.options.revertDuration, 10), function () {
                    f._trigger("stop", b) !== !1 && f._clear()
                })
            } else this._trigger("stop", b) !== !1 && this._clear();
            return !1
        },
        _mouseUp: function (b) {
            return this.options.iframeFix === !0 && a("div.ui-draggable-iframeFix").each(function () {
                this.parentNode.removeChild(this)
            }), a.ui.ddmanager && a.ui.ddmanager.dragStop(this, b), a.ui.mouse.prototype._mouseUp.call(this, b)
        },
        cancel: function () {
            return this.helper.is(".ui-draggable-dragging") ? this._mouseUp({}) : this._clear(), this
        },
        _getHandle: function (b) {
            var c = !this.options.handle || !a(this.options.handle, this.element).length ? !0 : !1;
            return a(this.options.handle, this.element).find("*").andSelf().each(function () {
                this == b.target && (c = !0)
            }), c
        },
        _createHelper: function (b) {
            var c = this.options,
                d = a.isFunction(c.helper) ? a(c.helper.apply(this.element[0], [b])) : c.helper == "clone" ? this.element.clone().removeAttr("id") : this.element;
            return d.parents("body").length || d.appendTo(c.appendTo == "parent" ? this.element[0].parentNode : c.appendTo), d[0] != this.element[0] && !/(fixed|absolute)/.test(d.css("position")) && d.css("position", "absolute"), d
        },
        _adjustOffsetFromHelper: function (b) {
            typeof b == "string" && (b = b.split(" ")), a.isArray(b) && (b = {
                left: +b[0],
                top: +b[1] || 0
            }), "left" in b && (this.offset.click.left = b.left + this.margins.left), "right" in b && (this.offset.click.left = this.helperProportions.width - b.right + this.margins.left), "top" in b && (this.offset.click.top = b.top + this.margins.top), "bottom" in b && (this.offset.click.top = this.helperProportions.height - b.bottom + this.margins.top)
        },
        _getParentOffset: function () {
            this.offsetParent = this.helper.offsetParent();
            var b = this.offsetParent.offset();
            this.cssPosition == "absolute" && this.scrollParent[0] != document && a.ui.contains(this.scrollParent[0], this.offsetParent[0]) && (b.left += this.scrollParent.scrollLeft(), b.top += this.scrollParent.scrollTop());
            if (this.offsetParent[0] == document.body || this.offsetParent[0].tagName && this.offsetParent[0].tagName.toLowerCase() == "html" && a.browser.msie) b = {
                top: 0,
                left: 0
            };
            return {
                top: b.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
                left: b.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
            }
        },
        _getRelativeOffset: function () {
            if (this.cssPosition == "relative") {
                var a = this.element.position();
                return {
                    top: a.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
                    left: a.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
                }
            }
            return {
                top: 0,
                left: 0
            }
        },
        _cacheMargins: function () {
            this.margins = {
                left: parseInt(this.element.css("marginLeft"), 10) || 0,
                top: parseInt(this.element.css("marginTop"), 10) || 0,
                right: parseInt(this.element.css("marginRight"), 10) || 0,
                bottom: parseInt(this.element.css("marginBottom"), 10) || 0
            }
        },
        _cacheHelperProportions: function () {
            this.helperProportions = {
                width: this.helper.outerWidth(),
                height: this.helper.outerHeight()
            }
        },
        _setContainment: function () {
            var b = this.options;
            b.containment == "parent" && (b.containment = this.helper[0].parentNode);
            if (b.containment == "document" || b.containment == "window") this.containment = [b.containment == "document" ? 0 : a(window).scrollLeft() - this.offset.relative.left - this.offset.parent.left, b.containment == "document" ? 0 : a(window).scrollTop() - this.offset.relative.top - this.offset.parent.top, (b.containment == "document" ? 0 : a(window).scrollLeft()) + a(b.containment == "document" ? document : window).width() - this.helperProportions.width - this.margins.left, (b.containment == "document" ? 0 : a(window).scrollTop()) + (a(b.containment == "document" ? document : window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top];
            if (!/^(document|window|parent)$/.test(b.containment) && b.containment.constructor != Array) {
                var c = a(b.containment),
                    d = c[0];
                if (!d) return;
                var e = c.offset(),
                    f = a(d).css("overflow") != "hidden";
                this.containment = [(parseInt(a(d).css("borderLeftWidth"), 10) || 0) + (parseInt(a(d).css("paddingLeft"), 10) || 0), (parseInt(a(d).css("borderTopWidth"), 10) || 0) + (parseInt(a(d).css("paddingTop"), 10) || 0), (f ? Math.max(d.scrollWidth, d.offsetWidth) : d.offsetWidth) - (parseInt(a(d).css("borderLeftWidth"), 10) || 0) - (parseInt(a(d).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left - this.margins.right, (f ? Math.max(d.scrollHeight, d.offsetHeight) : d.offsetHeight) - (parseInt(a(d).css("borderTopWidth"), 10) || 0) - (parseInt(a(d).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top - this.margins.bottom], this.relative_container = c
            } else b.containment.constructor == Array && (this.containment = b.containment)
        },
        _convertPositionTo: function (b, c) {
            c || (c = this.position);
            var d = b == "absolute" ? 1 : -1,
                e = this.options,
                f = this.cssPosition == "absolute" && (this.scrollParent[0] == document || !a.ui.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent,
                g = /(html|body)/i.test(f[0].tagName);
            return {
                top: c.top + this.offset.relative.top * d + this.offset.parent.top * d - (a.browser.safari && a.browser.version < 526 && this.cssPosition == "fixed" ? 0 : (this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : g ? 0 : f.scrollTop()) * d),
                left: c.left + this.offset.relative.left * d + this.offset.parent.left * d - (a.browser.safari && a.browser.version < 526 && this.cssPosition == "fixed" ? 0 : (this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : g ? 0 : f.scrollLeft()) * d)
            }
        },
        _generatePosition: function (b) {
            var c = this.options,
                d = this.cssPosition == "absolute" && (this.scrollParent[0] == document || !a.ui.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent,
                e = /(html|body)/i.test(d[0].tagName),
                f = b.pageX,
                g = b.pageY;
            if (this.originalPosition) {
                var h;
                if (this.containment) {
                    if (this.relative_container) {
                        var i = this.relative_container.offset();
                        h = [this.containment[0] + i.left, this.containment[1] + i.top, this.containment[2] + i.left, this.containment[3] + i.top]
                    } else h = this.containment;
                    b.pageX - this.offset.click.left < h[0] && (f = h[0] + this.offset.click.left), b.pageY - this.offset.click.top < h[1] && (g = h[1] + this.offset.click.top), b.pageX - this.offset.click.left > h[2] && (f = h[2] + this.offset.click.left), b.pageY - this.offset.click.top > h[3] && (g = h[3] + this.offset.click.top)
                }
                if (c.grid) {
                    var j = c.grid[1] ? this.originalPageY + Math.round((g - this.originalPageY) / c.grid[1]) * c.grid[1] : this.originalPageY;
                    g = h ? j - this.offset.click.top < h[1] || j - this.offset.click.top > h[3] ? j - this.offset.click.top < h[1] ? j + c.grid[1] : j - c.grid[1] : j : j;
                    var k = c.grid[0] ? this.originalPageX + Math.round((f - this.originalPageX) / c.grid[0]) * c.grid[0] : this.originalPageX;
                    f = h ? k - this.offset.click.left < h[0] || k - this.offset.click.left > h[2] ? k - this.offset.click.left < h[0] ? k + c.grid[0] : k - c.grid[0] : k : k
                }
            }
            return {
                top: g - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + (a.browser.safari && a.browser.version < 526 && this.cssPosition == "fixed" ? 0 : this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : e ? 0 : d.scrollTop()),
                left: f - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + (a.browser.safari && a.browser.version < 526 && this.cssPosition == "fixed" ? 0 : this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : e ? 0 : d.scrollLeft())
            }
        },
        _clear: function () {
            this.helper.removeClass("ui-draggable-dragging"), this.helper[0] != this.element[0] && !this.cancelHelperRemoval && this.helper.remove(), this.helper = null, this.cancelHelperRemoval = !1
        },
        _trigger: function (b, c, d) {
            return d = d || this._uiHash(), a.ui.plugin.call(this, b, [c, d]), b == "drag" && (this.positionAbs = this._convertPositionTo("absolute")), a.Widget.prototype._trigger.call(this, b, c, d)
        },
        plugins: {},
        _uiHash: function (a) {
            return {
                helper: this.helper,
                position: this.position,
                originalPosition: this.originalPosition,
                offset: this.positionAbs
            }
        }
    }), a.extend(a.ui.draggable, {
        version: "1.8.21"
    }), a.ui.plugin.add("draggable", "connectToSortable", {
        start: function (b, c) {
            var d = a(this).data("draggable"),
                e = d.options,
                f = a.extend({}, c, {
                    item: d.element
                });
            d.sortables = [], a(e.connectToSortable).each(function () {
                var c = a.data(this, "sortable");
                c && !c.options.disabled && (d.sortables.push({
                    instance: c,
                    shouldRevert: c.options.revert
                }), c.refreshPositions(), c._trigger("activate", b, f))
            })
        },
        stop: function (b, c) {
            var d = a(this).data("draggable"),
                e = a.extend({}, c, {
                    item: d.element
                });
            a.each(d.sortables, function () {
                this.instance.isOver ? (this.instance.isOver = 0, d.cancelHelperRemoval = !0, this.instance.cancelHelperRemoval = !1, this.shouldRevert && (this.instance.options.revert = !0), this.instance._mouseStop(b), this.instance.options.helper = this.instance.options._helper, d.options.helper == "original" && this.instance.currentItem.css({
                    top: "auto",
                    left: "auto"
                })) : (this.instance.cancelHelperRemoval = !1, this.instance._trigger("deactivate", b, e))
            })
        },
        drag: function (b, c) {
            var d = a(this).data("draggable"),
                e = this,
                f = function (b) {
                    var c = this.offset.click.top,
                        d = this.offset.click.left,
                        e = this.positionAbs.top,
                        f = this.positionAbs.left,
                        g = b.height,
                        h = b.width,
                        i = b.top,
                        j = b.left;
                    return a.ui.isOver(e + c, f + d, i, j, g, h)
                };
            a.each(d.sortables, function (f) {
                this.instance.positionAbs = d.positionAbs, this.instance.helperProportions = d.helperProportions, this.instance.offset.click = d.offset.click, this.instance._intersectsWith(this.instance.containerCache) ? (this.instance.isOver || (this.instance.isOver = 1, this.instance.currentItem = a(e).clone().removeAttr("id").appendTo(this.instance.element).data("sortable-item", !0), this.instance.options._helper = this.instance.options.helper, this.instance.options.helper = function () {
                    return c.helper[0]
                }, b.target = this.instance.currentItem[0], this.instance._mouseCapture(b, !0), this.instance._mouseStart(b, !0, !0), this.instance.offset.click.top = d.offset.click.top, this.instance.offset.click.left = d.offset.click.left, this.instance.offset.parent.left -= d.offset.parent.left - this.instance.offset.parent.left, this.instance.offset.parent.top -= d.offset.parent.top - this.instance.offset.parent.top, d._trigger("toSortable", b), d.dropped = this.instance.element, d.currentItem = d.element, this.instance.fromOutside = d), this.instance.currentItem && this.instance._mouseDrag(b)) : this.instance.isOver && (this.instance.isOver = 0, this.instance.cancelHelperRemoval = !0, this.instance.options.revert = !1, this.instance._trigger("out", b, this.instance._uiHash(this.instance)), this.instance._mouseStop(b, !0), this.instance.options.helper = this.instance.options._helper, this.instance.currentItem.remove(), this.instance.placeholder && this.instance.placeholder.remove(), d._trigger("fromSortable", b), d.dropped = !1)
            })
        }
    }), a.ui.plugin.add("draggable", "cursor", {
        start: function (b, c) {
            var d = a("body"),
                e = a(this).data("draggable").options;
            d.css("cursor") && (e._cursor = d.css("cursor")), d.css("cursor", e.cursor)
        },
        stop: function (b, c) {
            var d = a(this).data("draggable").options;
            d._cursor && a("body").css("cursor", d._cursor)
        }
    }), a.ui.plugin.add("draggable", "opacity", {
        start: function (b, c) {
            var d = a(c.helper),
                e = a(this).data("draggable").options;
            d.css("opacity") && (e._opacity = d.css("opacity")), d.css("opacity", e.opacity)
        },
        stop: function (b, c) {
            var d = a(this).data("draggable").options;
            d._opacity && a(c.helper).css("opacity", d._opacity)
        }
    }), a.ui.plugin.add("draggable", "scroll", {
        start: function (b, c) {
            var d = a(this).data("draggable");
            d.scrollParent[0] != document && d.scrollParent[0].tagName != "HTML" && (d.overflowOffset = d.scrollParent.offset())
        },
        drag: function (b, c) {
            var d = a(this).data("draggable"),
                e = d.options,
                f = !1;
            if (d.scrollParent[0] != document && d.scrollParent[0].tagName != "HTML") {
                if (!e.axis || e.axis != "x") d.overflowOffset.top + d.scrollParent[0].offsetHeight - b.pageY < e.scrollSensitivity ? d.scrollParent[0].scrollTop = f = d.scrollParent[0].scrollTop + e.scrollSpeed : b.pageY - d.overflowOffset.top < e.scrollSensitivity && (d.scrollParent[0].scrollTop = f = d.scrollParent[0].scrollTop - e.scrollSpeed);
                if (!e.axis || e.axis != "y") d.overflowOffset.left + d.scrollParent[0].offsetWidth - b.pageX < e.scrollSensitivity ? d.scrollParent[0].scrollLeft = f = d.scrollParent[0].scrollLeft + e.scrollSpeed : b.pageX - d.overflowOffset.left < e.scrollSensitivity && (d.scrollParent[0].scrollLeft = f = d.scrollParent[0].scrollLeft - e.scrollSpeed)
            } else {
                if (!e.axis || e.axis != "x") b.pageY - a(document).scrollTop() < e.scrollSensitivity ? f = a(document).scrollTop(a(document).scrollTop() - e.scrollSpeed) : a(window).height() - (b.pageY - a(document).scrollTop()) < e.scrollSensitivity && (f = a(document).scrollTop(a(document).scrollTop() + e.scrollSpeed));
                if (!e.axis || e.axis != "y") b.pageX - a(document).scrollLeft() < e.scrollSensitivity ? f = a(document).scrollLeft(a(document).scrollLeft() - e.scrollSpeed) : a(window).width() - (b.pageX - a(document).scrollLeft()) < e.scrollSensitivity && (f = a(document).scrollLeft(a(document).scrollLeft() + e.scrollSpeed))
            }
            f !== !1 && a.ui.ddmanager && !e.dropBehaviour && a.ui.ddmanager.prepareOffsets(d, b)
        }
    }), a.ui.plugin.add("draggable", "snap", {
        start: function (b, c) {
            var d = a(this).data("draggable"),
                e = d.options;
            d.snapElements = [], a(e.snap.constructor != String ? e.snap.items || ":data(draggable)" : e.snap).each(function () {
                var b = a(this),
                    c = b.offset();
                this != d.element[0] && d.snapElements.push({
                    item: this,
                    width: b.outerWidth(),
                    height: b.outerHeight(),
                    top: c.top,
                    left: c.left
                })
            })
        },
        drag: function (b, c) {
            var d = a(this).data("draggable"),
                e = d.options,
                f = e.snapTolerance,
                g = c.offset.left,
                h = g + d.helperProportions.width,
                i = c.offset.top,
                j = i + d.helperProportions.height;
            for (var k = d.snapElements.length - 1; k >= 0; k--) {
                var l = d.snapElements[k].left,
                    m = l + d.snapElements[k].width,
                    n = d.snapElements[k].top,
                    o = n + d.snapElements[k].height;
                if (!(l - f < g && g < m + f && n - f < i && i < o + f || l - f < g && g < m + f && n - f < j && j < o + f || l - f < h && h < m + f && n - f < i && i < o + f || l - f < h && h < m + f && n - f < j && j < o + f)) {
                    d.snapElements[k].snapping && d.options.snap.release && d.options.snap.release.call(d.element, b, a.extend(d._uiHash(), {
                        snapItem: d.snapElements[k].item
                    })), d.snapElements[k].snapping = !1;
                    continue
                }
                if (e.snapMode != "inner") {
                    var p = Math.abs(n - j) <= f,
                        q = Math.abs(o - i) <= f,
                        r = Math.abs(l - h) <= f,
                        s = Math.abs(m - g) <= f;
                    p && (c.position.top = d._convertPositionTo("relative", {
                        top: n - d.helperProportions.height,
                        left: 0
                    }).top - d.margins.top), q && (c.position.top = d._convertPositionTo("relative", {
                        top: o,
                        left: 0
                    }).top - d.margins.top), r && (c.position.left = d._convertPositionTo("relative", {
                        top: 0,
                        left: l - d.helperProportions.width
                    }).left - d.margins.left), s && (c.position.left = d._convertPositionTo("relative", {
                        top: 0,
                        left: m
                    }).left - d.margins.left)
                }
                var t = p || q || r || s;
                if (e.snapMode != "outer") {
                    var p = Math.abs(n - i) <= f,
                        q = Math.abs(o - j) <= f,
                        r = Math.abs(l - g) <= f,
                        s = Math.abs(m - h) <= f;
                    p && (c.position.top = d._convertPositionTo("relative", {
                        top: n,
                        left: 0
                    }).top - d.margins.top), q && (c.position.top = d._convertPositionTo("relative", {
                        top: o - d.helperProportions.height,
                        left: 0
                    }).top - d.margins.top), r && (c.position.left = d._convertPositionTo("relative", {
                        top: 0,
                        left: l
                    }).left - d.margins.left), s && (c.position.left = d._convertPositionTo("relative", {
                        top: 0,
                        left: m - d.helperProportions.width
                    }).left - d.margins.left)
                }!d.snapElements[k].snapping && (p || q || r || s || t) && d.options.snap.snap && d.options.snap.snap.call(d.element, b, a.extend(d._uiHash(), {
                    snapItem: d.snapElements[k].item
                })), d.snapElements[k].snapping = p || q || r || s || t
            }
        }
    }), a.ui.plugin.add("draggable", "stack", {
        start: function (b, c) {
            var d = a(this).data("draggable").options,
                e = a.makeArray(a(d.stack)).sort(function (b, c) {
                    return (parseInt(a(b).css("zIndex"), 10) || 0) - (parseInt(a(c).css("zIndex"), 10) || 0)
                });
            if (!e.length) return;
            var f = parseInt(e[0].style.zIndex) || 0;
            a(e).each(function (a) {
                this.style.zIndex = f + a
            }), this[0].style.zIndex = f + e.length
        }
    }), a.ui.plugin.add("draggable", "zIndex", {
        start: function (b, c) {
            var d = a(c.helper),
                e = a(this).data("draggable").options;
            d.css("zIndex") && (e._zIndex = d.css("zIndex")), d.css("zIndex", e.zIndex)
        },
        stop: function (b, c) {
            var d = a(this).data("draggable").options;
            d._zIndex && a(c.helper).css("zIndex", d._zIndex)
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.droppable.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.droppable", {
        widgetEventPrefix: "drop",
        options: {
            accept: "*",
            activeClass: !1,
            addClasses: !0,
            greedy: !1,
            hoverClass: !1,
            scope: "default",
            tolerance: "intersect"
        },
        _create: function () {
            var b = this.options,
                c = b.accept;
            this.isover = 0, this.isout = 1, this.accept = a.isFunction(c) ? c : function (a) {
                return a.is(c)
            }, this.proportions = {
                width: this.element[0].offsetWidth,
                height: this.element[0].offsetHeight
            }, a.ui.ddmanager.droppables[b.scope] = a.ui.ddmanager.droppables[b.scope] || [], a.ui.ddmanager.droppables[b.scope].push(this), b.addClasses && this.element.addClass("ui-droppable")
        },
        destroy: function () {
            var b = a.ui.ddmanager.droppables[this.options.scope];
            for (var c = 0; c < b.length; c++) b[c] == this && b.splice(c, 1);
            return this.element.removeClass("ui-droppable ui-droppable-disabled").removeData("droppable").unbind(".droppable"), this
        },
        _setOption: function (b, c) {
            b == "accept" && (this.accept = a.isFunction(c) ? c : function (a) {
                return a.is(c)
            }), a.Widget.prototype._setOption.apply(this, arguments)
        },
        _activate: function (b) {
            var c = a.ui.ddmanager.current;
            this.options.activeClass && this.element.addClass(this.options.activeClass), c && this._trigger("activate", b, this.ui(c))
        },
        _deactivate: function (b) {
            var c = a.ui.ddmanager.current;
            this.options.activeClass && this.element.removeClass(this.options.activeClass), c && this._trigger("deactivate", b, this.ui(c))
        },
        _over: function (b) {
            var c = a.ui.ddmanager.current;
            if (!c || (c.currentItem || c.element)[0] == this.element[0]) return;
            this.accept.call(this.element[0], c.currentItem || c.element) && (this.options.hoverClass && this.element.addClass(this.options.hoverClass), this._trigger("over", b, this.ui(c)))
        },
        _out: function (b) {
            var c = a.ui.ddmanager.current;
            if (!c || (c.currentItem || c.element)[0] == this.element[0]) return;
            this.accept.call(this.element[0], c.currentItem || c.element) && (this.options.hoverClass && this.element.removeClass(this.options.hoverClass), this._trigger("out", b, this.ui(c)))
        },
        _drop: function (b, c) {
            var d = c || a.ui.ddmanager.current;
            if (!d || (d.currentItem || d.element)[0] == this.element[0]) return !1;
            var e = !1;
            return this.element.find(":data(droppable)").not(".ui-draggable-dragging").each(function () {
                var b = a.data(this, "droppable");
                if (b.options.greedy && !b.options.disabled && b.options.scope == d.options.scope && b.accept.call(b.element[0], d.currentItem || d.element) && a.ui.intersect(d, a.extend(b, {
                    offset: b.element.offset()
                }), b.options.tolerance)) return e = !0, !1
            }), e ? !1 : this.accept.call(this.element[0], d.currentItem || d.element) ? (this.options.activeClass && this.element.removeClass(this.options.activeClass), this.options.hoverClass && this.element.removeClass(this.options.hoverClass), this._trigger("drop", b, this.ui(d)), this.element) : !1
        },
        ui: function (a) {
            return {
                draggable: a.currentItem || a.element,
                helper: a.helper,
                position: a.position,
                offset: a.positionAbs
            }
        }
    }), a.extend(a.ui.droppable, {
        version: "1.8.21"
    }), a.ui.intersect = function (b, c, d) {
        if (!c.offset) return !1;
        var e = (b.positionAbs || b.position.absolute).left,
            f = e + b.helperProportions.width,
            g = (b.positionAbs || b.position.absolute).top,
            h = g + b.helperProportions.height,
            i = c.offset.left,
            j = i + c.proportions.width,
            k = c.offset.top,
            l = k + c.proportions.height;
        switch (d) {
            case "fit":
                return i <= e && f <= j && k <= g && h <= l;
            case "intersect":
                return i < e + b.helperProportions.width / 2 && f - b.helperProportions.width / 2 < j && k < g + b.helperProportions.height / 2 && h - b.helperProportions.height / 2 < l;
            case "pointer":
                var m = (b.positionAbs || b.position.absolute).left + (b.clickOffset || b.offset.click).left,
                    n = (b.positionAbs || b.position.absolute).top + (b.clickOffset || b.offset.click).top,
                    o = a.ui.isOver(n, m, k, i, c.proportions.height, c.proportions.width);
                return o;
            case "touch":
                return (g >= k && g <= l || h >= k && h <= l || g < k && h > l) && (e >= i && e <= j || f >= i && f <= j || e < i && f > j);
            default:
                return !1
        }
    }, a.ui.ddmanager = {
        current: null,
        droppables: {
            "default": []
        },
        prepareOffsets: function (b, c) {
            var d = a.ui.ddmanager.droppables[b.options.scope] || [],
                e = c ? c.type : null,
                f = (b.currentItem || b.element).find(":data(droppable)").andSelf();
            g: for (var h = 0; h < d.length; h++) {
                if (d[h].options.disabled || b && !d[h].accept.call(d[h].element[0], b.currentItem || b.element)) continue;
                for (var i = 0; i < f.length; i++)
                    if (f[i] == d[h].element[0]) {
                        d[h].proportions.height = 0;
                        continue g
                    }
                d[h].visible = d[h].element.css("display") != "none";
                if (!d[h].visible) continue;
                e == "mousedown" && d[h]._activate.call(d[h], c), d[h].offset = d[h].element.offset(), d[h].proportions = {
                    width: d[h].element[0].offsetWidth,
                    height: d[h].element[0].offsetHeight
                }
            }
        },
        drop: function (b, c) {
            var d = !1;
            return a.each(a.ui.ddmanager.droppables[b.options.scope] || [], function () {
                if (!this.options) return;
                !this.options.disabled && this.visible && a.ui.intersect(b, this, this.options.tolerance) && (d = this._drop.call(this, c) || d), !this.options.disabled && this.visible && this.accept.call(this.element[0], b.currentItem || b.element) && (this.isout = 1, this.isover = 0, this._deactivate.call(this, c))
            }), d
        },
        dragStart: function (b, c) {
            b.element.parents(":not(body,html)").bind("scroll.droppable", function () {
                b.options.refreshPositions || a.ui.ddmanager.prepareOffsets(b, c)
            })
        },
        drag: function (b, c) {
            b.options.refreshPositions && a.ui.ddmanager.prepareOffsets(b, c), a.each(a.ui.ddmanager.droppables[b.options.scope] || [], function () {
                if (this.options.disabled || this.greedyChild || !this.visible) return;
                var d = a.ui.intersect(b, this, this.options.tolerance),
                    e = !d && this.isover == 1 ? "isout" : d && this.isover == 0 ? "isover" : null;
                if (!e) return;
                var f;
                if (this.options.greedy) {
                    var g = this.element.parents(":data(droppable):eq(0)");
                    g.length && (f = a.data(g[0], "droppable"), f.greedyChild = e == "isover" ? 1 : 0)
                }
                f && e == "isover" && (f.isover = 0, f.isout = 1, f._out.call(f, c)), this[e] = 1, this[e == "isout" ? "isover" : "isout"] = 0, this[e == "isover" ? "_over" : "_out"].call(this, c), f && e == "isout" && (f.isout = 0, f.isover = 1, f._over.call(f, c))
            })
        },
        dragStop: function (b, c) {
            b.element.parents(":not(body,html)").unbind("scroll.droppable"), b.options.refreshPositions || a.ui.ddmanager.prepareOffsets(b, c)
        }
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.resizable.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.resizable", a.ui.mouse, {
        widgetEventPrefix: "resize",
        options: {
            alsoResize: !1,
            animate: !1,
            animateDuration: "slow",
            animateEasing: "swing",
            aspectRatio: !1,
            autoHide: !1,
            containment: !1,
            ghost: !1,
            grid: !1,
            handles: "e,s,se",
            helper: !1,
            maxHeight: null,
            maxWidth: null,
            minHeight: 10,
            minWidth: 10,
            zIndex: 1e3
        },
        _create: function () {
            var b = this,
                c = this.options;
            this.element.addClass("ui-resizable"), a.extend(this, {
                _aspectRatio: !! c.aspectRatio,
                aspectRatio: c.aspectRatio,
                originalElement: this.element,
                _proportionallyResizeElements: [],
                _helper: c.helper || c.ghost || c.animate ? c.helper || "ui-resizable-helper" : null
            }), this.element[0].nodeName.match(/canvas|textarea|input|select|button|img/i) && (this.element.wrap(a('<div class="ui-wrapper" style="overflow: hidden;"></div>').css({
                position: this.element.css("position"),
                width: this.element.outerWidth(),
                height: this.element.outerHeight(),
                top: this.element.css("top"),
                left: this.element.css("left")
            })), this.element = this.element.parent().data("resizable", this.element.data("resizable")), this.elementIsWrapper = !0, this.element.css({
                marginLeft: this.originalElement.css("marginLeft"),
                marginTop: this.originalElement.css("marginTop"),
                marginRight: this.originalElement.css("marginRight"),
                marginBottom: this.originalElement.css("marginBottom")
            }), this.originalElement.css({
                marginLeft: 0,
                marginTop: 0,
                marginRight: 0,
                marginBottom: 0
            }), this.originalResizeStyle = this.originalElement.css("resize"), this.originalElement.css("resize", "none"), this._proportionallyResizeElements.push(this.originalElement.css({
                position: "static",
                zoom: 1,
                display: "block"
            })), this.originalElement.css({
                margin: this.originalElement.css("margin")
            }), this._proportionallyResize()), this.handles = c.handles || (a(".ui-resizable-handle", this.element).length ? {
                n: ".ui-resizable-n",
                e: ".ui-resizable-e",
                s: ".ui-resizable-s",
                w: ".ui-resizable-w",
                se: ".ui-resizable-se",
                sw: ".ui-resizable-sw",
                ne: ".ui-resizable-ne",
                nw: ".ui-resizable-nw"
            } : "e,s,se");
            if (this.handles.constructor == String) {
                this.handles == "all" && (this.handles = "n,e,s,w,se,sw,ne,nw");
                var d = this.handles.split(",");
                this.handles = {};
                for (var e = 0; e < d.length; e++) {
                    var f = a.trim(d[e]),
                        g = "ui-resizable-" + f,
                        h = a('<div class="ui-resizable-handle ' + g + '"></div>');
                    h.css({
                        zIndex: c.zIndex
                    }), "se" == f && h.addClass("ui-icon ui-icon-gripsmall-diagonal-se"), this.handles[f] = ".ui-resizable-" + f, this.element.append(h)
                }
            }
            this._renderAxis = function (b) {
                b = b || this.element;
                for (var c in this.handles) {
                    this.handles[c].constructor == String && (this.handles[c] = a(this.handles[c], this.element).show());
                    if (this.elementIsWrapper && this.originalElement[0].nodeName.match(/textarea|input|select|button/i)) {
                        var d = a(this.handles[c], this.element),
                            e = 0;
                        e = /sw|ne|nw|se|n|s/.test(c) ? d.outerHeight() : d.outerWidth();
                        var f = ["padding", /ne|nw|n/.test(c) ? "Top" : /se|sw|s/.test(c) ? "Bottom" : /^e$/.test(c) ? "Right" : "Left"].join("");
                        b.css(f, e), this._proportionallyResize()
                    }
                    if (!a(this.handles[c]).length) continue
                }
            }, this._renderAxis(this.element), this._handles = a(".ui-resizable-handle", this.element).disableSelection(), this._handles.mouseover(function () {
                if (!b.resizing) {
                    if (this.className) var a = this.className.match(/ui-resizable-(se|sw|ne|nw|n|e|s|w)/i);
                    b.axis = a && a[1] ? a[1] : "se"
                }
            }), c.autoHide && (this._handles.hide(), a(this.element).addClass("ui-resizable-autohide").hover(function () {
                if (c.disabled) return;
                a(this).removeClass("ui-resizable-autohide"), b._handles.show()
            }, function () {
                if (c.disabled) return;
                b.resizing || (a(this).addClass("ui-resizable-autohide"), b._handles.hide())
            })), this._mouseInit()
        },
        destroy: function () {
            this._mouseDestroy();
            var b = function (b) {
                a(b).removeClass("ui-resizable ui-resizable-disabled ui-resizable-resizing").removeData("resizable").unbind(".resizable").find(".ui-resizable-handle").remove()
            };
            if (this.elementIsWrapper) {
                b(this.element);
                var c = this.element;
                c.after(this.originalElement.css({
                    position: c.css("position"),
                    width: c.outerWidth(),
                    height: c.outerHeight(),
                    top: c.css("top"),
                    left: c.css("left")
                })).remove()
            }
            return this.originalElement.css("resize", this.originalResizeStyle), b(this.originalElement), this
        },
        _mouseCapture: function (b) {
            var c = !1;
            for (var d in this.handles) a(this.handles[d])[0] == b.target && (c = !0);
            return !this.options.disabled && c
        },
        _mouseStart: function (b) {
            var d = this.options,
                e = this.element.position(),
                f = this.element;
            this.resizing = !0, this.documentScroll = {
                top: a(document).scrollTop(),
                left: a(document).scrollLeft()
            }, (f.is(".ui-draggable") || /absolute/.test(f.css("position"))) && f.css({
                position: "absolute",
                top: e.top,
                left: e.left
            }), this._renderProxy();
            var g = c(this.helper.css("left")),
                h = c(this.helper.css("top"));
            d.containment && (g += a(d.containment).scrollLeft() || 0, h += a(d.containment).scrollTop() || 0), this.offset = this.helper.offset(), this.position = {
                left: g,
                top: h
            }, this.size = this._helper ? {
                width: f.outerWidth(),
                height: f.outerHeight()
            } : {
                width: f.width(),
                height: f.height()
            }, this.originalSize = this._helper ? {
                width: f.outerWidth(),
                height: f.outerHeight()
            } : {
                width: f.width(),
                height: f.height()
            }, this.originalPosition = {
                left: g,
                top: h
            }, this.sizeDiff = {
                width: f.outerWidth() - f.width(),
                height: f.outerHeight() - f.height()
            }, this.originalMousePosition = {
                left: b.pageX,
                top: b.pageY
            }, this.aspectRatio = typeof d.aspectRatio == "number" ? d.aspectRatio : this.originalSize.width / this.originalSize.height || 1;
            var i = a(".ui-resizable-" + this.axis).css("cursor");
            return a("body").css("cursor", i == "auto" ? this.axis + "-resize" : i), f.addClass("ui-resizable-resizing"), this._propagate("start", b), !0
        },
        _mouseDrag: function (b) {
            var c = this.helper,
                d = this.options,
                e = {}, f = this,
                g = this.originalMousePosition,
                h = this.axis,
                i = b.pageX - g.left || 0,
                j = b.pageY - g.top || 0,
                k = this._change[h];
            if (!k) return !1;
            var l = k.apply(this, [b, i, j]),
                m = a.browser.msie && a.browser.version < 7,
                n = this.sizeDiff;
            this._updateVirtualBoundaries(b.shiftKey);
            if (this._aspectRatio || b.shiftKey) l = this._updateRatio(l, b);
            return l = this._respectSize(l, b), this._propagate("resize", b), c.css({
                top: this.position.top + "px",
                left: this.position.left + "px",
                width: this.size.width + "px",
                height: this.size.height + "px"
            }), !this._helper && this._proportionallyResizeElements.length && this._proportionallyResize(), this._updateCache(l), this._trigger("resize", b, this.ui()), !1
        },
        _mouseStop: function (b) {
            this.resizing = !1;
            var c = this.options,
                d = this;
            if (this._helper) {
                var e = this._proportionallyResizeElements,
                    f = e.length && /textarea/i.test(e[0].nodeName),
                    g = f && a.ui.hasScroll(e[0], "left") ? 0 : d.sizeDiff.height,
                    h = f ? 0 : d.sizeDiff.width,
                    i = {
                        width: d.helper.width() - h,
                        height: d.helper.height() - g
                    }, j = parseInt(d.element.css("left"), 10) + (d.position.left - d.originalPosition.left) || null,
                    k = parseInt(d.element.css("top"), 10) + (d.position.top - d.originalPosition.top) || null;
                c.animate || this.element.css(a.extend(i, {
                    top: k,
                    left: j
                })), d.helper.height(d.size.height), d.helper.width(d.size.width), this._helper && !c.animate && this._proportionallyResize()
            }
            return a("body").css("cursor", "auto"), this.element.removeClass("ui-resizable-resizing"), this._propagate("stop", b), this._helper && this.helper.remove(), !1
        },
        _updateVirtualBoundaries: function (a) {
            var b = this.options,
                c, e, f, g, h;
            h = {
                minWidth: d(b.minWidth) ? b.minWidth : 0,
                maxWidth: d(b.maxWidth) ? b.maxWidth : Infinity,
                minHeight: d(b.minHeight) ? b.minHeight : 0,
                maxHeight: d(b.maxHeight) ? b.maxHeight : Infinity
            };
            if (this._aspectRatio || a) c = h.minHeight * this.aspectRatio, f = h.minWidth / this.aspectRatio, e = h.maxHeight * this.aspectRatio, g = h.maxWidth / this.aspectRatio, c > h.minWidth && (h.minWidth = c), f > h.minHeight && (h.minHeight = f), e < h.maxWidth && (h.maxWidth = e), g < h.maxHeight && (h.maxHeight = g);
            this._vBoundaries = h
        },
        _updateCache: function (a) {
            var b = this.options;
            this.offset = this.helper.offset(), d(a.left) && (this.position.left = a.left), d(a.top) && (this.position.top = a.top), d(a.height) && (this.size.height = a.height), d(a.width) && (this.size.width = a.width)
        },
        _updateRatio: function (a, b) {
            var c = this.options,
                e = this.position,
                f = this.size,
                g = this.axis;
            return d(a.height) ? a.width = a.height * this.aspectRatio : d(a.width) && (a.height = a.width / this.aspectRatio), g == "sw" && (a.left = e.left + (f.width - a.width), a.top = null), g == "nw" && (a.top = e.top + (f.height - a.height), a.left = e.left + (f.width - a.width)), a
        },
        _respectSize: function (a, b) {
            var c = this.helper,
                e = this._vBoundaries,
                f = this._aspectRatio || b.shiftKey,
                g = this.axis,
                h = d(a.width) && e.maxWidth && e.maxWidth < a.width,
                i = d(a.height) && e.maxHeight && e.maxHeight < a.height,
                j = d(a.width) && e.minWidth && e.minWidth > a.width,
                k = d(a.height) && e.minHeight && e.minHeight > a.height;
            j && (a.width = e.minWidth), k && (a.height = e.minHeight), h && (a.width = e.maxWidth), i && (a.height = e.maxHeight);
            var l = this.originalPosition.left + this.originalSize.width,
                m = this.position.top + this.size.height,
                n = /sw|nw|w/.test(g),
                o = /nw|ne|n/.test(g);
            j && n && (a.left = l - e.minWidth), h && n && (a.left = l - e.maxWidth), k && o && (a.top = m - e.minHeight), i && o && (a.top = m - e.maxHeight);
            var p = !a.width && !a.height;
            return p && !a.left && a.top ? a.top = null : p && !a.top && a.left && (a.left = null), a
        },
        _proportionallyResize: function () {
            var b = this.options;
            if (!this._proportionallyResizeElements.length) return;
            var c = this.helper || this.element;
            for (var d = 0; d < this._proportionallyResizeElements.length; d++) {
                var e = this._proportionallyResizeElements[d];
                if (!this.borderDif) {
                    var f = [e.css("borderTopWidth"), e.css("borderRightWidth"), e.css("borderBottomWidth"), e.css("borderLeftWidth")],
                        g = [e.css("paddingTop"), e.css("paddingRight"), e.css("paddingBottom"), e.css("paddingLeft")];
                    this.borderDif = a.map(f, function (a, b) {
                        var c = parseInt(a, 10) || 0,
                            d = parseInt(g[b], 10) || 0;
                        return c + d
                    })
                }
                if (!a.browser.msie || !a(c).is(":hidden") && !a(c).parents(":hidden").length) e.css({
                    height: c.height() - this.borderDif[0] - this.borderDif[2] || 0,
                    width: c.width() - this.borderDif[1] - this.borderDif[3] || 0
                });
                else continue
            }
        },
        _renderProxy: function () {
            var b = this.element,
                c = this.options;
            this.elementOffset = b.offset();
            if (this._helper) {
                this.helper = this.helper || a('<div style="overflow:hidden;"></div>');
                var d = a.browser.msie && a.browser.version < 7,
                    e = d ? 1 : 0,
                    f = d ? 2 : -1;
                this.helper.addClass(this._helper).css({
                    width: this.element.outerWidth() + f,
                    height: this.element.outerHeight() + f,
                    position: "absolute",
                    left: this.elementOffset.left - e + "px",
                    top: this.elementOffset.top - e + "px",
                    zIndex: ++c.zIndex
                }), this.helper.appendTo("body").disableSelection()
            } else this.helper = this.element
        },
        _change: {
            e: function (a, b, c) {
                return {
                    width: this.originalSize.width + b
                }
            },
            w: function (a, b, c) {
                var d = this.options,
                    e = this.originalSize,
                    f = this.originalPosition;
                return {
                    left: f.left + b,
                    width: e.width - b
                }
            },
            n: function (a, b, c) {
                var d = this.options,
                    e = this.originalSize,
                    f = this.originalPosition;
                return {
                    top: f.top + c,
                    height: e.height - c
                }
            },
            s: function (a, b, c) {
                return {
                    height: this.originalSize.height + c
                }
            },
            se: function (b, c, d) {
                return a.extend(this._change.s.apply(this, arguments), this._change.e.apply(this, [b, c, d]))
            },
            sw: function (b, c, d) {
                return a.extend(this._change.s.apply(this, arguments), this._change.w.apply(this, [b, c, d]))
            },
            ne: function (b, c, d) {
                return a.extend(this._change.n.apply(this, arguments), this._change.e.apply(this, [b, c, d]))
            },
            nw: function (b, c, d) {
                return a.extend(this._change.n.apply(this, arguments), this._change.w.apply(this, [b, c, d]))
            }
        },
        _propagate: function (b, c) {
            a.ui.plugin.call(this, b, [c, this.ui()]), b != "resize" && this._trigger(b, c, this.ui())
        },
        plugins: {},
        ui: function () {
            return {
                originalElement: this.originalElement,
                element: this.element,
                helper: this.helper,
                position: this.position,
                size: this.size,
                originalSize: this.originalSize,
                originalPosition: this.originalPosition
            }
        }
    }), a.extend(a.ui.resizable, {
        version: "1.8.21"
    }), a.ui.plugin.add("resizable", "alsoResize", {
        start: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = function (b) {
                    a(b).each(function () {
                        var b = a(this);
                        b.data("resizable-alsoresize", {
                            width: parseInt(b.width(), 10),
                            height: parseInt(b.height(), 10),
                            left: parseInt(b.css("left"), 10),
                            top: parseInt(b.css("top"), 10)
                        })
                    })
                };
            typeof e.alsoResize == "object" && !e.alsoResize.parentNode ? e.alsoResize.length ? (e.alsoResize = e.alsoResize[0], f(e.alsoResize)) : a.each(e.alsoResize, function (a) {
                f(a)
            }) : f(e.alsoResize)
        },
        resize: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = d.originalSize,
                g = d.originalPosition,
                h = {
                    height: d.size.height - f.height || 0,
                    width: d.size.width - f.width || 0,
                    top: d.position.top - g.top || 0,
                    left: d.position.left - g.left || 0
                }, i = function (b, d) {
                    a(b).each(function () {
                        var b = a(this),
                            e = a(this).data("resizable-alsoresize"),
                            f = {}, g = d && d.length ? d : b.parents(c.originalElement[0]).length ? ["width", "height"] : ["width", "height", "top", "left"];
                        a.each(g, function (a, b) {
                            var c = (e[b] || 0) + (h[b] || 0);
                            c && c >= 0 && (f[b] = c || null)
                        }), b.css(f)
                    })
                };
            typeof e.alsoResize == "object" && !e.alsoResize.nodeType ? a.each(e.alsoResize, function (a, b) {
                i(a, b)
            }) : i(e.alsoResize)
        },
        stop: function (b, c) {
            a(this).removeData("resizable-alsoresize")
        }
    }), a.ui.plugin.add("resizable", "animate", {
        stop: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = d._proportionallyResizeElements,
                g = f.length && /textarea/i.test(f[0].nodeName),
                h = g && a.ui.hasScroll(f[0], "left") ? 0 : d.sizeDiff.height,
                i = g ? 0 : d.sizeDiff.width,
                j = {
                    width: d.size.width - i,
                    height: d.size.height - h
                }, k = parseInt(d.element.css("left"), 10) + (d.position.left - d.originalPosition.left) || null,
                l = parseInt(d.element.css("top"), 10) + (d.position.top - d.originalPosition.top) || null;
            d.element.animate(a.extend(j, l && k ? {
                top: l,
                left: k
            } : {}), {
                duration: e.animateDuration,
                easing: e.animateEasing,
                step: function () {
                    var c = {
                        width: parseInt(d.element.css("width"), 10),
                        height: parseInt(d.element.css("height"), 10),
                        top: parseInt(d.element.css("top"), 10),
                        left: parseInt(d.element.css("left"), 10)
                    };
                    f && f.length && a(f[0]).css({
                        width: c.width,
                        height: c.height
                    }), d._updateCache(c), d._propagate("resize", b)
                }
            })
        }
    }), a.ui.plugin.add("resizable", "containment", {
        start: function (b, d) {
            var e = a(this).data("resizable"),
                f = e.options,
                g = e.element,
                h = f.containment,
                i = h instanceof a ? h.get(0) : /parent/.test(h) ? g.parent().get(0) : h;
            if (!i) return;
            e.containerElement = a(i);
            if (/document/.test(h) || h == document) e.containerOffset = {
                left: 0,
                top: 0
            }, e.containerPosition = {
                left: 0,
                top: 0
            }, e.parentData = {
                element: a(document),
                left: 0,
                top: 0,
                width: a(document).width(),
                height: a(document).height() || document.body.parentNode.scrollHeight
            };
            else {
                var j = a(i),
                    k = [];
                a(["Top", "Right", "Left", "Bottom"]).each(function (a, b) {
                    k[a] = c(j.css("padding" + b))
                }), e.containerOffset = j.offset(), e.containerPosition = j.position(), e.containerSize = {
                    height: j.innerHeight() - k[3],
                    width: j.innerWidth() - k[1]
                };
                var l = e.containerOffset,
                    m = e.containerSize.height,
                    n = e.containerSize.width,
                    o = a.ui.hasScroll(i, "left") ? i.scrollWidth : n,
                    p = a.ui.hasScroll(i) ? i.scrollHeight : m;
                e.parentData = {
                    element: i,
                    left: l.left,
                    top: l.top,
                    width: o,
                    height: p
                }
            }
        },
        resize: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = d.containerSize,
                g = d.containerOffset,
                h = d.size,
                i = d.position,
                j = d._aspectRatio || b.shiftKey,
                k = {
                    top: 0,
                    left: 0
                }, l = d.containerElement;
            l[0] != document && /static/.test(l.css("position")) && (k = g), i.left < (d._helper ? g.left : 0) && (d.size.width = d.size.width + (d._helper ? d.position.left - g.left : d.position.left - k.left), j && (d.size.height = d.size.width / d.aspectRatio), d.position.left = e.helper ? g.left : 0), i.top < (d._helper ? g.top : 0) && (d.size.height = d.size.height + (d._helper ? d.position.top - g.top : d.position.top), j && (d.size.width = d.size.height * d.aspectRatio), d.position.top = d._helper ? g.top : 0), d.offset.left = d.parentData.left + d.position.left, d.offset.top = d.parentData.top + d.position.top;
            var m = Math.abs((d._helper ? d.offset.left - k.left : d.offset.left - k.left) + d.sizeDiff.width),
                n = Math.abs((d._helper ? d.offset.top - k.top : d.offset.top - g.top) + d.sizeDiff.height),
                o = d.containerElement.get(0) == d.element.parent().get(0),
                p = /relative|absolute/.test(d.containerElement.css("position"));
            o && p && (m -= d.parentData.left), m + d.size.width >= d.parentData.width && (d.size.width = d.parentData.width - m, j && (d.size.height = d.size.width / d.aspectRatio)), n + d.size.height >= d.parentData.height && (d.size.height = d.parentData.height - n, j && (d.size.width = d.size.height * d.aspectRatio))
        },
        stop: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = d.position,
                g = d.containerOffset,
                h = d.containerPosition,
                i = d.containerElement,
                j = a(d.helper),
                k = j.offset(),
                l = j.outerWidth() - d.sizeDiff.width,
                m = j.outerHeight() - d.sizeDiff.height;
            d._helper && !e.animate && /relative/.test(i.css("position")) && a(this).css({
                left: k.left - h.left - g.left,
                width: l,
                height: m
            }), d._helper && !e.animate && /static/.test(i.css("position")) && a(this).css({
                left: k.left - h.left - g.left,
                width: l,
                height: m
            })
        }
    }), a.ui.plugin.add("resizable", "ghost", {
        start: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = d.size;
            d.ghost = d.originalElement.clone(), d.ghost.css({
                opacity: .25,
                display: "block",
                position: "relative",
                height: f.height,
                width: f.width,
                margin: 0,
                left: 0,
                top: 0
            }).addClass("ui-resizable-ghost").addClass(typeof e.ghost == "string" ? e.ghost : ""), d.ghost.appendTo(d.helper)
        },
        resize: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options;
            d.ghost && d.ghost.css({
                position: "relative",
                height: d.size.height,
                width: d.size.width
            })
        },
        stop: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options;
            d.ghost && d.helper && d.helper.get(0).removeChild(d.ghost.get(0))
        }
    }), a.ui.plugin.add("resizable", "grid", {
        resize: function (b, c) {
            var d = a(this).data("resizable"),
                e = d.options,
                f = d.size,
                g = d.originalSize,
                h = d.originalPosition,
                i = d.axis,
                j = e._aspectRatio || b.shiftKey;
            e.grid = typeof e.grid == "number" ? [e.grid, e.grid] : e.grid;
            var k = Math.round((f.width - g.width) / (e.grid[0] || 1)) * (e.grid[0] || 1),
                l = Math.round((f.height - g.height) / (e.grid[1] || 1)) * (e.grid[1] || 1);
            /^(se|s|e)$/.test(i) ? (d.size.width = g.width + k, d.size.height = g.height + l) : /^(ne)$/.test(i) ? (d.size.width = g.width + k, d.size.height = g.height + l, d.position.top = h.top - l) : /^(sw)$/.test(i) ? (d.size.width = g.width + k, d.size.height = g.height + l, d.position.left = h.left - k) : (d.size.width = g.width + k, d.size.height = g.height + l, d.position.top = h.top - l, d.position.left = h.left - k)
        }
    });
    var c = function (a) {
        return parseInt(a, 10) || 0
    }, d = function (a) {
        return !isNaN(parseInt(a, 10))
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.selectable.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.selectable", a.ui.mouse, {
        options: {
            appendTo: "body",
            autoRefresh: !0,
            distance: 0,
            filter: "*",
            tolerance: "touch"
        },
        _create: function () {
            var b = this;
            this.element.addClass("ui-selectable"), this.dragged = !1;
            var c;
            this.refresh = function () {
                c = a(b.options.filter, b.element[0]), c.addClass("ui-selectee"), c.each(function () {
                    var b = a(this),
                        c = b.offset();
                    a.data(this, "selectable-item", {
                        element: this,
                        $element: b,
                        left: c.left,
                        top: c.top,
                        right: c.left + b.outerWidth(),
                        bottom: c.top + b.outerHeight(),
                        startselected: !1,
                        selected: b.hasClass("ui-selected"),
                        selecting: b.hasClass("ui-selecting"),
                        unselecting: b.hasClass("ui-unselecting")
                    })
                })
            }, this.refresh(), this.selectees = c.addClass("ui-selectee"), this._mouseInit(), this.helper = a("<div class='ui-selectable-helper'></div>")
        },
        destroy: function () {
            return this.selectees.removeClass("ui-selectee").removeData("selectable-item"), this.element.removeClass("ui-selectable ui-selectable-disabled").removeData("selectable").unbind(".selectable"), this._mouseDestroy(), this
        },
        _mouseStart: function (b) {
            var c = this;
            this.opos = [b.pageX, b.pageY];
            if (this.options.disabled) return;
            var d = this.options;
            this.selectees = a(d.filter, this.element[0]), this._trigger("start", b), a(d.appendTo).append(this.helper), this.helper.css({
                left: b.clientX,
                top: b.clientY,
                width: 0,
                height: 0
            }), d.autoRefresh && this.refresh(), this.selectees.filter(".ui-selected").each(function () {
                var d = a.data(this, "selectable-item");
                d.startselected = !0, !b.metaKey && !b.ctrlKey && (d.$element.removeClass("ui-selected"), d.selected = !1, d.$element.addClass("ui-unselecting"), d.unselecting = !0, c._trigger("unselecting", b, {
                    unselecting: d.element
                }))
            }), a(b.target).parents().andSelf().each(function () {
                var d = a.data(this, "selectable-item");
                if (d) {
                    var e = !b.metaKey && !b.ctrlKey || !d.$element.hasClass("ui-selected");
                    return d.$element.removeClass(e ? "ui-unselecting" : "ui-selected").addClass(e ? "ui-selecting" : "ui-unselecting"), d.unselecting = !e, d.selecting = e, d.selected = e, e ? c._trigger("selecting", b, {
                        selecting: d.element
                    }) : c._trigger("unselecting", b, {
                        unselecting: d.element
                    }), !1
                }
            })
        },
        _mouseDrag: function (b) {
            var c = this;
            this.dragged = !0;
            if (this.options.disabled) return;
            var d = this.options,
                e = this.opos[0],
                f = this.opos[1],
                g = b.pageX,
                h = b.pageY;
            if (e > g) {
                var i = g;
                g = e, e = i
            }
            if (f > h) {
                var i = h;
                h = f, f = i
            }
            return this.helper.css({
                left: e,
                top: f,
                width: g - e,
                height: h - f
            }), this.selectees.each(function () {
                var i = a.data(this, "selectable-item");
                if (!i || i.element == c.element[0]) return;
                var j = !1;
                d.tolerance == "touch" ? j = !(i.left > g || i.right < e || i.top > h || i.bottom < f) : d.tolerance == "fit" && (j = i.left > e && i.right < g && i.top > f && i.bottom < h), j ? (i.selected && (i.$element.removeClass("ui-selected"), i.selected = !1), i.unselecting && (i.$element.removeClass("ui-unselecting"), i.unselecting = !1), i.selecting || (i.$element.addClass("ui-selecting"), i.selecting = !0, c._trigger("selecting", b, {
                    selecting: i.element
                }))) : (i.selecting && ((b.metaKey || b.ctrlKey) && i.startselected ? (i.$element.removeClass("ui-selecting"), i.selecting = !1, i.$element.addClass("ui-selected"), i.selected = !0) : (i.$element.removeClass("ui-selecting"), i.selecting = !1, i.startselected && (i.$element.addClass("ui-unselecting"), i.unselecting = !0), c._trigger("unselecting", b, {
                    unselecting: i.element
                }))), i.selected && !b.metaKey && !b.ctrlKey && !i.startselected && (i.$element.removeClass("ui-selected"), i.selected = !1, i.$element.addClass("ui-unselecting"), i.unselecting = !0, c._trigger("unselecting", b, {
                    unselecting: i.element
                })))
            }), !1
        },
        _mouseStop: function (b) {
            var c = this;
            this.dragged = !1;
            var d = this.options;
            return a(".ui-unselecting", this.element[0]).each(function () {
                var d = a.data(this, "selectable-item");
                d.$element.removeClass("ui-unselecting"), d.unselecting = !1, d.startselected = !1, c._trigger("unselected", b, {
                    unselected: d.element
                })
            }), a(".ui-selecting", this.element[0]).each(function () {
                var d = a.data(this, "selectable-item");
                d.$element.removeClass("ui-selecting").addClass("ui-selected"), d.selecting = !1, d.selected = !0, d.startselected = !0, c._trigger("selected", b, {
                    selected: d.element
                })
            }), this._trigger("stop", b), this.helper.remove(), !1
        }
    }), a.extend(a.ui.selectable, {
        version: "1.8.21"
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.sortable.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.sortable", a.ui.mouse, {
        widgetEventPrefix: "sort",
        ready: !1,
        options: {
            appendTo: "parent",
            axis: !1,
            connectWith: !1,
            containment: !1,
            cursor: "auto",
            cursorAt: !1,
            dropOnEmpty: !0,
            forcePlaceholderSize: !1,
            forceHelperSize: !1,
            grid: !1,
            handle: !1,
            helper: "original",
            items: "> *",
            opacity: !1,
            placeholder: !1,
            revert: !1,
            scroll: !0,
            scrollSensitivity: 20,
            scrollSpeed: 20,
            scope: "default",
            tolerance: "intersect",
            zIndex: 1e3
        },
        _create: function () {
            var a = this.options;
            this.containerCache = {}, this.element.addClass("ui-sortable"), this.refresh(), this.floating = this.items.length ? a.axis === "x" || /left|right/.test(this.items[0].item.css("float")) || /inline|table-cell/.test(this.items[0].item.css("display")) : !1, this.offset = this.element.offset(), this._mouseInit(), this.ready = !0
        },
        destroy: function () {
            a.Widget.prototype.destroy.call(this), this.element.removeClass("ui-sortable ui-sortable-disabled"), this._mouseDestroy();
            for (var b = this.items.length - 1; b >= 0; b--) this.items[b].item.removeData(this.widgetName + "-item");
            return this
        },
        _setOption: function (b, c) {
            b === "disabled" ? (this.options[b] = c, this.widget()[c ? "addClass" : "removeClass"]("ui-sortable-disabled")) : a.Widget.prototype._setOption.apply(this, arguments)
        },
        _mouseCapture: function (b, c) {
            var d = this;
            if (this.reverting) return !1;
            if (this.options.disabled || this.options.type == "static") return !1;
            this._refreshItems(b);
            var e = null,
                f = this,
                g = a(b.target).parents().each(function () {
                    if (a.data(this, d.widgetName + "-item") == f) return e = a(this), !1
                });
            a.data(b.target, d.widgetName + "-item") == f && (e = a(b.target));
            if (!e) return !1;
            if (this.options.handle && !c) {
                var h = !1;
                a(this.options.handle, e).find("*").andSelf().each(function () {
                    this == b.target && (h = !0)
                });
                if (!h) return !1
            }
            return this.currentItem = e, this._removeCurrentsFromItems(), !0
        },
        _mouseStart: function (b, c, d) {
            var e = this.options,
                f = this;
            this.currentContainer = this, this.refreshPositions(), this.helper = this._createHelper(b), this._cacheHelperProportions(), this._cacheMargins(), this.scrollParent = this.helper.scrollParent(), this.offset = this.currentItem.offset(), this.offset = {
                top: this.offset.top - this.margins.top,
                left: this.offset.left - this.margins.left
            }, a.extend(this.offset, {
                click: {
                    left: b.pageX - this.offset.left,
                    top: b.pageY - this.offset.top
                },
                parent: this._getParentOffset(),
                relative: this._getRelativeOffset()
            }), this.helper.css("position", "absolute"), this.cssPosition = this.helper.css("position"), this.originalPosition = this._generatePosition(b), this.originalPageX = b.pageX, this.originalPageY = b.pageY, e.cursorAt && this._adjustOffsetFromHelper(e.cursorAt), this.domPosition = {
                prev: this.currentItem.prev()[0],
                parent: this.currentItem.parent()[0]
            }, this.helper[0] != this.currentItem[0] && this.currentItem.hide(), this._createPlaceholder(), e.containment && this._setContainment(), e.cursor && (a("body").css("cursor") && (this._storedCursor = a("body").css("cursor")), a("body").css("cursor", e.cursor)), e.opacity && (this.helper.css("opacity") && (this._storedOpacity = this.helper.css("opacity")), this.helper.css("opacity", e.opacity)), e.zIndex && (this.helper.css("zIndex") && (this._storedZIndex = this.helper.css("zIndex")), this.helper.css("zIndex", e.zIndex)), this.scrollParent[0] != document && this.scrollParent[0].tagName != "HTML" && (this.overflowOffset = this.scrollParent.offset()), this._trigger("start", b, this._uiHash()), this._preserveHelperProportions || this._cacheHelperProportions();
            if (!d)
                for (var g = this.containers.length - 1; g >= 0; g--) this.containers[g]._trigger("activate", b, f._uiHash(this));
            return a.ui.ddmanager && (a.ui.ddmanager.current = this), a.ui.ddmanager && !e.dropBehaviour && a.ui.ddmanager.prepareOffsets(this, b), this.dragging = !0, this.helper.addClass("ui-sortable-helper"), this._mouseDrag(b), !0
        },
        _mouseDrag: function (b) {
            this.position = this._generatePosition(b), this.positionAbs = this._convertPositionTo("absolute"), this.lastPositionAbs || (this.lastPositionAbs = this.positionAbs);
            if (this.options.scroll) {
                var c = this.options,
                    d = !1;
                this.scrollParent[0] != document && this.scrollParent[0].tagName != "HTML" ? (this.overflowOffset.top + this.scrollParent[0].offsetHeight - b.pageY < c.scrollSensitivity ? this.scrollParent[0].scrollTop = d = this.scrollParent[0].scrollTop + c.scrollSpeed : b.pageY - this.overflowOffset.top < c.scrollSensitivity && (this.scrollParent[0].scrollTop = d = this.scrollParent[0].scrollTop - c.scrollSpeed), this.overflowOffset.left + this.scrollParent[0].offsetWidth - b.pageX < c.scrollSensitivity ? this.scrollParent[0].scrollLeft = d = this.scrollParent[0].scrollLeft + c.scrollSpeed : b.pageX - this.overflowOffset.left < c.scrollSensitivity && (this.scrollParent[0].scrollLeft = d = this.scrollParent[0].scrollLeft - c.scrollSpeed)) : (b.pageY - a(document).scrollTop() < c.scrollSensitivity ? d = a(document).scrollTop(a(document).scrollTop() - c.scrollSpeed) : a(window).height() - (b.pageY - a(document).scrollTop()) < c.scrollSensitivity && (d = a(document).scrollTop(a(document).scrollTop() + c.scrollSpeed)), b.pageX - a(document).scrollLeft() < c.scrollSensitivity ? d = a(document).scrollLeft(a(document).scrollLeft() - c.scrollSpeed) : a(window).width() - (b.pageX - a(document).scrollLeft()) < c.scrollSensitivity && (d = a(document).scrollLeft(a(document).scrollLeft() + c.scrollSpeed))), d !== !1 && a.ui.ddmanager && !c.dropBehaviour && a.ui.ddmanager.prepareOffsets(this, b)
            }
            this.positionAbs = this._convertPositionTo("absolute");
            if (!this.options.axis || this.options.axis != "y") this.helper[0].style.left = this.position.left + "px";
            if (!this.options.axis || this.options.axis != "x") this.helper[0].style.top = this.position.top + "px";
            for (var e = this.items.length - 1; e >= 0; e--) {
                var f = this.items[e],
                    g = f.item[0],
                    h = this._intersectsWithPointer(f);
                if (!h) continue;
                if (g != this.currentItem[0] && this.placeholder[h == 1 ? "next" : "prev"]()[0] != g && !a.ui.contains(this.placeholder[0], g) && (this.options.type == "semi-dynamic" ? !a.ui.contains(this.element[0], g) : !0)) {
                    this.direction = h == 1 ? "down" : "up";
                    if (this.options.tolerance == "pointer" || this._intersectsWithSides(f)) this._rearrange(b, f);
                    else break;
                    this._trigger("change", b, this._uiHash());
                    break
                }
            }
            return this._contactContainers(b), a.ui.ddmanager && a.ui.ddmanager.drag(this, b), this._trigger("sort", b, this._uiHash()), this.lastPositionAbs = this.positionAbs, !1
        },
        _mouseStop: function (b, c) {
            if (!b) return;
            a.ui.ddmanager && !this.options.dropBehaviour && a.ui.ddmanager.drop(this, b);
            if (this.options.revert) {
                var d = this,
                    e = d.placeholder.offset();
                d.reverting = !0, a(this.helper).animate({
                    left: e.left - this.offset.parent.left - d.margins.left + (this.offsetParent[0] == document.body ? 0 : this.offsetParent[0].scrollLeft),
                    top: e.top - this.offset.parent.top - d.margins.top + (this.offsetParent[0] == document.body ? 0 : this.offsetParent[0].scrollTop)
                }, parseInt(this.options.revert, 10) || 500, function () {
                    d._clear(b)
                })
            } else this._clear(b, c);
            return !1
        },
        cancel: function () {
            var b = this;
            if (this.dragging) {
                this._mouseUp({
                    target: null
                }), this.options.helper == "original" ? this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper") : this.currentItem.show();
                for (var c = this.containers.length - 1; c >= 0; c--) this.containers[c]._trigger("deactivate", null, b._uiHash(this)), this.containers[c].containerCache.over && (this.containers[c]._trigger("out", null, b._uiHash(this)), this.containers[c].containerCache.over = 0)
            }
            return this.placeholder && (this.placeholder[0].parentNode && this.placeholder[0].parentNode.removeChild(this.placeholder[0]), this.options.helper != "original" && this.helper && this.helper[0].parentNode && this.helper.remove(), a.extend(this, {
                helper: null,
                dragging: !1,
                reverting: !1,
                _noFinalSort: null
            }), this.domPosition.prev ? a(this.domPosition.prev).after(this.currentItem) : a(this.domPosition.parent).prepend(this.currentItem)), this
        },
        serialize: function (b) {
            var c = this._getItemsAsjQuery(b && b.connected),
                d = [];
            return b = b || {}, a(c).each(function () {
                var c = (a(b.item || this).attr(b.attribute || "id") || "").match(b.expression || /(.+)[-=_](.+)/);
                c && d.push((b.key || c[1] + "[]") + "=" + (b.key && b.expression ? c[1] : c[2]))
            }), !d.length && b.key && d.push(b.key + "="), d.join("&")
        },
        toArray: function (b) {
            var c = this._getItemsAsjQuery(b && b.connected),
                d = [];
            return b = b || {}, c.each(function () {
                d.push(a(b.item || this).attr(b.attribute || "id") || "")
            }), d
        },
        _intersectsWith: function (a) {
            var b = this.positionAbs.left,
                c = b + this.helperProportions.width,
                d = this.positionAbs.top,
                e = d + this.helperProportions.height,
                f = a.left,
                g = f + a.width,
                h = a.top,
                i = h + a.height,
                j = this.offset.click.top,
                k = this.offset.click.left,
                l = d + j > h && d + j < i && b + k > f && b + k < g;
            return this.options.tolerance == "pointer" || this.options.forcePointerForContainers || this.options.tolerance != "pointer" && this.helperProportions[this.floating ? "width" : "height"] > a[this.floating ? "width" : "height"] ? l : f < b + this.helperProportions.width / 2 && c - this.helperProportions.width / 2 < g && h < d + this.helperProportions.height / 2 && e - this.helperProportions.height / 2 < i
        },
        _intersectsWithPointer: function (b) {
            var c = this.options.axis === "x" || a.ui.isOverAxis(this.positionAbs.top + this.offset.click.top, b.top, b.height),
                d = this.options.axis === "y" || a.ui.isOverAxis(this.positionAbs.left + this.offset.click.left, b.left, b.width),
                e = c && d,
                f = this._getDragVerticalDirection(),
                g = this._getDragHorizontalDirection();
            return e ? this.floating ? g && g == "right" || f == "down" ? 2 : 1 : f && (f == "down" ? 2 : 1) : !1
        },
        _intersectsWithSides: function (b) {
            var c = a.ui.isOverAxis(this.positionAbs.top + this.offset.click.top, b.top + b.height / 2, b.height),
                d = a.ui.isOverAxis(this.positionAbs.left + this.offset.click.left, b.left + b.width / 2, b.width),
                e = this._getDragVerticalDirection(),
                f = this._getDragHorizontalDirection();
            return this.floating && f ? f == "right" && d || f == "left" && !d : e && (e == "down" && c || e == "up" && !c)
        },
        _getDragVerticalDirection: function () {
            var a = this.positionAbs.top - this.lastPositionAbs.top;
            return a != 0 && (a > 0 ? "down" : "up")
        },
        _getDragHorizontalDirection: function () {
            var a = this.positionAbs.left - this.lastPositionAbs.left;
            return a != 0 && (a > 0 ? "right" : "left")
        },
        refresh: function (a) {
            return this._refreshItems(a), this.refreshPositions(), this
        },
        _connectWith: function () {
            var a = this.options;
            return a.connectWith.constructor == String ? [a.connectWith] : a.connectWith
        },
        _getItemsAsjQuery: function (b) {
            var c = this,
                d = [],
                e = [],
                f = this._connectWith();
            if (f && b)
                for (var g = f.length - 1; g >= 0; g--) {
                    var h = a(f[g]);
                    for (var i = h.length - 1; i >= 0; i--) {
                        var j = a.data(h[i], this.widgetName);
                        j && j != this && !j.options.disabled && e.push([a.isFunction(j.options.items) ? j.options.items.call(j.element) : a(j.options.items, j.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), j])
                    }
                }
            e.push([a.isFunction(this.options.items) ? this.options.items.call(this.element, null, {
                options: this.options,
                item: this.currentItem
            }) : a(this.options.items, this.element).not(".ui-sortable-helper").not(".ui-sortable-placeholder"), this]);
            for (var g = e.length - 1; g >= 0; g--) e[g][0].each(function () {
                d.push(this)
            });
            return a(d)
        },
        _removeCurrentsFromItems: function () {
            var a = this.currentItem.find(":data(" + this.widgetName + "-item)");
            for (var b = 0; b < this.items.length; b++)
                for (var c = 0; c < a.length; c++) a[c] == this.items[b].item[0] && this.items.splice(b, 1)
        },
        _refreshItems: function (b) {
            this.items = [], this.containers = [this];
            var c = this.items,
                d = this,
                e = [
                    [a.isFunction(this.options.items) ? this.options.items.call(this.element[0], b, {
                        item: this.currentItem
                    }) : a(this.options.items, this.element), this]
                ],
                f = this._connectWith();
            if (f && this.ready)
                for (var g = f.length - 1; g >= 0; g--) {
                    var h = a(f[g]);
                    for (var i = h.length - 1; i >= 0; i--) {
                        var j = a.data(h[i], this.widgetName);
                        j && j != this && !j.options.disabled && (e.push([a.isFunction(j.options.items) ? j.options.items.call(j.element[0], b, {
                            item: this.currentItem
                        }) : a(j.options.items, j.element), j]), this.containers.push(j))
                    }
                }
            for (var g = e.length - 1; g >= 0; g--) {
                var k = e[g][1],
                    l = e[g][0];
                for (var i = 0, m = l.length; i < m; i++) {
                    var n = a(l[i]);
                    n.data(this.widgetName + "-item", k), c.push({
                        item: n,
                        instance: k,
                        width: 0,
                        height: 0,
                        left: 0,
                        top: 0
                    })
                }
            }
        },
        refreshPositions: function (b) {
            this.offsetParent && this.helper && (this.offset.parent = this._getParentOffset());
            for (var c = this.items.length - 1; c >= 0; c--) {
                var d = this.items[c];
                if (d.instance != this.currentContainer && this.currentContainer && d.item[0] != this.currentItem[0]) continue;
                var e = this.options.toleranceElement ? a(this.options.toleranceElement, d.item) : d.item;
                b || (d.width = e.outerWidth(), d.height = e.outerHeight());
                var f = e.offset();
                d.left = f.left, d.top = f.top
            }
            if (this.options.custom && this.options.custom.refreshContainers) this.options.custom.refreshContainers.call(this);
            else
                for (var c = this.containers.length - 1; c >= 0; c--) {
                    var f = this.containers[c].element.offset();
                    this.containers[c].containerCache.left = f.left, this.containers[c].containerCache.top = f.top, this.containers[c].containerCache.width = this.containers[c].element.outerWidth(), this.containers[c].containerCache.height = this.containers[c].element.outerHeight()
                }
            return this
        },
        _createPlaceholder: function (b) {
            var c = b || this,
                d = c.options;
            if (!d.placeholder || d.placeholder.constructor == String) {
                var e = d.placeholder;
                d.placeholder = {
                    element: function () {
                        var b = a(document.createElement(c.currentItem[0].nodeName)).addClass(e || c.currentItem[0].className + " ui-sortable-placeholder").removeClass("ui-sortable-helper")[0];
                        return e || (b.style.visibility = "hidden"), b
                    },
                    update: function (a, b) {
                        if (e && !d.forcePlaceholderSize) return;
                        b.height() || b.height(c.currentItem.innerHeight() - parseInt(c.currentItem.css("paddingTop") || 0, 10) - parseInt(c.currentItem.css("paddingBottom") || 0, 10)), b.width() || b.width(c.currentItem.innerWidth() - parseInt(c.currentItem.css("paddingLeft") || 0, 10) - parseInt(c.currentItem.css("paddingRight") || 0, 10))
                    }
                }
            }
            c.placeholder = a(d.placeholder.element.call(c.element, c.currentItem)), c.currentItem.after(c.placeholder), d.placeholder.update(c, c.placeholder)
        },
        _contactContainers: function (b) {
            var c = null,
                d = null;
            for (var e = this.containers.length - 1; e >= 0; e--) {
                if (a.ui.contains(this.currentItem[0], this.containers[e].element[0])) continue;
                if (this._intersectsWith(this.containers[e].containerCache)) {
                    if (c && a.ui.contains(this.containers[e].element[0], c.element[0])) continue;
                    c = this.containers[e], d = e
                } else this.containers[e].containerCache.over && (this.containers[e]._trigger("out", b, this._uiHash(this)), this.containers[e].containerCache.over = 0)
            }
            if (!c) return;
            if (this.containers.length === 1) this.containers[d]._trigger("over", b, this._uiHash(this)), this.containers[d].containerCache.over = 1;
            else if (this.currentContainer != this.containers[d]) {
                var f = 1e4,
                    g = null,
                    h = this.positionAbs[this.containers[d].floating ? "left" : "top"];
                for (var i = this.items.length - 1; i >= 0; i--) {
                    if (!a.ui.contains(this.containers[d].element[0], this.items[i].item[0])) continue;
                    var j = this.containers[d].floating ? this.items[i].item.offset().left : this.items[i].item.offset().top;
                    Math.abs(j - h) < f && (f = Math.abs(j - h), g = this.items[i], this.direction = j - h > 0 ? "down" : "up")
                }
                if (!g && !this.options.dropOnEmpty) return;
                this.currentContainer = this.containers[d], g ? this._rearrange(b, g, null, !0) : this._rearrange(b, null, this.containers[d].element, !0), this._trigger("change", b, this._uiHash()), this.containers[d]._trigger("change", b, this._uiHash(this)), this.options.placeholder.update(this.currentContainer, this.placeholder), this.containers[d]._trigger("over", b, this._uiHash(this)), this.containers[d].containerCache.over = 1
            }
        },
        _createHelper: function (b) {
            var c = this.options,
                d = a.isFunction(c.helper) ? a(c.helper.apply(this.element[0], [b, this.currentItem])) : c.helper == "clone" ? this.currentItem.clone() : this.currentItem;
            return d.parents("body").length || a(c.appendTo != "parent" ? c.appendTo : this.currentItem[0].parentNode)[0].appendChild(d[0]), d[0] == this.currentItem[0] && (this._storedCSS = {
                width: this.currentItem[0].style.width,
                height: this.currentItem[0].style.height,
                position: this.currentItem.css("position"),
                top: this.currentItem.css("top"),
                left: this.currentItem.css("left")
            }), (d[0].style.width == "" || c.forceHelperSize) && d.width(this.currentItem.width()), (d[0].style.height == "" || c.forceHelperSize) && d.height(this.currentItem.height()), d
        },
        _adjustOffsetFromHelper: function (b) {
            typeof b == "string" && (b = b.split(" ")), a.isArray(b) && (b = {
                left: +b[0],
                top: +b[1] || 0
            }), "left" in b && (this.offset.click.left = b.left + this.margins.left), "right" in b && (this.offset.click.left = this.helperProportions.width - b.right + this.margins.left), "top" in b && (this.offset.click.top = b.top + this.margins.top), "bottom" in b && (this.offset.click.top = this.helperProportions.height - b.bottom + this.margins.top)
        },
        _getParentOffset: function () {
            this.offsetParent = this.helper.offsetParent();
            var b = this.offsetParent.offset();
            this.cssPosition == "absolute" && this.scrollParent[0] != document && a.ui.contains(this.scrollParent[0], this.offsetParent[0]) && (b.left += this.scrollParent.scrollLeft(), b.top += this.scrollParent.scrollTop());
            if (this.offsetParent[0] == document.body || this.offsetParent[0].tagName && this.offsetParent[0].tagName.toLowerCase() == "html" && a.browser.msie) b = {
                top: 0,
                left: 0
            };
            return {
                top: b.top + (parseInt(this.offsetParent.css("borderTopWidth"), 10) || 0),
                left: b.left + (parseInt(this.offsetParent.css("borderLeftWidth"), 10) || 0)
            }
        },
        _getRelativeOffset: function () {
            if (this.cssPosition == "relative") {
                var a = this.currentItem.position();
                return {
                    top: a.top - (parseInt(this.helper.css("top"), 10) || 0) + this.scrollParent.scrollTop(),
                    left: a.left - (parseInt(this.helper.css("left"), 10) || 0) + this.scrollParent.scrollLeft()
                }
            }
            return {
                top: 0,
                left: 0
            }
        },
        _cacheMargins: function () {
            this.margins = {
                left: parseInt(this.currentItem.css("marginLeft"), 10) || 0,
                top: parseInt(this.currentItem.css("marginTop"), 10) || 0
            }
        },
        _cacheHelperProportions: function () {
            this.helperProportions = {
                width: this.helper.outerWidth(),
                height: this.helper.outerHeight()
            }
        },
        _setContainment: function () {
            var b = this.options;
            b.containment == "parent" && (b.containment = this.helper[0].parentNode);
            if (b.containment == "document" || b.containment == "window") this.containment = [0 - this.offset.relative.left - this.offset.parent.left, 0 - this.offset.relative.top - this.offset.parent.top, a(b.containment == "document" ? document : window).width() - this.helperProportions.width - this.margins.left, (a(b.containment == "document" ? document : window).height() || document.body.parentNode.scrollHeight) - this.helperProportions.height - this.margins.top];
            if (!/^(document|window|parent)$/.test(b.containment)) {
                var c = a(b.containment)[0],
                    d = a(b.containment).offset(),
                    e = a(c).css("overflow") != "hidden";
                this.containment = [d.left + (parseInt(a(c).css("borderLeftWidth"), 10) || 0) + (parseInt(a(c).css("paddingLeft"), 10) || 0) - this.margins.left, d.top + (parseInt(a(c).css("borderTopWidth"), 10) || 0) + (parseInt(a(c).css("paddingTop"), 10) || 0) - this.margins.top, d.left + (e ? Math.max(c.scrollWidth, c.offsetWidth) : c.offsetWidth) - (parseInt(a(c).css("borderLeftWidth"), 10) || 0) - (parseInt(a(c).css("paddingRight"), 10) || 0) - this.helperProportions.width - this.margins.left, d.top + (e ? Math.max(c.scrollHeight, c.offsetHeight) : c.offsetHeight) - (parseInt(a(c).css("borderTopWidth"), 10) || 0) - (parseInt(a(c).css("paddingBottom"), 10) || 0) - this.helperProportions.height - this.margins.top]
            }
        },
        _convertPositionTo: function (b, c) {
            c || (c = this.position);
            var d = b == "absolute" ? 1 : -1,
                e = this.options,
                f = this.cssPosition == "absolute" && (this.scrollParent[0] == document || !a.ui.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent,
                g = /(html|body)/i.test(f[0].tagName);
            return {
                top: c.top + this.offset.relative.top * d + this.offset.parent.top * d - (a.browser.safari && this.cssPosition == "fixed" ? 0 : (this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : g ? 0 : f.scrollTop()) * d),
                left: c.left + this.offset.relative.left * d + this.offset.parent.left * d - (a.browser.safari && this.cssPosition == "fixed" ? 0 : (this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : g ? 0 : f.scrollLeft()) * d)
            }
        },
        _generatePosition: function (b) {
            var c = this.options,
                d = this.cssPosition == "absolute" && (this.scrollParent[0] == document || !a.ui.contains(this.scrollParent[0], this.offsetParent[0])) ? this.offsetParent : this.scrollParent,
                e = /(html|body)/i.test(d[0].tagName);
            this.cssPosition == "relative" && (this.scrollParent[0] == document || this.scrollParent[0] == this.offsetParent[0]) && (this.offset.relative = this._getRelativeOffset());
            var f = b.pageX,
                g = b.pageY;
            if (this.originalPosition) {
                this.containment && (b.pageX - this.offset.click.left < this.containment[0] && (f = this.containment[0] + this.offset.click.left), b.pageY - this.offset.click.top < this.containment[1] && (g = this.containment[1] + this.offset.click.top), b.pageX - this.offset.click.left > this.containment[2] && (f = this.containment[2] + this.offset.click.left), b.pageY - this.offset.click.top > this.containment[3] && (g = this.containment[3] + this.offset.click.top));
                if (c.grid) {
                    var h = this.originalPageY + Math.round((g - this.originalPageY) / c.grid[1]) * c.grid[1];
                    g = this.containment ? h - this.offset.click.top < this.containment[1] || h - this.offset.click.top > this.containment[3] ? h - this.offset.click.top < this.containment[1] ? h + c.grid[1] : h - c.grid[1] : h : h;
                    var i = this.originalPageX + Math.round((f - this.originalPageX) / c.grid[0]) * c.grid[0];
                    f = this.containment ? i - this.offset.click.left < this.containment[0] || i - this.offset.click.left > this.containment[2] ? i - this.offset.click.left < this.containment[0] ? i + c.grid[0] : i - c.grid[0] : i : i
                }
            }
            return {
                top: g - this.offset.click.top - this.offset.relative.top - this.offset.parent.top + (a.browser.safari && this.cssPosition == "fixed" ? 0 : this.cssPosition == "fixed" ? -this.scrollParent.scrollTop() : e ? 0 : d.scrollTop()),
                left: f - this.offset.click.left - this.offset.relative.left - this.offset.parent.left + (a.browser.safari && this.cssPosition == "fixed" ? 0 : this.cssPosition == "fixed" ? -this.scrollParent.scrollLeft() : e ? 0 : d.scrollLeft())
            }
        },
        _rearrange: function (a, b, c, d) {
            c ? c[0].appendChild(this.placeholder[0]) : b.item[0].parentNode.insertBefore(this.placeholder[0], this.direction == "down" ? b.item[0] : b.item[0].nextSibling), this.counter = this.counter ? ++this.counter : 1;
            var e = this,
                f = this.counter;
            window.setTimeout(function () {
                f == e.counter && e.refreshPositions(!d)
            }, 0)
        },
        _clear: function (b, c) {
            this.reverting = !1;
            var d = [],
                e = this;
            !this._noFinalSort && this.currentItem.parent().length && this.placeholder.before(this.currentItem), this._noFinalSort = null;
            if (this.helper[0] == this.currentItem[0]) {
                for (var f in this._storedCSS)
                    if (this._storedCSS[f] == "auto" || this._storedCSS[f] == "static") this._storedCSS[f] = "";
                this.currentItem.css(this._storedCSS).removeClass("ui-sortable-helper")
            } else this.currentItem.show();
            this.fromOutside && !c && d.push(function (a) {
                this._trigger("receive", a, this._uiHash(this.fromOutside))
            }), (this.fromOutside || this.domPosition.prev != this.currentItem.prev().not(".ui-sortable-helper")[0] || this.domPosition.parent != this.currentItem.parent()[0]) && !c && d.push(function (a) {
                this._trigger("update", a, this._uiHash())
            });
            if (!a.ui.contains(this.element[0], this.currentItem[0])) {
                c || d.push(function (a) {
                    this._trigger("remove", a, this._uiHash())
                });
                for (var f = this.containers.length - 1; f >= 0; f--) a.ui.contains(this.containers[f].element[0], this.currentItem[0]) && !c && (d.push(function (a) {
                    return function (b) {
                        a._trigger("receive", b, this._uiHash(this))
                    }
                }.call(this, this.containers[f])), d.push(function (a) {
                    return function (b) {
                        a._trigger("update", b, this._uiHash(this))
                    }
                }.call(this, this.containers[f])))
            }
            for (var f = this.containers.length - 1; f >= 0; f--) c || d.push(function (a) {
                return function (b) {
                    a._trigger("deactivate", b, this._uiHash(this))
                }
            }.call(this, this.containers[f])), this.containers[f].containerCache.over && (d.push(function (a) {
                return function (b) {
                    a._trigger("out", b, this._uiHash(this))
                }
            }.call(this, this.containers[f])), this.containers[f].containerCache.over = 0);
            this._storedCursor && a("body").css("cursor", this._storedCursor), this._storedOpacity && this.helper.css("opacity", this._storedOpacity), this._storedZIndex && this.helper.css("zIndex", this._storedZIndex == "auto" ? "" : this._storedZIndex), this.dragging = !1;
            if (this.cancelHelperRemoval) {
                if (!c) {
                    this._trigger("beforeStop", b, this._uiHash());
                    for (var f = 0; f < d.length; f++) d[f].call(this, b);
                    this._trigger("stop", b, this._uiHash())
                }
                return !1
            }
            c || this._trigger("beforeStop", b, this._uiHash()), this.placeholder[0].parentNode.removeChild(this.placeholder[0]), this.helper[0] != this.currentItem[0] && this.helper.remove(), this.helper = null;
            if (!c) {
                for (var f = 0; f < d.length; f++) d[f].call(this, b);
                this._trigger("stop", b, this._uiHash())
            }
            return this.fromOutside = !1, !0
        },
        _trigger: function () {
            a.Widget.prototype._trigger.apply(this, arguments) === !1 && this.cancel()
        },
        _uiHash: function (b) {
            var c = b || this;
            return {
                helper: c.helper,
                placeholder: c.placeholder || a([]),
                position: c.position,
                originalPosition: c.originalPosition,
                offset: c.positionAbs,
                item: c.currentItem,
                sender: b ? b.element : null
            }
        }
    }), a.extend(a.ui.sortable, {
        version: "1.8.21"
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.accordion.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.accordion", {
        options: {
            active: 0,
            animated: "slide",
            autoHeight: !0,
            clearStyle: !1,
            collapsible: !1,
            event: "click",
            fillSpace: !1,
            header: "> li > :first-child,> :not(li):even",
            icons: {
                header: "ui-icon-triangle-1-e",
                headerSelected: "ui-icon-triangle-1-s"
            },
            navigation: !1,
            navigationFilter: function () {
                return this.href.toLowerCase() === location.href.toLowerCase()
            }
        },
        _create: function () {
            var b = this,
                c = b.options;
            b.running = 0, b.element.addClass("ui-accordion ui-widget ui-helper-reset").children("li").addClass("ui-accordion-li-fix"), b.headers = b.element.find(c.header).addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all").bind("mouseenter.accordion", function () {
                if (c.disabled) return;
                a(this).addClass("ui-state-hover")
            }).bind("mouseleave.accordion", function () {
                if (c.disabled) return;
                a(this).removeClass("ui-state-hover")
            }).bind("focus.accordion", function () {
                if (c.disabled) return;
                a(this).addClass("ui-state-focus")
            }).bind("blur.accordion", function () {
                if (c.disabled) return;
                a(this).removeClass("ui-state-focus")
            }), b.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom");
            if (c.navigation) {
                var d = b.element.find("a").filter(c.navigationFilter).eq(0);
                if (d.length) {
                    var e = d.closest(".ui-accordion-header");
                    e.length ? b.active = e : b.active = d.closest(".ui-accordion-content").prev()
                }
            }
            b.active = b._findActive(b.active || c.active).addClass("ui-state-default ui-state-active").toggleClass("ui-corner-all").toggleClass("ui-corner-top"), b.active.next().addClass("ui-accordion-content-active"), b._createIcons(), b.resize(), b.element.attr("role", "tablist"), b.headers.attr("role", "tab").bind("keydown.accordion", function (a) {
                return b._keydown(a)
            }).next().attr("role", "tabpanel"), b.headers.not(b.active || "").attr({
                "aria-expanded": "false",
                "aria-selected": "false",
                tabIndex: -1
            }).next().hide(), b.active.length ? b.active.attr({
                "aria-expanded": "true",
                "aria-selected": "true",
                tabIndex: 0
            }) : b.headers.eq(0).attr("tabIndex", 0), a.browser.safari || b.headers.find("a").attr("tabIndex", -1), c.event && b.headers.bind(c.event.split(" ").join(".accordion ") + ".accordion", function (a) {
                b._clickHandler.call(b, a, this), a.preventDefault()
            })
        },
        _createIcons: function () {
            var b = this.options;
            b.icons && (a("<span></span>").addClass("ui-icon " + b.icons.header).prependTo(this.headers), this.active.children(".ui-icon").toggleClass(b.icons.header).toggleClass(b.icons.headerSelected), this.element.addClass("ui-accordion-icons"))
        },
        _destroyIcons: function () {
            this.headers.children(".ui-icon").remove(), this.element.removeClass("ui-accordion-icons")
        },
        destroy: function () {
            var b = this.options;
            this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role"), this.headers.unbind(".accordion").removeClass("ui-accordion-header ui-accordion-disabled ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-state-disabled ui-corner-top").removeAttr("role").removeAttr("aria-expanded").removeAttr("aria-selected").removeAttr("tabIndex"), this.headers.find("a").removeAttr("tabIndex"), this._destroyIcons();
            var c = this.headers.next().css("display", "").removeAttr("role").removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active ui-accordion-disabled ui-state-disabled");
            return (b.autoHeight || b.fillHeight) && c.css("height", ""), a.Widget.prototype.destroy.call(this)
        },
        _setOption: function (b, c) {
            a.Widget.prototype._setOption.apply(this, arguments), b == "active" && this.activate(c), b == "icons" && (this._destroyIcons(), c && this._createIcons()), b == "disabled" && this.headers.add(this.headers.next())[c ? "addClass" : "removeClass"]("ui-accordion-disabled ui-state-disabled")
        },
        _keydown: function (b) {
            if (this.options.disabled || b.altKey || b.ctrlKey) return;
            var c = a.ui.keyCode,
                d = this.headers.length,
                e = this.headers.index(b.target),
                f = !1;
            switch (b.keyCode) {
                case c.RIGHT:
                case c.DOWN:
                    f = this.headers[(e + 1) % d];
                    break;
                case c.LEFT:
                case c.UP:
                    f = this.headers[(e - 1 + d) % d];
                    break;
                case c.SPACE:
                case c.ENTER:
                    this._clickHandler({
                        target: b.target
                    }, b.target), b.preventDefault()
            }
            return f ? (a(b.target).attr("tabIndex", -1), a(f).attr("tabIndex", 0), f.focus(), !1) : !0
        },
        resize: function () {
            var b = this.options,
                c;
            if (b.fillSpace) {
                if (a.browser.msie) {
                    var d = this.element.parent().css("overflow");
                    this.element.parent().css("overflow", "hidden")
                }
                c = this.element.parent().height(), a.browser.msie && this.element.parent().css("overflow", d), this.headers.each(function () {
                    c -= a(this).outerHeight(!0)
                }), this.headers.next().each(function () {
                    a(this).height(Math.max(0, c - a(this).innerHeight() + a(this).height()))
                }).css("overflow", "auto")
            } else b.autoHeight && (c = 0, this.headers.next().each(function () {
                c = Math.max(c, a(this).height("").height())
            }).height(c));
            return this
        },
        activate: function (a) {
            this.options.active = a;
            var b = this._findActive(a)[0];
            return this._clickHandler({
                target: b
            }, b), this
        },
        _findActive: function (b) {
            return b ? typeof b == "number" ? this.headers.filter(":eq(" + b + ")") : this.headers.not(this.headers.not(b)) : b === !1 ? a([]) : this.headers.filter(":eq(0)")
        },
        _clickHandler: function (b, c) {
            var d = this.options;
            if (d.disabled) return;
            if (!b.target) {
                if (!d.collapsible) return;
                this.active.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").children(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header), this.active.next().addClass("ui-accordion-content-active");
                var e = this.active.next(),
                    f = {
                        options: d,
                        newHeader: a([]),
                        oldHeader: d.active,
                        newContent: a([]),
                        oldContent: e
                    }, g = this.active = a([]);
                this._toggle(g, e, f);
                return
            }
            var h = a(b.currentTarget || c),
                i = h[0] === this.active[0];
            d.active = d.collapsible && i ? !1 : this.headers.index(h);
            if (this.running || !d.collapsible && i) return;
            var j = this.active,
                g = h.next(),
                e = this.active.next(),
                f = {
                    options: d,
                    newHeader: i && d.collapsible ? a([]) : h,
                    oldHeader: this.active,
                    newContent: i && d.collapsible ? a([]) : g,
                    oldContent: e
                }, k = this.headers.index(this.active[0]) > this.headers.index(h[0]);
            this.active = i ? a([]) : h, this._toggle(g, e, f, i, k), j.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").children(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header), i || (h.removeClass("ui-state-default ui-corner-all").addClass("ui-state-active ui-corner-top").children(".ui-icon").removeClass(d.icons.header).addClass(d.icons.headerSelected), h.next().addClass("ui-accordion-content-active"));
            return
        },
        _toggle: function (b, c, d, e, f) {
            var g = this,
                h = g.options;
            g.toShow = b, g.toHide = c, g.data = d;
            var i = function () {
                if (!g) return;
                return g._completed.apply(g, arguments)
            };
            g._trigger("changestart", null, g.data), g.running = c.size() === 0 ? b.size() : c.size();
            if (h.animated) {
                var j = {};
                h.collapsible && e ? j = {
                    toShow: a([]),
                    toHide: c,
                    complete: i,
                    down: f,
                    autoHeight: h.autoHeight || h.fillSpace
                } : j = {
                    toShow: b,
                    toHide: c,
                    complete: i,
                    down: f,
                    autoHeight: h.autoHeight || h.fillSpace
                }, h.proxied || (h.proxied = h.animated), h.proxiedDuration || (h.proxiedDuration = h.duration), h.animated = a.isFunction(h.proxied) ? h.proxied(j) : h.proxied, h.duration = a.isFunction(h.proxiedDuration) ? h.proxiedDuration(j) : h.proxiedDuration;
                var k = a.ui.accordion.animations,
                    l = h.duration,
                    m = h.animated;
                m && !k[m] && !a.easing[m] && (m = "slide"), k[m] || (k[m] = function (a) {
                    this.slide(a, {
                        easing: m,
                        duration: l || 700
                    })
                }), k[m](j)
            } else h.collapsible && e ? b.toggle() : (c.hide(), b.show()), i(!0);
            c.prev().attr({
                "aria-expanded": "false",
                "aria-selected": "false",
                tabIndex: -1
            }).blur(), b.prev().attr({
                "aria-expanded": "true",
                "aria-selected": "true",
                tabIndex: 0
            }).focus()
        },
        _completed: function (a) {
            this.running = a ? 0 : --this.running;
            if (this.running) return;
            this.options.clearStyle && this.toShow.add(this.toHide).css({
                height: "",
                overflow: ""
            }), this.toHide.removeClass("ui-accordion-content-active"), this.toHide.length && (this.toHide.parent()[0].className = this.toHide.parent()[0].className), this._trigger("change", null, this.data)
        }
    }), a.extend(a.ui.accordion, {
        version: "1.8.21",
        animations: {
            slide: function (b, c) {
                b = a.extend({
                    easing: "swing",
                    duration: 300
                }, b, c);
                if (!b.toHide.size()) {
                    b.toShow.animate({
                        height: "show",
                        paddingTop: "show",
                        paddingBottom: "show"
                    }, b);
                    return
                }
                if (!b.toShow.size()) {
                    b.toHide.animate({
                        height: "hide",
                        paddingTop: "hide",
                        paddingBottom: "hide"
                    }, b);
                    return
                }
                var d = b.toShow.css("overflow"),
                    e = 0,
                    f = {}, g = {}, h = ["height", "paddingTop", "paddingBottom"],
                    i, j = b.toShow;
                i = j[0].style.width, j.width(j.parent().width() - parseFloat(j.css("paddingLeft")) - parseFloat(j.css("paddingRight")) - (parseFloat(j.css("borderLeftWidth")) || 0) - (parseFloat(j.css("borderRightWidth")) || 0)), a.each(h, function (c, d) {
                    g[d] = "hide";
                    var e = ("" + a.css(b.toShow[0], d)).match(/^([\d+-.]+)(.*)$/);
                    f[d] = {
                        value: e[1],
                        unit: e[2] || "px"
                    }
                }), b.toShow.css({
                    height: 0,
                    overflow: "hidden"
                }).show(), b.toHide.filter(":hidden").each(b.complete).end().filter(":visible").animate(g, {
                    step: function (a, c) {
                        c.prop == "height" && (e = c.end - c.start === 0 ? 0 : (c.now - c.start) / (c.end - c.start)), b.toShow[0].style[c.prop] = e * f[c.prop].value + f[c.prop].unit
                    },
                    duration: b.duration,
                    easing: b.easing,
                    complete: function () {
                        b.autoHeight || b.toShow.css("height", ""), b.toShow.css({
                            width: i,
                            overflow: d
                        }), b.complete()
                    }
                })
            },
            bounceslide: function (a) {
                this.slide(a, {
                    easing: a.down ? "easeOutBounce" : "swing",
                    duration: a.down ? 1e3 : 200
                })
            }
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.autocomplete.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    var c = 0;
    a.widget("ui.autocomplete", {
        options: {
            appendTo: "body",
            autoFocus: !1,
            delay: 300,
            minLength: 1,
            position: {
                my: "left top",
                at: "left bottom",
                collision: "none"
            },
            source: null
        },
        pending: 0,
        _create: function () {
            debugger;
            var b = this,
                c = this.element[0].ownerDocument,
                d;
            this.isMultiLine = this.element.is("textarea"), this.element.addClass("ui-autocomplete-input").attr("autocomplete", "off").attr({
                role: "textbox",
                "aria-autocomplete": "list",
                "aria-haspopup": "true"
            }).bind("keydown.autocomplete", function (c) {
                if (b.options.disabled || b.element.propAttr("readOnly")) return;
                d = !1;
                var e = a.ui.keyCode;
                switch (c.keyCode) {
                    case e.PAGE_UP:
                        b._move("previousPage", c);
                        break;
                    case e.PAGE_DOWN:
                        b._move("nextPage", c);
                        break;
                    case e.UP:
                        b._keyEvent("previous", c);
                        break;
                    case e.DOWN:
                        b._keyEvent("next", c);
                        break;
                    case e.ENTER:
                    case e.NUMPAD_ENTER:
                        b.menu.active && (d = !0, c.preventDefault());
                    case e.TAB:
                        if (!b.menu.active) return;
                        b.menu.select(c);
                        break;
                    case e.ESCAPE:
                        b.element.val(b.term), b.close(c);
                        break;
                    default:
                        clearTimeout(b.searching), b.searching = setTimeout(function () {
                            b.term != b.element.val() && (b.selectedItem = null, b.search(null, c))
                        }, b.options.delay)
                }
            }).bind("keypress.autocomplete", function (a) {
                d && (d = !1, a.preventDefault())
            }).bind("focus.autocomplete", function () {
                if (b.options.disabled) return;
                b.selectedItem = null, b.previous = b.element.val()
            }).bind("blur.autocomplete", function (a) {
                if (b.options.disabled) return;
                clearTimeout(b.searching), b.closing = setTimeout(function () {
                    b.close(a), b._change(a)
                }, 150)
            }), this._initSource(), this.menu = a("<ul></ul>").addClass("ui-autocomplete").appendTo(a(this.options.appendTo || "body", c)[0]).mousedown(function (c) {
                var d = b.menu.element[0];
                a(c.target).closest(".ui-menu-item").length || setTimeout(function () {
                    a(document).one("mousedown", function (c) {
                        c.target !== b.element[0] && c.target !== d && !a.ui.contains(d, c.target) && b.close()
                    })
                }, 1), setTimeout(function () {
                    clearTimeout(b.closing)
                }, 13)
            }).menu({
                focus: function (a, c) {
                    var d = c.item.data("item.autocomplete");
                    !1 !== b._trigger("focus", a, {
                        item: d
                    }) && /^key/.test(a.originalEvent.type) && b.element.val(d.value)
                },
                selected: function (a, d) {
                    var e = d.item.data("item.autocomplete"),
                        f = b.previous;
                    b.element[0] !== c.activeElement && (b.element.focus(), b.previous = f, setTimeout(function () {
                        b.previous = f, b.selectedItem = e
                    }, 1)), !1 !== b._trigger("select", a, {
                        item: e
                    }) && b.element.val(e.value), b.term = b.element.val(), b.close(a), b.selectedItem = e
                },
                blur: function (a, c) {
                    b.menu.element.is(":visible") && b.element.val() !== b.term && b.element.val(b.term)
                }
            }).zIndex(this.element.zIndex() + 1).css({
                top: 0,
                left: 0
            }).hide().data("menu"), a.fn.bgiframe && this.menu.element.bgiframe(), b.beforeunloadHandler = function () {
                b.element.removeAttr("autocomplete")
            }, a(window).bind("beforeunload", b.beforeunloadHandler)
        },
        destroy: function () {
            this.element.removeClass("ui-autocomplete-input").removeAttr("autocomplete").removeAttr("role").removeAttr("aria-autocomplete").removeAttr("aria-haspopup"), this.menu.element.remove(), a(window).unbind("beforeunload", this.beforeunloadHandler), a.Widget.prototype.destroy.call(this)
        },
        _setOption: function (b, c) {
            a.Widget.prototype._setOption.apply(this, arguments), b === "source" && this._initSource(), b === "appendTo" && this.menu.element.appendTo(a(c || "body", this.element[0].ownerDocument)[0]), b === "disabled" && c && this.xhr && this.xhr.abort()
        },
        _initSource: function () {
            var b = this,
                c, d;
            a.isArray(this.options.source) ? (c = this.options.source, this.source = function (b, d) {
                d(a.ui.autocomplete.filter(c, b.term))
            }) : typeof this.options.source == "string" ? (d = this.options.source, this.source = function (c, e) {
                b.xhr && b.xhr.abort(), b.xhr = a.ajax({
                    url: d,
                    data: c,
                    dataType: "json",
                    success: function (a, b) {
                        e(a)
                    },
                    error: function () {
                        e([])
                    }
                })
            }) : this.source = this.options.source
        },
        search: function (a, b) {
            a = a != null ? a : this.element.val(), this.term = this.element.val();
            if (a.length < this.options.minLength) return this.close(b);
            clearTimeout(this.closing);
            if (this._trigger("search", b) === !1) return;
            return this._search(a)
        },
        _search: function (a) {
            this.pending++, this.element.addClass("ui-autocomplete-loading"), this.source({
                term: a
            }, this._response())
        },
        _response: function () {
            var a = this,
                b = ++c;
            return function (d) {
                b === c && a.__response(d), a.pending--, a.pending || a.element.removeClass("ui-autocomplete-loading")
            }
        },
        __response: function (a) {
            !this.options.disabled && a && a.length ? (a = this._normalize(a), this._suggest(a), this._trigger("open")) : this.close()
        },
        close: function (a) {
            clearTimeout(this.closing), this.menu.element.is(":visible") && (this.menu.element.hide(), this.menu.deactivate(), this._trigger("close", a))
        },
        _change: function (a) {
            this.previous !== this.element.val() && this._trigger("change", a, {
                item: this.selectedItem
            })
        },
        _normalize: function (b) {
            return b.length && b[0].label && b[0].value ? b : a.map(b, function (b) {
                return typeof b == "string" ? {
                    label: b,
                    value: b
                } : a.extend({
                    label: b.label || b.value,
                    value: b.value || b.label
                }, b)
            })
        },
        _suggest: function (b) {
            var c = this.menu.element.empty().zIndex(this.element.zIndex() + 1);
            this._renderMenu(c, b), this.menu.deactivate(), this.menu.refresh(), c.show(), this._resizeMenu(), c.position(a.extend({
                of: this.element
            }, this.options.position)), this.options.autoFocus && this.menu.next(new a.Event("mouseover"))
        },
        _resizeMenu: function () {
            var a = this.menu.element;
            a.outerWidth(Math.max(a.width("").outerWidth() + 1, this.element.outerWidth()))
        },
        _renderMenu: function (b, c) {
            var d = this;
            a.each(c, function (a, c) {
                d._renderItem(b, c)
            })
        },
        _renderItem: function (b, c) {
            return a("<li></li>").data("item.autocomplete", c).append(a("<a></a>").text(c.label)).appendTo(b)
        },
        _move: function (a, b) {
            if (!this.menu.element.is(":visible")) {
                this.search(null, b);
                return
            }
            if (this.menu.first() && /^previous/.test(a) || this.menu.last() && /^next/.test(a)) {
                this.element.val(this.term), this.menu.deactivate();
                return
            }
            this.menu[a](b)
        },
        widget: function () {
            return this.menu.element
        },
        _keyEvent: function (a, b) {
            if (!this.isMultiLine || this.menu.element.is(":visible")) this._move(a, b), b.preventDefault()
        }
    }), a.extend(a.ui.autocomplete, {
        escapeRegex: function (a) {
            return a.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&")
        },
        filter: function (b, c) {
            var d = new RegExp(a.ui.autocomplete.escapeRegex(c), "i");
            return a.grep(b, function (a) {
                return d.test(a.label || a.value || a)
            })
        }
    })
})(jQuery),
    function (a) {
        a.widget("ui.menu", {
            _create: function () {
                var b = this;
                this.element.addClass("ui-menu ui-widget ui-widget-content ui-corner-all").attr({
                    role: "listbox",
                    "aria-activedescendant": "ui-active-menuitem"
                }).click(function (c) {
                    if (!a(c.target).closest(".ui-menu-item a").length) return;
                    c.preventDefault(), b.select(c)
                }), this.refresh()
            },
            refresh: function () {
                var b = this,
                    c = this.element.children("li:not(.ui-menu-item):has(a)").addClass("ui-menu-item").attr("role", "menuitem");
                c.children("a").addClass("ui-corner-all").attr("tabindex", -1).mouseenter(function (c) {
                    b.activate(c, a(this).parent())
                }).mouseleave(function () {
                    b.deactivate()
                })
            },
            activate: function (a, b) {
                this.deactivate();
                if (this.hasScroll()) {
                    var c = b.offset().top - this.element.offset().top,
                        d = this.element.scrollTop(),
                        e = this.element.height();
                    c < 0 ? this.element.scrollTop(d + c) : c >= e && this.element.scrollTop(d + c - e + b.height())
                }
                this.active = b.eq(0).children("a").addClass("ui-state-hover").attr("id", "ui-active-menuitem").end(), this._trigger("focus", a, {
                    item: b
                })
            },
            deactivate: function () {
                if (!this.active) return;
                this.active.children("a").removeClass("ui-state-hover").removeAttr("id"), this._trigger("blur"), this.active = null
            },
            next: function (a) {
                this.move("next", ".ui-menu-item:first", a)
            },
            previous: function (a) {
                this.move("prev", ".ui-menu-item:last", a)
            },
            first: function () {
                return this.active && !this.active.prevAll(".ui-menu-item").length
            },
            last: function () {
                return this.active && !this.active.nextAll(".ui-menu-item").length
            },
            move: function (a, b, c) {
                if (!this.active) {
                    this.activate(c, this.element.children(b));
                    return
                }
                var d = this.active[a + "All"](".ui-menu-item").eq(0);
                d.length ? this.activate(c, d) : this.activate(c, this.element.children(b))
            },
            nextPage: function (b) {
                if (this.hasScroll()) {
                    if (!this.active || this.last()) {
                        this.activate(b, this.element.children(".ui-menu-item:first"));
                        return
                    }
                    var c = this.active.offset().top,
                        d = this.element.height(),
                        e = this.element.children(".ui-menu-item").filter(function () {
                            var b = a(this).offset().top - c - d + a(this).height();
                            return b < 10 && b > -10
                        });
                    e.length || (e = this.element.children(".ui-menu-item:last")), this.activate(b, e)
                } else this.activate(b, this.element.children(".ui-menu-item").filter(!this.active || this.last() ? ":first" : ":last"))
            },
            previousPage: function (b) {
                if (this.hasScroll()) {
                    if (!this.active || this.first()) {
                        this.activate(b, this.element.children(".ui-menu-item:last"));
                        return
                    }
                    var c = this.active.offset().top,
                        d = this.element.height(),
                        e = this.element.children(".ui-menu-item").filter(function () {
                            var b = a(this).offset().top - c + d - a(this).height();
                            return b < 10 && b > -10
                        });
                    e.length || (e = this.element.children(".ui-menu-item:first")), this.activate(b, e)
                } else this.activate(b, this.element.children(".ui-menu-item").filter(!this.active || this.first() ? ":last" : ":first"))
            },
            hasScroll: function () {
                return this.element.height() < this.element[a.fn.prop ? "prop" : "attr"]("scrollHeight")
            },
            select: function (a) {
                this._trigger("selected", a, {
                    item: this.active
                })
            }
        })
    }(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.button.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    var c, d, e, f, g = "ui-button ui-widget ui-state-default ui-corner-all",
        h = "ui-state-hover ui-state-active ",
        i = "ui-button-icons-only ui-button-icon-only ui-button-text-icons ui-button-text-icon-primary ui-button-text-icon-secondary ui-button-text-only",
        j = function () {
            var b = a(this).find(":ui-button");
            setTimeout(function () {
                b.button("refresh")
            }, 1)
        }, k = function (b) {
            var c = b.name,
                d = b.form,
                e = a([]);
            return c && (d ? e = a(d).find("[name='" + c + "']") : e = a("[name='" + c + "']", b.ownerDocument).filter(function () {
                return !this.form
            })), e
        };
    a.widget("ui.button", {
        options: {
            disabled: null,
            text: !0,
            label: null,
            icons: {
                primary: null,
                secondary: null
            }
        },
        _create: function () {
            this.element.closest("form").unbind("reset.button").bind("reset.button", j), typeof this.options.disabled != "boolean" ? this.options.disabled = !! this.element.propAttr("disabled") : this.element.propAttr("disabled", this.options.disabled), this._determineButtonType(), this.hasTitle = !! this.buttonElement.attr("title");
            var b = this,
                h = this.options,
                i = this.type === "checkbox" || this.type === "radio",
                l = "ui-state-hover" + (i ? "" : " ui-state-active"),
                m = "ui-state-focus";
            h.label === null && (h.label = this.buttonElement.html()), this.buttonElement.addClass(g).attr("role", "button").bind("mouseenter.button", function () {
                if (h.disabled) return;
                a(this).addClass("ui-state-hover"), this === c && a(this).addClass("ui-state-active")
            }).bind("mouseleave.button", function () {
                if (h.disabled) return;
                a(this).removeClass(l)
            }).bind("click.button", function (a) {
                h.disabled && (a.preventDefault(), a.stopImmediatePropagation())
            }), this.element.bind("focus.button", function () {
                b.buttonElement.addClass(m)
            }).bind("blur.button", function () {
                b.buttonElement.removeClass(m)
            }), i && (this.element.bind("change.button", function () {
                if (f) return;
                b.refresh()
            }), this.buttonElement.bind("mousedown.button", function (a) {
                if (h.disabled) return;
                f = !1, d = a.pageX, e = a.pageY
            }).bind("mouseup.button", function (a) {
                if (h.disabled) return;
                if (d !== a.pageX || e !== a.pageY) f = !0
            })), this.type === "checkbox" ? this.buttonElement.bind("click.button", function () {
                if (h.disabled || f) return !1;
                a(this).toggleClass("ui-state-active"), b.buttonElement.attr("aria-pressed", b.element[0].checked)
            }) : this.type === "radio" ? this.buttonElement.bind("click.button", function () {
                if (h.disabled || f) return !1;
                a(this).addClass("ui-state-active"), b.buttonElement.attr("aria-pressed", "true");
                var c = b.element[0];
                k(c).not(c).map(function () {
                    return a(this).button("widget")[0]
                }).removeClass("ui-state-active").attr("aria-pressed", "false")
            }) : (this.buttonElement.bind("mousedown.button", function () {
                if (h.disabled) return !1;
                a(this).addClass("ui-state-active"), c = this, a(document).one("mouseup", function () {
                    c = null
                })
            }).bind("mouseup.button", function () {
                if (h.disabled) return !1;
                a(this).removeClass("ui-state-active")
            }).bind("keydown.button", function (b) {
                if (h.disabled) return !1;
                (b.keyCode == a.ui.keyCode.SPACE || b.keyCode == a.ui.keyCode.ENTER) && a(this).addClass("ui-state-active")
            }).bind("keyup.button", function () {
                a(this).removeClass("ui-state-active")
            }), this.buttonElement.is("a") && this.buttonElement.keyup(function (b) {
                b.keyCode === a.ui.keyCode.SPACE && a(this).click()
            })), this._setOption("disabled", h.disabled), this._resetButton()
        },
        _determineButtonType: function () {
            this.element.is(":checkbox") ? this.type = "checkbox" : this.element.is(":radio") ? this.type = "radio" : this.element.is("input") ? this.type = "input" : this.type = "button";
            if (this.type === "checkbox" || this.type === "radio") {
                var a = this.element.parents().filter(":last"),
                    b = "label[for='" + this.element.attr("id") + "']";
                this.buttonElement = a.find(b), this.buttonElement.length || (a = a.length ? a.siblings() : this.element.siblings(), this.buttonElement = a.filter(b), this.buttonElement.length || (this.buttonElement = a.find(b))), this.element.addClass("ui-helper-hidden-accessible");
                var c = this.element.is(":checked");
                c && this.buttonElement.addClass("ui-state-active"), this.buttonElement.attr("aria-pressed", c)
            } else this.buttonElement = this.element
        },
        widget: function () {
            return this.buttonElement
        },
        destroy: function () {
            this.element.removeClass("ui-helper-hidden-accessible"), this.buttonElement.removeClass(g + " " + h + " " + i).removeAttr("role").removeAttr("aria-pressed").html(this.buttonElement.find(".ui-button-text").html()), this.hasTitle || this.buttonElement.removeAttr("title"), a.Widget.prototype.destroy.call(this)
        },
        _setOption: function (b, c) {
            a.Widget.prototype._setOption.apply(this, arguments);
            if (b === "disabled") {
                c ? this.element.propAttr("disabled", !0) : this.element.propAttr("disabled", !1);
                return
            }
            this._resetButton()
        },
        refresh: function () {
            var b = this.element.is(":disabled");
            b !== this.options.disabled && this._setOption("disabled", b), this.type === "radio" ? k(this.element[0]).each(function () {
                a(this).is(":checked") ? a(this).button("widget").addClass("ui-state-active").attr("aria-pressed", "true") : a(this).button("widget").removeClass("ui-state-active").attr("aria-pressed", "false")
            }) : this.type === "checkbox" && (this.element.is(":checked") ? this.buttonElement.addClass("ui-state-active").attr("aria-pressed", "true") : this.buttonElement.removeClass("ui-state-active").attr("aria-pressed", "false"))
        },
        _resetButton: function () {
            if (this.type === "input") {
                this.options.label && this.element.val(this.options.label);
                return
            }
            var b = this.buttonElement.removeClass(i),
                c = a("<span></span>", this.element[0].ownerDocument).addClass("ui-button-text").html(this.options.label).appendTo(b.empty()).text(),
                d = this.options.icons,
                e = d.primary && d.secondary,
                f = [];
            d.primary || d.secondary ? (this.options.text && f.push("ui-button-text-icon" + (e ? "s" : d.primary ? "-primary" : "-secondary")), d.primary && b.prepend("<span class='ui-button-icon-primary ui-icon " + d.primary + "'></span>"), d.secondary && b.append("<span class='ui-button-icon-secondary ui-icon " + d.secondary + "'></span>"), this.options.text || (f.push(e ? "ui-button-icons-only" : "ui-button-icon-only"), this.hasTitle || b.attr("title", c))) : f.push("ui-button-text-only"), b.addClass(f.join(" "))
        }
    }), a.widget("ui.buttonset", {
        options: {
            items: ":button, :submit, :reset, :checkbox, :radio, a, :data(button)"
        },
        _create: function () {
            this.element.addClass("ui-buttonset")
        },
        _init: function () {
            this.refresh()
        },
        _setOption: function (b, c) {
            b === "disabled" && this.buttons.button("option", b, c), a.Widget.prototype._setOption.apply(this, arguments)
        },
        refresh: function () {
            var b = this.element.css("direction") === "rtl";
            this.buttons = this.element.find(this.options.items).filter(":ui-button").button("refresh").end().not(":ui-button").button().end().map(function () {
                return a(this).button("widget")[0]
            }).removeClass("ui-corner-all ui-corner-left ui-corner-right").filter(":first").addClass(b ? "ui-corner-right" : "ui-corner-left").end().filter(":last").addClass(b ? "ui-corner-left" : "ui-corner-right").end().end()
        },
        destroy: function () {
            this.element.removeClass("ui-buttonset"), this.buttons.map(function () {
                return a(this).button("widget")[0]
            }).removeClass("ui-corner-left ui-corner-right").end().button("destroy"), a.Widget.prototype.destroy.call(this)
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.dialog.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    var c = "ui-dialog ui-widget ui-widget-content ui-corner-all ",
        d = {
            buttons: !0,
            height: !0,
            maxHeight: !0,
            maxWidth: !0,
            minHeight: !0,
            minWidth: !0,
            width: !0
        }, e = {
            maxHeight: !0,
            maxWidth: !0,
            minHeight: !0,
            minWidth: !0
        }, f = a.attrFn || {
            val: !0,
            css: !0,
            html: !0,
            text: !0,
            data: !0,
            width: !0,
            height: !0,
            offset: !0,
            click: !0
        };
    a.widget("ui.dialog", {
        options: {
            autoOpen: !0,
            buttons: {},
            closeOnEscape: !0,
            closeText: "close",
            dialogClass: "",
            draggable: !0,
            hide: null,
            height: "auto",
            maxHeight: !1,
            maxWidth: !1,
            minHeight: 150,
            minWidth: 150,
            modal: !1,
            position: {
                my: "center",
                at: "center",
                collision: "fit",
                using: function (b) {
                    var c = a(this).css(b).offset().top;
                    c < 0 && a(this).css("top", b.top - c)
                }
            },
            resizable: !0,
            show: null,
            stack: !0,
            title: "",
            width: 300,
            zIndex: 1e3
        },
        _create: function () {
            this.originalTitle = this.element.attr("title"), typeof this.originalTitle != "string" && (this.originalTitle = ""), this.options.title = this.options.title || this.originalTitle;
            var b = this,
                d = b.options,
                e = d.title || "&#160;",
                f = a.ui.dialog.getTitleId(b.element),
                g = (b.uiDialog = a("<div></div>")).appendTo(document.body).hide().addClass(c + d.dialogClass).css({
                    zIndex: d.zIndex
                }).attr("tabIndex", -1).css("outline", 0).keydown(function (c) {
                    d.closeOnEscape && !c.isDefaultPrevented() && c.keyCode && c.keyCode === a.ui.keyCode.ESCAPE && (b.close(c), c.preventDefault())
                }).attr({
                    role: "dialog",
                    "aria-labelledby": f
                }).mousedown(function (a) {
                    b.moveToTop(!1, a)
                }),
                h = b.element.show().removeAttr("title").addClass("ui-dialog-content ui-widget-content").appendTo(g),
                i = (b.uiDialogTitlebar = a("<div></div>")).addClass("ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix").prependTo(g),
                j = a('<a href="#"></a>').addClass("ui-dialog-titlebar-close ui-corner-all").attr("role", "button").hover(function () {
                    j.addClass("ui-state-hover")
                }, function () {
                    j.removeClass("ui-state-hover")
                }).focus(function () {
                    j.addClass("ui-state-focus")
                }).blur(function () {
                    j.removeClass("ui-state-focus")
                }).click(function (a) {
                    return b.close(a), !1
                }).appendTo(i),
                k = (b.uiDialogTitlebarCloseText = a("<span></span>")).addClass("ui-icon ui-icon-closethick").text(d.closeText).appendTo(j),
                l = a("<span></span>").addClass("ui-dialog-title").attr("id", f).html(e).prependTo(i);
            a.isFunction(d.beforeclose) && !a.isFunction(d.beforeClose) && (d.beforeClose = d.beforeclose), i.find("*").add(i).disableSelection(), d.draggable && a.fn.draggable && b._makeDraggable(), d.resizable && a.fn.resizable && b._makeResizable(), b._createButtons(d.buttons), b._isOpen = !1, a.fn.bgiframe && g.bgiframe()
        },
        _init: function () {
            this.options.autoOpen && this.open()
        },
        destroy: function () {
            var a = this;
            return a.overlay && a.overlay.destroy(), a.uiDialog.hide(), a.element.unbind(".dialog").removeData("dialog").removeClass("ui-dialog-content ui-widget-content").hide().appendTo("body"), a.uiDialog.remove(), a.originalTitle && a.element.attr("title", a.originalTitle), a
        },
        widget: function () {
            return this.uiDialog
        },
        close: function (b) {
            var c = this,
                d, e;
            if (!1 === c._trigger("beforeClose", b)) return;
            return c.overlay && c.overlay.destroy(), c.uiDialog.unbind("keypress.ui-dialog"), c._isOpen = !1, c.options.hide ? c.uiDialog.hide(c.options.hide, function () {
                c._trigger("close", b)
            }) : (c.uiDialog.hide(), c._trigger("close", b)), a.ui.dialog.overlay.resize(), c.options.modal && (d = 0, a(".ui-dialog").each(function () {
                this !== c.uiDialog[0] && (e = a(this).css("z-index"), isNaN(e) || (d = Math.max(d, e)))
            }), a.ui.dialog.maxZ = d), c
        },
        isOpen: function () {
            return this._isOpen
        },
        moveToTop: function (b, c) {
            var d = this,
                e = d.options,
                f;
            return e.modal && !b || !e.stack && !e.modal ? d._trigger("focus", c) : (e.zIndex > a.ui.dialog.maxZ && (a.ui.dialog.maxZ = e.zIndex), d.overlay && (a.ui.dialog.maxZ += 1, d.overlay.$el.css("z-index", a.ui.dialog.overlay.maxZ = a.ui.dialog.maxZ)), f = {
                scrollTop: d.element.scrollTop(),
                scrollLeft: d.element.scrollLeft()
            }, a.ui.dialog.maxZ += 1, d.uiDialog.css("z-index", a.ui.dialog.maxZ), d.element.attr(f), d._trigger("focus", c), d)
        },
        open: function () {
            if (this._isOpen) return;
            var b = this,
                c = b.options,
                d = b.uiDialog;
            return b.overlay = c.modal ? new a.ui.dialog.overlay(b) : null, b._size(), b._position(c.position), d.show(c.show), b.moveToTop(!0), c.modal && d.bind("keydown.ui-dialog", function (b) {
                if (b.keyCode !== a.ui.keyCode.TAB) return;
                var c = a(":tabbable", this),
                    d = c.filter(":first"),
                    e = c.filter(":last");
                if (b.target === e[0] && !b.shiftKey) return d.focus(1), !1;
                if (b.target === d[0] && b.shiftKey) return e.focus(1), !1
            }), a(b.element.find(":tabbable").get().concat(d.find(".ui-dialog-buttonpane :tabbable").get().concat(d.get()))).eq(0).focus(), b._isOpen = !0, b._trigger("open"), b
        },
        _createButtons: function (b) {
            var c = this,
                d = !1,
                e = a("<div></div>").addClass("ui-dialog-buttonpane ui-widget-content ui-helper-clearfix"),
                g = a("<div></div>").addClass("ui-dialog-buttonset").appendTo(e);
            c.uiDialog.find(".ui-dialog-buttonpane").remove(), typeof b == "object" && b !== null && a.each(b, function () {
                return !(d = !0)
            }), d && (a.each(b, function (b, d) {
                d = a.isFunction(d) ? {
                    click: d,
                    text: b
                } : d;
                var e = a('<button type="button"></button>').click(function () {
                    d.click.apply(c.element[0], arguments)
                }).appendTo(g);
                a.each(d, function (a, b) {
                    if (a === "click") return;
                    a in f ? e[a](b) : e.attr(a, b)
                }), a.fn.button && e.button()
            }), e.appendTo(c.uiDialog))
        },
        _makeDraggable: function () {
            function f(a) {
                return {
                    position: a.position,
                    offset: a.offset
                }
            }
            var b = this,
                c = b.options,
                d = a(document),
                e;
            b.uiDialog.draggable({
                cancel: ".ui-dialog-content, .ui-dialog-titlebar-close",
                handle: ".ui-dialog-titlebar",
                containment: "document",
                start: function (d, g) {
                    e = c.height === "auto" ? "auto" : a(this).height(), a(this).height(a(this).height()).addClass("ui-dialog-dragging"), b._trigger("dragStart", d, f(g))
                },
                drag: function (a, c) {
                    b._trigger("drag", a, f(c))
                },
                stop: function (g, h) {
                    c.position = [h.position.left - d.scrollLeft(), h.position.top - d.scrollTop()], a(this).removeClass("ui-dialog-dragging").height(e), b._trigger("dragStop", g, f(h)), a.ui.dialog.overlay.resize()
                }
            })
        },
        _makeResizable: function (c) {
            function h(a) {
                return {
                    originalPosition: a.originalPosition,
                    originalSize: a.originalSize,
                    position: a.position,
                    size: a.size
                }
            }
            c = c === b ? this.options.resizable : c;
            var d = this,
                e = d.options,
                f = d.uiDialog.css("position"),
                g = typeof c == "string" ? c : "n,e,s,w,se,sw,ne,nw";
            d.uiDialog.resizable({
                cancel: ".ui-dialog-content",
                containment: "document",
                alsoResize: d.element,
                maxWidth: e.maxWidth,
                maxHeight: e.maxHeight,
                minWidth: e.minWidth,
                minHeight: d._minHeight(),
                handles: g,
                start: function (b, c) {
                    a(this).addClass("ui-dialog-resizing"), d._trigger("resizeStart", b, h(c))
                },
                resize: function (a, b) {
                    d._trigger("resize", a, h(b))
                },
                stop: function (b, c) {
                    a(this).removeClass("ui-dialog-resizing"), e.height = a(this).height(), e.width = a(this).width(), d._trigger("resizeStop", b, h(c)), a.ui.dialog.overlay.resize()
                }
            }).css("position", f).find(".ui-resizable-se").addClass("ui-icon ui-icon-grip-diagonal-se")
        },
        _minHeight: function () {
            var a = this.options;
            return a.height === "auto" ? a.minHeight : Math.min(a.minHeight, a.height)
        },
        _position: function (b) {
            var c = [],
                d = [0, 0],
                e;
            if (b) {
                if (typeof b == "string" || typeof b == "object" && "0" in b) c = b.split ? b.split(" ") : [b[0], b[1]], c.length === 1 && (c[1] = c[0]), a.each(["left", "top"], function (a, b) {
                    +c[a] === c[a] && (d[a] = c[a], c[a] = b)
                }), b = {
                    my: c.join(" "),
                    at: c.join(" "),
                    offset: d.join(" ")
                };
                b = a.extend({}, a.ui.dialog.prototype.options.position, b)
            } else b = a.ui.dialog.prototype.options.position;
            e = this.uiDialog.is(":visible"), e || this.uiDialog.show(), this.uiDialog.css({
                top: 0,
                left: 0
            }).position(a.extend({
                    of: window
                }, b)), e || this.uiDialog.hide()
        },
        _setOptions: function (b) {
            var c = this,
                f = {}, g = !1;
            a.each(b, function (a, b) {
                c._setOption(a, b), a in d && (g = !0), a in e && (f[a] = b)
            }), g && this._size(), this.uiDialog.is(":data(resizable)") && this.uiDialog.resizable("option", f)
        },
        _setOption: function (b, d) {
            var e = this,
                f = e.uiDialog;
            switch (b) {
                case "beforeclose":
                    b = "beforeClose";
                    break;
                case "buttons":
                    e._createButtons(d);
                    break;
                case "closeText":
                    e.uiDialogTitlebarCloseText.text("" + d);
                    break;
                case "dialogClass":
                    f.removeClass(e.options.dialogClass).addClass(c + d);
                    break;
                case "disabled":
                    d ? f.addClass("ui-dialog-disabled") : f.removeClass("ui-dialog-disabled");
                    break;
                case "draggable":
                    var g = f.is(":data(draggable)");
                    g && !d && f.draggable("destroy"), !g && d && e._makeDraggable();
                    break;
                case "position":
                    e._position(d);
                    break;
                case "resizable":
                    var h = f.is(":data(resizable)");
                    h && !d && f.resizable("destroy"), h && typeof d == "string" && f.resizable("option", "handles", d), !h && d !== !1 && e._makeResizable(d);
                    break;
                case "title":
                    a(".ui-dialog-title", e.uiDialogTitlebar).html("" + (d || "&#160;"))
            }
            a.Widget.prototype._setOption.apply(e, arguments)
        },
        _size: function () {
            var b = this.options,
                c, d, e = this.uiDialog.is(":visible");
            this.element.show().css({
                width: "auto",
                minHeight: 0,
                height: 0
            }), b.minWidth > b.width && (b.width = b.minWidth), c = this.uiDialog.css({
                height: "auto",
                width: b.width
            }).height(), d = Math.max(0, b.minHeight - c);
            if (b.height === "auto")
                if (a.support.minHeight) this.element.css({
                    minHeight: d,
                    height: "auto"
                });
                else {
                    this.uiDialog.show();
                    var f = this.element.css("height", "auto").height();
                    e || this.uiDialog.hide(), this.element.height(Math.max(f, d))
                } else this.element.height(Math.max(b.height - c, 0));
            this.uiDialog.is(":data(resizable)") && this.uiDialog.resizable("option", "minHeight", this._minHeight())
        }
    }), a.extend(a.ui.dialog, {
        version: "1.8.21",
        uuid: 0,
        maxZ: 0,
        getTitleId: function (a) {
            var b = a.attr("id");
            return b || (this.uuid += 1, b = this.uuid), "ui-dialog-title-" + b
        },
        overlay: function (b) {
            this.$el = a.ui.dialog.overlay.create(b)
        }
    }), a.extend(a.ui.dialog.overlay, {
        instances: [],
        oldInstances: [],
        maxZ: 0,
        events: a.map("focus,mousedown,mouseup,keydown,keypress,click".split(","), function (a) {
            return a + ".dialog-overlay"
        }).join(" "),
        create: function (b) {
            this.instances.length === 0 && (setTimeout(function () {
                a.ui.dialog.overlay.instances.length && a(document).bind(a.ui.dialog.overlay.events, function (b) {
                    if (a(b.target).zIndex() < a.ui.dialog.overlay.maxZ) return !1
                })
            }, 1), a(document).bind("keydown.dialog-overlay", function (c) {
                b.options.closeOnEscape && !c.isDefaultPrevented() && c.keyCode && c.keyCode === a.ui.keyCode.ESCAPE && (b.close(c), c.preventDefault())
            }), a(window).bind("resize.dialog-overlay", a.ui.dialog.overlay.resize));
            var c = (this.oldInstances.pop() || a("<div></div>").addClass("ui-widget-overlay")).appendTo(document.body).css({
                width: this.width(),
                height: this.height()
            });
            return a.fn.bgiframe && c.bgiframe(), this.instances.push(c), c
        },
        destroy: function (b) {
            var c = a.inArray(b, this.instances);
            c != -1 && this.oldInstances.push(this.instances.splice(c, 1)[0]), this.instances.length === 0 && a([document, window]).unbind(".dialog-overlay"), b.remove();
            var d = 0;
            a.each(this.instances, function () {
                d = Math.max(d, this.css("z-index"))
            }), this.maxZ = d
        },
        height: function () {
            var b, c;
            return a.browser.msie && a.browser.version < 7 ? (b = Math.max(document.documentElement.scrollHeight, document.body.scrollHeight), c = Math.max(document.documentElement.offsetHeight, document.body.offsetHeight), b < c ? a(window).height() + "px" : b + "px") : a(document).height() + "px"
        },
        width: function () {
            var b, c;
            return a.browser.msie ? (b = Math.max(document.documentElement.scrollWidth, document.body.scrollWidth), c = Math.max(document.documentElement.offsetWidth, document.body.offsetWidth), b < c ? a(window).width() + "px" : b + "px") : a(document).width() + "px"
        },
        resize: function () {
            var b = a([]);
            a.each(a.ui.dialog.overlay.instances, function () {
                b = b.add(this)
            }), b.css({
                width: 0,
                height: 0
            }).css({
                width: a.ui.dialog.overlay.width(),
                height: a.ui.dialog.overlay.height()
            })
        }
    }), a.extend(a.ui.dialog.overlay.prototype, {
        destroy: function () {
            a.ui.dialog.overlay.destroy(this.$el)
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.slider.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    var c = 5;
    a.widget("ui.slider", a.ui.mouse, {
        widgetEventPrefix: "slide",
        options: {
            animate: !1,
            distance: 0,
            max: 100,
            min: 0,
            orientation: "horizontal",
            range: !1,
            step: 1,
            value: 0,
            values: null
        },
        _create: function () {
            var b = this,
                d = this.options,
                e = this.element.find(".ui-slider-handle").addClass("ui-state-default ui-corner-all"),
                f = "<a class='ui-slider-handle ui-state-default ui-corner-all' href='#'></a>",
                g = d.values && d.values.length || 1,
                h = [];
            this._keySliding = !1, this._mouseSliding = !1, this._animateOff = !0, this._handleIndex = null, this._detectOrientation(), this._mouseInit(), this.element.addClass("ui-slider ui-slider-" + this.orientation + " ui-widget" + " ui-widget-content" + " ui-corner-all" + (d.disabled ? " ui-slider-disabled ui-disabled" : "")), this.range = a([]), d.range && (d.range === !0 && (d.values || (d.values = [this._valueMin(), this._valueMin()]), d.values.length && d.values.length !== 2 && (d.values = [d.values[0], d.values[0]])), this.range = a("<div></div>").appendTo(this.element).addClass("ui-slider-range ui-widget-header" + (d.range === "min" || d.range === "max" ? " ui-slider-range-" + d.range : "")));
            for (var i = e.length; i < g; i += 1) h.push(f);
            this.handles = e.add(a(h.join("")).appendTo(b.element)), this.handle = this.handles.eq(0), this.handles.add(this.range).filter("a").click(function (a) {
                a.preventDefault()
            }).hover(function () {
                d.disabled || a(this).addClass("ui-state-hover")
            }, function () {
                a(this).removeClass("ui-state-hover")
            }).focus(function () {
                d.disabled ? a(this).blur() : (a(".ui-slider .ui-state-focus").removeClass("ui-state-focus"), a(this).addClass("ui-state-focus"))
            }).blur(function () {
                a(this).removeClass("ui-state-focus")
            }), this.handles.each(function (b) {
                a(this).data("index.ui-slider-handle", b)
            }), this.handles.keydown(function (d) {
                var e = a(this).data("index.ui-slider-handle"),
                    f, g, h, i;
                if (b.options.disabled) return;
                switch (d.keyCode) {
                    case a.ui.keyCode.HOME:
                    case a.ui.keyCode.END:
                    case a.ui.keyCode.PAGE_UP:
                    case a.ui.keyCode.PAGE_DOWN:
                    case a.ui.keyCode.UP:
                    case a.ui.keyCode.RIGHT:
                    case a.ui.keyCode.DOWN:
                    case a.ui.keyCode.LEFT:
                        d.preventDefault();
                        if (!b._keySliding) {
                            b._keySliding = !0, a(this).addClass("ui-state-active"), f = b._start(d, e);
                            if (f === !1) return
                        }
                }
                i = b.options.step, b.options.values && b.options.values.length ? g = h = b.values(e) : g = h = b.value();
                switch (d.keyCode) {
                    case a.ui.keyCode.HOME:
                        h = b._valueMin();
                        break;
                    case a.ui.keyCode.END:
                        h = b._valueMax();
                        break;
                    case a.ui.keyCode.PAGE_UP:
                        h = b._trimAlignValue(g + (b._valueMax() - b._valueMin()) / c);
                        break;
                    case a.ui.keyCode.PAGE_DOWN:
                        h = b._trimAlignValue(g - (b._valueMax() - b._valueMin()) / c);
                        break;
                    case a.ui.keyCode.UP:
                    case a.ui.keyCode.RIGHT:
                        if (g === b._valueMax()) return;
                        h = b._trimAlignValue(g + i);
                        break;
                    case a.ui.keyCode.DOWN:
                    case a.ui.keyCode.LEFT:
                        if (g === b._valueMin()) return;
                        h = b._trimAlignValue(g - i)
                }
                b._slide(d, e, h)
            }).keyup(function (c) {
                var d = a(this).data("index.ui-slider-handle");
                b._keySliding && (b._keySliding = !1, b._stop(c, d), b._change(c, d), a(this).removeClass("ui-state-active"))
            }), this._refreshValue(), this._animateOff = !1
        },
        destroy: function () {
            return this.handles.remove(), this.range.remove(), this.element.removeClass("ui-slider ui-slider-horizontal ui-slider-vertical ui-slider-disabled ui-widget ui-widget-content ui-corner-all").removeData("slider").unbind(".slider"), this._mouseDestroy(), this
        },
        _mouseCapture: function (b) {
            var c = this.options,
                d, e, f, g, h, i, j, k, l;
            return c.disabled ? !1 : (this.elementSize = {
                width: this.element.outerWidth(),
                height: this.element.outerHeight()
            }, this.elementOffset = this.element.offset(), d = {
                x: b.pageX,
                y: b.pageY
            }, e = this._normValueFromMouse(d), f = this._valueMax() - this._valueMin() + 1, h = this, this.handles.each(function (b) {
                var c = Math.abs(e - h.values(b));
                f > c && (f = c, g = a(this), i = b)
            }), c.range === !0 && this.values(1) === c.min && (i += 1, g = a(this.handles[i])), j = this._start(b, i), j === !1 ? !1 : (this._mouseSliding = !0, h._handleIndex = i, g.addClass("ui-state-active").focus(), k = g.offset(), l = !a(b.target).parents().andSelf().is(".ui-slider-handle"), this._clickOffset = l ? {
                left: 0,
                top: 0
            } : {
                left: b.pageX - k.left - g.width() / 2,
                top: b.pageY - k.top - g.height() / 2 - (parseInt(g.css("borderTopWidth"), 10) || 0) - (parseInt(g.css("borderBottomWidth"), 10) || 0) + (parseInt(g.css("marginTop"), 10) || 0)
            }, this.handles.hasClass("ui-state-hover") || this._slide(b, i, e), this._animateOff = !0, !0))
        },
        _mouseStart: function (a) {
            return !0
        },
        _mouseDrag: function (a) {
            var b = {
                x: a.pageX,
                y: a.pageY
            }, c = this._normValueFromMouse(b);
            return this._slide(a, this._handleIndex, c), !1
        },
        _mouseStop: function (a) {
            return this.handles.removeClass("ui-state-active"), this._mouseSliding = !1, this._stop(a, this._handleIndex), this._change(a, this._handleIndex), this._handleIndex = null, this._clickOffset = null, this._animateOff = !1, !1
        },
        _detectOrientation: function () {
            this.orientation = this.options.orientation === "vertical" ? "vertical" : "horizontal"
        },
        _normValueFromMouse: function (a) {
            var b, c, d, e, f;
            return this.orientation === "horizontal" ? (b = this.elementSize.width, c = a.x - this.elementOffset.left - (this._clickOffset ? this._clickOffset.left : 0)) : (b = this.elementSize.height, c = a.y - this.elementOffset.top - (this._clickOffset ? this._clickOffset.top : 0)), d = c / b, d > 1 && (d = 1), d < 0 && (d = 0), this.orientation === "vertical" && (d = 1 - d), e = this._valueMax() - this._valueMin(), f = this._valueMin() + d * e, this._trimAlignValue(f)
        },
        _start: function (a, b) {
            var c = {
                handle: this.handles[b],
                value: this.value()
            };
            return this.options.values && this.options.values.length && (c.value = this.values(b), c.values = this.values()), this._trigger("start", a, c)
        },
        _slide: function (a, b, c) {
            var d, e, f;
            this.options.values && this.options.values.length ? (d = this.values(b ? 0 : 1), this.options.values.length === 2 && this.options.range === !0 && (b === 0 && c > d || b === 1 && c < d) && (c = d), c !== this.values(b) && (e = this.values(), e[b] = c, f = this._trigger("slide", a, {
                handle: this.handles[b],
                value: c,
                values: e
            }), d = this.values(b ? 0 : 1), f !== !1 && this.values(b, c, !0))) : c !== this.value() && (f = this._trigger("slide", a, {
                handle: this.handles[b],
                value: c
            }), f !== !1 && this.value(c))
        },
        _stop: function (a, b) {
            var c = {
                handle: this.handles[b],
                value: this.value()
            };
            this.options.values && this.options.values.length && (c.value = this.values(b), c.values = this.values()), this._trigger("stop", a, c)
        },
        _change: function (a, b) {
            if (!this._keySliding && !this._mouseSliding) {
                var c = {
                    handle: this.handles[b],
                    value: this.value()
                };
                this.options.values && this.options.values.length && (c.value = this.values(b), c.values = this.values()), this._trigger("change", a, c)
            }
        },
        value: function (a) {
            if (arguments.length) {
                this.options.value = this._trimAlignValue(a), this._refreshValue(), this._change(null, 0);
                return
            }
            return this._value()
        },
        values: function (b, c) {
            var d, e, f;
            if (arguments.length > 1) {
                this.options.values[b] = this._trimAlignValue(c), this._refreshValue(), this._change(null, b);
                return
            }
            if (!arguments.length) return this._values();
            if (!a.isArray(arguments[0])) return this.options.values && this.options.values.length ? this._values(b) : this.value();
            d = this.options.values, e = arguments[0];
            for (f = 0; f < d.length; f += 1) d[f] = this._trimAlignValue(e[f]), this._change(null, f);
            this._refreshValue()
        },
        _setOption: function (b, c) {
            var d, e = 0;
            a.isArray(this.options.values) && (e = this.options.values.length), a.Widget.prototype._setOption.apply(this, arguments);
            switch (b) {
                case "disabled":
                    c ? (this.handles.filter(".ui-state-focus").blur(), this.handles.removeClass("ui-state-hover"), this.handles.propAttr("disabled", !0), this.element.addClass("ui-disabled")) : (this.handles.propAttr("disabled", !1), this.element.removeClass("ui-disabled"));
                    break;
                case "orientation":
                    this._detectOrientation(), this.element.removeClass("ui-slider-horizontal ui-slider-vertical").addClass("ui-slider-" + this.orientation), this._refreshValue();
                    break;
                case "value":
                    this._animateOff = !0, this._refreshValue(), this._change(null, 0), this._animateOff = !1;
                    break;
                case "values":
                    this._animateOff = !0, this._refreshValue();
                    for (d = 0; d < e; d += 1) this._change(null, d);
                    this._animateOff = !1
            }
        },
        _value: function () {
            var a = this.options.value;
            return a = this._trimAlignValue(a), a
        },
        _values: function (a) {
            var b, c, d;
            if (arguments.length) return b = this.options.values[a], b = this._trimAlignValue(b), b;
            c = this.options.values.slice();
            for (d = 0; d < c.length; d += 1) c[d] = this._trimAlignValue(c[d]);
            return c
        },
        _trimAlignValue: function (a) {
            if (a <= this._valueMin()) return this._valueMin();
            if (a >= this._valueMax()) return this._valueMax();
            var b = this.options.step > 0 ? this.options.step : 1,
                c = (a - this._valueMin()) % b,
                d = a - c;
            return Math.abs(c) * 2 >= b && (d += c > 0 ? b : -b), parseFloat(d.toFixed(5))
        },
        _valueMin: function () {
            return this.options.min
        },
        _valueMax: function () {
            return this.options.max
        },
        _refreshValue: function () {
            var b = this.options.range,
                c = this.options,
                d = this,
                e = this._animateOff ? !1 : c.animate,
                f, g = {}, h, i, j, k;
            this.options.values && this.options.values.length ? this.handles.each(function (b, i) {
                f = (d.values(b) - d._valueMin()) / (d._valueMax() - d._valueMin()) * 100, g[d.orientation === "horizontal" ? "left" : "bottom"] = f + "%", a(this).stop(1, 1)[e ? "animate" : "css"](g, c.animate), d.options.range === !0 && (d.orientation === "horizontal" ? (b === 0 && d.range.stop(1, 1)[e ? "animate" : "css"]({
                    left: f + "%"
                }, c.animate), b === 1 && d.range[e ? "animate" : "css"]({
                    width: f - h + "%"
                }, {
                    queue: !1,
                    duration: c.animate
                })) : (b === 0 && d.range.stop(1, 1)[e ? "animate" : "css"]({
                    bottom: f + "%"
                }, c.animate), b === 1 && d.range[e ? "animate" : "css"]({
                    height: f - h + "%"
                }, {
                    queue: !1,
                    duration: c.animate
                }))), h = f
            }) : (i = this.value(), j = this._valueMin(), k = this._valueMax(), f = k !== j ? (i - j) / (k - j) * 100 : 0, g[d.orientation === "horizontal" ? "left" : "bottom"] = f + "%", this.handle.stop(1, 1)[e ? "animate" : "css"](g, c.animate), b === "min" && this.orientation === "horizontal" && this.range.stop(1, 1)[e ? "animate" : "css"]({
                width: f + "%"
            }, c.animate), b === "max" && this.orientation === "horizontal" && this.range[e ? "animate" : "css"]({
                width: 100 - f + "%"
            }, {
                queue: !1,
                duration: c.animate
            }), b === "min" && this.orientation === "vertical" && this.range.stop(1, 1)[e ? "animate" : "css"]({
                height: f + "%"
            }, c.animate), b === "max" && this.orientation === "vertical" && this.range[e ? "animate" : "css"]({
                height: 100 - f + "%"
            }, {
                queue: !1,
                duration: c.animate
            }))
        }
    }), a.extend(a.ui.slider, {
        version: "1.8.21"
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.tabs.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    function e() {
        return ++c
    }

    function f() {
        return ++d
    }
    var c = 0,
        d = 0;
    a.widget("ui.tabs", {
        options: {
            add: null,
            ajaxOptions: null,
            cache: !1,
            cookie: null,
            collapsible: !1,
            disable: null,
            disabled: [],
            enable: null,
            event: "click",
            fx: null,
            idPrefix: "ui-tabs-",
            load: null,
            panelTemplate: "<div></div>",
            remove: null,
            select: null,
            show: null,
            spinner: "<em>Loading&#8230;</em>",
            tabTemplate: "<li><a href='#{href}'><span>#{label}</span></a></li>"
        },
        _create: function () {
            this._tabify(!0)
        },
        _setOption: function (a, b) {
            if (a == "selected") {
                if (this.options.collapsible && b == this.options.selected) return;
                this.select(b)
            } else this.options[a] = b, this._tabify()
        },
        _tabId: function (a) {
            return a.title && a.title.replace(/\s/g, "_").replace(/[^\w\u00c0-\uFFFF-]/g, "") || this.options.idPrefix + e()
        },
        _sanitizeSelector: function (a) {
            return a.replace(/:/g, "\\:")
        },
        _cookie: function () {
            var b = this.cookie || (this.cookie = this.options.cookie.name || "ui-tabs-" + f());
            return a.cookie.apply(null, [b].concat(a.makeArray(arguments)))
        },
        _ui: function (a, b) {
            return {
                tab: a,
                panel: b,
                index: this.anchors.index(a)
            }
        },
        _cleanup: function () {
            this.lis.filter(".ui-state-processing").removeClass("ui-state-processing").find("span:data(label.tabs)").each(function () {
                var b = a(this);
                b.html(b.data("label.tabs")).removeData("label.tabs")
            })
        },
        _tabify: function (c) {
            function m(b, c) {
                b.css("display", ""), !a.support.opacity && c.opacity && b[0].style.removeAttribute("filter")
            }
            var d = this,
                e = this.options,
                f = /^#.+/;
            this.list = this.element.find("ol,ul").eq(0), this.lis = a(" > li:has(a[href])", this.list), this.anchors = this.lis.map(function () {
                return a("a", this)[0]
            }), this.panels = a([]), this.anchors.each(function (b, c) {
                var g = a(c).attr("href"),
                    h = g.split("#")[0],
                    i;
                h && (h === location.toString().split("#")[0] || (i = a("base")[0]) && h === i.href) && (g = c.hash, c.href = g);
                if (f.test(g)) d.panels = d.panels.add(d.element.find(d._sanitizeSelector(g)));
                else if (g && g !== "#") {
                    a.data(c, "href.tabs", g), a.data(c, "load.tabs", g.replace(/#.*$/, ""));
                    var j = d._tabId(c);
                    c.href = "#" + j;
                    var k = d.element.find("#" + j);
                    k.length || (k = a(e.panelTemplate).attr("id", j).addClass("ui-tabs-panel ui-widget-content ui-corner-bottom").insertAfter(d.panels[b - 1] || d.list), k.data("destroy.tabs", !0)), d.panels = d.panels.add(k)
                } else e.disabled.push(b)
            }), c ? (this.element.addClass("ui-tabs ui-widget ui-widget-content ui-corner-all"), this.list.addClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"), this.lis.addClass("ui-state-default ui-corner-top"), this.panels.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom"), e.selected === b ? (location.hash && this.anchors.each(function (a, b) {
                if (b.hash == location.hash) return e.selected = a, !1
            }), typeof e.selected != "number" && e.cookie && (e.selected = parseInt(d._cookie(), 10)), typeof e.selected != "number" && this.lis.filter(".ui-tabs-selected").length && (e.selected = this.lis.index(this.lis.filter(".ui-tabs-selected"))), e.selected = e.selected || (this.lis.length ? 0 : -1)) : e.selected === null && (e.selected = -1), e.selected = e.selected >= 0 && this.anchors[e.selected] || e.selected < 0 ? e.selected : 0, e.disabled = a.unique(e.disabled.concat(a.map(this.lis.filter(".ui-state-disabled"), function (a, b) {
                return d.lis.index(a)
            }))).sort(), a.inArray(e.selected, e.disabled) != -1 && e.disabled.splice(a.inArray(e.selected, e.disabled), 1), this.panels.addClass("ui-tabs-hide"), this.lis.removeClass("ui-tabs-selected ui-state-active"), e.selected >= 0 && this.anchors.length && (d.element.find(d._sanitizeSelector(d.anchors[e.selected].hash)).removeClass("ui-tabs-hide"), this.lis.eq(e.selected).addClass("ui-tabs-selected ui-state-active"), d.element.queue("tabs", function () {
                d._trigger("show", null, d._ui(d.anchors[e.selected], d.element.find(d._sanitizeSelector(d.anchors[e.selected].hash))[0]))
            }), this.load(e.selected)), a(window).bind("unload", function () {
                d.lis.add(d.anchors).unbind(".tabs"), d.lis = d.anchors = d.panels = null
            })) : e.selected = this.lis.index(this.lis.filter(".ui-tabs-selected")), this.element[e.collapsible ? "addClass" : "removeClass"]("ui-tabs-collapsible"), e.cookie && this._cookie(e.selected, e.cookie);
            for (var g = 0, h; h = this.lis[g]; g++) a(h)[a.inArray(g, e.disabled) != -1 && !a(h).hasClass("ui-tabs-selected") ? "addClass" : "removeClass"]("ui-state-disabled");
            e.cache === !1 && this.anchors.removeData("cache.tabs"), this.lis.add(this.anchors).unbind(".tabs");
            if (e.event !== "mouseover") {
                var i = function (a, b) {
                    b.is(":not(.ui-state-disabled)") && b.addClass("ui-state-" + a)
                }, j = function (a, b) {
                    b.removeClass("ui-state-" + a)
                };
                this.lis.bind("mouseover.tabs", function () {
                    i("hover", a(this))
                }), this.lis.bind("mouseout.tabs", function () {
                    j("hover", a(this))
                }), this.anchors.bind("focus.tabs", function () {
                    i("focus", a(this).closest("li"))
                }), this.anchors.bind("blur.tabs", function () {
                    j("focus", a(this).closest("li"))
                })
            }
            var k, l;
            e.fx && (a.isArray(e.fx) ? (k = e.fx[0], l = e.fx[1]) : k = l = e.fx);
            var n = l ? function (b, c) {
                a(b).closest("li").addClass("ui-tabs-selected ui-state-active"), c.hide().removeClass("ui-tabs-hide").animate(l, l.duration || "normal", function () {
                    m(c, l), d._trigger("show", null, d._ui(b, c[0]))
                })
            } : function (b, c) {
                a(b).closest("li").addClass("ui-tabs-selected ui-state-active"), c.removeClass("ui-tabs-hide"), d._trigger("show", null, d._ui(b, c[0]))
            }, o = k ? function (a, b) {
                b.animate(k, k.duration || "normal", function () {
                    d.lis.removeClass("ui-tabs-selected ui-state-active"), b.addClass("ui-tabs-hide"), m(b, k), d.element.dequeue("tabs")
                })
            } : function (a, b, c) {
                d.lis.removeClass("ui-tabs-selected ui-state-active"), b.addClass("ui-tabs-hide"), d.element.dequeue("tabs")
            };
            this.anchors.bind(e.event + ".tabs", function () {
                var b = this,
                    c = a(b).closest("li"),
                    f = d.panels.filter(":not(.ui-tabs-hide)"),
                    g = d.element.find(d._sanitizeSelector(b.hash));
                if (c.hasClass("ui-tabs-selected") && !e.collapsible || c.hasClass("ui-state-disabled") || c.hasClass("ui-state-processing") || d.panels.filter(":animated").length || d._trigger("select", null, d._ui(this, g[0])) === !1) return this.blur(), !1;
                e.selected = d.anchors.index(this), d.abort();
                if (e.collapsible) {
                    if (c.hasClass("ui-tabs-selected")) return e.selected = -1, e.cookie && d._cookie(e.selected, e.cookie), d.element.queue("tabs", function () {
                        o(b, f)
                    }).dequeue("tabs"), this.blur(), !1;
                    if (!f.length) return e.cookie && d._cookie(e.selected, e.cookie), d.element.queue("tabs", function () {
                        n(b, g)
                    }), d.load(d.anchors.index(this)), this.blur(), !1
                }
                e.cookie && d._cookie(e.selected, e.cookie);
                if (g.length) f.length && d.element.queue("tabs", function () {
                    o(b, f)
                }), d.element.queue("tabs", function () {
                    n(b, g)
                }), d.load(d.anchors.index(this));
                else throw new Error("jQuery UI Tabs: Mismatching fragment identifier.");
                a.browser.msie && this.blur()
            }), this.anchors.bind("click.tabs", function () {
                return !1
            })
        },
        _getIndex: function (a) {
            return typeof a == "string" && (a = this.anchors.index(this.anchors.filter("[href$='" + a + "']"))), a
        },
        destroy: function () {
            var b = this.options;
            return this.abort(), this.element.unbind(".tabs").removeClass("ui-tabs ui-widget ui-widget-content ui-corner-all ui-tabs-collapsible").removeData("tabs"), this.list.removeClass("ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all"), this.anchors.each(function () {
                var b = a.data(this, "href.tabs");
                b && (this.href = b);
                var c = a(this).unbind(".tabs");
                a.each(["href", "load", "cache"], function (a, b) {
                    c.removeData(b + ".tabs")
                })
            }), this.lis.unbind(".tabs").add(this.panels).each(function () {
                a.data(this, "destroy.tabs") ? a(this).remove() : a(this).removeClass(["ui-state-default", "ui-corner-top", "ui-tabs-selected", "ui-state-active", "ui-state-hover", "ui-state-focus", "ui-state-disabled", "ui-tabs-panel", "ui-widget-content", "ui-corner-bottom", "ui-tabs-hide"].join(" "))
            }), b.cookie && this._cookie(null, b.cookie), this
        },
        add: function (c, d, e) {
            e === b && (e = this.anchors.length);
            var f = this,
                g = this.options,
                h = a(g.tabTemplate.replace(/#\{href\}/g, c).replace(/#\{label\}/g, d)),
                i = c.indexOf("#") ? this._tabId(a("a", h)[0]) : c.replace("#", "");
            h.addClass("ui-state-default ui-corner-top").data("destroy.tabs", !0);
            var j = f.element.find("#" + i);
            return j.length || (j = a(g.panelTemplate).attr("id", i).data("destroy.tabs", !0)), j.addClass("ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide"), e >= this.lis.length ? (h.appendTo(this.list), j.appendTo(this.list[0].parentNode)) : (h.insertBefore(this.lis[e]), j.insertBefore(this.panels[e])), g.disabled = a.map(g.disabled, function (a, b) {
                return a >= e ? ++a : a
            }), this._tabify(), this.anchors.length == 1 && (g.selected = 0, h.addClass("ui-tabs-selected ui-state-active"), j.removeClass("ui-tabs-hide"), this.element.queue("tabs", function () {
                f._trigger("show", null, f._ui(f.anchors[0], f.panels[0]))
            }), this.load(0)), this._trigger("add", null, this._ui(this.anchors[e], this.panels[e])), this
        },
        remove: function (b) {
            b = this._getIndex(b);
            var c = this.options,
                d = this.lis.eq(b).remove(),
                e = this.panels.eq(b).remove();
            return d.hasClass("ui-tabs-selected") && this.anchors.length > 1 && this.select(b + (b + 1 < this.anchors.length ? 1 : -1)), c.disabled = a.map(a.grep(c.disabled, function (a, c) {
                return a != b
            }), function (a, c) {
                return a >= b ? --a : a
            }), this._tabify(), this._trigger("remove", null, this._ui(d.find("a")[0], e[0])), this
        },
        enable: function (b) {
            b = this._getIndex(b);
            var c = this.options;
            if (a.inArray(b, c.disabled) == -1) return;
            return this.lis.eq(b).removeClass("ui-state-disabled"), c.disabled = a.grep(c.disabled, function (a, c) {
                return a != b
            }), this._trigger("enable", null, this._ui(this.anchors[b], this.panels[b])), this
        },
        disable: function (a) {
            a = this._getIndex(a);
            var b = this,
                c = this.options;
            return a != c.selected && (this.lis.eq(a).addClass("ui-state-disabled"), c.disabled.push(a), c.disabled.sort(), this._trigger("disable", null, this._ui(this.anchors[a], this.panels[a]))), this
        },
        select: function (a) {
            a = this._getIndex(a);
            if (a == -1)
                if (this.options.collapsible && this.options.selected != -1) a = this.options.selected;
                else return this;
            return this.anchors.eq(a).trigger(this.options.event + ".tabs"), this
        },
        load: function (b) {
            b = this._getIndex(b);
            var c = this,
                d = this.options,
                e = this.anchors.eq(b)[0],
                f = a.data(e, "load.tabs");
            this.abort();
            if (!f || this.element.queue("tabs").length !== 0 && a.data(e, "cache.tabs")) {
                this.element.dequeue("tabs");
                return
            }
            this.lis.eq(b).addClass("ui-state-processing");
            if (d.spinner) {
                var g = a("span", e);
                g.data("label.tabs", g.html()).html(d.spinner)
            }
            return this.xhr = a.ajax(a.extend({}, d.ajaxOptions, {
                url: f,
                success: function (f, g) {
                    c.element.find(c._sanitizeSelector(e.hash)).html(f), c._cleanup(), d.cache && a.data(e, "cache.tabs", !0), c._trigger("load", null, c._ui(c.anchors[b], c.panels[b]));
                    try {
                        d.ajaxOptions.success(f, g)
                    } catch (h) {}
                },
                error: function (a, f, g) {
                    c._cleanup(), c._trigger("load", null, c._ui(c.anchors[b], c.panels[b]));
                    try {
                        d.ajaxOptions.error(a, f, b, e)
                    } catch (g) {}
                }
            })), c.element.dequeue("tabs"), this
        },
        abort: function () {
            return this.element.queue([]), this.panels.stop(!1, !0), this.element.queue("tabs", this.element.queue("tabs").splice(-2, 2)), this.xhr && (this.xhr.abort(), delete this.xhr), this._cleanup(), this
        },
        url: function (a, b) {
            return this.anchors.eq(a).removeData("cache.tabs").data("load.tabs", b), this
        },
        length: function () {
            return this.anchors.length
        }
    }), a.extend(a.ui.tabs, {
        version: "1.8.21"
    }), a.extend(a.ui.tabs.prototype, {
        rotation: null,
        rotate: function (a, b) {
            var c = this,
                d = this.options,
                e = c._rotate || (c._rotate = function (b) {
                    clearTimeout(c.rotation), c.rotation = setTimeout(function () {
                        var a = d.selected;
                        c.select(++a < c.anchors.length ? a : 0)
                    }, a), b && b.stopPropagation()
                }),
                f = c._unrotate || (c._unrotate = b ? function (a) {
                    e()
                } : function (a) {
                    a.clientX && c.rotate(null)
                });
            return a ? (this.element.bind("tabsshow", e), this.anchors.bind(d.event + ".tabs", f), e()) : (clearTimeout(c.rotation), this.element.unbind("tabsshow", e), this.anchors.unbind(d.event + ".tabs", f), delete this._rotate, delete this._unrotate), this
        }
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.datepicker.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function ($, undefined) {
    function Datepicker() {
        this.debug = !1, this._curInst = null, this._keyEvent = !1, this._disabledInputs = [], this._datepickerShowing = !1, this._inDialog = !1, this._mainDivId = "ui-datepicker-div", this._inlineClass = "ui-datepicker-inline", this._appendClass = "ui-datepicker-append", this._triggerClass = "ui-datepicker-trigger", this._dialogClass = "ui-datepicker-dialog", this._disableClass = "ui-datepicker-disabled", this._unselectableClass = "ui-datepicker-unselectable", this._currentClass = "ui-datepicker-current-day", this._dayOverClass = "ui-datepicker-days-cell-over", this.regional = [], this.regional[""] = {
            closeText: "Done",
            prevText: "Prev",
            nextText: "Next",
            currentText: "Today",
            monthNames: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            monthNamesShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            dayNames: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
            dayNamesShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            dayNamesMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"],
            weekHeader: "Wk",
            dateFormat: "mm/dd/yy",
            firstDay: 0,
            isRTL: !1,
            showMonthAfterYear: !1,
            yearSuffix: ""
        }, this._defaults = {
            showOn: "focus",
            showAnim: "fadeIn",
            showOptions: {},
            defaultDate: null,
            appendText: "",
            buttonText: "...",
            buttonImage: "",
            buttonImageOnly: !1,
            hideIfNoPrevNext: !1,
            navigationAsDateFormat: !1,
            gotoCurrent: !1,
            changeMonth: !1,
            changeYear: !1,
            yearRange: "c-10:c+10",
            showOtherMonths: !1,
            selectOtherMonths: !1,
            showWeek: !1,
            calculateWeek: this.iso8601Week,
            shortYearCutoff: "+10",
            minDate: null,
            maxDate: null,
            duration: "fast",
            beforeShowDay: null,
            beforeShow: null,
            onSelect: null,
            onChangeMonthYear: null,
            onClose: null,
            numberOfMonths: 1,
            showCurrentAtPos: 0,
            stepMonths: 1,
            stepBigMonths: 12,
            altField: "",
            altFormat: "",
            constrainInput: !0,
            showButtonPanel: !1,
            autoSize: !1,
            disabled: !1
        }, $.extend(this._defaults, this.regional[""]), this.dpDiv = bindHover($('<div id="' + this._mainDivId + '" class="ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>'))
    }

    function bindHover(a) {
        var b = "button, .ui-datepicker-prev, .ui-datepicker-next, .ui-datepicker-calendar td a";
        return a.bind("mouseout", function (a) {
            var c = $(a.target).closest(b);
            if (!c.length) return;
            c.removeClass("ui-state-hover ui-datepicker-prev-hover ui-datepicker-next-hover")
        }).bind("mouseover", function (c) {
            var d = $(c.target).closest(b);
            if ($.datepicker._isDisabledDatepicker(instActive.inline ? a.parent()[0] : instActive.input[0]) || !d.length) return;
            d.parents(".ui-datepicker-calendar").find("a").removeClass("ui-state-hover"), d.addClass("ui-state-hover"), d.hasClass("ui-datepicker-prev") && d.addClass("ui-datepicker-prev-hover"), d.hasClass("ui-datepicker-next") && d.addClass("ui-datepicker-next-hover")
        })
    }

    function extendRemove(a, b) {
        $.extend(a, b);
        for (var c in b)
            if (b[c] == null || b[c] == undefined) a[c] = b[c];
        return a
    }

    function isArray(a) {
        return a && ($.browser.safari && typeof a == "object" && a.length || a.constructor && a.constructor.toString().match(/\Array\(\)/))
    }
    $.extend($.ui, {
        datepicker: {
            version: "1.8.21"
        }
    });
    var PROP_NAME = "datepicker",
        dpuuid = (new Date).getTime(),
        instActive;
    $.extend(Datepicker.prototype, {
        markerClassName: "hasDatepicker",
        maxRows: 4,
        log: function () {
            this.debug && console.log.apply("", arguments)
        },
        _widgetDatepicker: function () {
            return this.dpDiv
        },
        setDefaults: function (a) {
            return extendRemove(this._defaults, a || {}), this
        },
        _attachDatepicker: function (target, settings) {
            var inlineSettings = null;
            for (var attrName in this._defaults) {
                var attrValue = target.getAttribute("date:" + attrName);
                if (attrValue) {
                    inlineSettings = inlineSettings || {};
                    try {
                        inlineSettings[attrName] = eval(attrValue)
                    } catch (err) {
                        inlineSettings[attrName] = attrValue
                    }
                }
            }
            var nodeName = target.nodeName.toLowerCase(),
                inline = nodeName == "div" || nodeName == "span";
            target.id || (this.uuid += 1, target.id = "dp" + this.uuid);
            var inst = this._newInst($(target), inline);
            inst.settings = $.extend({}, settings || {}, inlineSettings || {}), nodeName == "input" ? this._connectDatepicker(target, inst) : inline && this._inlineDatepicker(target, inst)
        },
        _newInst: function (a, b) {
            var c = a[0].id.replace(/([^A-Za-z0-9_-])/g, "\\\\$1");
            return {
                id: c,
                input: a,
                selectedDay: 0,
                selectedMonth: 0,
                selectedYear: 0,
                drawMonth: 0,
                drawYear: 0,
                inline: b,
                dpDiv: b ? bindHover($('<div class="' + this._inlineClass + ' ui-datepicker ui-widget ui-widget-content ui-helper-clearfix ui-corner-all"></div>')) : this.dpDiv
            }
        },
        _connectDatepicker: function (a, b) {
            var c = $(a);
            b.append = $([]), b.trigger = $([]);
            if (c.hasClass(this.markerClassName)) return;
            this._attachments(c, b), c.addClass(this.markerClassName).keydown(this._doKeyDown).keypress(this._doKeyPress).keyup(this._doKeyUp).bind("setData.datepicker", function (a, c, d) {
                b.settings[c] = d
            }).bind("getData.datepicker", function (a, c) {
                return this._get(b, c)
            }), this._autoSize(b), $.data(a, PROP_NAME, b), b.settings.disabled && this._disableDatepicker(a)
        },
        _attachments: function (a, b) {
            var c = this._get(b, "appendText"),
                d = this._get(b, "isRTL");
            b.append && b.append.remove(), c && (b.append = $('<span class="' + this._appendClass + '">' + c + "</span>"), a[d ? "before" : "after"](b.append)), a.unbind("focus", this._showDatepicker), b.trigger && b.trigger.remove();
            var e = this._get(b, "showOn");
            (e == "focus" || e == "both") && a.focus(this._showDatepicker);
            if (e == "button" || e == "both") {
                var f = this._get(b, "buttonText"),
                    g = this._get(b, "buttonImage");
                b.trigger = $(this._get(b, "buttonImageOnly") ? $("<img/>").addClass(this._triggerClass).attr({
                    src: g,
                    alt: f,
                    title: f
                }) : $('<button type="button"></button>').addClass(this._triggerClass).html(g == "" ? f : $("<img/>").attr({
                    src: g,
                    alt: f,
                    title: f
                }))), a[d ? "before" : "after"](b.trigger), b.trigger.click(function () {
                    return $.datepicker._datepickerShowing && $.datepicker._lastInput == a[0] ? $.datepicker._hideDatepicker() : $.datepicker._datepickerShowing && $.datepicker._lastInput != a[0] ? ($.datepicker._hideDatepicker(), $.datepicker._showDatepicker(a[0])) : $.datepicker._showDatepicker(a[0]), !1
                })
            }
        },
        _autoSize: function (a) {
            if (this._get(a, "autoSize") && !a.inline) {
                var b = new Date(2009, 11, 20),
                    c = this._get(a, "dateFormat");
                if (c.match(/[DM]/)) {
                    var d = function (a) {
                        var b = 0,
                            c = 0;
                        for (var d = 0; d < a.length; d++) a[d].length > b && (b = a[d].length, c = d);
                        return c
                    };
                    b.setMonth(d(this._get(a, c.match(/MM/) ? "monthNames" : "monthNamesShort"))), b.setDate(d(this._get(a, c.match(/DD/) ? "dayNames" : "dayNamesShort")) + 20 - b.getDay())
                }
                a.input.attr("size", this._formatDate(a, b).length)
            }
        },
        _inlineDatepicker: function (a, b) {
            var c = $(a);
            if (c.hasClass(this.markerClassName)) return;
            c.addClass(this.markerClassName).append(b.dpDiv).bind("setData.datepicker", function (a, c, d) {
                b.settings[c] = d
            }).bind("getData.datepicker", function (a, c) {
                return this._get(b, c)
            }), $.data(a, PROP_NAME, b), this._setDate(b, this._getDefaultDate(b), !0), this._updateDatepicker(b), this._updateAlternate(b), b.settings.disabled && this._disableDatepicker(a), b.dpDiv.css("display", "block")
        },
        _dialogDatepicker: function (a, b, c, d, e) {
            var f = this._dialogInst;
            if (!f) {
                this.uuid += 1;
                var g = "dp" + this.uuid;
                this._dialogInput = $('<input type="text" id="' + g + '" style="position: absolute; top: -100px; width: 0px; z-index: -10;"/>'), this._dialogInput.keydown(this._doKeyDown), $("body").append(this._dialogInput), f = this._dialogInst = this._newInst(this._dialogInput, !1), f.settings = {}, $.data(this._dialogInput[0], PROP_NAME, f)
            }
            extendRemove(f.settings, d || {}), b = b && b.constructor == Date ? this._formatDate(f, b) : b, this._dialogInput.val(b), this._pos = e ? e.length ? e : [e.pageX, e.pageY] : null;
            if (!this._pos) {
                var h = document.documentElement.clientWidth,
                    i = document.documentElement.clientHeight,
                    j = document.documentElement.scrollLeft || document.body.scrollLeft,
                    k = document.documentElement.scrollTop || document.body.scrollTop;
                this._pos = [h / 2 - 100 + j, i / 2 - 150 + k]
            }
            return this._dialogInput.css("left", this._pos[0] + 20 + "px").css("top", this._pos[1] + "px"), f.settings.onSelect = c, this._inDialog = !0, this.dpDiv.addClass(this._dialogClass), this._showDatepicker(this._dialogInput[0]), $.blockUI && $.blockUI(this.dpDiv), $.data(this._dialogInput[0], PROP_NAME, f), this
        },
        _destroyDatepicker: function (a) {
            var b = $(a),
                c = $.data(a, PROP_NAME);
            if (!b.hasClass(this.markerClassName)) return;
            var d = a.nodeName.toLowerCase();
            $.removeData(a, PROP_NAME), d == "input" ? (c.append.remove(), c.trigger.remove(), b.removeClass(this.markerClassName).unbind("focus", this._showDatepicker).unbind("keydown", this._doKeyDown).unbind("keypress", this._doKeyPress).unbind("keyup", this._doKeyUp)) : (d == "div" || d == "span") && b.removeClass(this.markerClassName).empty()
        },
        _enableDatepicker: function (a) {
            var b = $(a),
                c = $.data(a, PROP_NAME);
            if (!b.hasClass(this.markerClassName)) return;
            var d = a.nodeName.toLowerCase();
            if (d == "input") a.disabled = !1, c.trigger.filter("button").each(function () {
                this.disabled = !1
            }).end().filter("img").css({
                opacity: "1.0",
                cursor: ""
            });
            else if (d == "div" || d == "span") {
                var e = b.children("." + this._inlineClass);
                e.children().removeClass("ui-state-disabled"), e.find("select.ui-datepicker-month, select.ui-datepicker-year").removeAttr("disabled")
            }
            this._disabledInputs = $.map(this._disabledInputs, function (b) {
                return b == a ? null : b
            })
        },
        _disableDatepicker: function (a) {
            var b = $(a),
                c = $.data(a, PROP_NAME);
            if (!b.hasClass(this.markerClassName)) return;
            var d = a.nodeName.toLowerCase();
            if (d == "input") a.disabled = !0, c.trigger.filter("button").each(function () {
                this.disabled = !0
            }).end().filter("img").css({
                opacity: "0.5",
                cursor: "default"
            });
            else if (d == "div" || d == "span") {
                var e = b.children("." + this._inlineClass);
                e.children().addClass("ui-state-disabled"), e.find("select.ui-datepicker-month, select.ui-datepicker-year").attr("disabled", "disabled")
            }
            this._disabledInputs = $.map(this._disabledInputs, function (b) {
                return b == a ? null : b
            }), this._disabledInputs[this._disabledInputs.length] = a
        },
        _isDisabledDatepicker: function (a) {
            if (!a) return !1;
            for (var b = 0; b < this._disabledInputs.length; b++)
                if (this._disabledInputs[b] == a) return !0;
            return !1
        },
        _getInst: function (a) {
            try {
                return $.data(a, PROP_NAME)
            } catch (b) {
                throw new Error("Missing instance data for this datepicker")
            }
        },
        _optionDatepicker: function (a, b, c) {
            var d = this._getInst(a);
            if (arguments.length == 2 && typeof b == "string") return b == "defaults" ? $.extend({}, $.datepicker._defaults) : d ? b == "all" ? $.extend({}, d.settings) : this._get(d, b) : null;
            var e = b || {};
            typeof b == "string" && (e = {}, e[b] = c);
            if (d) {
                this._curInst == d && this._hideDatepicker();
                var f = this._getDateDatepicker(a, !0),
                    g = this._getMinMaxDate(d, "min"),
                    h = this._getMinMaxDate(d, "max");
                extendRemove(d.settings, e), g !== null && e.dateFormat !== undefined && e.minDate === undefined && (d.settings.minDate = this._formatDate(d, g)), h !== null && e.dateFormat !== undefined && e.maxDate === undefined && (d.settings.maxDate = this._formatDate(d, h)), this._attachments($(a), d), this._autoSize(d), this._setDate(d, f), this._updateAlternate(d), this._updateDatepicker(d)
            }
        },
        _changeDatepicker: function (a, b, c) {
            this._optionDatepicker(a, b, c)
        },
        _refreshDatepicker: function (a) {
            var b = this._getInst(a);
            b && this._updateDatepicker(b)
        },
        _setDateDatepicker: function (a, b) {
            var c = this._getInst(a);
            c && (this._setDate(c, b), this._updateDatepicker(c), this._updateAlternate(c))
        },
        _getDateDatepicker: function (a, b) {
            var c = this._getInst(a);
            return c && !c.inline && this._setDateFromField(c, b), c ? this._getDate(c) : null
        },
        _doKeyDown: function (a) {
            var b = $.datepicker._getInst(a.target),
                c = !0,
                d = b.dpDiv.is(".ui-datepicker-rtl");
            b._keyEvent = !0;
            if ($.datepicker._datepickerShowing) switch (a.keyCode) {
                case 9:
                    $.datepicker._hideDatepicker(), c = !1;
                    break;
                case 13:
                    var e = $("td." + $.datepicker._dayOverClass + ":not(." + $.datepicker._currentClass + ")", b.dpDiv);
                    e[0] && $.datepicker._selectDay(a.target, b.selectedMonth, b.selectedYear, e[0]);
                    var f = $.datepicker._get(b, "onSelect");
                    if (f) {
                        var g = $.datepicker._formatDate(b);
                        f.apply(b.input ? b.input[0] : null, [g, b])
                    } else $.datepicker._hideDatepicker();
                    return !1;
                case 27:
                    $.datepicker._hideDatepicker();
                    break;
                case 33:
                    $.datepicker._adjustDate(a.target, a.ctrlKey ? -$.datepicker._get(b, "stepBigMonths") : -$.datepicker._get(b, "stepMonths"), "M");
                    break;
                case 34:
                    $.datepicker._adjustDate(a.target, a.ctrlKey ? +$.datepicker._get(b, "stepBigMonths") : +$.datepicker._get(b, "stepMonths"), "M");
                    break;
                case 35:
                    (a.ctrlKey || a.metaKey) && $.datepicker._clearDate(a.target), c = a.ctrlKey || a.metaKey;
                    break;
                case 36:
                    (a.ctrlKey || a.metaKey) && $.datepicker._gotoToday(a.target), c = a.ctrlKey || a.metaKey;
                    break;
                case 37:
                    (a.ctrlKey || a.metaKey) && $.datepicker._adjustDate(a.target, d ? 1 : -1, "D"), c = a.ctrlKey || a.metaKey, a.originalEvent.altKey && $.datepicker._adjustDate(a.target, a.ctrlKey ? -$.datepicker._get(b, "stepBigMonths") : -$.datepicker._get(b, "stepMonths"), "M");
                    break;
                case 38:
                    (a.ctrlKey || a.metaKey) && $.datepicker._adjustDate(a.target, -7, "D"), c = a.ctrlKey || a.metaKey;
                    break;
                case 39:
                    (a.ctrlKey || a.metaKey) && $.datepicker._adjustDate(a.target, d ? -1 : 1, "D"), c = a.ctrlKey || a.metaKey, a.originalEvent.altKey && $.datepicker._adjustDate(a.target, a.ctrlKey ? +$.datepicker._get(b, "stepBigMonths") : +$.datepicker._get(b, "stepMonths"), "M");
                    break;
                case 40:
                    (a.ctrlKey || a.metaKey) && $.datepicker._adjustDate(a.target, 7, "D"), c = a.ctrlKey || a.metaKey;
                    break;
                default:
                    c = !1
            } else a.keyCode == 36 && a.ctrlKey ? $.datepicker._showDatepicker(this) : c = !1;
            c && (a.preventDefault(), a.stopPropagation())
        },
        _doKeyPress: function (a) {
            var b = $.datepicker._getInst(a.target);
            if ($.datepicker._get(b, "constrainInput")) {
                var c = $.datepicker._possibleChars($.datepicker._get(b, "dateFormat")),
                    d = String.fromCharCode(a.charCode == undefined ? a.keyCode : a.charCode);
                return a.ctrlKey || a.metaKey || d < " " || !c || c.indexOf(d) > -1
            }
        },
        _doKeyUp: function (a) {
            var b = $.datepicker._getInst(a.target);
            if (b.input.val() != b.lastVal) try {
                var c = $.datepicker.parseDate($.datepicker._get(b, "dateFormat"), b.input ? b.input.val() : null, $.datepicker._getFormatConfig(b));
                c && ($.datepicker._setDateFromField(b), $.datepicker._updateAlternate(b), $.datepicker._updateDatepicker(b))
            } catch (d) {
                $.datepicker.log(d)
            }
            return !0
        },
        _showDatepicker: function (a) {
            a = a.target || a, a.nodeName.toLowerCase() != "input" && (a = $("input", a.parentNode)[0]);
            if ($.datepicker._isDisabledDatepicker(a) || $.datepicker._lastInput == a) return;
            var b = $.datepicker._getInst(a);
            $.datepicker._curInst && $.datepicker._curInst != b && ($.datepicker._curInst.dpDiv.stop(!0, !0), b && $.datepicker._datepickerShowing && $.datepicker._hideDatepicker($.datepicker._curInst.input[0]));
            var c = $.datepicker._get(b, "beforeShow"),
                d = c ? c.apply(a, [a, b]) : {};
            if (d === !1) return;
            extendRemove(b.settings, d), b.lastVal = null, $.datepicker._lastInput = a, $.datepicker._setDateFromField(b), $.datepicker._inDialog && (a.value = ""), $.datepicker._pos || ($.datepicker._pos = $.datepicker._findPos(a), $.datepicker._pos[1] += a.offsetHeight);
            var e = !1;
            $(a).parents().each(function () {
                return e |= $(this).css("position") == "fixed", !e
            }), e && $.browser.opera && ($.datepicker._pos[0] -= document.documentElement.scrollLeft, $.datepicker._pos[1] -= document.documentElement.scrollTop);
            var f = {
                left: $.datepicker._pos[0],
                top: $.datepicker._pos[1]
            };
            $.datepicker._pos = null, b.dpDiv.empty(), b.dpDiv.css({
                position: "absolute",
                display: "block",
                top: "-1000px"
            }), $.datepicker._updateDatepicker(b), f = $.datepicker._checkOffset(b, f, e), b.dpDiv.css({
                position: $.datepicker._inDialog && $.blockUI ? "static" : e ? "fixed" : "absolute",
                display: "none",
                left: f.left + "px",
                top: f.top + "px"
            });
            if (!b.inline) {
                var g = $.datepicker._get(b, "showAnim"),
                    h = $.datepicker._get(b, "duration"),
                    i = function () {
                        var a = b.dpDiv.find("iframe.ui-datepicker-cover");
                        if ( !! a.length) {
                            var c = $.datepicker._getBorders(b.dpDiv);
                            a.css({
                                left: -c[0],
                                top: -c[1],
                                width: b.dpDiv.outerWidth(),
                                height: b.dpDiv.outerHeight()
                            })
                        }
                    };
                b.dpDiv.zIndex($(a).zIndex() + 1), $.datepicker._datepickerShowing = !0, $.effects && $.effects[g] ? b.dpDiv.show(g, $.datepicker._get(b, "showOptions"), h, i) : b.dpDiv[g || "show"](g ? h : null, i), (!g || !h) && i(), b.input.is(":visible") && !b.input.is(":disabled") && b.input.focus(), $.datepicker._curInst = b
            }
        },
        _updateDatepicker: function (a) {
            var b = this;
            b.maxRows = 4;
            var c = $.datepicker._getBorders(a.dpDiv);
            instActive = a, a.dpDiv.empty().append(this._generateHTML(a));
            var d = a.dpDiv.find("iframe.ui-datepicker-cover");
            !d.length || d.css({
                left: -c[0],
                top: -c[1],
                width: a.dpDiv.outerWidth(),
                height: a.dpDiv.outerHeight()
            }), a.dpDiv.find("." + this._dayOverClass + " a").mouseover();
            var e = this._getNumberOfMonths(a),
                f = e[1],
                g = 17;
            a.dpDiv.removeClass("ui-datepicker-multi-2 ui-datepicker-multi-3 ui-datepicker-multi-4").width(""), f > 1 && a.dpDiv.addClass("ui-datepicker-multi-" + f).css("width", g * f + "em"), a.dpDiv[(e[0] != 1 || e[1] != 1 ? "add" : "remove") + "Class"]("ui-datepicker-multi"), a.dpDiv[(this._get(a, "isRTL") ? "add" : "remove") + "Class"]("ui-datepicker-rtl"), a == $.datepicker._curInst && $.datepicker._datepickerShowing && a.input && a.input.is(":visible") && !a.input.is(":disabled") && a.input[0] != document.activeElement && a.input.focus();
            if (a.yearshtml) {
                var h = a.yearshtml;
                setTimeout(function () {
                    h === a.yearshtml && a.yearshtml && a.dpDiv.find("select.ui-datepicker-year:first").replaceWith(a.yearshtml), h = a.yearshtml = null
                }, 0)
            }
        },
        _getBorders: function (a) {
            var b = function (a) {
                return {
                    thin: 1,
                    medium: 2,
                    thick: 3
                }[a] || a
            };
            return [parseFloat(b(a.css("border-left-width"))), parseFloat(b(a.css("border-top-width")))]
        },
        _checkOffset: function (a, b, c) {
            var d = a.dpDiv.outerWidth(),
                e = a.dpDiv.outerHeight(),
                f = a.input ? a.input.outerWidth() : 0,
                g = a.input ? a.input.outerHeight() : 0,
                h = document.documentElement.clientWidth + $(document).scrollLeft(),
                i = document.documentElement.clientHeight + $(document).scrollTop();
            return b.left -= this._get(a, "isRTL") ? d - f : 0, b.left -= c && b.left == a.input.offset().left ? $(document).scrollLeft() : 0, b.top -= c && b.top == a.input.offset().top + g ? $(document).scrollTop() : 0, b.left -= Math.min(b.left, b.left + d > h && h > d ? Math.abs(b.left + d - h) : 0), b.top -= Math.min(b.top, b.top + e > i && i > e ? Math.abs(e + g) : 0), b
        },
        _findPos: function (a) {
            var b = this._getInst(a),
                c = this._get(b, "isRTL");
            while (a && (a.type == "hidden" || a.nodeType != 1 || $.expr.filters.hidden(a))) a = a[c ? "previousSibling" : "nextSibling"];
            var d = $(a).offset();
            return [d.left, d.top]
        },
        _hideDatepicker: function (a) {
            var b = this._curInst;
            if (!b || a && b != $.data(a, PROP_NAME)) return;
            if (this._datepickerShowing) {
                var c = this._get(b, "showAnim"),
                    d = this._get(b, "duration"),
                    e = function () {
                        $.datepicker._tidyDialog(b)
                    };
                $.effects && $.effects[c] ? b.dpDiv.hide(c, $.datepicker._get(b, "showOptions"), d, e) : b.dpDiv[c == "slideDown" ? "slideUp" : c == "fadeIn" ? "fadeOut" : "hide"](c ? d : null, e), c || e(), this._datepickerShowing = !1;
                var f = this._get(b, "onClose");
                f && f.apply(b.input ? b.input[0] : null, [b.input ? b.input.val() : "", b]), this._lastInput = null, this._inDialog && (this._dialogInput.css({
                    position: "absolute",
                    left: "0",
                    top: "-100px"
                }), $.blockUI && ($.unblockUI(), $("body").append(this.dpDiv))), this._inDialog = !1
            }
        },
        _tidyDialog: function (a) {
            a.dpDiv.removeClass(this._dialogClass).unbind(".ui-datepicker-calendar")
        },
        _checkExternalClick: function (a) {
            if (!$.datepicker._curInst) return;
            var b = $(a.target),
                c = $.datepicker._getInst(b[0]);
            (b[0].id != $.datepicker._mainDivId && b.parents("#" + $.datepicker._mainDivId).length == 0 && !b.hasClass($.datepicker.markerClassName) && !b.closest("." + $.datepicker._triggerClass).length && $.datepicker._datepickerShowing && (!$.datepicker._inDialog || !$.blockUI) || b.hasClass($.datepicker.markerClassName) && $.datepicker._curInst != c) && $.datepicker._hideDatepicker()
        },
        _adjustDate: function (a, b, c) {
            var d = $(a),
                e = this._getInst(d[0]);
            if (this._isDisabledDatepicker(d[0])) return;
            this._adjustInstDate(e, b + (c == "M" ? this._get(e, "showCurrentAtPos") : 0), c), this._updateDatepicker(e)
        },
        _gotoToday: function (a) {
            var b = $(a),
                c = this._getInst(b[0]);
            if (this._get(c, "gotoCurrent") && c.currentDay) c.selectedDay = c.currentDay, c.drawMonth = c.selectedMonth = c.currentMonth, c.drawYear = c.selectedYear = c.currentYear;
            else {
                var d = new Date;
                c.selectedDay = d.getDate(), c.drawMonth = c.selectedMonth = d.getMonth(), c.drawYear = c.selectedYear = d.getFullYear()
            }
            this._notifyChange(c), this._adjustDate(b)
        },
        _selectMonthYear: function (a, b, c) {
            var d = $(a),
                e = this._getInst(d[0]);
            e["selected" + (c == "M" ? "Month" : "Year")] = e["draw" + (c == "M" ? "Month" : "Year")] = parseInt(b.options[b.selectedIndex].value, 10), this._notifyChange(e), this._adjustDate(d)
        },
        _selectDay: function (a, b, c, d) {
            var e = $(a);
            if ($(d).hasClass(this._unselectableClass) || this._isDisabledDatepicker(e[0])) return;
            var f = this._getInst(e[0]);
            f.selectedDay = f.currentDay = $("a", d).html(), f.selectedMonth = f.currentMonth = b, f.selectedYear = f.currentYear = c, this._selectDate(a, this._formatDate(f, f.currentDay, f.currentMonth, f.currentYear))
        },
        _clearDate: function (a) {
            var b = $(a),
                c = this._getInst(b[0]);
            this._selectDate(b, "")
        },
        _selectDate: function (a, b) {
            var c = $(a),
                d = this._getInst(c[0]);
            b = b != null ? b : this._formatDate(d), d.input && d.input.val(b), this._updateAlternate(d);
            var e = this._get(d, "onSelect");
            e ? e.apply(d.input ? d.input[0] : null, [b, d]) : d.input && d.input.trigger("change"), d.inline ? this._updateDatepicker(d) : (this._hideDatepicker(), this._lastInput = d.input[0], typeof d.input[0] != "object" && d.input.focus(), this._lastInput = null)
        },
        _updateAlternate: function (a) {
            var b = this._get(a, "altField");
            if (b) {
                var c = this._get(a, "altFormat") || this._get(a, "dateFormat"),
                    d = this._getDate(a),
                    e = this.formatDate(c, d, this._getFormatConfig(a));
                $(b).each(function () {
                    $(this).val(e)
                })
            }
        },
        noWeekends: function (a) {
            var b = a.getDay();
            return [b > 0 && b < 6, ""]
        },
        iso8601Week: function (a) {
            var b = new Date(a.getTime());
            b.setDate(b.getDate() + 4 - (b.getDay() || 7));
            var c = b.getTime();
            return b.setMonth(0), b.setDate(1), Math.floor(Math.round((c - b) / 864e5) / 7) + 1
        },
        parseDate: function (a, b, c) {
            if (a == null || b == null) throw "Invalid arguments";
            b = typeof b == "object" ? b.toString() : b + "";
            if (b == "") return null;
            var d = (c ? c.shortYearCutoff : null) || this._defaults.shortYearCutoff;
            d = typeof d != "string" ? d : (new Date).getFullYear() % 100 + parseInt(d, 10);
            var e = (c ? c.dayNamesShort : null) || this._defaults.dayNamesShort,
                f = (c ? c.dayNames : null) || this._defaults.dayNames,
                g = (c ? c.monthNamesShort : null) || this._defaults.monthNamesShort,
                h = (c ? c.monthNames : null) || this._defaults.monthNames,
                i = -1,
                j = -1,
                k = -1,
                l = -1,
                m = !1,
                n = function (b) {
                    var c = s + 1 < a.length && a.charAt(s + 1) == b;
                    return c && s++, c
                }, o = function (a) {
                    var c = n(a),
                        d = a == "@" ? 14 : a == "!" ? 20 : a == "y" && c ? 4 : a == "o" ? 3 : 2,
                        e = new RegExp("^\\d{1," + d + "}"),
                        f = b.substring(r).match(e);
                    if (!f) throw "Missing number at position " + r;
                    return r += f[0].length, parseInt(f[0], 10)
                }, p = function (a, c, d) {
                    var e = $.map(n(a) ? d : c, function (a, b) {
                            return [[b, a]]
                        }).sort(function (a, b) {
                            return -(a[1].length - b[1].length)
                        }),
                        f = -1;
                    $.each(e, function (a, c) {
                        var d = c[1];
                        if (b.substr(r, d.length).toLowerCase() == d.toLowerCase()) return f = c[0], r += d.length, !1
                    });
                    if (f != -1) return f + 1;
                    throw "Unknown name at position " + r
                }, q = function () {
                    if (b.charAt(r) != a.charAt(s)) throw "Unexpected literal at position " + r;
                    r++
                }, r = 0;
            for (var s = 0; s < a.length; s++)
                if (m) a.charAt(s) == "'" && !n("'") ? m = !1 : q();
                else switch (a.charAt(s)) {
                    case "d":
                        k = o("d");
                        break;
                    case "D":
                        p("D", e, f);
                        break;
                    case "o":
                        l = o("o");
                        break;
                    case "m":
                        j = o("m");
                        break;
                    case "M":
                        j = p("M", g, h);
                        break;
                    case "y":
                        i = o("y");
                        break;
                    case "@":
                        var t = new Date(o("@"));
                        i = t.getFullYear(), j = t.getMonth() + 1, k = t.getDate();
                        break;
                    case "!":
                        var t = new Date((o("!") - this._ticksTo1970) / 1e4);
                        i = t.getFullYear(), j = t.getMonth() + 1, k = t.getDate();
                        break;
                    case "'":
                        n("'") ? q() : m = !0;
                        break;
                    default:
                        q()
                }
            if (r < b.length) throw "Extra/unparsed characters found in date: " + b.substring(r);
            i == -1 ? i = (new Date).getFullYear() : i < 100 && (i += (new Date).getFullYear() - (new Date).getFullYear() % 100 + (i <= d ? 0 : -100));
            if (l > -1) {
                j = 1, k = l;
                do {
                    var u = this._getDaysInMonth(i, j - 1);
                    if (k <= u) break;
                    j++, k -= u
                } while (!0)
            }
            var t = this._daylightSavingAdjust(new Date(i, j - 1, k));
            if (t.getFullYear() != i || t.getMonth() + 1 != j || t.getDate() != k) throw "Invalid date";
            return t
        },
        ATOM: "yy-mm-dd",
        COOKIE: "D, dd M yy",
        ISO_8601: "yy-mm-dd",
        RFC_822: "D, d M y",
        RFC_850: "DD, dd-M-y",
        RFC_1036: "D, d M y",
        RFC_1123: "D, d M yy",
        RFC_2822: "D, d M yy",
        RSS: "D, d M y",
        TICKS: "!",
        TIMESTAMP: "@",
        W3C: "yy-mm-dd",
        _ticksTo1970: (718685 + Math.floor(492.5) - Math.floor(19.7) + Math.floor(4.925)) * 24 * 60 * 60 * 1e7,
        formatDate: function (a, b, c) {
            if (!b) return "";
            var d = (c ? c.dayNamesShort : null) || this._defaults.dayNamesShort,
                e = (c ? c.dayNames : null) || this._defaults.dayNames,
                f = (c ? c.monthNamesShort : null) || this._defaults.monthNamesShort,
                g = (c ? c.monthNames : null) || this._defaults.monthNames,
                h = function (b) {
                    var c = m + 1 < a.length && a.charAt(m + 1) == b;
                    return c && m++, c
                }, i = function (a, b, c) {
                    var d = "" + b;
                    if (h(a))
                        while (d.length < c) d = "0" + d;
                    return d
                }, j = function (a, b, c, d) {
                    return h(a) ? d[b] : c[b]
                }, k = "",
                l = !1;
            if (b)
                for (var m = 0; m < a.length; m++)
                    if (l) a.charAt(m) == "'" && !h("'") ? l = !1 : k += a.charAt(m);
                    else switch (a.charAt(m)) {
                        case "d":
                            k += i("d", b.getDate(), 2);
                            break;
                        case "D":
                            k += j("D", b.getDay(), d, e);
                            break;
                        case "o":
                            k += i("o", Math.round(((new Date(b.getFullYear(), b.getMonth(), b.getDate())).getTime() - (new Date(b.getFullYear(), 0, 0)).getTime()) / 864e5), 3);
                            break;
                        case "m":
                            k += i("m", b.getMonth() + 1, 2);
                            break;
                        case "M":
                            k += j("M", b.getMonth(), f, g);
                            break;
                        case "y":
                            k += h("y") ? b.getFullYear() : (b.getYear() % 100 < 10 ? "0" : "") + b.getYear() % 100;
                            break;
                        case "@":
                            k += b.getTime();
                            break;
                        case "!":
                            k += b.getTime() * 1e4 + this._ticksTo1970;
                            break;
                        case "'":
                            h("'") ? k += "'" : l = !0;
                            break;
                        default:
                            k += a.charAt(m)
                    }
            return k
        },
        _possibleChars: function (a) {
            var b = "",
                c = !1,
                d = function (b) {
                    var c = e + 1 < a.length && a.charAt(e + 1) == b;
                    return c && e++, c
                };
            for (var e = 0; e < a.length; e++)
                if (c) a.charAt(e) == "'" && !d("'") ? c = !1 : b += a.charAt(e);
                else switch (a.charAt(e)) {
                    case "d":
                    case "m":
                    case "y":
                    case "@":
                        b += "0123456789";
                        break;
                    case "D":
                    case "M":
                        return null;
                    case "'":
                        d("'") ? b += "'" : c = !0;
                        break;
                    default:
                        b += a.charAt(e)
                }
            return b
        },
        _get: function (a, b) {
            return a.settings[b] !== undefined ? a.settings[b] : this._defaults[b]
        },
        _setDateFromField: function (a, b) {
            if (a.input.val() == a.lastVal) return;
            var c = this._get(a, "dateFormat"),
                d = a.lastVal = a.input ? a.input.val() : null,
                e, f;
            e = f = this._getDefaultDate(a);
            var g = this._getFormatConfig(a);
            try {
                e = this.parseDate(c, d, g) || f
            } catch (h) {
                this.log(h), d = b ? "" : d
            }
            a.selectedDay = e.getDate(), a.drawMonth = a.selectedMonth = e.getMonth(), a.drawYear = a.selectedYear = e.getFullYear(), a.currentDay = d ? e.getDate() : 0, a.currentMonth = d ? e.getMonth() : 0, a.currentYear = d ? e.getFullYear() : 0, this._adjustInstDate(a)
        },
        _getDefaultDate: function (a) {
            return this._restrictMinMax(a, this._determineDate(a, this._get(a, "defaultDate"), new Date))
        },
        _determineDate: function (a, b, c) {
            var d = function (a) {
                var b = new Date;
                return b.setDate(b.getDate() + a), b
            }, e = function (b) {
                try {
                    return $.datepicker.parseDate($.datepicker._get(a, "dateFormat"), b, $.datepicker._getFormatConfig(a))
                } catch (c) {}
                var d = (b.toLowerCase().match(/^c/) ? $.datepicker._getDate(a) : null) || new Date,
                    e = d.getFullYear(),
                    f = d.getMonth(),
                    g = d.getDate(),
                    h = /([+-]?[0-9]+)\s*(d|D|w|W|m|M|y|Y)?/g,
                    i = h.exec(b);
                while (i) {
                    switch (i[2] || "d") {
                        case "d":
                        case "D":
                            g += parseInt(i[1], 10);
                            break;
                        case "w":
                        case "W":
                            g += parseInt(i[1], 10) * 7;
                            break;
                        case "m":
                        case "M":
                            f += parseInt(i[1], 10), g = Math.min(g, $.datepicker._getDaysInMonth(e, f));
                            break;
                        case "y":
                        case "Y":
                            e += parseInt(i[1], 10), g = Math.min(g, $.datepicker._getDaysInMonth(e, f))
                    }
                    i = h.exec(b)
                }
                return new Date(e, f, g)
            }, f = b == null || b === "" ? c : typeof b == "string" ? e(b) : typeof b == "number" ? isNaN(b) ? c : d(b) : new Date(b.getTime());
            return f = f && f.toString() == "Invalid Date" ? c : f, f && (f.setHours(0), f.setMinutes(0), f.setSeconds(0), f.setMilliseconds(0)), this._daylightSavingAdjust(f)
        },
        _daylightSavingAdjust: function (a) {
            return a ? (a.setHours(a.getHours() > 12 ? a.getHours() + 2 : 0), a) : null
        },
        _setDate: function (a, b, c) {
            var d = !b,
                e = a.selectedMonth,
                f = a.selectedYear,
                g = this._restrictMinMax(a, this._determineDate(a, b, new Date));
            a.selectedDay = a.currentDay = g.getDate(), a.drawMonth = a.selectedMonth = a.currentMonth = g.getMonth(), a.drawYear = a.selectedYear = a.currentYear = g.getFullYear(), (e != a.selectedMonth || f != a.selectedYear) && !c && this._notifyChange(a), this._adjustInstDate(a), a.input && a.input.val(d ? "" : this._formatDate(a))
        },
        _getDate: function (a) {
            var b = !a.currentYear || a.input && a.input.val() == "" ? null : this._daylightSavingAdjust(new Date(a.currentYear, a.currentMonth, a.currentDay));
            return b
        },
        _generateHTML: function (a) {
            var b = new Date;
            b = this._daylightSavingAdjust(new Date(b.getFullYear(), b.getMonth(), b.getDate()));
            var c = this._get(a, "isRTL"),
                d = this._get(a, "showButtonPanel"),
                e = this._get(a, "hideIfNoPrevNext"),
                f = this._get(a, "navigationAsDateFormat"),
                g = this._getNumberOfMonths(a),
                h = this._get(a, "showCurrentAtPos"),
                i = this._get(a, "stepMonths"),
                j = g[0] != 1 || g[1] != 1,
                k = this._daylightSavingAdjust(a.currentDay ? new Date(a.currentYear, a.currentMonth, a.currentDay) : new Date(9999, 9, 9)),
                l = this._getMinMaxDate(a, "min"),
                m = this._getMinMaxDate(a, "max"),
                n = a.drawMonth - h,
                o = a.drawYear;
            n < 0 && (n += 12, o--);
            if (m) {
                var p = this._daylightSavingAdjust(new Date(m.getFullYear(), m.getMonth() - g[0] * g[1] + 1, m.getDate()));
                p = l && p < l ? l : p;
                while (this._daylightSavingAdjust(new Date(o, n, 1)) > p) n--, n < 0 && (n = 11, o--)
            }
            a.drawMonth = n, a.drawYear = o;
            var q = this._get(a, "prevText");
            q = f ? this.formatDate(q, this._daylightSavingAdjust(new Date(o, n - i, 1)), this._getFormatConfig(a)) : q;
            var r = this._canAdjustMonth(a, -1, o, n) ? '<a class="ui-datepicker-prev ui-corner-all" onclick="DP_jQuery_' + dpuuid + ".datepicker._adjustDate('#" + a.id + "', -" + i + ", 'M');\"" + ' title="' + q + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "e" : "w") + '">' + q + "</span></a>" : e ? "" : '<a class="ui-datepicker-prev ui-corner-all ui-state-disabled" title="' + q + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "e" : "w") + '">' + q + "</span></a>",
                s = this._get(a, "nextText");
            s = f ? this.formatDate(s, this._daylightSavingAdjust(new Date(o, n + i, 1)), this._getFormatConfig(a)) : s;
            var t = this._canAdjustMonth(a, 1, o, n) ? '<a class="ui-datepicker-next ui-corner-all" onclick="DP_jQuery_' + dpuuid + ".datepicker._adjustDate('#" + a.id + "', +" + i + ", 'M');\"" + ' title="' + s + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "w" : "e") + '">' + s + "</span></a>" : e ? "" : '<a class="ui-datepicker-next ui-corner-all ui-state-disabled" title="' + s + '"><span class="ui-icon ui-icon-circle-triangle-' + (c ? "w" : "e") + '">' + s + "</span></a>",
                u = this._get(a, "currentText"),
                v = this._get(a, "gotoCurrent") && a.currentDay ? k : b;
            u = f ? this.formatDate(u, v, this._getFormatConfig(a)) : u;
            var w = a.inline ? "" : '<button type="button" class="ui-datepicker-close ui-state-default ui-priority-primary ui-corner-all" onclick="DP_jQuery_' + dpuuid + '.datepicker._hideDatepicker();">' + this._get(a, "closeText") + "</button>",
                x = d ? '<div class="ui-datepicker-buttonpane ui-widget-content">' + (c ? w : "") + (this._isInRange(a, v) ? '<button type="button" class="ui-datepicker-current ui-state-default ui-priority-secondary ui-corner-all" onclick="DP_jQuery_' + dpuuid + ".datepicker._gotoToday('#" + a.id + "');\"" + ">" + u + "</button>" : "") + (c ? "" : w) + "</div>" : "",
                y = parseInt(this._get(a, "firstDay"), 10);
            y = isNaN(y) ? 0 : y;
            var z = this._get(a, "showWeek"),
                A = this._get(a, "dayNames"),
                B = this._get(a, "dayNamesShort"),
                C = this._get(a, "dayNamesMin"),
                D = this._get(a, "monthNames"),
                E = this._get(a, "monthNamesShort"),
                F = this._get(a, "beforeShowDay"),
                G = this._get(a, "showOtherMonths"),
                H = this._get(a, "selectOtherMonths"),
                I = this._get(a, "calculateWeek") || this.iso8601Week,
                J = this._getDefaultDate(a),
                K = "";
            for (var L = 0; L < g[0]; L++) {
                var M = "";
                this.maxRows = 4;
                for (var N = 0; N < g[1]; N++) {
                    var O = this._daylightSavingAdjust(new Date(o, n, a.selectedDay)),
                        P = " ui-corner-all",
                        Q = "";
                    if (j) {
                        Q += '<div class="ui-datepicker-group';
                        if (g[1] > 1) switch (N) {
                            case 0:
                                Q += " ui-datepicker-group-first", P = " ui-corner-" + (c ? "right" : "left");
                                break;
                            case g[1] - 1:
                                Q += " ui-datepicker-group-last", P = " ui-corner-" + (c ? "left" : "right");
                                break;
                            default:
                                Q += " ui-datepicker-group-middle", P = ""
                        }
                        Q += '">'
                    }
                    Q += '<div class="ui-datepicker-header ui-widget-header ui-helper-clearfix' + P + '">' + (/all|left/.test(P) && L == 0 ? c ? t : r : "") + (/all|right/.test(P) && L == 0 ? c ? r : t : "") + this._generateMonthYearHeader(a, n, o, l, m, L > 0 || N > 0, D, E) + '</div><table class="ui-datepicker-calendar"><thead>' + "<tr>";
                    var R = z ? '<th class="ui-datepicker-week-col">' + this._get(a, "weekHeader") + "</th>" : "";
                    for (var S = 0; S < 7; S++) {
                        var T = (S + y) % 7;
                        R += "<th" + ((S + y + 6) % 7 >= 5 ? ' class="ui-datepicker-week-end"' : "") + ">" + '<span title="' + A[T] + '">' + C[T] + "</span></th>"
                    }
                    Q += R + "</tr></thead><tbody>";
                    var U = this._getDaysInMonth(o, n);
                    o == a.selectedYear && n == a.selectedMonth && (a.selectedDay = Math.min(a.selectedDay, U));
                    var V = (this._getFirstDayOfMonth(o, n) - y + 7) % 7,
                        W = Math.ceil((V + U) / 7),
                        X = j ? this.maxRows > W ? this.maxRows : W : W;
                    this.maxRows = X;
                    var Y = this._daylightSavingAdjust(new Date(o, n, 1 - V));
                    for (var Z = 0; Z < X; Z++) {
                        Q += "<tr>";
                        var _ = z ? '<td class="ui-datepicker-week-col">' + this._get(a, "calculateWeek")(Y) + "</td>" : "";
                        for (var S = 0; S < 7; S++) {
                            var ba = F ? F.apply(a.input ? a.input[0] : null, [Y]) : [!0, ""],
                                bb = Y.getMonth() != n,
                                bc = bb && !H || !ba[0] || l && Y < l || m && Y > m;
                            _ += '<td class="' + ((S + y + 6) % 7 >= 5 ? " ui-datepicker-week-end" : "") + (bb ? " ui-datepicker-other-month" : "") + (Y.getTime() == O.getTime() && n == a.selectedMonth && a._keyEvent || J.getTime() == Y.getTime() && J.getTime() == O.getTime() ? " " + this._dayOverClass : "") + (bc ? " " + this._unselectableClass + " ui-state-disabled" : "") + (bb && !G ? "" : " " + ba[1] + (Y.getTime() == k.getTime() ? " " + this._currentClass : "") + (Y.getTime() == b.getTime() ? " ui-datepicker-today" : "")) + '"' + ((!bb || G) && ba[2] ? ' title="' + ba[2] + '"' : "") + (bc ? "" : ' onclick="DP_jQuery_' + dpuuid + ".datepicker._selectDay('#" + a.id + "'," + Y.getMonth() + "," + Y.getFullYear() + ', this);return false;"') + ">" + (bb && !G ? "&#xa0;" : bc ? '<span class="ui-state-default">' + Y.getDate() + "</span>" : '<a class="ui-state-default' + (Y.getTime() == b.getTime() ? " ui-state-highlight" : "") + (Y.getTime() == k.getTime() ? " ui-state-active" : "") + (bb ? " ui-priority-secondary" : "") + '" href="#">' + Y.getDate() + "</a>") + "</td>", Y.setDate(Y.getDate() + 1), Y = this._daylightSavingAdjust(Y)
                        }
                        Q += _ + "</tr>"
                    }
                    n++, n > 11 && (n = 0, o++), Q += "</tbody></table>" + (j ? "</div>" + (g[0] > 0 && N == g[1] - 1 ? '<div class="ui-datepicker-row-break"></div>' : "") : ""), M += Q
                }
                K += M
            }
            return K += x + ($.browser.msie && parseInt($.browser.version, 10) < 7 && !a.inline ? '<iframe src="javascript:false;" class="ui-datepicker-cover" frameborder="0"></iframe>' : ""), a._keyEvent = !1, K
        },
        _generateMonthYearHeader: function (a, b, c, d, e, f, g, h) {
            var i = this._get(a, "changeMonth"),
                j = this._get(a, "changeYear"),
                k = this._get(a, "showMonthAfterYear"),
                l = '<div class="ui-datepicker-title">',
                m = "";
            if (f || !i) m += '<span class="ui-datepicker-month">' + g[b] + "</span>";
            else {
                var n = d && d.getFullYear() == c,
                    o = e && e.getFullYear() == c;
                m += '<select class="ui-datepicker-month" onchange="DP_jQuery_' + dpuuid + ".datepicker._selectMonthYear('#" + a.id + "', this, 'M');\" " + ">";
                for (var p = 0; p < 12; p++)(!n || p >= d.getMonth()) && (!o || p <= e.getMonth()) && (m += '<option value="' + p + '"' + (p == b ? ' selected="selected"' : "") + ">" + h[p] + "</option>");
                m += "</select>"
            }
            k || (l += m + (f || !i || !j ? "&#xa0;" : ""));
            if (!a.yearshtml) {
                a.yearshtml = "";
                if (f || !j) l += '<span class="ui-datepicker-year">' + c + "</span>";
                else {
                    var q = this._get(a, "yearRange").split(":"),
                        r = (new Date).getFullYear(),
                        s = function (a) {
                            var b = a.match(/c[+-].*/) ? c + parseInt(a.substring(1), 10) : a.match(/[+-].*/) ? r + parseInt(a, 10) : parseInt(a, 10);
                            return isNaN(b) ? r : b
                        }, t = s(q[0]),
                        u = Math.max(t, s(q[1] || ""));
                    t = d ? Math.max(t, d.getFullYear()) : t, u = e ? Math.min(u, e.getFullYear()) : u, a.yearshtml += '<select class="ui-datepicker-year" onchange="DP_jQuery_' + dpuuid + ".datepicker._selectMonthYear('#" + a.id + "', this, 'Y');\" " + ">";
                    for (; t <= u; t++) a.yearshtml += '<option value="' + t + '"' + (t == c ? ' selected="selected"' : "") + ">" + t + "</option>";
                    a.yearshtml += "</select>", l += a.yearshtml, a.yearshtml = null
                }
            }
            return l += this._get(a, "yearSuffix"), k && (l += (f || !i || !j ? "&#xa0;" : "") + m), l += "</div>", l
        },
        _adjustInstDate: function (a, b, c) {
            var d = a.drawYear + (c == "Y" ? b : 0),
                e = a.drawMonth + (c == "M" ? b : 0),
                f = Math.min(a.selectedDay, this._getDaysInMonth(d, e)) + (c == "D" ? b : 0),
                g = this._restrictMinMax(a, this._daylightSavingAdjust(new Date(d, e, f)));
            a.selectedDay = g.getDate(), a.drawMonth = a.selectedMonth = g.getMonth(), a.drawYear = a.selectedYear = g.getFullYear(), (c == "M" || c == "Y") && this._notifyChange(a)
        },
        _restrictMinMax: function (a, b) {
            var c = this._getMinMaxDate(a, "min"),
                d = this._getMinMaxDate(a, "max"),
                e = c && b < c ? c : b;
            return e = d && e > d ? d : e, e
        },
        _notifyChange: function (a) {
            var b = this._get(a, "onChangeMonthYear");
            b && b.apply(a.input ? a.input[0] : null, [a.selectedYear, a.selectedMonth + 1, a])
        },
        _getNumberOfMonths: function (a) {
            var b = this._get(a, "numberOfMonths");
            return b == null ? [1, 1] : typeof b == "number" ? [1, b] : b
        },
        _getMinMaxDate: function (a, b) {
            return this._determineDate(a, this._get(a, b + "Date"), null)
        },
        _getDaysInMonth: function (a, b) {
            return 32 - this._daylightSavingAdjust(new Date(a, b, 32)).getDate()
        },
        _getFirstDayOfMonth: function (a, b) {
            return (new Date(a, b, 1)).getDay()
        },
        _canAdjustMonth: function (a, b, c, d) {
            var e = this._getNumberOfMonths(a),
                f = this._daylightSavingAdjust(new Date(c, d + (b < 0 ? b : e[0] * e[1]), 1));
            return b < 0 && f.setDate(this._getDaysInMonth(f.getFullYear(), f.getMonth())), this._isInRange(a, f)
        },
        _isInRange: function (a, b) {
            var c = this._getMinMaxDate(a, "min"),
                d = this._getMinMaxDate(a, "max");
            return (!c || b.getTime() >= c.getTime()) && (!d || b.getTime() <= d.getTime())
        },
        _getFormatConfig: function (a) {
            var b = this._get(a, "shortYearCutoff");
            return b = typeof b != "string" ? b : (new Date).getFullYear() % 100 + parseInt(b, 10), {
                shortYearCutoff: b,
                dayNamesShort: this._get(a, "dayNamesShort"),
                dayNames: this._get(a, "dayNames"),
                monthNamesShort: this._get(a, "monthNamesShort"),
                monthNames: this._get(a, "monthNames")
            }
        },
        _formatDate: function (a, b, c, d) {
            b || (a.currentDay = a.selectedDay, a.currentMonth = a.selectedMonth, a.currentYear = a.selectedYear);
            var e = b ? typeof b == "object" ? b : this._daylightSavingAdjust(new Date(d, c, b)) : this._daylightSavingAdjust(new Date(a.currentYear, a.currentMonth, a.currentDay));
            return this.formatDate(this._get(a, "dateFormat"), e, this._getFormatConfig(a))
        }
    }), $.fn.datepicker = function (a) {
        if (!this.length) return this;
        $.datepicker.initialized || ($(document).mousedown($.datepicker._checkExternalClick).find("body").append($.datepicker.dpDiv), $.datepicker.initialized = !0);
        var b = Array.prototype.slice.call(arguments, 1);
        return typeof a != "string" || a != "isDisabled" && a != "getDate" && a != "widget" ? a == "option" && arguments.length == 2 && typeof arguments[1] == "string" ? $.datepicker["_" + a + "Datepicker"].apply($.datepicker, [this[0]].concat(b)) : this.each(function () {
            typeof a == "string" ? $.datepicker["_" + a + "Datepicker"].apply($.datepicker, [this].concat(b)) : $.datepicker._attachDatepicker(this, a)
        }) : $.datepicker["_" + a + "Datepicker"].apply($.datepicker, [this[0]].concat(b))
    }, $.datepicker = new Datepicker, $.datepicker.initialized = !1, $.datepicker.uuid = (new Date).getTime(), $.datepicker.version = "1.8.21", window["DP_jQuery_" + dpuuid] = $
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.ui.progressbar.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.widget("ui.progressbar", {
        options: {
            value: 0,
            max: 100
        },
        min: 0,
        _create: function () {
            this.element.addClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").attr({
                role: "progressbar",
                "aria-valuemin": this.min,
                "aria-valuemax": this.options.max,
                "aria-valuenow": this._value()
            }), this.valueDiv = a("<div class='ui-progressbar-value ui-widget-header ui-corner-left'></div>").appendTo(this.element), this.oldValue = this._value(), this._refreshValue()
        },
        destroy: function () {
            this.element.removeClass("ui-progressbar ui-widget ui-widget-content ui-corner-all").removeAttr("role").removeAttr("aria-valuemin").removeAttr("aria-valuemax").removeAttr("aria-valuenow"), this.valueDiv.remove(), a.Widget.prototype.destroy.apply(this, arguments)
        },
        value: function (a) {
            return a === b ? this._value() : (this._setOption("value", a), this)
        },
        _setOption: function (b, c) {
            b === "value" && (this.options.value = c, this._refreshValue(), this._value() === this.options.max && this._trigger("complete")), a.Widget.prototype._setOption.apply(this, arguments)
        },
        _value: function () {
            var a = this.options.value;
            return typeof a != "number" && (a = 0), Math.min(this.options.max, Math.max(this.min, a))
        },
        _percentage: function () {
            return 100 * this._value() / this.options.max
        },
        _refreshValue: function () {
            var a = this.value(),
                b = this._percentage();
            this.oldValue !== a && (this.oldValue = a, this._trigger("change")), this.valueDiv.toggle(a > this.min).toggleClass("ui-corner-right", a === this.options.max).width(b.toFixed(0) + "%"), this.element.attr("aria-valuenow", a)
        }
    }), a.extend(a.ui.progressbar, {
        version: "1.8.21"
    })
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.core.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
jQuery.effects || function (a, b) {
    function c(b) {
        var c;
        return b && b.constructor == Array && b.length == 3 ? b : (c = /rgb\(\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*,\s*([0-9]{1,3})\s*\)/.exec(b)) ? [parseInt(c[1], 10), parseInt(c[2], 10), parseInt(c[3], 10)] : (c = /rgb\(\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*,\s*([0-9]+(?:\.[0-9]+)?)\%\s*\)/.exec(b)) ? [parseFloat(c[1]) * 2.55, parseFloat(c[2]) * 2.55, parseFloat(c[3]) * 2.55] : (c = /#([a-fA-F0-9]{2})([a-fA-F0-9]{2})([a-fA-F0-9]{2})/.exec(b)) ? [parseInt(c[1], 16), parseInt(c[2], 16), parseInt(c[3], 16)] : (c = /#([a-fA-F0-9])([a-fA-F0-9])([a-fA-F0-9])/.exec(b)) ? [parseInt(c[1] + c[1], 16), parseInt(c[2] + c[2], 16), parseInt(c[3] + c[3], 16)] : (c = /rgba\(0, 0, 0, 0\)/.exec(b)) ? e.transparent : e[a.trim(b).toLowerCase()]
    }

    function d(b, d) {
        var e;
        do {
            e = a.curCSS(b, d);
            if (e != "" && e != "transparent" || a.nodeName(b, "body")) break;
            d = "backgroundColor"
        } while (b = b.parentNode);
        return c(e)
    }

    function h() {
        var a = document.defaultView ? document.defaultView.getComputedStyle(this, null) : this.currentStyle,
            b = {}, c, d;
        if (a && a.length && a[0] && a[a[0]]) {
            var e = a.length;
            while (e--) c = a[e], typeof a[c] == "string" && (d = c.replace(/\-(\w)/g, function (a, b) {
                return b.toUpperCase()
            }), b[d] = a[c])
        } else
            for (c in a) typeof a[c] == "string" && (b[c] = a[c]);
        return b
    }

    function i(b) {
        var c, d;
        for (c in b) d = b[c], (d == null || a.isFunction(d) || c in g || /scrollbar/.test(c) || !/color/i.test(c) && isNaN(parseFloat(d))) && delete b[c];
        return b
    }

    function j(a, b) {
        var c = {
            _: 0
        }, d;
        for (d in b) a[d] != b[d] && (c[d] = b[d]);
        return c
    }

    function k(b, c, d, e) {
        typeof b == "object" && (e = c, d = null, c = b, b = c.effect), a.isFunction(c) && (e = c, d = null, c = {});
        if (typeof c == "number" || a.fx.speeds[c]) e = d, d = c, c = {};
        return a.isFunction(d) && (e = d, d = null), c = c || {}, d = d || c.duration, d = a.fx.off ? 0 : typeof d == "number" ? d : d in a.fx.speeds ? a.fx.speeds[d] : a.fx.speeds._default, e = e || c.complete, [b, c, d, e]
    }

    function l(b) {
        return !b || typeof b == "number" || a.fx.speeds[b] ? !0 : typeof b == "string" && !a.effects[b] ? !0 : !1
    }
    a.effects = {}, a.each(["backgroundColor", "borderBottomColor", "borderLeftColor", "borderRightColor", "borderTopColor", "borderColor", "color", "outlineColor"], function (b, e) {
        a.fx.step[e] = function (a) {
            a.colorInit || (a.start = d(a.elem, e), a.end = c(a.end), a.colorInit = !0), a.elem.style[e] = "rgb(" + Math.max(Math.min(parseInt(a.pos * (a.end[0] - a.start[0]) + a.start[0], 10), 255), 0) + "," + Math.max(Math.min(parseInt(a.pos * (a.end[1] - a.start[1]) + a.start[1], 10), 255), 0) + "," + Math.max(Math.min(parseInt(a.pos * (a.end[2] - a.start[2]) + a.start[2], 10), 255), 0) + ")"
        }
    });
    var e = {
            aqua: [0, 255, 255],
            azure: [240, 255, 255],
            beige: [245, 245, 220],
            black: [0, 0, 0],
            blue: [0, 0, 255],
            brown: [165, 42, 42],
            cyan: [0, 255, 255],
            darkblue: [0, 0, 139],
            darkcyan: [0, 139, 139],
            darkgrey: [169, 169, 169],
            darkgreen: [0, 100, 0],
            darkkhaki: [189, 183, 107],
            darkmagenta: [139, 0, 139],
            darkolivegreen: [85, 107, 47],
            darkorange: [255, 140, 0],
            darkorchid: [153, 50, 204],
            darkred: [139, 0, 0],
            darksalmon: [233, 150, 122],
            darkviolet: [148, 0, 211],
            fuchsia: [255, 0, 255],
            gold: [255, 215, 0],
            green: [0, 128, 0],
            indigo: [75, 0, 130],
            khaki: [240, 230, 140],
            lightblue: [173, 216, 230],
            lightcyan: [224, 255, 255],
            lightgreen: [144, 238, 144],
            lightgrey: [211, 211, 211],
            lightpink: [255, 182, 193],
            lightyellow: [255, 255, 224],
            lime: [0, 255, 0],
            magenta: [255, 0, 255],
            maroon: [128, 0, 0],
            navy: [0, 0, 128],
            olive: [128, 128, 0],
            orange: [255, 165, 0],
            pink: [255, 192, 203],
            purple: [128, 0, 128],
            violet: [128, 0, 128],
            red: [255, 0, 0],
            silver: [192, 192, 192],
            white: [255, 255, 255],
            yellow: [255, 255, 0],
            transparent: [255, 255, 255]
        }, f = ["add", "remove", "toggle"],
        g = {
            border: 1,
            borderBottom: 1,
            borderColor: 1,
            borderLeft: 1,
            borderRight: 1,
            borderTop: 1,
            borderWidth: 1,
            margin: 1,
            padding: 1
        };
    a.effects.animateClass = function (b, c, d, e) {
        return a.isFunction(d) && (e = d, d = null), this.queue(function () {
            var g = a(this),
                k = g.attr("style") || " ",
                l = i(h.call(this)),
                m, n = g.attr("class") || "";
            a.each(f, function (a, c) {
                b[c] && g[c + "Class"](b[c])
            }), m = i(h.call(this)), g.attr("class", n), g.animate(j(l, m), {
                queue: !1,
                duration: c,
                easing: d,
                complete: function () {
                    a.each(f, function (a, c) {
                        b[c] && g[c + "Class"](b[c])
                    }), typeof g.attr("style") == "object" ? (g.attr("style").cssText = "", g.attr("style").cssText = k) : g.attr("style", k), e && e.apply(this, arguments), a.dequeue(this)
                }
            })
        })
    }, a.fn.extend({
        _addClass: a.fn.addClass,
        addClass: function (b, c, d, e) {
            return c ? a.effects.animateClass.apply(this, [{
                add: b
            },
                c, d, e
            ]) : this._addClass(b)
        },
        _removeClass: a.fn.removeClass,
        removeClass: function (b, c, d, e) {
            return c ? a.effects.animateClass.apply(this, [{
                remove: b
            },
                c, d, e
            ]) : this._removeClass(b)
        },
        _toggleClass: a.fn.toggleClass,
        toggleClass: function (c, d, e, f, g) {
            return typeof d == "boolean" || d === b ? e ? a.effects.animateClass.apply(this, [d ? {
                add: c
            } : {
                remove: c
            },
                e, f, g
            ]) : this._toggleClass(c, d) : a.effects.animateClass.apply(this, [{
                toggle: c
            },
                d, e, f
            ])
        },
        switchClass: function (b, c, d, e, f) {
            return a.effects.animateClass.apply(this, [{
                add: c,
                remove: b
            },
                d, e, f
            ])
        }
    }), a.extend(a.effects, {
        version: "1.8.21",
        save: function (a, b) {
            for (var c = 0; c < b.length; c++) b[c] !== null && a.data("ec.storage." + b[c], a[0].style[b[c]])
        },
        restore: function (a, b) {
            for (var c = 0; c < b.length; c++) b[c] !== null && a.css(b[c], a.data("ec.storage." + b[c]))
        },
        setMode: function (a, b) {
            return b == "toggle" && (b = a.is(":hidden") ? "show" : "hide"), b
        },
        getBaseline: function (a, b) {
            var c, d;
            switch (a[0]) {
                case "top":
                    c = 0;
                    break;
                case "middle":
                    c = .5;
                    break;
                case "bottom":
                    c = 1;
                    break;
                default:
                    c = a[0] / b.height
            }
            switch (a[1]) {
                case "left":
                    d = 0;
                    break;
                case "center":
                    d = .5;
                    break;
                case "right":
                    d = 1;
                    break;
                default:
                    d = a[1] / b.width
            }
            return {
                x: d,
                y: c
            }
        },
        createWrapper: function (b) {
            if (b.parent().is(".ui-effects-wrapper")) return b.parent();
            var c = {
                    width: b.outerWidth(!0),
                    height: b.outerHeight(!0),
                    "float": b.css("float")
                }, d = a("<div></div>").addClass("ui-effects-wrapper").css({
                    fontSize: "100%",
                    background: "transparent",
                    border: "none",
                    margin: 0,
                    padding: 0
                }),
                e = document.activeElement;
            try {
                e.id
            } catch (f) {
                e = document.body
            }
            return b.wrap(d), (b[0] === e || a.contains(b[0], e)) && a(e).focus(), d = b.parent(), b.css("position") == "static" ? (d.css({
                position: "relative"
            }), b.css({
                position: "relative"
            })) : (a.extend(c, {
                position: b.css("position"),
                zIndex: b.css("z-index")
            }), a.each(["top", "left", "bottom", "right"], function (a, d) {
                c[d] = b.css(d), isNaN(parseInt(c[d], 10)) && (c[d] = "auto")
            }), b.css({
                position: "relative",
                top: 0,
                left: 0,
                right: "auto",
                bottom: "auto"
            })), d.css(c).show()
        },
        removeWrapper: function (b) {
            var c, d = document.activeElement;
            return b.parent().is(".ui-effects-wrapper") ? (c = b.parent().replaceWith(b), (b[0] === d || a.contains(b[0], d)) && a(d).focus(), c) : b
        },
        setTransition: function (b, c, d, e) {
            return e = e || {}, a.each(c, function (a, c) {
                var f = b.cssUnit(c);
                f[0] > 0 && (e[c] = f[0] * d + f[1])
            }), e
        }
    }), a.fn.extend({
        effect: function (b, c, d, e) {
            var f = k.apply(this, arguments),
                g = {
                    options: f[1],
                    duration: f[2],
                    callback: f[3]
                }, h = g.options.mode,
                i = a.effects[b];
            return a.fx.off || !i ? h ? this[h](g.duration, g.callback) : this.each(function () {
                g.callback && g.callback.call(this)
            }) : i.call(this, g)
        },
        _show: a.fn.show,
        show: function (a) {
            if (l(a)) return this._show.apply(this, arguments);
            var b = k.apply(this, arguments);
            return b[1].mode = "show", this.effect.apply(this, b)
        },
        _hide: a.fn.hide,
        hide: function (a) {
            if (l(a)) return this._hide.apply(this, arguments);
            var b = k.apply(this, arguments);
            return b[1].mode = "hide", this.effect.apply(this, b)
        },
        __toggle: a.fn.toggle,
        toggle: function (b) {
            if (l(b) || typeof b == "boolean" || a.isFunction(b)) return this.__toggle.apply(this, arguments);
            var c = k.apply(this, arguments);
            return c[1].mode = "toggle", this.effect.apply(this, c)
        },
        cssUnit: function (b) {
            var c = this.css(b),
                d = [];
            return a.each(["em", "px", "%", "pt"], function (a, b) {
                c.indexOf(b) > 0 && (d = [parseFloat(c), b])
            }), d
        }
    }), a.easing.jswing = a.easing.swing, a.extend(a.easing, {
        def: "easeOutQuad",
        swing: function (b, c, d, e, f) {
            return a.easing[a.easing.def](b, c, d, e, f)
        },
        easeInQuad: function (a, b, c, d, e) {
            return d * (b /= e) * b + c
        },
        easeOutQuad: function (a, b, c, d, e) {
            return -d * (b /= e) * (b - 2) + c
        },
        easeInOutQuad: function (a, b, c, d, e) {
            return (b /= e / 2) < 1 ? d / 2 * b * b + c : -d / 2 * (--b * (b - 2) - 1) + c
        },
        easeInCubic: function (a, b, c, d, e) {
            return d * (b /= e) * b * b + c
        },
        easeOutCubic: function (a, b, c, d, e) {
            return d * ((b = b / e - 1) * b * b + 1) + c
        },
        easeInOutCubic: function (a, b, c, d, e) {
            return (b /= e / 2) < 1 ? d / 2 * b * b * b + c : d / 2 * ((b -= 2) * b * b + 2) + c
        },
        easeInQuart: function (a, b, c, d, e) {
            return d * (b /= e) * b * b * b + c
        },
        easeOutQuart: function (a, b, c, d, e) {
            return -d * ((b = b / e - 1) * b * b * b - 1) + c
        },
        easeInOutQuart: function (a, b, c, d, e) {
            return (b /= e / 2) < 1 ? d / 2 * b * b * b * b + c : -d / 2 * ((b -= 2) * b * b * b - 2) + c
        },
        easeInQuint: function (a, b, c, d, e) {
            return d * (b /= e) * b * b * b * b + c
        },
        easeOutQuint: function (a, b, c, d, e) {
            return d * ((b = b / e - 1) * b * b * b * b + 1) + c
        },
        easeInOutQuint: function (a, b, c, d, e) {
            return (b /= e / 2) < 1 ? d / 2 * b * b * b * b * b + c : d / 2 * ((b -= 2) * b * b * b * b + 2) + c
        },
        easeInSine: function (a, b, c, d, e) {
            return -d * Math.cos(b / e * (Math.PI / 2)) + d + c
        },
        easeOutSine: function (a, b, c, d, e) {
            return d * Math.sin(b / e * (Math.PI / 2)) + c
        },
        easeInOutSine: function (a, b, c, d, e) {
            return -d / 2 * (Math.cos(Math.PI * b / e) - 1) + c
        },
        easeInExpo: function (a, b, c, d, e) {
            return b == 0 ? c : d * Math.pow(2, 10 * (b / e - 1)) + c
        },
        easeOutExpo: function (a, b, c, d, e) {
            return b == e ? c + d : d * (-Math.pow(2, -10 * b / e) + 1) + c
        },
        easeInOutExpo: function (a, b, c, d, e) {
            return b == 0 ? c : b == e ? c + d : (b /= e / 2) < 1 ? d / 2 * Math.pow(2, 10 * (b - 1)) + c : d / 2 * (-Math.pow(2, -10 * --b) + 2) + c
        },
        easeInCirc: function (a, b, c, d, e) {
            return -d * (Math.sqrt(1 - (b /= e) * b) - 1) + c
        },
        easeOutCirc: function (a, b, c, d, e) {
            return d * Math.sqrt(1 - (b = b / e - 1) * b) + c
        },
        easeInOutCirc: function (a, b, c, d, e) {
            return (b /= e / 2) < 1 ? -d / 2 * (Math.sqrt(1 - b * b) - 1) + c : d / 2 * (Math.sqrt(1 - (b -= 2) * b) + 1) + c
        },
        easeInElastic: function (a, b, c, d, e) {
            var f = 1.70158,
                g = 0,
                h = d;
            if (b == 0) return c;
            if ((b /= e) == 1) return c + d;
            g || (g = e * .3);
            if (h < Math.abs(d)) {
                h = d;
                var f = g / 4
            } else var f = g / (2 * Math.PI) * Math.asin(d / h);
            return -(h * Math.pow(2, 10 * (b -= 1)) * Math.sin((b * e - f) * 2 * Math.PI / g)) + c
        },
        easeOutElastic: function (a, b, c, d, e) {
            var f = 1.70158,
                g = 0,
                h = d;
            if (b == 0) return c;
            if ((b /= e) == 1) return c + d;
            g || (g = e * .3);
            if (h < Math.abs(d)) {
                h = d;
                var f = g / 4
            } else var f = g / (2 * Math.PI) * Math.asin(d / h);
            return h * Math.pow(2, -10 * b) * Math.sin((b * e - f) * 2 * Math.PI / g) + d + c
        },
        easeInOutElastic: function (a, b, c, d, e) {
            var f = 1.70158,
                g = 0,
                h = d;
            if (b == 0) return c;
            if ((b /= e / 2) == 2) return c + d;
            g || (g = e * .3 * 1.5);
            if (h < Math.abs(d)) {
                h = d;
                var f = g / 4
            } else var f = g / (2 * Math.PI) * Math.asin(d / h);
            return b < 1 ? -0.5 * h * Math.pow(2, 10 * (b -= 1)) * Math.sin((b * e - f) * 2 * Math.PI / g) + c : h * Math.pow(2, -10 * (b -= 1)) * Math.sin((b * e - f) * 2 * Math.PI / g) * .5 + d + c
        },
        easeInBack: function (a, c, d, e, f, g) {
            return g == b && (g = 1.70158), e * (c /= f) * c * ((g + 1) * c - g) + d
        },
        easeOutBack: function (a, c, d, e, f, g) {
            return g == b && (g = 1.70158), e * ((c = c / f - 1) * c * ((g + 1) * c + g) + 1) + d
        },
        easeInOutBack: function (a, c, d, e, f, g) {
            return g == b && (g = 1.70158), (c /= f / 2) < 1 ? e / 2 * c * c * (((g *= 1.525) + 1) * c - g) + d : e / 2 * ((c -= 2) * c * (((g *= 1.525) + 1) * c + g) + 2) + d
        },
        easeInBounce: function (b, c, d, e, f) {
            return e - a.easing.easeOutBounce(b, f - c, 0, e, f) + d
        },
        easeOutBounce: function (a, b, c, d, e) {
            return (b /= e) < 1 / 2.75 ? d * 7.5625 * b * b + c : b < 2 / 2.75 ? d * (7.5625 * (b -= 1.5 / 2.75) * b + .75) + c : b < 2.5 / 2.75 ? d * (7.5625 * (b -= 2.25 / 2.75) * b + .9375) + c : d * (7.5625 * (b -= 2.625 / 2.75) * b + .984375) + c
        },
        easeInOutBounce: function (b, c, d, e, f) {
            return c < f / 2 ? a.easing.easeInBounce(b, c * 2, 0, e, f) * .5 + d : a.easing.easeOutBounce(b, c * 2 - f, 0, e, f) * .5 + e * .5 + d
        }
    })
}(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.blind.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.blind = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right"],
                e = a.effects.setMode(c, b.options.mode || "hide"),
                f = b.options.direction || "vertical";
            a.effects.save(c, d), c.show();
            var g = a.effects.createWrapper(c).css({
                    overflow: "hidden"
                }),
                h = f == "vertical" ? "height" : "width",
                i = f == "vertical" ? g.height() : g.width();
            e == "show" && g.css(h, 0);
            var j = {};
            j[h] = e == "show" ? i : 0, g.animate(j, b.duration, b.options.easing, function () {
                e == "hide" && c.hide(), a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(c[0], arguments), c.dequeue()
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.bounce.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.bounce = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right"],
                e = a.effects.setMode(c, b.options.mode || "effect"),
                f = b.options.direction || "up",
                g = b.options.distance || 20,
                h = b.options.times || 5,
                i = b.duration || 250;
            /show|hide/.test(e) && d.push("opacity"), a.effects.save(c, d), c.show(), a.effects.createWrapper(c);
            var j = f == "up" || f == "down" ? "top" : "left",
                k = f == "up" || f == "left" ? "pos" : "neg",
                g = b.options.distance || (j == "top" ? c.outerHeight({
                    margin: !0
                }) / 3 : c.outerWidth({
                    margin: !0
                }) / 3);
            e == "show" && c.css("opacity", 0).css(j, k == "pos" ? -g : g), e == "hide" && (g = g / (h * 2)), e != "hide" && h--;
            if (e == "show") {
                var l = {
                    opacity: 1
                };
                l[j] = (k == "pos" ? "+=" : "-=") + g, c.animate(l, i / 2, b.options.easing), g = g / 2, h--
            }
            for (var m = 0; m < h; m++) {
                var n = {}, p = {};
                n[j] = (k == "pos" ? "-=" : "+=") + g, p[j] = (k == "pos" ? "+=" : "-=") + g, c.animate(n, i / 2, b.options.easing).animate(p, i / 2, b.options.easing), g = e == "hide" ? g * 2 : g / 2
            }
            if (e == "hide") {
                var l = {
                    opacity: 0
                };
                l[j] = (k == "pos" ? "-=" : "+=") + g, c.animate(l, i / 2, b.options.easing, function () {
                    c.hide(), a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(this, arguments)
                })
            } else {
                var n = {}, p = {};
                n[j] = (k == "pos" ? "-=" : "+=") + g, p[j] = (k == "pos" ? "+=" : "-=") + g, c.animate(n, i / 2, b.options.easing).animate(p, i / 2, b.options.easing, function () {
                    a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(this, arguments)
                })
            }
            c.queue("fx", function () {
                c.dequeue()
            }), c.dequeue()
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.clip.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.clip = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right", "height", "width"],
                e = a.effects.setMode(c, b.options.mode || "hide"),
                f = b.options.direction || "vertical";
            a.effects.save(c, d), c.show();
            var g = a.effects.createWrapper(c).css({
                    overflow: "hidden"
                }),
                h = c[0].tagName == "IMG" ? g : c,
                i = {
                    size: f == "vertical" ? "height" : "width",
                    position: f == "vertical" ? "top" : "left"
                }, j = f == "vertical" ? h.height() : h.width();
            e == "show" && (h.css(i.size, 0), h.css(i.position, j / 2));
            var k = {};
            k[i.size] = e == "show" ? j : 0, k[i.position] = e == "show" ? 0 : j / 2, h.animate(k, {
                queue: !1,
                duration: b.duration,
                easing: b.options.easing,
                complete: function () {
                    e == "hide" && c.hide(), a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(c[0], arguments), c.dequeue()
                }
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.drop.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.drop = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right", "opacity"],
                e = a.effects.setMode(c, b.options.mode || "hide"),
                f = b.options.direction || "left";
            a.effects.save(c, d), c.show(), a.effects.createWrapper(c);
            var g = f == "up" || f == "down" ? "top" : "left",
                h = f == "up" || f == "left" ? "pos" : "neg",
                i = b.options.distance || (g == "top" ? c.outerHeight({
                    margin: !0
                }) / 2 : c.outerWidth({
                    margin: !0
                }) / 2);
            e == "show" && c.css("opacity", 0).css(g, h == "pos" ? -i : i);
            var j = {
                opacity: e == "show" ? 1 : 0
            };
            j[g] = (e == "show" ? h == "pos" ? "+=" : "-=" : h == "pos" ? "-=" : "+=") + i, c.animate(j, {
                queue: !1,
                duration: b.duration,
                easing: b.options.easing,
                complete: function () {
                    e == "hide" && c.hide(), a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(this, arguments), c.dequeue()
                }
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.explode.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.explode = function (b) {
        return this.queue(function () {
            var c = b.options.pieces ? Math.round(Math.sqrt(b.options.pieces)) : 3,
                d = b.options.pieces ? Math.round(Math.sqrt(b.options.pieces)) : 3;
            b.options.mode = b.options.mode == "toggle" ? a(this).is(":visible") ? "hide" : "show" : b.options.mode;
            var e = a(this).show().css("visibility", "hidden"),
                f = e.offset();
            f.top -= parseInt(e.css("marginTop"), 10) || 0, f.left -= parseInt(e.css("marginLeft"), 10) || 0;
            var g = e.outerWidth(!0),
                h = e.outerHeight(!0);
            for (var i = 0; i < c; i++)
                for (var j = 0; j < d; j++) e.clone().appendTo("body").wrap("<div></div>").css({
                    position: "absolute",
                    visibility: "visible",
                    left: -j * (g / d),
                    top: -i * (h / c)
                }).parent().addClass("ui-effects-explode").css({
                    position: "absolute",
                    overflow: "hidden",
                    width: g / d,
                    height: h / c,
                    left: f.left + j * (g / d) + (b.options.mode == "show" ? (j - Math.floor(d / 2)) * (g / d) : 0),
                    top: f.top + i * (h / c) + (b.options.mode == "show" ? (i - Math.floor(c / 2)) * (h / c) : 0),
                    opacity: b.options.mode == "show" ? 0 : 1
                }).animate({
                    left: f.left + j * (g / d) + (b.options.mode == "show" ? 0 : (j - Math.floor(d / 2)) * (g / d)),
                    top: f.top + i * (h / c) + (b.options.mode == "show" ? 0 : (i - Math.floor(c / 2)) * (h / c)),
                    opacity: b.options.mode == "show" ? 1 : 0
                }, b.duration || 500);
            setTimeout(function () {
                b.options.mode == "show" ? e.css({
                    visibility: "visible"
                }) : e.css({
                    visibility: "visible"
                }).hide(), b.callback && b.callback.apply(e[0]), e.dequeue(), a("div.ui-effects-explode").remove()
            }, b.duration || 500)
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.fade.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.fade = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = a.effects.setMode(c, b.options.mode || "hide");
            c.animate({
                opacity: d
            }, {
                queue: !1,
                duration: b.duration,
                easing: b.options.easing,
                complete: function () {
                    b.callback && b.callback.apply(this, arguments), c.dequeue()
                }
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.fold.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.fold = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right"],
                e = a.effects.setMode(c, b.options.mode || "hide"),
                f = b.options.size || 15,
                g = !! b.options.horizFirst,
                h = b.duration ? b.duration / 2 : a.fx.speeds._default / 2;
            a.effects.save(c, d), c.show();
            var i = a.effects.createWrapper(c).css({
                    overflow: "hidden"
                }),
                j = e == "show" != g,
                k = j ? ["width", "height"] : ["height", "width"],
                l = j ? [i.width(), i.height()] : [i.height(), i.width()],
                m = /([0-9]+)%/.exec(f);
            m && (f = parseInt(m[1], 10) / 100 * l[e == "hide" ? 0 : 1]), e == "show" && i.css(g ? {
                height: 0,
                width: f
            } : {
                height: f,
                width: 0
            });
            var n = {}, p = {};
            n[k[0]] = e == "show" ? l[0] : f, p[k[1]] = e == "show" ? l[1] : 0, i.animate(n, h, b.options.easing).animate(p, h, b.options.easing, function () {
                e == "hide" && c.hide(), a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(c[0], arguments), c.dequeue()
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.highlight.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.highlight = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["backgroundImage", "backgroundColor", "opacity"],
                e = a.effects.setMode(c, b.options.mode || "show"),
                f = {
                    backgroundColor: c.css("backgroundColor")
                };
            e == "hide" && (f.opacity = 0), a.effects.save(c, d), c.show().css({
                backgroundImage: "none",
                backgroundColor: b.options.color || "#ffff99"
            }).animate(f, {
                queue: !1,
                duration: b.duration,
                easing: b.options.easing,
                complete: function () {
                    e == "hide" && c.hide(), a.effects.restore(c, d), e == "show" && !a.support.opacity && this.style.removeAttribute("filter"), b.callback && b.callback.apply(this, arguments), c.dequeue()
                }
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.pulsate.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.pulsate = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = a.effects.setMode(c, b.options.mode || "show"),
                e = (b.options.times || 5) * 2 - 1,
                f = b.duration ? b.duration / 2 : a.fx.speeds._default / 2,
                g = c.is(":visible"),
                h = 0;
            g || (c.css("opacity", 0).show(), h = 1), (d == "hide" && g || d == "show" && !g) && e--;
            for (var i = 0; i < e; i++) c.animate({
                opacity: h
            }, f, b.options.easing), h = (h + 1) % 2;
            c.animate({
                opacity: h
            }, f, b.options.easing, function () {
                h == 0 && c.hide(), b.callback && b.callback.apply(this, arguments)
            }), c.queue("fx", function () {
                c.dequeue()
            }).dequeue()
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.scale.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.puff = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = a.effects.setMode(c, b.options.mode || "hide"),
                e = parseInt(b.options.percent, 10) || 150,
                f = e / 100,
                g = {
                    height: c.height(),
                    width: c.width()
                };
            a.extend(b.options, {
                fade: !0,
                mode: d,
                percent: d == "hide" ? e : 100,
                from: d == "hide" ? g : {
                    height: g.height * f,
                    width: g.width * f
                }
            }), c.effect("scale", b.options, b.duration, b.callback), c.dequeue()
        })
    }, a.effects.scale = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = a.extend(!0, {}, b.options),
                e = a.effects.setMode(c, b.options.mode || "effect"),
                f = parseInt(b.options.percent, 10) || (parseInt(b.options.percent, 10) == 0 ? 0 : e == "hide" ? 0 : 100),
                g = b.options.direction || "both",
                h = b.options.origin;
            e != "effect" && (d.origin = h || ["middle", "center"], d.restore = !0);
            var i = {
                height: c.height(),
                width: c.width()
            };
            c.from = b.options.from || (e == "show" ? {
                height: 0,
                width: 0
            } : i);
            var j = {
                y: g != "horizontal" ? f / 100 : 1,
                x: g != "vertical" ? f / 100 : 1
            };
            c.to = {
                height: i.height * j.y,
                width: i.width * j.x
            }, b.options.fade && (e == "show" && (c.from.opacity = 0, c.to.opacity = 1), e == "hide" && (c.from.opacity = 1, c.to.opacity = 0)), d.from = c.from, d.to = c.to, d.mode = e, c.effect("size", d, b.duration, b.callback), c.dequeue()
        })
    }, a.effects.size = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right", "width", "height", "overflow", "opacity"],
                e = ["position", "top", "bottom", "left", "right", "overflow", "opacity"],
                f = ["width", "height", "overflow"],
                g = ["fontSize"],
                h = ["borderTopWidth", "borderBottomWidth", "paddingTop", "paddingBottom"],
                i = ["borderLeftWidth", "borderRightWidth", "paddingLeft", "paddingRight"],
                j = a.effects.setMode(c, b.options.mode || "effect"),
                k = b.options.restore || !1,
                l = b.options.scale || "both",
                m = b.options.origin,
                n = {
                    height: c.height(),
                    width: c.width()
                };
            c.from = b.options.from || n, c.to = b.options.to || n;
            if (m) {
                var p = a.effects.getBaseline(m, n);
                c.from.top = (n.height - c.from.height) * p.y, c.from.left = (n.width - c.from.width) * p.x, c.to.top = (n.height - c.to.height) * p.y, c.to.left = (n.width - c.to.width) * p.x
            }
            var q = {
                from: {
                    y: c.from.height / n.height,
                    x: c.from.width / n.width
                },
                to: {
                    y: c.to.height / n.height,
                    x: c.to.width / n.width
                }
            };
            if (l == "box" || l == "both") q.from.y != q.to.y && (d = d.concat(h), c.from = a.effects.setTransition(c, h, q.from.y, c.from), c.to = a.effects.setTransition(c, h, q.to.y, c.to)), q.from.x != q.to.x && (d = d.concat(i), c.from = a.effects.setTransition(c, i, q.from.x, c.from), c.to = a.effects.setTransition(c, i, q.to.x, c.to));
            (l == "content" || l == "both") && q.from.y != q.to.y && (d = d.concat(g), c.from = a.effects.setTransition(c, g, q.from.y, c.from), c.to = a.effects.setTransition(c, g, q.to.y, c.to)), a.effects.save(c, k ? d : e), c.show(), a.effects.createWrapper(c), c.css("overflow", "hidden").css(c.from);
            if (l == "content" || l == "both") h = h.concat(["marginTop", "marginBottom"]).concat(g), i = i.concat(["marginLeft", "marginRight"]), f = d.concat(h).concat(i), c.find("*[width]").each(function () {
                var c = a(this);
                k && a.effects.save(c, f);
                var d = {
                    height: c.height(),
                    width: c.width()
                };
                c.from = {
                    height: d.height * q.from.y,
                    width: d.width * q.from.x
                }, c.to = {
                    height: d.height * q.to.y,
                    width: d.width * q.to.x
                }, q.from.y != q.to.y && (c.from = a.effects.setTransition(c, h, q.from.y, c.from), c.to = a.effects.setTransition(c, h, q.to.y, c.to)), q.from.x != q.to.x && (c.from = a.effects.setTransition(c, i, q.from.x, c.from), c.to = a.effects.setTransition(c, i, q.to.x, c.to)), c.css(c.from), c.animate(c.to, b.duration, b.options.easing, function () {
                    k && a.effects.restore(c, f)
                })
            });
            c.animate(c.to, {
                queue: !1,
                duration: b.duration,
                easing: b.options.easing,
                complete: function () {
                    c.to.opacity === 0 && c.css("opacity", c.from.opacity), j == "hide" && c.hide(), a.effects.restore(c, k ? d : e), a.effects.removeWrapper(c), b.callback && b.callback.apply(this, arguments), c.dequeue()
                }
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.shake.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.shake = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right"],
                e = a.effects.setMode(c, b.options.mode || "effect"),
                f = b.options.direction || "left",
                g = b.options.distance || 20,
                h = b.options.times || 3,
                i = b.duration || b.options.duration || 140;
            a.effects.save(c, d), c.show(), a.effects.createWrapper(c);
            var j = f == "up" || f == "down" ? "top" : "left",
                k = f == "up" || f == "left" ? "pos" : "neg",
                l = {}, m = {}, n = {};
            l[j] = (k == "pos" ? "-=" : "+=") + g, m[j] = (k == "pos" ? "+=" : "-=") + g * 2, n[j] = (k == "pos" ? "-=" : "+=") + g * 2, c.animate(l, i, b.options.easing);
            for (var p = 1; p < h; p++) c.animate(m, i, b.options.easing).animate(n, i, b.options.easing);
            c.animate(m, i, b.options.easing).animate(l, i / 2, b.options.easing, function () {
                a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(this, arguments)
            }), c.queue("fx", function () {
                c.dequeue()
            }), c.dequeue()
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.slide.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.slide = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = ["position", "top", "bottom", "left", "right"],
                e = a.effects.setMode(c, b.options.mode || "show"),
                f = b.options.direction || "left";
            a.effects.save(c, d), c.show(), a.effects.createWrapper(c).css({
                overflow: "hidden"
            });
            var g = f == "up" || f == "down" ? "top" : "left",
                h = f == "up" || f == "left" ? "pos" : "neg",
                i = b.options.distance || (g == "top" ? c.outerHeight({
                    margin: !0
                }) : c.outerWidth({
                    margin: !0
                }));
            e == "show" && c.css(g, h == "pos" ? isNaN(i) ? "-" + i : -i : i);
            var j = {};
            j[g] = (e == "show" ? h == "pos" ? "+=" : "-=" : h == "pos" ? "-=" : "+=") + i, c.animate(j, {
                queue: !1,
                duration: b.duration,
                easing: b.options.easing,
                complete: function () {
                    e == "hide" && c.hide(), a.effects.restore(c, d), a.effects.removeWrapper(c), b.callback && b.callback.apply(this, arguments), c.dequeue()
                }
            })
        })
    }
})(jQuery);;
/*! jQuery UI - v1.8.21 - 2012-06-05
 * https://github.com/jquery/jquery-ui
 * Includes: jquery.effects.transfer.js
 * Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function (a, b) {
    a.effects.transfer = function (b) {
        return this.queue(function () {
            var c = a(this),
                d = a(b.options.to),
                e = d.offset(),
                f = {
                    top: e.top,
                    left: e.left,
                    height: d.innerHeight(),
                    width: d.innerWidth()
                }, g = c.offset(),
                h = a('<div class="ui-effects-transfer"></div>').appendTo(document.body).addClass(b.options.className).css({
                    top: g.top,
                    left: g.left,
                    height: c.innerHeight(),
                    width: c.innerWidth(),
                    position: "absolute"
                }).animate(f, b.duration, b.options.easing, function () {
                    h.remove(), b.callback && b.callback.apply(c[0], arguments), c.dequeue()
                })
        })
    }
})(jQuery);;